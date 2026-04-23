<x-mail::message>
# Hello {{ $user->firstname . ' ' . $user->lastname }}!

You have been **mentioned** in a conversation: **{{ $conversation->title }}**

**Description**: {{ Str::limit($conversation->content, 150) }}

**Author**: {{ $conversation->user->firstname . ' ' . $conversation->user->lastname }}


<x-mail::button :url="route('conversations.show', $conversation->uuid)">
    View Conversation
</x-mail::button>

Your attention is requested in this conversation. Please check it out and respond if needed.

If you have any questions, feel free to reach out to our support team.

Thanks,<br>
<br>
Service Support - {{ config('app.name') }}
</x-mail::message>
