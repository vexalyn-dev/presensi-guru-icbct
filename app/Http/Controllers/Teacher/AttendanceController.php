<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\ClassAttendance;
use App\Models\TeachingSchedule;
use App\Models\TeacherSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // Absensi Harian (Datang/Pulang)
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $todayDayOfWeek = $today->dayOfWeek;

        // Absensi hari ini
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

        return view('teacher.attendance', compact(
            'todayAttendance',
            'recentAttendance',
            'scheduleStart',
            'scheduleEnd'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'qr_data' => 'required|string',
            'mode' => 'required|in:masuk,keluar',
        ]);

        $user = auth()->user();
        $now = Carbon::now();
        $today = $now->toDateString();

        // Parse QR data
        try {
            $qrData = json_decode($validated['qr_data'], true);
            if (!isset($qrData['user_id']) || $qrData['user_id'] != $user->id) {
                return back()->with('error', 'QR Code tidak valid untuk akun Anda.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Format QR Code tidak valid.');
        }

        // Gunakan jadwal default dari profil pengguna (TIDAK memakai TeacherSchedule)
        $scheduleStart = $user->default_check_in ? Carbon::parse($user->default_check_in) : null;

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['status' => 'Hadir']
        );

        if ($validated['mode'] === 'masuk') {
            if ($attendance->check_in) {
                return back()->with('error', 'Anda sudah melakukan presensi masuk hari ini.');
            }

            if ($scheduleStart) {
                $isLate = $now->format('H:i:s') > $scheduleStart->format('H:i:s');
            } else {
                $isLate = false;
            }
            $attendance->update([
                'check_in' => $now->format('H:i:s'),
                'status' => $isLate ? 'Terlambat' : 'Hadir',
            ]);

            return back()->with('success', 'Presensi masuk berhasil dicatat!');
        } else {
            if (!$attendance->check_in) {
                return back()->with('error', 'Anda belum melakukan presensi masuk.');
            }
            if ($attendance->check_out) {
                return back()->with('error', 'Anda sudah melakukan presensi pulang hari ini.');
            }

            $attendance->update([
                'check_out' => $now->format('H:i:s'),
            ]);

            return back()->with('success', 'Presensi pulang berhasil dicatat!');
        }
    }

    // Presensi Kelas
    public function classAttendance()
    {
        $user = auth()->user();
        $today = Carbon::today();

        $todaySchedules = TeachingSchedule::with(['classroom', 'subject'])
            ->where('user_id', $user->id)
            ->where('day_of_week', $today->dayOfWeek)
            ->where('is_active', true)
            ->orderBy('period')
            ->get();

        $todayClassAttendances = ClassAttendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->with('classroom')
            ->get()
            ->keyBy(function($att) {
                return $att->classroom_id . '_' . $att->period;
            });

        return view('teacher.class-attendance', compact('todaySchedules', 'todayClassAttendances'));
    }

    public function storeClassAttendance(Request $request)
    {
        $validated = $request->validate([
            'qr_data' => 'required|string',
        ]);

        $user = auth()->user();
        $now = Carbon::now();
        $today = $today = $now->toDateString();

        // Parse QR data
        try {
            $qrData = json_decode($validated['qr_data'], true);
            if (!isset($qrData['type']) || $qrData['type'] !== 'classroom') {
                return back()->with('error', 'QR Code bukan QR kelas.');
            }

            $classroom = Classroom::where('id', $qrData['classroom_id'])
                ->where('qr_token', $qrData['token'])
                ->first();

            if (!$classroom) {
                return back()->with('error', 'QR Code kelas tidak dikenali.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Format QR Code tidak valid.');
        }

        // Cari jadwal yang cocok
        $schedule = TeachingSchedule::where('user_id', $user->id)
            ->where('classroom_id', $classroom->id)
            ->where('day_of_week', $now->dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$schedule) {
            return back()->with('error', 'Anda tidak memiliki jadwal mengajar di kelas ini hari ini.');
        }

        $existingAttendance = ClassAttendance::where('user_id', $user->id)
            ->where('classroom_id', $classroom->id)
            ->where('date', $today)
            ->where('period', $schedule->period)
            ->first();

        $scheduleStart = Carbon::parse($schedule->start_time);
        $status = $now->format('H:i:s') > $scheduleStart->format('H:i:s') ? 'Terlambat' : 'Hadir';

        if (!$existingAttendance) {
            ClassAttendance::create([
                'user_id' => $user->id,
                'classroom_id' => $classroom->id,
                'teaching_schedule_id' => $schedule->id,
                'date' => $today,
                'period' => $schedule->period,
                'check_in_time' => $now->format('H:i:s'),
                'status' => $status,
            ]);

            return back()->with('success', "Presensi masuk kelas {$classroom->name} berhasil!");
        } elseif ($existingAttendance->check_in_time && !$existingAttendance->check_out_time) {
            $existingAttendance->update([
                'check_out_time' => $now->format('H:i:s'),
            ]);

            return back()->with('success', "Presensi keluar kelas {$classroom->name} berhasil!");
        } else {
            return back()->with('error', "Presensi untuk kelas {$classroom->name} sudah selesai.");
        }
    }
}