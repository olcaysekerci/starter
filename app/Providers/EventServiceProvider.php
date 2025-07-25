<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Modules\ActivityLog\Listeners\AuthEventListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Auth event listener'larını kaydet
        $authListener = new AuthEventListener();
        
        Event::listen(\Illuminate\Auth\Events\Login::class, [$authListener, 'handleLogin']);
        Event::listen(\Illuminate\Auth\Events\Logout::class, [$authListener, 'handleLogout']);
        Event::listen(\Illuminate\Auth\Events\Failed::class, [$authListener, 'handleFailed']);
        Event::listen(\Illuminate\Auth\Events\PasswordReset::class, [$authListener, 'handlePasswordReset']);
        Event::listen(\Illuminate\Auth\Events\Registered::class, [$authListener, 'handleRegistered']);
        Event::listen(\Illuminate\Auth\Events\Verified::class, [$authListener, 'handleVerified']);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
} 