<x-mail::message>
# Leave Request Approved ✅

Hello {{ $employee->firstname }},

Great news! Your leave request has been **approved**.

**Details:**
- **Type:** {{ ucfirst(str_replace('_', ' ', $leaveRequest->type)) }}
- **Start Date:** {{ $leaveRequest->start_date }}
- **End Date:** {{ $leaveRequest->end_date }}
- **Duration:** {{ $leaveRequest->total_days }} day(s)

@if($leaveRequest->review_notes)
**Notes from {{ $leaveRequest->reviewedBy->firstname }} {{ $leaveRequest->reviewedBy->lastname }}:**
{{ $leaveRequest->review_notes }}
@endif

<x-mail::button :url="$url">
View Details
</x-mail::button>

Enjoy your time off!

Thanks,<br>
HR Department – {{ config('app.name') }}
</x-mail::message>
