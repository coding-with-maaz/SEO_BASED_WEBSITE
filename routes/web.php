<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\TvShowController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Admin\ContentController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
Route::get('/movies/{id}', [MovieController::class, 'show'])->name('movies.show');

Route::get('/tv-shows', [TvShowController::class, 'index'])->name('tv-shows.index');
Route::get('/tv-shows/{id}', [TvShowController::class, 'show'])->name('tv-shows.show');

Route::get('/search', [SearchController::class, 'search'])->name('search');

// Admin routes for custom content management
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('contents', ContentController::class);
});
