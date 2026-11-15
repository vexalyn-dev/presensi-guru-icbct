<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceSessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return $next($request);
        }

        $minutes = (int) Setting::get('auto_logout', 'off');
        $lastActivity = session('last_activity_time');

        if ($minutes > 0 && $lastActivity && (time() - $lastActivity) > ($minutes * 60)) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('timeout', "Sesi Anda berakhir karena tidak aktif selama {$minutes} menit. Silakan login kembali.");
        }

        // Perbarui timestamp aktivitas
        session(['last_activity_time' => time()]);

        return $next($request);
    }
}
