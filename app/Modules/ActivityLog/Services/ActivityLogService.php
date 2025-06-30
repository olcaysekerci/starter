<?php

namespace App\Modules\ActivityLog\Services;

use App\Modules\ActivityLog\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ActivityLogService
{
    /**
     * Logları listele
     */
    public function getLogs(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Activity::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc');

        // Filtreleri uygula
        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * Tekil log detayı
     */
    public function getLog(int $id): ?Activity
    {
        return Activity::with(['causer', 'subject'])
            ->find($id);
    }

    /**
     * İstatistikleri al
     */
    public function getStats(): array
    {
        return [
            'total' => Activity::count(),
            'today' => Activity::whereDate('created_at', today())->count(),
            'this_week' => Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Activity::whereMonth('created_at', now()->month)->count(),
        ];
    }

    /**
     * Filtreleri uygula
     */
    protected function applyFilters(Builder $query, array $filters): void
    {
        // Kullanıcı filtresi
        if (!empty($filters['user_id'])) {
            $query->byUser($filters['user_id']);
        }

        // Model filtresi
        if (!empty($filters['model_type'])) {
            $query->byModel($filters['model_type']);
        }

        // Event filtresi
        if (!empty($filters['event'])) {
            $query->byEvent($filters['event']);
        }

        // Tarih aralığı filtresi
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->inDateRange($filters['date_from'], $filters['date_to']);
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
    }

    /**
     * Eski logları temizle
     */
    public function cleanupOldLogs(int $days = 30): int
    {
        $cutoffDate = now()->subDays($days);
        
        return Activity::where('created_at', '<', $cutoffDate)->delete();
    }

    /**
     * Kullanıcı loglarını temizle
     */
    public function cleanupUserLogs(int $userId): int
    {
        return Activity::where('causer_id', $userId)->delete();
    }

    /**
     * Model loglarını temizle
     */
    public function cleanupModelLogs(string $modelType): int
    {
        return Activity::where('subject_type', $modelType)->delete();
    }

    /**
     * Özel log kaydet
     */
    public function logCustomActivity(string $description, array $properties = []): Activity
    {
        return activity()
            ->causedBy(auth()->user())
            ->withProperties(array_merge($properties, [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]))
            ->log($description);
    }

    /**
     * Auth event logları
     */
    public function logAuthEvent(string $event, array $properties = []): Activity
    {
        return activity()
            ->causedBy(auth()->user())
            ->withProperties(array_merge($properties, [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]))
            ->log($event);
    }

    /**
     * Model değişiklik logları için özel metod
     */
    public function logModelChange(string $modelType, int $modelId, string $event, array $changes = []): Activity
    {
        return activity()
            ->causedBy(auth()->user())
            ->performedOn($modelType::find($modelId))
            ->withProperties(array_merge($changes, [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]))
            ->log($event);
    }

    /**
     * Toplu log temizleme
     */
    public function bulkCleanup(array $criteria): int
    {
        $query = Activity::query();

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
     * Log analizi
     */
    public function getAnalytics(array $filters = []): array
    {
        $query = Activity::query();
        $this->applyFilters($query, $filters);

        return [
            'by_event' => $query->clone()
                ->select('event', DB::raw('count(*) as count'))
                ->groupBy('event')
                ->pluck('count', 'event')
                ->toArray(),
            
            'by_model' => $query->clone()
                ->select('subject_type', DB::raw('count(*) as count'))
                ->groupBy('subject_type')
                ->pluck('count', 'subject_type')
                ->toArray(),
            
            'by_user' => $query->clone()
                ->select('causer_id', DB::raw('count(*) as count'))
                ->with('causer:id,name')
                ->groupBy('causer_id')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->causer->name ?? 'Bilinmeyen' => $item->count];
                })
                ->toArray(),
        ];
    }
} 