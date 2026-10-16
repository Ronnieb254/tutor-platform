<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SessionReminderNotification extends Notification implements ShouldQueue
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
        $otherPerson = $notifiable->id === $this->booking->student_id 
            ? $this->booking->tutor 
            : $this->booking->student;
            
        return (new MailMessage)
            ->subject('Session Starting Soon!')
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('Your session is starting in 1 hour.')
            ->line('With: ' . $otherPerson->name)
            ->line('Subject: ' . $this->booking->subject->name)
            ->line('Time: ' . $this->booking->scheduled_at->format('g:i A'))
            ->action('Join Session', route('sessions.join', $this->booking->uuid))
            ->line('Please be ready a few minutes before the session starts.');
    }

    public function toArray($notifiable): array
    {
        $otherPerson = $notifiable->id === $this->booking->student_id 
            ? $this->booking->tutor 
            : $this->booking->student;
            
        return [
            'booking_id' => $this->booking->id,
            'booking_uuid' => $this->booking->uuid,
            'other_person' => $otherPerson->name,
            'subject' => $this->booking->subject->name,
            'scheduled_at' => $this->booking->scheduled_at->toISOString(),
            'message' => 'Session starting in 1 hour with ' . $otherPerson->name,
            'type' => 'session_reminder',
        ];
    }
}
