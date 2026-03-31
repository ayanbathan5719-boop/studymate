<?php

namespace App\Notifications;

use App\Models\ForumPost;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewForumPost extends Notification implements ShouldQueue
{
    use Queueable;

    protected $post;
    protected $author;

    public function __construct(ForumPost $post, User $author)
    {
        $this->post = $post;
        $this->author = $author;
    }

    public function via($notifiable)
    {
        $channels = ['database'];
        
        // Check if user wants email notifications for new posts in their units
        if ($notifiable->notification_preferences['email_new_posts'] ?? true) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('💬 New Post in ' . $this->post->unit_code . ': ' . $this->post->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->author->name . ' created a new post in ' . $this->post->unit_code)
            ->line('Title: ' . $this->post->title)
            ->line('Preview: "' . Str::limit(strip_tags($this->post->content), 150) . '"')
            ->action('View Post', url('/forum/' . $this->post->id))
            ->line('Join the discussion!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'new_forum_post',
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'author_avatar' => $this->author->avatar,
            'unit_code' => $this->post->unit_code,
            'preview' => Str::limit(strip_tags($this->post->content), 100),
            'created_at' => now()->toISOString()
        ];
    }
}