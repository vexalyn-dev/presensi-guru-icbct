<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Setting;
use App\Models\ScanLog;
use App\Models\TeacherSchedule;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Services\ActivityLogService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Controller untuk alat scan QR fisik (barcode gun, ESP, dsb.)
 * Tidak memerlukan login / sesi guru piket.
 * Autentikasi cukup menggunakan X-Device-Key header.
 */
class DeviceAttendanceController extends Controller
{
    /**
     * Proses scan QR dari alat fisik.
     *
     * Request JSON:
     *   qr_data  : string  — isi QR (JSON {"teacher_id":1,"token":"..."} atau plain ID)
     *   mode     : string? — "masuk"|"keluar"|"auto" (default: auto)
     *   latitude : float?  — opsional, kirim jika alat punya GPS
     *   longitude: float?  — opsional
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_data'   => 'required|string',
            'mode'      => 'nullable|in:masuk,keluar,auto',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // ── 1. Decode QR ────────────────────────────────────────────────
        $qrData = json_decode($validated['qr_data'], true);

        if (!$qrData || !is_array($qrData)) {
            // Plain ID (integer atau string angka)
            $qrData = ['teacher_id' => $validated['qr_data']];
        }

        if (!isset($qrData['teacher_id'])) {
            return $this->fail('QR code tidak valid — teacher ID tidak ditemukan.');
        }

        // ── 2. Cari guru ─────────────────────────────────────────────────
        $teacher = User::where('id', $qrData['teacher_id'])
            ->where('role', 'guru')
            ->first();

        // Validasi token jika ada di QR
        if (isset($qrData['token']) && $teacher) {
            if ($teacher->qr_token !== $qrData['token']) {
                $this->logScan($teacher, 'in', 'failed', 'QR token kadaluarsa', $request);
                return $this->fail('QR code sudah kadaluarsa. Minta guru untuk memperbarui QR-nya.', 422);
            }
        }

        if (!$teacher) {
            return $this->fail('Guru tidak ditemukan. QR code mungkin tidak valid.', 404);
        }

        if (!$teacher->is_active) {
            $this->logScan($teacher, 'in', 'failed', 'Akun tidak aktif', $request);
            return $this->fail('Akun guru tidak aktif. Hubungi admin.', 403);
        }

        // ── 3. Cek hari libur ────────────────────────────────────────────
        if (Holiday::isHoliday(today())) {
            $holidayName = Holiday::getHolidayName(today());
            return $this->fail("Hari ini adalah hari libur ({$holidayName}). Tidak perlu presensi.");
        }

        // ── 4. Cek jadwal ─────────────────────────────────────────────────
        if (!TeacherSchedule::isScheduledToday($teacher->id)) {
            return $this->fail('Guru tidak dijadwalkan mengajar hari ini.');
        }

        $todaySchedule = TeacherSchedule::getTodaySchedule($teacher->id);

        // ── 5. GPS (opsional untuk alat fisik) ──────────────────────────
        // Hanya validasi GPS jika alat mengirim koordinat DAN GPS setting aktif
        $lat = $validated['latitude'] ?? null;
        $lng = $validated['longitude'] ?? null;

        if ($lat !== null && $lng !== null) {
            $gpsValidation = \App\Helpers\GpsHelper::validateLocation($lat, $lng);
            if (!$gpsValidation['valid']) {
                $this->logScan($teacher, 'in', 'failed', $gpsValidation['message'], $request);
                return $this->fail($gpsValidation['message']);
            }
        }

        // ── 6. Proses presensi ───────────────────────────────────────────
        $attendance = Attendance::where('user_id', $teacher->id)
            ->where('date', today())
            ->first();

        $mode = $validated['mode'] ?? 'auto';

        // Sudah masuk & sudah keluar → selesai
        if ($attendance && $attendance->check_in && $attendance->check_out) {
            return $this->fail('Presensi hari ini sudah lengkap (masuk & keluar).', 422);
        }

        // Sudah masuk, belum keluar → proses keluar
        if ($attendance && $attendance->check_in && !$attendance->check_out) {
            if ($mode === 'masuk') {
                return $this->fail('Guru sudah tercatat masuk pada ' . Carbon::parse($attendance->check_in)->format('H:i') . '. Gunakan mode keluar.', 422);
            }
            $result = $this->processKeluar($attendance, $teacher, $lat, $lng, $todaySchedule);
            $this->logScan($teacher, 'out', 'success', 'Presensi keluar berhasil', $request);
            return response()->json([
                'success'         => true,
                'message'         => 'Presensi keluar berhasil!',
                'teacher_name'    => $teacher->name,
                'attendance_type' => 'keluar',
                'status'          => $result['status'],
                'time'            => $result['time'],
            ]);
        }

        // Belum ada presensi sama sekali
        if ($mode === 'keluar') {
            return $this->fail('Guru belum tercatat presensi masuk hari ini.', 422);
        }

        // Mode masuk (default / auto)
        $result = $this->processMasuk($teacher, $lat, $lng, $todaySchedule);
        $this->logScan($teacher, 'in', 'success', 'Presensi masuk berhasil', $request);

        return response()->json([
            'success'         => true,
            'message'         => 'Presensi masuk berhasil!',
            'teacher_name'    => $teacher->name,
            'attendance_type' => 'masuk',
            'status'          => $result['status'],
            'time'            => $result['time'],
        ]);
    }

    // ────────────────────────────────────────────────────────────────────
    // Private helpers
    // ────────────────────────────────────────────────────────────────────

    private function processMasuk(User $teacher, ?float $lat, ?float $lng, $schedule): array
    {
        $now = Carbon::now();

        $startStr = $schedule
            ? Carbon::parse($schedule->start_time)->format('H:i')
            : ($teacher->start_time ? Carbon::parse($teacher->start_time)->format('H:i') : '07:30');

        $status = Carbon::parse($now->format('H:i'))->greaterThan(Carbon::parse($startStr))
            ? 'Terlambat'
            : 'Tepat Waktu';

        DB::transaction(function () use ($teacher, $now, $status, $lat, $lng) {
            Attendance::create([
                'user_id'     => $teacher->id,
                'date'        => today(),
                'check_in'    => $now->format('H:i:s'),
                'status'      => $status,
                'latitude'    => $lat,
                'longitude'   => $lng,
                'scan_method' => 'qr_code',
            ]);

            try {
                ActivityLogService::log(
                    'scan_in_daily',
                    'attendance',
                    "{$teacher->name} scan presensi MASUK via alat ({$status})",
                    null,
                    ['status' => $status, 'latitude' => $lat, 'longitude' => $lng],
                    $teacher
                );
            } catch (\Exception $e) {
                Log::warning('ActivityLog device scan_in failed: ' . $e->getMessage());
            }
        });

        $this->notifyAdmins($teacher, $status);
        $this->sendWaMasuk($teacher, $now, $startStr, $status);

        return ['status' => $status, 'time' => $now->format('H:i:s')];
    }

    private function processKeluar(Attendance $attendance, User $teacher, ?float $lat, ?float $lng, $schedule): array
    {
        $now = Carbon::now();

        $endStr = $schedule
            ? Carbon::parse($schedule->end_time)->format('H:i')
            : ($teacher->end_time ? Carbon::parse($teacher->end_time)->format('H:i') : '16:00');

        $statusOut = Carbon::parse($now->format('H:i'))->gte(Carbon::parse($endStr))
            ? 'Tepat Waktu'
            : 'Pulang Cepat';

        $attendance->update([
            'check_out'            => $now->format('H:i:s'),
            'check_out_status'     => $statusOut,
            'check_out_latitude'   => $lat,
            'check_out_longitude'  => $lng,
        ]);

        try {
            ActivityLogService::log(
                'scan_out_daily',
                'attendance',
                "{$teacher->name} scan presensi KELUAR via alat ({$statusOut})",
                null,
                ['status' => $statusOut, 'latitude' => $lat, 'longitude' => $lng],
                $teacher
            );
        } catch (\Exception $e) {
            Log::warning('ActivityLog device scan_out failed: ' . $e->getMessage());
        }

        $this->sendWaKeluar($teacher, $now, $endStr, $statusOut);

        return ['status' => $statusOut, 'time' => $now->format('H:i:s')];
    }

    private function notifyAdmins(User $teacher, string $status): void
    {
        try {
            $targets = User::whereIn('role', ['admin', 'operator', 'guru_piket'])->get();
            foreach ($targets as $target) {
                /** @var User $target */
                $target->notify(new SystemNotification(
                    "Guru {$teacher->name} telah absen ({$status}) via alat scan",
                    'success',
                    route('attendance.history')
                ));
            }
        } catch (\Exception $e) {
            Log::warning('Notify admins failed: ' . $e->getMessage());
        }
    }

    private function sendWaMasuk(User $teacher, Carbon $now, string $startStr, string $status): void
    {
        $dateStr = $now->locale('id')->isoFormat('dddd, D MMMM YYYY');

        if ($status === 'Tepat Waktu') {
            $msg = "Assalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                . "Yth. Bapak/Ibu {$teacher->name},\n\n"
                . "Presensi *MASUK* Anda pada {$dateStr} telah tercatat.\n\n"
                . "*Detail Presensi:*\n"
                . "• Waktu  : {$now->format('H:i')} WIB\n"
                . "• Jadwal : {$startStr} WIB\n"
                . "• Status : *{$status}*\n\n"
                . "Terima kasih atas kedisiplinan Anda.\n\n"
                . "Wassalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                . "Hormat kami,\n*Operator SMK ICB CT*";
        } else {
            $msg = "Assalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                . "Yth. Bapak/Ibu {$teacher->name},\n\n"
                . "Presensi *MASUK* Anda pada {$dateStr} telah tercatat.\n\n"
                . "*Detail Presensi:*\n"
                . "• Waktu  : {$now->format('H:i')} WIB\n"
                . "• Jadwal : {$startStr} WIB\n"
                . "• Status : *{$status}*\n\n"
                . "Mohon lebih memperhatikan waktu presensi.\n\n"
                . "Wassalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                . "Hormat kami,\n*Operator SMK ICB CT*";
        }

        $this->sendWA($teacher->phone, $msg);
    }

    private function sendWaKeluar(User $teacher, Carbon $now, string $endStr, string $status): void
    {
        $dateStr = $now->locale('id')->isoFormat('dddd, D MMMM YYYY');

        $msg = "Assalamualaikum Warahmatullahi Wabarakatuh.\n\n"
            . "Yth. Bapak/Ibu {$teacher->name},\n\n"
            . "Presensi *PULANG* Anda pada {$dateStr} telah tercatat.\n\n"
            . "*Detail Presensi:*\n"
            . "• Waktu  : {$now->format('H:i')} WIB\n"
            . "• Jadwal : {$endStr} WIB\n"
            . "• Status : *{$status}*\n\n"
            . ($status === 'Tepat Waktu'
                ? "Terima kasih atas dedikasi Anda hari ini.\n\n"
                : "Anda melakukan presensi pulang sebelum jadwal. Mohon sesuaikan.\n\n")
            . "Wassalamualaikum Warahmatullahi Wabarakatuh.\n\n"
            . "Hormat kami,\n*Operator SMK ICB CT*";

        $this->sendWA($teacher->phone, $msg);
    }

    private function sendWA(?string $phone, string $message): void
    {
        if (!$phone) return;
        $phone = preg_replace('/^08/', '628', $phone);
        try {
            Http::withHeaders(['Authorization' => env('FONNTE_TOKEN')])
                ->post(env('FONNTE_URL'), [
                    'target'  => $phone,
                    'message' => $message,
                ]);
        } catch (\Exception $e) {
            Log::error("WA Gagal ke {$phone}: " . $e->getMessage());
        }
    }

    private function logScan(User $teacher, string $mode, string $status, string $message, Request $request): void
    {
        try {
            ScanLog::create([
                'user_id'    => $teacher->id,
                'mode'       => $mode,
                'status'     => $status,
                'message'    => $message,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'latitude'   => $request->input('latitude'),
                'longitude'  => $request->input('longitude'),
                'device_info'=> $request->header('X-Device-Name', 'hardware-scanner'),
                'scanned_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('ScanLog failed: ' . $e->getMessage());
        }
    }

    private function fail(string $message, int $code = 422): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], $code);
    }
}
