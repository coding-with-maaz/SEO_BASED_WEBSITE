<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Services\TmdbService;
use App\Services\SeoService;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class TvShowController extends Controller
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

        // Get only custom TV show content from database
        // Priority: Latest updated first, then latest created, then release date, then sort order
        $customTvShows = Content::published()
            ->whereIn('type', ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show'])
            ->orderBy('updated_at', 'desc') // Latest updated first
            ->orderBy('created_at', 'desc') // Then latest created
            ->orderBy('release_date', 'desc') // Then by release date
            ->orderBy('sort_order', 'asc'); // Finally by sort order

        // Get total count for pagination
        $totalTvShows = $customTvShows->count();
        $totalPages = max(1, ceil($totalTvShows / $perPage));

        // Paginate custom TV shows
        $customTvShows = $customTvShows->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Get popular TV shows based on views for sidebar
        $popularTvShows = Content::published()
            ->whereIn('type', ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show'])
            ->orderBy('views', 'desc')
            ->orderBy('release_date', 'desc')
            ->take(5)
            ->get();

        return view('tv-shows.index', [
            'tvShows' => [],
            'customTvShows' => $customTvShows,
            'popularTvShows' => $popularTvShows,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'type' => 'custom',
            'seo' => $this->seo->forTvShowsIndex(),
        ]);
    }

    public function show($slug)
    {
        // First, try to find custom content by slug
        $content = Content::with('castMembers')
            ->whereIn('type', ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show'])
            ->where(function($query) use ($slug) {
                $query->where('slug', $slug)
                      ->orWhere(function($q) use ($slug) {
                          // Backward compatibility: check if it's an old custom_ format
                          if (str_starts_with($slug, 'custom_')) {
                              $contentId = str_replace('custom_', '', $slug);
                              $q->where('id', $contentId);
                          }
                      });
            })
            ->first();

        if ($content) {
            // Track view for recommendations
            $this->recommendations->trackView($content->id);
            
            // Increment views when TV show is viewed
            $content->increment('views');
            $content->refresh(); // Refresh to get updated views count
            
            // Load published episodes with servers
            $episodes = $content->episodes()
                ->where('is_published', true)
                ->with('servers')
                ->orderBy('episode_number')
                ->get();
            
            // Set episodes as a collection attribute
            $content->setRelation('episodes', $episodes);
            
            // Get various recommendation types
            $similarTvShows = $this->recommendations->getSimilarContent($content, 10);
            $trendingTvShows = $this->recommendations->getTrendingContent('tv_shows', 10);
            $youMayAlsoLike = $this->recommendations->getYouMayAlsoLike('tv_shows', 10);
            $trendingMovies = $this->recommendations->getTrendingContent('movies', 10);
            
            // Prepare TV show data for SEO
            $tvShowData = [
                'name' => $content->title,
                'title' => $content->title,
                'overview' => $content->description ?? '',
                'description' => $content->description ?? '',
                'first_air_date' => $content->release_date ? $content->release_date->format('Y-m-d') : null,
                'last_air_date' => $content->end_date ? $content->end_date->format('Y-m-d') : null,
                'vote_average' => $content->rating ?? 0,
                'rating' => $content->rating ?? 0,
                'poster_path' => $content->poster_path,
                'backdrop_path' => $content->backdrop_path,
                'number_of_seasons' => 1,
                'number_of_episodes' => $episodes->count(),
                'genres' => $content->genres ? array_map(function($genre) {
                    return ['name' => is_array($genre) ? ($genre['name'] ?? $genre) : $genre];
                }, is_array($content->genres) ? $content->genres : []) : [],
                'credits' => [
                    'cast' => $content->castMembers->map(function($castMember) {
                        return [
                            'name' => $castMember->name,
                        ];
                    })->toArray(),
                ],
            ];
            
            return view('tv-shows.show', [
                'content' => $content,
                'isCustom' => true,
                'similarTvShows' => $similarTvShows,
                'trendingTvShows' => $trendingTvShows,
                'youMayAlsoLike' => $youMayAlsoLike,
                'trendingMovies' => $trendingMovies,
                'seo' => $this->seo->forTvShow($tvShowData, $content),
            ]);
        }

        // If not found as custom content, try as TMDB ID (numeric)
        if (is_numeric($slug)) {
            $tvShow = $this->tmdb->getTvShowDetails($slug);

            if ($tvShow) {
                // Check if there's a custom content linked to this TMDB ID
                $customContent = Content::where('tmdb_id', $slug)
                    ->whereIn('type', ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show'])
                    ->first();
                
                // Track view if content exists
                if ($customContent) {
                    $this->recommendations->trackView($customContent->id);
                    $customContent->increment('views');
                    
                    // Load published episodes only if custom content exists
                    $episodes = $customContent->episodes()
                        ->where('is_published', true)
                        ->with('servers')
                        ->orderBy('episode_number')
                        ->get();
                    $customContent->setRelation('episodes', $episodes);
                }

                // Get recommendations
                $trendingTvShows = $this->recommendations->getTrendingContent('tv_shows', 10);
                $youMayAlsoLike = $this->recommendations->getYouMayAlsoLike('tv_shows', 10);
                $trendingMovies = $this->recommendations->getTrendingContent('movies', 10);

                return view('tv-shows.show', [
                    'tvShow' => $tvShow,
                    'content' => $customContent,
                    'isCustom' => false,
                    'trendingTvShows' => $trendingTvShows,
                    'youMayAlsoLike' => $youMayAlsoLike,
                    'trendingMovies' => $trendingMovies,
                    'seo' => $this->seo->forTvShow($tvShow, $customContent),
                ]);
            }
        }

        // Not found
        abort(404);
    }
}
