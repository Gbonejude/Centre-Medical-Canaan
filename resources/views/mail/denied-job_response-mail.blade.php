<x-mail::message>
# Hello {{ $guest->firstname. ' '.$guest->lastname }} !!

Thank you for applying for the position:
**{{ $jobOffer->title }}**<br>

After careful review, we regret to inform you that your application has not been selected for further consideration.

We encourage you to apply for future opportunities that match your skills and experiences.

If you have any questions, feel free to reach out to our support team.

Thanks,<br>
<br>
Service Support - {{ config('app.name') }}
</x-mail::message>