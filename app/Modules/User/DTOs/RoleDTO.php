<?php

namespace App\Modules\User\DTOs;

use Illuminate\Support\Collection;

class RoleDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $guard_name,
        public readonly ?string $display_name = null,
        public readonly ?string $description = null,
        public readonly bool $is_active = true,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
        public readonly array $permissions = [],
        public readonly int $users_count = 0
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            guard_name: $data['guard_name'],
            display_name: $data['display_name'] ?? null,
            description: $data['description'] ?? null,
            is_active: $data['is_active'] ?? true,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            permissions: $data['permissions'] ?? [],
            users_count: $data['users_count'] ?? 0
        );
    }

    public static function fromModel($role): self
    {
        return new self(
            id: $role->id,
            name: $role->name,
            guard_name: $role->guard_name,
            display_name: $role->display_name,
            description: $role->description,
            is_active: $role->is_active ?? true,
            created_at: $role->created_at?->toISOString(),
            updated_at: $role->updated_at?->toISOString(),
            permissions: $role->permissions?->toArray() ?? [],
            users_count: $role->users_count ?? 0
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
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'permissions' => $this->permissions,
            'users_count' => $this->users_count,
        ];
    }

    /**
     * Rolün görünen adını döndür
     */
    public function getDisplayName(): string
    {
        return $this->display_name ?? $this->name;
    }

    /**
     * Rolün sistem rolü olup olmadığını kontrol et
     */
    public function isSystemRole(): bool
    {
        return in_array($this->name, ['super-admin', 'admin', 'user']);
    }

    /**
     * Rolün silinebilir olup olmadığını kontrol et
     */
    public function isDeletable(): bool
    {
        return !$this->isSystemRole() && $this->users_count === 0;
    }

    /**
     * Rolün belirli bir izne sahip olup olmadığını kontrol et
     */
    public function hasPermission(string $permission): bool
    {
        return collect($this->permissions)->contains('name', $permission);
    }

    /**
     * Rolün izin sayısını döndür
     */
    public function getPermissionCount(): int
    {
        return count($this->permissions);
    }

    /**
     * Rolün aktif olup olmadığını kontrol et
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Rolün kullanıcı sayısını döndür
     */
    public function getUserCount(): int
    {
        return $this->users_count;
    }
} 