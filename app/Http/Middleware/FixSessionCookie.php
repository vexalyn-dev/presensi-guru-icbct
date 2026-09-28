<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FixSessionCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        // Deteksi HTTPS — secure=true hanya jika benar-benar via HTTPS
        // Ini aman: di belakang reverse proxy/cPanel SSL, X-Forwarded-Proto akan terdeteksi
        $isSecure = $request->secure() || $request->header('X-Forwarded-Proto') === 'https';

        config([
            'session.secure'      => $isSecure,
            'session.encrypt'     => false,
            'session.same_site'   => 'lax',
            'session.http_only'   => true,
            'session.cookie'      => 'icb_ct_session',
            'session.path'        => '/',
            'session.domain'      => null,
        ]);

        return $next($request);
    }
}
