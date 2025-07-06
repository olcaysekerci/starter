<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\Permission;
use App\Modules\User\Repositories\PermissionRepository;
use App\Modules\User\DTOs\PermissionDTO;
use App\Traits\TransactionTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionService
{
    use TransactionTrait;
    public function __construct(
        private PermissionRepository $permissionRepository
    ) {}

    /**
     * Tüm yetkileri getir (sayfalama olmadan)
     */
    public function getAllPermissions(): Collection
    {
        return $this->permissionRepository->getAll();
    }

    /**
     * Tüm yetkileri sayfalı olarak getir
     */
    public function getAllPermissionsWithPagination(int $perPage = 15): LengthAwarePaginator
    {
        return $this->permissionRepository->getAllPaginated($perPage);
    }

    /**
     * Aktif yetkileri getir
     */
    public function getActivePermissions(): Collection
    {
        return $this->permissionRepository->getActivePermissions();
    }

    /**
     * Modüle göre yetkileri getir
     */
    public function getPermissionsByModule(string $module): Collection
    {
        return $this->permissionRepository->getByModule($module);
    }

    /**
     * Yetki detayını getir
     */
    public function getPermissionById(int $id): ?Permission
    {
        return $this->permissionRepository->findById($id);
    }

    /**
     * Yetki detayını DTO olarak getir
     */
    public function getPermissionDTOById(int $id): ?PermissionDTO
    {
        $permission = $this->permissionRepository->findById($id);
        
        return $permission ? PermissionDTO::fromArray($permission->toArray()) : null;
    }

    /**
     * Yeni yetki oluştur
     */
    public function createPermission(array $data): Permission
    {
        return $this->createInTransaction(function() use ($data) {
            // Guard name varsayılan olarak web
            if (!isset($data['guard_name'])) {
                $data['guard_name'] = 'web';
            }

            $permission = $this->permissionRepository->create($data);
            
            return $permission;
        }, 'yetki oluşturma');
    }

    /**
     * Yetki güncelle
     */
    public function updatePermission(int $id, array $data): bool
    {
        return $this->updateInTransaction(function() use ($id, $data) {
            $result = $this->permissionRepository->update($id, $data);
            
            return $result;
        }, 'yetki güncelleme');
    }

    /**
     * Yetki sil
     */
    public function deletePermission(int $id): bool
    {
        return $this->deleteInTransaction(function() use ($id) {
            $permission = $this->permissionRepository->findById($id);
            
            // Sistem yetkileri silinemez
            if ($permission && in_array($permission->name, ['super-admin', 'admin'])) {
                return false;
            }
            
            $result = $this->permissionRepository->delete($id);
            
            return $result;
        }, 'yetki silme');
    }

    /**
     * Yetki arama
     */
    public function searchPermissions(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->permissionRepository->search($query, $perPage);
    }

    /**
     * Yetkiye sahip kullanıcıları getir
     */
    public function getUsersByPermission(int $permissionId): Collection
    {
        return $this->permissionRepository->getUsersByPermission($permissionId);
    }

    /**
     * Yetkiye sahip rolleri getir
     */
    public function getRolesByPermission(int $permissionId): Collection
    {
        return $this->permissionRepository->getRolesByPermission($permissionId);
    }

    /**
     * Modülleri getir
     */
    public function getModules(): array
    {
        return $this->permissionRepository->getModules();
    }

    /**
     * İstatistikleri getir
     */
    public function getStats(): array
    {
        return $this->permissionRepository->getStats();
    }

    /**
     * Varsayılan yetkileri oluştur
     */
    public function createDefaultPermissions(): void
    {
        $defaultPermissions = [
            // User modülü yetkileri
            [
                'name' => 'user.view',
                'display_name' => 'Kullanıcı Görüntüleme',
                'description' => 'Kullanıcıları görüntüleme yetkisi',
                'module' => 'User',
                'guard_name' => 'web',
                'is_active' => true,
            ],
            [
                'name' => 'user.create',
                'display_name' => 'Kullanıcı Oluşturma',
                'description' => 'Yeni kullanıcı oluşturma yetkisi',
                'module' => 'User',
                'guard_name' => 'web',
                'is_active' => true,
            ],
            [
                'name' => 'user.edit',
                'display_name' => 'Kullanıcı Düzenleme',
                'description' => 'Kullanıcı bilgilerini düzenleme yetkisi',
                'module' => 'User',
                'guard_name' => 'web',
                'is_active' => true,
            ],
            [
                'name' => 'user.delete',
                'display_name' => 'Kullanıcı Silme',
                'description' => 'Kullanıcı silme yetkisi',
                'module' => 'User',
                'guard_name' => 'web',
                'is_active' => true,
            ],
            
            // Role modülü yetkileri
            [
                'name' => 'role.view',
                'display_name' => 'Rol Görüntüleme',
                'description' => 'Rolleri görüntüleme yetkisi',
                'module' => 'Role',
                'guard_name' => 'web',
                'is_active' => true,
            ],
            [
                'name' => 'role.create',
                'display_name' => 'Rol Oluşturma',
                'description' => 'Yeni rol oluşturma yetkisi',
                'module' => 'Role',
                'guard_name' => 'web',
                'is_active' => true,
            ],
            [
                'name' => 'role.edit',
                'display_name' => 'Rol Düzenleme',
                'description' => 'Rol bilgilerini düzenleme yetkisi',
                'module' => 'Role',
                'guard_name' => 'web',
                'is_active' => true,
            ],
            [
                'name' => 'role.delete',
                'display_name' => 'Rol Silme',
                'description' => 'Rol silme yetkisi',
                'module' => 'Role',
                'guard_name' => 'web',
                'is_active' => true,
            ],
            
            // Permission modülü yetkileri
            [
                'name' => 'permission.view',
                'display_name' => 'Yetki Görüntüleme',
                'description' => 'Yetkileri görüntüleme yetkisi',
                'module' => 'Permission',
                'guard_name' => 'web',
                'is_active' => true,
            ],
            [
                'name' => 'permission.create',
                'display_name' => 'Yetki Oluşturma',
                'description' => 'Yeni yetki oluşturma yetkisi',
                'module' => 'Permission',
                'guard_name' => 'web',
                'is_active' => true,
            ],
            [
                'name' => 'permission.edit',
                'display_name' => 'Yetki Düzenleme',
                'description' => 'Yetki bilgilerini düzenleme yetkisi',
                'module' => 'Permission',
                'guard_name' => 'web',
                'is_active' => true,
            ],
            [
                'name' => 'permission.delete',
                'display_name' => 'Yetki Silme',
                'description' => 'Yetki silme yetkisi',
                'module' => 'Permission',
                'guard_name' => 'web',
                'is_active' => true,
            ],
        ];

        foreach ($defaultPermissions as $permissionData) {
            if (!$this->permissionRepository->findByName($permissionData['name'])) {
                $this->permissionRepository->create($permissionData);
            }
        }
    }
} 