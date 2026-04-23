<x-mail::message>
# New Individual Report Available

Hello Program Director,

A new individual report has been created by the Q. IMPROV team and requires your review.

**Customer**: {{ $customerName }}<br>
**Period**: {{ $period }}<br>
**Created by**: {{ $report->createdBy ? $report->createdBy->firstname . ' ' . $report->createdBy->lastname : 'System' }}<br>

Please review the report and either approve it or request corrections.

<x-mail::button :url="route('individual-reports.show', $report->uuid)">
Review Report
</x-mail::button>

Thank you,

CCS / LHC
</x-mail::message>
