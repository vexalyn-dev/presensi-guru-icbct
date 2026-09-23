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
        // guru_piket mendapat akses ke route presensi admin (check-status, teachers/data)
        $effectiveRoles = [];
        foreach ($roles as $role) {
            if ($role === 'admin') {
                $effectiveRoles[] = 'operator';
                $effectiveRoles[] = 'guru_piket';
            }
            $effectiveRoles[] = $role;
        }
        $effectiveRoles = array_unique($effectiveRoles);

        if (!in_array($user->role, $effectiveRoles)) {
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
