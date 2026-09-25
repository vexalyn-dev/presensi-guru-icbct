<?php

if (!function_exists('activeLayout')) {
    /**
     * Return the correct layout based on authenticated user role.
     * - guru_piket  → layouts.piket
     * - guru        → layouts.teacher
     * - admin/operator → layouts.app
     */
    function activeLayout(): string
    {
        $user = auth()->user();
        if (!$user) return 'layouts.app';

        // On teacher-specific routes, guru_piket should use layouts.teacher too
        $currentRoute = request()->route();
        if ($currentRoute && str_starts_with($currentRoute->getName() ?? '', 'teacher.')) {
            return 'layouts.teacher';
        }

        return match($user->role) {
            'guru_piket' => 'layouts.piket',
            'guru'       => 'layouts.teacher',
            default      => 'layouts.app',
        };
    }
}
