<?php

namespace App\Services;

use App\Helpers\SchemaHelper;
use App\Models\PageSeo;
use App\Services\TmdbService;

class SeoService
{
    protected $tmdb;
    protected $siteName;
    protected $siteUrl;
    protected $defaultImage;
    protected $twitterHandle;
    protected $facebookAppId;

    public function __construct(TmdbService $tmdb)
    {
        $this->tmdb = $tmdb;
        $this->siteName = config('app.name', 'Nazaarabox');
        $this->siteUrl = config('app.url', url('/'));
        $this->defaultImage = asset('favicon.ico');
        $this->twitterHandle = '@nazaarabox'; // Update with your Twitter handle
        $this->facebookAppId = ''; // Add your Facebook App ID if available
    }

    /**
     * Generate SEO metadata for a page
     * Checks for admin-managed PageSeo first, then uses provided data or defaults
     */
    public function generate(array $data = [], ?string $pageKey = null): array
    {
        // Check for admin-managed PageSeo first (always get fresh data)
        if ($pageKey) {
            $pageSeo = PageSeo::getByPageKey($pageKey);
            if ($pageSeo && $pageSeo->is_active) {
                // Use PageSeo data and merge with any override data from controller
                return $this->fromPageSeo($pageSeo, $data);
            }
        }

        $title = $data['title'] ?? $this->siteName;
        $description = $data['description'] ?? 'Watch and download your favorite movies and TV shows. Browse thousands of titles in high quality.';
        $keywords = $data['keywords'] ?? 'movies, tv shows, streaming, download, watch online, entertainment';
        $image = $data['image'] ?? $this->defaultImage;
        $url = $data['url'] ?? url()->current();
        $type = $data['type'] ?? 'website';
        $publishedTime = $data['published_time'] ?? null;
        $modifiedTime = $data['modified_time'] ?? null;
        $author = $data['author'] ?? $this->siteName;
        $schema = $data['schema'] ?? null;
        $canonical = $data['canonical'] ?? $url;
        $robots = $data['robots'] ?? 'index, follow';
        $locale = $data['locale'] ?? 'en_US';
        $alternateLocales = $data['alternate_locales'] ?? [];

        // Ensure image is absolute URL
        if ($image && !filter_var($image, FILTER_VALIDATE_URL)) {
            $image = url($image);
        }

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'image' => $image,
            'url' => $url,
            'type' => $type,
            'published_time' => $publishedTime,
            'modified_time' => $modifiedTime,
            'author' => $author,
            'schema' => $schema,
            'canonical' => $canonical,
            'robots' => $robots,
            'locale' => $locale,
            'alternate_locales' => $alternateLocales,
        ];
    }

    /**
     * Generate SEO from admin-managed PageSeo model
     */
    protected function fromPageSeo(PageSeo $pageSeo, array $overrideData = []): array
    {
        // Parse schema markup if exists
        $schema = null;
        if ($pageSeo->schema_markup) {
            $decoded = json_decode($pageSeo->schema_markup, true);
            $schema = is_array($decoded) ? [$decoded] : null;
        }

        // Build data array from PageSeo - prioritize PageSeo fields
        // Use meta_title directly, fallback to og_title only if meta_title is empty
        $title = !empty($pageSeo->meta_title) 
            ? $pageSeo->meta_title 
            : ($pageSeo->og_title ?? $this->siteName);
        
        $description = !empty($pageSeo->meta_description)
            ? $pageSeo->meta_description
            : ($pageSeo->og_description ?? '');
        
        $data = [
            'title' => $title,
            'description' => $description,
            'keywords' => $pageSeo->meta_keywords ?? '',
            'image' => $pageSeo->og_image ?? $pageSeo->twitter_image ?? $this->defaultImage,
            'url' => $pageSeo->og_url ?? url()->current(),
            'type' => $pageSeo->og_type ?? 'website',
            'canonical' => $pageSeo->canonical_url ?? url()->current(),
            'robots' => $pageSeo->meta_robots ?? 'index, follow',
            'schema' => $schema,
            'alternate_locales' => $pageSeo->hreflang_tags ?? [],
        ];

        // Only merge override data for fields that are truly missing (for dynamic content)
        foreach ($overrideData as $key => $value) {
            if (!array_key_exists($key, $data) || (empty($data[$key]) && $value !== null && $value !== '')) {
                $data[$key] = $value;
            }
        }

        // Set OG and Twitter fields from PageSeo (these should always use PageSeo values if set)
        if (!empty($pageSeo->og_title)) {
            $data['og_title'] = $pageSeo->og_title;
        }
        if (!empty($pageSeo->og_description)) {
            $data['og_description'] = $pageSeo->og_description;
        }

        // Generate without checking for PageSeo again (to avoid recursion)
        $title = $data['title'] ?? $this->siteName;
        $description = $data['description'] ?? '';
        $keywords = $data['keywords'] ?? '';
        $image = $data['image'] ?? $this->defaultImage;
        $url = $data['url'] ?? url()->current();
        $type = $data['type'] ?? 'website';
        $canonical = $data['canonical'] ?? $url;
        $robots = $data['robots'] ?? 'index, follow';
        $locale = $data['locale'] ?? 'en_US';
        $alternateLocales = $data['alternate_locales'] ?? [];
        $schema = $data['schema'] ?? null;

        // Ensure image is absolute URL
        if ($image && !filter_var($image, FILTER_VALIDATE_URL)) {
            $image = url($image);
        }

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'image' => $image,
            'url' => $url,
            'type' => $type,
            'canonical' => $canonical,
            'robots' => $robots,
            'locale' => $locale,
            'alternate_locales' => $alternateLocales,
            'schema' => $schema,
            // Twitter Card specific fields from PageSeo
            'twitter_card' => $pageSeo->twitter_card ?? 'summary_large_image',
            'twitter_title' => $pageSeo->twitter_title ?? $title,
            'twitter_description' => $pageSeo->twitter_description ?? $description,
            'twitter_image' => $pageSeo->twitter_image ? (filter_var($pageSeo->twitter_image, FILTER_VALIDATE_URL) ? $pageSeo->twitter_image : url($pageSeo->twitter_image)) : $image,
            // OG specific fields
            'og_title' => $pageSeo->og_title ?? $title,
            'og_description' => $pageSeo->og_description ?? $description,
            'og_image' => $pageSeo->og_image ? (filter_var($pageSeo->og_image, FILTER_VALIDATE_URL) ? $pageSeo->og_image : url($pageSeo->og_image)) : $image,
        ];
    }

    /**
     * Generate SEO for home page
     */
    public function forHome(): array
    {
        return $this->generate([
            'title' => 'Nazaarabox - Watch Movies & TV Shows Online',
            'description' => 'Discover and watch thousands of movies and TV shows online. Browse popular content, top-rated titles, and latest releases. Download and stream in high quality.',
            'keywords' => 'movies, tv shows, streaming, watch online, download movies, entertainment, latest movies, popular tv shows',
            'type' => 'website',
            'schema' => [
                SchemaHelper::website([
                    'name' => $this->siteName,
                    'url' => $this->siteUrl,
                    'search_url' => route('search') . '?q={search_term_string}',
                ]),
                SchemaHelper::organization([
                    'name' => $this->siteName,
                    'url' => $this->siteUrl,
                ]),
            ],
        ], 'home');
    }

    /**
     * Generate SEO for movies listing page
     */
    public function forMoviesIndex(): array
    {
        return $this->generate([
            'title' => 'Movies - Browse All Movies | Nazaarabox',
            'description' => 'Browse our complete collection of movies. Find action, drama, comedy, thriller, and more. Watch and download movies in high quality.',
            'keywords' => 'movies, watch movies, download movies, action movies, drama movies, comedy movies, latest movies',
            'type' => 'website',
            'schema' => [
                SchemaHelper::collectionPage([
                    'name' => 'Movies',
                    'url' => route('movies.index'),
                    'description' => 'Browse our complete collection of movies',
                ]),
            ],
        ], 'movies.index');
    }

    /**
     * Generate SEO for a movie detail page
     */
    public function forMovie($movie, $content = null): array
    {
        $title = $movie['title'] ?? $movie['original_title'] ?? 'Movie';
        $description = $movie['overview'] ?? $movie['description'] ?? "Watch {$title} online. Download and stream in high quality.";
        $releaseDate = $movie['release_date'] ?? ($content?->release_date?->format('Y-m-d'));
        $rating = $movie['vote_average'] ?? $movie['rating'] ?? 0;
        $duration = $movie['runtime'] ?? $content?->duration;
        $director = $movie['director'] ?? ($content?->director ?? null);
        
        // Get image
        $image = $this->getImageUrl($movie['backdrop_path'] ?? $movie['poster_path'] ?? $content?->backdrop_path ?? $content?->poster_path, 'w1280');
        
        // Get genres
        $genres = [];
        if (isset($movie['genres']) && is_array($movie['genres'])) {
            $genres = array_map(function($g) {
                return is_array($g) ? ($g['name'] ?? '') : $g;
            }, $movie['genres']);
        } elseif ($content?->genres) {
            $genres = is_array($content->genres) ? $content->genres : [$content->genres];
        }
        
        // Get actors
        $actors = [];
        if (isset($movie['credits']['cast'])) {
            $actors = array_slice(array_map(function($actor) {
                return $actor['name'] ?? '';
            }, $movie['credits']['cast']), 0, 10);
        } elseif ($content?->castMembers) {
            $actors = $content->castMembers->pluck('name')->toArray();
        }

        $url = route('movies.show', $content?->slug ?? ($movie['id'] ?? ''));
        $keywords = implode(', ', array_merge([$title], $genres, array_slice($actors, 0, 5)));

        // Generate movie schema
        $movieSchema = SchemaHelper::movie([
            'name' => $title,
            'description' => $description,
            'image' => $image,
            'url' => $url,
            'date_published' => $releaseDate,
            'duration' => $duration,
            'director' => $director,
            'aggregate_rating' => [
                'value' => $rating,
                'count' => $movie['views'] ?? $content?->views ?? 0,
            ],
            'genre' => $genres,
            'actor' => $actors,
        ]);

        return $this->generate([
            'title' => "{$title} ({$releaseDate}) - Watch Online | Nazaarabox",
            'description' => $description,
            'keywords' => $keywords,
            'image' => $image,
            'url' => $url,
            'type' => 'video.movie',
            'published_time' => $releaseDate ? date('c', strtotime($releaseDate)) : null,
            'schema' => [$movieSchema],
        ]);
    }

    /**
     * Generate SEO for TV shows listing page
     */
    public function forTvShowsIndex(): array
    {
        return $this->generate([
            'title' => 'TV Shows - Browse All TV Series | Nazaarabox',
            'description' => 'Browse our complete collection of TV shows and series. Find drama, comedy, action, thriller series and more. Watch and download TV shows in high quality.',
            'keywords' => 'tv shows, tv series, watch tv shows, download tv shows, drama series, comedy series, latest tv shows',
            'type' => 'website',
            'schema' => [
                SchemaHelper::collectionPage([
                    'name' => 'TV Shows',
                    'url' => route('tv-shows.index'),
                    'description' => 'Browse our complete collection of TV shows and series',
                ]),
            ],
        ], 'tv-shows.index');
    }

    /**
     * Generate SEO for a TV show detail page
     */
    public function forTvShow($show, $content = null): array
    {
        $title = $show['name'] ?? $show['title'] ?? 'TV Show';
        $description = $show['overview'] ?? $show['description'] ?? "Watch {$title} online. Download and stream all episodes in high quality.";
        $firstAirDate = $show['first_air_date'] ?? ($content?->release_date?->format('Y-m-d'));
        $lastAirDate = $show['last_air_date'] ?? ($content?->end_date?->format('Y-m-d'));
        $rating = $show['vote_average'] ?? $show['rating'] ?? 0;
        $numberOfSeasons = $show['number_of_seasons'] ?? 1;
        $numberOfEpisodes = $show['number_of_episodes'] ?? ($content ? $content->episodes()->count() : 0);
        
        // Get image
        $image = $this->getImageUrl($show['backdrop_path'] ?? $show['poster_path'] ?? $content?->backdrop_path ?? $content?->poster_path, 'w1280');
        
        // Get genres
        $genres = [];
        if (isset($show['genres']) && is_array($show['genres'])) {
            $genres = array_map(function($g) {
                return is_array($g) ? ($g['name'] ?? '') : $g;
            }, $show['genres']);
        } elseif ($content?->genres) {
            $genres = is_array($content->genres) ? $content->genres : [$content->genres];
        }
        
        // Get actors
        $actors = [];
        if (isset($show['credits']['cast'])) {
            $actors = array_slice(array_map(function($actor) {
                return $actor['name'] ?? '';
            }, $show['credits']['cast']), 0, 10);
        } elseif ($content?->castMembers) {
            $actors = $content->castMembers->pluck('name')->toArray();
        }

        $url = route('tv-shows.show', $content?->slug ?? ($show['id'] ?? ''));
        $keywords = implode(', ', array_merge([$title], $genres, array_slice($actors, 0, 5)));

        // Generate TV series schema
        $tvSchema = SchemaHelper::tvSeries([
            'name' => $title,
            'description' => $description,
            'image' => $image,
            'url' => $url,
            'start_date' => $firstAirDate,
            'end_date' => $lastAirDate,
            'number_of_seasons' => $numberOfSeasons,
            'number_of_episodes' => $numberOfEpisodes,
            'actor' => $actors,
        ]);

        return $this->generate([
            'title' => "{$title} - Watch Online | Nazaarabox",
            'description' => $description,
            'keywords' => $keywords,
            'image' => $image,
            'url' => $url,
            'type' => 'video.tv_show',
            'published_time' => $firstAirDate ? date('c', strtotime($firstAirDate)) : null,
            'schema' => [$tvSchema],
        ]);
    }

    /**
     * Generate SEO for cast listing page
     */
    public function forCastIndex(): array
    {
        return $this->generate([
            'title' => 'Cast & Actors - Browse All Cast Members | Nazaarabox',
            'description' => 'Browse our collection of actors and cast members. Discover popular actors, their movies and TV shows.',
            'keywords' => 'actors, cast, celebrities, movie stars, tv actors, popular actors',
            'type' => 'website',
        ], 'cast.index');
    }

    /**
     * Generate SEO for a cast member detail page
     */
    public function forCast($cast): array
    {
        $name = $cast->name ?? 'Actor';
        $biography = $cast->biography ?? '';
        $description = $biography ?: "Learn more about {$name}. Browse their movies and TV shows on Nazaarabox.";
        
        // Get image
        $image = $this->getImageUrl($cast->profile_path, 'w500');
        
        $url = route('cast.show', $cast->slug ?? $cast->id);
        $keywords = "{$name}, actor, cast, movies, tv shows";

        // Generate person schema
        $personSchema = SchemaHelper::person([
            'name' => $name,
            'description' => $description,
            'image' => $image,
        ]);

        return $this->generate([
            'title' => "{$name} - Movies & TV Shows | Nazaarabox",
            'description' => $description,
            'keywords' => $keywords,
            'image' => $image,
            'url' => $url,
            'type' => 'profile',
            'schema' => [$personSchema],
        ]);
    }

    /**
     * Generate SEO for search page
     */
    public function forSearch($query = null): array
    {
        $title = $query ? "Search Results for '{$query}' - Nazaarabox" : 'Search Movies & TV Shows - Nazaarabox';
        $description = $query 
            ? "Search results for '{$query}'. Find movies and TV shows matching your search."
            : 'Search for movies and TV shows. Find your favorite content quickly.';

        return $this->generate([
            'title' => $title,
            'description' => $description,
            'keywords' => 'search, find movies, find tv shows, search entertainment',
            'type' => 'website',
            'robots' => 'noindex, follow', // Don't index search pages
        ], 'search');
    }

    /**
     * Generate SEO for static pages
     */
    public function forPage($pageKey, $title = null, $description = null): array
    {
        $pages = [
            'about' => [
                'title' => 'About Us - Nazaarabox',
                'description' => 'Learn more about Nazaarabox. Your destination for movies and TV shows.',
            ],
            'dmca' => [
                'title' => 'DMCA - Digital Millennium Copyright Act | Nazaarabox',
                'description' => 'DMCA policy and copyright information for Nazaarabox.',
            ],
            'completed' => [
                'title' => 'Completed TV Shows - Nazaarabox',
                'description' => 'Browse completed TV shows and series. Watch finished series in high quality.',
            ],
            'upcoming' => [
                'title' => 'Upcoming Movies & TV Shows - Nazaarabox',
                'description' => 'Discover upcoming movies and TV shows. Stay updated with latest releases.',
            ],
        ];

        $pageData = $pages[$pageKey] ?? [
            'title' => $title ?? ucfirst($pageKey) . ' - Nazaarabox',
            'description' => $description ?? '',
        ];

        return $this->generate([
            'title' => $title ?? $pageData['title'],
            'description' => $description ?? $pageData['description'],
            'type' => 'website',
        ], $pageKey);
    }

    /**
     * Get image URL (handles TMDB and custom images)
     */
    protected function getImageUrl($path, $size = 'w1280'): string
    {
        if (!$path) {
            return $this->defaultImage;
        }

        // If it's already a full URL, return it
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // If it starts with /, it's a TMDB path
        if (str_starts_with($path, '/')) {
            return $this->tmdb->getImageUrl($path, $size);
        }

        // Otherwise, treat as relative URL
        return url($path);
    }

    /**
     * Get Twitter handle
     */
    public function getTwitterHandle(): string
    {
        return $this->twitterHandle;
    }

    /**
     * Get Facebook App ID
     */
    public function getFacebookAppId(): string
    {
        return $this->facebookAppId;
    }

    /**
     * Get sitemap URL
     */
    public function getSitemapUrl(): string
    {
        return route('sitemap.index');
    }

    /**
     * Get sitemap index URL
     */
    public function getSitemapIndexUrl(): string
    {
        return route('sitemap.sitemap-index');
    }

    /**
     * Automatically detect and generate SEO based on current route
     */
    public function forCurrentRoute(): array
    {
        $routeName = request()->route()?->getName();
        
        if (!$routeName) {
            return $this->forHome();
        }

        // Map route names to page keys and methods
        $routeMap = [
            'home' => ['pageKey' => 'home', 'method' => 'forHome'],
            'movies.index' => ['pageKey' => 'movies.index', 'method' => 'forMoviesIndex'],
            'tv-shows.index' => ['pageKey' => 'tv-shows.index', 'method' => 'forTvShowsIndex'],
            'cast.index' => ['pageKey' => 'cast.index', 'method' => 'forCastIndex'],
            'search' => ['pageKey' => 'search', 'method' => 'forSearch'],
            'about' => ['pageKey' => 'about', 'method' => 'forPage'],
            'dmca' => ['pageKey' => 'dmca', 'method' => 'forPage'],
            'completed' => ['pageKey' => 'completed', 'method' => 'forPage'],
            'upcoming' => ['pageKey' => 'upcoming', 'method' => 'forPage'],
        ];

        if (isset($routeMap[$routeName])) {
            $config = $routeMap[$routeName];
            
            // Use specific method if available
            if ($config['method'] === 'forPage') {
                return $this->forPage($config['pageKey']);
            }
            
            // Call the specific method
            if (method_exists($this, $config['method'])) {
                return $this->{$config['method']}();
            }
        }

        // Fallback: try to use page key directly
        if (str_contains($routeName, '.')) {
            $pageKey = str_replace(['movies.', 'tv-shows.', 'cast.'], ['movies.', 'tv-shows.', 'cast.'], $routeName);
            return $this->generate([], $pageKey);
        }

        // Final fallback
        return $this->forHome();
    }
}

