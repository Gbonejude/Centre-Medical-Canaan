<x-mail::message>
# Hello {{ $guest->firstname. ' '.$guest->lastname }} !!

We have received your application for the position:
**{{ $jobOffer->title }}**<br>

Thank you for applying. Our recruitment team will review your application and contact you if you are selected for the
next step.

If you have any questions, feel free to reach out to our support team.

Thanks,<br>
<br>
Service Support - {{ config('app.name') }}
</x-mail::message>