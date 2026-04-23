<x-mail::message>
# Hello {{ $guest->firstname. ' '.$guest->lastname }} !!

Your application for the position:
**{{ $jobOffer->title }}**<br>

is currently **{{ $status }}** by our recruitment team.<br>

We appreciate your interest. If your profile matches our requirements, we will get in touch with you for the next steps.<br>

If you have any questions, feel free to reach out to our support team.

Thanks,<br>
<br>
Service Support - {{ config('app.name') }}
</x-mail::message>