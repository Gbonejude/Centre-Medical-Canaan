<x-mail::message>
# Quarterly Report Signed

Hello,

**{{ $customerName }}** has approved and signed their quarterly report.

<x-mail::panel>
**Report Period:** {{ $period }}

**Signed At:** {{ \Carbon\Carbon::parse($report->customer_signed_at)->format('M d, Y g:i A') }}

**Signature:** {{ $report->customer_signature }}
</x-mail::panel>

The report is now fully completed and signed by the customer.

<x-mail::button :url="$viewUrl">
View Signed Report
</x-mail::button>

Thank you,<br>
{{ config('app.name') }}
</x-mail::message>
