<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Setting;
use App\Models\TeacherSchedule;
use App\Models\Teacher as TeacherModel;
use App\Helpers\GpsHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // Presensi Harian (Datang/Pulang)
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $todayDayOfWeek = $today->dayOfWeek;

        // Presensi hari ini
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        // Ambil jam masuk/pulang dari jadwal mengajar hari ini
        $todaySchedule = TeacherSchedule::where('user_id', $user->id)
            ->where('day_of_week', $todayDayOfWeek)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->first();

        $scheduleStart = $todaySchedule ? $todaySchedule->start_time : null;
        $scheduleEnd = $todaySchedule ? $todaySchedule->end_time : null;

        // Riwayat 7 hari terakhir
        $recentAttendance = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(7)
            ->get();

        if (!$user->qr_code_url) {
            $user->generateQrCode();
            $user->refresh();
        }

        $qrCodeUrl = $user->qr_code_url;

        return view('teacher.attendance', compact(
            'todayAttendance',
            'recentAttendance',
            'scheduleStart',
            'scheduleEnd',
            'qrCodeUrl'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'qr_data' => 'required|string',
            'mode' => 'required|in:masuk,keluar',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $user = auth()->user();
        $now = Carbon::now();
        $today = $now->toDateString();

        // Parse QR data — QR guru di-generate dengan field 'teacher_id' (bukan 'user_id')
        try {
            $qrData = json_decode($validated['qr_data'], true);
            if (is_array($qrData)) {
                $userId = $qrData['teacher_id'] ?? $qrData['user_id'] ?? null;
                $token = $qrData['token'] ?? null;

                if ($userId && $userId != $user->id) {
                    return $this->_jsonResp(false, 'QR Code tidak valid untuk akun Anda.');
                }
                if ($token && $user->qr_token && $token !== $user->qr_token) {
                    return $this->_jsonResp(false, 'Token QR Code tidak sesuai.');
                }
            } else {
                if ($validated['qr_data'] !== $user->qr_token && $validated['qr_data'] != $user->id) {
                    return $this->_jsonResp(false, 'Format QR Code tidak valid.');
                }
            }
        } catch (\Exception $e) {
            if ($validated['qr_data'] !== $user->qr_token && $validated['qr_data'] != $user->id) {
                return $this->_jsonResp(false, 'Format QR Code tidak valid.');
            }
        }

        // ===== GPS VALIDATION =====
        $gpsValidationStatus = Setting::get('gps_validation_status', 'on');
        if ($gpsValidationStatus === 'on') {
            $gpsValidation = GpsHelper::validateLocation($request->input('latitude'), $request->input('longitude'));
            if (!$gpsValidation['valid']) {
                return $this->_jsonResp(false, $gpsValidation['message']);
            }
        }
        // ===== END GPS VALIDATION =====

        if ($validated['mode'] === 'masuk') {
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->firstOrCreate(['user_id' => $user->id, 'date' => $today]);

            if ($attendance->check_in) {
                return $this->_jsonResp(false, 'Anda sudah melakukan presensi masuk hari ini.', [
                    'already_scanned' => true,
                    'check_in' => Carbon::parse($attendance->check_in)->format('H:i'),
                ]);
            }

            // Hitung threshold terlambat: prioritas TeacherSchedule hari ini > default_check_in > setting global
            $lateThreshold = null;
            $graceMinutes = (int) Setting::get('attendance_late_grace_period', 5);

            // 1. Coba dari TeacherSchedule hari ini (yang diatur admin/operator)
            $todaySchedule = TeacherSchedule::where('user_id', $user->id)
                ->where('day_of_week', $now->dayOfWeek)
                ->where('is_active', true)
                ->first();
            if ($todaySchedule && $todaySchedule->start_time) {
                $lateThreshold = Carbon::parse($todaySchedule->start_time)->addMinutes($graceMinutes);
            }

            // 2. Fallback ke default_check_in di profil pengguna
            if (!$lateThreshold && $user->default_check_in) {
                $lateThreshold = Carbon::parse($user->default_check_in)->addMinutes($graceMinutes);
            }

            // 3. Fallback ke setting global
            if (!$lateThreshold) {
                $startTimeStr = Setting::get('attendance_start_time', '06:30');
                try {
                    $lateThreshold = Carbon::parse($startTimeStr)->addMinutes($graceMinutes);
                } catch (\Exception $e) {
                    // ignore
                }
            }

            $isLate = $lateThreshold ? $now->gt($lateThreshold) : false;
            $attendance->update([
                'check_in' => $now->format('H:i:s'),
                'status' => $isLate ? 'Terlambat' : 'Hadir',
                'check_in_latitude' => $request->input('latitude'),
                'check_in_longitude' => $request->input('longitude'),
            ]);

        return response()->json([
            'success'      => true,
            'message'      => 'Presensi masuk berhasil dicatat!',
            'check_in'     => $now->format('H:i'),
            'check_in_ts'  => $now->timestamp,
            'status'       => $isLate ? 'Terlambat' : 'Hadir',
            'mode'         => 'masuk',
        ]);
        } else {
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();

            if (!$attendance || !$attendance->check_in) {
                return $this->_jsonResp(false, 'Anda belum melakukan presensi masuk.', ['already_scanned' => false]);
            }
            if ($attendance->check_out) {
                return $this->_jsonResp(false, 'Anda sudah melakukan presensi pulang hari ini.', [
                    'already_scanned' => true,
                    'check_out' => Carbon::parse($attendance->check_out)->format('H:i'),
                ]);
            }

            $attendance->update([
                'check_out' => $now->format('H:i:s'),
                'check_out_latitude' => $request->input('latitude'),
                'check_out_longitude' => $request->input('longitude'),
            ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Presensi pulang berhasil dicatat!',
            'check_out'  => $now->format('H:i'),
            'check_out_ts' => $now->timestamp,
            'mode'       => 'keluar',
        ]);
        }
    }

    private function _jsonResp(bool $success, string $message, array $data = []): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => $success, 'message' => $message, ...$data]);
        }
        return back()->with($success ? 'success' : 'error', $message);
    }

    /**
     * Polling endpoint — dipanggil tiap 300ms dari halaman presensi guru.
     * Menambahkan fields tambahan untuk deteksi state yang lebih akurat.
     */
    public function pollStatus(): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $att  = Attendance::where('user_id', $user->id)
                    ->whereDate('date', Carbon::today())
                    ->first();

        $hasCheckIn  = (bool) ($att?->check_in);
        $hasCheckOut = (bool) ($att?->check_out);

        return response()->json([
            'has_checkin'   => $hasCheckIn,
            'has_checkout'  => $hasCheckOut,
            'check_in'      => $att?->check_in  ? Carbon::parse($att->check_in)->format('H:i')  : null,
            'check_out'     => $att?->check_out ? Carbon::parse($att->check_out)->format('H:i') : null,
            'check_in_ts'   => $att?->check_in  ? Carbon::parse($att->check_in)->timestamp  : null,
            'check_out_ts'  => $att?->check_out ? Carbon::parse($att->check_out)->timestamp : null,
            'status'        => $att?->status ?? null,
            // Fields untuk deteksi state modal
            'checkin_done'  => $hasCheckIn && !$hasCheckOut,   // baru masuk, belum pulang
            'both_done'     => $hasCheckIn && $hasCheckOut,     // sudah lengkap
        ]);
    }

}
