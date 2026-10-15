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
            ->view('emails.tutor-approved', ['profile' => $this->profile, 'user' => $notifiable]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'tutor_approved',
            'message' => 'Congratulations! Your tutor profile has been approved and is now live on Mwalimu.',
        ];
    }
}
