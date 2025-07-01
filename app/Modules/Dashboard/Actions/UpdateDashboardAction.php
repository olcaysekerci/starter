<?php

namespace App\Modules\Dashboard\Actions;

use App\Modules\Dashboard\Models\Dashboard;
use App\Modules\Dashboard\Repositories\DashboardRepository;
use App\Modules\Dashboard\Exceptions\DashboardException;
use App\Modules\Dashboard\Requests\UpdateDashboardRequest;
use Illuminate\Support\Facades\Log;

class UpdateDashboardAction
{
    public function __construct(
        private DashboardRepository $dashboardRepository
    ) {}

    public function execute(Dashboard $dashboard, UpdateDashboardRequest $request): Dashboard
    {
        try {
            $data = $request->validated();
            
            $success = $this->dashboardRepository->update($dashboard->id, $data);
            
            if (!$success) {
                throw DashboardException::dashboardUpdateFailed($dashboard->id);
            }
            
            Log::info('Dashboard güncellendi', [
                'dashboard_id' => $dashboard->id,
                'updated_fields' => array_keys($data)
            ]);
            
            return $dashboard->fresh();
        } catch (DashboardException $e) {
            Log::error('Dashboard güncellenirken hata oluştu', [
                'dashboard_id' => $dashboard->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Dashboard güncellenirken beklenmeyen hata oluştu', [
                'dashboard_id' => $dashboard->id,
                'error' => $e->getMessage()
            ]);
            throw DashboardException::dashboardUpdateFailed($dashboard->id);
        }
    }
} 