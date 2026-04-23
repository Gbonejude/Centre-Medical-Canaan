<?php

namespace App\Mail;

use App\Models\IndividualReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IndividualReportCorrectionMail extends Mailable
{
    use Queueable, SerializesModels;

    public IndividualReport $report;

    public string $customerName;

    public string $period;

    public string $notes;

    /**
     * Create a new message instance.
     */
    public function __construct(IndividualReport $report, string $customerName, string $period, string $notes)
    {
        $this->report = $report;
        $this->customerName = $customerName;
        $this->period = $period;
        $this->notes = $notes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Correction Requested - Individual Report - {$this->customerName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.individual-report-correction',
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
