<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FixSessionCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        $sessionDir = storage_path('framework/sessions');
        @mkdir($sessionDir, 0777, true);
        @chmod($sessionDir, 0777);

        // Deteksi HTTPS lewat header proxy cPanel/Laravel
        $isSecure = $request->secure()
            || $request->header('X-Forwarded-Proto') === 'https'
            || $request->header('X-Forwarded-Ssl') === 'on';

        // Set secure cookie agar session tetap jalan di HTTPS
        if ($isSecure) {
            config(['session.secure' => true]);
        }

        return $next($request);
    }
}
