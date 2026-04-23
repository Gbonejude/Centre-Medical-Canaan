<x-mail::message>
# New Quarterly Report Available

Hello,

A new quarterly report has been created for you.

**Customer**: {{ $customerName }}<br>
**Report Period**: {{ $period }}<br>
**Created by**: {{ $report->createdBy ? $report->createdBy->firstname . ' ' . $report->createdBy->lastname : 'System' }}<br>

This report contains important information about your progress and outcomes during this quarter.

Please review and amend if necessary otherwise, approve sign and submit.

<x-mail::button :url="$viewUrl">
View Report
</x-mail::button>

Thank you

CCS / LHC
</x-mail::message>
