<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Classroom extends Model
{
    protected $fillable = [
        'name',
        'code',
        'building',
        'floor',
        'qr_token',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'floor' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($classroom) {
            if (empty($classroom->qr_token)) {
                $classroom->qr_token = Str::uuid()->toString();
            }
        });
    }

    public function teachingSchedules()
    {
        return $this->hasMany(TeachingSchedule::class);
    }

    public function classAttendances()
    {
        return $this->hasMany(ClassAttendance::class);
    }

    /**
     * Generate data untuk QR Code
     */
    public function getQrDataAttribute()
    {
        return json_encode([
            'type' => 'classroom',
            'classroom_id' => $this->id,
            'token' => $this->qr_token
        ]);
    }
}