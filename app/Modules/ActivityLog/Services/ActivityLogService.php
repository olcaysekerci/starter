<?php

namespace App\Modules\ActivityLog\Services;

use App\Modules\ActivityLog\Models\Activity;
use App\Modules\ActivityLog\Repositories\ActivityLogRepository;
use App\Modules\ActivityLog\DTOs\ActivityLogDTO;
use App\Modules\ActivityLog\Exceptions\ActivityLogException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class ActivityLogService
{
    public function __construct(
        private ActivityLogRepository $activityLogRepository
    ) {}

    /**
     * Tüm logları getir (sayfalama olmadan)
     */
    public function getAllLogs(): Collection
    {
        return $this->activityLogRepository->getAll();
    }

    /**
     * Logları listele
     */
    public function getLogs(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->activityLogRepository->getWithFilters($filters, $perPage);
    }

    /**
     * Tekil log detayı
     */
    public function getLog(int $id): ?Activity
    {
        $log = $this->activityLogRepository->findById($id);
        
        if (!$log) {
            throw ActivityLogException::logNotFound($id);
        }
        
        return $log;
    }

    /**
     * Log detayını DTO olarak getir
     */
    public function getLogDTOById(int $id): ?ActivityLogDTO
    {
        $log = $this->activityLogRepository->findById($id);
        
        if (!$log) {
            throw ActivityLogException::logNotFound($id);
        }
        
        return ActivityLogDTO::fromModel($log);
    }

    /**
     * Log detayını getir (frontend için)
     */
    public function getLogById(int $id): array
    {
        $log = $this->activityLogRepository->findById($id);
        
        if (!$log) {
            throw new ModelNotFoundException('Log bulunamadı.');
        }
        
        return [
            'id' => $log->id,
            'event' => $log->event,
            'resolved_event' => $log->resolved_event,
            'description' => $log->description,
            'subject_type' => $log->subject_type,
            'subject_id' => $log->subject_id,
            'causer_id' => $log->causer_id,
            'causer' => $log->causer ? [
                'id' => $log->causer->id,
                'full_name' => $log->causer->full_name,
                'email' => $log->causer->email,
            ] : null,
            'user_name' => $log->user_name,
            'model_name' => $log->model_name,
            'ip_address' => $log->ip_address,
            'user_agent' => $log->user_agent,
            'batch_uuid' => $log->batch_uuid,
            'created_at' => $log->created_at,
            'updated_at' => $log->updated_at,
            'formatted_changes' => $log->formatted_changes,
        ];
    }

    /**
     * İstatistikleri al
     */
    public function getStats(): array
    {
        return $this->activityLogRepository->getStats();
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
        try {
            DB::beginTransaction();
            
            $deletedCount = $this->activityLogRepository->cleanupOldLogs($days);
            
            DB::commit();
            
            Log::info('Eski loglar temizlendi', [
                'days' => $days,
                'deleted_count' => $deletedCount
            ]);
            
            return $deletedCount;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Log temizleme hatası', [
                'days' => $days,
                'error' => $e->getMessage()
            ]);
            
            throw ActivityLogException::cleanupFailed($days);
        }
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