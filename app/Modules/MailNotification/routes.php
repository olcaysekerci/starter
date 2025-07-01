<?php

use Illuminate\Support\Facades\Route;
use App\Modules\MailNotification\Controllers\Panel\MailNotificationController;

Route::prefix('panel')->name('panel.')->middleware(['auth', 'verified'])->group(function () {
    Route::prefix('mail-notifications')->name('mail-notifications.')->group(function () {
        Route::get('/', [MailNotificationController::class, 'index'])->name('index');
        Route::get('/{id}', [MailNotificationController::class, 'show'])->name('show');
        Route::post('/test', [MailNotificationController::class, 'test'])->name('test');
        Route::post('/retry', [MailNotificationController::class, 'retry'])->name('retry');
        Route::post('/retry/{id}', [MailNotificationController::class, 'retrySingle'])->name('retry-single');
        Route::post('/resend/{id}', [MailNotificationController::class, 'resend'])->name('resend');
        Route::post('/cleanup', [MailNotificationController::class, 'cleanup'])->name('cleanup');
    });
}); 