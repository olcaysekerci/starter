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
        'deleted_info',
        'resolved_event'
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
        if (!$this->description) {
            return 'İşlem detayı bulunamadı';
        }

        // Eğer description zaten Türkçe ise direkt döndür
        if (str_contains($this->description, 'oluşturdu') || 
            str_contains($this->description, 'güncelledi') || 
            str_contains($this->description, 'sildi') ||
            str_contains($this->description, 'giriş yaptı') ||
            str_contains($this->description, 'çıkış yaptı')) {
            return $this->description;
        }

        // Event'e göre mapping
        $descriptions = [
            'created' => 'oluşturdu',
            'updated' => 'güncelledi',
            'deleted' => 'sildi',
            'restored' => 'geri yükledi',
            'login' => 'giriş yaptı',
            'logout' => 'çıkış yaptı',
            'profile_updated' => 'profil güncelledi',
            'password_changed' => 'şifre değiştirdi',
            'Giriş yaptı' => 'giriş yaptı',
            'Çıkış yaptı' => 'çıkış yaptı',
            'Başarısız giriş denemesi' => 'başarısız giriş denemesi yaptı',
            'Şifre sıfırlandı' => 'şifre sıfırladı',
            'Kayıt oldu' => 'kayıt oldu',
            'E-posta doğrulandı' => 'e-posta doğruladı',
            'unknown' => 'bilinmeyen işlem',
        ];

        return $descriptions[$this->description] ?? $this->description;
    }

    public function getModelNameAttribute(): string
    {
        if (!$this->subject_type) {
            return 'Sistem';
        }

        $modelNames = [
            'App\Models\User' => 'Kullanıcı',
            'App\Modules\User\Models\User' => 'Kullanıcı',
            'App\Modules\User\Models\Role' => 'Rol',
            'App\Modules\User\Models\Permission' => 'Yetki',
            'App\Modules\Settings\Models\Setting' => 'Ayar',
            'App\Modules\MailNotification\Models\MailLog' => 'Mail Log',
        ];

        return $modelNames[$this->subject_type] ?? class_basename($this->subject_type);
    }

    public function getUserNameAttribute(): string
    {
        if (!$this->causer) {
            return 'Sistem';
        }
        
        return $this->causer->full_name ?? $this->causer->name ?? 'Bilinmeyen Kullanıcı';
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

        // Field name mapping
        $fieldNames = [
            'first_name' => 'Ad',
            'last_name' => 'Soyad',
            'name' => 'Ad',
            'email' => 'E-posta',
            'phone' => 'Telefon',
            'address' => 'Adres',
            'password' => 'Şifre',
            'role_id' => 'Rol',
            'is_active' => 'Aktif Durum',
            'email_verified_at' => 'E-posta Doğrulama',
            'display_name' => 'Görünen Ad',
            'description' => 'Açıklama',
            'guard_name' => 'Guard Adı',
            'permissions' => 'Yetkiler',
            'settings' => 'Ayarlar',
            'value' => 'Değer',
            'type' => 'Tür',
            'status' => 'Durum',
            'subject' => 'Konu',
            'content' => 'İçerik',
            'recipient' => 'Alıcı',
            'from_name' => 'Gönderen Adı',
            'from_email' => 'Gönderen E-posta',
            'sent_at' => 'Gönderilme Tarihi',
            'attempts' => 'Deneme Sayısı',
            'error_message' => 'Hata Mesajı',
        ];

        foreach ($changes as $field => $change) {
            $fieldName = $fieldNames[$field] ?? ucfirst(str_replace('_', ' ', $field));
            
            // Özel değer formatlamaları
            $oldValue = $this->formatFieldValue($field, $change['old'] ?? null);
            $newValue = $this->formatFieldValue($field, $change['new'] ?? null);
            
            $formatted[] = [
                'field' => $field,
                'field_name' => $fieldName,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'is_important' => in_array($field, ['email', 'password', 'name', 'first_name', 'last_name', 'role_id']),
            ];
        }

        return $formatted;
    }

    /**
     * Field değerlerini formatla
     */
    private function formatFieldValue(string $field, $value): string
    {
        if ($value === null || $value === '') {
            return 'Boş';
        }

        // Boolean değerler
        if (is_bool($value)) {
            return $value ? 'Evet' : 'Hayır';
        }

        // Tarih değerleri
        if (in_array($field, ['email_verified_at', 'sent_at', 'created_at', 'updated_at'])) {
            if ($value) {
                return \Carbon\Carbon::parse($value)->format('d.m.Y H:i');
            }
            return 'Boş';
        }

        // Şifre alanı
        if ($field === 'password') {
            return '••••••••';
        }

        // Aktif durum
        if ($field === 'is_active') {
            return $value ? 'Aktif' : 'Pasif';
        }

        // E-posta doğrulama
        if ($field === 'email_verified_at') {
            return $value ? 'Doğrulanmış' : 'Doğrulanmamış';
        }

        // Array değerler
        if (is_array($value)) {
            return implode(', ', $value);
        }

        return (string) $value;
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
     * Event alanı boşsa description'dan event'i çıkar
     */
    public function getResolvedEventAttribute(): string
    {
        if ($this->event) {
            return $this->event;
        }

        if (!$this->description) {
            return 'unknown';
        }

        // Description'dan event'i çıkar
        if (str_contains($this->description, 'Giriş yaptı')) {
            return 'login';
        }
        if (str_contains($this->description, 'Çıkış yaptı')) {
            return 'logout';
        }
        if (str_contains($this->description, 'Başarısız giriş denemesi')) {
            return 'failed_login';
        }
        if (str_contains($this->description, 'Şifre sıfırlandı')) {
            return 'password_reset';
        }
        if (str_contains($this->description, 'Kayıt oldu')) {
            return 'registered';
        }
        if (str_contains($this->description, 'E-posta doğrulandı')) {
            return 'email_verified';
        }
        if (str_contains($this->description, 'oluşturdu')) {
            return 'created';
        }
        if (str_contains($this->description, 'güncelledi')) {
            return 'updated';
        }
        if (str_contains($this->description, 'sildi')) {
            return 'deleted';
        }
        if (str_contains($this->description, 'geri yükledi')) {
            return 'restored';
        }

        return 'unknown';
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