<?php

namespace App\Modules\ActivityLog\Models;

use Spatie\Activitylog\Models\Activity as SpatieActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class Activity extends SpatieActivity
{
    /**
     * Accessor'ları JSON'a dahil et
     */
    protected $appends = [
        'user_name',
        'model_name', 
        'formatted_description',
        'ip_address',
        'user_agent',
        'has_changes',
        'formatted_changes',
        'is_password_change',
        'is_email_change',
        'is_admin_action',
        'deleted_info'
    ];

    /**
     * Filtreleme scope'ları
     */
    public function scopeByUser(Builder $query, $userId): Builder
    {
        return $query->where('causer_id', $userId);
    }

    public function scopeByModel(Builder $query, string $modelType): Builder
    {
        return $query->where('subject_type', $modelType);
    }

    public function scopeByEvent(Builder $query, string $event): Builder
    {
        return $query->where('event', $event);
    }

    public function scopeInDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Accessor'lar
     */
    public function getFormattedDescriptionAttribute(): string
    {
        if (!$this->event) {
            return 'Bilinmeyen işlem';
        }

        $descriptions = [
            'created' => 'oluşturdu',
            'updated' => 'güncelledi',
            'deleted' => 'sildi',
            'restored' => 'geri yükledi',
            'login' => 'giriş yaptı',
            'logout' => 'çıkış yaptı',
            'profile_updated' => 'profil güncelledi',
            'password_changed' => 'şifre değiştirdi',
            'unknown' => 'bilinmeyen işlem',
        ];

        return $descriptions[$this->event] ?? $this->event;
    }

    public function getModelNameAttribute(): string
    {
        if (!$this->subject_type) {
            return 'Sistem';
        }

        $modelNames = [
            'App\Models\User' => 'Kullanıcı',
            'App\Modules\User\Models\User' => 'Kullanıcı',
        ];

        return $modelNames[$this->subject_type] ?? class_basename($this->subject_type);
    }

    public function getUserNameAttribute(): string
    {
        if (!$this->causer) {
            return 'Sistem';
        }
        
        return $this->causer->name ?? 'Bilinmeyen Kullanıcı';
    }

    public function getIpAddressAttribute(): ?string
    {
        return $this->properties['ip_address'] ?? null;
    }

    public function getUserAgentAttribute(): ?string
    {
        return $this->properties['user_agent'] ?? null;
    }

    public function getHasChangesAttribute(): bool
    {
        return isset($this->properties['changes']) && !empty($this->properties['changes']);
    }

    public function getFormattedChangesAttribute(): array
    {
        if (!$this->has_changes) {
            return [];
        }

        $changes = $this->properties['changes'] ?? [];
        $formatted = [];

        foreach ($changes as $field => $change) {
            $formatted[] = [
                'field' => $field,
                'field_name' => $change['field_name'] ?? ucfirst($field),
                'old_value' => $change['old'] ?? 'Boş',
                'new_value' => $change['new'] ?? 'Boş',
                'is_important' => in_array($field, ['email', 'password', 'name']),
            ];
        }

        return $formatted;
    }

    /**
     * Özel durumlar için accessor'lar
     */
    public function getIsPasswordChangeAttribute(): bool
    {
        return isset($this->properties['password_changed']) && $this->properties['password_changed'];
    }

    public function getIsEmailChangeAttribute(): bool
    {
        return isset($this->properties['email_changed']) && $this->properties['email_changed'];
    }

    public function getIsAdminActionAttribute(): bool
    {
        return isset($this->properties['admin_action']) && $this->properties['admin_action'];
    }

    public function getOldValuesAttribute(): array
    {
        return $this->properties['old'] ?? [];
    }

    public function getNewValuesAttribute(): array
    {
        return $this->properties['attributes'] ?? [];
    }

    /**
     * Silinen kayıt bilgileri
     */
    public function getDeletedInfoAttribute(): ?array
    {
        return $this->properties['deleted_user_info'] ?? null;
    }

    /**
     * Değişiklik özeti
     */
    public function getChangeSummaryAttribute(): string
    {
        if (!$this->has_changes) {
            return $this->description ?? 'Değişiklik yok';
        }

        $changes = $this->properties['changes'] ?? [];
        $changeCount = count($changes);
        $fields = Arr::keys($changes);
        
        if ($changeCount === 1) {
            $fieldName = $changes[$fields[0]]['field_name'] ?? $fields[0];
            return "{$fieldName} alanını değiştirdi";
        }

        return "{$changeCount} alanı değiştirdi: " . implode(', ', Arr::slice($fields, 0, 3)) . 
               ($changeCount > 3 ? ' ve diğerleri' : '');
    }
} 