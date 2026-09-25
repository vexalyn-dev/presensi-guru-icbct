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
            if ($attendance->check_in) {
                return $this->_jsonResp(false, 'Anda sudah melakukan presensi masuk hari ini.');
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

            $isLate = $lateThreshold && $now->format('H:i:s') > $lateThreshold->format('H:i:s');
            $attendance->update([
                'check_in' => $now->format('H:i:s'),
                'status' => $isLate ? 'Terlambat' : 'Hadir',
                'check_in_latitude' => $request->input('latitude'),
                'check_in_longitude' => $request->input('longitude'),
            ]);

            return $this->_jsonResp(true, 'Presensi masuk berhasil dicatat!', [
                'check_in' => $now->format('H:i'),
                'status' => $isLate ? 'Terlambat' : 'Hadir',
            ]);
        } else {
            if (!$attendance->check_in) {
                return $this->_jsonResp(false, 'Anda belum melakukan presensi masuk.');
            }
            if ($attendance->check_out) {
                return $this->_jsonResp(false, 'Anda sudah melakukan presensi pulang hari ini.');
            }

            $attendance->update([
                'check_out' => $now->format('H:i:s'),
                'check_out_latitude' => $request->input('latitude'),
                'check_out_longitude' => $request->input('longitude'),
            ]);

            return $this->_jsonResp(true, 'Presensi pulang berhasil dicatat!', [
                'check_out' => $now->format('H:i'),
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

}
