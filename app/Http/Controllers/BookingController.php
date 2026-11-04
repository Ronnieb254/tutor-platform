<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\BookingService;
use App\Services\DarajaService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected DarajaService  $daraja
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

        // Parse scheduled_at as EAT and convert to UTC before storing
        $scheduledAtEat = \Carbon\Carbon::parse($request->input('scheduled_at'), 'Africa/Nairobi')->utc();

        $booking = $this->bookingService->createBooking(array_merge(
            $request->all(),
            [
                'student_id'   => auth()->id(),
                'scheduled_at' => $scheduledAtEat,
            ]
        ));

        if ((float) $booking->total_amount <= 0) {
            Payment::create([
                'booking_id'      => $booking->id,
                'user_id'         => auth()->id(),
                'amount'          => 0,
                'currency'        => 'KES',
                'mpesa_reference' => 'FREE-' . strtoupper(Str::random(12)),
                'status'          => 'success',
                'paid_at'         => now(),
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

        // Validate phone number is present
        $request->validate(['phone' => 'required|string|min:9|max:15']);

        // Initiate M-Pesa STK Push
        $reference = $this->daraja->generateReference();

        try {
            $result = $this->daraja->initializeStkPush(
                $request->input('phone'),
                $booking->total_amount,
                $reference,
                'Mwalimu Session'
            );

            \Log::info('Daraja STK Push response', ['result' => $result]);

            $responseCode = $result['ResponseCode'] ?? ($result['errorCode'] ?? null);

            if ($responseCode === '0' || isset($result['_mock'])) {
                // Store checkout_request_id so callback can match this payment
                Payment::create([
                    'booking_id'          => $booking->id,
                    'user_id'             => auth()->id(),
                    'amount'              => $booking->total_amount,
                    'currency'            => 'KES',
                    'mpesa_reference'     => $reference,
                    'checkout_request_id' => $result['CheckoutRequestID'] ?? null,
                    'merchant_request_id' => $result['MerchantRequestID'] ?? null,
                    'status'              => 'pending',
                    'payer_phone'         => $this->daraja->normalizePhone($request->input('phone')),
                ]);

                return response()->json([
                    'status'               => 'ok',
                    'reference'            => $reference,
                    'checkout_request_id'  => $result['CheckoutRequestID'] ?? null,
                    'booking_id'           => $booking->id,
                    'message'              => $result['CustomerMessage'] ?? 'Check your phone for the M-Pesa prompt.',
                ]);
            }

            $errorMessage = $result['errorMessage'] ?? ($result['ResponseDescription'] ?? 'Could not initialize payment.');
            \Log::error('Daraja STK Push failed', [
                'result'     => $result,
                'booking_id' => $booking->id,
            ]);

            $booking->delete();
            return response()->json([
                'status'  => 'error',
                'message' => $errorMessage,
                'debug'   => config('app.debug') ? $result : null,
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Daraja exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $booking->delete();
            return response()->json([
                'status'  => 'error',
                'message' => 'Payment gateway error: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function confirm(Request $request)
    {
        $request->validate(['reference' => 'required|string']);

        $payment = Payment::where('mpesa_reference', $request->reference)
            ->where('status', 'pending')
            ->first();

        // Already confirmed (e.g. by Daraja callback before the poll arrived)
        if (!$payment) {
            $payment = Payment::where('mpesa_reference', $request->reference)
                ->where('status', 'success')
                ->first();

            if ($payment && $payment->booking) {
                $fresh = $payment->booking->fresh();

                if ($fresh->status === 'confirmed' && !$fresh->session_code_student) {
                    $fresh->update([
                        'session_code_tutor'   => 'TUT-' . strtoupper(\Illuminate\Support\Str::random(6)),
                        'session_code_student' => 'STU-' . strtoupper(\Illuminate\Support\Str::random(6)),
                        'meeting_room'         => $fresh->meeting_room ?: 'Mwalimu-session-' . $fresh->uuid,
                        'meeting_url'          => $fresh->meeting_url ?: 'https://meet.jit.si/Mwalimu-session-' . $fresh->uuid,
                    ]);
                    $fresh->refresh();
                }

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

            return response()->json(['status' => 'pending', 'message' => 'Awaiting M-Pesa confirmation.']);
        }

        // Payment still pending — return pending so the front-end keeps polling
        return response()->json(['status' => 'pending', 'message' => 'Awaiting M-Pesa confirmation.']);
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
     * Force-confirm a pending booking (admin or assigned tutor only).
     * Generates session codes, updates status, and sends email notifications to both parties.
     */
    public function forceConfirm(Booking $booking)
    {
        $user = auth()->user();

        // Only admin or the booking's own tutor may force-confirm
        if (!$user->isAdmin() && $user->id !== $booking->tutor_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorised.'], 403);
        }

        // If already confirmed — resend codes without changing anything
        if ($booking->status === 'confirmed') {
            $booking->load(['student', 'tutor', 'subject']);

            // Ensure codes exist (generate if missing)
            if (!$booking->session_code_student) {
                $booking->update([
                    'session_code_tutor'   => 'TUT-' . strtoupper(\Illuminate\Support\Str::random(6)),
                    'session_code_student' => 'STU-' . strtoupper(\Illuminate\Support\Str::random(6)),
                    'meeting_room'         => $booking->meeting_room ?: 'Mwalimu-session-' . $booking->uuid,
                    'meeting_url'          => $booking->meeting_url ?: 'https://meet.jit.si/Mwalimu-session-' . $booking->uuid,
                ]);
                $booking->refresh()->load(['student', 'tutor', 'subject']);
            }

            // Resend notifications — send synchronously, bypass queue
            try {
                \Illuminate\Support\Facades\Notification::sendNow(
                    $booking->student,
                    new \App\Notifications\BookingConfirmedStudent($booking)
                );
            } catch (\Throwable $e) {
                \Log::warning('Resend student notification failed: ' . $e->getMessage());
            }
            try {
                \Illuminate\Support\Facades\Notification::sendNow(
                    $booking->tutor,
                    new \App\Notifications\BookingConfirmedTutor($booking)
                );
            } catch (\Throwable $e) {
                \Log::warning('Resend tutor notification failed: ' . $e->getMessage());
            }

            \Log::info('Booking codes resent by admin #' . $user->id, ['booking_uuid' => $booking->uuid]);

            return response()->json([
                'status'  => 'ok',
                'message' => 'Session codes resent to student and tutor.',
            ]);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot confirm a cancelled booking.',
            ], 422);
        }

        // Pending or any other status — confirm it
        try {
            $this->bookingService->confirmBooking($booking);
        } catch (\Throwable $e) {
            // Fallback: directly update without service layer
            \Log::error('forceConfirm: confirmBooking failed, applying fallback: ' . $e->getMessage());
            $booking->update([
                'status'               => 'confirmed',
                'session_code_tutor'   => 'TUT-' . strtoupper(\Illuminate\Support\Str::random(6)),
                'session_code_student' => 'STU-' . strtoupper(\Illuminate\Support\Str::random(6)),
                'meeting_room'         => $booking->meeting_room ?: 'Mwalimu-session-' . $booking->uuid,
                'meeting_url'          => $booking->meeting_url ?: 'https://meet.jit.si/Mwalimu-session-' . $booking->uuid,
            ]);
            $booking->refresh()->load(['student', 'tutor', 'subject']);

            try {
                \Illuminate\Support\Facades\Notification::sendNow(
                    $booking->student,
                    new \App\Notifications\BookingConfirmedStudent($booking)
                );
            } catch (\Throwable $ne) {
                \Log::warning('Student notification failed: ' . $ne->getMessage());
            }
            try {
                \Illuminate\Support\Facades\Notification::sendNow(
                    $booking->tutor,
                    new \App\Notifications\BookingConfirmedTutor($booking)
                );
            } catch (\Throwable $ne) {
                \Log::warning('Tutor notification failed: ' . $ne->getMessage());
            }
        }

        \Log::info('Booking force-confirmed by ' . $user->role . ' #' . $user->id, [
            'booking_uuid' => $booking->uuid,
        ]);

        return response()->json([
            'status'  => 'ok',
            'message' => 'Booking confirmed. Codes sent to student and tutor.',
        ]);
    }

    /**
    /**
     * Get available time slots for a tutor on a specific date.
     * Returns JSON array of available time slots in EAT (Africa/Nairobi, UTC+3), filtered by:
     * - Tutor's weekly availability schedule
     * - Existing bookings (non-cancelled)
     * - Current time (for today's date)
     */
    public function getAvailableSlots(\App\Models\TutorProfile $tutor, Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $tz = new \DateTimeZone('Africa/Nairobi');
        $eat = 'Africa/Nairobi';

        // Parse date in EAT
        $date = \Carbon\Carbon::parse($request->date, $eat)->startOfDay();
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

        // Current time in EAT — used to filter out past slots
        $nowEAT = \Carbon\Carbon::now($eat);

        // Generate time slots (30-minute intervals) in EAT
        $allSlots = [];
        foreach ($availability as $avail) {
            $start = \Carbon\Carbon::parse(
                $date->format('Y-m-d') . ' ' . $avail->start_time, $eat
            );
            $end = \Carbon\Carbon::parse(
                $date->format('Y-m-d') . ' ' . $avail->end_time, $eat
            );

            while ($start < $end) {
                $slotEnd = $start->copy()->addMinutes(30);
                if ($slotEnd <= $end) {
                    $allSlots[] = [
                        'time'     => $start->format('H:i'),
                        'label'    => $start->format('g:i A') . ' EAT',
                        'datetime' => $start->format('Y-m-d H:i:s'),
                    ];
                }
                $start->addMinutes(30);
            }
        }

        // Get existing bookings for this tutor on this date (non-cancelled)
        $existingBookings = \App\Models\Booking::where('tutor_id', $tutor->user_id)
            ->whereDate('scheduled_at', $date->format('Y-m-d'))
            ->whereNotIn('status', ['cancelled'])
            ->get()
            ->map(function ($booking) use ($eat) {
                $start = \Carbon\Carbon::parse($booking->scheduled_at)->setTimezone($eat);
                $end   = $start->copy()->addMinutes($booking->duration_minutes);
                return ['start' => $start, 'end' => $end];
            });

        // Filter out booked and past slots
        $availableSlots = array_filter($allSlots, function ($slot) use ($existingBookings, $date, $nowEAT, $eat) {
            $slotStart = \Carbon\Carbon::parse($slot['datetime'], $eat);

            // If date is today, only show slots at least 1 hour from now (EAT)
            if ($date->isSameDay($nowEAT) && $slotStart <= $nowEAT->copy()->addHour()) {
                return false;
            }

            // Check if this slot overlaps with any existing booking
            $slotEnd = $slotStart->copy()->addMinutes(30);
            foreach ($existingBookings as $booking) {
                if ($slotStart < $booking['end'] && $slotEnd > $booking['start']) {
                    return false;
                }
            }

            return true;
        });

        // Re-index array
        $availableSlots = array_values($availableSlots);

        return response()->json([
            'status' => 'ok',
            'slots'  => $availableSlots,
            'total'  => count($availableSlots),
        ]);
    }
}