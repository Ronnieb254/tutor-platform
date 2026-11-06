<?php

namespace App\Notifications;

use App\Models\TutorProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TutorApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public TutorProfile $profile) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Your Mwalimu profile is live!')
            ->greeting("Hello {$notifiable->name},")
            ->line('Congratulations! Your tutor profile has been **verified and approved** by our team.')
            ->line('Your profile is now live on Mwalimu and students can start booking sessions with you.')
            ->line('**What\'s next:**')
            ->line('• Set your weekly availability in your dashboard')
            ->line('• Complete your bio so students know what to expect')
            ->line('• Students can now find and book you directly')
            ->line('• You\'ll be notified by email when a booking is made')
            ->action('Go to My Dashboard', url('/dashboard/tutor'))
            ->line('If you have any questions, contact us at support@ovaldesk.co.ke.')
            ->salutation("Best regards,\nThe Mwalimu Team\nOval Desk Ventures");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'tutor_approved',
            'message' => 'Congratulations! Your tutor profile has been approved and is now live on Mwalimu.',
        ];
    }
}
