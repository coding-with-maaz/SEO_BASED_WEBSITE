<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Services\TmdbService;
use Illuminate\Http\Request;

class TvShowController extends Controller
{
    protected $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $type = $request->get('type', 'popular');

        $tvShows = match($type) {
            'top_rated' => $this->tmdb->getTopRatedTvShows($page),
            default => $this->tmdb->getPopularTvShows($page),
        };

        // Get custom TV show content
        $customTvShows = Content::published()
            ->whereIn('type', ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show'])
            ->orderBy('sort_order', 'asc')
            ->orderBy('release_date', 'desc')
            ->get();

        // Get top rated TV shows for sidebar
        $topRatedTvShows = $this->tmdb->getTopRatedTvShows(1);

        return view('tv-shows.index', [
            'tvShows' => $tvShows['results'] ?? [],
            'customTvShows' => $customTvShows,
            'topRatedTvShows' => $topRatedTvShows['results'] ?? [],
            'currentPage' => $tvShows['page'] ?? 1,
            'totalPages' => $tvShows['total_pages'] ?? 1,
            'type' => $type,
        ]);
    }

    public function show($id)
    {
        // Check if it's a custom content
        if (str_starts_with($id, 'custom_')) {
            $contentId = str_replace('custom_', '', $id);
            $content = Content::with(['episodes.servers'])->findOrFail($contentId);
            
            // Get recommended movies for custom content
            $recommendedMovies = $this->tmdb->getPopularMovies(1);
            
            return view('tv-shows.show', [
                'content' => $content,
                'isCustom' => true,
                'recommendedMovies' => $recommendedMovies['results'] ?? [],
            ]);
        }

        $tvShow = $this->tmdb->getTvShowDetails($id);

        if (!$tvShow) {
            abort(404);
        }

        // Check if there's a custom content linked to this TMDB ID
        $customContent = Content::where('tmdb_id', $id)
            ->whereIn('type', ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show'])
            ->with(['episodes.servers'])
            ->first();

        // Get recommended movies (use popular movies as recommendations)
        $recommendedMovies = $this->tmdb->getPopularMovies(1);

        return view('tv-shows.show', [
            'tvShow' => $tvShow,
            'content' => $customContent,
            'isCustom' => false,
            'recommendedMovies' => $recommendedMovies['results'] ?? [],
        ]);
    }
}
