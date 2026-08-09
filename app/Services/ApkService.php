<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

/**
 * Mengekstrak metadata dari file APK (.apk = ZIP dengan AndroidManifest.xml)
 * Menggunakan parsing binary AndroidManifest.xml yang terkompresi.
 */
class ApkService
{
    /**
     * Extract semua metadata dari file APK yang diupload.
     * Kembalikan array: name, version, min_android, size
     */
    public static function extractMetadata(UploadedFile $file): array
    {
        $meta = [
            'apk_name'        => null,
            'apk_version'     => null,
            'apk_min_android' => null,
            'apk_size'        => $file->getSize(),
        ];

        try {
            $manifest = self::readManifestFromApk($file->getRealPath());
            if ($manifest) {
                $meta['apk_version']     = self::parseVersionName($manifest);
                $meta['apk_min_android'] = self::parseMinSdk($manifest);
                $meta['apk_name']        = self::parseAppLabel($manifest);
            }
        } catch (\Throwable $e) {
            // Jika gagal parse, tetap lanjut dengan nilai null
        }

        return $meta;
    }

    /**
     * Baca AndroidManifest.xml dari dalam APK (format ZIP)
     */
    private static function readManifestFromApk(string $path): ?string
    {
        if (!class_exists('\ZipArchive')) return null;

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) return null;

        $manifest = $zip->getFromName('AndroidManifest.xml');
        $zip->close();

        return $manifest ?: null;
    }

    /**
     * Parse versionName dari binary AndroidManifest.xml
     * Format binary AXML: cari string pool lalu attribute "versionName"
     */
    private static function parseVersionName(string $binary): ?string
    {
        // Cari pola versionName dalam string pool binary AXML
        if (preg_match('/versionName[\x00-\x1f]+([0-9]+\.[0-9]+(?:\.[0-9]+)?(?:\.[0-9]+)?)/s', $binary, $m)) {
            return $m[1];
        }
        // Fallback: cari angka versi saja
        if (preg_match('/(\d+\.\d+(?:\.\d+)?(?:\.\d+)?)[\x00]{1,4}versionCode/s', $binary, $m)) {
            return $m[1];
        }
        return null;
    }

    /**
     * Parse minSdkVersion dan convert ke nama Android
     */
    private static function parseMinSdk(string $binary): ?string
    {
        // Cari minSdkVersion byte value dalam binary
        // minSdkVersion biasanya muncul sebagai integer kecil (1-34)
        if (preg_match('/minSdkVersion[\x00-\x1f\x01-\x08]{1,20}([\x08-\x22])\x00\x00\x00/s', $binary, $m)) {
            $sdk = ord($m[1]);
            return self::sdkToAndroid($sdk);
        }
        return null;
    }

    /**
     * Parse label aplikasi (nama app)
     */
    private static function parseAppLabel(string $binary): ?string
    {
        // Cari label string dalam manifest
        if (preg_match('/label[\x00-\x05]+([A-Za-z][A-Za-z0-9 \-_]{2,40})/s', $binary, $m)) {
            $label = trim($m[1]);
            if (strlen($label) >= 3) return $label;
        }
        return null;
    }

    /**
     * Convert SDK version integer ke nama Android
     */
    public static function sdkToAndroid(int $sdk): string
    {
        $map = [
            21 => 'Android 5.0+',
            22 => 'Android 5.1+',
            23 => 'Android 6.0+',
            24 => 'Android 7.0+',
            25 => 'Android 7.1+',
            26 => 'Android 8.0+',
            27 => 'Android 8.1+',
            28 => 'Android 9.0+',
            29 => 'Android 10+',
            30 => 'Android 11+',
            31 => 'Android 12+',
            32 => 'Android 12L+',
            33 => 'Android 13+',
            34 => 'Android 14+',
            35 => 'Android 15+',
        ];
        return $map[$sdk] ?? "Android API $sdk+";
    }
}
