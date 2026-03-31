<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EnrollmentStatusNotification extends Notification
{
    use Queueable;

    protected $enrollment;
    protected $status;
    protected $reason;

    public function __construct($enrollment, $status, $reason = null)
    {
        $this->enrollment = $enrollment;
        $this->status = $status;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $unit = $this->enrollment->unit;
        
        if ($this->status === 'approved') {
            return [
                'title' => 'Enrollment Approved',
                'message' => "Your request to enroll in {$unit->code} - {$unit->name} has been approved.",
                'type' => 'success',
                'unit_id' => $unit->id,
                'unit_code' => $unit->code,
                'enrollment_id' => $this->enrollment->id,
                'action_url' => route('topics.index', $unit->id),
                'action_text' => 'Start Learning',
            ];
        } else {
            return [
                'title' => 'Enrollment Request Update',
                'message' => "Your request to enroll in {$unit->code} - {$unit->name} has been rejected." . ($this->reason ? " Reason: {$this->reason}" : ''),
                'type' => 'error',
                'unit_id' => $unit->id,
                'unit_code' => $unit->code,
                'enrollment_id' => $this->enrollment->id,
                'action_url' => route('student.units.available'),
                'action_text' => 'Browse Units',
            ];
        }
    }
}