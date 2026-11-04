<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TutorContractAcceptedEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Mwalimu Tutor Services Agreement – Accepted')
            ->greeting('Dear Tutor,')
            ->line('Welcome to Mwalimu! We\'re excited to have you join our community of tutors.')
            ->line('Thank you for reviewing and accepting our **Tutor Services Agreement**.')
            ->line('The agreement outlines your rights and responsibilities, payment terms, safeguarding obligations, and professional conduct expectations while providing services through Mwalimu.')
            ->line('You can access and review the full agreement anytime by signing in to your Mwalimu account.')
            ->line('If you have any questions or need clarification, please contact us at info@ovaldesk.co.ke.')
            ->line('We look forward to working with you!')
            ->salutation("Best regards,\n**The Mwalimu Team**\nOval Desk Ventures");
    }
}
