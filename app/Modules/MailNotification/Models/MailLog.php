<?php

namespace App\Modules\MailNotification\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\MailNotification\Enums\MailStatus;
use App\Modules\ActivityLog\Traits\LogsActivity;

class MailLog extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'recipient',
        'subject',
        'content',
        'type',
        'status',
        'sent_at',
        'error_message',
        'ip_address',
        'user_agent',
        'metadata',
        'retry_count',
        'last_retry_at',
    ];

    protected $casts = [
        'status' => MailStatus::class,
        'sent_at' => 'datetime',
        'last_retry_at' => 'datetime',
        'metadata' => 'array',
        'retry_count' => 'integer',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'sent_at',
        'last_retry_at',
    ];

    /**
     * Activity log ayarları
     */
    protected $loggableAttributes = [
        'recipient',
        'subject',
        'type',
        'status',
        'sent_at',
        'error_message',
        'retry_count'
    ];

    protected $displayName = 'Mail Log';

    /**
     * Activity log options override
     */
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly($this->loggableAttributes)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('MailLog')
            ->setDescriptionForEvent(function (string $eventName) {
                return $this->getDescriptionForEvent($eventName);
            });
    }

    /**
     * Event açıklamalarını belirle
     */
    protected function getDescriptionForEvent(string $eventName): string
    {
        return match ($eventName) {
            'created' => 'Mail log kaydı oluşturuldu',
            'updated' => 'Mail log durumu güncellendi',
            'deleted' => 'Mail log kaydı silindi',
            'restored' => 'Mail log kaydı geri yüklendi',
            default => "Mail log üzerinde {$eventName} işlemi yapıldı",
        };
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByRecipient($query, $recipient)
    {
        return $query->where('recipient', 'like', "%{$recipient}%");
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('recipient', 'like', "%{$search}%")
              ->orWhere('subject', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%");
        });
    }

    public function scopeFailed($query)
    {
        return $query->where('status', MailStatus::FAILED);
    }

    public function scopePending($query)
    {
        return $query->where('status', MailStatus::PENDING);
    }

    public function scopeSent($query)
    {
        return $query->where('status', MailStatus::SENT);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute()
    {
        return $this->status->color();
    }

    public function getStatusBadgeClassAttribute()
    {
        return $this->status->badgeClass();
    }

    public function getShortContentAttribute()
    {
        return \Str::limit($this->content, 100);
    }

    public function getFormattedSentAtAttribute()
    {
        return $this->sent_at ? $this->sent_at->format('d.m.Y H:i:s') : '-';
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // Methods
    public function markAsSent()
    {
        $this->update([
            'status' => MailStatus::SENT,
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => MailStatus::FAILED,
            'error_message' => $errorMessage,
        ]);
    }

    public function incrementRetryCount()
    {
        $this->increment('retry_count');
        $this->update(['last_retry_at' => now()]);
    }

    public function canRetry(): bool
    {
        $maxRetries = config('modules.MailNotification.settings.mail_notification.max_retry_attempts', 3);
        return $this->retry_count < $maxRetries;
    }
} 