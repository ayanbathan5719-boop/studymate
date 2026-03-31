<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumPost extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'user_id',
        'unit_id',
        'unit_code',
        'tags',
        'views',
        'is_pinned',
        'is_announcement',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tags' => 'array',
        'views' => 'integer',
        'is_pinned' => 'boolean',
        'is_announcement' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Get the user who created this post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the unit this post belongs to (via ID).
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the unit associated with this post via unit_code.
     */
    public function unitByCode()
    {
        return $this->belongsTo(Unit::class, 'unit_code', 'code');
    }

    /**
     * Get the replies for this post.
     */
    public function replies()
    {
        return $this->hasMany(ForumReply::class);
    }

    /**
     * Get the flags for this forum post.
     */
    public function flags()
    {
        return $this->hasMany(Flag::class, 'forum_post_id');
    }

    /**
     * Get the resources attached to this post (Polymorphic Relationship).
     */
    public function resources()
    {
        return $this->morphMany(Resource::class, 'source');
    }

    /**
     * Get the count of replies.
     */
    public function getRepliesCountAttribute(): int
    {
        return $this->replies()->count();
    }

    /**
     * Increment views count.
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    /**
     * Scope a query to only include pinned posts.
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope a query to only include announcements.
     */
    public function scopeAnnouncements($query)
    {
        return $query->where('is_announcement', true);
    }

    /**
     * Scope a query to filter by tag.
     */
    public function scopeWithTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    /**
     * Scope a query to filter by unit code.
     */
    public function scopeForUnitCode($query, $unitCode)
    {
        return $query->where('unit_code', $unitCode);
    }

    /**
     * Check if the post was created by a lecturer.
     */
    public function getIsLecturerPostAttribute(): bool
    {
        return $this->user && $this->user->hasRole('lecturer');
    }

    /**
     * Check if the post was created by an admin.
     */
    public function getIsAdminPostAttribute(): bool
    {
        return $this->user && $this->user->hasRole('admin');
    }

    /**
     * Get the post's role badge.
     */
    public function getRoleBadgeAttribute(): string
    {
        if ($this->is_admin_post) {
            return 'Admin';
        }
        if ($this->is_lecturer_post) {
            return 'Lecturer';
        }
        return 'Student';
    }
}