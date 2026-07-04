<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ClassAttendance extends Model
{
    protected $fillable = [
        'user_id',
        'classroom_id',
        'teaching_schedule_id',
        'date',
        'period',
        'check_in_time',
        'check_out_time',
        'status',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'period' => 'integer'
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function teachingSchedule()
    {
        return $this->belongsTo(TeachingSchedule::class);
    }

    /**
     * Cek apakah guru sudah scan masuk untuk jam pelajaran ini
     */
    public static function hasCheckedIn($userId, $classroomId, $date, $period)
    {
        return self::where('user_id', $userId)
            ->where('classroom_id', $classroomId)
            ->where('date', $date)
            ->where('period', $period)
            ->whereNotNull('check_in_time')
            ->exists();
    }
}