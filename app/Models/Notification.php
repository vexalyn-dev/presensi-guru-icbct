<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function getIsReadAttribute()
    {
        return $this->read_at !== null;
    }

    public function getTitleAttribute()
    {
        return $this->data['title'] ?? 'Notifikasi';
    }

    public function getMessageAttribute()
    {
        return $this->data['message'] ?? '';
    }

    public function getActionUrlAttribute()
    {
        return $this->data['action_url'] ?? null;
    }

    public function getIconAttribute()
    {
        return $this->data['icon'] ?? 'bell';
    }

    public function getColorAttribute()
    {
        return $this->data['color'] ?? 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400';
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}