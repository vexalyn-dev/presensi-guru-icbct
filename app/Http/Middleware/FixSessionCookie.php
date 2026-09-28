<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FixSessionCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        // Session cookie aman: secure=true karena site wajib HTTPS
        // encrypt=false supaya gak perlu APP_KEY konsisten
        config([
            'session.secure'      => true,
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
