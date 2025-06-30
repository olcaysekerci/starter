<?php

namespace App\Modules\User\DTOs;

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
} 