<?php

namespace App\Modules\User;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // User modülü servislerini kaydet
        $this->app->bind(\App\Modules\User\Services\UserService::class);
        $this->app->bind(\App\Modules\User\Repositories\UserRepository::class);
        
        // User modülü model'ini kaydet
        $this->app->bind(\App\Modules\User\Models\User::class, function ($app) {
            return new \App\Modules\User\Models\User();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // User modülü route'larını yükle
        $this->loadRoutesFrom(__DIR__ . '/Web/routes.php');
        $this->loadRoutesFrom(__DIR__ . '/Panel/routes.php');
        
        // User modülü view'larını yükle (eğer blade view'ları kullanılacaksa)
        // $this->loadViewsFrom(__DIR__ . '/Views', 'user');
        
        // User modülü migration'larını yükle
        // $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        
        // User modülü config dosyasını yükle
        // $this->publishes([
        //     __DIR__ . '/Config/user.php' => config_path('user.php'),
        // ], 'user-config');
    }
} 