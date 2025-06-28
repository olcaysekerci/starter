<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    public function __construct(
        private User $model
    ) {}

    /**
     * Tüm kullanıcıları sayfalı olarak getir
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    /**
     * ID'ye göre kullanıcı bul
     */
    public function findById(int $id): ?User
    {
        return $this->model->find($id);
    }

    /**
     * Email'e göre kullanıcı bul
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Yeni kullanıcı oluştur
     */
    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    /**
     * Kullanıcı güncelle
     */
    public function update(int $id, array $data): bool
    {
        $user = $this->findById($id);
        
        if (!$user) {
            return false;
        }

        return $user->update($data);
    }

    /**
     * Kullanıcı sil
     */
    public function delete(int $id): bool
    {
        $user = $this->findById($id);
        
        if (!$user) {
            return false;
        }

        return $user->delete();
    }
} 