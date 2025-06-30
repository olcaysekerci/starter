<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository
{
    public function __construct(
        private Role $model
    ) {}

    /**
     * Tüm rolleri getir (sayfalama olmadan)
     */
    public function getAll(): Collection
    {
        return $this->model->with('permissions')->get();
    }

    /**
     * Tüm rolleri sayfalı olarak getir
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('permissions')->paginate($perPage);
    }

    /**
     * Aktif rolleri getir
     */
    public function getActiveRoles(): Collection
    {
        return $this->model->active()->with('permissions')->get();
    }

    /**
     * ID'ye göre rol bul
     */
    public function findById(int $id): ?Role
    {
        return $this->model->with('permissions')->find($id);
    }

    /**
     * Name'e göre rol bul
     */
    public function findByName(string $name): ?Role
    {
        return $this->model->where('name', $name)->first();
    }

    /**
     * Rol arama
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with('permissions')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('display_name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->paginate($perPage);
    }

    /**
     * Yeni rol oluştur
     */
    public function create(array $data): Role
    {
        $role = $this->model->create($data);
        
        // Yetkileri ata
        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }
        
        return $role->load('permissions');
    }

    /**
     * Rol güncelle
     */
    public function update(int $id, array $data): bool
    {
        $role = $this->findById($id);
        
        if (!$role) {
            return false;
        }

        $updated = $role->update($data);
        
        // Yetkileri güncelle
        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }
        
        return $updated;
    }

    /**
     * Rol sil
     */
    public function delete(int $id): bool
    {
        $role = $this->findById($id);
        
        if (!$role) {
            return false;
        }

        return $role->delete();
    }

    /**
     * Role sahip kullanıcıları getir
     */
    public function getUsersByRole(int $roleId): Collection
    {
        $role = $this->findById($roleId);
        
        if (!$role) {
            return collect();
        }
        
        return $role->users;
    }

    /**
     * İstatistikleri getir
     */
    public function getStats(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->active()->count(),
            'system' => $this->model->whereIn('name', ['super-admin', 'admin'])->count(),
            'custom' => $this->model->excludeSystem()->count(),
        ];
    }
} 