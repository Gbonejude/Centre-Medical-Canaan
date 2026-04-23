<x-mail::message>
# Correction Requested

Hello,

The Program Director has reviewed the individual report and requested corrections.

<x-mail::panel>
**Customer:** {{ $customerName }}
<br>
**Period:** {{ $period }}
</x-mail::panel>

**Correction Notes:**
<x-mail::panel>
{{ $notes }}
</x-mail::panel>

Please update the report according to these notes.

<x-mail::button :url="route('individual-reports.edit', $report->uuid)" color="error">
Edit Report
</x-mail::button>

Thank you,<br>
{{ config('app.name') }}
</x-mail::message>
