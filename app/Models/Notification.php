<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
        'action_url',
        'icon',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread()
    {
        if (!is_null($this->read_at)) {
            $this->update(['read_at' => null]);
        }
    }

    /**
     * Determine if the notification has been read.
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Get the notification icon class.
     */
    public function getIconClassAttribute(): string
    {
        $icons = [
            'info' => 'fas fa-info-circle',
            'success' => 'fas fa-check-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'danger' => 'fas fa-times-circle',
            'deadline' => 'fas fa-clock',
            'forum' => 'fas fa-comment',
            'flag' => 'fas fa-flag',
            'resource' => 'fas fa-file',
        ];

        return $icons[$this->icon] ?? 'fas fa-bell';
    }

    /**
     * Get the notification background color class.
     */
    public function getBgColorAttribute(): string
    {
        $colors = [
            'info' => 'bg-blue-100 text-blue-800',
            'success' => 'bg-green-100 text-green-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'danger' => 'bg-red-100 text-red-800',
            'deadline' => 'bg-orange-100 text-orange-800',
            'forum' => 'bg-purple-100 text-purple-800',
            'flag' => 'bg-red-100 text-red-800',
            'resource' => 'bg-indigo-100 text-indigo-800',
        ];

        return $colors[$this->icon] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get formatted time ago.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}