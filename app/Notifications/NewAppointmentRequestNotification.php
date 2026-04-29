<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewAppointmentRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Appointment $appointment) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Nouvelle demande de rendez-vous',
            'message' => $this->appointment->patient->firstname.' '.$this->appointment->patient->lastname
                .' a soumis une demande de rendez-vous pour le '
                .$this->appointment->appointment_date->format('d/m/Y')
                .' à '.$this->appointment->appointment_time,
            'url' => route('appointments.show', $this->appointment->uuid),
            'appointment_id' => $this->appointment->id,
        ];
    }
}
