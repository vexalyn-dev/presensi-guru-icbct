<?php

namespace App\Helpers;

use App\Models\Setting;

class GpsHelper
{
    /**
     * Hitung jarak antara 2 koordinat menggunakan Haversine formula
     * Return: jarak dalam meter
     */
    public static function calculateDistance(
        float $lat1, float $lon1,
        float $lat2, float $lon2
    ): float {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Validasi apakah user berada dalam radius sekolah
     * Return: ['valid' => bool, 'distance' => float, 'radius' => int]
     */
    public static function validateLocation(?float $userLat, ?float $userLng): array
    {
        $gpsStatus = Setting::get('gps_validation_status', 'on');
        if ($gpsStatus === 'off') {
            return [
                'valid' => true,
                'distance' => null,
                'radius' => 0,
                'message' => 'Validasi GPS dinonaktifkan oleh admin.',
            ];
        }

        // Ambil koordinat sekolah dari database
        $schoolLat = (float) Setting::get('school_latitude', -6.2087634);
        $schoolLng = (float) Setting::get('school_longitude', 106.8455994);
        $radius = (int) Setting::get('location_radius', 50); // dalam meter

        // Jika GPS tidak tersedia
        if ($userLat === null || $userLng === null) {
            return [
                'valid' => false,
                'distance' => null,
                'radius' => $radius,
                'message' => 'Lokasi GPS tidak tersedia. Aktifkan GPS di device Anda.',
            ];
        }

        // Hitung jarak
        $distance = self::calculateDistance($userLat, $userLng, $schoolLat, $schoolLng);

        return [
            'valid' => $distance <= $radius,
            'distance' => round($distance, 2),
            'radius' => $radius,
            'message' => $distance <= $radius
                ? 'Lokasi valid'
                : "Anda berada {$distance}m dari sekolah (maksimal {$radius}m)",
        ];
    }
}