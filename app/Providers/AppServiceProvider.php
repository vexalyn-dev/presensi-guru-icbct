<?php

namespace App\Providers;

use App\Models\DeveloperUpdate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        require_once app_path('Helpers/LayoutHelper.php');

        // Share latest active user-visible release to all layouts
        Blade::if('has_release', function ($release) {
            return $release && $release->is_active && $release->show_modal;
        });

        view()->composer(['layouts.app', 'layouts.teacher', 'layouts.piket'], function ($view) {
            try {
                $latestUpdate = DeveloperUpdate::latest_user_visible();
            } catch (\Throwable $e) {
                $latestUpdate = null;
            }
            $view->with('latestUpdate', $latestUpdate);
        });
    }
}
