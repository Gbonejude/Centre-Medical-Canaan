<?php

namespace App\Notifications;

use App\Models\Conversation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommunityConversationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $conversation;
    protected $type; // 'mention', 'permission', 'comment', 'reply', 'reaction'
    protected $senderName;

    /**
     * Create a new notification instance.
     */
    public function __construct(Conversation $conversation, string $type, string $senderName = null)
    {
        $this->conversation = $conversation;
        $this->type = $type;
        $this->senderName = $senderName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = match ($this->type) {
            'mention' => ($this->senderName ? "{$this->senderName} mentioned you" : "You were mentioned") . " in a community conversation: {$this->conversation->title}",
            'permission' => "A new community conversation you have access to was published: {$this->conversation->title}",
            'comment' => ($this->senderName ? "{$this->senderName} commented" : "New comment") . " on conversation: {$this->conversation->title}",
            'reply' => ($this->senderName ? "{$this->senderName} replied" : "New reply") . " to your comment in: {$this->conversation->title}",
            'reaction' => ($this->senderName ? "{$this->senderName} reacted" : "New reaction") . " to your content in: {$this->conversation->title}",
            default => "Update in community conversation: {$this->conversation->title}",
        };

        // Add 💬 emoji for comments/replies
        if (in_array($this->type, ['comment', 'reply'])) {
            $message = "💬 " . $message;
        }

        return [
            'conversation_id' => $this->conversation->id,
            'conversation_uuid' => $this->conversation->uuid,
            'title' => 'Community Conversation',
            'message' => $message,
            'type' => $this->type,
            'url' => route('conversations.show', $this->conversation->uuid, false),
        ];
    }
}
