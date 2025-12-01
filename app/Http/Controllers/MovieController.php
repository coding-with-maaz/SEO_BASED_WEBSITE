<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Services\TmdbService;
use App\Services\SeoService;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    protected $tmdb;
    protected $seo;
    protected $recommendations;

    public function __construct(TmdbService $tmdb, SeoService $seo, RecommendationService $recommendations)
    {
        $this->tmdb = $tmdb;
        $this->seo = $seo;
        $this->recommendations = $recommendations;
    }

    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = 20; // Items per page

        // Get only custom movie content from database
        // Priority: Latest updated first, then latest created, then release date, then sort order
        $customMovies = Content::published()
            ->whereIn('type', ['movie', 'documentary', 'short_film'])
            ->orderBy('updated_at', 'desc') // Latest updated first
            ->orderBy('created_at', 'desc') // Then latest created
            ->orderBy('release_date', 'desc') // Then by release date
            ->orderBy('sort_order', 'asc'); // Finally by sort order

        // Get total count for pagination
        $totalMovies = $customMovies->count();
        $totalPages = max(1, ceil($totalMovies / $perPage));

        // Paginate custom movies
        $customMovies = $customMovies->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Get popular movies based on views for sidebar
        $popularMovies = Content::published()
            ->whereIn('type', ['movie', 'documentary', 'short_film'])
            ->orderBy('views', 'desc')
            ->orderBy('release_date', 'desc')
            ->take(5)
            ->get();

        return view('movies.index', [
            'movies' => [],
            'customMovies' => $customMovies,
            'popularMovies' => $popularMovies,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'type' => 'custom', // Changed from dynamic type
            'seo' => $this->seo->forMoviesIndex(),
        ]);
    }

    public function show($slug)
    {
        // First, try to find custom content by slug
        $content = Content::with('castMembers')
            ->whereIn('type', ['movie', 'documentary', 'short_film'])
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
            // Track view for recommendations
            $this->recommendations->trackView($content->id);
            
            // Increment views when movie is viewed
            $content->increment('views');
            $content->refresh(); // Refresh to get updated views count
            
            // Get various recommendation types
            $similarMovies = $this->recommendations->getSimilarContent($content, 10);
            $trendingMovies = $this->recommendations->getTrendingContent('movies', 10);
            $youMayAlsoLike = $this->recommendations->getYouMayAlsoLike('movies', 10);
            
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
                'views' => $content->views ?? 0,
                'genres' => $content->genres ? array_map(function($genre) {
                    return ['name' => is_array($genre) ? ($genre['name'] ?? $genre) : $genre];
                }, is_array($content->genres) ? $content->genres : []) : [],
                'credits' => [
                    'cast' => $content->castMembers->map(function($castMember) {
                        return [
                            'id' => $castMember->id,
                            'slug' => $castMember->slug,
                            'name' => $castMember->name,
                            'character' => $castMember->pivot->character ?? '',
                            'profile_path' => $castMember->profile_path,
                        ];
                    })->toArray(),
                ],
                'production_countries' => $content->country ? [['name' => $content->country]] : [],
                'spoken_languages' => $content->language ? [['name' => $content->language]] : [],
                'videos' => ['results' => []],
                'recommendations' => ['results' => $similarMovies],
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
                'similarMovies' => $similarMovies,
                'trendingMovies' => $trendingMovies,
                'youMayAlsoLike' => $youMayAlsoLike,
                'seo' => $this->seo->forMovie($movieData, $content),
            ]);
        }

        // If not found as custom content, try as TMDB ID (numeric)
        if (is_numeric($slug)) {
            $movie = $this->tmdb->getMovieDetails($slug);

            if ($movie) {
                // Track view (if we can find the content in database)
                $dbContent = Content::where('tmdb_id', $slug)
                    ->where('content_type', 'tmdb')
                    ->first();
                if ($dbContent) {
                    $this->recommendations->trackView($dbContent->id);
                    $dbContent->increment('views');
                }
                
                // Get recommendations
                $trendingMovies = $this->recommendations->getTrendingContent('movies', 10);
                $youMayAlsoLike = $this->recommendations->getYouMayAlsoLike('movies', 10);
                
                // Replace TMDB recommendations with database recommendations
                $movie['recommendations'] = ['results' => $trendingMovies];
                
                return view('movies.show', [
                    'movie' => $movie,
                    'isCustom' => false,
                    'trendingMovies' => $trendingMovies,
                    'youMayAlsoLike' => $youMayAlsoLike,
                    'seo' => $this->seo->forMovie($movie),
                ]);
            }
        }

        // Not found
        abort(404);
    }
}
