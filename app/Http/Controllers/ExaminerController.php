<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ClinicEnrollment;
use App\Models\ExaminerClinic;
use App\Models\ExaminerRegistration;
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
            'status' => 'approved',
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
        
        // Check if registered examiner
        $registration = ExaminerRegistration::where('tutor_id', $user->id)->first();
        
        if (!$registration || !$registration->isApproved()) {
            return redirect()->route('examiner.apply')
                ->with('error', 'You must be an approved examiner to access this page.');
        }

        // Get examiner's clinics
        $clinics = ExaminerClinic::where('created_by', $user->id)
            ->with('subject')
            ->withCount('enrollments')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate earnings
        $earningsService = new \App\Services\EarningsService();
        $allEarnings = $earningsService->calculateTutorEarnings($user);
        $recentEarnings = $earningsService->getRecentEarnings($user);
        $earnings = array_merge($allEarnings, $recentEarnings);

        return view('pages.examiner-dashboard', compact('clinics', 'earnings'));
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

        $categories = Category::orderBy('name')->get();

        return view('pages.examiner-create-clinic', compact('categories'));
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
            'subject_id' => 'required|exists:categories,id',
            'paper_type' => 'required|in:PP1,PP2,PP3',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'max_students' => 'required|integer|min:1|max:50',
            'scheduled_at' => 'required|date|after:now',
        ]);

        // Generate BBB credentials
        $bbb = new \App\Services\BigBlueButtonService();
        $sessionCode = 'clinic-' . Str::random(12);
        $meetingID = 'clinic-' . time() . '-' . Str::random(8);
        $attendeePW = $bbb->generatePassword(12);
        $moderatorPW = $bbb->generatePassword(12);

        $clinic = ExaminerClinic::create([
            'subject_id' => $validated['subject_id'],
            'paper_type' => $validated['paper_type'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'max_students' => $validated['max_students'],
            'scheduled_at' => $validated['scheduled_at'],
            'created_by' => $user->id,
            'session_code' => $sessionCode,
            'bbb_meeting_id' => $meetingID,
            'bbb_attendee_pw' => $attendeePW,
            'bbb_moderator_pw' => $moderatorPW,
            'batch_number' => 1,
        ]);

        return redirect()->route('examiner.dashboard')
            ->with('success', 'Clinic created successfully!');
    }

    /**
     * Show clinic details with enrollments
     */
    public function showClinic($id)
    {
        $user = Auth::user();
        
        $clinic = ExaminerClinic::where('id', $id)
            ->where('created_by', $user->id)
            ->with(['subject', 'enrollments.user'])
            ->firstOrFail();

        return view('pages.examiner-clinic-details', compact('clinic'));
    }

    /**
     * Join clinic as examiner/moderator
     */
    public function joinAsExaminer($id)
    {
        $user = Auth::user();
        
        $clinic = ExaminerClinic::where('id', $id)
            ->where('created_by', $user->id)
            ->firstOrFail();

        // Initialize BBB service
        $bbb = new \App\Services\BigBlueButtonService();

        // Create/ensure meeting exists
        $meetingOptions = [
            'welcome' => "Welcome to {$clinic->name}! You are the moderator.",
            'record' => 'true',
            'autoStartRecording' => 'true',
            'allowStartStopRecording' => 'true',
            'maxParticipants' => $clinic->max_students + 5, // Students + buffer
            'logoutURL' => route('examiner.dashboard'),
            'lockSettingsDisableCam' => 'false',
            'lockSettingsDisableMic' => 'false',
            'muteOnStart' => 'false',
        ];

        $bbb->createMeeting(
            $clinic->bbb_meeting_id,
            $clinic->name ?? $clinic->title,
            $clinic->bbb_attendee_pw,
            $clinic->bbb_moderator_pw,
            $meetingOptions
        );

        // Generate join URL for moderator
        $joinUrl = $bbb->getJoinUrl(
            $clinic->bbb_meeting_id,
            $user->name . ' (Examiner)',
            $clinic->bbb_moderator_pw,
            true, // isModerator
            (string) $user->id
        );

        return redirect($joinUrl);
    }
}
