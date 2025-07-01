<?php

namespace App\Modules\Settings\Services;

use App\Modules\Settings\Repositories\SettingsRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class SettingsService
{
    public function __construct(
        private SettingsRepository $settingsRepository
    ) {}

    /**
     * Tüm ayarları al
     */
    public function getAllSettings(): array
    {
        return [
            'app' => $this->getAppSettings(),
            'mail' => $this->getMailSettings(),
        ];
    }

    /**
     * Uygulama ayarlarını al
     */
    public function getAppSettings(): array
    {
        return $this->settingsRepository->getByCategory('app');
    }

    /**
     * Mail ayarlarını al
     */
    public function getMailSettings(): array
    {
        return $this->settingsRepository->getByCategory('mail');
    }

    /**
     * Uygulama ayarlarını güncelle
     */
    public function updateAppSettings(array $data): bool
    {
        try {
            $settings = [
                ['category' => 'app', 'key' => 'site_name', 'value' => $data['site_name'] ?? '', 'type' => 'string'],
                ['category' => 'app', 'key' => 'site_description', 'value' => $data['site_description'] ?? '', 'type' => 'string'],
                ['category' => 'app', 'key' => 'site_logo', 'value' => $data['site_logo'] ?? '', 'type' => 'string'],
                ['category' => 'app', 'key' => 'site_favicon', 'value' => $data['site_favicon'] ?? '', 'type' => 'string'],
                ['category' => 'app', 'key' => 'contact_email', 'value' => $data['contact_email'] ?? '', 'type' => 'string'],
                ['category' => 'app', 'key' => 'contact_phone', 'value' => $data['contact_phone'] ?? '', 'type' => 'string'],
                ['category' => 'app', 'key' => 'contact_address', 'value' => $data['contact_address'] ?? '', 'type' => 'string'],
            ];

            $this->settingsRepository->setMultiple($settings);
            
            Log::info('Uygulama ayarları güncellendi', $data);
            return true;
        } catch (\Exception $e) {
            Log::error('Uygulama ayarları güncellenirken hata oluştu', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return false;
        }
    }

    /**
     * Mail ayarlarını güncelle
     */
    public function updateMailSettings(array $data): bool
    {
        try {
            $settings = [
                ['category' => 'mail', 'key' => 'driver', 'value' => $data['driver'] ?? 'smtp', 'type' => 'string'],
                ['category' => 'mail', 'key' => 'host', 'value' => $data['host'] ?? '', 'type' => 'string'],
                ['category' => 'mail', 'key' => 'port', 'value' => (int) ($data['port'] ?? 587), 'type' => 'integer'],
                ['category' => 'mail', 'key' => 'username', 'value' => $data['username'] ?? '', 'type' => 'string'],
                ['category' => 'mail', 'key' => 'password', 'value' => $data['password'] ?? '', 'type' => 'string'],
                ['category' => 'mail', 'key' => 'encryption', 'value' => $data['encryption'] ?? 'tls', 'type' => 'string'],
                ['category' => 'mail', 'key' => 'from_address', 'value' => $data['from_address'] ?? '', 'type' => 'string'],
                ['category' => 'mail', 'key' => 'from_name', 'value' => $data['from_name'] ?? '', 'type' => 'string'],
                ['category' => 'mail', 'key' => 'enabled', 'value' => (bool) ($data['enabled'] ?? true), 'type' => 'boolean'],
            ];

            $this->settingsRepository->setMultiple($settings);
            
            // Config cache'ini temizle
            Artisan::call('config:clear');
            Artisan::call('config:cache');
            
            Log::info('Mail ayarları güncellendi', array_except($data, ['password']));
            return true;
        } catch (\Exception $e) {
            Log::error('Mail ayarları güncellenirken hata oluştu', [
                'error' => $e->getMessage(),
                'data' => array_except($data, ['password'])
            ]);
            return false;
        }
    }

    /**
     * Mail test gönder
     */
    public function testMail(string $to): bool
    {
        try {
            $mailSettings = $this->getMailSettings();
            
            // Mail ayarlarını geçici olarak güncelle
            config([
                'mail.default' => $mailSettings['driver'] ?? 'smtp',
                'mail.mailers.smtp.host' => $mailSettings['host'] ?? '',
                'mail.mailers.smtp.port' => $mailSettings['port'] ?? 587,
                'mail.mailers.smtp.username' => $mailSettings['username'] ?? '',
                'mail.mailers.smtp.password' => $mailSettings['password'] ?? '',
                'mail.mailers.smtp.encryption' => $mailSettings['encryption'] ?? 'tls',
                'mail.from.address' => $mailSettings['from_address'] ?? '',
                'mail.from.name' => $mailSettings['from_name'] ?? '',
            ]);

            \Mail::raw('Bu bir test mailidir. Mail ayarlarınız başarıyla çalışıyor!', function ($message) use ($to, $mailSettings) {
                $message->to($to)
                        ->subject('Mail Ayarları Test - ' . ($mailSettings['from_name'] ?? config('app.name')))
                        ->from($mailSettings['from_address'] ?? '', $mailSettings['from_name'] ?? '');
            });

            Log::info('Mail test başarılı', ['to' => $to]);
            return true;
        } catch (\Exception $e) {
            Log::error('Mail test başarısız', [
                'error' => $e->getMessage(),
                'to' => $to
            ]);
            return false;
        }
    }

    /**
     * Varsayılan ayarları yükle
     */
    public function loadDefaultSettings(): void
    {
        $this->settingsRepository->loadDefaults();
    }

    /**
     * Cache'i temizle
     */
    public function clearCache(): void
    {
        $this->settingsRepository->clearCache();
    }
} 