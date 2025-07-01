<?php

namespace App\Modules\Settings\Exceptions;

use Exception;

class SettingsException extends Exception
{
    public static function settingNotFound(string $category, string $key): self
    {
        return new self("Ayar bulunamadı. Kategori: {$category}, Anahtar: {$key}");
    }

    public static function categoryNotFound(string $category): self
    {
        return new self("Ayar kategorisi bulunamadı: {$category}");
    }

    public static function invalidSettingType(string $type): self
    {
        return new self("Geçersiz ayar tipi: {$type}");
    }

    public static function settingUpdateFailed(string $category, string $key): self
    {
        return new self("Ayar güncellenemedi. Kategori: {$category}, Anahtar: {$key}");
    }

    public static function settingDeleteFailed(string $category, string $key): self
    {
        return new self("Ayar silinemedi. Kategori: {$category}, Anahtar: {$key}");
    }

    public static function categoryDeleteFailed(string $category): self
    {
        return new self("Ayar kategorisi silinemedi: {$category}");
    }

    public static function cacheClearFailed(string $category = null): self
    {
        $message = "Ayar cache'i temizlenemedi";
        if ($category) {
            $message .= ". Kategori: {$category}";
        }
        return new self($message);
    }

    public static function defaultSettingsLoadFailed(): self
    {
        return new self("Varsayılan ayarlar yüklenemedi.");
    }

    public static function mailTestFailed(string $email): self
    {
        return new self("Mail test başarısız. Email: {$email}");
    }

    public static function mailConfigurationError(): self
    {
        return new self("Mail konfigürasyon hatası. Lütfen mail ayarlarını kontrol edin.");
    }

    public static function invalidEmailAddress(string $email): self
    {
        return new self("Geçersiz email adresi: {$email}");
    }

    public static function requiredSettingMissing(string $key): self
    {
        return new self("Zorunlu ayar eksik: {$key}");
    }

    public static function settingValidationFailed(string $key, string $message): self
    {
        return new self("Ayar doğrulama hatası. Anahtar: {$key}, Hata: {$message}");
    }

    public static function appSettingsUpdateFailed(): self
    {
        return new self("Uygulama ayarları güncellenemedi.");
    }

    public static function mailSettingsUpdateFailed(): self
    {
        return new self("Mail ayarları güncellenemedi.");
    }

    public static function configCacheFailed(): self
    {
        return new self("Konfigürasyon cache'i oluşturulamadı.");
    }
} 