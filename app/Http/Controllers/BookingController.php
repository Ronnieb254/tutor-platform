<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\BookingService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService  $bookingService,
        protected PaystackService $paystack
    ) {}

    public function show(Booking $booking)
    {
        // Allow student, tutor, or admin to view
        $user = auth()->user();
        if ($user->id !== $booking->student_id &&
            $user->id !== $booking->tutor_id &&
            !$user->isAdmin()) {
            abort(403);
        }

        $booking->load([
            'student',
            'tutor.tutorProfile',
            'subject',
            'messages.sender',
            'review',
            'payment',
        ]);

        // Load subjects only if tutorProfile exists
        if ($booking->tutor && $booking->tutor->tutorProfile) {
            $booking->tutor->tutorProfile->load('subjects');
        }

        $roomName = $booking->meeting_room;
        
        // Join URL is no longer used for the button (we route to internal meeting room),
        // but keep it for backward compatibility in the template
        try {
            $joinUrl = $booking->getBBBJoinUrl($user);
        } catch (\Throwable $e) {
            \Log::warning('Failed to generate Jitsi join URL: ' . $e->getMessage());
            $joinUrl = null;
        }

        return view('pages.bookings.show', compact('booking', 'roomName', 'joinUrl'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tutor_profile_id' => 'required|exists:tutor_profiles,id',
            'subject_id'       => 'nullable|exists:subjects,id',
            'scheduled_at'     => 'required|date|after:now',
            'duration_minutes' => 'required|in:20,40,60',
            'session_type'     => 'nullable|in:individual,group',
            'booking_for'      => 'nullable|in:self,child',
            'child_profile_id' => 'nullable|exists:child_profiles,id',
            'notes'            => 'nullable|string|max:500',
        ]);

        $booking = $this->bookingService->createBooking(array_merge(
            $request->all(),
            ['student_id' => auth()->id()]
        ));

        if ((float) $booking->total_amount <= 0) {
            Payment::create([
                'booking_id'         => $booking->id,
                'user_id'            => auth()->id(),
                'amount'             => 0,
                'currency'           => 'KES',
                'paystack_reference' => 'FREE-' . strtoupper(Str::random(12)),
                'status'             => 'success',
                'paid_at'            => now(),
            ]);

            $this->bookingService->confirmBooking($booking);
            $fresh = $booking->fresh();

            return response()->json([
                'status'       => 'ok',
                'free'         => true,
                'booking_id'   => $fresh->id,
                'booking_uuid' => $fresh->uuid,
                'student_code' => $fresh->session_code_student,
                'tutor_code'   => $fresh->session_code_tutor,
                'booking_url'  => route('bookings.show', $fresh->uuid),
            ]);
        }

        // Initialize Paystack transaction
        $reference = $this->paystack->generateReference();
        
        try {
            $result = $this->paystack->initializeTransaction(
                auth()->user()->email,
                $booking->total_amount,
                $reference,
                ['booking_id' => $booking->id]
            );

            \Log::info('Paystack initialization response', ['result' => $result]);

            if ($result['status'] ?? false) {
                Payment::create([
                    'booking_id'           => $booking->id,
                    'user_id'              => auth()->id(),
                    'amount'               => $booking->total_amount,
                    'currency'             => 'KES',
                    'paystack_reference'   => $reference,
                    'paystack_access_code' => $result['data']['access_code'] ?? null,
                    'status'               => 'pending',
                ]);

                return response()->json([
                    'status'      => 'ok',
                    'access_code' => $result['data']['access_code'],
                    'reference'   => $reference,
                    'booking_id'  => $booking->id,
                ]);
            }

            $errorMessage = $result['message'] ?? 'Could not initialize payment.';
            \Log::error('Paystack initialization failed', [
                'result' => $result,
                'booking_id' => $booking->id,
                'user_email' => auth()->user()->email,
                'amount' => $booking->total_amount,
            ]);

            $booking->delete();
            return response()->json([
                'status' => 'error', 
                'message' => $errorMessage,
                'debug' => config('app.debug') ? $result : null
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Paystack exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $booking->delete();
            return response()->json([
                'status' => 'error', 
                'message' => 'Payment gateway error: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function confirm(Request $request)
    {
        $request->validate(['reference' => 'required|string']);

        $payment = Payment::where('paystack_reference', $request->reference)
            ->where('status', 'pending')
            ->first();

        // Already confirmed (e.g. by webhook before the JS confirm call arrived)
        if (!$payment) {
            $payment = Payment::where('paystack_reference', $request->reference)
                ->where('status', 'success')
                ->first();

            if ($payment && $payment->booking) {
                $fresh = $payment->booking->fresh();

                // If booking is confirmed but has no codes (edge case from previous bug), generate them
                if ($fresh->status === 'confirmed' && !$fresh->session_code_student) {
                    $fresh->update([
                        'session_code_tutor'   => 'TUT-' . strtoupper(\Illuminate\Support\Str::random(6)),
                        'session_code_student' => 'STU-' . strtoupper(\Illuminate\Support\Str::random(6)),
                        'meeting_room'         => $fresh->meeting_room ?: 'Mwalimu-session-' . $fresh->uuid,
                        'meeting_url'          => $fresh->meeting_url ?: 'https://meet.jit.si/Mwalimu-session-' . $fresh->uuid,
                    ]);
                    $fresh->refresh();
                }

                // If booking is still pending but payment succeeded, confirm it now
                if ($fresh->status === 'pending') {
                    try {
                        $this->bookingService->confirmBooking($fresh);
                    } catch (\Throwable $e) {
                        \Log::error('confirmBooking fallback failed: ' . $e->getMessage());
                        $fresh->update([
                            'status'               => 'confirmed',
                            'session_code_tutor'   => 'TUT-' . strtoupper(\Illuminate\Support\Str::random(6)),
                            'session_code_student' => 'STU-' . strtoupper(\Illuminate\Support\Str::random(6)),
                            'meeting_room'         => 'Mwalimu-session-' . $fresh->uuid,
                            'meeting_url'          => 'https://meet.jit.si/Mwalimu-session-' . $fresh->uuid,
                        ]);
                    }
                    $fresh->refresh();
                }

                return response()->json([
                    'status'       => 'ok',
                    'booking_uuid' => $fresh->uuid,
                    'student_code' => $fresh->session_code_student,
                    'booking_url'  => route('bookings.show', $fresh->uuid),
                ]);
            }

            return response()->json(['status' => 'error', 'message' => 'Payment record not found.'], 404);
        }

        // Verify with Paystack API
        $result = $this->paystack->verifyTransaction($request->reference);

        \Log::info('Paystack verify result', ['reference' => $request->reference, 'result' => $result]);

        // Paystack returns data.status = 'success' for a completed payment
        $paystackStatus = $result['data']['status'] ?? '';

        if ($paystackStatus === 'success') {
            $payment->update(['status' => 'success', 'paid_at' => now()]);

            $booking = $payment->booking;

            if ($booking && $booking->status !== 'confirmed') {
                try {
                    $this->bookingService->confirmBooking($booking);
                } catch (\Throwable $e) {
                    // confirmBooking failed but payment is already marked success.
                    // Force-update the booking status and codes so the student is not left hanging.
                    \Log::error('confirmBooking failed, applying fallback: ' . $e->getMessage(), [
                        'booking_id' => $booking->id,
                        'trace'      => $e->getTraceAsString(),
                    ]);

                    $tutorCode   = 'TUT-' . strtoupper(\Illuminate\Support\Str::random(6));
                    $studentCode = 'STU-' . strtoupper(\Illuminate\Support\Str::random(6));

                    $booking->update([
                        'status'               => 'confirmed',
                        'session_code_tutor'   => $tutorCode,
                        'session_code_student' => $studentCode,
                        'meeting_room'         => 'Mwalimu-session-' . $booking->uuid,
                        'meeting_url'          => 'https://meet.jit.si/Mwalimu-session-' . $booking->uuid,
                    ]);
                }
            }

            $fresh = $booking->fresh();

            return response()->json([
                'status'       => 'ok',
                'booking_uuid' => $fresh->uuid,
                'student_code' => $fresh->session_code_student,
                'booking_url'  => route('bookings.show', $fresh->uuid),
            ]);
        }

        \Log::warning('Paystack verify: unexpected status', [
            'reference'      => $request->reference,
            'paystack_status'=> $paystackStatus,
            'full_result'    => $result,
        ]);

        return response()->json([
            'status'  => 'error',
            'message' => 'Payment not confirmed by Paystack (status: ' . $paystackStatus . ').',
        ], 422);
    }

    public function cancel(Booking $booking, Request $request)
    {
        $user = auth()->user();
        if ($user->id !== $booking->student_id && $user->id !== $booking->tutor_id) {
            abort(403);
        }
        $this->bookingService->cancelBooking($booking, $request->reason ?? '', $user);
        return back()->with('success', 'Booking cancelled.');
    }

    public function markJoined(Booking $booking)
    {
        $user = auth()->user();
        if ($user->id === $booking->tutor_id) {
            $booking->update(['tutor_joined' => true]);
        } elseif ($user->id === $booking->student_id) {
            $booking->update(['student_joined' => true]);
        }
        return response()->json(['status' => 'ok']);
    }

    public function meetingRoom(Booking $booking)
    {
        // Allow student, tutor, or admin to view
        $user = auth()->user();
        if ($user->id !== $booking->student_id &&
            $user->id !== $booking->tutor_id &&
            !$user->isAdmin()) {
            abort(403);
        }

        // Only allow access to confirmed bookings
        if ($booking->status !== 'confirmed') {
            return redirect()
                ->route('bookings.show', $booking->uuid)
                ->with('error', 'This session has not been confirmed yet.');
        }

        // Check if session is starting within 15 minutes or already started
        $scheduledTime = \Carbon\Carbon::parse($booking->scheduled_at);
        $now = now();
        $minutesUntilStart = $now->diffInMinutes($scheduledTime, false);
        $minutesSinceStart = $scheduledTime->diffInMinutes($now, false);

        // Allow entry 15 minutes before or up to duration + 30 minutes after
        $canJoin = ($minutesUntilStart <= 15) && ($minutesSinceStart <= ($booking->duration_minutes + 30));

        if (!$canJoin) {
            return redirect()
                ->route('bookings.show', $booking->uuid)
                ->with('error', 'You can only join the session 15 minutes before it starts.');
        }

        $booking->load(['student', 'tutor', 'subject']);

        return view('pages.bookings.meeting-room', compact('booking'));
    }

    /**
     * Get available time slots for a tutor on a specific date.
     * Returns JSON array of available time slots, filtered by:
     * - Tutor's weekly availability schedule
     * - Existing bookings (non-cancelled)
     * - Current time (for today's date)
     */
    public function getAvailableSlots(\App\Models\TutorProfile $tutor, Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $date = \Carbon\Carbon::parse($request->date);
        $dayOfWeek = $date->dayOfWeek; // 0=Sunday, 6=Saturday

        // Get tutor's availability for this day
        $availability = $tutor->availabilities()
            ->where('is_active', true)
            ->where('day_of_week', $dayOfWeek)
            ->get();

        if ($availability->isEmpty()) {
            return response()->json([
                'status' => 'ok',
                'slots' => [],
                'message' => 'No availability set for this day.',
            ]);
        }

        // Generate time slots (30-minute intervals)
        $allSlots = [];
        foreach ($availability as $avail) {
            $start = \Carbon\Carbon::parse($date->format('Y-m-d') . ' ' . $avail->start_time);
            $end = \Carbon\Carbon::parse($date->format('Y-m-d') . ' ' . $avail->end_time);

            while ($start < $end) {
                $slotEnd = $start->copy()->addMinutes(30);
                if ($slotEnd <= $end) {
                    $allSlots[] = [
                        'time' => $start->format('H:i'),
                        'label' => $start->format('g:i A'),
                        'datetime' => $start->format('Y-m-d H:i:s'),
                    ];
                }
                $start->addMinutes(30);
            }
        }

        // Get existing bookings for this tutor on this date (non-cancelled)
        $existingBookings = \App\Models\Booking::where('tutor_id', $tutor->user_id)
            ->whereDate('scheduled_at', $date)
            ->whereNotIn('status', ['cancelled'])
            ->get()
            ->map(function ($booking) {
                $start = \Carbon\Carbon::parse($booking->scheduled_at);
                $end = $start->copy()->addMinutes($booking->duration_minutes);
                return ['start' => $start, 'end' => $end];
            });

        // Filter out booked slots
        $availableSlots = array_filter($allSlots, function ($slot) use ($existingBookings, $date) {
            $slotStart = \Carbon\Carbon::parse($slot['datetime']);

            // If date is today, only show slots at least 1 hour from now
            if ($date->isToday() && $slotStart <= now()->addHour()) {
                return false;
            }

            // Check if this slot overlaps with any existing booking
            $slotEnd = $slotStart->copy()->addMinutes(30);
            foreach ($existingBookings as $booking) {
                if ($slotStart < $booking['end'] && $slotEnd > $booking['start']) {
                    return false; // Overlaps
                }
            }

            return true;
        });

        // Re-index array
        $availableSlots = array_values($availableSlots);

        return response()->json([
            'status' => 'ok',
            'slots' => $availableSlots,
            'total' => count($availableSlots),
        ]);
    }
}