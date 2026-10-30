<?php

namespace App\Http\Controllers;

use App\Models\ClinicEnrollment;
use App\Models\ExaminerClinic;
use App\Models\ExaminerRegistration;
use App\Models\Subject;
use App\Services\JitsiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ExaminerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show examiner application form
     */
    public function showApplyForm()
    {
        $user = Auth::user();

        // Check if already applied
        $registration = ExaminerRegistration::where('tutor_id', $user->id)->first();

        if ($registration) {
            return redirect()->route('examiner.dashboard')
                ->with('info', 'You have already applied as an examiner.');
        }

        // Check if verified tutor
        if (!$user->profile || !$user->profile->tutor_verified) {
            return redirect()->back()
                ->with('error', 'Only verified KNEC Examiners can apply as examiners.');
        }

        return view('pages.examiner-apply');
    }

    /**
     * Process examiner application
     */
    public function apply(Request $request)
    {
        $user = Auth::user();

        // Verify tutor status
        if (!$user->profile || !$user->profile->tutor_verified) {
            return redirect()->back()
                ->with('error', 'Only verified tutors can apply as examiners.');
        }

        // Check if already applied
        if (ExaminerRegistration::where('tutor_id', $user->id)->exists()) {
            return redirect()->route('examiner.dashboard')
                ->with('info', 'You have already applied as an examiner.');
        }

        // Auto-approve verified tutors
        ExaminerRegistration::create([
            'tutor_id' => $user->id,
            'status'   => 'approved',
        ]);

        return redirect()->route('examiner.dashboard')
            ->with('success', 'Welcome! You can now create examiner clinics.');
    }

    /**
     * Show examiner dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Gate: only approved examiners can view the dashboard
        $registration = ExaminerRegistration::where('tutor_id', $user->id)->first();

        if (!$registration || !$registration->isApproved()) {
            return redirect()->route('examiner.apply')
                ->with('error', 'You must be an approved examiner to access this page.');
        }

        // Statistics
        $totalClinics  = ExaminerClinic::where('created_by', $user->id)->count();
        $totalStudents = ClinicEnrollment::whereHas('clinic', function ($q) use ($user) {
            $q->where('created_by', $user->id);
        })->count();
        $upcomingCount = ExaminerClinic::where('created_by', $user->id)->upcoming()->count();

        // Earnings (clinic share = tutor_amount = amount minus the 60% platform fee)
        $earningsService = new \App\Services\EarningsService();
        $allEarnings    = $earningsService->calculateTutorEarnings($user);
        $recentEarnings = $earningsService->getRecentEarnings($user);
        $earnings       = array_merge($allEarnings, $recentEarnings);
        $clinicEarnings = $earnings['clinic_earnings'] ?? 0;

        // Upcoming clinics (scheduled in the future)
        $upcomingClinics = ExaminerClinic::where('created_by', $user->id)
            ->with('subject')
            ->withCount('enrollments')
            ->upcoming()
            ->orderBy('scheduled_at', 'asc')
            ->take(6)
            ->get();

        // Past clinics (already started)
        $pastClinics = ExaminerClinic::where('created_by', $user->id)
            ->with('subject')
            ->withCount('enrollments')
            ->where('scheduled_at', '<', now())
            ->orderBy('scheduled_at', 'desc')
            ->take(10)
            ->get();

        // Recent enrollments across the examiner's clinics
        $recentEnrollments = ClinicEnrollment::whereHas('clinic', function ($q) use ($user) {
            $q->where('created_by', $user->id);
        })
            ->with(['student', 'clinic'])
            ->latest()
            ->take(8)
            ->get();

        return view('pages.examiner-dashboard', compact(
            'registration',
            'totalClinics',
            'totalStudents',
            'upcomingCount',
            'clinicEarnings',
            'upcomingClinics',
            'pastClinics',
            'recentEnrollments',
            'earnings'
        ));
    }

    /**
     * Show create clinic form
     */
    public function showCreateForm()
    {
        $user = Auth::user();

        // Check examiner status
        $registration = ExaminerRegistration::where('tutor_id', $user->id)->first();

        if (!$registration || !$registration->isApproved()) {
            return redirect()->route('examiner.apply')
                ->with('error', 'You must be an approved examiner.');
        }

        $subjects = Subject::active()->orderBy('name')->get();

        return view('pages.examiner-create-clinic', compact('subjects'));
    }

    /**
     * Store new clinic
     */
    public function createClinic(Request $request)
    {
        $user = Auth::user();

        // Verify examiner status
        $registration = ExaminerRegistration::where('tutor_id', $user->id)->first();

        if (!$registration || !$registration->isApproved()) {
            return redirect()->route('examiner.apply')
                ->with('error', 'You must be an approved examiner.');
        }

        $validated = $request->validate([
            'subject_id'    => 'required|exists:subjects,id',
            'paper_type'    => 'required|in:PP1,PP2,PP3',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'topics_covered'=> 'nullable|array',
            'price'         => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:30|max:180',
            'scheduled_at'  => 'required|date|after:now',
        ]);

        $clinic = ExaminerClinic::create([
            'subject_id'       => $validated['subject_id'],
            'paper_type'       => $validated['paper_type'],
            'title'            => $validated['title'],
            'name'             => $validated['title'],
            'description'      => $validated['description'] ?? null,
            'topics_covered'   => $validated['topics_covered'] ?? null,
            'price'            => $validated['price'],
            'max_students'     => 30,  // Always 30 slots
            'duration_minutes' => $validated['duration_minutes'] ?? 60,
            'scheduled_at'     => $validated['scheduled_at'],
            'created_by'       => $user->id,
            'tutor_id'         => $user->id,
            'status'           => 'scheduled',
            'batch_number'     => 1,
        ]);

        return redirect()->route('examiner.dashboard')
            ->with('success', 'Clinic created successfully! Students can now book slots.');
    }

    /**
     * Show clinic details with enrollments
     */
    public function showClinic($id)
    {
        $user = Auth::user();

        $clinic = ExaminerClinic::where('id', $id)
            ->where('created_by', $user->id)
            ->with(['subject', 'enrollments.student'])
            ->firstOrFail();

        return view('pages.examiner-clinic-details', compact('clinic'));
    }

    /**
     * Join clinic as examiner/moderator via Jitsi
     */
    public function joinAsExaminer(ExaminerClinic $clinic)
    {
        $user = Auth::user();

        // Admin can join any clinic; examiner can only join their own
        if (!$user->isAdmin() && $clinic->created_by !== $user->id && $clinic->tutor_id !== $user->id) {
            abort(403, 'You are not the host of this clinic.');
        }

        // Build a secure, time-limited moderator token
        $tokenData = [
            'clinic_id'    => $clinic->id,
            'examiner_id'  => $user->id,
            'is_moderator' => true,
            'issued_at'    => now()->timestamp,
            'expires_at'   => now()->addHours(3)->timestamp,
        ];

        $token = encrypt($tokenData);

        return redirect()->route('clinic.session-room', ['token' => $token]);
    }
}
