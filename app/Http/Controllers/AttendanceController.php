<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Halaman Scan QR dengan 2 Mode
     */
    public function scan()
    {
        return view('attendance.scan');
    }

    /**
     * Process QR Code - Auto detect mode (Masuk/Keluar)
     */
    public function store(Request $request)
    {
        $ajaxRequest = $request->expectsJson() || $request->ajax();
        $validated = $request->validate([
            'qr_data'   => 'required|string',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'mode'      => 'nullable|in:masuk,keluar',
        ]);

        try {
            $qrData = json_decode($validated['qr_data'], true);
            
            if (!isset($qrData['teacher_id'], $qrData['token'])) {
                return $ajaxRequest
                    ? response()->json(['success' => false, 'message' => 'QR code tidak valid.'], 422)
                    : back()->with('error', 'QR code tidak valid.');
            }
            
            $teacher = User::find($qrData['teacher_id']);
            
            if (!$teacher || $teacher->role !== 'guru' || $teacher->qr_token !== $qrData['token']) {
                return $ajaxRequest
                    ? response()->json(['success' => false, 'message' => 'QR code tidak valid atau sudah kadaluarsa.'], 422)
                    : back()->with('error', 'QR code tidak valid atau sudah kadaluarsa.');
            }
            
            if (!$teacher->is_active) {
                return $ajaxRequest
                    ? response()->json(['success' => false, 'message' => 'Guru ini tidak aktif.'], 422)
                    : back()->with('error', 'Guru ini tidak aktif.');
            }
            
        } catch (\Exception $e) {
            return $ajaxRequest
                ? response()->json(['success' => false, 'message' => 'Gagal memproses QR code.'], 422)
                : back()->with('error', 'Gagal memproses QR code.');
        }

        if (\App\Models\Holiday::isHoliday(today())) {
            $holidayName = \App\Models\Holiday::getHolidayName(today());
            return $ajaxRequest
                ? response()->json(['success' => false, 'message' => "Hari ini adalah hari libur ({$holidayName}). Tidak perlu absen."], 422)
                : back()->with('error', "Hari ini adalah hari libur ({$holidayName}). Tidak perlu absen.");
        }

        // ✅ CEK: Apakah guru dijadwalkan hari ini?
        if (!\App\Models\TeacherSchedule::isScheduledToday($teacher->id)) {
            return $ajaxRequest
                ? response()->json(['success' => false, 'message' => 'Anda tidak dijadwalkan mengajar hari ini.'], 422)
                : back()->with('error', 'Anda tidak dijadwalkan mengajar hari ini.');
        }

        // ✅ Ambil jadwal hari ini
        $todaySchedule = \App\Models\TeacherSchedule::getTodaySchedule($teacher->id);
        
        if (!$todaySchedule) {
            return $ajaxRequest
                ? response()->json(['success' => false, 'message' => 'Jadwal hari ini tidak ditemukan.'], 422)
                : back()->with('error', 'Jadwal hari ini tidak ditemukan.');
        }

        $attendance = Attendance::where('user_id', $teacher->id)
            ->where('date', today())
            ->first();

        // Mode yang dipilih operator dari UI
        $requestedMode = $request->input('mode', 'masuk');

        // Sudah masuk belum keluar → proses keluar
        if ($attendance && $attendance->check_in && !$attendance->check_out) {
            $this->processKeluar($attendance, $teacher, $validated, $todaySchedule);
            return $ajaxRequest
                ? response()->json(['success' => true, 'message' => 'Presensi keluar berhasil!'])
                : redirect()->route('dashboard')->with('success', 'Presensi keluar berhasil!');
        }

        // Sudah masuk dan sudah keluar → selesai
        if ($attendance && $attendance->check_in && $attendance->check_out) {
            return $ajaxRequest
                ? response()->json(['success' => false, 'message' => 'Presensi hari ini sudah lengkap (masuk & keluar).'], 422)
                : back()->with('error', 'Presensi hari ini sudah lengkap (masuk & keluar).');
        }

        // Belum ada absen sama sekali
        if ($requestedMode === 'keluar') {
            // Operator paksa mode keluar → buat record masuk dulu lalu langsung keluar
            $now = now();
            $newAttendance = Attendance::create([
                'user_id'     => $teacher->id,
                'date'        => today(),
                'check_in'    => $now->format('H:i:s'),
                'check_out'   => null,
                'status'      => 'Tidak Diketahui',
                'latitude'    => $validated['latitude'] ?? null,
                'longitude'   => $validated['longitude'] ?? null,
                'scan_method' => 'qr_code',
            ]);
            $this->processKeluar($newAttendance, $teacher, $validated, $todaySchedule);
            return $ajaxRequest
                ? response()->json(['success' => true, 'message' => 'Presensi keluar berhasil dicatat!'])
                : redirect()->route('dashboard')->with('success', 'Presensi keluar berhasil dicatat!');
        }

        // Mode masuk (default)
        $this->processMasuk($teacher, $validated, $todaySchedule);
        return $ajaxRequest
            ? response()->json(['success' => true, 'message' => 'Presensi masuk berhasil!'])
            : redirect()->route('dashboard')->with('success', 'Presensi masuk berhasil!');
    }

    /**
     * Process Presensi MASUK
     */
    private function processMasuk(User $teacher, array $validated, $todaySchedule = null)
    {
        $now = now();
        $currentTime = Carbon::parse($now->format('H:i'));

        $teacherStartStr = $todaySchedule ? Carbon::parse($todaySchedule->start_time)->format('H:i') : ($teacher->start_time ? Carbon::parse($teacher->start_time)->format('H:i') : '07:30');
        $teacherStart = Carbon::parse($teacherStartStr);
        $status = $currentTime->greaterThan($teacherStart) ? 'Terlambat' : 'Tepat Waktu';

        Attendance::create([
            'user_id' => $teacher->id,
            'date' => today(),
            'check_in' => $now->format('H:i:s'),
            'check_out' => null,
            'status' => $status,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'scan_method' => 'qr_code',
        ]);

        if ($status === 'Tepat Waktu') {
            $msg = "Assalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                . "Yth. Bapak/Ibu {$teacher->name},\n\n"
                . "Dengan ini kami sampaikan bahwa presensi *MASUK* Anda pada hari ini, "
                . $now->locale('id')->isoFormat('dddd, D MMMM YYYY')
                . ", telah tercatat dengan baik.\n\n"
                . "*Detail Presensi:*\n"
                . "• Waktu Presensi: {$now->format('H:i')} WIB\n"
                . "• Jadwal Masuk: {$teacherStartStr} WIB\n"
                . "• Status Kehadiran: *{$status}*\n\n"
                . "Terima kasih atas kedisiplinan Anda. Semoga hari ini menjadi hari yang produktif dan bermanfaat.\n\n"
                . "Wassalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                . "Hormat kami,\n*Operator SMK ICB CT*";
        } else {
            $msg = "Assalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                . "Yth. Bapak/Ibu {$teacher->name},\n\n"
                . "Dengan ini kami sampaikan bahwa presensi *MASUK* Anda pada hari ini, "
                . $now->locale('id')->isoFormat('dddd, D MMMM YYYY')
                . ", telah tercatat.\n\n"
                . "*Detail Presensi:*\n"
                . "• Waktu Presensi: {$now->format('H:i')} WIB\n"
                . "• Jadwal Masuk: {$teacherStartStr} WIB\n"
                . "• Status Kehadiran: *{$status}*\n\n"
                . "Mohon untuk lebih memperhatikan waktu presensi di kemudian hari. Terima kasih atas pengertiannya.\n\n"
                . "Wassalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                . "Hormat kami,\n*Operator SMK ICB CT*";
        }

        $this->sendWA($teacher->phone, $msg);

        return redirect()->route('dashboard')->with('success', 'Presensi masuk berhasil!');
    }

    /**
     * Process Presensi KELUAR
     */
    private function processKeluar(Attendance $attendance, User $teacher, array $validated, $todaySchedule = null)
    {
        $now = now();
        $currentTime = Carbon::parse($now->format('H:i'));

        $teacherEndStr = $todaySchedule ? Carbon::parse($todaySchedule->end_time)->format('H:i') : ($teacher->end_time ? Carbon::parse($teacher->end_time)->format('H:i') : '16:00');
        $teacherEnd = Carbon::parse($teacherEndStr);
        $statusOut = $currentTime->gte($teacherEnd) ? 'Tepat Waktu' : 'Pulang Cepat';

        $checkInTime = Carbon::parse($attendance->check_in);
        $workDuration = $checkInTime->diffInHours($currentTime);

        $attendance->update([
            'check_out' => $now->format('H:i:s'),
            'check_out_status' => $statusOut,
        ]);

        if ($statusOut === 'Tepat Waktu') {
            $msg = "Assalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                . "Yth. Bapak/Ibu {$teacher->name},\n\n"
                . "Dengan ini kami sampaikan bahwa presensi *PULANG* Anda pada hari ini, "
                . $now->locale('id')->isoFormat('dddd, D MMMM YYYY')
                . ", telah tercatat dengan baik.\n\n"
                . "*Detail Presensi:*\n"
                . "• Waktu Presensi: {$now->format('H:i')} WIB\n"
                . "• Jadwal Pulang: {$teacherEndStr} WIB\n"
                . "• Status: *{$statusOut}*\n\n"
                . "Terima kasih atas dedikasi dan kerja keras Anda hari ini. Semoga istirahat Anda berkualitas dan sampai jumpa besok.\n\n"
                . "Wassalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                . "Hormat kami,\n*Operator SMK ICB CT*";
        } else {
            $msg = "Assalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                . "Yth. Bapak/Ibu {$teacher->name},\n\n"
                . "Dengan ini kami sampaikan bahwa presensi *PULANG* Anda pada hari ini, "
                . $now->locale('id')->isoFormat('dddd, D MMMM YYYY')
                . ", telah tercatat.\n\n"
                . "*Detail Presensi:*\n"
                . "• Waktu Presensi: {$now->format('H:i')} WIB\n"
                . "• Jadwal Pulang: {$teacherEndStr} WIB\n"
                . "• Status: *{$statusOut}*\n\n"
                . "Catatan: Anda melakukan presensi pulang sebelum jadwal yang ditentukan. Mohon untuk menyesuaikan dengan jadwal kerja yang berlaku di kemudian hari. Terima kasih atas pengertiannya.\n\n"
                . "Wassalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                . "Hormat kami,\n*Operator SMK ICB CT*";
        }

        $this->sendWA($teacher->phone, $msg);

        return redirect()->route('dashboard')->with('success', "Presensi Keluar Berhasil! Waktu keluar: {$now->format('H:i')} - Durasi kerja: {$workDuration} jam");
    }

    /**
     * Check status attendance untuk AJAX
     */
    public function checkStatus(int $teacherId)
    {
        $attendance = Attendance::where('user_id', $teacherId)
            ->where('date', today())
            ->first();
        
        return response()->json([
            'already_checked_in' => $attendance !== null,
            'checked_out' => $attendance ? $attendance->check_out !== null : false,
            'check_in_time' => $attendance ? $attendance->check_in : null,
            'check_out_time' => $attendance ? $attendance->check_out : null,
            'status' => $attendance ? $attendance->status : null,
        ]);
    }

    /**
     * Kirim pesan WhatsApp via Fonnte
     */
    private function sendWA(string $phone, string $message)
    {
        if (!$phone) return;
        $phone = preg_replace('/^08/', '628', $phone);
        try {
            Http::withHeaders(['Authorization' => env('FONNTE_TOKEN')])
                ->post(env('FONNTE_URL'), [
                    'target' => $phone,
                    'message' => $message,
                ]);
        } catch (\Exception $e) {
            Log::error("WA Gagal ke {$phone}: " . $e->getMessage());
        }
    }
}