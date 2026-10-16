<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Message $message)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'booking_id' => $this->message->booking_id,
            'booking_uuid' => $this->message->booking->uuid,
            'sender_name' => $this->message->sender->name,
            'sender_avatar' => $this->message->sender->avatar_url,
            'preview' => \Str::limit($this->message->body, 50),
            'message' => 'New message from ' . $this->message->sender->name,
            'type' => 'new_message',
        ];
    }
}
