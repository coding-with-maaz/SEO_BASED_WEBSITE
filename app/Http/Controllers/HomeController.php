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

    public function index(Request $request)
    {
        $page = (int)$request->get('page', 1);
        $perPage = 20; // Items per page

        // Get all custom content (published only) - will be shown on first pages
        $allCustomContent = Content::published()
            ->orderBy('sort_order', 'asc')
            ->orderBy('release_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Convert custom content to array format
        $customContentArray = [];
        foreach ($allCustomContent as $content) {
            $customContentArray[] = [
                'type' => in_array($content->type, ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show']) ? 'tv' : 'movie',
                'id' => $content->slug ?? ('custom_' . $content->id),
                'slug' => $content->slug,
                'title' => $content->title,
                'date' => $content->release_date ? $content->release_date->format('Y-m-d') : null,
                'rating' => $content->rating ?? 0,
                'backdrop' => $content->backdrop_path ?? $content->poster_path ?? null,
                'poster' => $content->poster_path ?? null,
                'overview' => $content->description ?? '',
                'is_custom' => true,
                'content_id' => $content->id,
                'content_type' => $content->type,
                'dubbing_language' => $content->dubbing_language,
            ];
        }

        // Get TMDB content for current page
        $popularMovies = $this->tmdb->getPopularMovies($page);
        $popularTvShows = $this->tmdb->getPopularTvShows($page);
        $tmdbMovies = $popularMovies['results'] ?? [];
        $tmdbTvShows = $popularTvShows['results'] ?? [];
        $tmdbTotalPages = max($popularMovies['total_pages'] ?? 1, $popularTvShows['total_pages'] ?? 1);

        // Combine content for current page
        $allContent = [];

        // Include custom content only on page 1
        if ($page == 1) {
            $allContent = $customContentArray;
        }

        // Add TMDB movies for current page
        foreach ($tmdbMovies as $movie) {
            $allContent[] = [
                'type' => 'movie',
                'id' => $movie['id'],
                'title' => $movie['title'] ?? 'Unknown',
                'date' => $movie['release_date'] ?? null,
                'rating' => $movie['vote_average'] ?? 0,
                'backdrop' => $movie['backdrop_path'] ?? $movie['poster_path'] ?? null,
                'poster' => $movie['poster_path'] ?? null,
                'overview' => $movie['overview'] ?? '',
                'is_custom' => false,
            ];
        }

        // Add TMDB TV shows for current page
        foreach ($tmdbTvShows as $tvShow) {
            $allContent[] = [
                'type' => 'tv',
                'id' => $tvShow['id'],
                'title' => $tvShow['name'] ?? 'Unknown',
                'date' => $tvShow['first_air_date'] ?? null,
                'rating' => $tvShow['vote_average'] ?? 0,
                'backdrop' => $tvShow['backdrop_path'] ?? $tvShow['poster_path'] ?? null,
                'poster' => $tvShow['poster_path'] ?? null,
                'overview' => $tvShow['overview'] ?? '',
                'is_custom' => false,
            ];
        }

        // Sort by date (newest first), custom content first if same date
        usort($allContent, function($a, $b) {
            $dateA = $a['date'] ?? '1970-01-01';
            $dateB = $b['date'] ?? '1970-01-01';
            if ($dateA === $dateB) {
                return ($b['is_custom'] ?? false) <=> ($a['is_custom'] ?? false);
            }
            return strcmp($dateB, $dateA);
        });

        // Limit to items per page (for page 1, we might have more items if custom + TMDB exceed perPage)
        $allContent = array_slice($allContent, 0, $perPage);

        // Calculate total pages (custom content pages + TMDB pages)
        $customContentCount = count($customContentArray);
        $customPages = max(1, ceil($customContentCount / $perPage));
        $totalPages = $customPages + $tmdbTotalPages;

        // Get top rated movies for sidebar
        $topRatedMovies = $this->tmdb->getTopRatedMovies(1);

        return view('home', [
            'allContent' => $allContent,
            'currentPage' => $page,
            'totalPages' => max(1, $totalPages),
            'topRatedMovies' => $topRatedMovies['results'] ?? [],
        ]);
    }
}
