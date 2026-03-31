<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudySession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'unit_id',
        'resource_id',
        'started_at',
        'ended_at',
        'duration_seconds',
        'status',
        'session_type',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration_seconds' => 'integer',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'metadata',
    ];

    /**
     * Get the student who owns this study session.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the unit being studied.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the resource being studied.
     */
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    /**
     * Scope a query to only include active sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include completed sessions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include sessions for a specific student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to only include sessions for a specific unit.
     */
    public function scopeForUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    /**
     * Scope a query to only include sessions within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('started_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include sessions from today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('started_at', now()->toDateString());
    }

    /**
     * Scope a query to only include sessions from this week.
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('started_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Get formatted duration (e.g., "2 hours 30 minutes").
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration_seconds) {
            return '0 minutes';
        }

        $hours = floor($this->duration_seconds / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);

        if ($hours > 0) {
            return $hours . ' hour' . ($hours > 1 ? 's' : '') .
                ($minutes > 0 ? ' ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '') : '');
        }

        return $minutes . ' minute' . ($minutes > 1 ? 's' : '');
    }

    /**
     * Get short duration (e.g., "2h 30m").
     */
    public function getShortDurationAttribute(): string
    {
        if (!$this->duration_seconds) {
            return '0m';
        }

        $hours = floor($this->duration_seconds / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);

        $parts = [];
        if ($hours > 0) {
            $parts[] = $hours . 'h';
        }
        if ($minutes > 0) {
            $parts[] = $minutes . 'm';
        }

        return implode(' ', $parts);
    }

    /**
     * End the session and calculate duration.
     */
    public function endSession(): void
    {
        $this->ended_at = now();
        $this->duration_seconds = now()->diffInSeconds($this->started_at);
        $this->status = 'completed';
        $this->save();
    }

    /**
     * Abandon the session (user closed without proper end).
     */
    public function abandon(): void
    {
        $this->status = 'abandoned';
        $this->save();
    }

    /**
     * Check if session is still active.
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if session is completed.
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Get the session type icon.
     */
    public function getTypeIconAttribute(): string
    {
        return match ($this->session_type) {
            'resource' => '📖',
            'forum' => '💬',
            'custom' => '📌',
            default => '📚',
        };
    }

    /**
     * Get motivational message based on study time.
     */
    public function getMotivationalMessageAttribute(): ?string
    {
        $totalSeconds = $this->duration_seconds ?? 0;

        if ($totalSeconds < 600) { // Less than 10 minutes
            return "Great start! Every minute counts. 🔥";
        }

        if ($totalSeconds < 1800) { // Less than 30 minutes
            return "You're making progress! Keep going! 💪";
        }

        if ($totalSeconds < 3600) { // Less than 1 hour
            return "Awesome focus session! You're on fire! 🚀";
        }

        return "Incredible dedication! You're crushing it! 🌟";
    }
}
