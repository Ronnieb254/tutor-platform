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
            'tutor.tutorProfile.subjects',
            'subject',
            'messages.sender',
            'review',
            'payment',
        ]);

        $roomName = $booking->meeting_room;
        $joinUrl  = $booking->getBBBJoinUrl($user);

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
            ->firstOrFail();

        // Verify with Paystack API
        $result = $this->paystack->verifyTransaction($request->reference);

        if (($result['data']['status'] ?? '') === 'success') {
            $payment->update(['status' => 'success', 'paid_at' => now()]);
            $booking = $payment->booking;
            $this->bookingService->confirmBooking($booking);
            $fresh = $booking->fresh();

            return response()->json([
                'status'       => 'ok',
                'booking_uuid' => $fresh->uuid,
                'student_code' => $fresh->session_code_student,
                'tutor_code'   => $fresh->session_code_tutor,
                'booking_url'  => route('bookings.show', $fresh->uuid),
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Payment verification failed.'], 422);
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
}
