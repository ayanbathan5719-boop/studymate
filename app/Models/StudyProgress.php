<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyProgress extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'study_progress';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'user_id',
        'unit_code',
        'topic_id',
        'resource_id',
        'duration_minutes',
        'completed',
        'completed_at',
        'notes',
        'last_studied_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
        'last_studied_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the student that owns this progress record.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the user that owns this progress record (alternative to student).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the unit that this progress record belongs to.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_code', 'code');
    }

    /**
     * Get the topic that this progress record belongs to.
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get the resource that this progress record belongs to.
     */
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    /**
     * Scope a query to only include progress for a specific student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to only include progress for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include progress for a specific unit code.
     */
    public function scopeForUnitCode($query, $unitCode)
    {
        return $query->where('unit_code', $unitCode);
    }

    /**
     * Scope a query to only include progress for a specific unit.
     */
    public function scopeForUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    /**
     * Scope a query to only include progress for a specific topic.
     */
    public function scopeForTopic($query, $topicId)
    {
        return $query->where('topic_id', $topicId);
    }

    /**
     * Scope a query to only include completed items.
     */
    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    /**
     * Scope a query to only include incomplete items.
     */
    public function scopeIncomplete($query)
    {
        return $query->where('completed', false);
    }

    /**
     * Get the total study time for a student (in hours).
     */
    public static function getTotalStudyHours($studentId)
    {
        $minutes = self::forStudent($studentId)->sum('duration_minutes');
        return round($minutes / 60, 1);
    }

    /**
     * Get the total study time for a user (in hours).
     */
    public static function getTotalStudyHoursForUser($userId)
    {
        $minutes = self::forUser($userId)->sum('duration_minutes');
        return round($minutes / 60, 1);
    }

    /**
     * Get the current streak for a student.
     */
    public static function getCurrentStreak($studentId)
    {
        $progress = self::forStudent($studentId)
            ->selectRaw('DATE(last_studied_at) as date')
            ->whereNotNull('last_studied_at')
            ->distinct()
            ->orderBy('date', 'desc')
            ->get()
            ->pluck('date');
        
        if ($progress->isEmpty()) {
            return 0;
        }
        
        $streak = 1;
        $currentDate = now()->startOfDay();
        
        // Check if studied today
        if (!$progress->contains($currentDate->toDateString())) {
            return 0;
        }
        
        // Count consecutive days
        while ($progress->contains($currentDate->subDay()->toDateString())) {
            $streak++;
        }
        
        return $streak;
    }

    /**
     * Get the current streak for a user.
     */
    public static function getCurrentStreakForUser($userId)
    {
        $progress = self::forUser($userId)
            ->selectRaw('DATE(last_studied_at) as date')
            ->whereNotNull('last_studied_at')
            ->distinct()
            ->orderBy('date', 'desc')
            ->get()
            ->pluck('date');
        
        if ($progress->isEmpty()) {
            return 0;
        }
        
        $streak = 1;
        $currentDate = now()->startOfDay();
        
        // Check if studied today
        if (!$progress->contains($currentDate->toDateString())) {
            return 0;
        }
        
        // Count consecutive days
        while ($progress->contains($currentDate->subDay()->toDateString())) {
            $streak++;
        }
        
        return $streak;
    }
}