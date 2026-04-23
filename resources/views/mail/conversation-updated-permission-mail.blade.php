<x-mail::message>
# Hello {{ $user->firstname . ' ' . $user->lastname }}!

A conversation that you have permission to access has been **updated**: **{{ $conversation->title }}**

**Description**: {{ Str::limit($conversation->content, 150) }}

**Author**: {{ $conversation->user->firstname . ' ' . $conversation->user->lastname }}

<x-mail::button :url="route('conversations.show', $conversation->uuid)">
    View Conversation
</x-mail::button>

This conversation has been modified and may require your attention. Please check it out to stay up to date.

If you have any questions, feel free to reach out to our support team.

Thanks,<br>
<br>
Service Support - {{ config('app.name') }}
</x-mail::message>