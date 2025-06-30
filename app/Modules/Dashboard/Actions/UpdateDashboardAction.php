<?php

namespace App\Modules\Dashboard\Actions;

use App\Modules\Dashboard\Models\Dashboard;
use App\Modules\Dashboard\Requests\UpdateDashboardRequest;

class UpdateDashboardAction
{
    public function execute(Dashboard $dashboard, UpdateDashboardRequest $request): Dashboard
    {
        $dashboard->update($request->validated());
        
        return $dashboard;
    }
} 