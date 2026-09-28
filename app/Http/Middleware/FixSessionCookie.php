<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FixSessionCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        // Paksa session cookie non-secure + non-encrypt untuk kompatibilitas maksimal
        config([
            'session.secure'           => false,
            'session.encrypt'          => false,
            'session.same_site'        => 'lax',
            'session.http_only'        => true,
            'session.cookie'           => 'icb_ct_session',
            'session.path'             => '/',
            'session.domain'           => null,
        ]);

        return $next($request);
    }
}
