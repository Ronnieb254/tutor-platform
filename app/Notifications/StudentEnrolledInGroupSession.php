<?php

namespace App\Notifications;

use App\Models\ClinicEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentEnrolledInGroupSession extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ClinicEnrollment $enrollment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $clinic = $this->enrollment->clinic;
        $student = $this->enrollment->student;
        $enrollmentCount = $clinic->enrollments()->where('status', '!=', 'cancelled')->count();

        return (new MailMessage)
            ->subject('New Student Enrolled in ' . $clinic->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A student has just enrolled in your group session!')
            ->line('')
            ->line('**Session Details:**')
            ->line('• **Title:** ' . $clinic->title)
            ->line('• **Date & Time:** ' . $clinic->scheduled_at->format('D, M j, Y \a\t g:ia'))
            ->line('• **Duration:** ' . $clinic->duration_minutes . ' minutes')
            ->line('• **Student:** ' . $student->name)
            ->line('• **Total Enrollments:** ' . $enrollmentCount . ' / ' . $clinic->max_students)
            ->line('')
            ->action('View Session Details', route('admin.examiner-clinics.show', $clinic))
            ->line('Thank you for teaching on Mwalimu!')
            ->salutation('Best regards, Mwalimu Team');
    }

    public function toDatabase(object $notifiable): array
    {
        $clinic = $this->enrollment->clinic;
        $student = $this->enrollment->student;

        return [
            'type' => 'group_session_enrollment',
            'title' => 'New student enrolled!',
            'message' => $student->name . ' enrolled in your group session "' . $clinic->title . '"',
            'clinic_id' => $clinic->id,
            'student_id' => $student->id,
            'enrollment_count' => $clinic->enrollments()->where('status', '!=', 'cancelled')->count(),
            'action_url' => route('admin.examiner-clinics.show', $clinic),
        ];
    }
}
