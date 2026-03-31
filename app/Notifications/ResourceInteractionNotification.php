<?php

namespace App\Notifications;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResourceInteractionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $resource;
    protected $student;
    protected $action;

    /**
     * Create a new notification instance.
     */
    public function __construct(Resource $resource, User $student, string $action)
    {
        $this->resource = $resource;
        $this->student = $student;
        $this->action = $action; // 'view' or 'download'
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $actionText = $this->action === 'download' ? 'downloaded' : 'viewed';
        
        return [
            'type' => 'resource_interaction',
            'title' => 'Resource ' . ucfirst($this->action),
            'message' => $this->student->name . ' ' . $actionText . ' ' . $this->resource->title,
            'resource_id' => $this->resource->id,
            'resource_title' => $this->resource->title,
            'unit_id' => $this->resource->unit_id,
            'unit_code' => $this->resource->unit_code,
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'action' => $this->action,
            'icon' => 'resource',
            'action_url' => '/lecturer/resources/' . $this->resource->id,
        ];
    }
}