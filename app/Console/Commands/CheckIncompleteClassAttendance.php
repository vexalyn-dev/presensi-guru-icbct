<?php

namespace App\Console\Commands;

use App\Models\ClassAttendance;
use App\Models\Holiday;
use App\Models\TeachingSchedule;
use App\Models\User;
use App\Helpers\NotificationHelper;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CheckIncompleteClassAttendance extends Command
{
    protected $signature = 'attendance:check-incomplete';
    protected $description = 'Check incomplete class attendance and notify admin';

    public function handle()
    {
        $today = Carbon::today();
        $dayOfWeek = $today->dayOfWeek;
        $now = Carbon::now();

        if (Holiday::isHoliday($today)) {
            $this->info('Hari libur - tidak ada pengecekan presensi kelas.');
            return Command::SUCCESS;
        }

        $schedules = TeachingSchedule::where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->with(['user', 'classroom', 'subject'])
            ->get();

        $incompleteCount = 0;

        foreach ($schedules as $schedule) {
            if (!$schedule->user) {
                continue;
            }

            $endTime = Carbon::parse($today->format('Y-m-d') . ' ' . $schedule->end_time);

            if (!$now->gt($endTime->copy()->addMinutes(15))) {
                continue;
            }

            $attendance = ClassAttendance::where('user_id', $schedule->user_id)
                ->where('classroom_id', $schedule->classroom_id)
                ->where('period', $schedule->period)
                ->whereDate('date', $today)
                ->first();

            if ($attendance && $attendance->check_out_time) {
                continue;
            }

            $cacheKey = "incomplete_class_{$schedule->id}_{$today->toDateString()}";
            if (!Cache::add($cacheKey, true, now()->endOfDay())) {
                continue;
            }

            $incompleteCount++;
            $className = $schedule->classroom?->name ?? '-';
            $subjectName = $schedule->subject?->name ?? '-';

            NotificationHelper::send(
                $schedule->user,
                'warning',
                'Presensi Kelas Tidak Lengkap',
                "Anda belum scan keluar di kelas {$className} ({$subjectName}). Silakan hubungi admin jika ada kendala.",
                route('teacher.class-attendance'),
                'alert-triangle',
                'bg-yellow-100 text-yellow-600'
            );

            foreach (User::whereIn('role', ['admin', 'operator'])->get() as $admin) {
                NotificationHelper::send(
                    $admin,
                    'warning',
                    'Presensi Tidak Lengkap',
                    "{$schedule->user->name} belum scan keluar di kelas {$className} ({$subjectName}) - Jam ke-{$schedule->period}",
                    route('admin.live-monitoring.index'),
                    'alert-triangle',
                    'bg-yellow-100 text-yellow-600'
                );
            }

            $this->info("Notifikasi dikirim: {$schedule->user->name} - {$className}");
        }

        $this->info("Selesai! {$incompleteCount} presensi tidak lengkap.");
        return Command::SUCCESS;
    }
}
