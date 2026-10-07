<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PasswordResetOtpController extends Controller
{
    /**
     * Tampilkan halaman verifikasi OTP.
     * Email disimpan di session setelah kirim OTP dari forgot-password.
     */
    public function showVerifyForm(Request $request): View|RedirectResponse
    {
        $email = session('otp_email');

        if (! $email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Sesi tidak valid. Silakan minta kode OTP ulang.']);
        }

        return view('auth.verify-otp', ['email' => $email]);
    }

    /**
     * Proses verifikasi kode OTP yang dimasukkan user.
     * Jika valid → simpan token sementara di session → redirect ke form reset password.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
        ], [
            'otp.required' => 'Kode OTP harus diisi.',
            'otp.size'     => 'Kode OTP harus 6 digit.',
            'otp.regex'    => 'Kode OTP hanya boleh berisi angka.',
        ]);

        $email = session('otp_email');

        if (! $email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Sesi tidak valid. Silakan minta kode OTP ulang.']);
        }

        $valid = PasswordResetOtp::verify($email, $request->otp);

        if (! $valid) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau sudah kadaluarsa.']);
        }

        // OTP valid → tandai sesi bahwa user boleh reset password
        session([
            'otp_verified_email' => $email,
            'otp_email'          => null,
        ]);

        return redirect()->route('password.reset.form');
    }

    /**
     * Kirim ulang OTP ke email yang sama (resend).
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $email = session('otp_email');

        if (! $email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Sesi tidak valid. Silakan masukkan email Anda lagi.']);
        }

        // Throttle: cek apakah sudah ada OTP aktif (hindari spam)
        if (PasswordResetOtp::hasActive($email)) {
            return back()->withErrors([
                'otp' => 'Kode OTP masih aktif. Harap tunggu sebentar sebelum meminta kode baru.',
            ]);
        }

        // Pastikan email terdaftar (tanpa bocorkan info — cukup kirim saja jika ada)
        $user = User::where('email', $email)->first();
        if ($user) {
            $otp = PasswordResetOtp::generateFor($email);
            Mail::to($email)->send(new PasswordResetOtpMail($otp, $email));
        }

        return back()->with('resent', 'Kode OTP baru telah dikirim ke email Anda.');
    }

    /**
     * Tampilkan form buat password baru (setelah OTP terverifikasi).
     */
    public function showResetForm(): View|RedirectResponse
    {
        $email = session('otp_verified_email');

        if (! $email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Sesi tidak valid. Silakan mulai ulang proses reset password.']);
        }

        return view('auth.reset-password-otp', ['email' => $email]);
    }

    /**
     * Proses simpan password baru.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $email = session('otp_verified_email');

        if (! $email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Sesi tidak valid. Silakan mulai ulang proses reset password.']);
        }

        $request->validate([
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ], [
            'password.required'  => 'Password baru harus diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Akun tidak ditemukan.']);
        }

        $user->forceFill([
            'password'       => Hash::make($request->password),
            'remember_token' => \Illuminate\Support\Str::random(60),
        ])->save();

        // Hapus semua OTP lama untuk email ini
        PasswordResetOtp::where('email', $email)->delete();

        // Bersihkan session
        session()->forget(['otp_verified_email', 'otp_email']);

        return redirect()->route('login')
            ->with('status', 'Password berhasil diubah. Silakan login dengan password baru Anda.');
    }
}
