<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    public function handle(Request $request, $next): mixed
    {
        // Lewati CSRF token check untuk user developer
        if ($this->isDevUser($request)) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }

    protected function isDevUser(Request $request): bool
    {
        $user = $request->user();
        return $user !== null && method_exists($user, 'isDeveloper') && $user->isDeveloper();
    }
}
