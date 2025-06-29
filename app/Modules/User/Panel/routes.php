<?php

use App\Modules\User\Panel\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('panel')->name('panel.')->group(function () {
        Route::resource('users', UserController::class);
    });
}); 