<?php

use App\Modules\User\Panel\Controllers\UserController;
use App\Modules\User\Panel\Controllers\RoleController;
use App\Modules\User\Panel\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('panel')->name('panel.')->group(function () {
        // User routes
        Route::resource('users', UserController::class);
        Route::get('users/search', [UserController::class, 'search'])->name('users.search');
        
        // Role routes
        Route::resource('roles', RoleController::class);
        Route::get('roles/search', [RoleController::class, 'search'])->name('roles.search');
        
        // Permission routes
        Route::resource('permissions', PermissionController::class);
        Route::get('permissions/search', [PermissionController::class, 'search'])->name('permissions.search');
        Route::get('permissions/module/{module}', [PermissionController::class, 'byModule'])->name('permissions.by-module');
    });
}); 