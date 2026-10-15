<?php

namespace App\Livewire\Booking;

use App\Models\TutorProfile;
use App\Models\Subject;
use App\Models\Payment;
use App\Services\PaystackService;
use App\Services\BookingService;
use Livewire\Component;
use Illuminate\Support\Carbon;

class BookingWizard extends Component
{
    public TutorProfile $tutorProfile;
    public int    $step             = 1;
    public int    $duration         = 60;
    public string $scheduledDate    = '';
    public string $scheduledTime    = '';
    public ?int   $subjectId        = null;
    public string $bookingFor       = 'self';
    public ?int   $childProfileId   = null;
    public string $notes            = '';

    // Payment state
    public ?string $paystackReference  = null;
    public ?string $paystackAccessCode = null;
    public bool   $paymentProcessing   = false;
    public bool   $bookingConfirmed    = false;
    public ?string $studentCode        = null;
    public ?string $bookingUuid        = null;

    public function mount(TutorProfile $tutorProfile)
    {
        $this->tutorProfile = $tutorProfile;
        $this->scheduledDate = now()->addDay()->format('Y-m-d');
    }

    public function getSessionPriceProperty(): float
    {
        $prices = \App\Models\TutorProfile::sessionPrices();
        return (float) ($prices[$this->duration]['price'] ?? 300);
    }

    public function getSessionNameProperty(): string
    {
        $prices = \App\Models\TutorProfile::sessionPrices();
        return $prices[$this->duration]['name'] ?? 'Session';
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            $this->validate([
                'duration'      => 'required|in:15,30,45,60',
                'scheduledDate' => 'required|date|after:today',
                'scheduledTime' => 'required',
            ]);
        }
        $this->step++;
    }

    public function prevStep() { $this->step = max(1, $this->step - 1); }

    public function initPayment(PaystackService $paystack, BookingService $bookingService)
    {
        $this->validate([
            'duration'      => 'required|in:15,30,45,60',
            'scheduledDate' => 'required|date|after:today',
            'scheduledTime' => 'required',
        ]);

        $scheduledAt = Carbon::parse("{$this->scheduledDate} {$this->scheduledTime}");

        $booking = $bookingService->createBooking([
            'tutor_profile_id' => $this->tutorProfile->id,
            'student_id'       => auth()->id(),
            'subject_id'       => $this->subjectId,
            'scheduled_at'     => $scheduledAt,
            'duration_minutes' => $this->duration,
            'booking_for'      => $this->bookingFor,
            'child_profile_id' => $this->childProfileId,
            'notes'            => $this->notes,
        ]);

        $reference = $paystack->generateReference();
        $result = $paystack->initializeTransaction(
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
                'paystack_access_code' => $result['data']['access_code'],
                'status'               => 'pending',
            ]);

            $this->paystackReference  = $reference;
            $this->paystackAccessCode = $result['data']['access_code'];

            // Dispatch JS to open Paystack popup
            $this->dispatch('open-paystack', [
                'access_code' => $result['data']['access_code'],
                'reference'   => $reference,
                'email'       => auth()->user()->email,
                'amount'      => (int) round($booking->total_amount * 100),
            ]);
        }
    }

    public function paymentSuccess(string $reference, BookingService $bookingService)
    {
        $payment = Payment::where('paystack_reference', $reference)->firstOrFail();
        $payment->update(['status' => 'success', 'paid_at' => now()]);
        $bookingService->confirmBooking($payment->booking);

        $this->bookingConfirmed = true;
        $this->studentCode      = $payment->booking->fresh()->session_code_student;
        $this->bookingUuid      = $payment->booking->uuid;
        $this->step             = 3;
    }

    public function render()
    {
        return view('livewire.booking.booking-wizard', [
            'subjects' => $this->tutorProfile->subjects,
            'children' => auth()->user()?->childProfiles ?? collect(),
        ]);
    }
}
