<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->role !== $role) {
            if ($user->isTeacher()) {
                return redirect()->route('teacher.dashboard');
            }

            if ($user->isAdmin()) {
                return redirect()->route('dashboard');
            }

            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
