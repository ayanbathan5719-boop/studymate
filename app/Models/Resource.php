<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',      // Changed from 'type' to 'file_type'
        'file_size',
        'download_count', // Changed from 'downloads_count'
        'views_count',    // For tracking views
        'unit_id',
        'unit_code',      // For quick access to unit code
        'topic_id',
        'uploaded_by',    // This is the user who uploaded
        'user_id',        // Alternative user reference
        'is_official',
        'url',            // For link-type resources
        'source_id',      // For polymorphic relationship
        'source_type',    // For polymorphic relationship
    ];

    /**
     * Get the user who uploaded this resource.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the unit that this resource belongs to.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Get the topic that this resource belongs to.
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get the downloads for this resource.
     */
    public function downloads()
    {
        return $this->hasMany(ResourceDownload::class);
    }

    /**
     * Get the parent model (forum post or forum reply) that this resource belongs to.
     */
    public function source()
    {
        return $this->morphTo();
    }

    /**
     * Get the download count attribute.
     */
    public function getDownloadsCountAttribute()
    {
        return $this->download_count ?? 0;
    }

    /**
     * Get the views count attribute.
     */
    public function getViewsCountAttribute()
    {
        return $this->views_count ?? 0;
    }

    /**
     * Scope a query to only include resources of a given type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('file_type', $type);
    }

    /**
     * Scope a query to only include resources from a given unit.
     */
    public function scopeForUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    /**
     * Scope a query to only include resources from a given unit code.
     */
    public function scopeForUnitCode($query, $unitCode)
    {
        return $query->where('unit_code', $unitCode);
    }

    /**
     * Get the file size in human readable format.
     */
    public function getFileSizeFormattedAttribute()
    {
        if (!$this->file_size) {
            return 'N/A';
        }
        
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the icon class based on resource type.
     */
    public function getIconClassAttribute()
    {
        switch ($this->file_type) {
            case 'pdf':
                return 'fa-file-pdf';
            case 'video':
                return 'fa-video';
            case 'link':
                return 'fa-link';
            case 'document':
                return 'fa-file-word';
            case 'image':
                return 'fa-file-image';
            case 'audio':
                return 'fa-file-audio';
            default:
                return 'fa-file';
        }
    }

    /**
     * Get the color class based on resource type.
     */
    public function getColorClassAttribute()
    {
        switch ($this->file_type) {
            case 'pdf':
                return 'pdf';
            case 'video':
                return 'video';
            case 'link':
                return 'link';
            case 'document':
                return 'document';
            default:
                return 'default';
        }
    }

    /**
     * Check if this is a link resource.
     */
    public function isLink()
    {
        return $this->file_type === 'link';
    }

    /**
     * Check if this is a file resource.
     */
    public function isFile()
    {
        return $this->file_type !== 'link';
    }

    /**
     * Get the URL for accessing this resource.
     */
    public function getAccessUrlAttribute()
    {
        if ($this->isLink()) {
            return $this->url;
        }
        
        return asset('storage/' . $this->file_path);
    }

    /**
     * Increment the download count.
     */
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    /**
     * Increment the views count.
     */
    public function incrementViewsCount()
    {
        $this->increment('views_count');
    }
}