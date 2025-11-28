<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Services\TmdbService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function index()
    {
        $popularMovies = $this->tmdb->getPopularMovies(1);
        $topRatedMovies = $this->tmdb->getTopRatedMovies(1);
        $nowPlayingMovies = $this->tmdb->getNowPlayingMovies(1);
        $popularTvShows = $this->tmdb->getPopularTvShows(1);
        $topRatedTvShows = $this->tmdb->getTopRatedTvShows(1);

        // Get custom content (published only)
        $customContent = Content::published()
            ->orderBy('sort_order', 'asc')
            ->orderBy('release_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('home', [
            'popularMovies' => $popularMovies['results'] ?? [],
            'topRatedMovies' => $topRatedMovies['results'] ?? [],
            'nowPlayingMovies' => $nowPlayingMovies['results'] ?? [],
            'popularTvShows' => $popularTvShows['results'] ?? [],
            'topRatedTvShows' => $topRatedTvShows['results'] ?? [],
            'customContent' => $customContent,
        ]);
    }
}
