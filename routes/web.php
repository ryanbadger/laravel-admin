<?php

use Illuminate\Support\Facades\Route;
use RyanBadger\LaravelAdmin\Controllers\AdminController;

// Ensuring all admin routes are under a specific namespace and middleware group
Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->namespace('RyanBadger\LaravelAdmin\Controllers')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::get('/{model}', [AdminController::class, 'index'])->name('index');
    Route::get('/{model}/create', [AdminController::class, 'create'])->name('create');
    Route::post('/{model}', [AdminController::class, 'store'])->name('store');
    Route::get('/{model}/{id}/edit', [AdminController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
    Route::put('/{model}/{id}', [AdminController::class, 'update'])->name('update')->where('id', '[0-9]+');
    Route::delete('/{model}/{id}', [AdminController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
});

