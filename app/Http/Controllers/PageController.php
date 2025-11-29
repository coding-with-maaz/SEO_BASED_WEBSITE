<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PageController extends Controller
{
    protected $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function dmca()
    {
        return view('pages.dmca');
    }

    public function about()
    {
        return view('pages.about');
    }

    public function completed(Request $request)
    {
        $page = $request->get('page', 1);

        // Check if columns exist
        $hasSeriesStatus = Schema::hasColumn('contents', 'series_status');
        $hasEndDate = Schema::hasColumn('contents', 'end_date');

        // Get custom completed TV shows/series
        $query = Content::published()
            ->whereIn('type', ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show']);
        
        // Only filter by series_status if the column exists
        if ($hasSeriesStatus) {
            $query->where('series_status', 'completed');
        }
        
        // Build ordering
        $query->orderBy('sort_order', 'asc');
        
        // Only order by end_date if the column exists
        if ($hasEndDate) {
            $query->orderBy('end_date', 'desc');
        }
        
        $query->orderBy('release_date', 'desc');
        
        $customCompleted = $query->get();

        // Get top rated TV shows for sidebar
        $topRatedTvShows = $this->tmdb->getTopRatedTvShows(1);

        return view('pages.completed', [
            'customCompleted' => $customCompleted,
            'topRatedTvShows' => $topRatedTvShows['results'] ?? [],
        ]);
    }

    public function upcoming(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = 20; // Items per page

        // Check if columns exist
        $hasSeriesStatus = Schema::hasColumn('contents', 'series_status');

        // Get upcoming movies and TV shows with pagination
        $query = Content::published()
            ->where(function($q) use ($hasSeriesStatus) {
                // Filter by series_status = 'upcoming' if column exists
                if ($hasSeriesStatus) {
                    $q->where('series_status', 'upcoming')
                      ->orWhere('release_date', '>', now());
                } else {
                    // If series_status column doesn't exist, just filter by future release dates
                    $q->where('release_date', '>', now());
                }
            })
            ->orderBy('sort_order', 'asc')
            ->orderBy('release_date', 'asc'); // Order by release date ascending (soonest first)
        
        $customUpcomingPaginated = $query->paginate($perPage, ['*'], 'custom_page', $page);
        $customUpcoming = $customUpcomingPaginated->items();
        $customTotalPages = $customUpcomingPaginated->lastPage();
        $customCurrentPage = $customUpcomingPaginated->currentPage();

        // Get upcoming movies from TMDB
        $upcomingMovies = $this->tmdb->getUpcomingMovies($page);
        $tmdbMovies = $upcomingMovies['results'] ?? [];
        $tmdbTotalPages = $upcomingMovies['total_pages'] ?? 1;
        $tmdbCurrentPage = $upcomingMovies['page'] ?? 1;

        // Get top rated TV shows for sidebar
        $topRatedTvShows = $this->tmdb->getTopRatedTvShows(1);

        // Use custom pagination if available, otherwise use TMDB pagination
        $totalPages = $customTotalPages > 0 ? $customTotalPages : $tmdbTotalPages;
        $currentPage = $page;

        return view('pages.upcoming', [
            'customUpcoming' => $customUpcoming,
            'upcomingMovies' => $tmdbMovies,
            'topRatedTvShows' => $topRatedTvShows['results'] ?? [],
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }
}

