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
        
        $mail = (new MailMessage)
            ->subject('Mwalimu Tutor Services Agreement - Action Required')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Welcome to Mwalimu! We\'re excited to have you join our community of verified tutors.')
            ->line('Before you can start accepting bookings, you must read and accept our Tutor Services Agreement.')
            ->line('What you need to do:')
            ->line('1. Sign in to your account at ' . url('/login'))
            ->line('2. Read the Tutor Services Agreement on the contract page')
            ->line('3. Accept the agreement by checking the box and clicking "Accept Agreement"')
            ->line('Important: You must accept the contract before accessing your dashboard or receiving any bookings.')
            ->line('The agreement outlines your rights, responsibilities, payment terms, safeguarding obligations, and professional conduct expectations.');
        
        // Attach PDF only if it exists
        if (file_exists($contractPath)) {
            $mail->attach($contractPath, [
                'as' => 'Mwalimu-Tutor-Services-Agreement.pdf',
                'mime' => 'application/pdf',
            ]);
        } else {
            $mail->line('You can view the full agreement online when you sign in.');
        }
        
        return $mail
            ->line('If you have any questions about the agreement, please contact us at info@ovaldesk.co.ke')
            ->salutation('Best regards, The Mwalimu Team');
    }
}
