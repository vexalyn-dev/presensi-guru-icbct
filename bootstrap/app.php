<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'             => \App\Http\Middleware\RoleMiddleware::class,
            'session.timeout'  => \App\Http\Middleware\EnforceSessionTimeout::class,
            'maintenance.check'=> \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);
        // Session timeout + maintenance check global untuk semua web request
        $middleware->appendToGroup('web', \App\Http\Middleware\EnforceSessionTimeout::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckMaintenanceMode::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            return response()->view('errors.404', [], 404);
        });
    })
    ->create();