<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deadline extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'topic_id',
        'title',
        'description',
        'due_date',
        'type',
        'created_by'
    ];

    protected $casts = [
        'due_date' => 'datetime'
    ];

    /**
     * Get the unit that owns the deadline.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the topic that owns the deadline.
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get the lecturer who created the deadline.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the acceptances for this deadline.
     */
    public function acceptances()
    {
        return $this->hasMany(DeadlineAcceptance::class);
    }

    /**
     * Check if a specific student has accepted this deadline.
     */
    public function isAcceptedBy($studentId)
    {
        return $this->acceptances()
                    ->where('student_id', $studentId)
                    ->exists();
    }

    /**
     * Check if a specific student has declined this deadline.
     */
    public function isDeclinedBy($studentId)
    {
        return $this->acceptances()
                    ->where('student_id', $studentId)
                    ->where('status', 'declined')
                    ->exists();
    }

    /**
     * Get the acceptance status for a student.
     */
    public function getAcceptanceStatusFor($studentId)
    {
        $acceptance = $this->acceptances()
                           ->where('student_id', $studentId)
                           ->first();
        
        return $acceptance ? $acceptance->status : null;
    }

    /**
     * Get the type badge class.
     */
    public function getTypeBadgeAttribute()
    {
        return match($this->type) {
            'topic' => 'badge-info',
            'assignment' => 'badge-warning',
            default => 'badge-secondary'
        };
    }

    /**
     * Get the type icon.
     */
    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'topic' => 'fa-book-open',
            'assignment' => 'fa-pencil-alt',
            default => 'fa-clock'
        };
    }

    /**
     * Scope a query to only include topic deadlines.
     */
    public function scopeTopicDeadlines($query)
    {
        return $query->where('type', 'topic');
    }

    /**
     * Scope a query to only include general deadlines.
     */
    public function scopeGeneral($query)
    {
        return $query->where('type', 'general');
    }

    /**
     * Scope a query to only include assignment deadlines.
     */
    public function scopeAssignments($query)
    {
        return $query->where('type', 'assignment');
    }

    /**
     * Scope a query to only include deadlines for a specific topic.
     */
    public function scopeForTopic($query, $topicId)
    {
        return $query->where('topic_id', $topicId);
    }

    /**
     * Get the formatted due date.
     */
    public function getFormattedDueDateAttribute()
    {
        return $this->due_date->format('M d, Y');
    }

    /**
     * Get the due time.
     */
    public function getDueTimeAttribute()
    {
        return $this->due_date->format('h:i A');
    }

    /**
     * Check if the deadline is upcoming.
     */
    public function getIsUpcomingAttribute()
    {
        return $this->due_date->isFuture();
    }

    /**
     * Check if the deadline is overdue.
     */
    public function getIsOverdueAttribute()
    {
        return $this->due_date->isPast();
    }

    /**
     * Get days remaining until deadline.
     */
    public function getDaysRemainingAttribute()
    {
        if ($this->is_overdue) {
            return 0;
        }

        return now()->diffInDays($this->due_date);
    }
}