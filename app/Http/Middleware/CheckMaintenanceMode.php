<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AppSetting;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // Bypass untuk route login & asset agar tidak infinite redirect
        if ($request->routeIs('login', 'logout', 'password.*')) {
            return $next($request);
        }

        // Admin & Operator selalu bisa akses — bypass maintenance
        $user = $request->user();
        if ($user && $user->canAccessAdmin()) {
            return $next($request);
        }

        // Developer — bypass maintenance sepenuhnya (termasuk saat belum login)
        // Auth dicek manual di DeveloperController::verifySecret()
        if ($request->routeIs('developer.*')) {
            return $next($request);
        }

        try {
            $setting = AppSetting::getInstance();

            if ($setting && $setting->maintenance_mode) {
                $message = $setting->maintenance_message
                    ?? 'Sistem sedang dalam pemeliharaan. Mohon tunggu sebentar.';

                return response()->view('errors.maintenance', [
                    'message' => $message,
                ], 503);
            }
        } catch (\Exception $e) {
            // DB error — jangan block akses
        }

        return $next($request);
    }
}
