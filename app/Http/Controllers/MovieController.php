<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Services\TmdbService;
use Illuminate\Http\Request;

class MovieController extends Controller
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

        $movies = match($type) {
            'top_rated' => $this->tmdb->getTopRatedMovies($page),
            'now_playing' => $this->tmdb->getNowPlayingMovies($page),
            'upcoming' => $this->tmdb->getUpcomingMovies($page),
            default => $this->tmdb->getPopularMovies($page),
        };

        // Get custom movie content
        $customMovies = Content::published()
            ->whereIn('type', ['movie', 'documentary', 'short_film'])
            ->orderBy('sort_order', 'asc')
            ->orderBy('release_date', 'desc')
            ->get();

        // Get top rated movies for sidebar
        $topRatedMovies = $this->tmdb->getTopRatedMovies(1);

        return view('movies.index', [
            'movies' => $movies['results'] ?? [],
            'customMovies' => $customMovies,
            'topRatedMovies' => $topRatedMovies['results'] ?? [],
            'currentPage' => $movies['page'] ?? 1,
            'totalPages' => $movies['total_pages'] ?? 1,
            'type' => $type,
        ]);
    }

    public function show($slug)
    {
        // First, try to find custom content by slug
        $content = Content::whereIn('type', ['movie', 'documentary', 'short_film'])
            ->where(function($query) use ($slug) {
                $query->where('slug', $slug)
                      ->orWhere(function($q) use ($slug) {
                          // Backward compatibility: check if it's an old custom_ format or numeric ID
                          if (str_starts_with($slug, 'custom_')) {
                              $contentId = str_replace('custom_', '', $slug);
                              $q->where('id', $contentId);
                          } elseif (is_numeric($slug)) {
                              $q->where('id', $slug);
                          }
                      });
            })
            ->first();

        if ($content) {
            // Get recommended movies for custom content
            $recommendedMovies = $this->tmdb->getPopularMovies(1);
            
            // Prepare movie data from custom content
            $movieData = [
                'title' => $content->title,
                'original_title' => $content->title,
                'vote_average' => $content->rating ?? 0,
                'release_date' => $content->release_date ? $content->release_date->format('Y-m-d') : null,
                'runtime' => $content->duration ?? null,
                'overview' => $content->description ?? '',
                'poster_path' => $content->poster_path,
                'backdrop_path' => $content->backdrop_path,
                'genres' => $content->genres ? array_map(function($genre) {
                    return ['name' => is_array($genre) ? ($genre['name'] ?? $genre) : $genre];
                }, is_array($content->genres) ? $content->genres : []) : [],
                'credits' => [
                    'cast' => $content->cast ? array_map(function($castMember) {
                        if (is_array($castMember)) {
                            return [
                                'name' => $castMember['name'] ?? $castMember,
                                'character' => $castMember['character'] ?? '',
                                'profile_path' => $castMember['profile_path'] ?? null,
                            ];
                        }
                        return ['name' => $castMember, 'character' => '', 'profile_path' => null];
                    }, is_array($content->cast) ? $content->cast : []) : [],
                ],
                'production_countries' => $content->country ? [['name' => $content->country]] : [],
                'spoken_languages' => $content->language ? [['name' => $content->language]] : [],
                'videos' => ['results' => []],
                'recommendations' => ['results' => $recommendedMovies['results'] ?? []],
            ];

            // Add director to crew
            if ($content->director) {
                $movieData['credits']['crew'][] = [
                    'name' => $content->director,
                    'job' => 'Director',
                ];
            }

            return view('movies.show', [
                'movie' => $movieData,
                'content' => $content,
                'isCustom' => true,
            ]);
        }

        // If not found as custom content, try as TMDB ID (numeric)
        if (is_numeric($slug)) {
            $movie = $this->tmdb->getMovieDetails($slug);

            if ($movie) {
                return view('movies.show', [
                    'movie' => $movie,
                    'isCustom' => false,
                ]);
            }
        }

        // Not found
        abort(404);
    }
}
