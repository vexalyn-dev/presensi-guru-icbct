<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Cari user berdasarkan email
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if (!$user) {
                // Buat user baru
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'role' => 'guru', // Default role
                    'email_verified_at' => now(),
                    'password' => bcrypt(uniqid()), // Random password
                ]);
                
                // KIRIM WELCOME EMAIL
                try {
                    Mail::to($user->email)->send(new WelcomeEmail($user));
                } catch (\Exception $e) {
                    // Log error jika email gagal kirim
                    Log::error('Failed to send welcome email: ' . $e->getMessage());
                }
            } else {
                // Update google_id jika belum ada
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            }
            
            Auth::login($user, true);
            
            return redirect()->intended('/dashboard');
            
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Terjadi kesalahan saat login dengan Google');
        }
    }
}