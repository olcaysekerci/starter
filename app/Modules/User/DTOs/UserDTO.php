<?php

namespace App\Modules\User\DTOs;

use Illuminate\Support\Collection;

class UserDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $full_name,
        public readonly string $email,
        public readonly ?string $phone = null,
        public readonly ?string $address = null,
        public readonly bool $is_active = true,
        public readonly ?string $email_verified_at = null,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
        public readonly ?string $last_login = null,
        public readonly ?Collection $roles = null,
        public readonly ?Collection $permissions = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            first_name: $data['first_name'] ?? '',
            last_name: $data['last_name'] ?? '',
            full_name: $data['full_name'] ?? trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
            email: $data['email'],
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            is_active: $data['is_active'] ?? true,
            email_verified_at: $data['email_verified_at'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            last_login: $data['last_login'] ?? null,
            roles: isset($data['roles']) ? collect($data['roles']) : null,
            permissions: isset($data['permissions']) ? collect($data['permissions']) : null,
        );
    }

    public static function fromModel($user): self
    {
        return new self(
            id: $user->id,
            first_name: $user->first_name,
            last_name: $user->last_name,
            full_name: $user->full_name,
            email: $user->email,
            phone: $user->phone,
            address: $user->address,
            is_active: $user->is_active ?? true,
            email_verified_at: $user->email_verified_at?->toISOString(),
            created_at: $user->created_at?->toISOString(),
            updated_at: $user->updated_at?->toISOString(),
            last_login: $user->last_login,
            roles: $user->roles ?? null,
            permissions: $user->permissions ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'last_login' => $this->last_login,
            'roles' => $this->roles?->toArray(),
            'permissions' => $this->permissions?->toArray(),
        ];
    }

    /**
     * Admin paneli için tüm verileri döndür
     */
    public function toAdminArray(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'is_active' => $this->is_active,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'last_login' => $this->last_login,
            'roles' => $this->roles?->toArray(),
            'permissions' => $this->permissions?->toArray(),
        ];
    }

    public function hasRole(string $role): bool
    {
        return $this->roles?->contains('name', $role) ?? false;
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions?->contains('name', $permission) ?? false;
    }

    public function isVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }
} 