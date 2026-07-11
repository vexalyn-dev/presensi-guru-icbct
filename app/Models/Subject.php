<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'category',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the teachers assigned to this subject (many-to-many).
     */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'subject_teacher', 'subject_id', 'teacher_id')
                    ->withTimestamps();
    }
}