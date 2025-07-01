<?php

use App\Modules\Settings\Panel\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('panel')->name('panel.')->group(function () {
        Route::prefix('settings')->name('settings.')->group(function () {
            // Ana ayarlar sayfası
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            
            // Uygulama ayarları
            Route::prefix('app')->name('app.')->group(function () {
                Route::get('/', [SettingsController::class, 'app'])->name('index');
                Route::post('/', [SettingsController::class, 'updateApp'])->name('update');
            });
            
            // Mail ayarları
            Route::prefix('mail')->name('mail.')->group(function () {
                Route::get('/', [SettingsController::class, 'mail'])->name('index');
                Route::post('/', [SettingsController::class, 'updateMail'])->name('update');
                Route::post('/test', [SettingsController::class, 'testMail'])->name('test');
            });
            
            // Eski route'lar (geriye uyumluluk için)
            Route::get('/profile', [SettingsController::class, 'profile'])->name('profile');
            Route::get('/security', [SettingsController::class, 'security'])->name('security');
        });
    });
}); 