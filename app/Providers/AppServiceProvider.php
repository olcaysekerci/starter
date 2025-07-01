<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // User modülü service provider'ını kaydet
        $this->app->register(\App\Modules\User\UserServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Süper admin için tüm yetkileri ver
        Gate::before(function ($user, $ability) {
            if ($user && $user->hasRole('super-admin')) {
                return true;
            }
        });

        // Kullanıcı yetkileri
        Gate::define('view-users', function ($user) {
            return $user && $user->hasAnyRole(['super-admin', 'admin', 'User Manager']);
        });

        Gate::define('create-users', function ($user) {
            return $user && $user->hasAnyRole(['super-admin', 'admin', 'User Manager']);
        });

        Gate::define('edit-users', function ($user) {
            return $user && $user->hasAnyRole(['super-admin', 'admin', 'User Manager']);
        });

        Gate::define('delete-users', function ($user) {
            return $user && $user->hasAnyRole(['super-admin', 'admin']);
        });
    }
}
