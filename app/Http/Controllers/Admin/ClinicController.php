<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExaminerClinic;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    /**
     * List all clinics with summary stats.
     */
    public function index(Request $request)
    {
        $status  = $request->get('status', '');
        $search  = $request->get('search', '');
        $dateStr = $request->get('date', '');

        $query = ExaminerClinic::with(['subject', 'tutor', 'creator'])
            ->withCount('enrollments')
            ->orderBy('scheduled_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('subject', fn($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        if ($dateStr) {
            $query->whereDate('scheduled_at', $dateStr);
        }

        $clinics = $query->paginate(20)->withQueryString();

        $stats = [
            'total'     => ExaminerClinic::count(),
            'scheduled' => ExaminerClinic::where('status', 'scheduled')->count(),
            'today'     => ExaminerClinic::whereDate('scheduled_at', today())->count(),
            'full'      => ExaminerClinic::withCount('enrollments')
                              ->get()
                              ->filter(fn($c) => $c->enrollments_count >= $c->max_students)
                              ->count(),
        ];

        return view('pages.admin.clinics', compact('clinics', 'stats', 'status', 'search', 'dateStr'));
    }

    /**
     * Show the create-clinic form (admin version – can assign any tutor).
     */
    public function create()
    {
        $subjects = Subject::active()->orderBy('name')->get();
        $tutors   = User::where('role', 'tutor')
            ->whereHas('tutorProfile', fn($q) => $q->where('is_approved', true))
            ->orderBy('name')
            ->get();

        return view('pages.admin.clinic-create', compact('subjects', 'tutors'));
    }

    /**
     * Store a new clinic (admin-created).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id'       => 'required|exists:subjects,id',
            'paper_type'       => 'required|in:PP1,PP2,PP3',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'tutor_id'         => 'required|exists:users,id',
            'price'            => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:30|max:180',
            'scheduled_at'     => 'required|date|after:now',
        ]);

        ExaminerClinic::create([
            'subject_id'       => $validated['subject_id'],
            'paper_type'       => $validated['paper_type'],
            'title'            => $validated['title'],
            'name'             => $validated['title'],
            'description'      => $validated['description'] ?? null,
            'tutor_id'         => $validated['tutor_id'],
            'price'            => $validated['price'],
            'max_students'     => 30,   // Always 30 slots
            'duration_minutes' => $validated['duration_minutes'],
            'scheduled_at'     => $validated['scheduled_at'],
            'created_by'       => auth()->id(),
            'status'           => 'scheduled',
            'batch_number'     => 1,
        ]);

        return redirect()->route('admin.clinics')
            ->with('success', 'Clinic created successfully. Students can now book slots.');
    }

    /**
     * Show a single clinic's details and enrollment list.
     */
    public function show(ExaminerClinic $clinic)
    {
        $clinic->load(['subject', 'tutor', 'creator', 'enrollments.student', 'childSessions']);

        return view('pages.admin.clinic-show', compact('clinic'));
    }

    /**
     * Cancel a clinic and optionally notify enrolled students.
     */
    public function cancel(ExaminerClinic $clinic)
    {
        $clinic->update(['status' => 'cancelled']);

        return redirect()->route('admin.clinics')
            ->with('success', 'Clinic "' . $clinic->title . '" has been cancelled.');
    }

    /**
     * Manually trigger overflow clinic creation for a full clinic.
     */
    public function createOverflow(ExaminerClinic $clinic)
    {
        if (!$clinic->isFull()) {
            return back()->with('error', 'This clinic is not full yet — overflow is only created when all 30 slots are taken.');
        }

        if ($clinic->hasOverflowClinic()) {
            return back()->with('info', 'An overflow clinic already exists for this session.');
        }

        $overflow = $clinic->createOverflowClinic();

        if ($overflow) {
            return redirect()->route('admin.clinics')
                ->with('success', "Overflow clinic created: {$overflow->title} starting {$overflow->scheduled_at->format('D, M j \\a\\t g:ia')}.");
        }

        return back()->with('error', 'Could not create overflow clinic.');
    }

    /**
     * Delete a clinic (only if no enrollments and still scheduled).
     */
    public function destroy(ExaminerClinic $clinic)
    {
        if ($clinic->enrollments()->count() > 0) {
            return back()->with('error', 'Cannot delete a clinic that has enrolled students. Cancel it instead.');
        }

        $title = $clinic->title;
        $clinic->delete();

        return redirect()->route('admin.clinics')
            ->with('success', 'Clinic "' . $title . '" deleted.');
    }
}
