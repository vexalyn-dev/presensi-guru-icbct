<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class PasswordResetOtp extends Model
{
    protected $fillable = ['email', 'otp', 'expires_at', 'used'];

    protected $casts = [
        'expires_at' => 'datetime',
        'used'       => 'boolean',
    ];

    /**
     * Generate & simpan OTP baru untuk email tertentu.
     * Hapus OTP lama milik email yang sama sebelum membuat yang baru.
     */
    public static function generateFor(string $email): string
    {
        // Hapus semua OTP lama untuk email ini
        static::where('email', $email)->delete();

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        static::create([
            'email'      => $email,
            'otp'        => Hash::make($otp),
            'expires_at' => now()->addMinutes(10),
            'used'       => false,
        ]);

        return $otp;
    }

    /**
     * Verifikasi OTP — cek kecocokan, belum dipakai, dan belum expired.
     * Jika valid, tandai sebagai used dan kembalikan true.
     */
    public static function verify(string $email, string $otp): bool
    {
        $record = static::where('email', $email)
                        ->where('used', false)
                        ->where('expires_at', '>', now())
                        ->latest()
                        ->first();

        if (! $record) {
            return false;
        }

        if (! Hash::check($otp, $record->otp)) {
            return false;
        }

        $record->update(['used' => true]);

        return true;
    }

    /**
     * Cek apakah ada OTP aktif (belum expired, belum used) untuk email ini.
     */
    public static function hasActive(string $email): bool
    {
        return static::where('email', $email)
                     ->where('used', false)
                     ->where('expires_at', '>', now())
                     ->exists();
    }
}
