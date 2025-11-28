<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'cast',
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
        'cast' => 'array',
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
}
