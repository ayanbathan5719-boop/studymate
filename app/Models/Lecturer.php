<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lecturer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'department',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user associated with this lecturer via email.
     * This links to the users table for authentication.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * Get the units taught by this lecturer.
     * Assuming units table has lecturer_id column.
     */
    public function units()
    {
        return $this->hasMany(Unit::class, 'lecturer_id');
    }

    /**
     * Get the deadlines created by this lecturer.
     * Assuming deadlines table has lecturer_id column.
     */
    public function deadlines()
    {
        return $this->hasMany(Deadline::class, 'lecturer_id');
    }

    /**
     * Get the forum posts created by this lecturer.
     * This links through the user relationship.
     */
    public function forumPosts()
    {
        return $this->hasManyThrough(
            ForumPost::class,
            User::class,
            'email', // Foreign key on users table
            'user_id', // Foreign key on forum_posts table
            'email', // Local key on lecturers table
            'id' // Local key on users table
        );
    }

    /**
     * Get the resources uploaded by this lecturer (official resources).
     */
    public function resources()
    {
        return $this->hasManyThrough(
            Resource::class,
            User::class,
            'email',
            'uploaded_by',
            'email',
            'id'
        )->where('is_official', true);
    }

    /**
     * Scope a query to only include lecturers from a specific department.
     */
    public function scopeInDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Get the count of units taught.
     */
    public function getUnitsCountAttribute(): int
    {
        return $this->units()->count();
    }

    /**
     * Get the count of active deadlines.
     */
    public function getActiveDeadlinesCountAttribute(): int
    {
        return $this->deadlines()
            ->where('due_date', '>', now())
            ->count();
    }

    /**
     * Check if this lecturer teaches a specific unit.
     */
    public function teachesUnit($unitId): bool
    {
        return $this->units()->where('id', $unitId)->exists();
    }
}
