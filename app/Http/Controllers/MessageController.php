<?php

namespace App\Http\Controllers;

use App\Models\{Booking, Message};
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    public function index(Booking $booking)
    {
        if (!$this->canAccessBooking($booking)) {
            abort(403, 'Unauthorized');
        }

        $messages = $booking->messages()
            ->with(['sender:id,name,avatar', 'receiver:id,name,avatar'])
            ->latest()
            ->paginate(50);

        // Mark messages as read
        $this->markAsRead($booking);

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    public function store(Request $request, Booking $booking)
    {
        if (!$this->canAccessBooking($booking)) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $receiver_id = Auth::id() === $booking->student_id 
            ? $booking->tutor_id 
            : $booking->student_id;

        $message = Message::create([
            'booking_id' => $booking->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $receiver_id,
            'body' => $validated['body'],
        ]);

        $message->load(['sender:id,name,avatar', 'receiver:id,name,avatar']);

        // Send notification to receiver
        $this->notificationService->notifyNewMessage($message);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $message,
            ], 201);
        }

        return back()->with('success', 'Message sent!');
    }

    public function markAsRead(Booking $booking)
    {
        if (!$this->canAccessBooking($booking)) {
            abort(403, 'Unauthorized');
        }

        $booking->messages()
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Messages marked as read',
        ]);
    }

    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count,
        ]);
    }

    /**
     * Check if user can access this booking's messages
     */
    private function canAccessBooking(Booking $booking): bool
    {
        $user = Auth::user();
        return $user->id === $booking->student_id 
            || $user->id === $booking->tutor_id;
    }
}
