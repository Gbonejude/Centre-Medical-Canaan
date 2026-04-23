<x-mail::message>
# Hello {{ $user->firstname  }}!

We have a new job offer available:
**{{ $jobOffer->title }}**

You can view the details here:
<x-mail::button :url="$url">
    View Job Offer
</x-mail::button>
Thank you,
<br>
Recruitment Team – {{ config('app.name') }}
</x-mail::message>
