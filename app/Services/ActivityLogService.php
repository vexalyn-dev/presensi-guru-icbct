<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Log aktivitas
     */
    public static function log(
        string $type,
        string $category,
        string $description,
        $subject = null,
        array $extraProperties = [],
        $user = null
    ): ActivityLog {
        $user = $user ?? auth()->user();
        
        $properties = array_merge([
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
        ], $extraProperties);

        // Tambah info lokasi jika ada
        if (isset($extraProperties['latitude']) && isset($extraProperties['longitude'])) {
            $properties['location'] = [
                'latitude' => $extraProperties['latitude'],
                'longitude' => $extraProperties['longitude'],
                'map_url' => "https://www.google.com/maps?q={$extraProperties['latitude']},{$extraProperties['longitude']}",
            ];
        }

        // Tambah info device
        $properties['device_info'] = self::parseUserAgent(Request::userAgent());

        $log = new ActivityLog([
            'user_id' => $user?->id,
            'type' => $type,
            'category' => $category,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'properties' => $properties,
        ]);

        // Set subject jika ada (polymorphic)
        if ($subject && $subject instanceof \Illuminate\Database\Eloquent\Model) {
            $log->subject_type = get_class($subject);
            $log->subject_id = $subject->id;
        }

        $log->save();

        return $log;
    }

    /**
     * Parse User Agent untuk info device
     */
    protected static function parseUserAgent(?string $ua): array
    {
        if (!$ua) return ['os' => 'Unknown', 'browser' => 'Unknown', 'device' => 'Unknown'];

        $os = 'Unknown';
        if (str_contains($ua, 'Windows')) $os = 'Windows';
        elseif (str_contains($ua, 'Macintosh')) $os = 'macOS';
        elseif (str_contains($ua, 'Linux')) $os = 'Linux';
        elseif (str_contains($ua, 'Android')) $os = 'Android';
        elseif (str_contains($ua, 'iPhone')) $os = 'iOS (iPhone)';
        elseif (str_contains($ua, 'iPad')) $os = 'iOS (iPad)';

        $browser = 'Unknown';
        if (str_contains($ua, 'Edg/')) $browser = 'Microsoft Edge';
        elseif (str_contains($ua, 'Chrome/')) $browser = 'Google Chrome';
        elseif (str_contains($ua, 'Firefox/')) $browser = 'Mozilla Firefox';
        elseif (str_contains($ua, 'Safari/') && !str_contains($ua, 'Chrome')) $browser = 'Safari';

        $device = 'Desktop';
        if (str_contains($ua, 'Mobile') || str_contains($ua, 'Android') || str_contains($ua, 'iPhone')) {
            $device = 'Mobile';
        } elseif (str_contains($ua, 'Tablet') || str_contains($ua, 'iPad')) {
            $device = 'Tablet';
        }

        return [
            'os' => $os,
            'browser' => $browser,
            'device' => $device,
        ];
    }

    /**
     * Shortcut methods
     */
    public static function scanIn($user, $classroom, $schedule, $location = [])
    {
        return self::log(
            'scan_in',
            'attendance',
            "{$user->name} scan MASUK di {$classroom->name} (Jam ke-{$schedule->period})",
            $classroom,
            array_merge([
                'classroom_name' => $classroom->name,
                'classroom_code' => $classroom->code ?? '-',
                'period' => $schedule->period,
                'subject' => $schedule->subject->name ?? '-',
                'schedule_id' => $schedule->id,
            ], $location),
            $user
        );
    }

    public static function scanOut($user, $classroom, $schedule, $location = [])
    {
        return self::log(
            'scan_out',
            'attendance',
            "{$user->name} scan KELUAR di {$classroom->name} (Jam ke-{$schedule->period})",
            $classroom,
            array_merge([
                'classroom_name' => $classroom->name,
                'classroom_code' => $classroom->code ?? '-',
                'period' => $schedule->period,
                'subject' => $schedule->subject->name ?? '-',
                'schedule_id' => $schedule->id,
            ], $location),
            $user
        );
    }

    public static function login($user)
    {
        return self::log(
            'login',
            'auth',
            "{$user->name} login ke sistem",
            null,
            ['email' => $user->email],
            $user
        );
    }

    public static function logout($user)
    {
        return self::log(
            'logout',
            'auth',
            "{$user->name} logout dari sistem",
            null,
            [],
            $user
        );
    }

    public static function settingsChange($user, $setting, $oldValue, $newValue)
    {
        return self::log(
            'settings_change',
            'settings',
            "{$user->name} mengubah pengaturan: {$setting}",
            null,
            [
                'setting_key' => $setting,
                'old_value' => $oldValue,
                'new_value' => $newValue,
            ],
            $user
        );
    }

    public static function teacherCreated($admin, $teacher)
    {
        return self::log(
            'teacher_created',
            'teacher',
            "{$admin->name} menambahkan guru: {$teacher->name}",
            $teacher,
            ['teacher_code' => $teacher->teacher_code],
            $admin
        );
    }

    public static function teacherUpdated($admin, $teacher, $changes)
    {
        return self::log(
            'teacher_updated',
            'teacher',
            "{$admin->name} memperbarui data guru: {$teacher->name}",
            $teacher,
            ['changes' => $changes],
            $admin
        );
    }

    public static function teacherDeleted($admin, $teacher)
    {
        return self::log(
            'teacher_deleted',
            'teacher',
            "{$admin->name} menghapus guru: {$teacher->name}",
            null,
            ['teacher_name' => $teacher->name, 'teacher_code' => $teacher->teacher_code],
            $admin
        );
    }
}