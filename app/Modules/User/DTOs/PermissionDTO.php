<?php

namespace App\Modules\User\DTOs;

class PermissionDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $guard_name,
        public readonly ?string $display_name = null,
        public readonly ?string $description = null,
        public readonly ?string $module = null,
        public readonly bool $is_active = true,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
        public readonly int $users_count = 0,
        public readonly int $roles_count = 0
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            guard_name: $data['guard_name'],
            display_name: $data['display_name'] ?? null,
            description: $data['description'] ?? null,
            module: $data['module'] ?? null,
            is_active: $data['is_active'] ?? true,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            users_count: $data['users_count'] ?? 0,
            roles_count: $data['roles_count'] ?? 0
        );
    }

    public static function fromModel($permission): self
    {
        return new self(
            id: $permission->id,
            name: $permission->name,
            guard_name: $permission->guard_name,
            display_name: $permission->display_name,
            description: $permission->description,
            module: $permission->module,
            is_active: $permission->is_active ?? true,
            created_at: $permission->created_at?->toISOString(),
            updated_at: $permission->updated_at?->toISOString(),
            users_count: $permission->users_count ?? 0,
            roles_count: $permission->roles_count ?? 0
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'display_name' => $this->display_name,
            'description' => $this->description,
            'module' => $this->module,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'users_count' => $this->users_count,
            'roles_count' => $this->roles_count,
        ];
    }

    /**
     * İznin görünen adını döndür
     */
    public function getDisplayName(): string
    {
        return $this->display_name ?? $this->name;
    }

    /**
     * İznin modülünü döndür
     */
    public function getModule(): string
    {
        return $this->module ?? 'Genel';
    }

    /**
     * İznin modül etiketini döndür
     */
    public function getModuleLabel(): string
    {
        $labels = [
            'user' => 'Kullanıcı Yönetimi',
            'role' => 'Rol Yönetimi',
            'permission' => 'İzin Yönetimi',
            'dashboard' => 'Dashboard',
            'settings' => 'Ayarlar',
            'mail' => 'Mail Yönetimi',
            'activity' => 'Aktivite Logları',
        ];

        return $labels[$this->module] ?? ucfirst($this->module ?? 'Genel');
    }

    /**
     * İznin sistem izni olup olmadığını kontrol et
     */
    public function isSystemPermission(): bool
    {
        return in_array($this->name, [
            'view-users', 'create-users', 'update-users', 'delete-users',
            'view-roles', 'create-roles', 'update-roles', 'delete-roles',
            'view-permissions', 'create-permissions', 'update-permissions', 'delete-permissions'
        ]);
    }

    /**
     * İznin silinebilir olup olmadığını kontrol et
     */
    public function isDeletable(): bool
    {
        return !$this->isSystemPermission() && $this->users_count === 0 && $this->roles_count === 0;
    }

    /**
     * İznin aktif olup olmadığını kontrol et
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * İznin kullanıcı sayısını döndür
     */
    public function getUserCount(): int
    {
        return $this->users_count;
    }

    /**
     * İznin rol sayısını döndür
     */
    public function getRoleCount(): int
    {
        return $this->roles_count;
    }

    /**
     * İznin toplam kullanım sayısını döndür
     */
    public function getTotalUsage(): int
    {
        return $this->users_count + $this->roles_count;
    }

    /**
     * İznin açıklamasını döndür
     */
    public function getDescription(): string
    {
        return $this->description ?? 'Açıklama yok';
    }
} 