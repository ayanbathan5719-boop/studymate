<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
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
     * 🔒 PREVENT UPDATES AND DELETES - Enforce append-only
     */
    public static function boot()
    {
        parent::boot();

        // Prevent updating any audit log
        static::updating(function () {
            throw new \Exception('Audit logs cannot be updated. They are append-only.');
        });

        // Prevent deleting any audit log
        static::deleting(function () {
            throw new \Exception('Audit logs cannot be deleted. They are append-only.');
        });
    }

    /**
     * Get the user who performed this action.
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Scope a query to only include logs for a specific module.
     */
    public function scopeForModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope a query to only include logs for a specific action.
     */
    public function scopeWithAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to only include logs for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include logs within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include logs from today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now()->toDateString());
    }

    /**
     * Get a human-readable description of the action.
     */
    public function getActionDescriptionAttribute(): string
    {
        return match ($this->action) {
            'LOGIN' => 'User logged in',
            'LOGOUT' => 'User logged out',
            'CREATE' => 'Created new record',
            'UPDATE' => 'Updated record',
            'DELETE' => 'Deleted record',
            'DENY_ACCESS' => 'Denied access to user',
            'RESTORE_ACCESS' => 'Restored user access',
            'CONFIGURE' => 'Changed system settings',
            'GENERATE_REPORT' => 'Generated report',
            default => $this->action,
        };
    }

    /**
     * Get the module icon.
     */
    public function getModuleIconAttribute(): string
    {
        return match ($this->module) {
            'ADMIN' => '👑',
            'LECTURER' => '👨‍🏫',
            'STUDENT' => '🎓',
            'AUTH' => '🔐',
            'FORUM' => '💬',
            'RESOURCE' => '📁',
            'DEADLINE' => '⏰',
            'COURSE' => '📚',
            'UNIT' => '📖',
            default => '📋',
        };
    }

    /**
     * Get the action color class (for UI).
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'LOGIN' => 'green',
            'LOGOUT' => 'gray',
            'CREATE' => 'blue',
            'UPDATE' => 'yellow',
            'DELETE' => 'red',
            'DENY_ACCESS' => 'red',
            'RESTORE_ACCESS' => 'green',
            'CONFIGURE' => 'purple',
            'GENERATE_REPORT' => 'indigo',
            default => 'gray',
        };
    }

    /**
     * Get formatted old data for display.
     */
    public function getFormattedOldDataAttribute(): ?array
    {
        if (empty($this->old_data)) {
            return null;
        }

        // Remove sensitive data
        $data = $this->old_data;
        if (isset($data['password'])) {
            $data['password'] = '[REDACTED]';
        }
        if (isset($data['remember_token'])) {
            $data['remember_token'] = '[REDACTED]';
        }

        return $data;
    }

    /**
     * Get formatted new data for display.
     */
    public function getFormattedNewDataAttribute(): ?array
    {
        if (empty($this->new_data)) {
            return null;
        }

        // Remove sensitive data
        $data = $this->new_data;
        if (isset($data['password'])) {
            $data['password'] = '[REDACTED]';
        }
        if (isset($data['remember_token'])) {
            $data['remember_token'] = '[REDACTED]';
        }

        return $data;
    }

    /**
     * Log an action (helper method for controllers).
     */
    public static function log($userId, $action, $module, $description = null, $oldData = null, $newData = null)
    {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
