<x-mail::message>
    # Document Expired

    Hello {{ $document->user?->firstname ?? 'there' }},

    We would like to inform you that one of your documents has **expired** as of today.

    <x-mail::panel>
        **Document Type:** {{ $document->documentType?->name ?? 'N/A' }}

        **Expiry Date:** {{ $document->expiry_date?->format('F j, Y') ?? 'N/A' }}

        @if($document->issue_date)
            **Issue Date:** {{ $document->issue_date->format('F j, Y') }}
        @endif

        @if($document->notes)
            **Notes:** {{ $document->notes }}
        @endif
    </x-mail::panel>

    ⚠️ This document is now **expired**. Please contact your administrator to renew it as soon as possible to avoid any
    disruption to your services.

    <x-mail::button :url="route('documents.show', $document->uuid)" color="red">
        View Expired Document
    </x-mail::button>

    Thank you,<br>
    CCS / LHC
</x-mail::message>