<?php

namespace App\Console\Commands;

use App\Helpers\NotificationHelper;
use App\Models\TeachingSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendTeachingScheduleReminder extends Command
{
    protected $signature = 'reminder:teaching-schedule';
    protected $description = 'Send reminder 10 minutes before teaching schedule';

    public function handle()
    {
        $now = Carbon::now();
        $currentDayOfWeek = $now->dayOfWeek;
        $currentTime = $now->format('H:i');
        
        // Get schedules that start in 10 minutes
        $schedules = TeachingSchedule::with('user')
            ->where('day_of_week', $currentDayOfWeek)
            ->where('is_active', true)
            ->get();

        foreach ($schedules as $schedule) {
            $scheduleTime = Carbon::parse($schedule->start_time);
            $tenMinutesBefore = $scheduleTime->copy()->subMinutes(10);
            
            // Check if current time is within reminder window (10 minutes before)
            if ($currentTime >= $tenMinutesBefore->format('H:i') && $currentTime <= $scheduleTime->format('H:i')) {
                // Check if teacher hasn't checked in for this class yet
                $hasCheckedIn = \App\Models\ClassAttendance::where('user_id', $schedule->user_id)
                    ->whereDate('date', $now)
                    ->where('classroom_id', $schedule->classroom_id)
                    ->where('period', $schedule->period)
                    ->whereNotNull('check_in_time')
                    ->exists();

                if (!$hasCheckedIn) {
                    NotificationHelper::send(
                        $schedule->user,
                        'warning',
                        'Pengingat Jadwal Mengajar',
                        'Anda memiliki jadwal mengajar di kelas ' . $schedule->classroom->name . ' (Jam ke-' . $schedule->period . ') pada pukul ' . $scheduleTime->format('H:i') . '. Segera lakukan presensi kelas.',
                        route('teacher.class-attendance'),
                        'alert-circle',
                        'bg-yellow-100 text-yellow-600'
                    );

                    $this->info("Reminder sent to {$schedule->user->name} for class {$schedule->classroom->name}");
                }
            }
        }

        return Command::SUCCESS;
    }
}