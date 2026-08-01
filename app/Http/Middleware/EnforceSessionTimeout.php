<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceSessionTimeout
{
    /** Timeout dalam detik (30 menit) */
    const TIMEOUT = 30 * 60;

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return $next($request);
        }

        $lastActivity = session('last_activity_time');

        if ($lastActivity && (time() - $lastActivity) > self::TIMEOUT) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('timeout', 'Sesi Anda berakhir karena tidak aktif selama 30 menit. Silakan login kembali.');
        }

        // Perbarui timestamp aktivitas
        session(['last_activity_time' => time()]);

        return $next($request);
    }
}
