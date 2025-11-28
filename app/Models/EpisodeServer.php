<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EpisodeServer extends Model
{
    protected $fillable = [
        'episode_id',
        'server_name',
        'quality',
        'download_link',
        'watch_link',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the episode that owns this server
     */
    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }
}
