<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Episode extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'content_id',
        'episode_number',
        'title',
        'slug',
        'description',
        'thumbnail_path',
        'air_date',
        'duration',
        'views',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'air_date' => 'date',
        'is_published' => 'boolean',
        'views' => 'integer',
        'duration' => 'integer',
        'episode_number' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the content that owns the episode
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    /**
     * Get the servers for this episode
     */
    public function servers(): HasMany
    {
        return $this->hasMany(EpisodeServer::class)->orderBy('sort_order', 'asc');
    }

    /**
     * Scope for published episodes
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope ordered by episode number
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('episode_number', 'asc');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($episode) {
            if (empty($episode->slug)) {
                $episode->slug = $episode->generateUniqueSlug();
            }
        });

        static::updating(function ($episode) {
            if ($episode->isDirty('title') && empty($episode->slug)) {
                $episode->slug = $episode->generateUniqueSlug();
            }
        });

        // Clear sitemap cache when episode is saved or deleted
        static::saved(function ($episode) {
            if (app()->bound(\App\Services\SitemapService::class)) {
                app(\App\Services\SitemapService::class)->clearCache();
            }
        });

        static::deleted(function ($episode) {
            if (app()->bound(\App\Services\SitemapService::class)) {
                app(\App\Services\SitemapService::class)->clearCache();
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

        while (static::where('slug', $slug)
            ->where('content_id', $this->content_id)
            ->where('id', '!=', $this->id ?? 0)
            ->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
