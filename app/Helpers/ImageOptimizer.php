<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageOptimizer
{
    private const MAX_SIZE  = 800;
    private const QUALITY   = 80;

    /**
     * Simpan foto yang sudah di-compress ke storage.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $path  Path relatif (contoh: 'profiles/photo.jpg')
     * @return string  Path relatif yang tersimpan
     */
    public static function store(UploadedFile $file, string $path): string
    {
        $image = imagecreatefromstring($file->getContent());
        if ($image === false) {
            // Fallback ke method biasa
            $tmp = tempnam(sys_get_temp_dir(), 'img_');
            file_put_contents($tmp, $file->getContent());
            $image = imagecreatefromstring(file_get_contents($tmp));
            unlink($tmp);
        }

        $origWidth  = imagesx($image);
        $origHeight = imagesy($image);

        if ($origWidth > self::MAX_SIZE || $origHeight > self::MAX_SIZE) {
            if ($origWidth >= $origHeight) {
                $newWidth  = self::MAX_SIZE;
                $newHeight = (int) round($origHeight * self::MAX_SIZE / $origWidth);
            } else {
                $newHeight = self::MAX_SIZE;
                $newWidth  = (int) round($origWidth * self::MAX_SIZE / $origHeight);
            }

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
            imagedestroy($image);
            $image = $resized;
        }

        // Cover / crop ke square
        $target = imagecreatetruecolor(self::MAX_SIZE, self::MAX_SIZE);
        $bgColor = imagecolorallocate($target, 255, 255, 255);
        imagefilledrectangle($target, 0, 0, self::MAX_SIZE, self::MAX_SIZE, $bgColor);

        $srcX = (int) round(($image->width ?? $newWidth ?? $origWidth) / 2 - self::MAX_SIZE / 2);
        $srcY = (int) round(($image->height ?? $newHeight ?? $origHeight) / 2 - self::MAX_SIZE / 2);
        $srcX = max(0, $srcX);
        $srcY = max(0, $srcY);

        imagecopyresampled(
            $target, $image,
            0, 0,
            $srcX, $srcY,
            self::MAX_SIZE, self::MAX_SIZE,
            self::MAX_SIZE, self::MAX_SIZE
        );

        imagedestroy($image);

        ob_start();
        imagejpeg($target, null, self::QUALITY);
        $data = ob_get_clean();
        imagedestroy($target);

        Storage::disk('public')->put($path, $data, 'public');

        return $path;
    }

    /**
     * Replace foto lama dengan yang baru (compressed).
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string|null  $oldPath   Path relatif foto lama
     * @param  string  $newPath   Path relatif foto baru
     * @return string  Path relatif baru
     */
    public static function replace(UploadedFile $file, ?string $oldPath, string $newPath): string
    {
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        return static::store($file, $newPath);
    }
}
