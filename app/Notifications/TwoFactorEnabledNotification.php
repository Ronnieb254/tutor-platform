<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorEnabledNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Two-Factor Authentication Enabled')
            ->line('Two-factor authentication has been successfully enabled for your Mwalimu account.')
            ->line('From now on, you will be asked to enter a verification code from your authenticator app when you sign in.')
            ->line('If you did not enable this feature, please contact support immediately.')
            ->line('Thank you for helping keep your account secure!');
    }
}
