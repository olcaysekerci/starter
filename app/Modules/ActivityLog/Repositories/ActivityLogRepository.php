<?php

namespace App\Modules\ActivityLog\Repositories;

use App\Modules\ActivityLog\Models\Activity;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogRepository
{
    public function __construct(
        private Activity $model
    ) {}

    /**
     * Tüm logları getir (sayfalama olmadan)
     */
    public function getAll(): Collection
    {
        return $this->model
            ->with(['causer', 'subject'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Tüm logları sayfalı olarak getir
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['causer', 'subject'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * ID'ye göre log bul
     */
    public function findById(int $id): ?Activity
    {
        return $this->model
            ->with(['causer', 'subject'])
            ->find($id);
    }

    /**
     * Kullanıcıya göre logları getir
     */
    public function findByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['causer', 'subject'])
            ->where('causer_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Modele göre logları getir
     */
    public function findByModel(string $modelType, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['causer', 'subject'])
            ->where('subject_type', $modelType)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Event'e göre logları getir
     */
    public function findByEvent(string $event, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['causer', 'subject'])
            ->where('event', $event)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Tarih aralığına göre logları getir
     */
    public function findByDateRange(string $startDate, string $endDate, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['causer', 'subject'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Log arama
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['causer', 'subject'])
            ->where(function ($q) use ($query) {
                $q->where('description', 'like', "%{$query}%")
                  ->orWhereHas('causer', function ($userQuery) use ($query) {
                      $userQuery->where('name', 'like', "%{$query}%")
                               ->orWhere('email', 'like', "%{$query}%");
                  });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Filtrelere göre logları getir
     */
    public function getWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['causer', 'subject']);

        // Kullanıcı filtresi
        if (!empty($filters['user_id'])) {
            $query->where('causer_id', $filters['user_id']);
        }

        // Model filtresi
        if (!empty($filters['model_type'])) {
            $query->where('subject_type', $filters['model_type']);
        }

        // Event filtresi
        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        // Tarih aralığı filtresi
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('created_at', [$filters['date_from'], $filters['date_to']]);
        }

        // Arama filtresi
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('causer', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * İstatistikleri getir
     */
    public function getStats(): array
    {
        return [
            'total' => $this->model->count(),
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
     * Kullanıcı loglarını temizle
     */
    public function cleanupUserLogs(int $userId): int
    {
        return $this->model->where('causer_id', $userId)->delete();
    }

    /**
     * Model loglarını temizle
     */
    public function cleanupModelLogs(string $modelType): int
    {
        return $this->model->where('subject_type', $modelType)->delete();
    }

    /**
     * Toplu temizleme
     */
    public function bulkCleanup(array $criteria): int
    {
        $query = $this->model->query();

        if (!empty($criteria['days'])) {
            $query->where('created_at', '<', now()->subDays($criteria['days']));
        }

        if (!empty($criteria['user_id'])) {
            $query->where('causer_id', $criteria['user_id']);
        }

        if (!empty($criteria['model_type'])) {
            $query->where('subject_type', $criteria['model_type']);
        }

        if (!empty($criteria['event'])) {
            $query->where('event', $criteria['event']);
        }

        return $query->delete();
    }

    /**
     * Model instance'ını getir
     */
    public function getModel(): Activity
    {
        return $this->model;
    }
} 