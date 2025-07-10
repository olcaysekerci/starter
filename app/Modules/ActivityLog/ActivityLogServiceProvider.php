<?php

namespace App\Modules\ActivityLog;

use Illuminate\Support\ServiceProvider;

class ActivityLogServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ActivityLog modülü servislerini kaydet
        $this->app->bind(\App\Modules\ActivityLog\Services\ActivityLogService::class);
        
        // ActivityLog modülü repository'lerini kaydet
        $this->app->bind(\App\Modules\ActivityLog\Repositories\ActivityLogRepository::class);
        
        // ActivityLog modülü model'lerini kaydet
        $this->app->bind(\App\Modules\ActivityLog\Models\Activity::class, function ($app) {
            return new \App\Modules\ActivityLog\Models\Activity();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ActivityLog modülü route'larını yükle
        $this->loadRoutesFrom(__DIR__ . '/Panel/routes.php');
        
        // ActivityLog modülü event listener'larını kaydet
        $this->app['events']->listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Modules\ActivityLog\Listeners\AuthEventListener::class
        );
        
        $this->app['events']->listen(
            \Illuminate\Auth\Events\Logout::class,
            \App\Modules\ActivityLog\Listeners\AuthEventListener::class
        );
        
        $this->app['events']->listen(
            \Illuminate\Auth\Events\Failed::class,
            \App\Modules\ActivityLog\Listeners\AuthEventListener::class
        );
        
        $this->app['events']->listen(
            \Illuminate\Auth\Events\PasswordReset::class,
            \App\Modules\ActivityLog\Listeners\AuthEventListener::class
        );
        
        $this->app['events']->listen(
            \Illuminate\Auth\Events\Registered::class,
            \App\Modules\ActivityLog\Listeners\AuthEventListener::class
        );
        
        $this->app['events']->listen(
            \Illuminate\Auth\Events\Verified::class,
            \App\Modules\ActivityLog\Listeners\AuthEventListener::class
        );
    }
}