<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public string $cancelledBy
    ) {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $canceller = $this->cancelledBy === 'student' ? $this->booking->student : $this->booking->tutor;
        
        return (new MailMessage)
            ->subject('Booking Cancelled')
            ->greeting('Hello ' . $notifiable->name)
            ->line('A booking has been cancelled by ' . $canceller->name)
            ->line('Subject: ' . $this->booking->subject->name)
            ->line('Scheduled: ' . $this->booking->scheduled_at->format('l, F j, Y \a\t g:i A'))
            ->line('Reason: ' . ($this->booking->cancellation_reason ?? 'No reason provided'))
            ->action('View Details', route('sessions.show', $this->booking->uuid))
            ->line('If you have any questions, please contact support.');
    }

    public function toArray($notifiable): array
    {
        $canceller = $this->cancelledBy === 'student' ? $this->booking->student : $this->booking->tutor;
        
        return [
            'booking_id' => $this->booking->id,
            'booking_uuid' => $this->booking->uuid,
            'cancelled_by' => $canceller->name,
            'subject' => $this->booking->subject->name,
            'scheduled_at' => $this->booking->scheduled_at->toISOString(),
            'reason' => $this->booking->cancellation_reason,
            'message' => 'Booking cancelled by ' . $canceller->name,
            'type' => 'booking_cancelled',
        ];
    }
}
