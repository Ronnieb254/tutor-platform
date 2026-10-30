<?php

namespace App\Http\Controllers;

use App\Models\ExaminerClinic;
use App\Models\ExaminerClinicEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExaminerClinicEnrollmentController extends Controller
{
    /**
     * Display available examiner clinics for students to enroll.
     */
    public function index(Request $request)
    {
        $query = ExaminerClinic::with(['subject'])
            ->where('status', 'upcoming')
            ->where('scheduled_at', '>', now())
            ->withCount('enrollments')
            ->orderBy('scheduled_at', 'asc');

        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $clinics = $query->paginate(12);

        // Get user's enrolled clinic IDs
        $enrolledClinicIds = [];
        if (Auth::check()) {
            $enrolledClinicIds = ExaminerClinicEnrollment::where('user_id', Auth::id())
                ->pluck('examiner_clinic_id')
                ->toArray();
        }

        return view('pages.examiner-clinics.index', compact('clinics', 'enrolledClinicIds'));
    }

    /**
     * Display the specified clinic details.
     */
    public function show(ExaminerClinic $examinerClinic)
    {
        $examinerClinic->load(['subject']);

        $enrollmentCount = $examinerClinic->enrollments()->count();
        $spotsRemaining = $examinerClinic->max_students - $enrollmentCount;

        $isEnrolled = false;
        $userEnrollment = null;

        if (Auth::check()) {
            $userEnrollment = ExaminerClinicEnrollment::where('examiner_clinic_id', $examinerClinic->id)
                ->where('user_id', Auth::id())
                ->first();
            $isEnrolled = $userEnrollment !== null;
        }

        return view('pages.examiner-clinics.show', compact(
            'examinerClinic',
            'enrollmentCount',
            'spotsRemaining',
            'isEnrolled',
            'userEnrollment'
        ));
    }

    /**
     * Enroll the authenticated user in a clinic.
     */
    public function enroll(Request $request, ExaminerClinic $examinerClinic)
    {
        $user = Auth::user();

        // Check if clinic is available for enrollment
        if ($examinerClinic->status !== 'upcoming') {
            return back()->with('error', 'This clinic is not available for enrollment.');
        }

        if ($examinerClinic->scheduled_at <= now()) {
            return back()->with('error', 'Cannot enroll in a clinic that has already started.');
        }

        // Check if user is already enrolled
        $existingEnrollment = ExaminerClinicEnrollment::where('examiner_clinic_id', $examinerClinic->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingEnrollment) {
            return back()->with('error', 'You are already enrolled in this clinic.');
        }

        // Check capacity
        $enrollmentCount = $examinerClinic->enrollments()->count();

        if ($enrollmentCount >= $examinerClinic->max_students) {
            // Clinic is full - check if overflow clinic exists or create one
            $overflowClinic = ExaminerClinic::where('parent_clinic_id', $examinerClinic->id)
                ->where('status', 'upcoming')
                ->whereColumn('enrollments_count', '<', 'max_students')
                ->first();

            if (!$overflowClinic) {
                // Create overflow clinic
                $overflowClinic = $examinerClinic->createOverflowClinic();
            }

            return redirect()
                ->route('examiner-clinics.show', $overflowClinic)
                ->with('info', 'The original clinic was full. You have been redirected to an overflow clinic with the same content.');
        }

        // Create enrollment
        ExaminerClinicEnrollment::create([
            'examiner_clinic_id' => $examinerClinic->id,
            'user_id' => $user->id,
            'enrolled_at' => now(),
        ]);

        return back()->with('success', 'You have been successfully enrolled in this clinic!');
    }

    /**
     * Cancel enrollment (unenroll).
     */
    public function unenroll(ExaminerClinic $examinerClinic)
    {
        $user = Auth::user();

        $enrollment = ExaminerClinicEnrollment::where('examiner_clinic_id', $examinerClinic->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'You are not enrolled in this clinic.');
        }

        // Don't allow unenrollment if clinic has started
        if (in_array($examinerClinic->status, ['in_progress', 'completed'])) {
            return back()->with('error', 'Cannot unenroll from a clinic that has started or completed.');
        }

        // Don't allow unenrollment within 1 hour of start time
        if ($examinerClinic->scheduled_at->subHour() <= now()) {
            return back()->with('error', 'Cannot unenroll less than 1 hour before the clinic starts.');
        }

        $enrollment->delete();

        return back()->with('success', 'You have been unenrolled from this clinic.');
    }

    /**
     * Join the clinic session (redirect to Jitsi).
     */
    public function join(ExaminerClinic $examinerClinic)
    {
        $user = Auth::user();

        // Check if user is enrolled
        $enrollment = ExaminerClinicEnrollment::where('examiner_clinic_id', $examinerClinic->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'You must be enrolled to join this clinic.');
        }

        // Check if user can join (based on canJoin() method in model)
        if (!$examinerClinic->canJoin()) {
            $minutesUntilStart = now()->diffInMinutes($examinerClinic->scheduled_at, false);
            
            if ($minutesUntilStart > 0) {
                return back()->with('error', "Clinic starts in {$minutesUntilStart} minutes. Join button will be available 10 minutes before start time.");
            } else {
                return back()->with('error', 'This clinic session has ended.');
            }
        }

        // Mark attendance
        $enrollment->update(['attended' => true]);

        // Get Jitsi join URL
        $jitsiUrl = $examinerClinic->getJitsiJoinUrl($user->name ?? 'Student');

        return redirect()->away($jitsiUrl);
    }

    /**
     * Show user's enrolled clinics.
     */
    public function myEnrollments()
    {
        $user = Auth::user();

        $enrollments = ExaminerClinicEnrollment::with(['examinerClinic.subject'])
            ->where('user_id', $user->id)
            ->whereHas('examinerClinic', function ($q) {
                $q->whereIn('status', ['upcoming', 'in_progress']);
            })
            ->orderBy('enrolled_at', 'desc')
            ->paginate(15);

        return view('pages.examiner-clinics.my-enrollments', compact('enrollments'));
    }
}
