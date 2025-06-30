<?php

use App\Modules\Dashboard\Panel\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
 
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('panel')->name('panel.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
}); 