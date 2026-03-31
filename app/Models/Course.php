<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
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
        'created_by',
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
     * Get the user who created this course.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the units for this course.
     */
    public function units()
    {
        return $this->hasMany(Unit::class, 'course_id');
    }

    /**
     * Get the students enrolled in this course (through units).
     */
    public function students()
    {
        return $this->hasManyThrough(
            StudentUnit::class,
            Unit::class,
            'course_id', // Foreign key on units table
            'unit_id',    // Foreign key on student_unit table
            'id',         // Local key on courses table
            'id'          // Local key on units table
        )->with('user');
    }
}