<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GroupSessionSuggestion;
use Illuminate\Http\Request;

class GroupSessionSuggestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display all group session suggestions with optional filtering.
     */
    public function index(Request $request)
    {
        $query = GroupSessionSuggestion::with(['groupSession', 'subject', 'createdBy']);

        // Filter by group_session_id if provided
        if ($request->has('group_session_id') && $request->input('group_session_id')) {
            $query->where('group_session_id', $request->input('group_session_id'));
        }

        // Search across area, subject name, and created_by name
        if ($request->has('search') && $request->input('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('area', 'like', "%{$search}%")
                  ->orWhereHas('subject', fn($s) => $s->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('createdBy', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        // Order by most recent first
        $suggestions = $query->orderByDesc('created_at')->paginate(50);

        // Get list of group sessions for filter dropdown
        $groupSessions = \App\Models\ExaminerClinic::where('is_group_session', true)
            ->orderByDesc('scheduled_at')
            ->get();

        return view('pages.admin.group-session-suggestions', compact('suggestions', 'groupSessions'));
    }

    /**
     * Delete a suggestion (hard delete).
     */
    public function destroy(GroupSessionSuggestion $suggestion)
    {
        $suggestion->delete();

        return back()->with('success', 'Suggestion deleted successfully.');
    }
}
