<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomUnit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'name',
        'description',
        'color',
        'icon',
        'goal_minutes',
        'progress'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'goal_minutes' => 'integer',
        'progress' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the student who owns this custom unit.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the study sessions for this custom unit.
     */
    public function studySessions()
    {
        return $this->hasMany(StudySession::class, 'custom_unit_id');
    }

    /**
     * Get the resources for this custom unit.
     */
    public function resources()
    {
        return $this->hasMany(Resource::class, 'custom_unit_id');
    }

    /**
     * Get the deadlines for this custom unit.
     */
    public function deadlines()
    {
        return $this->hasMany(Deadline::class, 'custom_unit_id');
    }

    /**
     * Get progress percentage.
     */
    public function getProgressPercentageAttribute()
    {
        if (!$this->goal_minutes) return 0;
        return min(round(($this->progress / $this->goal_minutes) * 100), 100);
    }
}