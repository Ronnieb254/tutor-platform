<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedTutor extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Session Booked – ' . $this->booking->student->name . ' on ' . $this->booking->scheduled_at->format('M j, Y'))
            ->view('emails.booking-confirmed-tutor', ['booking' => $this->booking]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'new_booking',
            'booking_id'   => $this->booking->id,
            'message'      => 'New booking from ' . $this->booking->student->name,
            'code'         => $this->booking->session_code_tutor,
            'scheduled_at' => $this->booking->scheduled_at->toDateTimeString(),
        ];
    }
}
