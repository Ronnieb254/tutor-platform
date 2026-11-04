<?php

namespace App\Livewire\Booking;

use App\Models\TutorProfile;
use App\Models\Subject;
use App\Models\Payment;
use App\Services\DarajaService;
use App\Services\BookingService;
use Livewire\Component;
use Illuminate\Support\Carbon;

class BookingWizard extends Component
{
    public TutorProfile $tutorProfile;
    public int    $step             = 1;
    public int    $duration         = 60;
    public string $sessionType     = 'individual';
    public string $scheduledDate    = '';
    public string $scheduledTime    = '';
    public ?int   $subjectId        = null;
    public string $bookingFor       = 'self';
    public ?int   $childProfileId   = null;
    public string $notes            = '';
    public string $phone            = '';  // M-Pesa phone number

    // Payment state
    public ?string $mpesaReference     = null;
    public ?string $checkoutRequestId  = null;
    public bool   $paymentProcessing   = false;
    public bool   $stkSent             = false;   // STK Push dispatched, waiting for customer
    public bool   $bookingConfirmed    = false;
    public ?string $studentCode        = null;
    public ?string $bookingUuid        = null;

    public function mount(TutorProfile $tutorProfile)
    {
        $this->tutorProfile = $tutorProfile;
        $this->scheduledDate = now()->format('Y-m-d');
    }

    public function getSessionPriceProperty(): float
    {
        $prices = \App\Models\TutorProfile::sessionPrices();
        $key = "{$this->duration}_{$this->sessionType}";
        // Fallback: try individual for this duration
        if (!isset($prices[$key])) {
            $key = "{$this->duration}_individual";
        }
        return (float) ($prices[$key]['price'] ?? 300);
    }

    public function getSessionNameProperty(): string
    {
        $prices = \App\Models\TutorProfile::sessionPrices();
        $key = "{$this->duration}_{$this->sessionType}";
        if (!isset($prices[$key])) {
            $key = "{$this->duration}_individual";
        }
        return $prices[$key]['name'] ?? 'Session';
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            $this->validate([
                'duration'      => 'required|in:20,40,60',
                'sessionType'   => 'required|in:individual,group',
                'scheduledDate' => 'required|date|after_or_equal:today',
                'scheduledTime' => 'required',
                'notes'         => 'required|string|min:5',
            ]);
        }
        $this->step++;
    }

    public function prevStep() { $this->step = max(1, $this->step - 1); }

    public function initPayment(DarajaService $daraja, BookingService $bookingService)
    {
        $this->validate([
            'duration'      => 'required|in:20,40,60',
            'sessionType'   => 'required|in:individual,group',
            'scheduledDate' => 'required|date|after:today',
            'scheduledTime' => 'required',
            'notes'         => 'required|string|min:5',
            'phone'         => 'required|string|min:9|max:15',
        ]);

        $scheduledAt = Carbon::parse("{$this->scheduledDate} {$this->scheduledTime}", 'Africa/Nairobi')
            ->utc();

        $booking = $bookingService->createBooking([
            'tutor_profile_id' => $this->tutorProfile->id,
            'student_id'       => auth()->id(),
            'subject_id'       => $this->subjectId,
            'scheduled_at'     => $scheduledAt,
            'duration_minutes' => $this->duration,
            'session_type'     => $this->sessionType,
            'booking_for'      => $this->bookingFor,
            'child_profile_id' => $this->childProfileId,
            'notes'            => $this->notes,
        ]);

        // Free session — confirm immediately
        if ((float) $booking->total_amount <= 0) {
            Payment::create([
                'booking_id'      => $booking->id,
                'user_id'         => auth()->id(),
                'amount'          => 0,
                'currency'        => 'KES',
                'mpesa_reference' => 'FREE-' . strtoupper(\Illuminate\Support\Str::random(12)),
                'status'          => 'success',
                'paid_at'         => now(),
            ]);
            $bookingService->confirmBooking($booking);
            $this->bookingConfirmed = true;
            $this->studentCode      = $booking->fresh()->session_code_student;
            $this->bookingUuid      = $booking->uuid;
            $this->step             = 3;
            return;
        }

        $reference = $daraja->generateReference();
        $result    = $daraja->initializeStkPush(
            $this->phone,
            $booking->total_amount,
            $reference,
            'Mwalimu Session'
        );

        $responseCode = $result['ResponseCode'] ?? ($result['errorCode'] ?? null);

        if ($responseCode === '0' || isset($result['_mock'])) {
            Payment::create([
                'booking_id'          => $booking->id,
                'user_id'             => auth()->id(),
                'amount'              => $booking->total_amount,
                'currency'            => 'KES',
                'mpesa_reference'     => $reference,
                'checkout_request_id' => $result['CheckoutRequestID'] ?? null,
                'merchant_request_id' => $result['MerchantRequestID'] ?? null,
                'payer_phone'         => $daraja->normalizePhone($this->phone),
                'status'              => 'pending',
            ]);

            $this->mpesaReference    = $reference;
            $this->checkoutRequestId = $result['CheckoutRequestID'] ?? null;
            $this->stkSent           = true;

            // Tell the JS layer to start polling
            $this->dispatch('stk-sent', [
                'reference'           => $reference,
                'checkout_request_id' => $result['CheckoutRequestID'] ?? null,
                'message'             => $result['CustomerMessage'] ?? 'Check your phone for the M-Pesa prompt.',
            ]);
        } else {
            $booking->delete();
            $this->addError('phone', 'Payment could not be initialised: ' . ($result['errorMessage'] ?? $result['ResponseDescription'] ?? 'Please try again.'));
        }
    }

    public function paymentSuccess(string $reference, BookingService $bookingService)
    {
        $payment = Payment::where('mpesa_reference', $reference)->firstOrFail();

        if ($payment->status !== 'success') {
            $payment->update(['status' => 'success', 'paid_at' => now()]);
        }

        $booking = $payment->booking;
        if ($booking && $booking->status !== 'confirmed') {
            $bookingService->confirmBooking($booking);
        }

        $this->bookingConfirmed = true;
        $this->studentCode      = $booking->fresh()->session_code_student;
        $this->bookingUuid      = $booking->uuid;
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
