<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeachingSchedule;
use App\Models\TeacherSchedule;
use App\Models\Attendance;
use App\Models\ClassAttendance;
use App\Models\LeaveRequest;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $todayDayOfWeek = $today->dayOfWeek;

        // Jadwal mengajar hari ini (with class attendances) — match by teaching_schedule_id ATAU classroom_id
        $todaySchedules = TeachingSchedule::where('user_id', $user->id)
            ->where('day_of_week', $todayDayOfWeek)
            ->where('is_active', true)
            ->with(['classroom', 'subject', 'classAttendances' => function ($query) use ($user, $today) {
                $query->where('user_id', $user->id)
                      ->whereDate('date', $today);
            }])
            ->orderBy('start_time')
            ->get();

        // Ambil semua presensi kelas hari ini (termasuk shared space yang tidak punya teaching_schedule_id)
        $allTodayClassAttendances = ClassAttendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->get()
            ->keyBy('teaching_schedule_id');

        // Inject attendances ke jadwal jika classAttendances kosong tapi ada data via teaching_schedule_id
        foreach ($todaySchedules as $schedule) {
            if ($schedule->classAttendances->isEmpty() && isset($allTodayClassAttendances[$schedule->id])) {
                $schedule->classAttendances->push($allTodayClassAttendances[$schedule->id]);
            }
        }

        // JADWAL KERJA (Work Schedule)
        $workSchedule = TeacherSchedule::where('user_id', $user->id)
            ->where('is_active', true)
            ->get()
            ->sortBy('day_of_week');

        // Absensi hari ini
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        // Presensi kelas hari ini
        $todayClassAttendances = ClassAttendance::with('classroom')
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->get();

        // Statistik bulan ini
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        // Status 'Hadir' di admin scan disimpan sebagai 'Tepat Waktu', perlu cover keduanya
        $stats = [
            'hadir' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->whereIn('status', ['Hadir', 'Tepat Waktu'])
                ->count(),
            'terlambat' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('status', 'Terlambat')
                ->count(),
            // Izin dihitung dari LeaveRequest yang disetujui (bukan dari Attendance)
            'izin' => LeaveRequest::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                      ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth]);
                })
                ->count(),
            'alpha' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('status', 'Alpha')
                ->count(),
        ];

        return view('teacher.dashboard', compact(
            'todaySchedules',
            'workSchedule',
            'todayAttendance',
            'todayClassAttendances',
            'stats'
        ));
    }
}