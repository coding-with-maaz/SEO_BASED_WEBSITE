<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\TvShowController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CastController as PublicCastController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EpisodeController;
use App\Http\Controllers\Admin\EpisodeServerController;
use App\Http\Controllers\Admin\ServerController;
use App\Http\Controllers\Admin\CastController;
use App\Http\Controllers\Admin\PageSeoController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\Admin\SeoToolsController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// SEO routes (must be before other routes for proper matching)
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');

// Sitemap routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap/index.xml', [SitemapController::class, 'sitemapIndex'])->name('sitemap.sitemap-index');
Route::get('/sitemap/static.xml', [SitemapController::class, 'static'])->name('sitemap.static');
Route::get('/sitemap/movies.xml', [SitemapController::class, 'movies'])->name('sitemap.movies');
Route::get('/sitemap/tv-shows.xml', [SitemapController::class, 'tvShows'])->name('sitemap.tv-shows');
Route::get('/sitemap/cast.xml', [SitemapController::class, 'cast'])->name('sitemap.cast');
Route::get('/sitemap/episodes.xml', [SitemapController::class, 'episodes'])->name('sitemap.episodes');

Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
Route::get('/movies/{slug}', [MovieController::class, 'show'])->name('movies.show');

Route::get('/tv-shows', [TvShowController::class, 'index'])->name('tv-shows.index');
Route::get('/tv-shows/{slug}', [TvShowController::class, 'show'])->name('tv-shows.show');

Route::get('/search', [SearchController::class, 'search'])->name('search');

// Cast pages
Route::get('/cast', [PublicCastController::class, 'index'])->name('cast.index');
Route::get('/cast/{slug}', [PublicCastController::class, 'show'])->name('cast.show');

// Static pages
Route::get('/dmca', [PageController::class, 'dmca'])->name('dmca');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/completed', [PageController::class, 'completed'])->name('completed');
Route::get('/upcoming', [PageController::class, 'upcoming'])->name('upcoming');
Route::get('/how-to-download', [PageController::class, 'howToDownload'])->name('how-to-download');

// Comment routes
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
Route::get('/comments/{contentId}', [CommentController::class, 'getComments'])->name('comments.get');
Route::post('/comments/{id}/like', [CommentController::class, 'like'])->name('comments.like');
Route::post('/comments/{id}/dislike', [CommentController::class, 'dislike'])->name('comments.dislike');

// Share tracking routes
Route::post('/share/track', [ShareController::class, 'track'])->name('share.track');
Route::get('/share/stats', [ShareController::class, 'stats'])->name('share.stats');

// Admin authentication routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Public login routes
    Route::get('login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
    
    // Protected logout route
    Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');
});

// Admin routes for custom content management (protected)
Route::prefix('admin')->name('admin.')->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('contents', ContentController::class);
    
    // Server Management routes (for movies)
    Route::get('servers', [ServerController::class, 'index'])->name('servers.index');
    Route::get('servers/{content}', [ServerController::class, 'show'])->name('servers.show');
    Route::post('servers/{content}', [ServerController::class, 'store'])->name('servers.store');
    Route::put('servers/{content}', [ServerController::class, 'update'])->name('servers.update');
    Route::delete('servers/{content}', [ServerController::class, 'destroy'])->name('servers.destroy');
    
    // TMDB search and import routes
    Route::get('contents/tmdb/search', [ContentController::class, 'searchTmdb'])->name('contents.tmdb.search');
    Route::get('contents/tmdb/details', [ContentController::class, 'getTmdbDetails'])->name('contents.tmdb.details');
    Route::get('contents/tmdb/import', function() {
        return redirect()->route('admin.contents.create')
            ->with('error', 'Invalid request. Please use the import form to import content from TMDB.');
    });
    Route::post('contents/tmdb/import', [ContentController::class, 'importFromTmdb'])->name('contents.tmdb.import');
    Route::post('contents/article', [ContentController::class, 'storeArticleContent'])->name('contents.article.store');
    
        // Content server management routes
        Route::prefix('contents/{content}')->group(function () {
            Route::post('servers', [ContentController::class, 'addServer'])->name('contents.servers.store');
            Route::put('servers/update', [ContentController::class, 'updateServer'])->name('contents.servers.update');
            Route::delete('servers/delete', [ContentController::class, 'deleteServer'])->name('contents.servers.destroy');
            
            // Cast management routes (using ID for route model binding)
            Route::get('cast', [CastController::class, 'index'])->name('contents.cast.index');
            Route::get('cast/search', [CastController::class, 'search'])->name('contents.cast.search');
            Route::post('cast', [CastController::class, 'store'])->name('contents.cast.store');
            Route::put('cast/{castId}', [CastController::class, 'update'])->name('contents.cast.update');
            Route::delete('cast/{castId}', [CastController::class, 'destroy'])->name('contents.cast.destroy');
            Route::post('cast/reorder', [CastController::class, 'reorder'])->name('contents.cast.reorder');
            
            // Episode management routes
            Route::resource('episodes', EpisodeController::class)->except(['show']);
            
                    // Episode server routes
                    Route::prefix('episodes/{episode}')->group(function () {
                        Route::get('servers', [EpisodeServerController::class, 'index'])->name('episodes.servers.index');
                        Route::post('servers', [EpisodeServerController::class, 'store'])->name('episodes.servers.store');
                        Route::put('servers/{server}', [EpisodeServerController::class, 'update'])->name('episodes.servers.update');
                        Route::delete('servers/{server}', [EpisodeServerController::class, 'destroy'])->name('episodes.servers.destroy');
                    });
        });
    
    // Public Pages SEO Management
    Route::resource('page-seo', PageSeoController::class);
    
    // Comments Management
    Route::get('comments', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::get('comments/{comment}', [AdminCommentController::class, 'show'])->name('comments.show');
    Route::post('comments/{comment}/approve', [AdminCommentController::class, 'approve'])->name('comments.approve');
    Route::post('comments/{comment}/reject', [AdminCommentController::class, 'reject'])->name('comments.reject');
    Route::post('comments/{comment}/spam', [AdminCommentController::class, 'markAsSpam'])->name('comments.spam');
    Route::post('comments/{comment}/pin', [AdminCommentController::class, 'togglePin'])->name('comments.pin');
    Route::post('comments/bulk-action', [AdminCommentController::class, 'bulkAction'])->name('comments.bulk-action');
    Route::delete('comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');
    
    // SEO Tools
    Route::prefix('seo-tools')->name('seo-tools.')->group(function () {
        Route::get('/', [SeoToolsController::class, 'index'])->name('index');
        Route::post('submit-sitemap', [SeoToolsController::class, 'submitSitemap'])->name('submit-sitemap');
        Route::post('check-seo', [SeoToolsController::class, 'checkSeo'])->name('check-seo');
        Route::post('check-links', [SeoToolsController::class, 'checkBrokenLinks'])->name('check-links');
        Route::post('test-rich-snippets', [SeoToolsController::class, 'testRichSnippets'])->name('test-rich-snippets');
        Route::get('submission-history', [SeoToolsController::class, 'submissionHistory'])->name('submission-history');
    });
});
