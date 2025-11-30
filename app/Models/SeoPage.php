<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoPage extends Model
{
    protected $fillable = [
        'page_key',
        'page_name',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_robots',
        'meta_author',
        'meta_language',
        'meta_geo_region',
        'meta_geo_placename',
        'meta_geo_position_lat',
        'meta_geo_position_lon',
        'meta_revisit_after',
        'og_title',
        'og_description',
        'og_image',
        'og_url',
        'og_type',
        'og_locale',
        'og_site_name',
        'og_video_url',
        'og_video_duration',
        'og_video_type',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'twitter_site',
        'twitter_creator',
        'canonical_url',
        'hreflang_tags',
        'schema_markup',
        'additional_meta_tags',
        'breadcrumb_schema',
        'preconnect_domains',
        'dns_prefetch_domains',
        'enable_amp',
        'amp_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'enable_amp' => 'boolean',
        'schema_markup' => 'array',
        'breadcrumb_schema' => 'array',
        'additional_meta_tags' => 'array',
        'hreflang_tags' => 'array',
        'meta_geo_position_lat' => 'decimal:8',
        'meta_geo_position_lon' => 'decimal:8',
        'og_video_duration' => 'integer',
    ];

    /**
     * Get SEO data by page key
     */
    public static function getByPageKey($pageKey)
    {
        return static::where('page_key', $pageKey)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get all available page keys
     */
    public static function getAvailablePageKeys()
    {
        return [
            'home' => 'Home Page',
            'movies.index' => 'Movies List Page',
            'movies.show' => 'Movie Detail Page',
            'tv-shows.index' => 'TV Shows List Page',
            'tv-shows.show' => 'TV Show Detail Page',
            'cast.index' => 'Cast List Page',
            'cast.show' => 'Cast Detail Page',
            'search' => 'Search Page',
            'dmca' => 'DMCA Page',
            'about' => 'About Us Page',
            'completed' => 'Completed TV Shows Page',
            'upcoming' => 'Upcoming Page',
        ];
    }
}

