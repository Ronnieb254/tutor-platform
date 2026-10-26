<?php

namespace App\Http\Controllers;

use App\Models\TutorProfile;
use App\Models\Subject;
use App\Models\Category;
use Illuminate\Http\Request;

class TutorSubjectController extends Controller
{
    /**
     * Show the subject management page for a tutor
     */
    public function index(TutorProfile $tutorProfile)
    {
        // Authorization: admin or the tutor themselves
        if (!auth()->user()->isAdmin() && auth()->id() !== $tutorProfile->user_id) {
            abort(403);
        }

        $tutorProfile->load('subjects', 'user');
        $categories = Category::with('subjects')->orderBy('name')->get();

        return view('pages.tutor-subjects.index', compact('tutorProfile', 'categories'));
    }

    /**
     * Attach subjects to a tutor profile
     */
    public function store(Request $request, TutorProfile $tutorProfile)
    {
        // Authorization: admin or the tutor themselves
        if (!auth()->user()->isAdmin() && auth()->id() !== $tutorProfile->user_id) {
            abort(403);
        }

        $request->validate([
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        // Sync subjects (replaces existing with new selection)
        $tutorProfile->subjects()->sync($request->subject_ids);

        return back()->with('success', 'Subjects updated successfully!');
    }

    /**
     * Add a single subject (AJAX)
     */
    public function attach(Request $request, TutorProfile $tutorProfile)
    {
        if (!auth()->user()->isAdmin() && auth()->id() !== $tutorProfile->user_id) {
            abort(403);
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
        ]);

        // Attach without detaching existing
        $tutorProfile->subjects()->syncWithoutDetaching([$request->subject_id]);

        return response()->json([
            'status' => 'ok',
            'message' => 'Subject added successfully',
            'count' => $tutorProfile->subjects()->count(),
        ]);
    }

    /**
     * Remove a single subject (AJAX)
     */
    public function detach(Request $request, TutorProfile $tutorProfile)
    {
        if (!auth()->user()->isAdmin() && auth()->id() !== $tutorProfile->user_id) {
            abort(403);
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $tutorProfile->subjects()->detach($request->subject_id);

        return response()->json([
            'status' => 'ok',
            'message' => 'Subject removed successfully',
            'count' => $tutorProfile->subjects()->count(),
        ]);
    }
}
