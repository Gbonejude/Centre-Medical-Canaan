<x-mail::message>
# Hello {{ $user->firstname. ' '.$user->lastname }} !!

We have received your application for the position:
**{{ $jobOffer->title }}**<br>

Thank you for applying. Our recruitment team will review your application and contact you if you are selected for the
next step.

Thanks,<br>
<br>
Recruitment Team – {{ config('app.name') }}
</x-mail::message>
