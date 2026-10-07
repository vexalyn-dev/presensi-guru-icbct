<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Tampilkan halaman lupa password (form input email).
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses request OTP reset password.
     * Generate OTP, kirim ke email, simpan email di session → redirect ke halaman verifikasi OTP.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->email;
        $user  = User::where('email', $email)->first();

        // Kirim OTP hanya jika email terdaftar, tapi selalu tampilkan response yang sama
        // agar tidak membocorkan info akun (anti-enumeration)
        if ($user) {
            $otp = PasswordResetOtp::generateFor($email);
            Mail::to($email)->send(new PasswordResetOtpMail($otp, $email));
        }

        // Simpan email di session untuk dipakai di halaman verify OTP
        session(['otp_email' => $email]);

        // Redirect ke halaman verify OTP dengan pesan sukses
        return redirect()->route('password.verify-otp')
            ->with('status', 'Kode OTP telah dikirim ke email Anda.');
    }
}
