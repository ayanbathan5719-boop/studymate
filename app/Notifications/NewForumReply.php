<?php

namespace App\Notifications;

use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewForumReply extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reply;
    protected $post;
    protected $replier;

    public function __construct(ForumReply $reply, ForumPost $post, User $replier)
    {
        $this->reply = $reply;
        $this->post = $post;
        $this->replier = $replier;
    }

    public function via($notifiable)
    {
        $channels = ['database'];
        
        // Check user notification preferences
        if ($notifiable->notification_preferences['email_replies'] ?? true) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Reply on "' . $this->post->title . '"')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->replier->name . ' replied to your post in the forum.')
            ->line('Reply: "' . Str::limit($this->reply->content, 100) . '"')
            ->action('View Reply', url('/forum/' . $this->post->id))
            ->line('Thank you for using StudyMate!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'forum_reply',
            'reply_id' => $this->reply->id,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'replier_id' => $this->replier->id,
            'replier_name' => $this->replier->name,
            'replier_avatar' => $this->replier->avatar,
            'reply_content' => Str::limit($this->reply->content, 100),
            'unit_code' => $this->post->unit_code,
            'created_at' => now()->toISOString()
        ];
    }
}