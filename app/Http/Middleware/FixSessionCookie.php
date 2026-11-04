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

        return $next($request);
    }
}
