<x-mail::message>
# Hello {{ $guest->firstname. ' '.$guest->lastname }} !!

Congratulations! 🎉<br><br>

Your application for the position:
**{{ $jobOffer->title }}**<br>

has been **approved** by our recruitment team.

We will contact you shortly with the next steps in the hiring process. Please make sure your contact details are up to date.

If you have any questions, feel free to reach out to our support team.

Thanks,<br>
<br>
Service Support - {{ config('app.name') }}
</x-mail::message>