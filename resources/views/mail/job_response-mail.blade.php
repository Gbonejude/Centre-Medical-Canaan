<x-mail::message>
# Hello {{ $user->firstname. ' '.$user->lastname }} !!

Your application for the position:
**{{ $jobOffer->title }}**<br>

@switch($status)
@case('new_inquiry')
We have received your application. Our recruitment team will review it shortly.<br>
@break

@case('in_review')
Your application is currently  # under review  by our recruitment team.<br>
@break

@case('approved')
Congratulations  🎉! Your application has been **Approved**. We will contact you for the next steps.<br>
@break

@case('denied')
We appreciate your interest, but unfortunately, your application has been **denied**.<br>
Please feel free to apply again in the future.<br>
@break

@default
Your application status has been updated.
@endswitch

If you have any questions, feel free to reach out to our support team.

Thanks,<br>
<br>
Service Support - {{ config('app.name') }}
</x-mail::message>