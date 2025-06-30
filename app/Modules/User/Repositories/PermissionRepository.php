<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\Permission;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PermissionRepository
{
    public function __construct(
        private Permission $model
    ) {}

    /**
     * Tüm yetkileri getir (sayfalama olmadan)
     */
    public function getAll(): Collection
    {
        return $this->model->with('roles')->get();
    }

    /**
     * Tüm yetkileri sayfalı olarak getir
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('roles')->paginate($perPage);
    }

    /**
     * Aktif yetkileri getir
     */
    public function getActivePermissions(): Collection
    {
        return $this->model->active()->with('roles')->get();
    }

    /**
     * Modüle göre yetkileri getir
     */
    public function getByModule(string $module): Collection
    {
        return $this->model->byModule($module)->with('roles')->get();
    }

    /**
     * ID'ye göre yetki bul
     */
    public function findById(int $id): ?Permission
    {
        return $this->model->with('roles')->find($id);
    }

    /**
     * Name'e göre yetki bul
     */
    public function findByName(string $name): ?Permission
    {
        return $this->model->where('name', $name)->first();
    }

    /**
     * Yetki arama
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with('roles')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('display_name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orWhere('module', 'like', "%{$query}%")
            ->paginate($perPage);
    }

    /**
     * Yeni yetki oluştur
     */
    public function create(array $data): Permission
    {
        return $this->model->create($data);
    }

    /**
     * Yetki güncelle
     */
    public function update(int $id, array $data): bool
    {
        $permission = $this->findById($id);
        
        if (!$permission) {
            return false;
        }

        return $permission->update($data);
    }

    /**
     * Yetki sil
     */
    public function delete(int $id): bool
    {
        $permission = $this->findById($id);
        
        if (!$permission) {
            return false;
        }

        return $permission->delete();
    }

    /**
     * Yetkiye sahip kullanıcıları getir
     */
    public function getUsersByPermission(int $permissionId): Collection
    {
        $permission = $this->findById($permissionId);
        
        if (!$permission) {
            return collect();
        }
        
        return $permission->users;
    }

    /**
     * Yetkiye sahip rolleri getir
     */
    public function getRolesByPermission(int $permissionId): Collection
    {
        $permission = $this->findById($permissionId);
        
        if (!$permission) {
            return collect();
        }
        
        return $permission->roles;
    }

    /**
     * Modülleri getir
     */
    public function getModules(): array
    {
        return $this->model
            ->distinct()
            ->pluck('module')
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * İstatistikleri getir
     */
    public function getStats(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->active()->count(),
            'modules' => count($this->getModules()),
            'unassigned' => $this->model->whereDoesntHave('roles')->count(),
        ];
    }
} 