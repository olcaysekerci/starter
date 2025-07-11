<?php

use App\Modules\User\Panel\Controllers\UserController;
use App\Modules\User\Panel\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('panel')->name('panel.')->group(function () {
        // User routes
        Route::resource('users', UserController::class);
        Route::get('users/search', [UserController::class, 'search'])->name('users.search');
        Route::get('users/{user}/send-email', [UserController::class, 'sendEmail'])->name('users.send-email');
        
        // Role routes (includes permission management)
        Route::resource('roles', RoleController::class);
        Route::get('roles/search', [RoleController::class, 'search'])->name('roles.search');
        Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
    });
}); 