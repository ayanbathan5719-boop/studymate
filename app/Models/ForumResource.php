<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'forum_post_id',
        'user_id',
        'unit_code',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'url',
    ];

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'forum_post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIconClassAttribute()
    {
        switch ($this->type) {
            case 'file':
            case 'document':
                return 'fa-file-alt';
            case 'link':
                return 'fa-link';
            case 'video':
                return 'fa-video';
            default:
                return 'fa-paperclip';
        }
    }
}