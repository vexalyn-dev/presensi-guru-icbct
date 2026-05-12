<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_name',
        'app_logo',
        'app_favicon',
        'app_timezone',
        'app_language',
        'attendance_start_time',
        'attendance_end_time',
        'attendance_late_grace_period',
        'location_required',
        'photo_required',
        'location_latitude',
        'location_longitude',
        'location_radius',
        'email_notification',
        'late_notification',
        'admin_email',
        'primary_color',
        'accent_color',
    ];

    protected $casts = [
        'location_required' => 'boolean',
        'photo_required' => 'boolean',
        'email_notification' => 'boolean',
        'late_notification' => 'boolean',
        'attendance_late_grace_period' => 'integer',
        'location_radius' => 'integer',
    ];

    public function getLogoUrlAttribute()
    {
        return $this->app_logo ? asset('storage/' . $this->app_logo) : asset('images/default-logo.png');
    }

    public function getFaviconUrlAttribute()
    {
        return $this->app_favicon ? asset('storage/' . $this->app_favicon) : asset('images/default-favicon.png');
    }

    public static function getInstance()
    {
        return self::firstOrCreate([]);
    }
}