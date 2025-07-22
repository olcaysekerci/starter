<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Modül route'larını en başta yükle
\App\Helpers\ModuleLoader::loadAllModules();

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard route'u - Fortify'un home path'i için
    Route::get('/dashboard', function () {
        return redirect()->route('panel.dashboard');
    })->name('dashboard');
    
    // Tüm modülleri otomatik yükle
});
