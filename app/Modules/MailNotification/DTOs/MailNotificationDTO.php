<?php

namespace App\Modules\MailNotification\DTOs;

use App\Modules\MailNotification\Enums\MailStatus;

class MailNotificationDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $recipient,
        public readonly string $subject,
        public readonly string $content,
        public readonly string $type,
        public readonly MailStatus $status,
        public readonly ?string $sent_at = null,
        public readonly ?string $error_message = null,
        public readonly ?string $ip_address = null,
        public readonly ?string $user_agent = null,
        public readonly ?array $metadata = null,
        public readonly int $retry_count = 0,
        public readonly ?string $last_retry_at = null,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
        public readonly ?string $status_label = null,
        public readonly ?string $status_color = null,
        public readonly ?string $status_badge_class = null,
        public readonly ?string $short_content = null,
        public readonly ?string $formatted_sent_at = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            recipient: $data['recipient'],
            subject: $data['subject'],
            content: $data['content'],
            type: $data['type'],
            status: $data['status'] instanceof MailStatus ? $data['status'] : MailStatus::from($data['status']),
            sent_at: $data['sent_at'] ?? null,
            error_message: $data['error_message'] ?? null,
            ip_address: $data['ip_address'] ?? null,
            user_agent: $data['user_agent'] ?? null,
            metadata: $data['metadata'] ?? null,
            retry_count: $data['retry_count'] ?? 0,
            last_retry_at: $data['last_retry_at'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            status_label: $data['status_label'] ?? null,
            status_color: $data['status_color'] ?? null,
            status_badge_class: $data['status_badge_class'] ?? null,
            short_content: $data['short_content'] ?? null,
            formatted_sent_at: $data['formatted_sent_at'] ?? null,
        );
    }

    public static function fromModel($mailLog): self
    {
        return new self(
            id: $mailLog->id,
            recipient: $mailLog->recipient,
            subject: $mailLog->subject,
            content: $mailLog->content,
            type: $mailLog->type,
            status: $mailLog->status,
            sent_at: $mailLog->sent_at?->toISOString(),
            error_message: $mailLog->error_message,
            ip_address: $mailLog->ip_address,
            user_agent: $mailLog->user_agent,
            metadata: $mailLog->metadata,
            retry_count: $mailLog->retry_count,
            last_retry_at: $mailLog->last_retry_at?->toISOString(),
            created_at: $mailLog->created_at?->toISOString(),
            updated_at: $mailLog->updated_at?->toISOString(),
            status_label: $mailLog->status_label ?? null,
            status_color: $mailLog->status_color ?? null,
            status_badge_class: $mailLog->status_badge_class ?? null,
            short_content: $mailLog->short_content ?? null,
            formatted_sent_at: $mailLog->formatted_sent_at ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'recipient' => $this->recipient,
            'subject' => $this->subject,
            'content' => $this->content,
            'type' => $this->type,
            'status' => $this->status->value,
            'sent_at' => $this->sent_at,
            'error_message' => $this->error_message,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'metadata' => $this->metadata,
            'retry_count' => $this->retry_count,
            'last_retry_at' => $this->last_retry_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'status_badge_class' => $this->status_badge_class,
            'short_content' => $this->short_content,
            'formatted_sent_at' => $this->formatted_sent_at,
        ];
    }

    public function isSent(): bool
    {
        return $this->status === MailStatus::SENT;
    }

    public function isFailed(): bool
    {
        return $this->status === MailStatus::FAILED;
    }

    public function isPending(): bool
    {
        return $this->status === MailStatus::PENDING;
    }

    public function canRetry(): bool
    {
        $maxRetries = config('modules.MailNotification.settings.mail_notification.max_retry_attempts', 3);
        return $this->isFailed() && $this->retry_count < $maxRetries;
    }

    public function hasError(): bool
    {
        return !empty($this->error_message);
    }

    public function getTypeLabel(): string
    {
        $labels = [
            'notification' => 'Bildirim',
            'welcome' => 'Hoş Geldin',
            'test' => 'Test',
            'email_change' => 'Email Değişikliği',
            'password_reset' => 'Şifre Sıfırlama',
            'verification' => 'Doğrulama',
        ];

        return $labels[$this->type] ?? ucfirst($this->type);
    }

    public function getRetryStatus(): string
    {
        if ($this->isSent()) {
            return 'Gönderildi';
        }

        if ($this->isFailed()) {
            if ($this->canRetry()) {
                return "Yeniden Denenebilir ({$this->retry_count}/3)";
            } else {
                return 'Maksimum Deneme Sayısına Ulaşıldı';
            }
        }

        if ($this->isPending()) {
            return 'Bekliyor';
        }

        return 'Bilinmeyen';
    }
} 