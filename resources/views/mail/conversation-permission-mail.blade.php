<x-mail::message>
# Hello {{ $user->firstname . ' ' . $user->lastname }}!

A new conversation has been created that you have access to: **{{ $conversation->title }}**

**Description**: {{ Str::limit($conversation->content, 150) }}

**Author**: {{ $conversation->user->firstname . ' ' . $conversation->user->lastname }}


<x-mail::button :url="route('conversations.show', $conversation->uuid)">
    View Conversation
</x-mail::button>

This conversation is available to you based on your permissions. Please check it out and participate if needed.

If you have any questions, feel free to reach out to our support team.

Thanks,<br>
<br>
Service Support - {{ config('app.name') }}
</x-mail::message>