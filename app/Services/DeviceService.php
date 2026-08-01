<?php

namespace App\Services;

use App\Models\User;
use App\Models\TeacherDevice;

class DeviceService
{
    const MAX_DEVICES = 2;

    public function registerDevice(User $user, string $deviceName, string $deviceToken, ?string $userAgent = null): TeacherDevice
    {
        $activeCount = $user->devices()->where('is_active', true)->count();

        if ($activeCount >= self::MAX_DEVICES) {
            throw new \Exception('Maksimal ' . self::MAX_DEVICES . ' perangkat. Hapus perangkat lama di menu Kelola Perangkat.');
        }

        // Parse UA manual — tidak butuh package Jenssegers
        $os      = $this->parseOS($userAgent ?? '');
        $browser = $this->parseBrowser($userAgent ?? '');

        return $user->devices()->updateOrCreate(
            ['device_token' => $deviceToken],
            [
                'device_name'  => $deviceName,
                'user_agent'   => $userAgent,
                'os'           => $os,
                'browser'      => $browser,
                'is_active'    => true,
                'last_used_at' => now(),
            ]
        );
    }

    public function validateDevice(User $user, ?string $deviceToken): bool
    {
        if (!$deviceToken) return false;

        $device = $user->devices()
            ->where('device_token', $deviceToken)
            ->where('is_active', true)
            ->first();

        if ($device) {
            $device->update(['last_used_at' => now()]);
            return true;
        }

        return false;
    }

    private function parseOS(string $ua): string
    {
        if (str_contains($ua, 'Windows NT 10')) return 'Windows 10/11';
        if (str_contains($ua, 'Windows'))       return 'Windows';
        if (str_contains($ua, 'Mac OS X'))      return 'macOS';
        if (str_contains($ua, 'Android'))       return 'Android';
        if (str_contains($ua, 'iPhone'))        return 'iOS (iPhone)';
        if (str_contains($ua, 'iPad'))          return 'iOS (iPad)';
        if (str_contains($ua, 'Linux'))         return 'Linux';
        return 'Unknown OS';
    }

    private function parseBrowser(string $ua): string
    {
        if (str_contains($ua, 'Edg/'))                                  return 'Microsoft Edge';
        if (str_contains($ua, 'Chrome/') && !str_contains($ua, 'Edg')) return 'Google Chrome';
        if (str_contains($ua, 'Firefox/'))                              return 'Mozilla Firefox';
        if (str_contains($ua, 'Safari/') && !str_contains($ua, 'Chrome')) return 'Safari';
        return 'Unknown Browser';
    }
}
