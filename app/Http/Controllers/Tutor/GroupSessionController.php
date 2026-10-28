<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\ExaminerClinic;
use App\Models\Subject;
use App\Models\ClinicEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Tutor-facing Group Session controller.
 *
 * Tutors create and manage their group sessions (multi-student classes) from
 * the tutor dashboard. Group sessions are stored in `examiner_clinics` with
 * `is_group_session = true`.
 */
class GroupSessionController extends Controller
{
    /**
     * List the authenticated tutor's group sessions.
     */
    public function index(Request $request)
    {
        $tutor = $request->user();

        $sessions = ExaminerClinic::where('tutor_id', $tutor->id)
            ->groupSessions()
            ->with(['subject', 'enrollments'])
            ->orderByDesc('scheduled_at')
            ->paginate(10);

        $upcomingCount = ExaminerClinic::where('tutor_id', $tutor->id)
            ->groupSessions()
            ->upcoming()
            ->count();

        $totalEnrollments = ClinicEnrollment::whereHas('clinic', function ($q) use ($tutor) {
            $q->where('tutor_id', $tutor->id);
            if (\Schema::hasColumn('examiner_clinics', 'is_group_session')) {
                $q->where('is_group_session', true);
            }
        })->count();

        $totalEarnings = ClinicEnrollment::whereHas('clinic', function ($q) use ($tutor) {
            $q->where('tutor_id', $tutor->id);
            if (\Schema::hasColumn('examiner_clinics', 'is_group_session')) {
                $q->where('is_group_session', true);
            }
        })->sum('amount');

        return view('pages.tutor.group-sessions.index', compact(
            'sessions', 'upcomingCount', 'totalEnrollments', 'totalEarnings'
        ));
    }

    /**
     * Show the create form.
     */
    public function create(Request $request)
    {
        $tutor = $request->user();

        // Get subjects this tutor teaches
        $tutorProfile = $tutor->tutorProfile;
        $subjects = $tutorProfile
            ? $tutorProfile->subjects()->active()->orderBy('name')->get()
            : Subject::active()->orderBy('name')->get();

        return view('pages.tutor.group-sessions.create', compact('subjects'));
    }

    /**
     * Store a newly created group session (or a multi-session series).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id'       => 'required|exists:subjects,id',
            'title'            => 'required|string|max:200',
            'description'       => 'required|string|max:2000',
            'topics_covered'   => 'nullable|string|max:1000',
            'scheduled_at'     => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:30|max:240',
            'price'            => 'required|numeric|min:0',
            'max_students'     => 'required|integer|min:2|max:100',
            'is_series'        => 'nullable|in:0,1',
            'series_count'     => 'nullable|integer|min:2|max:12',
            'series_interval'  => 'nullable|in:daily,weekly',
        ]);

        $tutor = $request->user();

        // Parse topics into array if provided
        $topics = [];
        if (!empty($validated['topics_covered'])) {
            $topics = array_map('trim', explode(',', $validated['topics_covered']));
            $topics = array_filter($topics);
        }

        $isSeries = !empty($validated['is_series']) && \Schema::hasColumn('examiner_clinics', 'series_id');
        $seriesCount = $isSeries ? (int) ($validated['series_count'] ?? 2) : 1;
        $interval = $validated['series_interval'] ?? 'weekly';

        // Generate a shared series id for multi-session series
        $seriesId = $isSeries ? Str::uuid() : null;

        try {
            return \DB::transaction(function () use ($validated, $topics, $tutor, $isSeries, $seriesCount, $interval, $seriesId) {
                $created = [];

                for ($i = 1; $i <= $seriesCount; $i++) {
                    $scheduledAt = \Carbon\Carbon::parse($validated['scheduled_at']);
                    if ($i > 1) {
                        if ($interval === 'daily') {
                            $scheduledAt = $scheduledAt->addDays($i - 1);
                        } else {
                            $scheduledAt = $scheduledAt->addWeeks($i - 1);
                        }
                    }

                    $title = $seriesCount > 1
                        ? $validated['title'] . " (Session {$i} of {$seriesCount})"
                        : $validated['title'];

                    $data = [
                        'tutor_id'         => $tutor->id,
                        'subject_id'       => $validated['subject_id'],
                        'title'            => $title,
                        'name'             => $title,
                        'description'      => $validated['description'],
                        'topics_covered'   => $topics,
                        'scheduled_at'     => $scheduledAt,
                        'duration_minutes' => $validated['duration_minutes'],
                        'price'            => $validated['price'],
                        'max_students'     => $validated['max_students'],
                        'status'           => 'scheduled',
                        'is_group_session' => true,
                        'created_by'       => $tutor->id,
                    ];

                    if (\Schema::hasColumn('examiner_clinics', 'series_id')) {
                        $data['series_id'] = $seriesId;
                        $data['session_number'] = $i;
                        $data['total_sessions'] = $seriesCount;
                    }

                    $created[] = ExaminerClinic::create($data);
                }

                $msg = $isSeries
                    ? "Created a {$seriesCount}-session series. Students can enroll per session."
                    : "Group session created successfully.";

                return redirect()->route('tutor.group-sessions.index')
                    ->with('success', $msg);
            });
        } catch (\Exception $e) {
            \Log::error('Failed to create group session: ' . $e->getMessage(), [
                'tutor_id' => $tutor->id,
                'trace'    => $e->getTraceAsString(),
            ]);

            return back()->withInput()
                ->with('error', 'Could not create the group session. Please try again.');
        }
    }

    /**
     * Show a single group session (tutor view) with enrollment list.
     */
    public function show(Request $request, ExaminerClinic $groupSession)
    {
        $tutor = $request->user();

        if ($groupSession->tutor_id !== $tutor->id && !$tutor->isAdmin()) {
            abort(403);
        }

        $groupSession->load(['subject', 'enrollments.student', 'enrollments.payment']);
        $seriesSessions = $groupSession->seriesSessions();

        return view('pages.tutor.group-sessions.show', compact('groupSession', 'seriesSessions'));
    }

    /**
     * Cancel a group session.
     */
    public function cancel(Request $request, ExaminerClinic $groupSession)
    {
        $tutor = $request->user();

        if ($groupSession->tutor_id !== $tutor->id && !$tutor->isAdmin()) {
            abort(403);
        }

        $groupSession->update(['status' => 'cancelled']);

        return back()->with('success', 'Group session cancelled. Enrolled students will be notified.');
    }
}
