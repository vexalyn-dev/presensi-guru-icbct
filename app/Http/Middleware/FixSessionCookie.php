<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FixSessionCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Pastikan folder sessions ada dan writable
        $sessionDir = storage_path('framework/sessions');
        @mkdir($sessionDir, 0777, true);
        @chmod($sessionDir, 0777);

        // 2. Deteksi HTTPS
        $isSecure = $request->secure()
            || $request->header('X-Forwarded-Proto') === 'https'
            || $request->header('X-Forwarded-Ssl') === 'on';

        // 3. Override config session sebelum StartSession
        config([
            'session.driver'    => env('SESSION_DRIVER', 'file'),
            'session.encrypt'   => false,
            'session.secure'    => $isSecure,
            'session.same_site' => 'Lax',
            'session.http_only' => true,
            'session.cookie'    => 'icb_ct_session',
            'session.path'      => '/',
            'session.domain'    => null,
            'session.lifetime'  => env('SESSION_LIFETIME', 120),
        ]);

        return $next($request);
    }
}
