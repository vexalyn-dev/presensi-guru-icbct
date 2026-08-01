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

        // operator mendapat akses ke semua route yang membutuhkan 'admin'
        $effectiveRole = ($role === 'admin' && $user->role === 'operator') ? 'admin' : $user->role;

        if ($effectiveRole !== $role) {
            return $this->redirectByRole($user);
        }

        return $next($request);
    }

    private function redirectByRole($user): Response
    {
        if ($user->canAccessAdmin()) {
            return redirect()->route('dashboard');
        }
        if ($user->isTeacher()) {
            return redirect()->route('teacher.dashboard');
        }
        abort(403, 'Unauthorized');
    }
}
