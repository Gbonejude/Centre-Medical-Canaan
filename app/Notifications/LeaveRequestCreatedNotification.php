<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestCreatedNotification extends Notification implements ShouldQueue
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
        $url = route('leave-requests.show', ['leaveRequest' => $this->leaveRequest->uuid]);

        return (new MailMessage)
            ->subject("New Leave Request from {$this->leaveRequest->user->firstname} {$this->leaveRequest->user->lastname}")
            ->markdown('mail.leave-request-created', [
                'admin' => $notifiable,
                'leaveRequest' => $this->leaveRequest,
                'url' => $url,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Leave Request 📅',
            'message' => "{$this->leaveRequest->user->firstname} {$this->leaveRequest->user->lastname} has submitted a leave request for {$this->leaveRequest->total_days} day(s).",
            'leave_request_id' => $this->leaveRequest->id,
            'leave_request_uuid' => $this->leaveRequest->uuid,
            'employee_name' => "{$this->leaveRequest->user->firstname} {$this->leaveRequest->user->lastname}",
            'type' => $this->leaveRequest->type,
            'start_date' => $this->leaveRequest->start_date,
            'end_date' => $this->leaveRequest->end_date,
            'total_days' => $this->leaveRequest->total_days,
            'url' => route('leave-requests.show', ['leaveRequest' => $this->leaveRequest->uuid]),
        ];
    }
}
