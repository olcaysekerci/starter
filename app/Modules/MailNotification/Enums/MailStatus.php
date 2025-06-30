<?php

namespace App\Modules\MailNotification\Enums;

enum MailStatus: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case FAILED = 'failed';
    case DELIVERED = 'delivered';
    case BOUNCED = 'bounced';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Bekliyor',
            self::SENT => 'Gönderildi',
            self::FAILED => 'Başarısız',
            self::DELIVERED => 'Teslim Edildi',
            self::BOUNCED => 'Geri Döndü',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::SENT => 'blue',
            self::FAILED => 'red',
            self::DELIVERED => 'green',
            self::BOUNCED => 'orange',
        };
    }

    public function badgeClass(): string
    {
        $color = $this->color();
        return match($color) {
            'yellow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'red' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'green' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'orange' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }
} 