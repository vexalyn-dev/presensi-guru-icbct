<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FixSessionCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Pastikan folder sessions ada dan writable (cPanel sering lack permission)
        $sessionPath = storage_path('framework/sessions');
        if (!is_dir($sessionPath)) {
            @mkdir($sessionPath, 0777, true);
        }
        @chmod($sessionPath, 0777);

        // 2. Deteksi HTTPS aman (cPanel SSL terminator biasanya set X-Forwarded-Proto)
        $isSecure = $request->secure()
            || $request->header('X-Forwarded-Proto') === 'https'
            || $request->header('X-Forwarded-SSL') === 'on';

        // 3. Override config SESSION sebelum StartSession membaca-nya
        config([
            'session.driver'     => env('SESSION_DRIVER', 'file'),
            'session.encrypt'    => false,  // decrypt gak perlu, session ID tetap random
            'session.secure'     => $isSecure,
            'session.same_site'  => 'lax',
            'session.http_only'  => true,
            'session.cookie'     => 'icb_ct_session',
            'session.path'       => '/',
            'session.domain'     => null,
            'session.lifetime'   => env('SESSION_LIFETIME', 120),
        ]);

        // 4. Langsung set cookie manual sebagai fallback jika StartSession belum jalan
        if (! $request->hasSession() && ! $request->cookies->has('icb_ct_session')) {
            // Session belum dibuat — biarkan StartSession berikutnya yang membuatnya
            // dengan config yang sudah di-override di atas
        }

        return $next($request);
    }
}
