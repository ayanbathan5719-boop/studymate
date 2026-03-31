<?php

namespace App\Notifications;

use App\Models\ForumPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentQuestionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $post;
    protected $unit;

    /**
     * Create a new notification instance.
     */
    public function __construct(ForumPost $post)
    {
        $this->post = $post;
        $this->unit = $post->unit;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $studentName = $this->post->user->name ?? 'A student';
        
        return (new MailMessage)
            ->subject('New Question in ' . $this->unit->code)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($studentName . ' has posted a question in ' . $this->unit->name)
            ->line('**' . $this->post->title . '**')
            ->line($this->post->content)
            ->action('View Question', url('/lecturer/forum/' . $this->post->id))
            ->line('Please respond to help your student.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $studentName = $this->post->user->name ?? 'A student';
        
        return [
            'type' => 'student_question',
            'title' => 'New Question: ' . $this->post->title,
            'message' => $studentName . ' posted a question in ' . $this->unit->code,
            'post_id' => $this->post->id,
            'unit_id' => $this->unit->id,
            'unit_code' => $this->unit->code,
            'student_name' => $studentName,
            'icon' => 'question',
            'action_url' => '/lecturer/forum/' . $this->post->id,
        ];
    }
}