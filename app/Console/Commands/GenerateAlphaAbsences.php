<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\TeacherSchedule;
use App\Models\User;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateAlphaAbsences extends Command
{
    protected $signature = 'attendance:generate-alpha';
    protected $description = 'Auto-generate alpha status for teachers who did not attend before checkout time';

    public function handle()
    {
        $today = Carbon::today();
        $todayDayOfWeek = Carbon::now()->dayOfWeek;
        $now = Carbon::now();

        $teachers = User::where('is_active', true)
            ->whereIn('role', ['guru', 'guru_piket'])
            ->get();

        foreach ($teachers as $teacher) {
            $hasSchedule = TeacherSchedule::where('user_id', $teacher->id)
                ->where('day_of_week', $todayDayOfWeek)
                ->where('is_active', true)
                ->exists();

            if (!$hasSchedule) {
                continue;
            }

            $hasAttendance = Attendance::where('user_id', $teacher->id)
                ->whereDate('date', $today)
                ->exists();

            $hasLeave = LeaveRequest::where('user_id', $teacher->id)
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->where('status', 'approved')
                ->exists();

            if ($hasAttendance || $hasLeave) {
                continue;
            }

            $checkoutTime = $this->getCheckoutTime($teacher, $todayDayOfWeek);

            if ($now->greaterThanOrEqualTo($checkoutTime)) {
                Attendance::create([
                    'user_id' => $teacher->id,
                    'date' => $today,
                    'check_in' => null,
                    'check_out' => null,
                    'status' => 'Alpha',
                    'scan_method' => 'auto_generated',
                ]);

                $this->info("Alpha: {$teacher->name} - {$today->format('Y-m-d')} (jam pulang: {$checkoutTime->format('H:i')})");
            }
        }

        $this->info('Selesai!');
        return Command::SUCCESS;
    }

    private function getCheckoutTime(User $teacher, int $dayOfWeek): Carbon
    {
        $schedule = TeacherSchedule::where('user_id', $teacher->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if ($schedule && $schedule->end_time) {
            return Carbon::parse($today->format('Y-m-d') . ' ' . $schedule->end_time);
        }

        if ($teacher->default_check_out) {
            return Carbon::parse(Carbon::today()->format('Y-m-d') . ' ' . $teacher->default_check_out);
        }

        $endTime = Setting::get('attendance_end_time', '16:00');
        return Carbon::parse(Carbon::today()->format('Y-m-d') . ' ' . $endTime);
    }
}
