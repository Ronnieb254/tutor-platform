<?php

namespace App\Http\Controllers;

use App\Models\ExaminerClinic;
use App\Models\ClinicEnrollment;
use App\Models\Payment;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Public-facing Group Session controller.
 *
 * Group sessions are stored in the same `examiner_clinics` table, distinguished
 * by the `is_group_session` boolean. They reuse the Jitsi join infrastructure
 * from ClinicBookingController — only the listing/enrollment UI differs.
 */
class GroupSessionController extends Controller
{
    /**
     * Public listing of upcoming group sessions.
     */
    public function index(Request $request)
    {
        $search    = $request->get('search', '');
        $subjectId = $request->get('subject', '');
        $sort      = $request->get('sort', 'date');
        $track     = session('curriculum_track', '');

        // Load subjects filtered by active track (graceful fallback: show all if no track set)
        $subjects = $track
            ? Subject::active()
                ->whereHas('curriculum', fn($q) => $q->where('track', $track))
                ->with('category')->orderBy('name')->get()
            : Subject::active()->with('category')->orderBy('name')->get();

        $sessions = ExaminerClinic::upcoming()
            ->groupSessions()
            ->with(['tutor', 'subject', 'enrollments'])
            ->when($track && \Schema::hasColumn('examiner_clinics', 'curriculum_id'), function ($q) use ($track) {
                $q->where(function ($inner) use ($track) {
                    $inner->whereHas('curriculum', fn($c) => $c->where('track', $track))
                          ->orWhereNull('curriculum_id'); // include untagged content (legacy)
                });
            })
            ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
            ->when($search, function ($q) use ($search) {
                $q->whereHas('subject', fn($s) => $s->where('name', 'like', "%{$search}%"))
                  ->orWhere('title', 'like', "%{$search}%");
            });

        if ($sort === 'subject') {
            $sessions->orderByRaw('(SELECT name FROM subjects WHERE subjects.id = examiner_clinics.subject_id)');
        } elseif ($sort === 'price_low') {
            $sessions->orderBy('price');
        } elseif ($sort === 'price_high') {
            $sessions->orderByDesc('price');
        } else {
            $sessions->orderBy('scheduled_at');
        }

        $sessions = $sessions->paginate(12)->withQueryString();

        return view('pages.group-sessions.index', compact(
            'sessions', 'subjects', 'search', 'subjectId', 'sort', 'track'
        ));
    }

    /**
     * Single group session detail page.
     */
    public function show(ExaminerClinic $groupSession)
    {
        // Defensive: if column doesn't exist yet, treat it as a group session
        if (\Schema::hasColumn('examiner_clinics', 'is_group_session') && !$groupSession->is_group_session) {
            abort(404);
        }

        $groupSession->load(['tutor', 'subject', 'enrollments.student']);
        $seriesSessions = $groupSession->seriesSessions();

        return view('pages.group-sessions.show', compact('groupSession', 'seriesSessions'));
    }

    /**
     * Enroll a student in a group session via M-Pesa payment code submission.
     */
    public function enroll(Request $request, ExaminerClinic $groupSession)
    {
        if (\Schema::hasColumn('examiner_clinics', 'is_group_session') && !$groupSession->is_group_session) {
            return back()->with('error', 'This is not a group session.');
        }

        $request->validate([
            'phone'       => 'required|string|min:9|max:15',
            'mpesa_code'  => 'required|string|min:6|max:30',
        ]);

        $user = $request->user();

        if ($groupSession->isEnrolledBy($user)) {
            return back()->with('error', 'You are already enrolled in this group session.');
        }

        if ($groupSession->isFull()) {
            return back()->with('error', 'This session is full. Please choose another time slot.');
        }

        try {
            // Normalize phone number
            $daraja = app(\App\Services\DarajaService::class);
            $phone = $daraja->normalizePhone($request->input('phone'));
            $mpesaCode = strtoupper(trim($request->input('mpesa_code')));
            
            // Generate reference for tracking
            $reference = $daraja->generateReference('GRP');

            // Create payment record with pending status for admin verification
            $payment = Payment::create([
                'user_id'         => $user->id,
                'clinic_id'       => $groupSession->id,
                'amount'          => $groupSession->price,
                'currency'        => 'KES',
                'payment_type'    => 'group_session',
                'mpesa_code'      => $mpesaCode,
                'mpesa_reference' => $reference,
                'payer_phone'     => $phone,
                'status'          => 'pending_verification',
                'metadata'        => [
                    'session_id'     => $groupSession->id,
                    'session_title'  => $groupSession->title,
                    'tutor_id'       => $groupSession->tutor_id,
                    'submitted_at'   => now()->toDateTimeString(),
                ],
            ]);

            // Create provisional clinic enrollment (pending payment verification)
            $enrollment = ClinicEnrollment::create([
                'clinic_id'    => $groupSession->id,
                'student_id'   => $user->id,
                'payment_id'   => $payment->id,
                'enrolled_at'  => now(),
                'status'       => 'pending', // Will be activated once payment verified
            ]);

            \Log::info('Group session enrollment initiated', [
                'session_id' => $groupSession->id,
                'student_id' => $user->id,
                'payment_id' => $payment->id,
                'mpesa_code' => $mpesaCode,
            ]);

            // Redirect to pending verification page
            return redirect()
                ->route('group-sessions.payment-pending', ['reference' => $reference])
                ->with('info', 'Payment code submitted. Admin will verify within 2 hours.');

        } catch (\Exception $e) {
            \Log::error('Group session enrollment failed: ' . $e->getMessage(), [
                'session_id' => $groupSession->id,
                'user_id'    => $user->id,
            ]);
            return back()->with('error', 'Failed to process enrollment. Please try again or contact support.');
        }
    }

    /**
     * Show payment-pending page after student submits M-Pesa code.
     */
    public function paymentPending(Request $request)
    {
        $reference = $request->get('reference') ?? session('group_payment_ref');

        if (!$reference) {
            return redirect()->route('group-sessions.index')
                ->with('error', 'Payment reference missing.');
        }

        $payment = Payment::where('mpesa_reference', $reference)->first();

        if (!$payment) {
            return redirect()->route('group-sessions.index')
                ->with('error', 'Payment not found. Please contact support.');
        }

        $groupSession = $payment->clinic;

        // If already verified, redirect to enrollment confirmed page
        if ($payment->status === 'success') {
            return redirect()->route('group-sessions.my-sessions')
                ->with('success', 'Your enrollment is confirmed! Check your email for the session code.');
        }

        return view('pages.group-sessions.payment-pending', compact('groupSession', 'reference'));
    }

    /**
     * Student's enrolled group sessions.
     */
    public function mySessions(Request $request)
    {
        $user = $request->user();

        $enrollments = ClinicEnrollment::where('student_id', $user->id)
            ->whereHas('clinic', function ($q) {
                if (\Schema::hasColumn('examiner_clinics', 'is_group_session')) {
                    $q->where('is_group_session', true);
                }
            })
            ->with(['clinic.tutor', 'clinic.subject', 'payment'])
            ->latest()
            ->paginate(10);

        return view('pages.group-sessions.my-sessions', compact('enrollments'));
    }

    /**
     * Show checkout page with manual payment instructions.
     */
    public function checkout(ExaminerClinic $groupSession)
    {
        if (\Schema::hasColumn('examiner_clinics', 'is_group_session') && !$groupSession->is_group_session) {
            abort(404);
        }

        $groupSession->load(['tutor', 'subject', 'enrollments']);

        return view('pages.group-sessions.checkout', compact('groupSession'));
    }

    /**
     * Store a topic suggestion for group sessions.
     */
    public function suggestTopic(Request $request)
    {
        $validated = $request->validate([
            'topic'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            // Build the data to insert
            $data = [
                'topic'       => $validated['topic'],
                'description' => $validated['description'] ?? null,
            ];

            // Only add optional fields if user is authenticated
            if (auth()->check()) {
                $data['user_id'] = auth()->id();
                $data['email'] = auth()->user()->email ?? null;
            } else {
                $data['email'] = $request->ip();
            }

            // Only add ip_address if column exists
            if (\Schema::hasColumn('group_session_suggestions', 'ip_address')) {
                $data['ip_address'] = $request->ip();
            }

            // Create the suggestion
            \App\Models\GroupSessionSuggestion::create($data);

            return back()->with('success', 'Thank you! Your topic suggestion has been recorded. Our team will review it soon.');
        } catch (\Exception $e) {
            \Log::error('Failed to create group session suggestion: ' . $e->getMessage());
            return back()->with('error', 'Failed to submit suggestion. Please try again.');
        }
    }
}
