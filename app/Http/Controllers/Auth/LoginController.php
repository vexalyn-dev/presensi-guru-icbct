<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    private function redirectByRole($user)
    {
        if ($user->isTeacher()) {
            return redirect()->route('teacher.dashboard')
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        if ($user->isAdmin()) {
            return redirect()->route('dashboard')
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
            
            return $this->redirectByRole($user);
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
        
        return redirect('/login')->with('success', 'Anda berhasil logout.');
    }
}
