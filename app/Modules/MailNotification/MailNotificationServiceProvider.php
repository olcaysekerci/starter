<?php

namespace App\Modules\MailNotification;

use Illuminate\Support\ServiceProvider;

class MailNotificationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // MailNotification modülü servislerini kaydet
        $this->app->bind(\App\Modules\MailNotification\Services\MailNotificationService::class);
        
        // MailNotification modülü repository'lerini kaydet
        $this->app->bind(\App\Modules\MailNotification\Repositories\MailNotificationRepository::class);
        
        // MailNotification modülü model'lerini kaydet
        $this->app->bind(\App\Modules\MailNotification\Models\MailLog::class, function ($app) {
            return new \App\Modules\MailNotification\Models\MailLog();
        });
        
        // MailNotification modülü config dosyasını yükle
        $this->mergeConfigFrom(
            __DIR__ . '/config.php', 'mail_notification'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // MailNotification modülü route'larını yükle
        $this->loadRoutesFrom(__DIR__ . '/Panel/routes.php');
        
        // MailNotification modülü config dosyasını yayınla
        $this->publishes([
            __DIR__ . '/config.php' => config_path('mail_notification.php'),
        ], 'mail-notification-config');
    }
}