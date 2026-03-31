<?php

namespace App\Notifications;

use App\Models\ForumPost;
use App\Models\Flag;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PostFlagged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $flag;
    protected $post;
    protected $reporter;

    public function __construct(Flag $flag, ForumPost $post, User $reporter)
    {
        $this->flag = $flag;
        $this->post = $post;
        $this->reporter = $reporter;
    }

    public function via($notifiable)
    {
        $channels = ['database'];
        
        // Admins always get email for flags
        if ($notifiable->hasRole('admin')) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🚩 Post Flagged: ' . $this->post->title)
            ->greeting('Moderation Alert!')
            ->line('A post has been flagged by ' . $this->reporter->name)
            ->line('Post: "' . $this->post->title . '"')
            ->line('Reason: ' . $this->flag->reason)
            ->line('Unit: ' . $this->post->unit_code)
            ->line('Author: ' . $this->post->user->name)
            ->action('Review Flag', url('/admin/forum/flags'))
            ->line('Please review this flag at your earliest convenience.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'post_flagged',
            'flag_id' => $this->flag->id,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'reporter_id' => $this->reporter->id,
            'reporter_name' => $this->reporter->name,
            'reason' => $this->flag->reason,
            'unit_code' => $this->post->unit_code,
            'post_author' => $this->post->user->name,
            'created_at' => now()->toISOString()
        ];
    }
}