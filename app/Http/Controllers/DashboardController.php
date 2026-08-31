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
            ->whereIn('status', [
                User::STATUS_HADIR,
                User::STATUS_TERLAMBAT,
                User::STATUS_TEPAT_WAKTU,
            ])
            ->count();

        $terlambat = Attendance::whereDate('date', $today)
            ->where('status', User::STATUS_TERLAMBAT)
            ->count();

        $tidakHadir = Attendance::whereDate('date', $today)
            ->whereIn('status', [
                User::STATUS_IZIN,
                User::STATUS_ALPHA,
                User::STATUS_SAKIT,
            ])
            ->count();

        $izinCuti = Attendance::whereDate('date', $today)
            ->whereIn('status', [
                User::STATUS_IZIN,
                User::STATUS_SAKIT,
                User::STATUS_CUTI,
            ])
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
        
        $stats = Attendance::selectRaw('
            DATE(date) as date_str,
            SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as hadir,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as terlambat,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as alpha,
            SUM(CASE WHEN status IN (?, ?, ?) THEN 1 ELSE 0 END) as izin
        ', [User::STATUS_HADIR, User::STATUS_TEPAT_WAKTU, User::STATUS_TERLAMBAT, User::STATUS_ALPHA, User::STATUS_IZIN, User::STATUS_SAKIT, User::STATUS_CUTI])
            ->whereBetween('date', [Carbon::today()->subDays(29), Carbon::today()])
            ->groupBy('date_str')
            ->orderBy('date_str')
            ->get();

        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::today()->subDays(29 - $i);
            $dateStr = $date->toDateString();
            $row = $stats->firstWhere('date_str', $dateStr);
            $chartHadirData[]   = $row ? (int) $row->hadir : 0;
            $chartTerlambatData[] = $row ? (int) $row->terlambat : 0;
            $chartTidakHadirData[] = $row ? (int) $row->alpha : 0;
            $chartIzinData[]    = $row ? (int) $row->izin : 0;
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