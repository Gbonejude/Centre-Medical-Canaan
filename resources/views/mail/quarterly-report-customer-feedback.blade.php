<x-mail::message>
# {{ $actionType === 'correction_requested' ? 'Correction Requested' : 'Customer Notes Added' }}

Hello,

**{{ $customerName }}** has {{ $actionType === 'correction_requested' ? 'requested corrections' : 'added notes' }} to their quarterly report.

<x-mail::panel>
**Report Period:** {{ $period }}

**Customer Status:** {{ ucwords(str_replace('_', ' ', $report->customer_status)) }}

@if($report->customer_reviewed_at)
**Reviewed At:** {{ \Carbon\Carbon::parse($report->customer_reviewed_at)->format('M d, Y g:i A') }}
@endif
</x-mail::panel>

## Customer Notes/Comments:

{{ $customerNotes }}

<x-mail::button :url="$viewUrl">
View Quarterly Report
</x-mail::button>

@if($actionType === 'correction_requested')
**Action Required:** Please review the customer's feedback and make the necessary corrections to the report.
@else
**Note:** The customer has added notes for your review. No immediate action is required unless the feedback indicates issues.
@endif

Thank you,<br>
{{ config('app.name') }}
</x-mail::message>
