<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'rating' => 'decimal:1',
        'genres' => 'array',
        'cast' => 'array',
        'is_featured' => 'boolean',
        'views' => 'integer',
        'episode_count' => 'integer',
        'sort_order' => 'integer',
    ];

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
