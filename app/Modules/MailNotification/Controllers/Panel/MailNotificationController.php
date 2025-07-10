<?php

namespace App\Modules\MailNotification\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Modules\MailNotification\Models\MailLog;
use App\Modules\MailNotification\Services\MailNotificationService;
use App\Modules\MailNotification\Repositories\MailNotificationRepository;
use App\Modules\MailNotification\Exceptions\MailNotificationException;
use App\Modules\MailNotification\Enums\MailStatus;
use App\Modules\MailNotification\Requests\SendTestMailRequest;
use App\Modules\MailNotification\Requests\CleanupLogsRequest;
use Illuminate\Support\Facades\Log;

class MailNotificationController extends Controller
{
    public function __construct(
        private MailNotificationService $mailNotificationService,
        private MailNotificationRepository $mailNotificationRepository
    ) {}

    /**
     * Mail logları listesi
     */
    public function index(Request $request)
    {
        try {
            $filters = [
                'search' => $request->search,
                'status' => $request->status,
                'type' => $request->type,
                'recipient' => $request->recipient,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
            ];

            $mailLogs = $this->mailNotificationRepository->getWithFilters($filters, 15);

            // İstatistikler
            $stats = $this->mailNotificationService->getStats();

            // Filtre seçenekleri
            $statusOptions = collect(MailStatus::cases())->map(function ($status) {
                return [
                    'value' => $status->value,
                    'label' => $status->label(),
                ];
            });

            $typeOptions = MailLog::distinct('type')
                ->whereNotNull('type')
                ->pluck('type')
                ->map(function ($type) {
                    return [
                        'value' => $type,
                        'label' => ucfirst($type),
                    ];
                });

            return Inertia::render('MailNotification/Panel/Index', [
                'mailLogs' => $mailLogs,
                'stats' => $stats,
                'filters' => [
                    'search' => $request->search,
                    'status' => $request->status,
                    'type' => $request->type,
                    'recipient' => $request->recipient,
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                ],
                'filterOptions' => [
                    'status' => $statusOptions,
                    'type' => $typeOptions,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Mail logları listesi yüklenirken hata oluştu', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Mail logları yüklenirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Mail detayı
     */
    public function show($id)
    {
        try {
            $mailLog = $this->mailNotificationRepository->findById($id);
            
            if (!$mailLog) {
                throw MailNotificationException::mailLogNotFound($id);
            }

            return Inertia::render('MailNotification/Panel/Show', [
                'mailLog' => $mailLog,
            ]);
        } catch (MailNotificationException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Mail detayı yüklenirken hata oluştu', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Mail detayı yüklenirken hata oluştu.');
        }
    }

    /**
     * Test mail gönder
     */
    public function test(SendTestMailRequest $request)
    {

        $email = $request->email;
        $subject = $request->subject ?? 'Test Mail - ' . config('app.name');

        try {
            $sent = $this->mailNotificationService->sendTestMail($email, $subject);

            if ($sent) {
                return back()->with('success', 'Test mail başarıyla gönderildi!');
            } else {
                throw MailNotificationException::testMailFailed($email);
            }
        } catch (MailNotificationException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Test mail gönderim hatası', [
                'email' => $email,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Test mail gönderilirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Başarısız mailleri yeniden dene
     */
    public function retry()
    {
        try {
            $retriedCount = $this->mailNotificationService->retryFailedMails();

            if ($retriedCount > 0) {
                return back()->with('success', "{$retriedCount} mail başarıyla yeniden gönderildi.");
            } else {
                return back()->with('info', 'Yeniden gönderilecek mail bulunamadı.');
            }
        } catch (\Exception $e) {
            Log::error('Toplu mail yeniden gönderimi hatası', [
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Mail yeniden gönderimi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Tekil mail yeniden dene
     */
    public function retrySingle($id)
    {
        try {
            $mailLog = $this->mailNotificationRepository->findById($id);
            
            if (!$mailLog) {
                throw MailNotificationException::mailLogNotFound($id);
            }
            
            if ($mailLog->status !== MailStatus::FAILED) {
                throw MailNotificationException::mailNotFailed($id);
            }

            if (!$mailLog->canRetry()) {
                throw MailNotificationException::maxRetryAttemptsReached($id);
            }

            $sent = $this->mailNotificationService->retrySingleMail($mailLog);

            if ($sent) {
                return back()->with('success', 'Mail başarıyla yeniden gönderildi.');
            } else {
                throw MailNotificationException::retryFailed($id);
            }
        } catch (MailNotificationException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Tekil mail yeniden gönderimi hatası', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Mail yeniden gönderimi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Mail yeniden gönder
     */
    public function resend($id)
    {
        try {
            $mailLog = $this->mailNotificationRepository->findById($id);
            
            if (!$mailLog) {
                throw MailNotificationException::mailLogNotFound($id);
            }

            $sent = $this->mailNotificationService->resendMail($mailLog);

            if ($sent) {
                return back()->with('success', 'Mail başarıyla yeniden gönderildi.');
            } else {
                throw MailNotificationException::resendFailed($id);
            }
        } catch (MailNotificationException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Mail yeniden gönderimi hatası', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Mail yeniden gönderimi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Eski logları temizle
     */
    public function cleanup(CleanupLogsRequest $request)
    {

        try {
            $deletedCount = $this->mailNotificationService->cleanupOldLogs($request->days);
            return back()->with('success', "{$deletedCount} eski mail logu temizlendi.");
        } catch (\Exception $e) {
            Log::error('Log temizleme hatası', [
                'days' => $request->days,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Log temizleme sırasında hata oluştu: ' . $e->getMessage());
        }
    }
} 