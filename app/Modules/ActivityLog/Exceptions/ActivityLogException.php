<?php

namespace App\Modules\ActivityLog\Exceptions;

use Exception;

class ActivityLogException extends Exception
{
    public static function logNotFound(int $id): self
    {
        return new self("Log bulunamadı. ID: {$id}");
    }

    public static function cleanupFailed(int $days): self
    {
        return new self("{$days} günden eski loglar temizlenirken hata oluştu.");
    }

    public static function userLogsCleanupFailed(int $userId): self
    {
        return new self("Kullanıcı logları temizlenirken hata oluştu. User ID: {$userId}");
    }

    public static function modelLogsCleanupFailed(string $modelType): self
    {
        return new self("Model logları temizlenirken hata oluştu. Model: {$modelType}");
    }

    public static function bulkCleanupFailed(): self
    {
        return new self("Toplu temizleme işlemi başarısız oldu.");
    }

    public static function invalidFilter(string $filter): self
    {
        return new self("Geçersiz filtre: {$filter}");
    }

    public static function analyticsFailed(): self
    {
        return new self("Analitik veriler alınırken hata oluştu.");
    }

    public static function exportFailed(): self
    {
        return new self("Log dışa aktarma işlemi başarısız oldu.");
    }

    public static function logCreationFailed(string $description): self
    {
        return new self("Log kaydı oluşturulamadı: {$description}");
    }
} 