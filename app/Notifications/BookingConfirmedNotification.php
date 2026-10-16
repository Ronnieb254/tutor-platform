<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Booking $booking)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Confirmed!')
            ->greeting('Great news, ' . $notifiable->name . '!')
            ->line('Your booking with ' . $this->booking->tutor->name . ' has been confirmed.')
            ->line('Subject: ' . $this->booking->subject->name)
            ->line('Scheduled: ' . $this->booking->scheduled_at->format('l, F j, Y \a\t g:i A'))
            ->line('Duration: ' . $this->booking->duration_minutes . ' minutes')
            ->action('View Session Details', route('sessions.show', $this->booking->uuid))
            ->line('You can join the session 1 hour before the scheduled time.');
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_uuid' => $this->booking->uuid,
            'tutor_name' => $this->booking->tutor->name,
            'subject' => $this->booking->subject->name,
            'scheduled_at' => $this->booking->scheduled_at->toISOString(),
            'message' => 'Your booking with ' . $this->booking->tutor->name . ' has been confirmed',
            'type' => 'booking_confirmed',
        ];
    }
}
