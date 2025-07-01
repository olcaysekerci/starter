<?php

namespace App\Modules\MailNotification\Repositories;

use App\Modules\MailNotification\Models\MailLog;
use App\Modules\MailNotification\Enums\MailStatus;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MailNotificationRepository
{
    public function __construct(
        private MailLog $model
    ) {}

    /**
     * Tüm mail loglarını getir (sayfalama olmadan)
     */
    public function getAll(): Collection
    {
        return $this->model
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Tüm mail loglarını sayfalı olarak getir
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * ID'ye göre mail log bul
     */
    public function findById(int $id): ?MailLog
    {
        return $this->model->find($id);
    }

    /**
     * Mail log arama
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where(function ($q) use ($query) {
                $q->where('recipient', 'like', "%{$query}%")
                  ->orWhere('subject', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Duruma göre mail logları getir
     */
    public function findByStatus(MailStatus $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Tipe göre mail logları getir
     */
    public function findByType(string $type, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Alıcıya göre mail logları getir
     */
    public function findByRecipient(string $recipient, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('recipient', 'like', "%{$recipient}%")
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Tarih aralığına göre mail logları getir
     */
    public function findByDateRange(string $startDate, string $endDate, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Filtrelere göre mail logları getir
     */
    public function getWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query();

        // Arama filtresi
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Durum filtresi
        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        // Tip filtresi
        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        // Alıcı filtresi
        if (!empty($filters['recipient'])) {
            $query->byRecipient($filters['recipient']);
        }

        // Tarih aralığı filtresi
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->byDateRange($filters['date_from'], $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Başarısız mailleri getir
     */
    public function getFailedMails(): Collection
    {
        return $this->model
            ->failed()
            ->where('retry_count', '<', config('modules.MailNotification.settings.mail_notification.max_retry_attempts', 3))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Bekleyen mailleri getir
     */
    public function getPendingMails(): Collection
    {
        return $this->model
            ->pending()
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Gönderilmiş mailleri getir
     */
    public function getSentMails(): Collection
    {
        return $this->model
            ->sent()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * İstatistikleri getir
     */
    public function getStats(): array
    {
        return [
            'total' => $this->model->count(),
            'sent' => $this->model->sent()->count(),
            'failed' => $this->model->failed()->count(),
            'pending' => $this->model->pending()->count(),
            'today' => $this->model->whereDate('created_at', today())->count(),
            'this_week' => $this->model->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => $this->model->whereMonth('created_at', now()->month)->count(),
        ];
    }

    /**
     * Eski logları temizle
     */
    public function cleanupOldLogs(int $days): int
    {
        $cutoffDate = now()->subDays($days);
        return $this->model->where('created_at', '<', $cutoffDate)->delete();
    }

    /**
     * Yeni mail log oluştur
     */
    public function create(array $data): MailLog
    {
        return $this->model->create($data);
    }

    /**
     * Mail log güncelle
     */
    public function update(int $id, array $data): bool
    {
        $mailLog = $this->findById($id);
        
        if (!$mailLog) {
            return false;
        }

        return $mailLog->update($data);
    }

    /**
     * Mail log sil
     */
    public function delete(int $id): bool
    {
        $mailLog = $this->findById($id);
        
        if (!$mailLog) {
            return false;
        }

        return $mailLog->delete();
    }

    /**
     * Model instance'ını getir
     */
    public function getModel(): MailLog
    {
        return $this->model;
    }
} 