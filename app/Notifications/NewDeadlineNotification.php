<?php

namespace App\Notifications;

use App\Models\Deadline;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\NewDeadlineNotification;

class NewDeadlineNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $deadline;

    /**
     * Create a new notification instance.
     */
    public function __construct(Deadline $deadline)
    {
        $this->deadline = $deadline;
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
        $unit = $this->deadline->unit;
        $dueDate = \Carbon\Carbon::parse($this->deadline->due_date)->format('M d, Y \a\t h:i A');
        
        return (new MailMessage)
            ->subject('New Deadline: ' . $this->deadline->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new deadline has been set for ' . $unit->name)
            ->line('**' . $this->deadline->title . '**')
            ->line('Due: ' . $dueDate)
            ->line($this->deadline->description ?? '')
            ->action('View Deadline', url('/student/deadlines'))
            ->line('Stay on track with your studies!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $unit = $this->deadline->unit;
        
        return [
            'type' => 'deadline',
            'title' => 'New Deadline: ' . $this->deadline->title,
            'message' => 'A new deadline has been set for ' . $unit->name,
            'deadline_id' => $this->deadline->id,
            'unit_id' => $unit->id,
            'unit_code' => $unit->code,
            'due_date' => $this->deadline->due_date,
            'icon' => 'deadline',
            'action_url' => '/student/deadlines',
        ];
    }
}