<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'notification_preferences',
        'avatar',
        'is_flagged',
        'flag_count',
        'last_flag_at',
        'forum_access',
        'forum_restricted_until'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_preferences' => 'array',
            'forum_restricted_until' => 'datetime',
            'last_flag_at' => 'datetime',
        ];
    }

    /**
     * =============================================
     * NOTIFICATION RELATIONSHIPS
     * =============================================
     */

    /**
     * Get the notifications for the user.
     */
    public function notifications()
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the user's unread notifications.
     */
    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    /**
     * Get the user's read notifications.
     */
    public function readNotifications()
    {
        return $this->notifications()->whereNotNull('read_at');
    }

    /**
     * =============================================
     * LECTURER RELATIONSHIPS
     * =============================================
     */

    /**
     * Get the lecturer profile associated with this user.
     * Links via email field.
     */
    public function lecturerProfile()
    {
        return $this->hasOne(Lecturer::class, 'email', 'email');
    }

    /**
     * Check if user has a lecturer profile.
     */
    public function getHasLecturerProfileAttribute(): bool
    {
        return $this->lecturerProfile()->exists();
    }

    /**
     * Check if user is a lecturer (via Spatie role).
     */
    public function getIsLecturerAttribute(): bool
    {
        return $this->hasRole('lecturer');
    }

    /**
     * Check if user is a student (via Spatie role).
     */
    public function getIsStudentAttribute(): bool
    {
        return $this->hasRole('student');
    }

    /**
     * Check if user is an admin (via Spatie role).
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Get the role badge for the user.
     * Professional version without emojis
     */
    public function getRoleBadgeAttribute(): string
    {
        if ($this->is_admin) {
            return 'Administrator';
        }
        if ($this->is_lecturer) {
            return 'Lecturer';
        }
        if ($this->is_student) {
            return 'Student';
        }
        return 'User';
    }

    /**
     * Get the role badge with color class for styling
     */
    public function getRoleBadgeClassAttribute(): string
    {
        if ($this->is_admin) {
            return 'role-badge-admin';
        }
        if ($this->is_lecturer) {
            return 'role-badge-lecturer';
        }
        if ($this->is_student) {
            return 'role-badge-student';
        }
        return 'role-badge-user';
    }

    /**
     * =============================================
     * UNIT RELATIONSHIPS
     * =============================================
     */

    /**
     * Get the units this lecturer is assigned to.
     */
    public function units()
    {
        return $this->belongsToMany(Unit::class, 'lecturer_unit', 'lecturer_id', 'unit_id')
                    ->withTimestamps();
    }

    /**
     * Get units assigned to this lecturer (alias for units()).
     * Alias for better semantic clarity when working with lecturer units.
     */
    public function assignedUnits()
    {
        return $this->belongsToMany(Unit::class, 'lecturer_unit', 'lecturer_id', 'unit_id')
                    ->withTimestamps();
    }

    /**
     * Get units taught by this lecturer (alias for units()).
     * Alternative semantic naming for clarity.
     */
    public function taughtUnits()
    {
        return $this->belongsToMany(Unit::class, 'lecturer_unit', 'lecturer_id', 'unit_id')
                    ->withTimestamps();
    }

    /**
     * Get the units this student is enrolled in.
     */
    public function enrolledUnits()
    {
        return $this->belongsToMany(Unit::class, 'student_unit', 'student_id', 'unit_id')
            ->withPivot('status', 'enrolled_at', 'completed_at', 'is_custom')
            ->withTimestamps();
    }

    /**
     * Get the student's active units (enrolled, not completed).
     */
    public function activeUnits()
    {
        return $this->enrolledUnits()
            ->wherePivot('status', 'enrolled');
    }

    /**
     * Get the student's completed units.
     */
    public function completedUnits()
    {
        return $this->enrolledUnits()
            ->wherePivot('status', 'completed');
    }

    /**
     * Get the custom units created by this student.
     */
    public function customUnits()
    {
        return $this->hasMany(CustomUnit::class, 'student_id');
    }

    /**
     * Check if a lecturer is assigned to a specific unit.
     */
    public function isAssignedToUnit($unitId): bool
    {
        return $this->units()->where('unit_id', $unitId)->exists();
    }

    /**
     * Check if a student is enrolled in a specific unit.
     */
    public function isEnrolledInUnit($unitId): bool
    {
        return $this->enrolledUnits()->where('unit_id', $unitId)->exists();
    }

    /**
     * Get all units (both assigned and enrolled based on role).
     */
    public function getAllUnits()
    {
        if ($this->is_lecturer) {
            return $this->units();
        }
        
        if ($this->is_student) {
            return $this->enrolledUnits();
        }
        
        return collect();
    }

    /**
     * =============================================
     * STUDY SESSION RELATIONSHIPS
     * =============================================
     */

    /**
     * Get the study sessions for this student.
     */
    public function studySessions()
    {
        return $this->hasMany(StudySession::class, 'student_id');
    }

    /**
     * Get today's total study time in seconds.
     */
    public function getTodayStudyTimeAttribute(): int
    {
        return $this->studySessions()
            ->whereDate('started_at', now()->toDateString())
            ->sum('duration_seconds');
    }

    /**
     * Get this week's total study time in seconds.
     */
    public function getWeekStudyTimeAttribute(): int
    {
        return $this->studySessions()
            ->whereBetween('started_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->sum('duration_seconds');
    }

    /**
     * Get formatted study time (hours and minutes).
     */
    public function getFormattedStudyTime(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' sec';
        }
        
        $minutes = floor($seconds / 60);
        if ($minutes < 60) {
            return $minutes . ' min';
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($remainingMinutes > 0) {
            return $hours . 'h ' . $remainingMinutes . 'm';
        }
        
        return $hours . ' hours';
    }

    /**
     * =============================================
     * FORUM RELATIONSHIPS
     * =============================================
     */

    /**
     * Get the forum posts created by this user.
     */
    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class, 'user_id');
    }

    /**
     * Get the forum replies created by this user.
     */
    public function forumReplies()
    {
        return $this->hasMany(ForumReply::class, 'user_id');
    }

    /**
     * Get the user's forum activity count.
     */
    public function getForumActivityCountAttribute(): int
    {
        return $this->forumPosts()->count() + $this->forumReplies()->count();
    }

    /**
     * =============================================
     * RESOURCE RELATIONSHIPS
     * =============================================
     */

    /**
     * Get the resources uploaded by this user.
     */
    public function resources()
    {
        return $this->hasMany(Resource::class, 'uploaded_by');
    }

    /**
     * Get total resources uploaded count.
     */
    public function getTotalResourcesAttribute(): int
    {
        return $this->resources()->count();
    }

    /**
     * =============================================
     * DEADLINE RELATIONSHIPS
     * =============================================
     */

    /**
     * Get the deadlines accepted by this student.
     */
    public function acceptedDeadlines()
    {
        return $this->belongsToMany(Deadline::class, 'deadline_student', 'student_id', 'deadline_id')
            ->withPivot('accepted_at', 'reminded_at', 'completed_at', 'score')
            ->withTimestamps();
    }

    /**
     * Get pending deadlines (accepted but not completed).
     */
    public function pendingDeadlines()
    {
        return $this->acceptedDeadlines()
            ->wherePivotNull('completed_at')
            ->where('due_date', '>', now());
    }

    /**
     * Get completed deadlines.
     */
    public function completedDeadlines()
    {
        return $this->acceptedDeadlines()
            ->wherePivotNotNull('completed_at');
    }

    /**
     * Get overdue deadlines (accepted but not completed and past due date).
     */
    public function overdueDeadlines()
    {
        return $this->acceptedDeadlines()
            ->wherePivotNull('completed_at')
            ->where('due_date', '<', now());
    }

    /**
     * Get deadline completion rate.
     */
    public function getDeadlineCompletionRateAttribute(): float
    {
        $total = $this->acceptedDeadlines()->count();
        $completed = $this->completedDeadlines()->count();
        
        if ($total === 0) {
            return 0;
        }
        
        return round(($completed / $total) * 100, 1);
    }

    /**
     * =============================================
     * AUDIT LOG RELATIONSHIPS
     * =============================================
     */

    /**
     * Get the audit logs for this user.
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * =============================================
     * FLAGGING RELATIONSHIPS
     * =============================================
     */

    /**
     * Get flags reported against this user.
     */
    public function flagsReceived()
    {
        return $this->hasMany(Flag::class, 'reported_user_id');
    }

    /**
     * Get flags this user has made.
     */
    public function flagsMade()
    {
        return $this->hasMany(Flag::class, 'reporter_id');
    }

    /**
     * Get pending flags count.
     */
    public function getPendingFlagsCountAttribute(): int
    {
        return $this->flagsReceived()->where('status', 'pending')->count();
    }

    /**
     * Check if user should be moderated (e.g., flagged 3+ times)
     */
    public function getNeedsModerationAttribute(): bool
    {
        return $this->pending_flags_count >= 3;
    }

    /**
     * Get flag status badge.
     * Professional version without emojis
     */
    public function getFlagStatusAttribute(): ?array
    {
        $count = $this->pending_flags_count;

        if ($count >= 5) {
            return ['badge' => 'Critical', 'color' => '#ef4444', 'class' => 'flag-critical'];
        } elseif ($count >= 3) {
            return ['badge' => 'Warning', 'color' => '#f59e0b', 'class' => 'flag-warning'];
        } elseif ($count >= 1) {
            return ['badge' => 'Flagged', 'color' => '#10b981', 'class' => 'flag-flagged'];
        }

        return null;
    }

    /**
     * Check if user has forum access.
     */
    public function getHasForumAccessAttribute(): bool
    {
        return $this->forum_access && ($this->forum_restricted_until === null || $this->forum_restricted_until->isPast());
    }

    /**
     * Get forum restriction status.
     */
    public function getForumRestrictionStatusAttribute(): ?string
    {
        if (!$this->forum_access) {
            return 'Permanently restricted';
        }

        if ($this->forum_restricted_until && $this->forum_restricted_until->isFuture()) {
            return 'Restricted until ' . $this->forum_restricted_until->format('M d, Y');
        }

        return null;
    }

    /**
     * =============================================
     * ADDITIONAL HELPER METHODS
     * =============================================
     */

    /**
     * Get user's full display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get user's initials for avatar.
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        
        return $initials ?: 'U';
    }

    /**
     * Check if user has any activity (posts, replies, resources).
     */
    public function getHasActivityAttribute(): bool
    {
        return $this->forumActivityCount > 0 || $this->total_resources > 0;
    }

    /**
     * Get user's join date formatted.
     */
    public function getJoinDateAttribute(): string
    {
        return $this->created_at->format('F j, Y');
    }
}