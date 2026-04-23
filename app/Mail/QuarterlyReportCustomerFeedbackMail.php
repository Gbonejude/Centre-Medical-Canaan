<?php

namespace App\Mail;

use App\Models\QuarterlyReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuarterlyReportCustomerFeedbackMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public string $viewUrl;

    public function __construct(
        public QuarterlyReport $report,
        public string $customerName,
        public string $period,
        public string $actionType, // 'notes_added' or 'correction_requested'
        public string $customerNotes
    ) {
        $this->viewUrl = route('quarterly-reports.show', $report->uuid);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->actionType === 'correction_requested'
            ? 'Correction Requested for Quarterly Report - '.$this->customerName
            : 'Customer Notes Added to Quarterly Report - '.$this->customerName;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.quarterly-report-customer-feedback'
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
