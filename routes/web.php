<?php

use Illuminate\Support\Facades\Route;
use RyanBadger\LaravelAdmin\Controllers\AdminController;

// Ensuring all admin routes are under a specific namespace and middleware group
Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::get('/{slug}', [AdminController::class, 'index'])->name('index');
    Route::get('/{slug}/create', [AdminController::class, 'create'])->name('create');
    Route::post('/{slug}', [AdminController::class, 'store'])->name('store');
    Route::get('/{slug}/{id}/edit', [AdminController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
    Route::put('/{slug}/{id}', [AdminController::class, 'update'])->name('update')->where('id', '[0-9]+');
    Route::delete('/{slug}/{id}', [AdminController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
});
