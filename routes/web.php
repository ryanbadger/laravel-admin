<?php

use Illuminate\Support\Facades\Route;
use RyanBadger\LaravelAdmin\Controllers\AdminController;
use RyanBadger\LaravelAdmin\Controllers\MediaController;
use RyanBadger\LaravelAdmin\Controllers\FormBuilderController;

// Ensuring all admin routes are under a specific namespace and middleware group
Route::middleware(['web', 'auth', 'cms.access'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/dashboard'); // Redirect /admin to /admin/dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/upload', [MediaController::class, 'upload'])->name('upload');
    Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

    // Form Management Routes
    Route::get('forms/field-template', [FormBuilderController::class, 'fieldTemplate'])->name('forms.field-template');
    Route::get('forms', [FormBuilderController::class, 'index'])->name('forms.index');
    Route::get('forms/create', [FormBuilderController::class, 'create'])->name('forms.create');
    Route::post('forms', [FormBuilderController::class, 'store'])->name('forms.store');
    Route::get('forms/{id}/edit', [FormBuilderController::class, 'edit'])->name('forms.edit')->where('id', '[0-9]+');
    Route::put('forms/{id}', [FormBuilderController::class, 'update'])->name('forms.update')->where('id', '[0-9]+');
    Route::delete('forms/{id}', [FormBuilderController::class, 'destroy'])->name('forms.destroy')->where('id', '[0-9]+');

    // Keep this as the last route to handle dynamic resource routes
    Route::get('/{slug}', [AdminController::class, 'index'])->name('index');
    Route::get('/{slug}/create', [AdminController::class, 'create'])->name('create');
    Route::post('/{slug}', [AdminController::class, 'store'])->name('store');
    Route::get('/{slug}/{id}/edit', [AdminController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
    Route::put('/{slug}/{id}', [AdminController::class, 'update'])->name('update')->where('id', '[0-9]+');
    Route::delete('/{slug}/{id}', [AdminController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
});
