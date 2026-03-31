<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class StudentUnit extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'student_unit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'unit_id',
        'status',
        'enrolled_at',
        'completed_at',
        'is_custom',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'enrolled_at' => 'date',
        'completed_at' => 'date',
        'is_custom' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * Get the student (user) associated with this enrollment.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the unit associated with this enrollment.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the custom unit details if this is a custom unit.
     */
    public function customUnit()
    {
        return $this->belongsTo(CustomUnit::class, 'unit_id', 'id')
            ->where('is_custom', true);
    }

    /**
     * Scope a query to only include active enrollments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'enrolled');
    }

    /**
     * Scope a query to only include completed units.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include dropped units.
     */
    public function scopeDropped($query)
    {
        return $query->where('status', 'dropped');
    }

    /**
     * Scope a query to only include custom units.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_custom', true);
    }

    /**
     * Scope a query to only include system units (not custom).
     */
    public function scopeSystem($query)
    {
        return $query->where('is_custom', false);
    }

    /**
     * Scope a query to only include enrollments for a specific student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Mark this unit as completed.
     */
    public function markAsCompleted(): void
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    /**
     * Mark this unit as dropped.
     */
    public function markAsDropped(): void
    {
        $this->status = 'dropped';
        $this->save();
    }

    /**
     * Reactivate a dropped unit.
     */
    public function reactivate(): void
    {
        $this->status = 'enrolled';
        $this->completed_at = null;
        $this->save();
    }

    /**
     * Check if the unit is active.
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'enrolled';
    }

    /**
     * Check if the unit is completed.
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Get the status badge.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'enrolled' => '🟢 Enrolled',
            'completed' => '✅ Completed',
            'dropped' => '⚪ Dropped',
            default => '📌 ' . ucfirst($this->status),
        };
    }

    /**
     * Get the enrollment duration in days.
     */
    public function getEnrollmentDurationAttribute(): int
    {
        $endDate = $this->completed_at ?? now();
        return $this->enrolled_at->diffInDays($endDate);
    }

    /**
     * Get progress percentage (if unit has deadlines/study sessions).
     * This will be enhanced later with actual progress tracking.
     */
    public function getProgressPercentageAttribute(): int
    {
        // Placeholder - will be enhanced with actual progress logic
        if ($this->is_completed) {
            return 100;
        }

        // TODO: Calculate based on deadlines completed vs total
        // or study sessions completed vs expected

        return 0;
    }
}
