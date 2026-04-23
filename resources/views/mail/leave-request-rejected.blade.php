<x-mail::message>
# Leave Request Rejected ❌

Hello {{ $employee->firstname }},

We regret to inform you that your leave request has been **rejected**.

**Details:**
- **Type:** {{ ucfirst(str_replace('_', ' ', $leaveRequest->type)) }}
- **Start Date:** {{ $leaveRequest->start_date }}
- **End Date:** {{ $leaveRequest->end_date }}
- **Duration:** {{ $leaveRequest->total_days }} day(s)

**Reason for rejection:**
{{ $leaveRequest->review_notes }}

**Reviewed by:** {{ $leaveRequest->reviewedBy->firstname }} {{ $leaveRequest->reviewedBy->lastname }}

<x-mail::button :url="$url">
View Details
</x-mail::button>

If you have any questions or concerns, please contact the HR department.

Thanks,<br>
HR Department – {{ config('app.name') }}
</x-mail::message>
