<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewAppointmentRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Appointment $appointment)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Nouveau Rendez-vous',
            'message' => 'Une nouvelle demande de rendez-vous a été soumise par ' . $this->appointment->patient->lastname . ' ' . $this->appointment->patient->firstname,
            'url' => route('appointments.show', $this->appointment->id),
            'appointment_id' => $this->appointment->id,
        ];
    }
}
