<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'user_id',
        'unit_id',
        'title',
        'url',
        'description',
        'type',
        'clicks',
    ];

    protected $casts = [
        'clicks' => 'integer',
    ];

    /**
     * Get the student who created this link.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the user who created this link.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the unit this link belongs to.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Increment click count.
     */
    public function incrementClicks()
    {
        $this->increment('clicks');
    }
}