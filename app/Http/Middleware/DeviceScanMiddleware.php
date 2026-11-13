<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk alat scan QR fisik (barcode scanner, ESP, dsb.)
 * Validasi menggunakan API key statis yang disimpan di .env (DEVICE_SCAN_KEY).
 * Tidak membutuhkan session / login apapun.
 */
class DeviceScanMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredKey = config('app.device_scan_key');

        // Jika key belum dikonfigurasi → tolak semua request
        if (empty($configuredKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint belum dikonfigurasi. Hubungi administrator.',
            ], 503);
        }

        $providedKey = $request->header('X-Device-Key')
            ?? $request->input('device_key');

        if (!$providedKey || !hash_equals($configuredKey, $providedKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Device key tidak valid.',
            ], 401);
        }

        return $next($request);
    }
}
