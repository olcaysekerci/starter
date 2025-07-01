<?php

namespace App\Modules\MailNotification\Exceptions;

use Exception;

class MailNotificationException extends Exception
{
    public static function mailLogNotFound(int $id): self
    {
        return new self("Mail log bulunamadı. ID: {$id}");
    }

    public static function mailSendFailed(string $recipient, string $error = ''): self
    {
        $message = "Mail gönderilemedi. Alıcı: {$recipient}";
        if ($error) {
            $message .= " Hata: {$error}";
        }
        return new self($message);
    }

    public static function invalidEmailAddress(string $email): self
    {
        return new self("Geçersiz email adresi: {$email}");
    }

    public static function mailNotFailed(int $id): self
    {
        return new self("Bu mail başarısız değil. ID: {$id}");
    }

    public static function maxRetryAttemptsReached(int $id): self
    {
        return new self("Bu mail için maksimum deneme sayısına ulaşıldı. ID: {$id}");
    }

    public static function retryFailed(int $id): self
    {
        return new self("Mail yeniden gönderimi başarısız. ID: {$id}");
    }

    public static function cleanupFailed(int $days): self
    {
        return new self("{$days} günden eski mail logları temizlenirken hata oluştu.");
    }

    public static function invalidMailType(string $type): self
    {
        return new self("Geçersiz mail tipi: {$type}");
    }

    public static function mailConfigurationError(): self
    {
        return new self("Mail konfigürasyon hatası. Lütfen mail ayarlarını kontrol edin.");
    }

    public static function testMailFailed(string $email): self
    {
        return new self("Test mail gönderilemedi. Email: {$email}");
    }

    public static function bulkRetryFailed(): self
    {
        return new self("Toplu mail yeniden gönderimi başarısız oldu.");
    }

    public static function mailContentRequired(): self
    {
        return new self("Mail içeriği zorunludur.");
    }

    public static function mailSubjectRequired(): self
    {
        return new self("Mail konusu zorunludur.");
    }

    public static function mailRecipientRequired(): self
    {
        return new self("Mail alıcısı zorunludur.");
    }

    public static function resendFailed(int $id): self
    {
        return new self("Mail yeniden gönderimi başarısız. ID: {$id}");
    }
} 