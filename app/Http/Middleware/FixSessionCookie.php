<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FixSessionCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan semua folder storage/cache ada
        $dirs = [
            storage_path('framework/views'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('logs'),
        ];
        foreach ($dirs as $dir) {
            @mkdir($dir, 0777, true);
            @chmod($dir, 0777);
        }

        // Deteksi HTTPS
        $isSecure = $request->secure()
            || $request->header('X-Forwarded-Proto') === 'https'
            || $request->header('X-Forwarded-Ssl') === 'on';

        if ($isSecure) {
            config(['session.secure' => true]);
        }

        return $next($request);
    }
}
