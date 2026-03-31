<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Unit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'course_id',
        'lecturer_id',
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
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::saving(function ($unit) {
            if ($unit->code) {
                $unit->code = strtoupper($unit->code);
            }
        });
    }

    /**
     * Validation rules for unit codes.
     */
    public static function validateUnitCode($code, $unitId = null)
    {
        $validator = Validator::make(['code' => $code], [
            'code' => [
                'required',
                'string',
                'max:10',
                'regex:/^[A-Z]{2,4}\d{4}$/', // Format: 2-4 letters followed by 4 digits (e.g., BIT2204)
                Rule::unique('units', 'code')->ignore($unitId),
            ],
        ], [
            'code.regex' => 'The unit code must be in the format: 2-4 letters followed by 4 digits (e.g., BIT2204, ICT2101)',
            'code.unique' => 'This unit code is already in use.',
        ]);

        return $validator;
    }

    /**
     * Get the course that this unit belongs to.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the lecturer assigned to this unit.
     */
    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    // Add this method for unit's lecturers
    public function lecturers()
    {
        return $this->belongsToMany(User::class, 'lecturer_unit', 'unit_id', 'lecturer_id')
                    ->withTimestamps();
    }

    // Add this method for unit's enrolled students
    public function students()
    {
        return $this->belongsToMany(User::class, 'student_unit', 'unit_id', 'student_id')
                    ->withTimestamps();
    }

    /**
     * Get the resources for this unit.
     */
    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    /**
     * Get the forum posts for this unit (via unit_id).
     */
    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    /**
     * Get forum posts for this unit by unit code.
     */
    public function forumPostsByCode()
    {
        return $this->hasMany(ForumPost::class, 'unit_code', 'code');
    }

    /**
     * Get the deadlines for this unit.
     */
    public function deadlines()
    {
        return $this->hasMany(Deadline::class);
    }

    /**
     * Get the topics for this unit.
     */
    public function topics()
    {
        return $this->hasMany(Topic::class, 'unit_code', 'code');
    }

    /**
     * Get the study sessions for this unit.
     */
    public function studySessions()
    {
        return $this->hasMany(StudySession::class);
    }

    /**
     * Get the students enrolled in this unit.
     */
    // public function students()
    // {
    //     return $this->belongsToMany(User::class, 'student_unit', 'unit_id', 'student_id')
    //         ->withPivot('status', 'enrolled_at', 'completed_at', 'is_custom')
    //         ->withTimestamps();
    // }

    /**
     * Get the courses this unit belongs to (for many-to-many relationship).
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_unit')
            ->withTimestamps();
    }

    /**
     * Scope a query to get units with their forum posts by code.
     */
    public function scopeWithForumPosts($query)
    {
        return $query->with('forumPostsByCode');
    }

    /**
     * Get count of forum posts for this unit.
     */
    public function getForumPostsCountAttribute(): int
    {
        return $this->forumPostsByCode()->count();
    }

    /**
     * Get recent forum posts for this unit.
     */
    public function getRecentForumPosts($limit = 5)
    {
        return $this->forumPostsByCode()
            ->latest()
            ->limit($limit)
            ->get();
    }
}