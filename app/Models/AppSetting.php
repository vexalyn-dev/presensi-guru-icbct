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
        'class_switch_grace_period',
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
        'maintenance_mode',
        'maintenance_message',
        // APK
        'apk_file',
        'apk_name',
        'apk_version',
        'apk_min_android',
        'apk_size',
        'apk_uploaded_at',
        'apk_changelog',
    ];

    protected $casts = [
        'location_required'            => 'boolean',
        'photo_required'               => 'boolean',
        'email_notification'           => 'boolean',
        'late_notification'            => 'boolean',
        'maintenance_mode'             => 'boolean',
        'attendance_late_grace_period' => 'integer',
        'class_switch_grace_period'    => 'integer',
        'location_radius'              => 'integer',
        'apk_size'                     => 'integer',
        'apk_uploaded_at'              => 'datetime',
    ];

    public function getLogoUrlAttribute()
    {
        return $this->app_logo ? asset('storage/' . $this->app_logo) : asset('images/default-logo.png');
    }

    public function getFaviconUrlAttribute()
    {
        return $this->app_favicon ? asset('storage/' . $this->app_favicon) : asset('images/default-favicon.png');
    }

    /** URL download APK */
    public function getApkUrlAttribute(): ?string
    {
        return $this->apk_file ? asset('storage/' . $this->apk_file) : null;
    }

    /** Ukuran APK dalam format manusia (MB) */
    public function getApkSizeHumanAttribute(): string
    {
        if (!$this->apk_size) return '-';
        $mb = $this->apk_size / 1048576;
        return $mb < 1 ? round($mb * 1024) . ' KB' : '~' . round($mb, 1) . ' MB';
    }

    /** Versi dengan prefix v */
    public function getApkVersionLabelAttribute(): string
    {
        return $this->apk_version ? 'v' . ltrim($this->apk_version, 'v') : 'v1.0.0';
    }

    public static function getInstance()
    {
        return self::firstOrCreate([]);
    }
}