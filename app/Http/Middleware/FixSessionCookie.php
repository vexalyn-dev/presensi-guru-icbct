<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FixSessionCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        // Buat folder wajib yang mungkin belum ada di shared hosting
        $folders = [
            storage_path('framework/cache'),
            storage_path('framework/views'),
            storage_path('framework/sessions'),
            storage_path('logs'),
            storage_path('app'),
        ];
        foreach ($folders as $folder) {
            if (!is_dir($folder)) {
                @mkdir($folder, 0777, true);
            }
            @chmod($folder, 0777);
        }

        return $next($request);
    }
}
