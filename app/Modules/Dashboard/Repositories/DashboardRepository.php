<?php

namespace App\Modules\Dashboard\Repositories;

use App\Modules\Dashboard\Models\Dashboard;

class DashboardRepository
{
    public function __construct(
        private Dashboard $model
    ) {}

    public function getAll()
    {
        return $this->model->all();
    }

    public function findById(int $id)
    {
        return $this->model->find($id);
    }
} 