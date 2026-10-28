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
        $search   = $request->get('search', '');
        $subjectId = $request->get('subject', '');
        $sort     = $request->get('sort', 'date');

        $subjects = Subject::active()->with('category')->orderBy('name')->get();

        $sessions = ExaminerClinic::upcoming()
            ->groupSessions()
            ->with(['tutor', 'subject', 'enrollments'])
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
            'sessions', 'subjects', 'search', 'subjectId', 'sort'
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
     * Enroll a student in a group session (pay-per-session).
     * Reuses the same payment + enrollment pattern as ClinicController::enroll.
     */
    public function enroll(Request $request, ExaminerClinic $groupSession)
    {
        // Defensive: guard against non-group-session records
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

        try {
            return \DB::transaction(function () use ($groupSession, $user) {
                $paymentData = [
                    'user_id'       => $user->id,
                    'clinic_id'     => $groupSession->id,
                    'amount'        => $groupSession->price,
                    'currency'      => 'KES',
                    'payment_type'  => 'group_session',
                    'status'        => 'success',
                    'paid_at'       => now(),
                ];

                if (\Schema::hasColumn('payments', 'commission_rate')) {
                    $paymentData['commission_rate']  = 60.00;
                    $paymentData['tutor_amount']      = round($groupSession->price * 0.40, 2);
                    $paymentData['platform_amount']   = round($groupSession->price * 0.60, 2);
                }

                $payment = Payment::create($paymentData);

                $enrollment = ClinicEnrollment::create([
                    'clinic_id'    => $groupSession->id,
                    'student_id'   => $user->id,
                    'payment_id'   => $payment->id,
                    'session_code' => strtoupper(Str::random(8)),
                ]);

                return redirect()->route('group-sessions.show', $groupSession)
                    ->with('success', "You're enrolled! Your session code is {$enrollment->session_code}. Join 15 minutes before the start time.");
            });
        } catch (\Exception $e) {
            \Log::error('Group session enrollment failed: ' . $e->getMessage(), [
                'session_id' => $groupSession->id,
                'user_id'    => $user->id,
                'trace'      => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Something went wrong while processing your enrollment. Please try again or contact support.');
        }
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
}
