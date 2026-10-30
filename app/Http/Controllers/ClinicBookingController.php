<?php

namespace App\Http\Controllers;

use App\Models\ExaminerClinic;
use App\Models\ClinicEnrollment;
use App\Models\Payment;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClinicBookingController extends Controller
{
    protected $paystack;

    public function __construct(PaystackService $paystack)
    {
        $this->paystack = $paystack;
    }

    /**
     * Show the clinic checkout page before payment.
     */
    public function checkout(Request $request, ExaminerClinic $clinic)
    {
        $user = $request->user();

        // Check if already enrolled
        if ($clinic->isEnrolledBy($user)) {
            return redirect()->route('examiner-clinic')
                ->with('info', 'You are already enrolled in this clinic session.');
        }

        // Check if full
        if ($clinic->isFull()) {
            return redirect()->route('examiner-clinic')
                ->with('error', 'This session is full. A new session may be available soon.');
        }

        return view('pages.clinic-checkout', [
            'clinic' => $clinic,
            'user' => $user,
            'price' => $clinic->price,
        ]);
    }

    /**
     * Process the payment via Paystack.
     * Redirects to Paystack payment gateway.
     */
    public function pay(Request $request, ExaminerClinic $clinic)
    {
        $user = $request->user();

        // Double-check capacity
        if ($clinic->isFull()) {
            return back()->with('error', 'Session is full. Please try another session.');
        }

        // Check not already enrolled
        if ($clinic->isEnrolledBy($user)) {
            return back()->with('error', 'You are already enrolled in this session.');
        }

        // Initialize Paystack transaction
        $reference = $this->paystack->generateReference('CLINIC');
        
        try {
            $result = $this->paystack->initializeTransaction(
                $user->email,
                (float) $clinic->price, // PaystackService multiplies by 100 internally
                $reference,
                [
                    'clinic_id' => $clinic->id,
                    'user_id' => $user->id,
                    'type' => 'clinic',
                ]
            );

            if (!$result['status']) {
                return back()->with('error', 'Payment initialization failed. Please try again.');
            }

            // Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'clinic_id' => $clinic->id,
                'amount' => $clinic->price,
                'currency' => 'KES',
                'payment_type' => 'clinic',
                'status' => 'pending',
                'paystack_reference' => $reference,
                'paystack_access_code' => $result['data']['access_code'] ?? null,
                'metadata' => [
                    'clinic_id' => $clinic->id,
                    'clinic_title' => $clinic->title ?? $clinic->name,
                ]
            ]);
            // Commission auto-calculated via Payment model boot method

            // Store reference in session for callback verification
            session(['clinic_payment_ref' => $reference, 'clinic_id' => $clinic->id]);

            // Redirect to Paystack payment page
            return redirect($result['data']['authorization_url']);

        } catch (\Exception $e) {
            return back()->with('error', 'Payment error: ' . $e->getMessage());
        }
    }

    /**
     * Handle Paystack callback/redirect after payment.
     * User is redirected here from Paystack after payment.
     */
    public function callback(Request $request)
    {
        $reference = $request->get('reference');
        $clinicId = session('clinic_id');

        if (!$reference || !$clinicId) {
            return redirect()->route('examiner-clinic')
                ->with('error', 'Invalid payment reference. Please contact support.');
        }

        try {
            // Verify payment with Paystack
            $verifyResult = $this->paystack->verifyTransaction($reference);

            if ($verifyResult['status'] !== true) {
                return redirect()->route('examiner-clinic')
                    ->with('error', 'Payment verification failed.');
            }

            $paymentData = $verifyResult['data'];

            // Update payment record
            $payment = Payment::where('paystack_reference', $reference)->first();
            if ($payment) {
                $payment->update([
                    'status' => 'success',
                    'paid_at' => now(),
                ]);
            }

            // Get clinic and enroll user
            $clinic = ExaminerClinic::findOrFail($clinicId);
            $user = auth()->user();

            // Double-check not already enrolled
            if ($clinic->isEnrolledBy($user)) {
                return redirect()->route('clinic.my-bookings')
                    ->with('warning', 'You were already enrolled in this clinic.');
            }

            // Check capacity one more time
            if ($clinic->isFull()) {
                // Refund scenario (for now, just notify)
                return redirect()->route('examiner-clinic')
                    ->with('error', 'Session became full. Please contact support for refund assistance.');
            }

            // Create enrollment
            $enrollment = ClinicEnrollment::create([
                'clinic_id' => $clinic->id,
                'student_id' => $user->id,
                'payment_id' => $payment->id,
                'session_code' => strtoupper(Str::random(8)),
            ]);

            // If this enrollment fills the session, auto-create next one
            if ($clinic->fresh()->isFull() && $clinic->parent_clinic_id === null) {
                $clinic->autoCreateNextSession();
            }

            // Clear session
            session()->forget(['clinic_payment_ref', 'clinic_id']);

            return redirect()->route('clinic.my-bookings')
                ->with('success', "Booking confirmed! Your session code: {$enrollment->session_code}. The clinic is on {$clinic->scheduled_at->format('D, M j \a\t g:ia')}.");

        } catch (\Exception $e) {
            return redirect()->route('examiner-clinic')
                ->with('error', 'Payment verification error: ' . $e->getMessage());
        }
    }

    /**
     * Show user's clinic bookings (my bookings page).
     */
    public function myBookings(Request $request)
    {
        $user = $request->user();

        $bookings = ClinicEnrollment::where('student_id', $user->id)
            ->with(['clinic.tutor', 'clinic.subject', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $upcomingCount = $bookings->filter(fn($b) => $b->clinic->scheduled_at->isFuture())->count();

        return view('pages.clinic-my-bookings', compact('bookings', 'upcomingCount'));
    }

    /**
     * Join clinic session via Jitsi.
     * Checks enrollment and generates a secure time-limited token.
     */
    public function joinSession(Request $request, ExaminerClinic $clinic)
    {
        $user = $request->user();

        // Admins and the clinic tutor can join as moderator without enrollment
        $isModerator = $user->isAdmin() || $user->id === $clinic->tutor_id || $user->id === $clinic->created_by;

        if (!$isModerator) {
            // Verify enrollment for students
            $enrollment = ClinicEnrollment::where('clinic_id', $clinic->id)
                ->where('student_id', $user->id)
                ->first();

            if (!$enrollment) {
                return redirect()->route('clinic.my-bookings')
                    ->with('error', 'You are not enrolled in this clinic session. Please book a spot first.');
            }

            // Allow joining 15 minutes before session starts
            $canJoinFrom = $clinic->scheduled_at->copy()->subMinutes(15);
            if (now()->lt($canJoinFrom)) {
                return back()->with('error', "This clinic starts at {$clinic->scheduled_at->format('g:ia')}. You can join from " . $canJoinFrom->format('g:ia') . '.');
            }

            // Mark attendance
            if (!$enrollment->attended) {
                $enrollment->update(['attended' => true]);
            }
        }

        // Build secure token
        $tokenData = [
            'clinic_id'    => $clinic->id,
            'user_id'      => $user->id,
            'is_moderator' => $isModerator,
            'examiner_id'  => $isModerator ? $user->id : null,
            'enrollment_id'=> isset($enrollment) ? $enrollment->id : null,
            'session_code' => $clinic->session_code ?? ('MwalimuClinic-' . $clinic->id),
            'issued_at'    => now()->timestamp,
            'expires_at'   => now()->addHours(3)->timestamp,
        ];

        $token = encrypt($tokenData);

        return redirect()->route('clinic.session-room', ['token' => $token]);
    }

    /**
     * Generate secure session URL (platform-only access).
     * This URL is platform-generated and includes auth tokens.
     */
    private function generateSessionUrl(ExaminerClinic $clinic, ClinicEnrollment $enrollment): string
    {
        // Generate a time-limited access token
        $tokenData = [
            'clinic_id' => $clinic->id,
            'enrollment_id' => $enrollment->id,
            'user_id' => auth()->id(),
            'session_code' => $clinic->session_code ?? ('clinic-' . $clinic->id),
            'issued_at' => now()->timestamp,
            'expires_at' => now()->addHours(2)->timestamp, // Valid for 2 hours
        ];

        $token = encrypt($tokenData);

        // Return route to session room with token
        return route('clinic.session-room', ['token' => $token]);
    }

    /**
     * Display the actual Jitsi meeting room.
     * Verifies token before allowing access.
     * Handles both student tokens and moderator tokens.
     */
    public function sessionRoom(Request $request)
    {
        $token = $request->get('token');

        if (!$token) {
            abort(403, 'Access denied. Invalid or missing session token.');
        }

        try {
            $data = decrypt($token);

            // Verify token not expired
            if (now()->timestamp > $data['expires_at']) {
                abort(403, 'Session token expired. Please rejoin from the platform.');
            }

            // Verify token belongs to the authenticated user
            $userId = $data['user_id'] ?? $data['examiner_id'] ?? null;
            if ($userId !== auth()->id()) {
                abort(403, 'Token mismatch. Please rejoin from the platform.');
            }

            $isModerator = isset($data['is_moderator']) && $data['is_moderator'] === true;

            $clinic = ExaminerClinic::findOrFail($data['clinic_id']);

            if ($isModerator) {
                $displayName  = auth()->user()->name . ' (Examiner)';
                $sessionCode  = null;
            } else {
                $enrollment = ClinicEnrollment::findOrFail($data['enrollment_id']);

                // Mark as attended
                if (!$enrollment->attended) {
                    $enrollment->update(['attended' => true]);
                }

                $displayName = auth()->user()->name;
                $sessionCode = $enrollment->session_code;
            }

            $roomId = $data['session_code'] ?? $clinic->session_code ?? ('MwalimuClinic-' . $clinic->id);

            return view('pages.clinic-jitsi-room', [
                'clinic'      => $clinic,
                'roomId'      => $roomId,
                'displayName' => $displayName,
                'isModerator' => $isModerator,
                'sessionCode' => $sessionCode,
            ]);

        } catch (\Exception $e) {
            abort(403, 'Invalid session token. Please rejoin from the platform.');
        }
    }
}
