<?php

namespace App\Modules\ActivityLog\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Modules\ActivityLog\Services\ActivityLogService;
use App\Modules\ActivityLog\Exceptions\ActivityLogException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {}

    /**
     * Log listesi
     */
    public function index(Request $request): Response
    {
        $filters = $request->only([
            'search',
            'user_id',
            'model_type',
            'event',
            'date_from',
            'date_to'
        ]);

        $logs = $this->activityLogService->getLogs($filters, 15);
        $stats = $this->activityLogService->getStats();
        
        // Logların accessor'larının düzgün çalışması için
        $logs->getCollection()->transform(function ($log) {
            return [
                'id' => $log->id,
                'log_name' => $log->log_name,
                'description' => $log->description,
                'formatted_description' => $log->formatted_description,
                'subject_type' => $log->subject_type,
                'subject_id' => $log->subject_id,
                'causer_type' => $log->causer_type,
                'causer_id' => $log->causer_id,
                'causer' => $log->causer ? [
                    'id' => $log->causer->id,
                    'full_name' => $log->causer->full_name ?? $log->causer->name,
                    'email' => $log->causer->email,
                ] : null,
                'user_name' => $log->user_name,
                'model_name' => $log->model_name,
                'event' => $log->event,
                'resolved_event' => $log->resolved_event,
                'properties' => $log->properties,
                'created_at' => $log->created_at,
                'updated_at' => $log->updated_at,
            ];
        });

        return Inertia::render('ActivityLog/Panel/Index', [
            'logs' => $logs,
            'stats' => $stats,
            'filters' => $filters,
        ]);
    }

    /**
     * Log detayı
     */
    public function show($id): Response
    {
        try {
            $log = $this->activityLogService->getLogById($id);

            return Inertia::render('ActivityLog/Panel/Show', [
                'log' => $log,
            ]);
        } catch (ModelNotFoundException $e) {
            abort(404, 'Log bulunamadı.');
        } catch (ActivityLogException $e) {
            abort(500, $e->getMessage());
        }
    }

    /**
     * Eski logları temizle
     */
    public function cleanup(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'days' => 'required|integer|min:1|max:365',
            ]);

            $deletedCount = $this->activityLogService->cleanupOldLogs($request->days);

            return back()->with('success', "{$deletedCount} adet eski log başarıyla temizlendi.");
        } catch (ActivityLogException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Log temizleme işlemi başarısız oldu.');
        }
    }

    /**
     * Toplu temizleme
     */
    public function bulkCleanup(Request $request)
    {
        $request->validate([
            'criteria' => 'required|array',
            'criteria.days' => 'nullable|integer|min:1|max:365',
            'criteria.user_id' => 'nullable|integer|exists:users,id',
            'criteria.model_type' => 'nullable|string',
            'criteria.event' => 'nullable|string',
        ]);

        $deletedCount = $this->activityLogService->bulkCleanup($request->criteria);

        return back()->with('success', "{$deletedCount} adet log başarıyla temizlendi.");
    }

    /**
     * Analitik veriler
     */
    public function analytics(Request $request)
    {
        $filters = $request->only([
            'user_id',
            'model_type',
            'event',
            'date_from',
            'date_to'
        ]);

        $analytics = $this->activityLogService->getAnalytics($filters);

        return response()->json($analytics);
    }
} 