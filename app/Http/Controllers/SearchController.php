<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Services\TmdbService;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $query = $request->get('q', '');
        $page = $request->get('page', 1);
        $genre = $request->get('genre');
        $year = $request->get('year');
        $minRating = $request->get('min_rating');
        $type = $request->get('type');
        $sortBy = $request->get('sort_by', 'relevance');
        $contentType = $request->get('content_type', 'all'); // all, movies, tv_shows

        // Get popular content based on views for sidebar
        $popularContent = Content::published()
            ->orderBy('views', 'desc')
            ->orderBy('release_date', 'desc')
            ->take(5)
            ->get();

        // Get all unique genres from database
        $allGenres = $this->getAllGenres();
        
        // Get years range
        $years = $this->getYearsRange();

        // Search database content
        $dbMovies = collect([]);
        $dbTvShows = collect([]);

        if ($query || $genre || $year || $minRating || $type) {
            // Search movies
            if ($contentType === 'all' || $contentType === 'movies') {
                $dbMoviesQuery = Content::published()
                    ->whereIn('type', ['movie', 'documentary', 'short_film']);

                if ($query) {
                    $dbMoviesQuery->where(function($q) use ($query) {
                        $q->where('title', 'like', '%' . $query . '%')
                          ->orWhere('description', 'like', '%' . $query . '%');
                    });
                }

                if ($genre) {
                    $dbMoviesQuery->where(function($q) use ($genre) {
                        $q->whereJsonContains('genres', $genre)
                          ->orWhereJsonContains('genres', ['name' => $genre])
                          ->orWhere('genres', 'like', '%' . $genre . '%');
                    });
                }

                if ($year) {
                    $dbMoviesQuery->whereYear('release_date', $year);
                }

                if ($minRating) {
                    $dbMoviesQuery->where('rating', '>=', $minRating);
                }

                if ($type) {
                    $dbMoviesQuery->where('type', $type);
                }

                // Apply sorting
                $dbMoviesQuery = $this->applySorting($dbMoviesQuery, $sortBy);

                $dbMovies = $dbMoviesQuery->get()->map(function($content) {
                    return $this->formatContentForSearch($content, 'movie');
                });
            }

            // Search TV shows
            if ($contentType === 'all' || $contentType === 'tv_shows') {
                $dbTvShowsQuery = Content::published()
                    ->whereIn('type', ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show']);

                if ($query) {
                    $dbTvShowsQuery->where(function($q) use ($query) {
                        $q->where('title', 'like', '%' . $query . '%')
                          ->orWhere('description', 'like', '%' . $query . '%');
                    });
                }

                if ($genre) {
                    $dbTvShowsQuery->where(function($q) use ($genre) {
                        $q->whereJsonContains('genres', $genre)
                          ->orWhereJsonContains('genres', ['name' => $genre])
                          ->orWhere('genres', 'like', '%' . $genre . '%');
                    });
                }

                if ($year) {
                    $dbTvShowsQuery->whereYear('release_date', $year);
                }

                if ($minRating) {
                    $dbTvShowsQuery->where('rating', '>=', $minRating);
                }

                if ($type) {
                    $dbTvShowsQuery->where('type', $type);
                }

                // Apply sorting
                $dbTvShowsQuery = $this->applySorting($dbTvShowsQuery, $sortBy);

                $dbTvShows = $dbTvShowsQuery->get()->map(function($content) {
                    return $this->formatContentForSearch($content, 'tv_show');
                });
            }
        }

        // Only use database results (no TMDB search)
        $allMovies = $dbMovies;
        $allTvShows = $dbTvShows;

        return view('search.index', [
            'movies' => $allMovies,
            'tvShows' => $allTvShows,
            'query' => $query,
            'filters' => [
                'genre' => $genre,
                'year' => $year,
                'min_rating' => $minRating,
                'type' => $type,
                'sort_by' => $sortBy,
                'content_type' => $contentType,
            ],
            'allGenres' => $allGenres,
            'years' => $years,
            'popularContent' => $popularContent,
            'seo' => $this->seo->forSearch($query),
        ]);
    }

    private function getAllGenres(): array
    {
        $genres = [];
        
        // Get genres from database
        $dbGenres = Content::published()
            ->whereNotNull('genres')
            ->pluck('genres')
            ->flatten()
            ->filter()
            ->map(function($genre) {
                if (is_array($genre)) {
                    return $genre['name'] ?? $genre;
                }
                return $genre;
            })
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        return array_merge($genres, $dbGenres);
    }

    private function getYearsRange(): array
    {
        $minYear = Content::published()->min(DB::raw('YEAR(release_date)'));
        $maxYear = Content::published()->max(DB::raw('YEAR(release_date)'));
        
        if (!$minYear || !$maxYear) {
            $currentYear = date('Y');
            return range($currentYear, $currentYear - 50);
        }

        return range($maxYear, max($minYear, $maxYear - 50));
    }

    private function applySorting($query, $sortBy)
    {
        switch ($sortBy) {
            case 'newest':
                return $query->orderBy('release_date', 'desc')
                             ->orderBy('created_at', 'desc');
            case 'oldest':
                return $query->orderBy('release_date', 'asc')
                             ->orderBy('created_at', 'asc');
            case 'rating':
                return $query->orderBy('rating', 'desc')
                             ->orderBy('views', 'desc');
            case 'views':
                return $query->orderBy('views', 'desc')
                             ->orderBy('rating', 'desc');
            case 'title':
                return $query->orderBy('title', 'asc');
            case 'relevance':
            default:
                return $query->orderBy('views', 'desc')
                             ->orderBy('rating', 'desc')
                             ->orderBy('release_date', 'desc');
        }
    }

    private function formatContentForSearch($content, $defaultType = 'movie')
    {
        $genres = $content->genres ?? [];
        $formattedGenres = [];
        if (is_array($genres)) {
            foreach ($genres as $genre) {
                if (is_array($genre)) {
                    $formattedGenres[] = $genre['name'] ?? $genre;
                } else {
                    $formattedGenres[] = $genre;
                }
            }
        }

        // Get slug or use ID
        $slug = $content->slug ?? ('custom_' . $content->id);

        return [
            'id' => $slug,
            'title' => $content->title,
            'name' => $content->title,
            'original_title' => $content->title,
            'original_name' => $content->title,
            'release_date' => $content->release_date ? $content->release_date->format('Y-m-d') : null,
            'first_air_date' => $content->release_date ? $content->release_date->format('Y-m-d') : null,
            'vote_average' => $content->rating ?? 0,
            'overview' => $content->description ?? '',
            'poster_path' => $content->poster_path,
            'backdrop_path' => $content->backdrop_path,
            'genres' => array_map(function($g) {
                return ['name' => $g];
            }, $formattedGenres),
            'is_custom' => true,
            'content_type' => $content->content_type ?? 'custom',
            'type' => $content->type ?? $defaultType,
            'views' => $content->views ?? 0,
        ];
    }
}
