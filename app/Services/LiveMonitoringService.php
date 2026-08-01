<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\ClassAttendance;
use App\Models\TeachingSchedule;
use App\Models\User;
use Carbon\Carbon;

class LiveMonitoringService
{
    /**
     * Ambil semua data monitoring real-time
     */
    public function getLiveData(): array
    {
        $now          = Carbon::now();
        $today        = $now->toDateString();
        $dayOfWeek    = $now->dayOfWeek; // 0=Sunday, 1=Monday, ..., 6=Saturday
        $currentTime  = $now->format('H:i:s');

        return [
            'sedang_mengajar'   => $this->getSedangMengajar($today, $currentTime),
            'belum_scan_masuk'  => $this->getBelumScanMasuk($today, $dayOfWeek, $currentTime),
            'belum_scan_keluar' => $this->getBelumScanKeluar($today),
            'sudah_selesai'     => $this->getSudahSelesai($today),
            'stats'             => $this->getStats($today),
            'waktu_server'      => $now->format('H:i:s'),
            'updated_at'        => $now->format('H:i:s'),
        ];
    }

    /**
     * 1. Guru yang SEDANG MENGAJAR sekarang
     * Punya ClassAttendance: check_in_time ada, check_out_time null, date=hari ini
     */
    private function getSedangMengajar(string $today, string $currentTime): array
    {
        return ClassAttendance::with([
                'user:id,name,teacher_code,photo',
                'classroom:id,name,code',
                'subject:id,name',
                'teachingSchedule:id,period,start_time,end_time',
            ])
            ->whereDate('date', $today)
            ->whereNotNull('check_in_time')
            ->whereNull('check_out_time')
            ->get()
            ->map(function ($ca) {
                $checkInTs = Carbon::parse($ca->date->format('Y-m-d') . ' ' . $ca->check_in_time)->timestamp;
                return [
                    'id'              => $ca->id,
                    'user'            => [
                        'id'           => $ca->user->id,
                        'name'         => $ca->user->name,
                        'teacher_code' => $ca->user->teacher_code,
                        'photo'        => $ca->user->photo ? asset('storage/' . $ca->user->photo) : null,
                        'initial'      => strtoupper(substr($ca->user->name, 0, 1)),
                    ],
                    'subject'         => $ca->subject->name ?? '-',
                    'classroom'       => $ca->classroom->name ?? '-',
                    'classroom_code'  => $ca->classroom->code ?? '-',
                    'period'          => $ca->period,
                    'check_in_time'   => substr($ca->check_in_time, 0, 5),
                    'timestamp_masuk' => $checkInTs,
                    'status'          => 'mengajar',
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * 2. Guru yang punya jadwal hari ini tapi BELUM SCAN MASUK
     * Ada TeachingSchedule hari ini yang jam mulainya sudah lewat, tapi tidak ada ClassAttendance
     */
    private function getBelumScanMasuk(string $today, int $dayOfWeek, string $currentTime): array
    {
        // Ambil semua jadwal hari ini yang jam mulainya sudah lewat
        $schedules = TeachingSchedule::with([
                'user:id,name,teacher_code,photo',
                'classroom:id,name,code',
                'subject:id,name',
            ])
            ->where('day_of_week', $dayOfWeek)
            ->whereNotNull('start_time')
            ->where('start_time', '<=', $currentTime)
            ->get();

        $result = [];
        foreach ($schedules as $schedule) {
            // Cek apakah sudah scan masuk hari ini
            $sudahScan = ClassAttendance::where('user_id', $schedule->user_id)
                ->whereDate('date', $today)
                ->where('period', $schedule->period)
                ->whereNotNull('check_in_time')
                ->exists();

            if (!$sudahScan) {
                $terlambatMenit = (int) Carbon::parse($schedule->start_time)->diffInMinutes(Carbon::now());
                $result[] = [
                    'user'            => [
                        'id'           => $schedule->user->id,
                        'name'         => $schedule->user->name,
                        'teacher_code' => $schedule->user->teacher_code,
                        'photo'        => $schedule->user->photo ? asset('storage/' . $schedule->user->photo) : null,
                        'initial'      => strtoupper(substr($schedule->user->name, 0, 1)),
                    ],
                    'subject'         => $schedule->subject->name ?? '-',
                    'classroom'       => $schedule->classroom->name ?? '-',
                    'period'          => $schedule->period,
                    'jam_mulai'       => substr($schedule->start_time ?? '', 0, 5),
                    'terlambat_menit' => max(0, $terlambatMenit),
                    'status'          => $terlambatMenit > 15 ? 'terlambat_parah' : 'belum_masuk',
                ];
            }
        }

        return $result;
    }

    /**
     * 3. Guru yang sudah scan MASUK tapi belum scan KELUAR (masih di sekolah)
     * Presensi harian: check_in ada, check_out null
     */
    private function getBelumScanKeluar(string $today): array
    {
        return Attendance::with('user:id,name,teacher_code,photo')
            ->whereDate('date', $today)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->whereHas('user', fn($q) => $q->where('role', 'guru'))
            ->get()
            ->map(function ($att) {
                $checkIn = $att->check_in instanceof \Carbon\Carbon
                    ? $att->check_in
                    : Carbon::parse($att->date . ' ' . $att->check_in);

                return [
                    'user'            => [
                        'id'           => $att->user->id,
                        'name'         => $att->user->name,
                        'teacher_code' => $att->user->teacher_code,
                        'photo'        => $att->user->photo ? asset('storage/' . $att->user->photo) : null,
                        'initial'      => strtoupper(substr($att->user->name, 0, 1)),
                    ],
                    'check_in_time'   => $checkIn->format('H:i'),
                    'timestamp_masuk' => $checkIn->timestamp,
                    'status_presensi' => $att->status ?? '-',
                    'status'          => 'di_sekolah',
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * 4. Guru yang sudah selesai mengajar (ClassAttendance lengkap IN + OUT hari ini)
     */
    private function getSudahSelesai(string $today): array
    {
        return ClassAttendance::with([
                'user:id,name,teacher_code,photo',
                'subject:id,name',
                'classroom:id,name',
            ])
            ->whereDate('date', $today)
            ->whereNotNull('check_in_time')
            ->whereNotNull('check_out_time')
            ->get()
            ->map(function ($ca) {
                $checkIn  = Carbon::parse($ca->date->format('Y-m-d') . ' ' . $ca->check_in_time);
                $checkOut = Carbon::parse($ca->date->format('Y-m-d') . ' ' . $ca->check_out_time);
                $durasiMenit = $checkIn->diffInMinutes($checkOut);

                return [
                    'user'           => [
                        'id'      => $ca->user->id,
                        'name'    => $ca->user->name,
                        'initial' => strtoupper(substr($ca->user->name, 0, 1)),
                    ],
                    'subject'        => $ca->subject->name ?? '-',
                    'classroom'      => $ca->classroom->name ?? '-',
                    'period'         => $ca->period,
                    'check_in_time'  => substr($ca->check_in_time, 0, 5),
                    'check_out_time' => substr($ca->check_out_time, 0, 5),
                    'durasi_menit'   => $durasiMenit,
                    'durasi_label'   => floor($durasiMenit / 60) . 'j ' . ($durasiMenit % 60) . 'm',
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Stats ringkasan hari ini
     */
    private function getStats(string $today): array
    {
        $totalGuru     = User::where('role', 'guru')->where('is_active', true)->count();
        $sudahMasuk    = Attendance::whereDate('date', $today)->whereNotNull('check_in')->whereHas('user', fn($q) => $q->where('role', 'guru'))->count();
        $sudahKeluar   = Attendance::whereDate('date', $today)->whereNotNull('check_out')->whereHas('user', fn($q) => $q->where('role', 'guru'))->count();
        $sedangMengajar = ClassAttendance::whereDate('date', $today)->whereNotNull('check_in_time')->whereNull('check_out_time')->count();

        return [
            'total_guru'      => $totalGuru,
            'sudah_masuk'     => $sudahMasuk,
            'sudah_keluar'    => $sudahKeluar,
            'sedang_mengajar' => $sedangMengajar,
            'belum_masuk'     => max(0, $totalGuru - $sudahMasuk),
        ];
    }
}
