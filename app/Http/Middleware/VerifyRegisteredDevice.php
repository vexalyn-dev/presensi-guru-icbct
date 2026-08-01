<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\DeviceService;
use Symfony\Component\HttpFoundation\Response;

class VerifyRegisteredDevice
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Hanya terapkan ke role guru biasa
        if (!$user || $user->role !== 'guru') {
            return $next($request);
        }

        // Bypass halaman device itu sendiri agar tidak redirect loop
        if ($request->routeIs('devices.*')) {
            return $next($request);
        }

        $deviceToken  = $request->cookie('device_token');
        $deviceService = app(DeviceService::class);

        if (!$deviceToken || !$deviceService->validateDevice($user, $deviceToken)) {
            return redirect()->route('devices.register')
                ->with('warning', 'Perangkat ini belum terdaftar. Silakan daftarkan perangkat Anda terlebih dahulu.');
        }

        return $next($request);
    }
}
