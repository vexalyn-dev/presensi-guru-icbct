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

        // 3. Override config sebelum StartSession
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

        // 4. Manual set cookie via PHP setcookie() sebagai fallback
        //    Ini bypass jika Laravel/Symfony headers di-block oleh cPanel/mod_security
        if (! $request->cookies->has('icb_ct_session')) {
            $sessionName = 'icb_ct_session';
            $sessionId   = session()->getId() ?: 'session_started';

            // Set cookie langsung via PHP
            setcookie($sessionName, $sessionId, [
                'expires'  => time() + (60 * 120),  // 2 jam
                'path'     => '/',
                'domain'   => null,
                'secure'   => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);

            // Also set XSRF-TOKEN cookie untuk CSRF protection
            $csrfToken = csrf_token();
            setcookie('XSRF-TOKEN', $csrfToken, [
                'expires'  => time() + (60 * 120),
                'path'     => '/',
                'domain'   => null,
                'secure'   => $isSecure,
                'httponly' => false,  // XSRF cookie harus bisa dibaca JS
                'samesite' => 'Lax',
            ]);
        }

        return $next($request);
    }
}
