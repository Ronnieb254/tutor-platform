<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class TutorContractEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $contractPath = storage_path('app/contracts/tutor-agreement.pdf');
        
        return (new MailMessage)
            ->subject('Mwalimu Tutor Services Agreement - Action Required')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Welcome to Mwalimu! We're excited to have you join our community of verified tutors.')
            ->line('Before you can start accepting bookings, you must read and accept our Tutor Services Agreement.')
            ->line('**What you need to do:**')
            ->line('1. Download and read the attached agreement carefully')
            ->line('2. Sign in to your account at ' . url('/login'))
            ->line('3. Accept the agreement by checking the box on the contract page')
            ->line('**Important:** You must accept the contract before accessing your dashboard or receiving any bookings.')
            ->line('The agreement outlines your rights, responsibilities, payment terms, safeguarding obligations, and professional conduct expectations.')
            ->attach($contractPath, [
                'as' => 'Mwalimu-Tutor-Services-Agreement.pdf',
                'mime' => 'application/pdf',
            ])
            ->line('If you have any questions about the agreement, please contact us at info@ovaldesk.co.ke')
            ->salutation('Best regards, The Mwalimu Team');
    }
}
