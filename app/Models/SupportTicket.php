<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id', 'ticket_id', 'type', 'title', 'description',
        'category', 'priority', 'status', 'metadata', 'attachments',
        'extra_fields', 'vexalyn_sent_at', 'vexalyn_response',
    ];

    protected $casts = [
        'metadata'         => 'array',
        'attachments'      => 'array',
        'extra_fields'     => 'array',
        'vexalyn_sent_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function typeLabels(): array
    {
        return [
            'bug'         => ['label' => 'Laporkan Bug',          'icon' => 'bug',          'color' => 'red'],
            'feature'     => ['label' => 'Request Fitur',         'icon' => 'lightbulb',    'color' => 'amber'],
            'maintenance' => ['label' => 'Permohonan Maintenance','icon' => 'wrench',        'color' => 'blue'],
            'question'    => ['label' => 'Pertanyaan / Bantuan',  'icon' => 'help-circle',   'color' => 'purple'],
        ];
    }

    public static function statusLabels(): array
    {
        return [
            'new'         => ['label' => 'Baru',         'color' => 'blue'],
            'review'      => ['label' => 'Review',        'color' => 'amber'],
            'in_progress' => ['label' => 'Diproses',      'color' => 'indigo'],
            'testing'     => ['label' => 'Testing',       'color' => 'purple'],
            'completed'   => ['label' => 'Selesai',       'color' => 'green'],
            'rejected'    => ['label' => 'Ditolak',       'color' => 'red'],
            'on_hold'     => ['label' => 'Ditangguhkan',  'color' => 'slate'],
        ];
    }

    public static function priorityLabels(): array
    {
        return [
            'low'      => ['label' => 'Rendah',   'color' => 'green'],
            'medium'   => ['label' => 'Sedang',   'color' => 'amber'],
            'high'     => ['label' => 'Tinggi',   'color' => 'orange'],
            'critical' => ['label' => 'Kritis',   'color' => 'red'],
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status]['label'] ?? ucfirst($this->status);
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::priorityLabels()[$this->priority]['label'] ?? ucfirst($this->priority);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabels()[$this->type]['label'] ?? ucfirst($this->type);
    }
}
