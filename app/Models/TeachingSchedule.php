<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TeachingSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'classroom_id',
        'subject_id',
        'day_of_week',
        'period',
        'start_time',
        'end_time',
        'academic_year',
        'semester',
        'is_active'
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'period' => 'integer',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classAttendances()
    {
        return $this->hasMany(ClassAttendance::class);
    }

    /**
     * Get day name from day of week index
     * 
     * @param int $dayOfWeek
     * @return string
     */
    public static function getDayName(int $dayOfWeek): string
    {
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return $days[$dayOfWeek] ?? 'Unknown';
    }

    /**
     * Ambil jadwal mengajar hari ini untuk guru
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTodaySchedules(int $userId)
    {
        $today = Carbon::now()->dayOfWeek;

        return self::with(['classroom', 'subject'])
            ->where('user_id', $userId)
            ->where('day_of_week', $today)
            ->where('is_active', true)
            ->orderBy('period')
            ->get();
    }

    /**
     * Cari jadwal yang cocok untuk scan saat ini
     * 
     * @param int $userId
     * @param int $classroomId
     * @param int|null $period
     * @return self|null
     */
    public static function findMatchingSchedule(int $userId, int $classroomId, ?int $period = null)
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');
        $today = $now->dayOfWeek;

        $query = self::where('user_id', $userId)
            ->where('classroom_id', $classroomId)
            ->where('day_of_week', $today)
            ->where('is_active', true);

        if ($period) {
            $query->where('period', $period);
        }

        // Cari jadwal yang waktunya mencakup saat ini atau toleransi 15 menit sebelum jadwal mulai
        return $query->where(function ($q) use ($currentTime) {
            $q->where(function ($inner) use ($currentTime) {
                $inner->where('start_time', '<=', $currentTime)
                    ->where('end_time', '>=', $currentTime);
            })->orWhere(function ($inner) use ($currentTime) {
                $inner->whereRaw('TIME_SUB(start_time, INTERVAL 15 MINUTE) <= ?', [$currentTime])
                    ->where('start_time', '>=', $currentTime);
            });
        })->first();
    }
}