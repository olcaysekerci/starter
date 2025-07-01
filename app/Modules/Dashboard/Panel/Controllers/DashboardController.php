<?php

namespace App\Modules\Dashboard\Panel\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Dashboard\Services\DashboardService;
use App\Modules\Dashboard\Exceptions\DashboardException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    /**
     * Ana dashboard sayfası
     */
    public function index(): Response
    {
        try {
            $dashboardData = $this->dashboardService->getDashboardData();
            
            return Inertia::render('Dashboard/Panel/Index', [
                'dashboard' => $dashboardData
            ]);
        } catch (DashboardException $e) {
            Log::error('Dashboard yüklenirken hata oluştu', [
                'error' => $e->getMessage()
            ]);
            
            return Inertia::render('Dashboard/Panel/Index', [
                'dashboard' => [],
                'error' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard yüklenirken beklenmeyen hata oluştu', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Inertia::render('Dashboard/Panel/Index', [
                'dashboard' => [],
                'error' => 'Dashboard yüklenirken bir hata oluştu.'
            ]);
        }
    }

    /**
     * Kullanıcı tercihlerini güncelle
     */
    public function updatePreferences(Request $request): RedirectResponse
    {
        $request->validate([
            'preferences' => 'required|array',
        ]);

        try {
            $userId = auth()->id();
            $preferences = $request->preferences;
            
            $this->dashboardService->updateUserPreferences($userId, $preferences);
            
            return back()->with('success', 'Dashboard tercihleri başarıyla güncellendi.');
        } catch (DashboardException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Dashboard tercihleri güncellenirken beklenmeyen hata oluştu', [
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Dashboard tercihleri güncellenirken bir hata oluştu.');
        }
    }

    /**
     * Dashboard istatistiklerini getir (AJAX)
     */
    public function getStatistics(): \Illuminate\Http\JsonResponse
    {
        try {
            $statistics = $this->dashboardService->getStatistics();
            
            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        } catch (DashboardException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('Dashboard istatistikleri yüklenirken beklenmeyen hata oluştu', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'İstatistikler yüklenirken bir hata oluştu.'
            ], 500);
        }
    }

    /**
     * Widget verilerini getir (AJAX)
     */
    public function getWidgetData(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'widget' => 'required|string',
        ]);

        try {
            $widgetName = $request->widget;
            $widgets = $this->dashboardService->getWidgets();
            
            if (!isset($widgets[$widgetName])) {
                throw DashboardException::widgetLoadFailed($widgetName);
            }
            
            return response()->json([
                'success' => true,
                'data' => $widgets[$widgetName]
            ]);
        } catch (DashboardException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('Widget verisi yüklenirken beklenmeyen hata oluştu', [
                'widget' => $request->widget,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Widget verisi yüklenirken bir hata oluştu.'
            ], 500);
        }
    }

    /**
     * Grafik verilerini getir (AJAX)
     */
    public function getChartData(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'chart' => 'required|string',
        ]);

        try {
            $chartName = $request->chart;
            $charts = $this->dashboardService->getCharts();
            
            if (!isset($charts[$chartName])) {
                throw DashboardException::chartDataLoadFailed($chartName);
            }
            
            return response()->json([
                'success' => true,
                'data' => $charts[$chartName]
            ]);
        } catch (DashboardException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('Grafik verisi yüklenirken beklenmeyen hata oluştu', [
                'chart' => $request->chart,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Grafik verisi yüklenirken bir hata oluştu.'
            ], 500);
        }
    }
} 