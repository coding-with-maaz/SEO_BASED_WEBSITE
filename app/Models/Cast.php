<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Cast extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'profile_path',
        'biography',
        'birthday',
        'birthplace',
    ];

    protected $casts = [
        'birthday' => 'date',
    ];

    /**
     * Boot the model and generate slug automatically.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cast) {
            if (empty($cast->slug)) {
                $cast->slug = $cast->generateUniqueSlug();
            }
        });

        static::updating(function ($cast) {
            if ($cast->isDirty('name') && empty($cast->slug)) {
                $cast->slug = $cast->generateUniqueSlug();
            }
        });

        // Clear sitemap cache when cast is saved or deleted
        static::saved(function ($cast) {
            if (app()->bound(\App\Services\SitemapService::class)) {
                app(\App\Services\SitemapService::class)->clearCache();
            }
        });

        static::deleted(function ($cast) {
            if (app()->bound(\App\Services\SitemapService::class)) {
                app(\App\Services\SitemapService::class)->clearCache();
            }
        });
    }

    /**
     * Generate a unique slug from the name.
     */
    public function generateUniqueSlug()
    {
        $slug = Str::slug($this->name);
        $originalSlug = $slug;
        $count = 1;

        // Check for existing slugs including soft-deleted ones (to avoid conflicts)
        while (static::withTrashed()->where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
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
     * Get all content (movies/TV shows) this cast member is in
     */
    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_cast')
            ->withPivot('character', 'order')
            ->withTimestamps()
            ->orderByPivot('order', 'asc');
    }

    /**
     * Search casts by name
     */
    public static function searchByName(string $query)
    {
        return static::where('name', 'like', '%' . $query . '%')
            ->orderBy('name')
            ->get();
    }
}
