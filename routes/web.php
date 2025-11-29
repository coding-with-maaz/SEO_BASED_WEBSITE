<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\TvShowController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\EpisodeController;
use App\Http\Controllers\Admin\EpisodeServerController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
Route::get('/movies/{slug}', [MovieController::class, 'show'])->name('movies.show');

Route::get('/tv-shows', [TvShowController::class, 'index'])->name('tv-shows.index');
Route::get('/tv-shows/{slug}', [TvShowController::class, 'show'])->name('tv-shows.show');

Route::get('/search', [SearchController::class, 'search'])->name('search');

// Static pages
Route::get('/dmca', [PageController::class, 'dmca'])->name('dmca');
Route::get('/completed', [PageController::class, 'completed'])->name('completed');
Route::get('/upcoming', [PageController::class, 'upcoming'])->name('upcoming');

// Admin routes for custom content management
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('contents', ContentController::class);
    
    // Episode management routes
    Route::prefix('contents/{content}')->group(function () {
        Route::resource('episodes', EpisodeController::class)->except(['show']);
        
        // Episode server routes
        Route::prefix('episodes/{episode}')->group(function () {
            Route::post('servers', [EpisodeServerController::class, 'store'])->name('episodes.servers.store');
            Route::put('servers/{server}', [EpisodeServerController::class, 'update'])->name('episodes.servers.update');
            Route::delete('servers/{server}', [EpisodeServerController::class, 'destroy'])->name('episodes.servers.destroy');
        });
    });
});
