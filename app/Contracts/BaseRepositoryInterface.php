<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * Get all records
     */
    public function getAll(): Collection;

    /**
     * Get paginated records
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find record by ID
     */
    public function findById(int $id): ?Model;

    /**
     * Find record by ID or fail
     */
    public function findByIdOrFail(int $id): Model;

    /**
     * Create new record
     */
    public function create(array $data): Model;

    /**
     * Update existing record
     */
    public function update(Model $model, array $data): Model;

    /**
     * Delete record
     */
    public function delete(Model $model): bool;

    /**
     * Get model instance
     */
    public function getModel(): Model;
}