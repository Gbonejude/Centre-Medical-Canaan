<x-mail::message>
# New Leave Request

Hello {{ $admin->firstname }},

A new leave request has been submitted and requires your attention.

**Employee:** {{ $leaveRequest->user->firstname }} {{ $leaveRequest->user->lastname }}

**Details:**
- **Type:** {{ ucfirst(str_replace('_', ' ', $leaveRequest->type)) }}
- **Start Date:** {{ $leaveRequest->start_date }}
- **End Date:** {{ $leaveRequest->end_date }}
- **Duration:** {{ $leaveRequest->total_days }} day(s)

@if($leaveRequest->reason)
**Reason:**
{{ $leaveRequest->reason }}
@endif

<x-mail::button :url="$url">
View Request
</x-mail::button>

Please review and process this request at your earliest convenience.

Thanks,<br>
HR Department – {{ config('app.name') }}
</x-mail::message>
