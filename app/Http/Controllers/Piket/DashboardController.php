<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Stat presensi hari ini
        $hadirHariIni    = Attendance::whereDate('date', $today)->where('status', 'Hadir')->count();
        $terlambatHariIni = Attendance::whereDate('date', $today)->where('status', 'Terlambat')->count();
        $totalGuru       = User::whereIn('role', ['guru', 'guru_piket'])->where('is_active', true)->count();
        $belumAbsen      = max(0, $totalGuru - $hadirHariIni - $terlambatHariIni);

        // Pengajuan izin pending
        $pendingLeaves = LeaveRequest::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $pendingCount = LeaveRequest::where('status', 'pending')->count();

        // Presensi terbaru hari ini
        $recentAttendances = Attendance::with('user')
            ->whereDate('date', $today)
            ->orderBy('check_in', 'desc')
            ->take(8)
            ->get();

        return view('piket.dashboard', compact(
            'hadirHariIni', 'terlambatHariIni', 'totalGuru', 'belumAbsen',
            'pendingLeaves', 'pendingCount', 'recentAttendances'
        ));
    }
}
