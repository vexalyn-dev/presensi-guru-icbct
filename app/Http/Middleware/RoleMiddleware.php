<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // operator mendapat akses ke semua route yang membutuhkan 'admin'
        $effectiveRoles = $roles;
        if (in_array('admin', $roles, true)) {
            $effectiveRoles[] = 'operator';
        }
        $effectiveRoles = array_unique($effectiveRoles);

        if (!in_array($user->role, $effectiveRoles, true)) {
            return $this->redirectByRole($user);
        }

        return $next($request);
    }

    private function redirectByRole($user): Response
    {
        if ($user->isDeveloper()) {
            return redirect()->route('developer.index', config('app.developer_secret_key'));
        }
        if ($user->canAccessAdmin()) {
            return redirect()->route('dashboard');
        }
        if ($user->isGuruPiket()) {
            return redirect()->route('piket.dashboard');
        }
        if ($user->isTeacher()) {
            return redirect()->route('teacher.dashboard');
        }
        abort(403, 'Unauthorized');
    }
}
