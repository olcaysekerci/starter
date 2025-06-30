<?php

use App\Modules\Settings\Panel\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('panel')->name('panel.')->group(function () {
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::get('/profile', [SettingsController::class, 'profile'])->name('profile');
            Route::get('/security', [SettingsController::class, 'security'])->name('security');
        });
    });
}); 