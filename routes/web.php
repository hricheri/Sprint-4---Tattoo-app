<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtistProfileController;
use App\Http\Controllers\ArtistBrowseController;
use App\Http\Controllers\SwapController;

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

    Route::get('/explore', [ArtistBrowseController::class, 'explore'])->name('explore');
    Route::post('/likes', [ArtistBrowseController::class, 'like'])->name('likes.store');
    Route::get('/favorites', [ArtistBrowseController::class, 'favorites'])->name('favorites');
    Route::post('/explore/dismiss', [ArtistBrowseController::class, 'dismiss'])->name('explore.dismiss');

    Route::get('/swaps', [SwapController::class, 'index'])->name('swaps.index');
    Route::get('/swaps/create/{artist}', [SwapController::class, 'create'])->name('swaps.create');
    Route::post('/swaps', [SwapController::class, 'store'])->name('swaps.store');
    Route::post('/swaps/{swap}/accept', [SwapController::class, 'accept'])->name('swaps.accept');
    Route::post('/swaps/{swap}/reject', [SwapController::class, 'reject'])->name('swaps.reject');

    Route::view('/availability', 'availability')->name('availability');
});

require __DIR__.'/auth.php';