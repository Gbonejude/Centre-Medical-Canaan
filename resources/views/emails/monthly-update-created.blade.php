<x-mail::message>
# New Monthly Update Available

Hello,

A new monthly update has been created and requires your attention.

**Customer**: {{ $customerName }}<br>
**Period**: {{ $period }}<br>
**Created by**: {{ $update->createdBy ? $update->createdBy->firstname . ' ' . $update->createdBy->lastname : 'System' }}<br>

As a member of the Quality Improvement team, you can now review this monthly update and create the corresponding Individual Report.

<x-mail::button :url="$viewUrl">
View Monthly Update
</x-mail::button>

Thank you,

CCS / LHC
</x-mail::message>
