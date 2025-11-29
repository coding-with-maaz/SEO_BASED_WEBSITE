<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Services\TmdbService;
use Illuminate\Http\Request;

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

    public function completed(Request $request)
    {
        $page = $request->get('page', 1);

        // Get custom completed TV shows/series
        $customCompleted = Content::published()
            ->whereIn('type', ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show'])
            ->where('series_status', 'completed')
            ->orderBy('sort_order', 'asc')
            ->orderBy('end_date', 'desc')
            ->orderBy('release_date', 'desc')
            ->get();

        // Get top rated TV shows for sidebar
        $topRatedTvShows = $this->tmdb->getTopRatedTvShows(1);

        return view('pages.completed', [
            'customCompleted' => $customCompleted,
            'topRatedTvShows' => $topRatedTvShows['results'] ?? [],
        ]);
    }
}

