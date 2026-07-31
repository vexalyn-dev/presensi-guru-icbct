<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'type', 'category', 'description', 'user_id', 'subject_type', 
        'subject_id', 'properties', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    // Helper untuk mendapatkan icon berdasarkan kategori
    public function getIconAttribute(): string
    {
        $icons = [
            'attendance' => 'scan-line',
            'auth'       => 'log-in',
            'system'     => 'settings',
            'settings'   => 'settings',
            'teacher'    => 'users',
            'classroom'  => 'school',
        ];
        return $icons[$this->category] ?? 'activity';
    }

    // Helper untuk mendapatkan warna berdasarkan kategori
    public function getColorAttribute(): string
    {
        $colors = [
            'attendance' => 'green',
            'auth'       => 'blue',
            'system'     => 'slate',
            'settings'   => 'purple',
            'teacher'    => 'amber',
            'classroom'  => 'indigo',
        ];
        return $colors[$this->category] ?? 'slate';
    }

    // Helper untuk mendapatkan info device dari properties
    public function getDeviceAttribute(): array
    {
        return $this->properties['device_info'] ?? [];
    }
}