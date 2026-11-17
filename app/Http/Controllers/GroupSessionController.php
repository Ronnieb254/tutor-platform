<?php

namespace App\Http\Controllers;

use App\Models\ExaminerClinic;
use App\Models\ClinicEnrollment;
use App\Models\Payment;
use App\Models\Subject;
use App\Services\PaystackService;
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
     * Enroll a student in a group session via Paystack.
     *
     * Creates a pending Payment and redirects to Paystack's hosted checkout
     * page. The actual ClinicEnrollment (with session code) is created by
     * PaystackCallbackHelper once the payment is verified on the callback.
     */
    public function enroll(Request $request, ExaminerClinic $groupSession)
    {
        if (\Schema::hasColumn('examiner_clinics', 'is_group_session') && !$groupSession->is_group_session) {
            return back()->with('error', 'This is not a group session.');
        }

        $user = $request->user();

        if ($groupSession->isEnrolledBy($user)) {
            return back()->with('error', 'You are already enrolled in this group session.');
        }

        if ($groupSession->isFull()) {
            return back()->with('error', 'This session is full. Please choose another time slot.');
        }

        $paystack  = app(PaystackService::class);
        $reference = $paystack->generateReference('GRP');

        $payment = Payment::create([
            'user_id'         => $user->id,
            'clinic_id'        => $groupSession->id,
            'amount'          => $groupSession->price,
            'currency'         => 'KES',
            'payment_type'     => 'group_session',
            'mpesa_reference'  => $reference,
            'status'           => 'pending',
            'metadata'         => [
                'flow'         => 'paystack',
                'session_id'   => $groupSession->id,
                'session_title' => $groupSession->title,
            ],
        ]);

        $result = $paystack->initializeTransaction(
            $user->email,
            (int) round((float) $groupSession->price * 100),
            $reference,
            [
                'flow'          => 'paystack',
                'session_id'    => $groupSession->id,
                'session_title' => $groupSession->title,
            ],
            route('paystack.callback')
        );

        if (!($result['status'] ?? false) || empty($result['data']['authorization_url'])) {
            // Roll back the pending payment so the student can retry cleanly.
            $payment->delete();

            return back()->with('error', $result['message'] ?? 'Could not start payment. Please try again or contact support.');
        }

        $data = $result['data'];

        $metadata = $payment->metadata ?? [];
        $metadata['access_code']       = $data['access_code'] ?? null;
        $metadata['authorization_url'] = $data['authorization_url'] ?? null;
        $payment->metadata = $metadata;
        $payment->save();

        // Send the student to Paystack's hosted checkout page.
        return redirect($data['authorization_url']);
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
     * Dedicated group session meeting room.
     * Verifies the user is the tutor/moderator or has an active enrollment.
     */
    public function room(Request $request, ExaminerClinic $groupSession)
    {
        $user = $request->user();

        if (\Schema::hasColumn('examiner_clinics', 'is_group_session') && !$groupSession->is_group_session) {
            abort(404);
        }

        $isModerator = $user->isAdmin()
            || $user->id === $groupSession->tutor_id
            || $user->id === $groupSession->created_by;

        if (!$isModerator) {
            $enrollment = ClinicEnrollment::where('clinic_id', $groupSession->id)
                ->where('student_id', $user->id)
                ->first();

            if (!$enrollment) {
                return redirect()->route('group-sessions.my-sessions')
                    ->with('error', 'You are not enrolled in this session.');
            }

            // Allow joining 15 min before start
            $canJoinFrom = $groupSession->scheduled_at->copy()->subMinutes(15);
            if (now()->lt($canJoinFrom)) {
                return redirect()->route('group-sessions.my-sessions')
                    ->with('error', 'This session opens at ' . $canJoinFrom->setTimezone('Africa/Nairobi')->format('g:ia') . ' EAT.');
            }

            if (!$enrollment->attended) {
                $enrollment->update(['attended' => true]);
            }

            $sessionCode = $enrollment->session_code;
        } else {
            $sessionCode = $groupSession->tutor_code ?? $groupSession->session_code;
        }

        $groupSession->load(['tutor', 'subject', 'enrollments']);

        return view('pages.group-sessions.room', [
            'groupSession' => $groupSession,
            'isModerator'  => $isModerator,
            'sessionCode'  => $sessionCode,
        ]);
    }

    /**
     * Mark that the current user has joined the group session room.
     * Returns attendance counts so the client can start the timer.
     */
    public function joined(Request $request, ExaminerClinic $groupSession)
    {
        $user = $request->user();

        // For the tutor we track via a lightweight cache flag; for students via enrollment.
        $isModerator = $user->id === $groupSession->tutor_id || $user->id === $groupSession->created_by || $user->isAdmin();

        if ($isModerator) {
            cache()->put("gs_{$groupSession->id}_tutor_joined", true, now()->addHours(4));
        } else {
            ClinicEnrollment::where('clinic_id', $groupSession->id)
                ->where('student_id', $user->id)
                ->update(['attended' => true]);
            cache()->put("gs_{$groupSession->id}_student_{$user->id}_joined", true, now()->addHours(4));
        }

        $tutorJoined   = (bool) cache("gs_{$groupSession->id}_tutor_joined");
        $studentsJoined = ClinicEnrollment::where('clinic_id', $groupSession->id)
            ->where('attended', true)
            ->count();

        return response()->json([
            'tutor_joined'    => $tutorJoined,
            'students_joined' => $studentsJoined,
            'both_joined'     => $tutorJoined && $studentsJoined > 0,
        ]);
    }

    /**
     * Poll attendance for the group session room.
     */
    public function attendance(Request $request, ExaminerClinic $groupSession)
    {
        $tutorJoined = (bool) cache("gs_{$groupSession->id}_tutor_joined");
        $studentsJoined = ClinicEnrollment::where('clinic_id', $groupSession->id)
            ->where('attended', true)
            ->count();
        $totalEnrolled = ClinicEnrollment::where('clinic_id', $groupSession->id)->count();

        // Elapsed seconds since session was scheduled to start
        $startedAt = $groupSession->scheduled_at;
        $elapsed   = max(0, now()->diffInSeconds($startedAt, false) * -1);

        return response()->json([
            'tutor_joined'    => $tutorJoined,
            'students_joined' => $studentsJoined,
            'total_enrolled'  => $totalEnrolled,
            'both_joined'     => $tutorJoined && $studentsJoined > 0,
            'elapsed_seconds' => (int) $elapsed,
        ]);
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
