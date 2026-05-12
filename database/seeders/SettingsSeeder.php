<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Create default app settings
        AppSetting::create([
            'app_name' => 'ICB CT - Absensi Guru',
            'app_timezone' => 'Asia/Jakarta',
            'app_language' => 'id',
            'attendance_start_time' => '07:30',
            'attendance_end_time' => '08:00',
            'attendance_late_grace_period' => 15,
            'location_required' => true,
            'photo_required' => true,
            'location_radius' => 100,
            'email_notification' => true,
            'late_notification' => true,
            'primary_color' => '#0F172A',
            'accent_color' => '#FACC15',
        ]);

        // Create settings entries
        $settings = [
            ['key' => 'app_name', 'value' => 'ICB CT - Absensi Guru', 'type' => 'string', 'group' => 'general'],
            ['key' => 'app_timezone', 'value' => 'Asia/Jakarta', 'type' => 'string', 'group' => 'general'],
            ['key' => 'app_language', 'value' => 'id', 'type' => 'string', 'group' => 'general'],
            ['key' => 'attendance_start_time', 'value' => '07:30', 'type' => 'string', 'group' => 'attendance'],
            ['key' => 'attendance_end_time', 'value' => '08:00', 'type' => 'string', 'group' => 'attendance'],
            ['key' => 'attendance_late_grace_period', 'value' => '15', 'type' => 'integer', 'group' => 'attendance'],
            ['key' => 'location_required', 'value' => '1', 'type' => 'boolean', 'group' => 'attendance'],
            ['key' => 'photo_required', 'value' => '1', 'type' => 'boolean', 'group' => 'attendance'],
            ['key' => 'email_notification', 'value' => '1', 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'late_notification', 'value' => '1', 'type' => 'boolean', 'group' => 'notification'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}