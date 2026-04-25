<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentAssignedDoctor extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;

    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouveau rendez-vous assigné - CMC',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointments.assigned_doctor',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
