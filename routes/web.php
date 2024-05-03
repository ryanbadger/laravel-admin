<?php

use Illuminate\Support\Facades\Route;
use RyanBadger\LaravelAdmin\Controllers\AdminController;
use RyanBadger\LaravelAdmin\Controllers\MediaController;


// Ensuring all admin routes are under a specific namespace and middleware group
Route::middleware(['web', 'auth', 'cms.access'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/dashboard'); // Redirect /admin to /admin/dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/upload', [MediaController::class, 'upload'])->name('upload');
    Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
    Route::get('/{slug}', [AdminController::class, 'index'])->name('index');
    Route::get('/{slug}/create', [AdminController::class, 'create'])->name('create');
    Route::post('/{slug}', [AdminController::class, 'store'])->name('store');
    Route::get('/{slug}/{id}/edit', [AdminController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
    Route::put('/{slug}/{id}', [AdminController::class, 'update'])->name('update')->where('id', '[0-9]+');
    Route::delete('/{slug}/{id}', [AdminController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
    // Dynamic AJAX route for relationship searches
    Route::get('/{slug}/relation-search/{field}', [AdminController::class, 'relationSearch'])->name('relation.search');

});
