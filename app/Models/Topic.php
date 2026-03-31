<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_code',
        'title',
        'description',
        'order',
        'status',
        'video_url',
        'content',
        'attachments',
        'estimated_minutes'
    ];

    protected $casts = [
        'attachments' => 'array',
        'estimated_minutes' => 'integer',
        'order' => 'integer'
    ];

    /**
     * Get the unit that owns the topic.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_code', 'code');
    }

    /**
     * Get the lecturer who created this topic.
     */
    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    /**
     * Get the resources for this topic.
     */
    public function resources()
    {
        return $this->hasMany(Resource::class, 'topic_id');
    }

    /**
     * Get the deadlines for this topic.
     */
    public function deadlines()
    {
        return $this->hasMany(Deadline::class, 'topic_id');
    }

    /**
     * Scope a query to only include published topics.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to order by the 'order' field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get the formatted estimated time.
     */
    public function getFormattedTimeAttribute()
    {
        if (!$this->estimated_minutes) {
            return 'No time estimate';
        }

        $hours = floor($this->estimated_minutes / 60);
        $minutes = $this->estimated_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . ($minutes > 0 ? $minutes . 'm' : '');
        }

        return $minutes . ' minutes';
    }

    /**
     * Get the video embed URL if it's a YouTube video.
     */
    public function getVideoEmbedAttribute()
    {
        if (!$this->video_url) {
            return null;
        }

        // Check if it's a YouTube URL
        if (str_contains($this->video_url, 'youtube.com') || str_contains($this->video_url, 'youtu.be')) {
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->video_url, $matches);
            $videoId = $matches[1] ?? null;
            
            if ($videoId) {
                return "https://www.youtube.com/embed/{$videoId}";
            }
        }

        return $this->video_url;
    }

    /**
     * Get the progress for a specific student.
     */
    public function getProgressForStudent($studentId)
    {
        // This will be implemented when we add study tracking
        return 0;
    }

    /**
     * Check if topic is completed by a student.
     */
    public function isCompletedBy($studentId)
    {
        // This will be implemented when we add study tracking
        return false;
    }
}