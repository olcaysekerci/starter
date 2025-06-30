<?php

use App\Modules\ActivityLog\Controllers\Panel\ActivityLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Panel routes
    Route::prefix('panel')->name('panel.')->group(function () {
        Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
            Route::get('/', [ActivityLogController::class, 'index'])->name('index');
            Route::get('/{id}', [ActivityLogController::class, 'show'])->name('show');
            Route::post('/cleanup', [ActivityLogController::class, 'cleanup'])->name('cleanup');
            Route::post('/bulk-cleanup', [ActivityLogController::class, 'bulkCleanup'])->name('bulk-cleanup');
            Route::get('/analytics', [ActivityLogController::class, 'analytics'])->name('analytics');
        });
    });
}); 