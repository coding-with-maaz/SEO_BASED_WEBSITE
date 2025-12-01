<?php

namespace App\Services;

use App\Models\Content;
use App\Models\Cast;
use App\Models\Episode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SitemapService
{
    protected $siteUrl;
    protected $cacheDuration;

    public function __construct()
    {
        $this->siteUrl = rtrim(config('app.url', url('/')), '/');
        $this->cacheDuration = config('sitemap.cache_duration', 3600); // 1 hour default
    }

    /**
     * Get all sitemap URLs organized by type
     */
    public function getAllUrls(): array
    {
        return Cache::remember('sitemap_all_urls', $this->cacheDuration, function () {
            return [
                'static' => $this->getStaticPages(),
                'movies' => $this->getMoviesUrls(),
                'tv_shows' => $this->getTvShowsUrls(),
                'cast' => $this->getCastUrls(),
                'episodes' => $this->getEpisodesUrls(),
            ];
        });
    }

    /**
     * Get static pages (home, about, dmca, etc.)
     */
    public function getStaticPages(): array
    {
        $pages = [
            [
                'loc' => route('home'),
                'lastmod' => $this->getSiteLastModified(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => route('movies.index'),
                'lastmod' => $this->getContentLastModified('movie'),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => route('tv-shows.index'),
                'lastmod' => $this->getContentLastModified('tv_show'),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => route('cast.index'),
                'lastmod' => $this->getCastLastModified(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('about'),
                'lastmod' => $this->getSiteLastModified(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
            [
                'loc' => route('dmca'),
                'lastmod' => $this->getSiteLastModified(),
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ],
            [
                'loc' => route('completed'),
                'lastmod' => $this->getContentLastModified('tv_show', 'completed'),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ],
            [
                'loc' => route('upcoming'),
                'lastmod' => $this->getContentLastModified(null, 'upcoming'),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
        ];

        return $pages;
    }

    /**
     * Get all movie URLs
     */
    public function getMoviesUrls(): array
    {
        $movies = Content::published()
            ->whereIn('type', ['movie', 'documentary', 'short_film'])
            ->whereNotNull('slug')
            ->orderBy('updated_at', 'desc')
            ->get();

        $urls = [];
        foreach ($movies as $movie) {
            $urls[] = [
                'loc' => route('movies.show', $movie->slug),
                'lastmod' => $this->formatDate($movie->updated_at),
                'changefreq' => $this->getContentChangeFreq($movie),
                'priority' => $this->getContentPriority($movie),
            ];
        }

        return $urls;
    }

    /**
     * Get all TV show URLs
     */
    public function getTvShowsUrls(): array
    {
        $tvShows = Content::published()
            ->whereIn('type', ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show'])
            ->whereNotNull('slug')
            ->orderBy('updated_at', 'desc')
            ->get();

        $urls = [];
        foreach ($tvShows as $tvShow) {
            $urls[] = [
                'loc' => route('tv-shows.show', $tvShow->slug),
                'lastmod' => $this->formatDate($tvShow->updated_at),
                'changefreq' => $this->getContentChangeFreq($tvShow),
                'priority' => $this->getContentPriority($tvShow),
            ];
        }

        return $urls;
    }

    /**
     * Get all cast member URLs
     */
    public function getCastUrls(): array
    {
        $casts = Cast::whereNotNull('slug')
            ->orderBy('updated_at', 'desc')
            ->get();

        $urls = [];
        foreach ($casts as $cast) {
            $urls[] = [
                'loc' => route('cast.show', $cast->slug),
                'lastmod' => $this->formatDate($cast->updated_at),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }

        return $urls;
    }

    /**
     * Get all episode URLs
     */
    public function getEpisodesUrls(): array
    {
        $episodes = Episode::published()
            ->whereHas('content', function ($query) {
                $query->published();
            })
            ->whereNotNull('slug')
            ->with('content:id,slug,type')
            ->orderBy('updated_at', 'desc')
            ->get();

        $urls = [];
        foreach ($episodes as $episode) {
            if ($episode->content) {
                $urls[] = [
                    'loc' => $this->buildEpisodeUrl($episode),
                    'lastmod' => $this->formatDate($episode->updated_at),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            }
        }

        return $urls;
    }

    /**
     * Build episode URL (if you have episode routes)
     */
    protected function buildEpisodeUrl(Episode $episode): string
    {
        // For now, link to TV show page
        // You can modify this if you have episode detail pages
        return route('tv-shows.show', $episode->content->slug);
    }

    /**
     * Get sitemap index (for multiple sitemaps)
     */
    public function getSitemapIndex(): array
    {
        return [
            [
                'loc' => route('sitemap.static'),
                'lastmod' => $this->getSiteLastModified(),
            ],
            [
                'loc' => route('sitemap.movies'),
                'lastmod' => $this->getContentLastModified('movie'),
            ],
            [
                'loc' => route('sitemap.tv-shows'),
                'lastmod' => $this->getContentLastModified('tv_show'),
            ],
            [
                'loc' => route('sitemap.cast'),
                'lastmod' => $this->getCastLastModified(),
            ],
            [
                'loc' => route('sitemap.episodes'),
                'lastmod' => $this->getEpisodesLastModified(),
            ],
        ];
    }

    /**
     * Get single sitemap by type
     */
    public function getSitemapByType(string $type): array
    {
        return match ($type) {
            'static' => $this->getStaticPages(),
            'movies' => $this->getMoviesUrls(),
            'tv-shows' => $this->getTvShowsUrls(),
            'cast' => $this->getCastUrls(),
            'episodes' => $this->getEpisodesUrls(),
            default => [],
        };
    }

    /**
     * Get all URLs in a single array (for main sitemap)
     */
    public function getAllUrlsFlat(): array
    {
        $all = $this->getAllUrls();
        return array_merge(
            $all['static'],
            $all['movies'],
            $all['tv_shows'],
            $all['cast'],
            $all['episodes']
        );
    }

    /**
     * Get content change frequency based on content attributes
     */
    protected function getContentChangeFreq(Content $content): string
    {
        // Featured content changes more frequently
        if ($content->is_featured) {
            return 'daily';
        }

        // Recent content changes more frequently
        if ($content->release_date && $content->release_date->gt(now()->subMonths(3))) {
            return 'weekly';
        }

        return 'monthly';
    }

    /**
     * Get content priority based on content attributes
     */
    protected function getContentPriority(Content $content): string
    {
        // Featured content has higher priority
        if ($content->is_featured) {
            return '0.9';
        }

        // Popular content (high views) has higher priority
        if ($content->views > 1000) {
            return '0.8';
        }

        // Recent content has higher priority
        if ($content->release_date && $content->release_date->gt(now()->subMonths(6))) {
            return '0.7';
        }

        return '0.6';
    }

    /**
     * Format date for sitemap (W3C format)
     */
    protected function formatDate($date): string
    {
        if (!$date) {
            return Carbon::now()->toW3cString();
        }

        return Carbon::parse($date)->toW3cString();
    }

    /**
     * Get site last modified date
     */
    protected function getSiteLastModified(): string
    {
        return Carbon::now()->toW3cString();
    }

    /**
     * Get content last modified date
     */
    protected function getContentLastModified(?string $type = null, ?string $status = null): string
    {
        $query = Content::published();
        
        if ($type) {
            if (in_array($type, ['movie', 'documentary', 'short_film'])) {
                $query->whereIn('type', ['movie', 'documentary', 'short_film']);
            } else {
                $query->whereIn('type', ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show']);
            }
        }

        if ($status === 'completed') {
            $query->where('series_status', 'completed');
        } elseif ($status === 'upcoming') {
            $query->where(function($q) {
                $q->where('series_status', 'upcoming')
                  ->orWhere('status', 'upcoming');
            });
        }

        $latest = $query->orderBy('updated_at', 'desc')->first();
        
        return $latest ? $this->formatDate($latest->updated_at) : $this->getSiteLastModified();
    }

    /**
     * Get cast last modified date
     */
    protected function getCastLastModified(): string
    {
        $latest = Cast::orderBy('updated_at', 'desc')->first();
        return $latest ? $this->formatDate($latest->updated_at) : $this->getSiteLastModified();
    }

    /**
     * Get episodes last modified date
     */
    protected function getEpisodesLastModified(): string
    {
        $latest = Episode::published()
            ->whereHas('content', function ($query) {
                $query->published();
            })
            ->orderBy('updated_at', 'desc')
            ->first();
        
        return $latest ? $this->formatDate($latest->updated_at) : $this->getSiteLastModified();
    }

    /**
     * Clear sitemap cache
     */
    public function clearCache(): void
    {
        Cache::forget('sitemap_all_urls');
    }
}

