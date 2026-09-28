<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            // Global rate limiter — 60 request per menit untuk semua route
            Route::middleware('throttle:60,1')
                ->group(function () {
                    // Semua route di web.php otomatis terkena rate limit
                    // Webhook dikecualikan dengan ->withoutMiddleware di route-nya
                });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'                  => \App\Http\Middleware\RoleMiddleware::class,
            'session.timeout'       => \App\Http\Middleware\EnforceSessionTimeout::class,
            'maintenance.check'     => \App\Http\Middleware\CheckMaintenanceMode::class,
            'csp'                   => \App\Http\Middleware\ContentSecurityPolicy::class,
        ]);

        // Trust reverse proxy / CDN (Cloudflare, cPanel SSL terminator, dll)
        // Pastikan Laravel terdeteksi sebagai HTTPS meskipun request datang dari HTTP internal
        $middleware->trustProxies(at: '*');

        // Force HTTPS — redirect semua HTTP ke HTTPS
        $middleware->appendToGroup('web', function ($request, $next) {
            if (!$request->secure() && app()->environment('production')) {
                return redirect()->secure($request->path());
            }
            return $next($request);
        });

        // Fix session cookie — dijalankan sebelum StartSession agar config override efektif
        $middleware->prependToGroup('web', \App\Http\Middleware\FixSessionCookie::class);

        // Session timeout + maintenance check global untuk semua web request
        $middleware->appendToGroup('web', \App\Http\Middleware\EnforceSessionTimeout::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckMaintenanceMode::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\ContentSecurityPolicy::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            return response()->view('errors.404', [], 404);
        });
    })
    ->create();
