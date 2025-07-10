<?php

namespace App\Modules\Dashboard;

use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Dashboard modülü servislerini kaydet
        $this->app->bind(\App\Modules\Dashboard\Services\DashboardService::class);
        
        // Dashboard modülü repository'lerini kaydet
        $this->app->bind(\App\Modules\Dashboard\Repositories\DashboardRepository::class);
        
        // Dashboard modülü action'larını kaydet
        $this->app->bind(\App\Modules\Dashboard\Actions\UpdateDashboardAction::class);
        
        // Dashboard modülü model'lerini kaydet
        $this->app->bind(\App\Modules\Dashboard\Models\Dashboard::class, function ($app) {
            return new \App\Modules\Dashboard\Models\Dashboard();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Dashboard modülü route'larını yükle
        $this->loadRoutesFrom(__DIR__ . '/Panel/routes.php');
    }
} 