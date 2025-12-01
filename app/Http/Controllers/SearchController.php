<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Services\TmdbService;
use App\Services\SeoService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $tmdb;
    protected $seo;

    public function __construct(TmdbService $tmdb, SeoService $seo)
    {
        $this->tmdb = $tmdb;
        $this->seo = $seo;
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $page = $request->get('page', 1);

        // Get popular content based on views for sidebar
        $popularContent = Content::published()
            ->orderBy('views', 'desc')
            ->orderBy('release_date', 'desc')
            ->take(5)
            ->get();

        if (!$query) {
            return view('search.index', [
                'movies' => [],
                'tvShows' => [],
                'query' => '',
                'popularContent' => $popularContent,
                'seo' => $this->seo->forSearch(),
            ]);
        }

        $results = $this->tmdb->search($query, $page);

        return view('search.index', [
            'movies' => $results['movies']['results'] ?? [],
            'tvShows' => $results['tv_shows']['results'] ?? [],
            'query' => $query,
            'currentPage' => $results['movies']['page'] ?? 1,
            'totalPages' => $results['movies']['total_pages'] ?? 1,
            'popularContent' => $popularContent,
            'seo' => $this->seo->forSearch($query),
        ]);
    }
}
