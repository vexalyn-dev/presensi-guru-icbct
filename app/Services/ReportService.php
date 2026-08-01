<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\ClassAttendance;
use App\Models\User;
use Carbon\Carbon;

class ReportService
{
    /**
     * Laporan Presensi Harian — status kehadiran, ketepatan, scan tidak lengkap
     */
    public function getAttendanceReport(int $month, int $year, ?int $userId = null): array
    {
        $query = Attendance::with('user:id,name,teacher_code')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereHas('user', fn($q) => $q->where('role', 'guru'));

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $records = $query->get();

        return $records
            ->groupBy('user_id')
            ->map(function ($items) {
                $user     = $items->first()->user;
                $total    = $items->count();
                $hadir    = $items->whereIn('status', ['Hadir', 'Tepat Waktu'])->count();
                $telat    = $items->where('status', 'Terlambat')->count();
                $izin     = $items->whereIn('status', ['Izin', 'Sakit', 'Cuti'])->count();
                $alpha    = $items->where('status', 'Alpha')->count();

                // Scan tidak lengkap = ada check_in tapi tidak ada check_out
                $incomplete = $items->filter(fn($a) => $a->check_in && !$a->check_out)->count();

                $tepatWaktu = $hadir;
                $pct = $total > 0 ? round(($tepatWaktu / $total) * 100, 1) : 0;

                return [
                    'user'                => $user,
                    'total'               => $total,
                    'hadir'               => $hadir,
                    'telat'               => $telat,
                    'izin_sakit'          => $izin,
                    'alpha'               => $alpha,
                    'incomplete_scans'    => $incomplete,
                    'persentase_ketepatan'=> $pct,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Laporan Kinerja — durasi rata-rata mengajar dari ClassAttendance
     */
    public function getPerformanceReport(int $month, int $year, ?int $userId = null): array
    {
        $query = ClassAttendance::with('user:id,name,teacher_code', 'subject:id,name')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereNotNull('check_in_time')
            ->whereNotNull('check_out_time')
            ->whereHas('user', fn($q) => $q->where('role', 'guru'));

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $records = $query->get();

        $performance = $records
            ->groupBy('user_id')
            ->map(function ($items) {
                $user        = $items->first()->user;
                $totalMenit  = 0;
                $validSesi   = 0;

                foreach ($items as $ca) {
                    try {
                        $base = $ca->date instanceof \Carbon\Carbon
                            ? $ca->date->format('Y-m-d')
                            : (string) $ca->date;
                        $in  = Carbon::parse($base . ' ' . $ca->check_in_time);
                        $out = Carbon::parse($base . ' ' . $ca->check_out_time);
                        $diff = $in->diffInMinutes($out);
                        if ($diff > 0 && $diff < 1440) {
                            $totalMenit += $diff;
                            $validSesi++;
                        }
                    } catch (\Exception $e) {
                        // skip record rusak
                    }
                }

                $avg = $validSesi > 0 ? round($totalMenit / $validSesi) : 0;

                return [
                    'user'             => $user,
                    'total_sesi'       => $validSesi,
                    'total_menit'      => $totalMenit,
                    'avg_menit'        => $avg,
                    'rata_rata_durasi' => floor($avg / 60) . 'j ' . ($avg % 60) . 'm',
                ];
            })
            ->sortByDesc('avg_menit')
            ->values()
            ->toArray();

        // Grafik tren per hari
        $chartData = ClassAttendance::selectRaw('DATE(date) as tgl, COUNT(*) as total')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereNotNull('check_in_time')
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->pluck('total', 'tgl')
            ->toArray();

        return [
            'performance' => $performance,
            'chart_data'  => $chartData,
        ];
    }
}
