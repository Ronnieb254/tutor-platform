<?php

namespace App\Notifications;

use App\Models\ClinicEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClinicEnrollmentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ClinicEnrollment $enrollment) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $clinic      = $this->enrollment->clinic;
        $scheduledAt = $clinic->scheduled_at
            ? $clinic->scheduled_at->format('D, d M Y \a\t H:i')
            : 'TBC';
        $price = 'KES ' . number_format($clinic->price, 2);

        return (new MailMessage)
            ->subject('Booking Confirmed — ' . $clinic->title . ' | Mwalimu')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your booking for the following session has been confirmed:')
            ->line('**' . $clinic->title . '**')
            ->line('Subject: ' . ($clinic->subject?->name ?? '—'))
            ->line('Date / Time: ' . $scheduledAt)
            ->line('Duration: ' . $clinic->duration_minutes . ' minutes')
            ->line('Price paid: ' . $price)
            ->line('Your session access code is: **' . $this->enrollment->session_code . '**')
            ->line('You will need this code to enter the session room. Do not share it.')
            ->action('View My Booking', url('/clinic/my-bookings'));
    }

    public function toArray(object $notifiable): array
    {
        $clinic = $this->enrollment->clinic;

        return [
            'type'          => 'clinic_enrollment_confirmed',
            'enrollment_id' => $this->enrollment->id,
            'clinic_id'     => $this->enrollment->clinic_id,
            'title'         => $clinic?->title,
            'session_code'  => $this->enrollment->session_code,
            'scheduled_at'  => $clinic?->scheduled_at?->toDateTimeString(),
            'message'       => 'Your booking for "' . ($clinic?->title ?? 'clinic') . '" is confirmed. Code: ' . $this->enrollment->session_code,
            'url'           => url('/clinic/my-bookings'),
        ];
    }
}
