<?php

namespace App\Modules\ActivityLog\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity as SpatieLogsActivity;

trait LogsActivity
{
    use SpatieLogsActivity;

    /**
     * Activity log ayarları
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getLoggableAttributes())
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($this->getLogName())
            ->setDescriptionForEvent(function (string $eventName) {
                return $this->getDescriptionForEvent($eventName);
            });
    }

    /**
     * Loglanacak alanları belirle
     */
    protected function getLoggableAttributes(): array
    {
        return $this->loggableAttributes ?? [
            'name',
            'email',
            'email_verified_at',
            'password',
            'remember_token',
            'profile_photo_path',
            'two_factor_secret',
            'two_factor_recovery_codes',
            'two_factor_confirmed_at',
            'current_team_id',
            'profile_photo_disk',
        ];
    }

    /**
     * Log adını belirle
     */
    protected function getLogName(): string
    {
        return $this->logName ?? class_basename($this);
    }

    /**
     * Event açıklamalarını belirle
     */
    protected function getDescriptionForEvent(string $eventName): string
    {
        $modelName = $this->getModelDisplayName();
        
        return match ($eventName) {
            'created' => "{$modelName} oluşturdu",
            'updated' => "{$modelName} güncelledi",
            'deleted' => "{$modelName} sildi",
            'restored' => "{$modelName} geri yükledi",
            default => "{$modelName} üzerinde {$eventName} işlemi yapıldı",
        };
    }

    /**
     * Model görünen adını al
     */
    protected function getModelDisplayName(): string
    {
        return $this->displayName ?? class_basename($this);
    }

    /**
     * Özel alan adlarını belirle
     */
    protected function getAttributeNames(): array
    {
        return [
            'name' => 'İsim',
            'email' => 'E-posta',
            'password' => 'Şifre',
            'email_verified_at' => 'E-posta Doğrulama',
            'profile_photo_path' => 'Profil Fotoğrafı',
            'two_factor_secret' => 'İki Faktörlü Doğrulama',
            'current_team_id' => 'Aktif Takım',
        ];
    }

    /**
     * Hassas alanları belirle (loglanmayacak)
     */
    protected function getHiddenAttributes(): array
    {
        return [
            'password',
            'remember_token',
            'two_factor_secret',
            'two_factor_recovery_codes',
        ];
    }



    /**
     * Şifre değişikliği logu
     */
    public function logPasswordChange(): void
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($this)
            ->withProperties([
                'password_changed' => true,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Şifre değiştirdi');
    }

    /**
     * E-posta değişikliği logu
     */
    public function logEmailChange(string $oldEmail, string $newEmail): void
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($this)
            ->withProperties([
                'email_changed' => true,
                'old_email' => $oldEmail,
                'new_email' => $newEmail,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('E-posta adresini değiştirdi');
    }

    /**
     * Admin işlemi logu
     */
    public function logAdminAction(string $description, array $properties = []): void
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($this)
            ->withProperties(array_merge($properties, [
                'admin_action' => true,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]))
            ->log($description);
    }
} 