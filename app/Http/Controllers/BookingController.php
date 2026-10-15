<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\BookingService;
use App\Services\PaystackService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService  $bookingService,
        protected PaystackService $paystack
    ) {}

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        $booking->load(['student', 'tutor', 'subject', 'messages.sender', 'review']);
        return view('pages.bookings.show', compact('booking'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tutor_profile_id' => 'required|exists:tutor_profiles,id',
            'subject_id'       => 'nullable|exists:subjects,id',
            'scheduled_at'     => 'required|date|after:now',
            'duration_minutes' => 'required|in:15,30,45,60',
            'booking_for'      => 'in:self,child',
            'child_profile_id' => 'nullable|exists:child_profiles,id',
        ]);

        $booking = $this->bookingService->createBooking(array_merge(
            $request->all(),
            ['student_id' => auth()->id()]
        ));

        // Initialize Paystack transaction
        $reference = $this->paystack->generateReference();
        $result    = $this->paystack->initializeTransaction(
            auth()->user()->email,
            $booking->total_amount,
            $reference,
            ['booking_id' => $booking->id]
        );

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
                'status'       => 'ok',
                'access_code'  => $result['data']['access_code'],
                'reference'    => $reference,
                'booking_id'   => $booking->id,
            ]);
        }

        $booking->delete();
        return response()->json(['status' => 'error', 'message' => 'Could not initialize payment.'], 422);
    }

    public function confirm(Request $request)
    {
        $request->validate(['reference' => 'required|string']);

        $payment = Payment::where('paystack_reference', $request->reference)
            ->where('status', 'pending')
            ->firstOrFail();

        // Verify with Paystack
        $result = $this->paystack->verifyTransaction($request->reference);

        if (($result['data']['status'] ?? '') === 'success') {
            $payment->update(['status' => 'success', 'paid_at' => now()]);
            $booking = $payment->booking;
            $this->bookingService->confirmBooking($booking);

            $fresh = $booking->fresh();

            return response()->json([
                'status'        => 'ok',
                'booking_uuid'  => $fresh->uuid,
                'student_code'  => $fresh->session_code_student,
                'tutor_code'    => $fresh->session_code_tutor,
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Payment verification failed.'], 422);
    }

    public function cancel(Booking $booking, Request $request)
    {
        $this->authorize('cancel', $booking);
        $this->bookingService->cancelBooking($booking, $request->reason ?? '', auth()->user());
        return back()->with('success', 'Booking cancelled.');
    }
}
