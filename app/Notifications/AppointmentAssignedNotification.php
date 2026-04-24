<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AppointmentAssignedNotification extends Notification implements ShouldQueue
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
            'title' => 'Nouveau Patient Affecté',
            'message' => 'On vous a affecté le rendez-vous de ' . $this->appointment->patient->lastname . ' ' . $this->appointment->patient->firstname . ' le ' . $this->appointment->appointment_date->format('d/m/Y') . ' à ' . $this->appointment->appointment_time,
            'url' => route('appointments.show', $this->appointment->id),
            'appointment_id' => $this->appointment->id,
        ];
    }
}
