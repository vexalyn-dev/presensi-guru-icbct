<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // operator selalu dapat akses ke route yang allow 'admin'
        $allowedRoles = $roles;
        if (in_array('admin', $roles) && !in_array('operator', $roles)) {
            $allowedRoles[] = 'operator';
        }

        if (!in_array($user->role, $allowedRoles)) {
            if ($user->canAccessAdmin()) {
                return redirect()->route('dashboard');
            }
            if ($user->isTeacher()) {
                return redirect()->route('teacher.dashboard');
            }
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
