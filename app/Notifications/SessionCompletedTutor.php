<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SessionCompletedTutor extends Notification
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
            'not_delivered'       => 'Session not delivered – no earnings credited',
            'partially_delivered' => 'Session partially delivered – 50% earnings credited',
            default               => 'Session completed – earnings credited to your wallet',
        };

        return (new MailMessage)
            ->subject($subjectLine)
            ->view('emails.session-completed-tutor', ['booking' => $this->booking]);
    }

    public function toArray(object $notifiable): array
    {
        $delivery = $this->booking->delivery_status ?? 'fully_delivered';

        $earning = match ($delivery) {
            'not_delivered'       => 0,
            'partially_delivered' => round((float) $this->booking->tutor_earning * 0.5, 2),
            default               => (float) $this->booking->tutor_earning,
        };

        $message = match ($delivery) {
            'not_delivered'       => 'Session #' . substr($this->booking->uuid, 0, 8) . ' was marked not delivered. No earnings have been credited.',
            'partially_delivered' => 'Session #' . substr($this->booking->uuid, 0, 8) . ' was partially delivered. KSh ' . number_format($earning, 0) . ' credited.',
            default               => 'Session complete! KSh ' . number_format($earning, 0) . ' credited to your wallet.',
        };

        return [
            'type'            => 'session_completed',
            'booking_id'      => $this->booking->id,
            'booking_uuid'    => $this->booking->uuid,
            'delivery_status' => $delivery,
            'earning'         => $earning,
            'message'         => $message,
            'url'             => route('bookings.show', $this->booking->uuid),
        ];
    }
}
