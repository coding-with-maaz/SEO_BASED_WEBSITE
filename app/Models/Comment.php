<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'content_id',
        'name',
        'email',
        'comment',
        'parent_id',
        'status',
        'ip_address',
        'user_agent',
        'likes',
        'dislikes',
        'is_pinned',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'is_pinned' => 'boolean',
        'likes' => 'integer',
        'dislikes' => 'integer',
    ];

    /**
     * Get the content that owns the comment.
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    /**
     * Get the parent comment.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get the child comments (replies).
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->where('status', 'approved')->orderBy('created_at', 'asc');
    }

    /**
     * Get all replies including pending ones (for admin).
     */
    public function allReplies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    /**
     * Scope a query to only include approved comments.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending comments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include top-level comments (no parent).
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the depth level of nested comment.
     */
    public function getDepthAttribute(): int
    {
        if (!$this->parent_id) {
            return 0;
        }
        
        $depth = 0;
        $parent = $this->parent;
        while ($parent && $depth < 10) { // Safety limit
            $depth++;
            $parent = $parent->parent;
        }
        return $depth;
    }
    
    /**
     * Calculate depth without accessing parent relationship (for performance).
     */
    public function calculateDepth(): int
    {
        if (!$this->parent_id) {
            return 0;
        }
        
        $depth = 0;
        $parentId = $this->parent_id;
        $maxDepth = 10; // Safety limit
        
        while ($parentId && $depth < $maxDepth) {
            $parent = Comment::find($parentId);
            if (!$parent) break;
            $depth++;
            $parentId = $parent->parent_id;
        }
        
        return $depth;
    }

    /**
     * Check if comment is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if comment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Approve the comment.
     */
    public function approve(): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject the comment.
     */
    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Mark as spam.
     */
    public function markAsSpam(): void
    {
        $this->update(['status' => 'spam']);
    }
}
