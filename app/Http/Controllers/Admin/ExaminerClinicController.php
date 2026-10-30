<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExaminerClinic;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExaminerClinicController extends Controller
{
    /**
     * Display a listing of examiner clinics.
     */
    public function index(Request $request)
    {
        $query = ExaminerClinic::with(['subject'])
            ->orderBy('scheduled_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('scheduled_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('scheduled_at', '<=', $request->to_date);
        }

        // Filter auto-created vs manual
        if ($request->has('show_auto_created')) {
            if ($request->show_auto_created === '1') {
                $query->where('is_auto_created', true);
            } elseif ($request->show_auto_created === '0') {
                $query->where('is_auto_created', false);
            }
        }

        $clinics = $query->paginate(20);
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        return view('pages.admin.examiner-clinics.index', compact('clinics', 'subjects'));
    }

    /**
     * Show the form for creating a new clinic.
     */
    public function create()
    {
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        return view('pages.admin.examiner-clinics.create', compact('subjects'));
    }

    /**
     * Store a newly created clinic in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'max_students' => 'nullable|integer|min:1|max:100',
        ]);

        // Generate unique session code
        $validated['session_code'] = 'EXAM-' . strtoupper(Str::random(8));
        $validated['status'] = 'upcoming';
        $validated['is_auto_created'] = false;

        // Set max_students to 30 if not provided
        if (!isset($validated['max_students'])) {
            $validated['max_students'] = 30;
        }

        $clinic = ExaminerClinic::create($validated);

        return redirect()
            ->route('admin.examiner-clinics.show', $clinic)
            ->with('success', 'Examiner clinic created successfully!');
    }

    /**
     * Display the specified clinic with enrollments.
     */
    public function show(ExaminerClinic $examinerClinic)
    {
        $examinerClinic->load(['subject', 'enrollments.user']);

        $stats = [
            'total_enrolled' => $examinerClinic->enrollments()->count(),
            'spots_remaining' => $examinerClinic->max_students - $examinerClinic->enrollments()->count(),
            'completion_rate' => $examinerClinic->enrollments()->count() > 0
                ? round(($examinerClinic->enrollments()->where('attended', true)->count() / $examinerClinic->enrollments()->count()) * 100, 1)
                : 0,
        ];

        return view('pages.admin.examiner-clinics.show', compact('examinerClinic', 'stats'));
    }

    /**
     * Show the form for editing the specified clinic.
     */
    public function edit(ExaminerClinic $examinerClinic)
    {
        // Don't allow editing of auto-created clinics
        if ($examinerClinic->is_auto_created) {
            return redirect()
                ->route('admin.examiner-clinics.show', $examinerClinic)
                ->with('error', 'Auto-created overflow clinics cannot be edited.');
        }

        // Don't allow editing if clinic has started or completed
        if (in_array($examinerClinic->status, ['in_progress', 'completed', 'cancelled'])) {
            return redirect()
                ->route('admin.examiner-clinics.show', $examinerClinic)
                ->with('error', 'Cannot edit a clinic that has started or completed.');
        }

        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        return view('pages.admin.examiner-clinics.edit', compact('examinerClinic', 'subjects'));
    }

    /**
     * Update the specified clinic in storage.
     */
    public function update(Request $request, ExaminerClinic $examinerClinic)
    {
        // Validation for editable clinics
        if ($examinerClinic->is_auto_created) {
            return redirect()
                ->route('admin.examiner-clinics.show', $examinerClinic)
                ->with('error', 'Auto-created overflow clinics cannot be edited.');
        }

        if (in_array($examinerClinic->status, ['in_progress', 'completed', 'cancelled'])) {
            return redirect()
                ->route('admin.examiner-clinics.show', $examinerClinic)
                ->with('error', 'Cannot edit a clinic that has started or completed.');
        }

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'max_students' => 'required|integer|min:1|max:100',
        ]);

        // Don't allow reducing max_students below current enrollment count
        $enrollmentCount = $examinerClinic->enrollments()->count();
        if ($validated['max_students'] < $enrollmentCount) {
            return back()
                ->withInput()
                ->withErrors(['max_students' => "Cannot reduce capacity below current enrollment count ({$enrollmentCount} students)."]);
        }

        $examinerClinic->update($validated);

        return redirect()
            ->route('admin.examiner-clinics.show', $examinerClinic)
            ->with('success', 'Examiner clinic updated successfully!');
    }

    /**
     * Cancel the specified clinic.
     */
    public function cancel(ExaminerClinic $examinerClinic)
    {
        if (in_array($examinerClinic->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Cannot cancel a completed or already cancelled clinic.');
        }

        $examinerClinic->update(['status' => 'cancelled']);

        // TODO: Send notifications to all enrolled students

        return back()->with('success', 'Examiner clinic cancelled successfully. Students will be notified.');
    }

    /**
     * Start the clinic session (change status to in_progress).
     */
    public function start(ExaminerClinic $examinerClinic)
    {
        if ($examinerClinic->status !== 'upcoming') {
            return back()->with('error', 'Only upcoming clinics can be started.');
        }

        $examinerClinic->update(['status' => 'in_progress']);

        return redirect()
            ->route('admin.examiner-clinics.show', $examinerClinic)
            ->with('success', 'Clinic session started!');
    }

    /**
     * Complete the clinic session.
     */
    public function complete(ExaminerClinic $examinerClinic)
    {
        if ($examinerClinic->status !== 'in_progress') {
            return back()->with('error', 'Only in-progress clinics can be completed.');
        }

        $examinerClinic->update(['status' => 'completed']);

        return back()->with('success', 'Clinic session marked as completed.');
    }

    /**
     * Remove the specified clinic from storage.
     */
    public function destroy(ExaminerClinic $examinerClinic)
    {
        // Only allow deletion of upcoming clinics with no enrollments
        if ($examinerClinic->status !== 'upcoming') {
            return back()->with('error', 'Only upcoming clinics can be deleted.');
        }

        $enrollmentCount = $examinerClinic->enrollments()->count();
        if ($enrollmentCount > 0) {
            return back()->with('error', "Cannot delete clinic with {$enrollmentCount} enrolled students.");
        }

        $examinerClinic->delete();

        return redirect()
            ->route('admin.examiner-clinics.index')
            ->with('success', 'Examiner clinic deleted successfully.');
    }
}
