<?php

namespace App\Services;

use App\Models\{User, Booking, Message};
use App\Notifications\{
    BookingCreatedNotification,
    BookingConfirmedNotification,
    BookingCancelledNotification,
    NewMessageNotification,
    SessionReminderNotification,
    UserJoinedSessionNotification
};
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Notify tutor about new booking
     */
    public function notifyBookingCreated(Booking $booking): void
    {
        if ($this->shouldNotify($booking->tutor, 'booking')) {
            $booking->tutor->notify(new BookingCreatedNotification($booking));
        }
    }

    /**
     * Notify student that booking was confirmed
     */
    public function notifyBookingConfirmed(Booking $booking): void
    {
        if ($this->shouldNotify($booking->student, 'booking')) {
            $booking->student->notify(new BookingConfirmedNotification($booking));
        }
    }

    /**
     * Notify both parties about cancellation
     */
    public function notifyBookingCancelled(Booking $booking, string $cancelledBy): void
    {
        $otherParty = $cancelledBy === 'student' ? $booking->tutor : $booking->student;
        
        if ($this->shouldNotify($otherParty, 'booking')) {
            $otherParty->notify(new BookingCancelledNotification($booking, $cancelledBy));
        }
    }

    /**
     * Notify receiver about new message
     */
    public function notifyNewMessage(Message $message): void
    {
        if ($this->shouldNotify($message->receiver, 'message')) {
            $message->receiver->notify(new NewMessageNotification($message));
        }
    }

    /**
     * Send session reminders (1 hour before)
     */
    public function notifySessionReminder(Booking $booking): void
    {
        $users = [$booking->student, $booking->tutor];
        
        foreach ($users as $user) {
            if ($this->shouldNotify($user, 'reminder')) {
                $user->notify(new SessionReminderNotification($booking));
            }
        }
    }

    /**
     * Notify when user joins session
     */
    public function notifyUserJoined(Booking $booking, User $joiner): void
    {
        $otherUser = $joiner->id === $booking->student_id ? $booking->tutor : $booking->student;
        
        if ($this->shouldNotify($otherUser, 'booking')) {
            $otherUser->notify(new UserJoinedSessionNotification($booking, $joiner));
        }
    }

    /**
     * Get user notifications
     */
    public function getUserNotifications(User $user, int $limit = 10)
    {
        return $user->notifications()->latest()->limit($limit)->get();
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(string $notificationId): void
    {
        $notification = \Illuminate\Notifications\DatabaseNotification::find($notificationId);
        
        if ($notification) {
            $notification->markAsRead();
        }
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead(User $user): void
    {
        $user->unreadNotifications->markAsRead();
    }

    /**
     * Check if user should receive notification type
     */
    private function shouldNotify(User $user, string $type): bool
    {
        $preferences = $user->notificationPreferences;
        
        if (!$preferences) {
            return true; // Default to sending if no preferences set
        }

        return match($type) {
            'booking' => $preferences->booking_notifications,
            'message' => $preferences->message_notifications,
            'reminder' => $preferences->reminder_notifications,
            default => true,
        };
    }
}
