<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    private function redirectByRole(\App\Models\User $user)
    {
        if ($user->isGuruPiket()) {
            return redirect()->route('piket.dashboard')
                ->with('show_welcome', true)
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        if ($user->isTeacher()) {
            return redirect()->route('teacher.dashboard')
                ->with('show_welcome', true)
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        if ($user->canAccessAdmin()) {
            return redirect()->route('dashboard')
                ->with('show_welcome', true)
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        Auth::logout();

        return redirect()->route('login')
            ->with('error', 'Role akun tidak dikenali. Hubungi admin.');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Log aktivitas login
            try { ActivityLogService::login($user); } catch (\Exception $e) {}

            // AJAX request — return JSON dengan redirect URL
            if ($request->expectsJson() || $request->ajax()) {
                $redirectUrl = match(true) {
                    $user->isGuruPiket()    => route('piket.dashboard'),
                    $user->isTeacher()      => route('teacher.dashboard'),
                    $user->canAccessAdmin() => route('dashboard'),
                    default                 => route('login'),
                };
                $welcomeKey = match(true) {
                    $user->isGuruPiket()    => 'show_welcome_piket_' . $user->id,
                    $user->isTeacher()      => 'show_welcome_' . $user->id,
                    $user->canAccessAdmin() => 'show_welcome_admin_' . $user->id,
                    default                 => null,
                };
                return response()->json([
                    'success'      => true,
                    'redirect'     => $redirectUrl,
                    'show_welcome' => true,
                    'welcome_key'  => $welcomeKey,
                    'user_id'      => $user->id,
                    'user_name'    => $user->name,
                ]);
            }

            return $this->redirectByRole($user);
        }

        // AJAX request — return JSON error
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'errors'  => ['email' => ['Email atau password yang Anda masukkan salah.']],
            ], 422);
        }

        return redirect()->back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Anda berhasil logout.');
    }
}
