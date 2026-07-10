<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\TeacherSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateAlphaAbsences extends Command
{
    protected $signature = 'attendance:generate-alpha';
    protected $description = 'Auto-generate alpha status for teachers who did not attend';

    public function handle()
    {
        $yesterday = Carbon::yesterday();
        $yesterdayDayOfWeek = $yesterday->dayOfWeek;
        
        $teachers = User::where('role', 'guru')
            ->where('is_active', true)
            ->get();

        foreach ($teachers as $teacher) {
            // Cek apakah guru punya jadwal kemarin
            $hasSchedule = TeacherSchedule::where('user_id', $teacher->id)
                ->where('day_of_week', $yesterdayDayOfWeek)
                ->where('is_active', true)
                ->exists();

            if ($hasSchedule) {
                // Cek apakah sudah ada absensi
                $hasAttendance = Attendance::where('user_id', $teacher->id)
                    ->whereDate('date', $yesterday)
                    ->exists();

                // Cek apakah ada izin/sakit yang disetujui
                $hasLeave = LeaveRequest::where('user_id', $teacher->id)
                    ->where('start_date', '<=', $yesterday)
                    ->where('end_date', '>=', $yesterday)
                    ->where('status', 'approved')
                    ->exists();

                // Kalau tidak absen dan tidak izin = Alpha
                if (!$hasAttendance && !$hasLeave) {
                    Attendance::create([
                        'user_id' => $teacher->id,
                        'date' => $yesterday,
                        'check_in' => null,
                        'check_out' => null,
                        'status' => 'Alpha',
                        'scan_method' => 'auto_generated',
                    ]);

                    $this->info("Alpha: {$teacher->name} - {$yesterday->format('Y-m-d')}");
                }
            }
        }

        $this->info('Selesai!');
        return Command::SUCCESS;
    }
}