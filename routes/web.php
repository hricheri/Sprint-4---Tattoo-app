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

    Route::get('/artists/{artist}', [ArtistProfileController::class, 'publicShow'])->name('artists.show');
    Route::get('/artists/{artist}/availability', [ArtistProfileController::class, 'availability'])->name('artists.availability');

    Route::get('/explore', [ArtistBrowseController::class, 'explore'])->name('explore');
    Route::post('/likes', [ArtistBrowseController::class, 'like'])->name('likes.store');
    Route::get('/favorites', [ArtistBrowseController::class, 'favorites'])->name('favorites');
    Route::post('/explore/dismiss', [ArtistBrowseController::class, 'dismiss'])->name('explore.dismiss');

    Route::get('/swaps', [SwapController::class, 'index'])->name('swaps.index');
    Route::post('/swaps/start/{artist}', [SwapController::class, 'start'])->name('swaps.start');
    Route::post('/swaps/{swap}/confirm-dates', [SwapController::class, 'confirmDates'])->name('swaps.confirm-dates');
    Route::post('/swaps/{swap}/reject', [SwapController::class, 'reject'])->name('swaps.reject');
    Route::post('/swaps/{swap}/cancel', [SwapController::class, 'cancel'])->name('swaps.cancel');
    Route::post('/swaps/{swap}/dismiss-cancellation', [SwapController::class, 'dismissCancellation'])->name('swaps.dismiss-cancellation');
    Route::post('/swaps/{swap}/search-after-cancellation', [SwapController::class, 'searchAfterCancellation'])->name('swaps.search-after-cancellation');
    Route::post('/swaps/{swap}/promo-sent', [SwapController::class, 'markPromoSent'])->name('swaps.promo-sent');

    Route::view('/availability', 'availability')->name('availability');
});

require __DIR__.'/auth.php';