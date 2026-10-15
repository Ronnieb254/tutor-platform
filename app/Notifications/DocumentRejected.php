<?php

namespace App\Notifications;

use App\Models\TutorDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TutorDocument $document,
        public string $reason
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $docLabel = ucwords(str_replace('_', ' ', $this->document->type));
        return (new MailMessage)
            ->subject('Action Required: Document Rejected – Mwalimu')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your **{$docLabel}** was rejected by our verification team.")
            ->line("**Reason:** {$this->reason}")
            ->line('Please log in to your dashboard, go to **My Documents**, and re-upload a clear, valid copy.')
            ->action('Re-upload Documents', url('/dashboard/tutor?tab=documents'))
            ->line('If you have questions, contact us at info@ovaldesk.co.ke');
    }

    public function toArray(object $notifiable): array
    {
        $docLabel = ucwords(str_replace('_', ' ', $this->document->type));
        return [
            'type'    => 'document_rejected',
            'message' => "Your {$docLabel} was rejected: {$this->reason}. Please re-upload.",
        ];
    }
}
