<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SessionCompletedStudent extends Notification
{
    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $delivery = $this->booking->delivery_status ?? 'fully_delivered';

        $subjectLine = match ($delivery) {
            'not_delivered'      => 'Your session was not delivered – full refund credited',
            'partially_delivered'=> 'Session summary – partial refund credited to your wallet',
            default              => 'Session completed – leave a review for your tutor',
        };

        return (new MailMessage)
            ->subject($subjectLine)
            ->view('emails.session-completed-student', ['booking' => $this->booking]);
    }

    public function toArray(object $notifiable): array
    {
        $delivery = $this->booking->delivery_status ?? 'fully_delivered';

        $message = match ($delivery) {
            'not_delivered'       => 'Your session was not delivered. A full refund has been credited to your wallet.',
            'partially_delivered' => 'Your session was partially delivered. 50% has been refunded to your wallet.',
            default               => 'Your session with ' . $this->booking->tutor->name . ' is complete! Please leave a review.',
        };

        return [
            'type'            => 'session_completed',
            'booking_id'      => $this->booking->id,
            'booking_uuid'    => $this->booking->uuid,
            'delivery_status' => $delivery,
            'message'         => $message,
            'url'             => route('bookings.show', $this->booking->uuid),
        ];
    }
}
