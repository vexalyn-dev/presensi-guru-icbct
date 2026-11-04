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
            Route::middleware('throttle:60,1')
                ->group(function () {});
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'                  => \App\Http\Middleware\RoleMiddleware::class,
            'session.timeout'       => \App\Http\Middleware\EnforceSessionTimeout::class,
            'maintenance.check'     => \App\Http\Middleware\CheckMaintenanceMode::class,
            'csp'                   => \App\Http\Middleware\ContentSecurityPolicy::class,
        ]);

        // Fix session cookie — SEBELUM StartSession agar config override efektif
        $middleware->prependToGroup('web', \App\Http\Middleware\FixSessionCookie::class);

        $middleware->appendToGroup('web', \App\Http\Middleware\EnforceSessionTimeout::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckMaintenanceMode::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\ContentSecurityPolicy::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            return response()->view('errors.404', [], 404);
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            $msg = $e->getMessage() ?? '';
            // Kalau pesan mengandung 'developer' → tampilkan halaman 403 khusus developer
            if (stripos($msg, 'developer') !== false) {
                return response()->view('errors.403-developer', [], 403);
            }
            return response()->view('errors.403', [], 403);
        });
    })
    ->create();
