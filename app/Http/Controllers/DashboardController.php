<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Setting;
use App\Models\AppSetting;
use App\Models\Holiday;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalGuru = User::where('role', 'guru')->count();
        
        $hadirHariIni = Attendance::whereDate('date', $today)
            ->whereIn('status', ['Hadir', 'Terlambat', 'Tepat Waktu'])
            ->count();

        $terlambat = Attendance::whereDate('date', $today)
            ->where('status', 'Terlambat')
            ->count();

        $tidakHadir = Attendance::whereDate('date', $today)
            ->whereIn('status', ['Izin', 'Alpha', 'Sakit'])
            ->count();

        $izinCuti = Attendance::whereDate('date', $today)
            ->whereIn('status', ['Izin', 'Sakit', 'Cuti'])
            ->count();

        $recentAttendances = Attendance::with('user')
            ->latest()
            ->take(5)
            ->get();

        // 4 Data chart untuk 30 hari terakhir (support mode 3/7/14/30 hari)
        $chartHadirData = [];
        $chartTerlambatData = [];
        $chartTidakHadirData = [];
        $chartIzinData = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            $chartHadirData[] = Attendance::whereDate('date', $date)
                ->whereIn('status', ['Hadir', 'Tepat Waktu'])->count();
            
            $chartTerlambatData[] = Attendance::whereDate('date', $date)
                ->where('status', 'Terlambat')->count();
            
            $chartTidakHadirData[] = Attendance::whereDate('date', $date)
                ->where('status', 'Alpha')->count();
            
            $chartIzinData[] = Attendance::whereDate('date', $date)
                ->whereIn('status', ['Izin', 'Sakit', 'Cuti'])->count();
        }

        $appSettings = AppSetting::getInstance();

        // Ambil data holiday untuk 30 hari terakhir
        $holidayDates = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            if (Holiday::isHoliday($date)) {
                $holidayName = Holiday::getHolidayName($date);
                $holidayDates[] = [
                    'date'  => $date->format('Y-m-d'),
                    'name'  => $holidayName,
                    'type'  => $date->isWeekend() ? 'weekend' : 'holiday',
                ];
            }
        }

        return view('dashboard', compact(
            'totalGuru',
            'hadirHariIni',
            'terlambat',
            'tidakHadir',
            'izinCuti',
            'chartHadirData',
            'chartTerlambatData',
            'chartTidakHadirData',
            'chartIzinData',
            'recentAttendances',
            'appSettings',
            'holidayDates'
        ));
    }
}