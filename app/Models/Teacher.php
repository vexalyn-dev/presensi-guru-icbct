<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'employee_code',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($teacher) {
            if (empty($teacher->employee_code)) {
                $teacher->employee_code = static::generateEmployeeCode();
            }
        });
    }

    public static function generateEmployeeCode()
    {
        $prefix = 'SMKICBCT-';
        
        // Generate 5 digit random number
        do {
            $randomNumber = str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
            $code = $prefix . $randomNumber;
        } while (static::where('employee_code', $code)->exists());

        return $code;
    }

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

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher', 'teacher_id', 'subject_id')
                    ->withTimestamps();
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0F172A&color=fff';
    }
}