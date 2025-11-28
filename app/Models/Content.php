<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Content extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
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
}
