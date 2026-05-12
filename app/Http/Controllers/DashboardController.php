<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Setting;
use App\Models\AppSetting;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Mengambil data statistik
        $totalGuru = User::where('role', 'guru')->count();
        
        $hadirHariIni = Attendance::whereDate('date', $today)
            ->whereIn('status', ['Hadir', 'Terlambat'])
            ->count();

        $terlambat = Attendance::whereDate('date', $today)
            ->where('status', 'Terlambat')
            ->count();

        $tidakHadir = Attendance::whereDate('date', $today)
            ->whereIn('status', ['Izin', 'Alpha', 'Sakit'])
            ->count();

        // Mengambil riwayat absensi terbaru (dengan relasi user)
        $recentAttendances = Attendance::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Mengambil data untuk grafik (7 hari terakhir)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Attendance::whereDate('date', $date)
                ->whereIn('status', ['Hadir', 'Terlambat'])
                ->count();
            $chartData[] = $count;
        }

        // Mengambil pengaturan jam (untuk jam masuk/pulang)
        $appSettings = AppSetting::getInstance();

        // Mengirim semua variabel ke view dashboard
        return view('dashboard', compact(
            'totalGuru',
            'hadirHariIni',
            'terlambat',
            'tidakHadir',
            'recentAttendances',
            'appSettings',
            'chartData'
        ));
    }
}