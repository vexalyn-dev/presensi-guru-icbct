<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'nip',
        'name',
        'email',
        'phone',
        'photo',
        'address',
        'education',
        'major_specialty',
        'certification_status',
        'join_date',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'join_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function teachingSchedules(): HasMany
    {
        return $this->hasMany(TeachingSchedule::class, 'user_id');
    }

    public function classAttendances(): HasMany
    {
        return $this->hasMany(ClassAttendance::class, 'user_id');
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0F172A&color=fff';
    }
}