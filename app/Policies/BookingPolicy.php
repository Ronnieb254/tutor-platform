<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->student_id
            || $user->id === $booking->tutor_id
            || $user->isAdmin();
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return ($user->id === $booking->student_id || $user->id === $booking->tutor_id)
            && in_array($booking->status, ['pending', 'confirmed'])
            && $booking->scheduled_at->isFuture();
    }
}
