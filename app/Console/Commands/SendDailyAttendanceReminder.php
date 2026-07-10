<?php

namespace App\Console\Commands;

use App\Helpers\NotificationHelper;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDailyAttendanceReminder extends Command
{
    protected $signature = 'reminder:daily-attendance';
    protected $description = 'Send reminder for teachers who haven\'t checked in';

    public function handle()
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        
        // Get all teachers
        $teachers = User::where('role', 'guru')
            ->where('is_active', true)
            ->get();

        foreach ($teachers as $teacher) {
            // Check if teacher has checked in today
            $hasCheckedIn = Attendance::where('user_id', $teacher->id)
                ->whereDate('date', $today)
                ->whereNotNull('check_in')
                ->exists();

            // Check if teacher has any class today
            $hasClassToday = \App\Models\TeachingSchedule::where('user_id', $teacher->id)
                ->where('day_of_week', $now->dayOfWeek)
                ->where('is_active', true)
                ->exists();

            // Send reminder if hasn't checked in and has class today
            if (!$hasCheckedIn && $hasClassToday) {
                // Send reminder after 8 AM
                if ($now->format('H:i') >= '08:00') {
                    NotificationHelper::send(
                        $teacher,
                        'warning',
                        'Pengingat Absensi Harian',
                        'Anda belum melakukan absensi harian hari ini. Segera lakukan presensi datang dan presensi kelas.',
                        route('teacher.attendance'),
                        'alert-triangle',
                        'bg-orange-100 text-orange-600'
                    );

                    $this->info("Reminder sent to {$teacher->name} for daily attendance");
                }
            }
        }

        return Command::SUCCESS;
    }
}