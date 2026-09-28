<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Open redirect prevention: intended() URL harus berupa path internal (tidak ada host external)
        $intended = session()->pull('url.intended');
        if ($intended) {
            $parsedHost = parse_url($intended, PHP_URL_HOST);
            $appHost    = parse_url(config('app.url'), PHP_URL_HOST);
            // Buang intended jika mengarah ke host lain atau bukan relative path
            if ($parsedHost !== null && $parsedHost !== $appHost) {
                $intended = null;
            }
        }

        // Redirect based on role
        $defaultRoute = $user->isDeveloper()
            ? route('developer.index', config('app.developer_secret_key'))
            : ($user->isTeacher()
                ? route('teacher.dashboard', absolute: false)
                : route('dashboard', absolute: false));

        return redirect($intended ?? $defaultRoute);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
