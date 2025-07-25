<?php

namespace App\Modules\MailNotification\Services;

use App\Traits\TransactionTrait;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Modules\MailNotification\Models\MailLog;
use App\Modules\MailNotification\Enums\MailStatus;
use App\Modules\MailNotification\Repositories\MailNotificationRepository;
use App\Modules\MailNotification\Exceptions\MailNotificationException;
use Illuminate\Mail\Message;

class MailNotificationService
{
    use TransactionTrait;
    public function __construct(
        private MailNotificationRepository $mailNotificationRepository
    ) {}

    /**
     * Mail gönder ve logla
     */
    public function send(array $data): bool
    {
        $mailData = $this->prepareMailData($data);
        
        return $this->executeBooleanInTransaction(function () use ($mailData) {
            // Mail log kaydı oluştur
            $mailLog = $this->mailNotificationRepository->create([
                'recipient' => $mailData['to'],
                'subject' => $mailData['subject'],
                'content' => $mailData['content'],
                'type' => $mailData['type'],
                'status' => MailStatus::PENDING,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => $mailData['metadata'],
                'retry_count' => 0,
            ]);
            
            // Mail gönder
            $sent = $this->sendMail($mailData);
            
            if ($sent) {
                $mailLog->markAsSent();
                
                // Başarılı mail gönderimi aktivite log
                activity('mail_notification')
                    ->causedBy(auth()->user())
                    ->performedOn($mailLog)
                    ->withProperties([
                        'mail_data' => [
                            'recipient' => $mailData['to'],
                            'subject' => $mailData['subject'],
                            'type' => $mailData['type']
                        ],
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent()
                    ])
                    ->log('Mail başarıyla gönderildi');
                
                return true;
            } else {
                $mailLog->markAsFailed('Mail gönderimi başarısız');
                
                // Başarısız mail gönderimi aktivite log
                activity('mail_notification')
                    ->causedBy(auth()->user())
                    ->performedOn($mailLog)
                    ->withProperties([
                        'mail_data' => [
                            'recipient' => $mailData['to'],
                            'subject' => $mailData['subject'],
                            'type' => $mailData['type']
                        ],
                        'error' => 'Mail gönderimi başarısız',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent()
                    ])
                    ->log('Mail gönderimi başarısız');
                
                return false;
            }
        }, 'mail sending');
    }

    /**
     * Test mail gönder
     */
    public function sendTestMail(string $to, string $subject = 'Test Mail'): bool
    {
        Log::info('MailNotificationService::sendTestMail çağrıldı', [
            'to' => $to,
            'subject' => $subject
        ]);

        $content = $this->getTestMailContent();
        
        $result = $this->send([
            'to' => $to,
            'subject' => $subject,
            'content' => $content,
            'type' => 'test'
        ]);

        Log::info('MailNotificationService::sendTestMail sonucu', [
            'to' => $to,
            'subject' => $subject,
            'result' => $result
        ]);

        return $result;
    }

    /**
     * Mail verilerini hazırla
     */
    private function prepareMailData(array $data): array
    {
        $config = config('modules.MailNotification.settings.mail_notification', []);
        
        return [
            'to' => $data['to'],
            'subject' => $data['subject'] ?? 'Bildirim',
            'content' => $data['content'] ?? '',
            'type' => $data['type'] ?? 'notification',
            'from_email' => $data['from_email'] ?? ($config['default_from_email'] ?? env('MAIL_FROM_ADDRESS', 'noreply@example.com')),
            'from_name' => $data['from_name'] ?? ($config['default_from_name'] ?? env('MAIL_FROM_NAME', 'Application')),
            'metadata' => $data['metadata'] ?? [],
        ];
    }

    /**
     * Mail gönder
     */
    private function sendMail(array $mailData): bool
    {
        try {
            Mail::raw($mailData['content'], function (Message $message) use ($mailData) {
                $message->to($mailData['to'])
                        ->subject($mailData['subject'])
                        ->from($mailData['from_email'], $mailData['from_name']);
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error('Mail gönderim hatası', [
                'error' => $e->getMessage(),
                'recipient' => $mailData['to']
            ]);
            return false;
        }
    }

    /**
     * Test mail içeriği
     */
    private function getTestMailContent(): string
    {
        return "Merhaba,\n\n" .
               "Mail sistemi başarıyla çalışıyor. Bu mail, sistem ayarlarınızın doğru yapılandırıldığını onaylar.\n\n" .
               "Teknik Destek Ekibi\n" . config('app.name');
    }

    /**
     * Başarısız mailleri yeniden dene
     */
    public function retryFailedMails(): int
    {
        $failedMails = $this->mailNotificationRepository->getFailedMails();

        $retriedCount = 0;

        foreach ($failedMails as $mailLog) {
            if ($mailLog->canRetry()) {
                $mailLog->incrementRetryCount();
                
                $mailData = [
                    'to' => $mailLog->recipient,
                    'subject' => $mailLog->subject,
                    'content' => $mailLog->content,
                    'type' => $mailLog->type,
                ];

                if ($this->sendMail($mailData)) {
                    $mailLog->markAsSent();
                    $retriedCount++;
                }
            }
        }

        return $retriedCount;
    }

    /**
     * Tekil mail yeniden dene
     */
    public function retrySingleMail(MailLog $mailLog): bool
    {
        if (!$mailLog->canRetry()) {
            return false;
        }

        $mailLog->incrementRetryCount();
        
        $mailData = [
            'to' => $mailLog->recipient,
            'subject' => $mailLog->subject,
            'content' => $mailLog->content,
            'type' => $mailLog->type,
        ];

        if ($this->sendMail($mailData)) {
            $mailLog->markAsSent();
            
            // Mail yeniden deneme aktivite log
            activity('mail_notification')
                ->causedBy(auth()->user())
                ->performedOn($mailLog)
                ->withProperties([
                    'retry_attempt' => $mailLog->retry_count,
                    'mail_data' => [
                        'recipient' => $mailLog->recipient,
                        'subject' => $mailLog->subject,
                        'type' => $mailLog->type
                    ],
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ])
                ->log('Başarısız mail yeniden denendi');
                
            return true;
        }

        return false;
    }

    /**
     * Mail istatistikleri
     */
    public function getStats(): array
    {
        return $this->mailNotificationRepository->getStats();
    }

    /**
     * Mail yeniden gönder
     */
    public function resendMail(MailLog $mailLog): bool
    {
        $mailData = [
            'to' => $mailLog->recipient,
            'subject' => $mailLog->subject,
            'content' => $mailLog->content,
            'type' => $mailLog->type,
        ];

        if ($this->sendMail($mailData)) {
            $mailLog->markAsSent();
            return true;
        }

        return false;
    }

    /**
     * Eski logları temizle
     */
    public function cleanupOldLogs(int $days = 90): int
    {
        return $this->mailNotificationRepository->cleanupOldLogs($days);
    }
}