<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        if (!in_array($user->role, $roles)) {
            if ($user->isTeacher()) {
                return redirect()->route('teacher.dashboard');
            }

            if ($user->isAdmin()) {
                return redirect()->route('dashboard');
            }

            abort(403, 'Unauthorized access. You do not have permission to access this page.');
        }

        return $next($request);
    }
}
