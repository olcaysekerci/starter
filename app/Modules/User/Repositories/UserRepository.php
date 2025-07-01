<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function __construct(
        private User $model
    ) {}

    /**
     * Tüm kullanıcıları getir (sayfalama olmadan)
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * Tüm kullanıcıları sayfalı olarak getir
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['roles', 'permissions'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * ID'ye göre kullanıcı bul
     */
    public function findById(int $id): ?User
    {
        return $this->model
            ->with(['roles', 'permissions'])
            ->find($id);
    }

    /**
     * Email'e göre kullanıcı bul
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Kullanıcı arama
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['roles', 'permissions'])
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Yeni kullanıcı oluştur
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    /**
     * Kullanıcı güncelle
     */
    public function update(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $user = $this->findById($id);
            
            if (!$user) {
                return false;
            }

            return $user->update($data);
        });
    }

    /**
     * Kullanıcı sil
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = $this->findById($id);
            
            if (!$user) {
                return false;
            }

            return $user->delete();
        });
    }

    /**
     * Model instance'ını getir
     */
    public function getModel(): User
    {
        return $this->model;
    }
} 