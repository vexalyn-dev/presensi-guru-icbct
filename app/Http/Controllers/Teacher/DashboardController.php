<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeachingSchedule;
use App\Models\Attendance;
use App\Models\ClassAttendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $todayDayOfWeek = $today->dayOfWeek;

        // Jadwal mengajar hari ini
        $todaySchedules = TeachingSchedule::with(['classroom', 'subject'])
            ->where('user_id', $user->id)
            ->where('day_of_week', $todayDayOfWeek)
            ->where('is_active', true)
            ->orderBy('period')
            ->get();

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
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();

        $monthlyStats = [
            'total_days' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->count(),
            'hadir' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', 'Hadir')
                ->count(),
            'terlambat' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', 'Terlambat')
                ->count(),
            'izin' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', 'Izin')
                ->count(),
            'alpha' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', 'Alpha')
                ->count(),
        ];

        return view('teacher.dashboard', compact(
            'todaySchedules',
            'todayAttendance',
            'todayClassAttendances',
            'monthlyStats'
        ));
    }
}