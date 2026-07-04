<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TeacherSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Cek apakah guru dijadwalkan hari ini
     */
    public static function isScheduledToday($userId)
    {
        $today = Carbon::now()->dayOfWeek; // 0=Minggu, 6=Sabtu

        return self::where('user_id', $userId)
            ->where('day_of_week', $today)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Ambil jadwal hari ini untuk guru
     */
    public static function getTodaySchedule($userId)
    {
        $today = Carbon::now()->dayOfWeek;

        return self::where('user_id', $userId)
            ->where('day_of_week', $today)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Ambil semua jadwal guru
     */
    public static function getTeacherSchedules($userId)
    {
        return self::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->get();
    }

    /**
     * Nama hari dalam bahasa Indonesia
     */
    public static function getDayName($dayOfWeek)
    {
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return $days[$dayOfWeek] ?? 'Unknown';
    }
}