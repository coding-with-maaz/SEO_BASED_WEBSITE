<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Episode extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'content_id',
        'episode_number',
        'title',
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
}
