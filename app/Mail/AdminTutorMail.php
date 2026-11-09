<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminTutorMail extends Mailable
{
    use Queueable, SerializesModels;

    private string $subjectText;
    private string $bodyText;
    private string $fromNameText;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $subject,
        string $body,
        string $fromName = 'Mwalimu Support'
    ) {
        $this->subjectText = $subject;
        $this->bodyText = $body;
        $this->fromNameText = $fromName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: 'support@ovaldesk.co.ke',
            subject: $this->subjectText,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-tutor-message',
            with: [
                'subject' => $this->subjectText,
                'body' => $this->bodyText,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
