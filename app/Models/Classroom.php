<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Classroom extends Model
{
    protected $fillable = [
        'name',
        'code',        // X-RPL, XI-RPL, XII-RPL
        'class_level', // X, XI, XII
        'qr_token',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    /**
     * Get class level badge color
     */
    public function getClassLevelColorAttribute()
    {
        $colors = [
            'X' => 'from-blue-500 to-cyan-500',
            'XI' => 'from-violet-500 to-purple-500',
            'XII' => 'from-emerald-500 to-teal-500'
        ];
        return $colors[$this->class_level] ?? 'from-slate-500 to-gray-500';
    }

    /**
     * Accessor: Extract major code from code field (e.g. X-RPL => RPL)
     */
    public function getMajorCodeAttribute()
    {
        if ($this->attributes['code'] ?? null) {
            return preg_replace('/^(XII|XI|X)-/', '', $this->attributes['code']);
        }
        return null;
    }

    /**
     * Mutator: Auto-uppercase code on set
     */
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }
}