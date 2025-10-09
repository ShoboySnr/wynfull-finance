<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('dashboards.*', function ($view) {
            $user = auth()->user();

            if ($user) {
                $user->loadMissing(['roles']);
                $view->with('authUser', $user);
                $view->with('authMeta', [
//                    'unread_notifications' => $user->unreadNotifications()->count(),
                ]);
            }
        });
    }
}
