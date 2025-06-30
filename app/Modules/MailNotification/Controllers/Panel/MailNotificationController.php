<?php

namespace App\Modules\MailNotification\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Modules\MailNotification\Models\MailLog;
use App\Modules\MailNotification\Services\MailDispatcherService;
use App\Modules\MailNotification\Enums\MailStatus;
use Illuminate\Support\Facades\Log;

class MailNotificationController extends Controller
{
    protected $mailDispatcher;

    public function __construct(MailDispatcherService $mailDispatcher)
    {
        $this->mailDispatcher = $mailDispatcher;
    }

    /**
     * Mail logları listesi
     */
    public function index(Request $request)
    {
        $query = MailLog::query();

        // Filtreleme
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('recipient')) {
            $query->byRecipient($request->recipient);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        // Sıralama
        $query->orderBy('created_at', 'desc');

        // Pagination
        $mailLogs = $query->paginate(15)->withQueryString();

        // İstatistikler
        $stats = $this->mailDispatcher->getStats();

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

        return Inertia::render('MailNotification/Index', [
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
    }

    /**
     * Mail detayı
     */
    public function show($id)
    {
        $mailLog = MailLog::findOrFail($id);

        return Inertia::render('MailNotification/Show', [
            'mailLog' => $mailLog,
        ]);
    }

    /**
     * Test mail gönder
     */
    public function test(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'nullable|string|max:255',
        ]);

        $email = $request->email;
        $subject = $request->subject ?? 'Test Mail - ' . config('app.name');

        // Debug log ekle
        Log::info('Test mail gönderimi başlatıldı', [
            'requested_email' => $email,
            'requested_subject' => $subject,
            'request_data' => $request->all()
        ]);

        try {
            $sent = $this->mailDispatcher->sendTestMail($email, $subject);

            if ($sent) {
                Log::info('Test mail başarıyla gönderildi', [
                    'email' => $email,
                    'subject' => $subject
                ]);
                return back()->with('success', 'Test mail başarıyla gönderildi!');
            } else {
                Log::error('Test mail gönderilemedi', [
                    'email' => $email,
                    'subject' => $subject
                ]);
                return back()->with('error', 'Test mail gönderilemedi. Lütfen mail ayarlarını kontrol edin.');
            }
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
            $retriedCount = $this->mailDispatcher->retryFailedMails();

            if ($retriedCount > 0) {
                return back()->with('success', "{$retriedCount} mail başarıyla yeniden gönderildi.");
            } else {
                return back()->with('info', 'Yeniden gönderilecek mail bulunamadı.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Mail yeniden gönderimi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Tekil mail yeniden dene
     */
    public function retrySingle($id)
    {
        try {
            $mailLog = MailLog::findOrFail($id);
            
            if ($mailLog->status !== MailStatus::FAILED) {
                return back()->with('error', 'Bu mail başarısız değil.');
            }

            if (!$mailLog->canRetry()) {
                return back()->with('error', 'Bu mail için maksimum deneme sayısına ulaşıldı.');
            }

            $sent = $this->mailDispatcher->retrySingleMail($mailLog);

            if ($sent) {
                return back()->with('success', 'Mail başarıyla yeniden gönderildi.');
            } else {
                return back()->with('error', 'Mail yeniden gönderilemedi.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Mail yeniden gönderimi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Eski logları temizle
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        try {
            $deletedCount = $this->mailDispatcher->cleanupOldLogs($request->days);
            return back()->with('success', "{$deletedCount} eski mail logu temizlendi.");
        } catch (\Exception $e) {
            return back()->with('error', 'Log temizleme sırasında hata oluştu: ' . $e->getMessage());
        }
    }
} 