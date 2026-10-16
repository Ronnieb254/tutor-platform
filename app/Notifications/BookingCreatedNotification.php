<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreatedNotification extends Notification implements ShouldQueue
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
            ->subject('New Booking Request')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have a new booking request from ' . $this->booking->student->name)
            ->line('Subject: ' . $this->booking->subject->name)
            ->line('Scheduled: ' . $this->booking->scheduled_at->format('l, F j, Y \a\t g:i A'))
            ->line('Duration: ' . $this->booking->duration_minutes . ' minutes')
            ->action('View Booking', route('sessions.show', $this->booking->uuid))
            ->line('Please confirm or decline this booking at your earliest convenience.');
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_uuid' => $this->booking->uuid,
            'student_name' => $this->booking->student->name,
            'subject' => $this->booking->subject->name,
            'scheduled_at' => $this->booking->scheduled_at->toISOString(),
            'message' => 'New booking request from ' . $this->booking->student->name,
            'type' => 'booking_created',
        ];
    }
}
