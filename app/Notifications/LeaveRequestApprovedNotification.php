<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public LeaveRequest $leaveRequest)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('leave-requests.my-show', ['leaveRequest' => $this->leaveRequest->uuid]);

        return (new MailMessage)
            ->subject('Leave Request Approved ✅')
            ->markdown('mail.leave-request-approved', [
                'employee' => $notifiable,
                'leaveRequest' => $this->leaveRequest,
                'url' => $url,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $data = [
            'title' => 'Leave Request Approved ✅',
            'message' => "Your leave request from {$this->leaveRequest->start_date} to {$this->leaveRequest->end_date} has been approved.",
            'leave_request_id' => $this->leaveRequest->id,
            'leave_request_uuid' => $this->leaveRequest->uuid,
            'type' => $this->leaveRequest->type,
            'start_date' => $this->leaveRequest->start_date,
            'end_date' => $this->leaveRequest->end_date,
            'total_days' => $this->leaveRequest->total_days,
            'approved_by' => $this->leaveRequest->reviewedBy ? "{$this->leaveRequest->reviewedBy->firstname} {$this->leaveRequest->reviewedBy->lastname}" : null,
            'url' => route('leave-requests.my-show', ['leaveRequest' => $this->leaveRequest->uuid]),
        ];

        if ($this->leaveRequest->review_notes) {
            $data['review_notes'] = $this->leaveRequest->review_notes;
        }

        return $data;
    }
}
