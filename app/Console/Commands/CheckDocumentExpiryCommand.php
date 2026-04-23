<?php

namespace App\Console\Commands;

use App\Mail\DocumentExpiredMail;
use App\Mail\DocumentExpiryReminderMail;
use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckDocumentExpiryCommand extends Command
{
    protected $signature = 'app:documents:check-expiry';

    protected $description = 'Send email notifications for documents approaching expiry or that have just expired';

    public function handle(): void
    {
        $today = Carbon::today();

        $this->info("Checking document expiry notifications for {$today->format('Y-m-d')}");

        $this->sendReminderNotifications($today);
        $this->sendExpiredNotifications($today);

        $this->info('Document expiry check completed.');
    }

    /**
     * Send reminder emails for documents whose reminder_date is today.
     */
    private function sendReminderNotifications(Carbon $today): void
    {
        $documents = Document::with(['user', 'documentType'])
            ->whereNotNull('reminder_date')
            ->whereDate('reminder_date', $today)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>', $today) // not yet expired
            ->whereHas('user', fn ($q) => $q->whereNotNull('email'))
            ->get();

        $this->info("Documents with reminder date today: {$documents->count()}");

        foreach ($documents as $document) {
            $this->processReminderNotification($document);
        }
    }

    /**
     * Send expired emails for documents whose expiry_date is today.
     */
    private function sendExpiredNotifications(Carbon $today): void
    {
        $documents = Document::with(['user', 'documentType'])
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', $today)
            ->whereHas('user', fn ($q) => $q->whereNotNull('email'))
            ->get();

        $this->info("Documents expiring today: {$documents->count()}");

        foreach ($documents as $document) {
            $this->processExpiredNotification($document);
        }
    }

    private function processReminderNotification(Document $document): void
    {
        $user = $document->user;

        if (! $user || ! $user->email) {
            $this->warn("Document {$document->uuid}: no user email, skipping.");

            return;
        }

        $daysUntilExpiry = (int) Carbon::today()->diffInDays($document->expiry_date, false);

        try {
            Mail::to($user->email)->send(new DocumentExpiryReminderMail($document, $daysUntilExpiry));

            $this->info("Reminder sent to {$user->email} for document '{$document->documentType?->name}' (expires in {$daysUntilExpiry} day(s)).");
        } catch (\Exception $e) {
            $this->error("Failed to send reminder to {$user->email}: {$e->getMessage()}");
            Log::error('Document expiry reminder email failed', [
                'document_uuid' => $document->uuid,
                'user_email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function processExpiredNotification(Document $document): void
    {
        $user = $document->user;

        if (! $user || ! $user->email) {
            $this->warn("Document {$document->uuid}: no user email, skipping.");

            return;
        }

        try {
            Mail::to($user->email)->send(new DocumentExpiredMail($document));

            $this->info("Expiry notice sent to {$user->email} for document '{$document->documentType?->name}'.");
        } catch (\Exception $e) {
            $this->error("Failed to send expiry notice to {$user->email}: {$e->getMessage()}");
            Log::error('Document expired email failed', [
                'document_uuid' => $document->uuid,
                'user_email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
