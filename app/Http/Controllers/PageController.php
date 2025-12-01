<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Services\TmdbService;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PageController extends Controller
{
    protected $tmdb;
    protected $seo;

    public function __construct(TmdbService $tmdb, SeoService $seo)
    {
        $this->tmdb = $tmdb;
        $this->seo = $seo;
    }

    public function dmca()
    {
        return view('pages.dmca', [
            'seo' => $this->seo->forPage('dmca'),
        ]);
    }

    public function about()
    {
        return view('pages.about', [
            'seo' => $this->seo->forPage('about'),
        ]);
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

        // Get popular content based on views for sidebar
        $popularContent = Content::published()
            ->orderBy('views', 'desc')
            ->orderBy('release_date', 'desc')
            ->take(5)
            ->get();

        return view('pages.completed', [
            'customCompleted' => $customCompleted,
            'popularContent' => $popularContent,
            'seo' => $this->seo->forPage('completed'),
        ]);
    }

    public function upcoming(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = 20; // Items per page

        // Check if columns exist
        $hasSeriesStatus = Schema::hasColumn('contents', 'series_status');

        // Get upcoming movies and TV shows from database only
        // Show content where series_status = 'upcoming' OR status = 'upcoming'
        // Exclude drafts
        $query = Content::where('status', '!=', 'draft')
            ->where(function($q) use ($hasSeriesStatus) {
                if ($hasSeriesStatus) {
                    // Show content with series_status = 'upcoming' OR status = 'upcoming'
                    $q->where('series_status', 'upcoming')
                      ->orWhere('status', 'upcoming');
                } else {
                    // If series_status column doesn't exist, just check status
                    $q->where('status', 'upcoming');
                }
            })
            ->orderBy('sort_order', 'asc')
            ->orderBy('release_date', 'asc'); // Order by release date ascending (soonest first)
        
        // Get total count for pagination
        $totalItems = $query->count();
        $totalPages = max(1, ceil($totalItems / $perPage));

        // Paginate upcoming content
        $customUpcoming = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Get popular content based on views for sidebar
        $popularContent = Content::published()
            ->orderBy('views', 'desc')
            ->orderBy('release_date', 'desc')
            ->take(5)
            ->get();

        return view('pages.upcoming', [
            'customUpcoming' => $customUpcoming,
            'popularContent' => $popularContent,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'seo' => $this->seo->forPage('upcoming'),
        ]);
    }
}

