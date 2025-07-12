<?php

namespace App\Modules\ActivityLog\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Activity;

class AuthEventListener
{
    /**
     * Event listener'ın ana metodu
     */
    public function __invoke($event): void
    {
        switch (get_class($event)) {
            case Login::class:
                $this->handleLogin($event);
                break;
            case Logout::class:
                $this->handleLogout($event);
                break;
            case Failed::class:
                $this->handleFailed($event);
                break;
            case PasswordReset::class:
                $this->handlePasswordReset($event);
                break;
            case Registered::class:
                $this->handleRegistered($event);
                break;
            case Verified::class:
                $this->handleVerified($event);
                break;
        }
    }

    /**
     * Kullanıcı giriş yaptığında
     */
    public function handleLogin(Login $event): void
    {
        activity()
            ->causedBy($event->user)
            ->withProperties([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'login_method' => 'password',
                'remember' => $event->remember ?? false,
            ])
            ->log('Giriş yaptı');
    }

    /**
     * Kullanıcı çıkış yaptığında
     */
    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            activity()
                ->causedBy($event->user)
                ->withProperties([
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ])
                ->log('Çıkış yaptı');
        }
    }

    /**
     * Giriş başarısız olduğunda
     */
    public function handleFailed(Failed $event): void
    {
        activity()
            ->withProperties([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => $event->credentials['email'] ?? 'unknown',
                'reason' => 'Başarısız giriş denemesi',
            ])
            ->log('Başarısız giriş denemesi');
    }

    /**
     * Şifre sıfırlandığında
     */
    public function handlePasswordReset(PasswordReset $event): void
    {
        activity()
            ->causedBy($event->user)
            ->withProperties([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => $event->user->email,
            ])
            ->log('Şifre sıfırlandı');
    }

    /**
     * Kullanıcı kayıt olduğunda
     */
    public function handleRegistered(Registered $event): void
    {
        activity()
            ->causedBy($event->user)
            ->performedOn($event->user)
            ->withProperties([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => $event->user->email,
                'registration_method' => 'web',
            ])
            ->log('Kayıt oldu');
    }

    /**
     * E-posta doğrulandığında
     */
    public function handleVerified(Verified $event): void
    {
        activity()
            ->causedBy($event->user)
            ->performedOn($event->user)
            ->withProperties([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => $event->user->email,
            ])
            ->log('E-posta doğrulandı');
    }
} 