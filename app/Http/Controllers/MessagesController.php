<?php

namespace App\Http\Controllers;

use App\Models\{Message, Booking};
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessagesController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    /**
     * Show messages page
     */
    public function index()
    {
        $user = Auth::user();
        $conversationsArray = [];
        
        if ($user->isTutor()) {
            // Tutors see all students they have sessions with
            $studentIds = Booking::where('tutor_id', $user->id)
                ->distinct()
                ->pluck('student_id')
                ->toArray();
            
            $students = \App\Models\User::whereIn('id', $studentIds)->get();
            
            foreach ($students as $student) {
                $unreadCount = Message::where('sender_id', $student->id)
                    ->where('receiver_id', $user->id)
                    ->whereNull('read_at')
                    ->count();
                
                $conversationsArray[] = [
                    'user' => $student,
                    'unread' => $unreadCount
                ];
            }
        } else {
            // Students see all tutors they have sessions with
            $tutorIds = Booking::where('student_id', $user->id)
                ->distinct()
                ->pluck('tutor_id')
                ->toArray();
            
            $tutors = \App\Models\User::whereIn('id', $tutorIds)->get();
            
            foreach ($tutors as $tutor) {
                $unreadCount = Message::where('sender_id', $tutor->id)
                    ->where('receiver_id', $user->id)
                    ->whereNull('read_at')
                    ->count();
                
                $conversationsArray[] = [
                    'user' => $tutor,
                    'unread' => $unreadCount
                ];
            }
        }
        
        // Convert to Collection for blade compatibility
        $conversations = collect($conversationsArray);
        
        return view('messages.index', compact('conversations'));
    }

    /**
     * Get messages with a specific user
     */
    public function getMessages($userId)
    {
        $user = Auth::user();
        
        // Verify that user has a session with this person
        $hasSession = Booking::where(function ($q) use ($user, $userId) {
            $q->where('tutor_id', $user->id)->where('student_id', $userId)
              ->orWhere('tutor_id', $userId)->where('student_id', $user->id);
        })->exists();
        
        if (!$hasSession) {
            return response()->json([
                'success' => false,
                'message' => 'No session found with this user'
            ], 403);
        }
        
        $messages = Message::where(function ($q) use ($user, $userId) {
            $q->where('sender_id', $user->id)->where('receiver_id', $userId)
              ->orWhere('sender_id', $userId)->where('receiver_id', $user->id);
        })
        ->with(['sender:id,name,avatar', 'receiver:id,name,avatar'])
        ->orderBy('created_at')
        ->get();
        
        // Mark as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Send message to user
     */
    public function sendMessage(Request $request, $userId)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);
        
        // Verify that user has a session with this person
        $hasSession = Booking::where(function ($q) use ($user, $userId) {
            $q->where('tutor_id', $user->id)->where('student_id', $userId)
              ->orWhere('tutor_id', $userId)->where('student_id', $user->id);
        })->exists();
        
        if (!$hasSession) {
            return response()->json([
                'success' => false,
                'message' => 'No session found with this user'
            ], 403);
        }
        
        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $userId,
            'body' => $validated['body'],
            'booking_id' => null, // Direct messages not tied to booking
        ]);
        
        $message->load(['sender:id,name,avatar', 'receiver:id,name,avatar']);
        
        // Send notification
        $this->notificationService->notifyNewMessage($message);
        
        return response()->json([
            'success' => true,
            'message' => 'Message sent',
            'data' => $message
        ], 201);
    }
}
