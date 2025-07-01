<?php

namespace App\Modules\Dashboard\Exceptions;

use Exception;

class DashboardException extends Exception
{
    public static function dashboardNotFound(int $id): self
    {
        return new self("Dashboard bulunamadı. ID: {$id}");
    }

    public static function dashboardUpdateFailed(int $id): self
    {
        return new self("Dashboard güncellenemedi. ID: {$id}");
    }

    public static function dashboardCreateFailed(): self
    {
        return new self("Dashboard oluşturulamadı.");
    }

    public static function dashboardDeleteFailed(int $id): self
    {
        return new self("Dashboard silinemedi. ID: {$id}");
    }

    public static function invalidDashboardData(): self
    {
        return new self("Geçersiz dashboard verisi.");
    }

    public static function statisticsLoadFailed(): self
    {
        return new self("Dashboard istatistikleri yüklenemedi.");
    }

    public static function widgetLoadFailed(string $widgetName): self
    {
        return new self("Dashboard widget'ı yüklenemedi: {$widgetName}");
    }

    public static function chartDataLoadFailed(string $chartName): self
    {
        return new self("Dashboard grafik verisi yüklenemedi: {$chartName}");
    }

    public static function userPreferencesLoadFailed(): self
    {
        return new self("Kullanıcı dashboard tercihleri yüklenemedi.");
    }

    public static function userPreferencesSaveFailed(): self
    {
        return new self("Kullanıcı dashboard tercihleri kaydedilemedi.");
    }

    public static function dashboardAccessDenied(): self
    {
        return new self("Dashboard'a erişim reddedildi.");
    }

    public static function dashboardConfigurationError(): self
    {
        return new self("Dashboard konfigürasyon hatası.");
    }

    public static function realTimeDataLoadFailed(): self
    {
        return new self("Gerçek zamanlı dashboard verisi yüklenemedi.");
    }

    public static function exportFailed(string $format): self
    {
        return new self("Dashboard verisi {$format} formatında dışa aktarılamadı.");
    }

    public static function cacheClearFailed(): self
    {
        return new self("Dashboard cache'i temizlenemedi.");
    }
} 