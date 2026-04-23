<x-mail::message>
# Individual Report Approved

Hello,

**{{ $customerName }}** has approved their individual report.

<x-mail::panel>
**Report Period:** {{ $period }}

**Approved At:** {{ \Carbon\Carbon::parse($report->reviewed_at)->format('M d, Y g:i A') }}
</x-mail::panel>

The report has been reviewed and approved by the customer.

<x-mail::button :url="route('individual-reports.show', $report->uuid)">
View Approved Report
</x-mail::button>

Thank you,<br>
CCS / LHC
</x-mail::message>
