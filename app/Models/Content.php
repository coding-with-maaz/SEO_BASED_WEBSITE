<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Content extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'type',
        'content_type',
        'tmdb_id',
        'poster_path',
        'backdrop_path',
        'release_date',
        'rating',
        'episode_count',
        'status',
        'series_status',
        'network',
        'end_date',
        'duration',
        'country',
        'director',
        'genres',
        'language',
        'dubbing_language',
        'download_link',
        'watch_link',
        'servers',
        'views',
        'sort_order',
        'is_featured',
    ];

    protected $casts = [
        'release_date' => 'date',
        'end_date' => 'date',
        'rating' => 'decimal:1',
        'genres' => 'array',
        'servers' => 'array',
        'is_featured' => 'boolean',
        'views' => 'integer',
        'episode_count' => 'integer',
        'duration' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get episodes for this content
     */
    public function episodes()
    {
        return $this->hasMany(Episode::class)->published()->ordered();
    }

    /**
     * Get all episodes (including unpublished)
     */
    public function allEpisodes()
    {
        return $this->hasMany(Episode::class)->ordered();
    }

    /**
     * Get all cast members for this content
     */
    public function casts(): BelongsToMany
    {
        return $this->belongsToMany(Cast::class, 'content_cast')
            ->withPivot('character', 'order')
            ->withTimestamps()
            ->orderByPivot('order', 'asc');
    }

    /**
     * Get available content types
     */
    public static function getContentTypes(): array
    {
        return [
            'movie' => 'Movie',
            'tv_show' => 'TV Show',
            'web_series' => 'Web Series',
            'documentary' => 'Documentary',
            'short_film' => 'Short Film',
            'anime' => 'Anime',
            'cartoon' => 'Cartoon',
            'reality_show' => 'Reality Show',
            'talk_show' => 'Talk Show',
            'sports' => 'Sports',
        ];
    }

    /**
     * Get available dubbing languages
     */
    public static function getDubbingLanguages(): array
    {
        return [
            'hindi' => 'Hindi',
            'english' => 'English',
            'urdu' => 'Urdu',
            'tamil' => 'Tamil',
            'telugu' => 'Telugu',
            'bengali' => 'Bengali',
            'marathi' => 'Marathi',
            'gujarati' => 'Gujarati',
            'punjabi' => 'Punjabi',
            'kannada' => 'Kannada',
            'malayalam' => 'Malayalam',
        ];
    }

    /**
     * Get available series statuses
     */
    public static function getSeriesStatuses(): array
    {
        return [
            'ongoing' => 'Ongoing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'upcoming' => 'Upcoming',
            'on_hold' => 'On Hold',
        ];
    }

    /**
     * Scope for published content
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for featured content
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($content) {
            if (empty($content->slug)) {
                $content->slug = $content->generateUniqueSlug();
            }
        });

        static::updating(function ($content) {
            if ($content->isDirty('title') && empty($content->slug)) {
                $content->slug = $content->generateUniqueSlug();
            }
        });
    }

    /**
     * Generate a unique slug from the title.
     */
    public function generateUniqueSlug()
    {
        $slug = Str::slug($this->title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Find content by slug or ID (for backward compatibility).
     */
    public static function findBySlugOrId($identifier)
    {
        // Try to find by slug first
        $content = static::where('slug', $identifier)->first();
        
        if (!$content) {
            // Try to find by ID as fallback
            $content = static::find($identifier);
        }
        
        return $content;
    }

    /**
     * Get normalized servers array with consistent structure
     */
    public function getNormalizedServers()
    {
        $servers = $this->servers ?? [];
        $normalized = [];

        foreach ($servers as $server) {
            // Normalize old structures to new consistent structure
            $normalized[] = [
                'id' => $server['id'] ?? uniqid('server_', true),
                'name' => $server['name'] ?? $server['server_name'] ?? 'Server',
                'url' => $server['url'] ?? $server['watch_link'] ?? null,
                'quality' => $server['quality'] ?? 'HD',
                'download_link' => $server['download_link'] ?? null,
                'sort_order' => $server['sort_order'] ?? 0,
                'active' => $server['active'] ?? $server['is_active'] ?? true,
            ];
        }

        // Sort by sort_order
        usort($normalized, function($a, $b) {
            return ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0);
        });

        return $normalized;
    }

    /**
     * Get active servers only
     */
    public function getActiveServers()
    {
        $servers = $this->getNormalizedServers();
        return array_filter($servers, function($server) {
            return ($server['active'] ?? true) === true;
        });
    }

    /**
     * Get all download links (from servers and content level)
     */
    public function getAllDownloadLinks()
    {
        $downloadLinks = [];
        
        // Get download links from servers
        foreach ($this->getActiveServers() as $server) {
            if (!empty($server['download_link'])) {
                $downloadLinks[] = [
                    'name' => $server['name'] . ' - ' . ($server['quality'] ?? 'HD'),
                    'url' => $server['download_link'],
                    'quality' => $server['quality'] ?? 'HD',
                ];
            }
        }
        
        // Add content-level download link if exists
        if (!empty($this->download_link)) {
            $downloadLinks[] = [
                'name' => 'Direct Download',
                'url' => $this->download_link,
                'quality' => 'HD',
            ];
        }
        
        return $downloadLinks;
    }
}
