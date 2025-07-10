<?php

namespace App\Modules\Settings\Repositories;

use App\Modules\Settings\Models\Setting;
use Illuminate\Support\Collection;

class SettingsRepository
{
    public function __construct(
        private Setting $model
    ) {}
    /**
     * Tüm ayarları al
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * Kategoriye göre ayarları al
     */
    public function getByCategory(string $category): array
    {
        return $this->model::getCategory($category);
    }

    /**
     * Tekil ayar al
     */
    public function get(string $category, string $key, $default = null)
    {
        return $this->model::get($category, $key, $default);
    }

    /**
     * Ayar kaydet
     */
    public function set(string $category, string $key, $value, string $type = 'string', string $description = null): bool
    {
        return $this->model::set($category, $key, $value, $type, $description);
    }

    /**
     * Toplu ayar kaydet
     */
    public function setMultiple(array $settings): bool
    {
        try {
            foreach ($settings as $setting) {
                $this->set(
                    $setting['category'],
                    $setting['key'],
                    $setting['value'],
                    $setting['type'] ?? 'string',
                    $setting['description'] ?? null
                );
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Kategoriye ait ayarları sil
     */
    public function deleteCategory(string $category): bool
    {
        try {
            $this->model->where('category', $category)->delete();
            $this->model::clearCache($category);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Tekil ayar sil
     */
    public function delete(string $category, string $key): bool
    {
        try {
            $this->model->where('category', $category)
                   ->where('key', $key)
                   ->delete();
            $this->model::clearCache($category, $key);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Cache'i temizle
     */
    public function clearCache(string $category = null, string $key = null): void
    {
        $this->model::clearCache($category, $key);
    }

    /**
     * Varsayılan ayarları yükle
     */
    public function loadDefaults(): void
    {
        $defaultSettings = [
            // App Settings
            ['category' => 'app', 'key' => 'site_name', 'value' => config('app.name'), 'type' => 'string', 'description' => 'Site adı'],
            ['category' => 'app', 'key' => 'site_description', 'value' => 'Laravel + Inertia + Vue 3 Uygulaması', 'type' => 'string', 'description' => 'Site açıklaması'],
            ['category' => 'app', 'key' => 'site_logo', 'value' => '', 'type' => 'string', 'description' => 'Site logosu'],
            ['category' => 'app', 'key' => 'site_favicon', 'value' => '', 'type' => 'string', 'description' => 'Site favicon'],
            ['category' => 'app', 'key' => 'contact_email', 'value' => 'info@example.com', 'type' => 'string', 'description' => 'İletişim e-posta adresi'],
            ['category' => 'app', 'key' => 'contact_phone', 'value' => '', 'type' => 'string', 'description' => 'İletişim telefon numarası'],
            ['category' => 'app', 'key' => 'contact_address', 'value' => '', 'type' => 'string', 'description' => 'İletişim adresi'],
            
            // Mail Settings
            ['category' => 'mail', 'key' => 'driver', 'value' => config('mail.default', 'smtp'), 'type' => 'string', 'description' => 'Mail sürücüsü'],
            ['category' => 'mail', 'key' => 'host', 'value' => config('mail.mailers.smtp.host', ''), 'type' => 'string', 'description' => 'SMTP sunucu adresi'],
            ['category' => 'mail', 'key' => 'port', 'value' => config('mail.mailers.smtp.port', 587), 'type' => 'integer', 'description' => 'SMTP port'],
            ['category' => 'mail', 'key' => 'username', 'value' => config('mail.mailers.smtp.username', ''), 'type' => 'string', 'description' => 'SMTP kullanıcı adı'],
            ['category' => 'mail', 'key' => 'password', 'value' => config('mail.mailers.smtp.password', ''), 'type' => 'string', 'description' => 'SMTP şifre'],
            ['category' => 'mail', 'key' => 'encryption', 'value' => config('mail.mailers.smtp.encryption', 'tls'), 'type' => 'string', 'description' => 'SMTP şifreleme'],
            ['category' => 'mail', 'key' => 'from_address', 'value' => config('mail.from.address', 'noreply@example.com'), 'type' => 'string', 'description' => 'Gönderen e-posta adresi'],
            ['category' => 'mail', 'key' => 'from_name', 'value' => config('mail.from.name', config('app.name')), 'type' => 'string', 'description' => 'Gönderen adı'],
            ['category' => 'mail', 'key' => 'enabled', 'value' => true, 'type' => 'boolean', 'description' => 'Mail sistemi aktif mi?'],
        ];

        foreach ($defaultSettings as $setting) {
            $this->set(
                $setting['category'],
                $setting['key'],
                $setting['value'],
                $setting['type'],
                $setting['description']
            );
        }
    }
} 