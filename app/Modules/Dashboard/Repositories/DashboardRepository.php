<?php

namespace App\Modules\Dashboard\Repositories;

use App\Modules\Dashboard\Models\Dashboard;
use App\Modules\Dashboard\Exceptions\DashboardException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DashboardRepository
{
    public function __construct(
        private Dashboard $model
    ) {}

    /**
     * Tüm dashboard'ları getir
     */
    public function getAll(): Collection
    {
        return $this->model
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Tüm dashboard'ları sayfalı olarak getir
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * ID'ye göre dashboard bul
     */
    public function findById(int $id): ?Dashboard
    {
        return $this->model->find($id);
    }

    /**
     * ID'ye göre dashboard bul (exception ile)
     */
    public function findByIdOrFail(int $id): Dashboard
    {
        $dashboard = $this->findById($id);
        
        if (!$dashboard) {
            throw DashboardException::dashboardNotFound($id);
        }
        
        return $dashboard;
    }

    /**
     * Aktif dashboard'ları getir
     */
    public function getActive(): Collection
    {
        return $this->model
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Kullanıcıya ait dashboard'ları getir
     */
    public function getByUserId(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Dashboard arama
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Yeni dashboard oluştur
     */
    public function create(array $data): Dashboard
    {
        return $this->model->create($data);
    }

    /**
     * Dashboard güncelle
     */
    public function update(int $id, array $data): bool
    {
        $dashboard = $this->findById($id);
        
        if (!$dashboard) {
            throw DashboardException::dashboardNotFound($id);
        }

        return $dashboard->update($data);
    }

    /**
     * Dashboard sil
     */
    public function delete(int $id): bool
    {
        $dashboard = $this->findById($id);
        
        if (!$dashboard) {
            throw DashboardException::dashboardNotFound($id);
        }

        return $dashboard->delete();
    }

    /**
     * Dashboard istatistiklerini getir
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->where('is_active', true)->count(),
            'inactive' => $this->model->where('is_active', false)->count(),
            'today' => $this->model->whereDate('created_at', today())->count(),
            'this_week' => $this->model->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => $this->model->whereMonth('created_at', now()->month)->count(),
        ];
    }

    /**
     * Kullanıcı tercihlerini getir
     */
    public function getUserPreferences(int $userId): array
    {
        $dashboard = $this->model
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->first();

        return $dashboard?->user_preferences ?? [];
    }

    /**
     * Kullanıcı tercihlerini güncelle
     */
    public function updateUserPreferences(int $userId, array $preferences): bool
    {
        $dashboard = $this->model
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->first();

        if (!$dashboard) {
            // Varsayılan dashboard oluştur
            $dashboard = $this->create([
                'user_id' => $userId,
                'title' => 'Ana Dashboard',
                'description' => 'Kullanıcı ana dashboard\'u',
                'is_active' => true,
                'user_preferences' => $preferences,
            ]);
        } else {
            $dashboard->update(['user_preferences' => $preferences]);
        }

        return true;
    }

    /**
     * Model instance'ını getir
     */
    public function getModel(): Dashboard
    {
        return $this->model;
    }
} 