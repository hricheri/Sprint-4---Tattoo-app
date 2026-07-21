<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtistProfileController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::get('/artist-profile/create', [ArtistProfileController::class, 'create'])->name('artist-profile.create');
    Route::post('/artist-profile', [ArtistProfileController::class, 'store'])->name('artist-profile.store');
    Route::get('/artist-profile', [ArtistProfileController::class, 'show'])->name('artist-profile.show');
    Route::get('/artist-profile/edit', [ArtistProfileController::class, 'edit'])->name('artist-profile.edit');
    Route::put('/artist-profile', [ArtistProfileController::class, 'update'])->name('artist-profile.update');
    Route::delete('/artist-profile', [ArtistProfileController::class, 'destroy'])->name('artist-profile.destroy');
    Route::get('/artist-profile/preview', [ArtistProfileController::class, 'preview'])->name('artist-profile.preview');
    });

require __DIR__.'/auth.php';
