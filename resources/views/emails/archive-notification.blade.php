@component('mail::message')
# New Document Available

Hello {{ $recipientName }},

A new document has been shared with you on **CSS Drive**.

**{{ $archiveTitle }}**

@if($description)
{{ $description }}
@endif

@component('mail::button', ['url' => $url])
View CSS Drive
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
