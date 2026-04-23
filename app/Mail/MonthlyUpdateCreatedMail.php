<?php

namespace App\Mail;

use App\Models\IndividualMonthlyUpdate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MonthlyUpdateCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public IndividualMonthlyUpdate $update;

    public string $customerName;

    public string $period;

    public string $viewUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(IndividualMonthlyUpdate $update, string $customerName, string $period)
    {
        $this->update = $update;
        $this->customerName = $customerName;
        $this->period = $period;
        $this->viewUrl = route('individual-monthly-updates.show', $update->uuid);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Monthly Update Created - {$this->customerName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.monthly-update-created',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
