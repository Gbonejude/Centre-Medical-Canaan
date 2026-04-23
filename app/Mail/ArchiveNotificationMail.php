<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArchiveNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $archiveTitle,
        public string $description,
        public string $recipientName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Document Available – ' . $this->archiveTitle,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.archive-notification',
            with: [
                'archiveTitle'  => $this->archiveTitle,
                'description'   => $this->description,
                'recipientName' => $this->recipientName,
                'url'           => route('archives.index'),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
