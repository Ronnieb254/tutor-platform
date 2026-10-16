<?php

namespace App\Notifications;

use App\Models\{Booking, User};
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UserJoinedSessionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public User $joiner
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_uuid' => $this->booking->uuid,
            'joiner_name' => $this->joiner->name,
            'joiner_role' => $this->joiner->role,
            'message' => $this->joiner->name . ' has joined the session',
            'type' => 'user_joined',
        ];
    }
}
