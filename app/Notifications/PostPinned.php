<?php

namespace App\Notifications;

use App\Models\ForumPost;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PostPinned extends Notification implements ShouldQueue
{
    use Queueable;

    protected $post;
    protected $pinnedBy;

    public function __construct(ForumPost $post, User $pinnedBy)
    {
        $this->post = $post;
        $this->pinnedBy = $pinnedBy;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('📌 Your Post was Pinned: ' . $this->post->title)
            ->greeting('Congratulations ' . $notifiable->name . '!')
            ->line('Your post "' . $this->post->title . '" has been pinned by ' . $this->pinnedBy->name)
            ->line('Pinned posts are highlighted and stay at the top of the forum.')
            ->action('View Post', url('/forum/' . $this->post->id))
            ->line('Keep up the great contributions!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'post_pinned',
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'pinned_by_id' => $this->pinnedBy->id,
            'pinned_by_name' => $this->pinnedBy->name,
            'unit_code' => $this->post->unit_code,
            'created_at' => now()->toISOString()
        ];
    }
}