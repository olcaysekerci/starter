<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\Role;
use App\Modules\User\Repositories\RoleRepository;
use App\Modules\User\Repositories\PermissionRepository;
use App\Modules\User\DTOs\RoleDTO;
use App\Traits\TransactionTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleService
{
    use TransactionTrait;
    public function __construct(
        private RoleRepository $roleRepository,
        private PermissionRepository $permissionRepository
    ) {}

    /**
     * Tüm rolleri getir (sayfalama olmadan)
     */
    public function getAllRoles(): Collection
    {
        return $this->roleRepository->getAll();
    }

    /**
     * Tüm rolleri sayfalı olarak getir
     */
    public function getAllRolesWithPagination(int $perPage = 15): LengthAwarePaginator
    {
        return $this->roleRepository->getAllPaginated($perPage);
    }

    /**
     * Aktif rolleri getir
     */
    public function getActiveRoles(): Collection
    {
        return $this->roleRepository->getActiveRoles();
    }

    /**
     * Rol detayını getir
     */
    public function getRoleById(int $id): ?Role
    {
        $role = $this->roleRepository->findById($id);
        
        if ($role) {
            // users_count attribute'unu yükle
            $role->loadCount('users');
        }
        
        return $role;
    }

    /**
     * Rol detayını DTO olarak getir
     */
    public function getRoleDTOById(int $id): ?RoleDTO
    {
        $role = $this->roleRepository->findById($id);
        
        return $role ? RoleDTO::fromArray($role->toArray()) : null;
    }

    /**
     * Yeni rol oluştur
     */
    public function createRole(array $data): Role
    {
        return $this->createInTransaction(function() use ($data) {
            // Guard name varsayılan olarak web
            if (!isset($data['guard_name'])) {
                $data['guard_name'] = 'web';
            }

            // Display name otomatik oluştur
            if (!isset($data['display_name']) && isset($data['name'])) {
                $data['display_name'] = $this->generateDisplayName($data['name']);
            }

            // Rol aktif olarak oluştur
            $data['is_active'] = true;

            $role = $this->roleRepository->create($data);
            
            return $role;
        }, 'rol oluşturma');
    }

    /**
     * Display name otomatik oluştur
     */
    private function generateDisplayName(string $name): string
    {
        // Önce name'i temizle (tire, alt çizgi vs. kaldır)
        $cleanName = str_replace(['-', '_', '.'], ' ', $name);
        
        // İlk harfleri büyük yap
        $displayName = ucwords($cleanName);
        
        // Türkçe karakterleri düzelt
        $displayName = str_replace(
            ['İ', 'I', 'İ̇', 'İ̇̇'], 
            ['İ', 'ı', 'İ', 'İ'], 
            $displayName
        );
        
        return $displayName;
    }

    /**
     * Rol güncelle
     */
    public function updateRole(int $id, array $data): bool
    {
        return $this->updateInTransaction(function() use ($id, $data) {
            // Display name otomatik oluştur
            if (isset($data['name'])) {
                $data['display_name'] = $this->generateDisplayName($data['name']);
            }
            
            $result = $this->roleRepository->update($id, $data);
            
            return $result;
        }, 'rol güncelleme');
    }

    /**
     * Rol sil
     */
    public function deleteRole(int $id): bool
    {
        return $this->deleteInTransaction(function() use ($id) {
            $role = $this->roleRepository->findById($id);
            
            // Sistem rolleri silinemez
            if ($role && in_array($role->name, ['super-admin', 'admin'])) {
                return false;
            }
            
            $result = $this->roleRepository->delete($id);
            
            return $result;
        }, 'rol silme');
    }

    /**
     * Rol arama
     */
    public function searchRoles(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->roleRepository->search($query, $perPage);
    }

    /**
     * Role sahip kullanıcıları getir
     */
    public function getUsersByRole(int $roleId): Collection
    {
        return $this->roleRepository->getUsersByRole($roleId);
    }

    /**
     * Tüm yetkileri getir (rol oluşturma/düzenleme için)
     */
    public function getAllPermissions(): Collection
    {
        return $this->permissionRepository->getActivePermissions();
    }

    /**
     * Modüllere göre yetkileri getir
     */
    public function getPermissionsByModules(): array
    {
        $permissions = $this->permissionRepository->getActivePermissions();
        $modules = $this->permissionRepository->getModules();
        
        $grouped = [];
        
        foreach ($modules as $module) {
            $grouped[$module] = $permissions->where('module', $module)->values();
        }
        
        // Modülü olmayan yetkiler
        $ungrouped = $permissions->whereNull('module')->values();
        if ($ungrouped->count() > 0) {
            $grouped['Diğer'] = $ungrouped;
        }
        
        return $grouped;
    }

    /**
     * İstatistikleri getir
     */
    public function getStats(): array
    {
        return $this->roleRepository->getStats();
    }

    /**
     * Name'e göre rol getir
     */
    public function getRoleByName(string $name): ?Role
    {
        return $this->roleRepository->findByName($name);
    }

    /**
     * Varsayılan rolleri oluştur
     */
    public function createDefaultRoles(): void
    {
        $defaultRoles = [
            [
                'name' => 'super-admin',
                'display_name' => 'Süper Yönetici',
                'description' => 'Sistem üzerinde tam yetkiye sahip süper yönetici',
                'guard_name' => 'web',
                'is_active' => true,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Yönetici',
                'description' => 'Sistem yöneticisi',
                'guard_name' => 'web',
                'is_active' => true,
            ],
            [
                'name' => 'moderator',
                'display_name' => 'Moderatör',
                'description' => 'İçerik moderatörü',
                'guard_name' => 'web',
                'is_active' => true,
            ],
            [
                'name' => 'user',
                'display_name' => 'Kullanıcı',
                'description' => 'Standart kullanıcı',
                'guard_name' => 'web',
                'is_active' => true,
            ],
        ];

        foreach ($defaultRoles as $roleData) {
            if (!$this->roleRepository->findByName($roleData['name'])) {
                $this->roleRepository->create($roleData);
            }
        }
    }
} 