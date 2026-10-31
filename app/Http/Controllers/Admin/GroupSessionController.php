<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExaminerClinic;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GroupSessionController extends Controller
{
    public function index(Request $request)
    {
        $status    = $request->input('status', '');
        $subjectId = $request->input('subject_id', '');
        $search    = $request->input('search', '');

        // Stats before filtering
        $statsCounts = ExaminerClinic::groupSessions()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        $stats = [
            'total'       => ExaminerClinic::groupSessions()->count(),
            'scheduled'   => $statsCounts['scheduled']    ?? 0,
            'in_progress' => $statsCounts['in_progress']  ?? 0,
            'completed'   => $statsCounts['completed']    ?? 0,
            'cancelled'   => $statsCounts['cancelled']    ?? 0,
        ];

        $sessions = ExaminerClinic::with(['subject', 'tutor'])
            ->withCount('enrollments')
            ->groupSessions()
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->where('title', 'like', "%{$search}%")
                   ->orWhere('description', 'like', "%{$search}%");
            }))
            ->orderBy('scheduled_at', 'desc')
            ->paginate(20);

        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        return view('pages.admin.group-sessions.index', compact('sessions', 'subjects', 'stats', 'status', 'subjectId', 'search'));
    }

    public function create()
    {
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $tutors   = User::where('role', 'tutor')->orderBy('name')->get();

        return view('pages.admin.group-sessions.create', compact('subjects', 'tutors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id'        => 'required|exists:subjects,id',
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'tutor_id'          => 'nullable|exists:users,id',
            'scheduled_at'      => 'required|date|after:now',
            'duration_minutes'  => 'required|integer|min:15|max:480',
            'max_students'      => 'nullable|integer|min:1|max:200',
            'price'             => 'nullable|numeric|min:0',
        ]);

        $validated['is_group_session'] = true;
        $validated['session_code']     = 'GRP-' . strtoupper(Str::random(8));
        $validated['meeting_room']     = 'Mwalimu-group-' . Str::uuid();
        $validated['created_by']       = auth()->id();
        $validated['status']           = 'scheduled';
        $validated['max_students']     = $validated['max_students'] ?? 30;
        $validated['price']            = $validated['price'] ?? 500;

        $session = ExaminerClinic::create($validated);

        return redirect()
            ->route('admin.group-sessions.show', $session)
            ->with('success', 'Group session "' . $session->title . '" created successfully.');
    }

    public function show(ExaminerClinic $groupSession)
    {
        abort_if(!$groupSession->isGroupSession(), 404);

        $groupSession->load(['subject', 'tutor', 'enrollments.student']);

        $enrolled = $groupSession->enrollments_count ?? $groupSession->enrollments()->count();

        $stats = [
            'enrolled'   => $enrolled,
            'remaining'  => max(0, ($groupSession->max_students ?? 30) - $enrolled),
            'capacity'   => $groupSession->max_students ?? 30,
        ];

        return view('pages.admin.group-sessions.show', compact('groupSession', 'stats'));
    }

    public function edit(ExaminerClinic $groupSession)
    {
        abort_if(!$groupSession->isGroupSession(), 404);

        if (in_array($groupSession->status, ['completed', 'cancelled'])) {
            return redirect()
                ->route('admin.group-sessions.show', $groupSession)
                ->with('error', 'Cannot edit a ' . $groupSession->status . ' session.');
        }

        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $tutors   = User::where('role', 'tutor')->orderBy('name')->get();

        return view('pages.admin.group-sessions.edit', compact('groupSession', 'subjects', 'tutors'));
    }

    public function update(Request $request, ExaminerClinic $groupSession)
    {
        abort_if(!$groupSession->isGroupSession(), 404);

        if (in_array($groupSession->status, ['completed', 'cancelled'])) {
            return redirect()
                ->route('admin.group-sessions.show', $groupSession)
                ->with('error', 'Cannot update a ' . $groupSession->status . ' session.');
        }

        $validated = $request->validate([
            'subject_id'        => 'required|exists:subjects,id',
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'tutor_id'          => 'nullable|exists:users,id',
            'scheduled_at'      => 'required|date',
            'duration_minutes'  => 'required|integer|min:15|max:480',
            'max_students'      => 'nullable|integer|min:1|max:200',
            'price'             => 'nullable|numeric|min:0',
        ]);

        // Prevent reducing capacity below enrolled count
        $enrolled = $groupSession->enrollments()->count();
        if (!empty($validated['max_students']) && $validated['max_students'] < $enrolled) {
            return back()
                ->withInput()
                ->withErrors(['max_students' => "Cannot reduce capacity below current enrollment ({$enrolled})."]);
        }

        $validated['is_group_session'] = true;

        $groupSession->update($validated);

        return redirect()
            ->route('admin.group-sessions.show', $groupSession)
            ->with('success', 'Group session updated successfully.');
    }

    public function destroy(ExaminerClinic $groupSession)
    {
        abort_if(!$groupSession->isGroupSession(), 404);

        $enrolled = $groupSession->enrollments()->count();

        if ($groupSession->status !== 'scheduled' || $enrolled > 0) {
            return redirect()
                ->route('admin.group-sessions.show', $groupSession)
                ->with('error', 'Can only delete a scheduled session with no enrollments.');
        }

        $title = $groupSession->title;
        $groupSession->delete();

        return redirect()
            ->route('admin.group-sessions.index')
            ->with('success', "Group session \"{$title}\" deleted.");
    }

    public function cancel(ExaminerClinic $groupSession)
    {
        abort_if(!$groupSession->isGroupSession(), 404);

        if (in_array($groupSession->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Session is already ' . $groupSession->status . '.');
        }

        $groupSession->update(['status' => 'cancelled']);

        return back()->with('success', 'Group session cancelled.');
    }

    public function start(ExaminerClinic $groupSession)
    {
        abort_if(!$groupSession->isGroupSession(), 404);

        if ($groupSession->status !== 'scheduled') {
            return back()->with('error', 'Only scheduled sessions can be started.');
        }

        $groupSession->update(['status' => 'in_progress']);

        return redirect()
            ->route('admin.group-sessions.show', $groupSession)
            ->with('success', 'Group session started!');
    }

    public function complete(ExaminerClinic $groupSession)
    {
        abort_if(!$groupSession->isGroupSession(), 404);

        if ($groupSession->status !== 'in_progress') {
            return back()->with('error', 'Only in-progress sessions can be marked complete.');
        }

        $groupSession->update(['status' => 'completed']);

        return back()->with('success', 'Group session marked as completed.');
    }
}
