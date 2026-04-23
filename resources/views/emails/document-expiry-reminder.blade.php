<x-mail::message>
    # Document Expiry Reminder

    Hello {{ $document->user?->firstname ?? 'there' }},

    This is a reminder that one of your documents is **expiring soon** and requires your attention.

    <x-mail::panel>
        **Document Type:** {{ $document->documentType?->name ?? 'N/A' }}

        **Expiry Date:** {{ $document->expiry_date?->format('F j, Y') ?? 'N/A' }}

        **Time Remaining:**
        {{ $daysUntilExpiry === 0 ? 'Expires today' : ($daysUntilExpiry === 1 ? 'Expires tomorrow' : "Expires in {$daysUntilExpiry} days") }}

        @if($document->issue_date)
            **Issue Date:** {{ $document->issue_date->format('F j, Y') }}
        @endif
    </x-mail::panel>

    Please make sure to renew this document before it expires to avoid any service disruptions.

    <x-mail::button :url="route('documents.show', $document->uuid)">
        View Document
    </x-mail::button>

    If you have any questions, please contact your administrator.

    Thank you,<br>
    CCS / LHC
</x-mail::message>