<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSeo extends Model
{
    protected $table = 'page_seos';
    
    protected $fillable = [
        'page_key',
        'page_name',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_robots',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'og_url',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'canonical_url',
        'schema_markup',
        'hreflang_tags',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'hreflang_tags' => 'array',
    ];

    /**
     * Get SEO data by page key
     */
    public static function getByPageKey(string $pageKey): ?self
    {
        // Always get fresh data from database (no cache, no model caching)
        return static::withoutGlobalScopes()
            ->where('page_key', $pageKey)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get available page keys
     */
    public static function getAvailablePageKeys(): array
    {
        return [
            'home' => 'Home Page',
            'movies.index' => 'Movies Listing',
            'tv-shows.index' => 'TV Shows Listing',
            'cast.index' => 'Cast Listing',
            'search' => 'Search Page',
            'about' => 'About Page',
            'dmca' => 'DMCA Page',
            'completed' => 'Completed TV Shows',
            'upcoming' => 'Upcoming Content',
        ];
    }

    /**
     * Get schema markup as array
     */
    public function getSchemaMarkupArray(): ?array
    {
        if (!$this->schema_markup) {
            return null;
        }

        $decoded = json_decode($this->schema_markup, true);
        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Clear all caches when PageSeo is saved or deleted
        static::saved(function ($pageSeo) {
            // Clear sitemap cache
            if (app()->bound(\App\Services\SitemapService::class)) {
                app(\App\Services\SitemapService::class)->clearCache();
            }
            // Clear application cache
            \Illuminate\Support\Facades\Cache::flush();
            // Clear compiled view files for this specific view
            $viewPath = base_path('storage/framework/views');
            if (file_exists($viewPath)) {
                $files = glob($viewPath . '/*.php');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        @unlink($file);
                    }
                }
            }
        });

        static::deleted(function ($pageSeo) {
            // Clear sitemap cache
            if (app()->bound(\App\Services\SitemapService::class)) {
                app(\App\Services\SitemapService::class)->clearCache();
            }
            // Clear application cache
            \Illuminate\Support\Facades\Cache::flush();
            // Clear compiled view files
            $viewPath = base_path('storage/framework/views');
            if (file_exists($viewPath)) {
                $files = glob($viewPath . '/*.php');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        @unlink($file);
                    }
                }
            }
        });
    }
}
