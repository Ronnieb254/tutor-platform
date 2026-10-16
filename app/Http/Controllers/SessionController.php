<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class SessionController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->get('filter', 'upcoming');

        $query = $user->isTutor() 
            ? Booking::where('tutor_id', $user->id)
            : Booking::where('student_id', $user->id);

        $query->with(['student', 'tutor', 'subject']);

        if ($filter === 'upcoming') {
            $query->upcoming()->orderBy('scheduled_at');
        } elseif ($filter === 'history') {
            $query->history()->orderByDesc('scheduled_at');
        } else {
            $query->orderByDesc('scheduled_at');
        }

        $sessions = $query->paginate(10);

        return view('sessions.index', compact('sessions', 'filter'));
    }

    public function show(Booking $booking)
    {
        // Check authorization manually
        if (!$this->canViewBooking($booking)) {
            abort(403, 'Unauthorized');
        }

        $booking->load(['student', 'tutor', 'subject', 'messages.sender']);

        return view('sessions.show', compact('booking'));
    }

    public function join(Booking $booking)
    {
        // Check authorization manually
        if (!$this->canViewBooking($booking)) {
            abort(403, 'Unauthorized');
        }

        // Check authorization manually
        if (!$this->canViewBooking($booking)) {
            abort(403, 'Unauthorized');
        }

        // Check if booking is confirmed
        if ($booking->status !== 'confirmed') {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This session has not been confirmed yet.',
                ], 400);
            }
            return back()->with('error', 'This session has not been confirmed yet.');
        }

        // Check time window (60 min before to 30 min after)
        $now = now();
        $opensAt = $booking->scheduled_at->copy()->subMinutes(60);
        $closesAt = $booking->scheduled_at->copy()->addMinutes($booking->duration_minutes + 30);

        if (!$now->between($opensAt, $closesAt)) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The session is not yet available to join. You can join 1 hour before the scheduled time.',
                ], 400);
            }
            return back()->with('error', 'The session is not yet available to join.');
        }

        // Mark user as joined
        if (Auth::id() === $booking->student_id) {
            $booking->update(['student_joined' => true]);
        } else {
            $booking->update(['tutor_joined' => true]);
        }

        // Notify other party
        $this->notificationService->notifyUserJoined($booking, Auth::user());

        // Redirect to meeting URL
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Joining session...',
                'redirect' => $booking->meeting_url,
            ]);
        }

        return redirect($booking->meeting_url);
    }

    public function history()
    {
        $user = Auth::user();

        $query = $user->isTutor() 
            ? Booking::where('tutor_id', $user->id)
            : Booking::where('student_id', $user->id);

        $sessions = $query->history()
            ->with(['student', 'tutor', 'subject', 'review'])
            ->orderByDesc('scheduled_at')
            ->paginate(15);

        return view('sessions.history', compact('sessions'));
    }

    public function confirm(Request $request, Booking $booking)
    {
        if (!$this->canConfirmBooking($booking)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot confirm this booking.',
            ], 403);
        }

        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This booking cannot be confirmed.',
            ], 400);
        }

        $booking->update(['status' => 'confirmed']);

        // Send notification to student
        $this->notificationService->notifyBookingConfirmed($booking);

        return response()->json([
            'success' => true,
            'message' => 'Booking confirmed successfully!',
        ]);
    }

    public function cancel(Request $request, Booking $booking)
    {
        if (!$this->canCancelBooking($booking)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot cancel this booking.',
            ], 403);
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['reason'] ?? null,
            'cancelled_at' => now(),
        ]);

        // Determine who cancelled
        $cancelledBy = Auth::id() === $booking->student_id ? 'student' : 'tutor';

        // Send notification
        $this->notificationService->notifyBookingCancelled($booking, $cancelledBy);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully.',
        ]);
    }

    public function complete(Booking $booking)
    {
        if (!$this->canCompletBooking($booking)) {
            abort(403, 'Unauthorized');
        }

        $booking->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session marked as completed.',
        ]);
    }

    /**
     * Check if user can view a booking
     */
    private function canViewBooking(Booking $booking): bool
    {
        $user = Auth::user();
        return $user->id === $booking->student_id 
            || $user->id === $booking->tutor_id 
            || $user->isAdmin();
    }

    /**
     * Check if user can confirm booking
     */
    private function canConfirmBooking(Booking $booking): bool
    {
        $user = Auth::user();
        return $user->id === $booking->tutor_id && $booking->status === 'pending';
    }

    /**
     * Check if user can cancel booking
     */
    private function canCancelBooking(Booking $booking): bool
    {
        $user = Auth::user();
        return ($user->id === $booking->student_id || $user->id === $booking->tutor_id)
            && !in_array($booking->status, ['completed', 'cancelled']);
    }

    /**
     * Check if user can complete booking
     */
    private function canCompletBooking(Booking $booking): bool
    {
        $user = Auth::user();
        return $user->id === $booking->tutor_id 
            && $booking->status === 'confirmed';
    }
}
