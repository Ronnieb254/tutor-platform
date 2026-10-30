<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetSuccessNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected string $ipAddress,
        protected string $userAgent
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $timestamp = now()->setTimezone($notifiable->timezone ?? 'Africa/Nairobi')
            ->format('F j, Y \a\t g:i A T');
        
        $appName = config('app.name', 'Mwalimu');

        return (new MailMessage)
            ->subject('Your password has been reset')
            ->greeting('Hello ' . explode(' ', $notifiable->name)[0] . '!')
            ->line('Your ' . $appName . ' account password was successfully changed on ' . $timestamp . '.')
            ->line('**Security Details:**')
            ->line('• IP Address: ' . $this->ipAddress)
            ->line('• Device: ' . $this->getUserAgentSummary())
            ->line('If you did not make this change, please contact our support team immediately to secure your account.')
            ->action('Go to Dashboard', url('/dashboard'))
            ->line('Thank you for keeping your account secure!');
    }

    /**
     * Get a simplified user agent summary.
     */
    protected function getUserAgentSummary(): string
    {
        $ua = $this->userAgent;
        
        // Try to extract browser and OS
        $browser = 'Unknown Browser';
        $os = 'Unknown OS';

        // Browser detection
        if (str_contains($ua, 'Chrome') && !str_contains($ua, 'Edg')) {
            $browser = 'Chrome';
        } elseif (str_contains($ua, 'Safari') && !str_contains($ua, 'Chrome')) {
            $browser = 'Safari';
        } elseif (str_contains($ua, 'Firefox')) {
            $browser = 'Firefox';
        } elseif (str_contains($ua, 'Edg')) {
            $browser = 'Edge';
        }

        // OS detection
        if (str_contains($ua, 'Windows')) {
            $os = 'Windows';
        } elseif (str_contains($ua, 'Mac OS')) {
            $os = 'macOS';
        } elseif (str_contains($ua, 'Linux')) {
            $os = 'Linux';
        } elseif (str_contains($ua, 'Android')) {
            $os = 'Android';
        } elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) {
            $os = 'iOS';
        }

        return $browser . ' on ' . $os;
    }
}
