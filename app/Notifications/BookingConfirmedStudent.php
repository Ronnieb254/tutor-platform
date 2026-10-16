<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedStudent extends Notification
{
    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Confirmed – Session with ' . $this->booking->tutor->name)
            ->view('emails.booking-confirmed-student', ['booking' => $this->booking]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'booking_confirmed',
            'booking_id'   => $this->booking->id,
            'booking_uuid' => $this->booking->uuid,
            'message'      => 'Your session with ' . $this->booking->tutor->name . ' is confirmed!',
            'code'         => $this->booking->session_code_student,
            'scheduled_at' => $this->booking->scheduled_at->toDateTimeString(),
            'url'          => route('bookings.show', $this->booking->uuid),
        ];
    }
}
