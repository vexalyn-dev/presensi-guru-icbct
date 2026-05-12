<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect to provider
     */
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Callback from provider
     */
    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            // Find or create user
            $user = User::firstOrCreate(
                ['email' => $socialUser->getEmail()],
                [
                    'name' => $socialUser->getName(),
                    'password' => bcrypt(Str::random(24)),
                    'role' => 'guru',
                    'is_active' => true,
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                ]
            );
            
            // Update provider info if user exists
            if ($user->wasRecentlyCreated === false) {
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                ]);
            }
            
            Auth::login($user);
            
            return redirect()->intended('/dashboard')->with('success', 'Login berhasil melalui ' . ucfirst($provider));
            
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Terjadi kesalahan saat login dengan ' . ucfirst($provider));
        }
    }
}