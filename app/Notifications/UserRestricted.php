<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserRestricted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $days;

    /**
     * Create a new notification instance.
     */
    public function __construct($days)
    {
        $this->days = $days;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('⚠️ Forum Access Restricted')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your access to the forum has been temporarily restricted due to multiple flags on your posts.')
            ->line('Restriction period: ' . $this->days . ' days')
            ->line('During this time, you can still view posts but cannot create new posts or replies.')
            ->line('If you believe this is a mistake, please contact your lecturer or administrator.')
            ->line('Thank you for your understanding.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'user_restricted',
            'title' => 'Forum Access Restricted',
            'message' => "Your forum access has been restricted for {$this->days} days due to multiple flags.",
            'days' => $this->days,
            'color' => '#ef4444',
            'icon' => 'fa-ban',
            'created_at' => now()->toISOString()
        ];
    }
}