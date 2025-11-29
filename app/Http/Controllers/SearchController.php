<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $page = $request->get('page', 1);

        // Get top rated movies for sidebar
        $topRatedMovies = $this->tmdb->getTopRatedMovies(1);

        if (!$query) {
            return view('search.index', [
                'movies' => [],
                'tvShows' => [],
                'query' => '',
                'topRatedMovies' => $topRatedMovies['results'] ?? [],
            ]);
        }

        $results = $this->tmdb->search($query, $page);

        return view('search.index', [
            'movies' => $results['movies']['results'] ?? [],
            'tvShows' => $results['tv_shows']['results'] ?? [],
            'query' => $query,
            'currentPage' => $results['movies']['page'] ?? 1,
            'totalPages' => $results['movies']['total_pages'] ?? 1,
            'topRatedMovies' => $topRatedMovies['results'] ?? [],
        ]);
    }
}
