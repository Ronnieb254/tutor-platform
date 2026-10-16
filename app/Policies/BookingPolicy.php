<?php

namespace App\Policies;

use App\Models\{User, Booking};

class BookingPolicy
{
    /**
     * Determine if user can view the booking
     */
    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->student_id 
            || $user->id === $booking->tutor_id 
            || $user->isAdmin();
    }

    /**
     * Determine if user can confirm the booking
     */
    public function confirm(User $user, Booking $booking): bool
    {
        return $user->id === $booking->tutor_id && $booking->status === 'pending';
    }

    /**
     * Determine if user can cancel the booking
     */
    public function cancel(User $user, Booking $booking): bool
    {
        // Both student and tutor can cancel, but not if already completed
        return ($user->id === $booking->student_id || $user->id === $booking->tutor_id)
            && !in_array($booking->status, ['completed', 'cancelled']);
    }

    /**
     * Determine if user can complete the booking
     */
    public function complete(User $user, Booking $booking): bool
    {
        return $user->id === $booking->tutor_id 
            && $booking->status === 'confirmed';
    }

    /**
     * Determine if user can message in this booking
     */
    public function message(User $user, Booking $booking): bool
    {
        return $user->id === $booking->student_id 
            || $user->id === $booking->tutor_id;
    }
}
