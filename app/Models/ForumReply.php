<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'content',
        'user_id',
        'forum_post_id',
        'parent_id',
        'edited_at',
        'edit_count',
        'original_content',
    ];

    protected $casts = [
        'edited_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the post that this reply belongs to.
     */
    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'forum_post_id');
    }

    /**
     * Get the user who created this reply.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent reply (for nested replies).
     */
    public function parent()
    {
        return $this->belongsTo(ForumReply::class, 'parent_id');
    }

    /**
     * Get the child replies (for nested replies).
     */
    public function children()
    {
        return $this->hasMany(ForumReply::class, 'parent_id');
    }

    /**
     * Get the resources attached to this reply (Polymorphic Relationship).
     */
    public function resources()
    {
        return $this->morphMany(Resource::class, 'source');
    }

    /**
     * Check if the reply has been edited.
     */
    public function isEdited(): bool
    {
        return !is_null($this->edited_at);
    }

    /**
     * Check if the reply can still be edited (within 5 minutes).
     */
    public function canEdit(): bool
    {
        if (!$this->isEdited() && $this->created_at->diffInMinutes(now()) <= 5) {
            return true;
        }
        
        return false;
    }

    /**
     * Get the edited time formatted.
     */
    public function getEditedTimeAttribute(): ?string
    {
        if ($this->edited_at) {
            return $this->edited_at->diffForHumans();
        }
        return null;
    }

    /**
     * Get the edit count display.
     */
    public function getEditCountDisplayAttribute(): string
    {
        if ($this->edit_count > 0) {
            return $this->edit_count . ' edit' . ($this->edit_count > 1 ? 's' : '');
        }
        return '';
    }

    /**
     * Scope to get top-level replies (no parent).
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get replies for a specific post.
     */
    public function scopeForPost($query, $postId)
    {
        return $query->where('forum_post_id', $postId);
    }
}