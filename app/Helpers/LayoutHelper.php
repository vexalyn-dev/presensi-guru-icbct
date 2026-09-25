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

        return match($user->role) {
            'guru_piket' => 'layouts.piket',
            'guru'       => 'layouts.teacher',
            default      => 'layouts.app',
        };
    }
}
