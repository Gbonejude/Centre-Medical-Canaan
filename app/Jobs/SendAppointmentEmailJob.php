<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendAppointmentEmailJob implements ShouldQueue
{
    use Queueable;

    protected $appointment;

    protected $type;

    /**
     * Create a new job instance.
     */
    public function __construct($appointment, $type)
    {
        $this->appointment = $appointment;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $appointment = \App\Models\Appointment::with(['patient', 'medicalService', 'doctor'])->find($this->appointment->id);

        if (! $appointment) {
            return;
        }

        switch ($this->type) {
            case 'requested_patient':
                \Illuminate\Support\Facades\Mail::to($appointment->patient->email)
                    ->send(new \App\Mail\AppointmentRequestedPatient($appointment));
                break;

            case 'status_changed':
                \Illuminate\Support\Facades\Mail::to($appointment->patient->email)
                    ->send(new \App\Mail\AppointmentStatusChanged($appointment));
                break;

            case 'new_request_staff':
                $receptionists = \App\Models\User::permission(['RECEPTIONIST', 'ADMIN', 'SUPER ADMIN'])->get();

                foreach ($receptionists as $staff) {
                    \Illuminate\Support\Facades\Mail::to($staff->email)
                        ->send(new \App\Mail\NewAppointmentRequestStaff($appointment));

                    $staff->notify(new \App\Notifications\NewAppointmentRequestNotification($appointment));
                }
                break;

            case 'assigned_doctor':
                if ($appointment->doctor) {
                    \Illuminate\Support\Facades\Mail::to($appointment->doctor->email)
                        ->send(new \App\Mail\AppointmentAssignedDoctor($appointment));
                    $appointment->doctor->notify(new \App\Notifications\AppointmentAssignedNotification($appointment));
                }
                break;
        }
    }
}
