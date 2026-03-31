<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceDownload extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'resource_downloads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'resource_id',
        'user_id',
        'ip_address',
        'user_agent',
        'downloaded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'downloaded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the resource that was downloaded.
     */
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    /**
     * Get the user who downloaded the resource.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include downloads from a specific resource.
     */
    public function scopeForResource($query, $resourceId)
    {
        return $query->where('resource_id', $resourceId);
    }

    /**
     * Scope a query to only include downloads by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include downloads within a date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get the IP address masked for privacy.
     */
    public function getMaskedIpAttribute()
    {
        if (!$this->ip_address) {
            return 'N/A';
        }
        
        // Mask the last octet for privacy
        $parts = explode('.', $this->ip_address);
        if (count($parts) === 4) {
            $parts[3] = '***';
            return implode('.', $parts);
        }
        
        return $this->ip_address;
    }

    /**
     * Get a short summary of the user agent.
     */
    public function getDeviceSummaryAttribute()
    {
        if (!$this->user_agent) {
            return 'Unknown';
        }
        
        $ua = $this->user_agent;
        
        if (strpos($ua, 'Windows') !== false) {
            return 'Windows';
        } elseif (strpos($ua, 'Mac') !== false) {
            return 'macOS';
        } elseif (strpos($ua, 'iPhone') !== false) {
            return 'iPhone';
        } elseif (strpos($ua, 'iPad') !== false) {
            return 'iPad';
        } elseif (strpos($ua, 'Android') !== false) {
            return 'Android';
        } elseif (strpos($ua, 'Linux') !== false) {
            return 'Linux';
        }
        
        return 'Unknown';
    }
}