<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TutorNeedsSubjects extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Action Required: Select Your Teaching Subjects – Mwalimu')
            ->greeting("Hello {$notifiable->name},")
            ->line('Great news — your verification documents have all been approved!')
            ->line('Before your profile can go live, please select **up to 2 subjects** you intend to teach on Mwalimu.')
            ->line('Log in to your dashboard and go to **My Subjects** to make your selection.')
            ->action('Select My Subjects', url('/tutor/subjects'))
            ->line('Once you have selected your subjects, your profile will be reviewed and activated.')
            ->line('If you have any questions, contact us at support@ovaldesk.co.ke.')
            ->salutation('Best regards, The Mwalimu Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'needs_subjects',
            'message' => 'Your documents are approved! Please select up to 2 subjects to complete your profile.',
        ];
    }
}
