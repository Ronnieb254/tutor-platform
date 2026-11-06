<?php

namespace App\Notifications;

use App\Models\ExaminerClinic;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClinicTutorAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ExaminerClinic $clinic) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $sessionType = $this->clinic->is_group_session ? 'group session' : 'clinic session';
        $scheduledAt = $this->clinic->scheduled_at
            ? $this->clinic->scheduled_at->format('D, d M Y \a\t H:i')
            : 'TBC';

        return (new MailMessage)
            ->subject('You have been assigned to a clinic session — Mwalimu')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have been assigned as the tutor for the following session:')
            ->line('**' . $this->clinic->title . '**')
            ->line('Subject: ' . ($this->clinic->subject?->name ?? '—'))
            ->line('Date / Time: ' . $scheduledAt)
            ->line('Duration: ' . $this->clinic->duration_minutes . ' minutes')
            ->line('Your tutor access code is: **' . $this->clinic->tutor_code . '**')
            ->line('Keep this code private. Use it to enter the session room on the day.')
            ->action('View Session', url('/clinic/' . $this->clinic->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'clinic_tutor_assigned',
            'clinic_id'   => $this->clinic->id,
            'title'       => $this->clinic->title,
            'tutor_code'  => $this->clinic->tutor_code,
            'scheduled_at'=> $this->clinic->scheduled_at?->toDateTimeString(),
            'message'     => 'You have been assigned as the tutor for: ' . $this->clinic->title,
            'url'         => url('/clinic/' . $this->clinic->id),
        ];
    }
}
