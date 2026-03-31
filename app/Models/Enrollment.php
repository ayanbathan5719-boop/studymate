<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'student_unit';

    protected $fillable = [
        'student_id',
        'unit_id',
        'status',
        'enrolled_at',
        'approved_by',
        'approved_at',
        'rejected_reason',
        'completed_at',
        'is_custom'
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_custom' => 'boolean',
    ];

    /**
     * Get the student who enrolled.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the unit that was enrolled.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the admin who approved/rejected this enrollment.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if enrollment is approved.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if enrollment is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if enrollment is rejected.
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Approve this enrollment.
     */
    public function approve($adminId = null)
    {
        $this->status = 'approved';
        $this->approved_by = $adminId ?? Auth::id();
        $this->approved_at = now();
        $this->rejected_reason = null;
        $this->save();

        return $this;
    }

    /**
     * Reject this enrollment.
     */
    public function reject($reason = null, $adminId = null)
    {
        $this->status = 'rejected';
        $this->approved_by = $adminId ?? Auth::id();
        $this->approved_at = now();
        $this->rejected_reason = $reason;
        $this->save();

        return $this;
    }

    /**
     * Scope a query to only include approved enrollments.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending enrollments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include rejected enrollments.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}