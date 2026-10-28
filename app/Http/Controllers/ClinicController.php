<?php

namespace App\Http\Controllers;

use App\Models\ExaminerClinic;
use App\Models\ClinicEnrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClinicController extends Controller
{
    /**
     * Display the Examiner's Clinic page with filtering by subject and paper type.
     */
    public function index(Request $request)
    {
        $search   = $request->get('search', '');
        $subjectId = $request->get('subject', '');
        $paperType = $request->get('paper', '');
        $sort     = $request->get('sort', 'date');

        $subjects = \App\Models\Subject::active()->with('category')->orderBy('name')->get();

        // Common paper types offered per subject
        $paperTypes = ['PP1' => 'Paper 1', 'PP2' => 'Paper 2', 'PP3' => 'Paper 3'];

        $clinics = ExaminerClinic::upcoming()
            ->with(['tutor', 'subject', 'enrollments'])
            ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
            ->when($paperType, fn($q) => $q->where('paper_type', $paperType))
            ->when($search, function ($q) use ($search) {
                $q->whereHas('subject', fn($s) => $s->where('name', 'like', "%{$search}%"))
                  ->orWhere('title', 'like', "%{$search}%");
            });

        // Sort
        if ($sort === 'subject') {
            $clinics->orderByRaw('(SELECT name FROM subjects WHERE subjects.id = examiner_clinics.subject_id)');
        } else {
            $clinics->orderBy('scheduled_at');
        }

        $clinics = $clinics->paginate(12)->withQueryString();

        return view('pages.examiner-clinic', compact('clinics', 'subjects', 'paperTypes', 'search', 'subjectId', 'paperType', 'sort'));
    }

    /**
     * Enroll a student in a clinic session.
     * Checks capacity (30 max), processes payment, and triggers auto-creation of next session if full.
     */
    public function enroll(Request $request, ExaminerClinic $clinic)
    {
        $user = $request->user();

        // Check if already enrolled
        if ($clinic->isEnrolledBy($user)) {
            return back()->with('error', 'You are already enrolled in this clinic session.');
        }

        // Check capacity — 30 students max
        if ($clinic->isFull()) {
            // Find or auto-create the next available session
            try {
                $next = ExaminerClinic::findNextAvailable($clinic->subject_id, $clinic->paper_type);
                if (!$next) {
                    $next = $clinic->autoCreateNextSession();
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to find/create overflow clinic: ' . $e->getMessage());
                $next = null;
            }

            if ($next) {
                return redirect()->route('examiner-clinic')
                    ->with('info', "This session is full. A new session has been opened: {$next->paperLabel()} on {$next->scheduled_at->format('D, M j \a\t g:ia')}.")
                    ->with('redirect_clinic_id', $next->id);
            }

            return back()->with('error', 'This session is full and no new session could be created. Please try again later.');
        }

        // Use a transaction so partial failures don't leave orphan records
        try {
            return \DB::transaction(function () use ($clinic, $user) {
                // Create payment record — explicitly set payment_type so commission is correct
                $paymentData = [
                    'user_id'       => $user->id,
                    'clinic_id'     => $clinic->id,
                    'amount'        => $clinic->price,
                    'currency'      => 'KES',
                    'payment_type'  => 'clinic',
                    'status'        => 'success',
                    'paid_at'       => now(),
                ];

                // Only set commission fields if the columns exist (guards against missing migration)
                if (\Schema::hasColumn('payments', 'commission_rate')) {
                    $paymentData['commission_rate']  = 60.00;
                    $paymentData['tutor_amount']      = round($clinic->price * 0.40, 2);
                    $paymentData['platform_amount']   = round($clinic->price * 0.60, 2);
                }

                $payment = Payment::create($paymentData);

                // Create enrollment
                $enrollment = ClinicEnrollment::create([
                    'clinic_id'    => $clinic->id,
                    'student_id'   => $user->id,
                    'payment_id'   => $payment->id,
                    'session_code' => strtoupper(Str::random(8)),
                ]);

                // If this enrollment fills the session, auto-create the next one
                if ($clinic->fresh()->isFull()) {
                    try {
                        // Only auto-create if this is the original (not an overflow batch)
                        $isOriginal = true;
                        if (\Schema::hasColumn('examiner_clinics', 'parent_clinic_id')) {
                            $isOriginal = $clinic->parent_clinic_id === null;
                        }
                        if ($isOriginal) {
                            $clinic->autoCreateNextSession();
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Failed to auto-create overflow clinic: ' . $e->getMessage());
                    }
                }

                return back()->with('success', "You're enrolled! Your session code is {$enrollment->session_code}. The clinic is on {$clinic->scheduled_at->format('D, M j \a\t g:ia')}.");
            });
        } catch (\Exception $e) {
            \Log::error('Clinic enrollment failed: ' . $e->getMessage(), [
                'clinic_id' => $clinic->id,
                'user_id'   => $user->id,
                'trace'     => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Something went wrong while processing your enrollment. Please try again or contact support.');
        }
    }
}
