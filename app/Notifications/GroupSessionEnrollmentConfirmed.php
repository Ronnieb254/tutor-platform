<?php

namespace App\Notifications;

use App\Models\ClinicEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupSessionEnrollmentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ClinicEnrollment $enrollment,
        public string $sessionCode,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $clinic = $this->enrollment->clinic;
        $tutor = $clinic->tutor;

        return (new MailMessage)
            ->subject('Your Group Session is Confirmed! 🎉')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Great news! Your payment has been verified and your seat in the group session is confirmed.')
            ->line('')
            ->line('**Session Details:**')
            ->line('• **Title:** ' . $clinic->title)
            ->line('• **Instructor:** ' . ($tutor?->name ?? 'Mwalimu Team'))
            ->line('• **Date & Time:** ' . $clinic->scheduled_at->format('D, M j, Y \a\t g:ia'))
            ->line('• **Duration:** ' . $clinic->duration_minutes . ' minutes')
            ->line('• **Your Session Code:** ' . $sessionCode)
            ->line('')
            ->line('**How to Join:**')
            ->line('1. Log in to your Mwalimu account')
            ->line('2. Go to "My Sessions" → "Group Sessions"')
            ->line('3. Click on "' . $clinic->title . '"')
            ->line('4. Enter your session code when prompted: ' . $sessionCode)
            ->line('')
            ->action('View Your Booking', route('group-sessions.my-sessions'))
            ->line('**Important:** Save your session code - you\'ll need it to join the class.')
            ->line('')
            ->line('If you have any questions, feel free to reach out to our support team.')
            ->salutation('Best regards, Mwalimu Team');
    }

    public function toDatabase(object $notifiable): array
    {
        $clinic = $this->enrollment->clinic;

        return [
            'type' => 'group_session_confirmed',
            'title' => 'Your group session is confirmed!',
            'message' => 'Your payment for "' . $clinic->title . '" has been verified. Your session code is: ' . $this->sessionCode,
            'clinic_id' => $clinic->id,
            'enrollment_id' => $this->enrollment->id,
            'session_code' => $this->sessionCode,
            'action_url' => route('group-sessions.my-sessions'),
        ];
    }
}
