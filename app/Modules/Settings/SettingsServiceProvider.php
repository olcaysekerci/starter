<?php

namespace App\Modules\Settings;

use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Settings modülü servislerini kaydet
        $this->app->bind(\App\Modules\Settings\Services\SettingsService::class);
        
        // Settings modülü repository'lerini kaydet
        $this->app->bind(\App\Modules\Settings\Repositories\SettingsRepository::class);
        
        // Settings modülü model'lerini kaydet
        $this->app->bind(\App\Modules\Settings\Models\Setting::class, function ($app) {
            return new \App\Modules\Settings\Models\Setting();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Settings modülü route'larını yükle
        $this->loadRoutesFrom(__DIR__ . '/Panel/routes.php');
        
        // Settings modülü migration'larını yükle
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        
        // Varsayılan ayarları yükle (sadece ilk kurulumda)
        $this->loadDefaultSettings();
    }

    /**
     * Varsayılan ayarları yükle
     */
    private function loadDefaultSettings(): void
    {
        // Sadece settings tablosu varsa ve boşsa yükle
        if ($this->app->runningInConsole()) {
            return; // Console'da çalışıyorsa yükleme
        }

        try {
            $settingsCount = \App\Modules\Settings\Models\Setting::count();
            
            if ($settingsCount === 0) {
                $settingsService = $this->app->make(\App\Modules\Settings\Services\SettingsService::class);
                $settingsService->loadDefaultSettings();
            }
        } catch (\Exception $e) {
            // Tablo henüz oluşturulmamış olabilir, hata verme
        }
    }
} 