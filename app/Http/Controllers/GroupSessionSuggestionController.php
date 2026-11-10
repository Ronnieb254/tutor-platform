<?php

namespace App\Http\Controllers;

use App\Models\ExaminerClinic;
use App\Models\GroupSessionSuggestion;
use App\Models\GroupSessionSuggestionVote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GroupSessionSuggestionController extends Controller
{
    /**
     * Retrieve all suggestions for a group session, sorted by votes DESC, paginated.
     */
    public function index(ExaminerClinic $groupSession)
    {
        $suggestions = GroupSessionSuggestion::where('group_session_id', $groupSession->id)
            ->with(['subject', 'createdBy'])
            ->orderByDesc('votes_count')
            ->paginate(10);

        return response()->json($suggestions);
    }

    /**
     * Create a new suggestion for a group session.
     * Validates area (3-500 chars), subject_id exists if provided, custom_subject required if subject_id not provided.
     */
    public function store(Request $request, ExaminerClinic $groupSession)
    {
        $validated = $request->validate([
            'area' => 'required|string|min:3|max:500',
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'custom_subject' => 'nullable|string|max:255',
        ]);

        // Require either subject_id or custom_subject
        if (!$validated['subject_id'] && !$validated['custom_subject']) {
            return response()->json(
                ['error' => 'Either subject_id or custom_subject must be provided'],
                422
            );
        }

        // If subject_id is provided, verify it's active
        if ($validated['subject_id']) {
            $subject = \App\Models\Subject::find($validated['subject_id']);
            if (!$subject || !$subject->is_active) {
                return response()->json(
                    ['error' => 'Selected subject is not active or does not exist'],
                    422
                );
            }
        }

        // Create the suggestion
        $suggestion = GroupSessionSuggestion::create([
            'group_session_id' => $groupSession->id,
            'subject_id' => $validated['subject_id'],
            'custom_subject' => $validated['custom_subject'],
            'area' => $validated['area'],
            'votes_count' => 0,
            'created_by' => auth()->id(),
        ]);

        $suggestion->load(['subject', 'createdBy']);

        return response()->json($suggestion, 201);
    }

    /**
     * Vote on a suggestion.
     * Determines voter identity and prevents duplicate votes.
     */
    public function vote(Request $request, ExaminerClinic $groupSession, GroupSessionSuggestion $suggestion)
    {
        $request->validate([
            'browser_id' => 'nullable|string|max:36',
        ]);

        // Ensure suggestion belongs to this group session
        if ($suggestion->group_session_id !== $groupSession->id) {
            return response()->json(['error' => 'Suggestion does not belong to this group session'], 404);
        }

        // Determine voter identity
        $voterId = null;
        $voterIp = null;
        $browserId = null;

        if (auth()->check()) {
            $voterId = auth()->id();
        } elseif ($request->ip()) {
            $voterIp = $request->ip();
        } elseif ($request->input('browser_id')) {
            $browserId = $request->input('browser_id');
        } else {
            return response()->json(['error' => 'Unable to identify voter'], 400);
        }

        // Check for existing vote
        if ($this->hasDuplicate($voterId, $voterIp, $browserId, $suggestion->id)) {
            return response()->json(['error' => 'You have already voted on this suggestion'], 409);
        }

        // Create vote record
        GroupSessionSuggestionVote::create([
            'suggestion_id' => $suggestion->id,
            'voter_id' => $voterId,
            'voter_ip' => $voterIp,
            'browser_id' => $browserId,
        ]);

        // Increment votes count
        $suggestion->increment('votes_count');

        \Log::info('Vote recorded on suggestion', [
            'suggestion_id' => $suggestion->id,
            'voter_id' => $voterId,
            'voter_ip' => $voterIp,
            'browser_id' => $browserId,
        ]);

        return response()->json(['success' => true, 'votes_count' => $suggestion->fresh()->votes_count]);
    }

    /**
     * Remove a vote from a suggestion.
     */
    public function unvote(Request $request, ExaminerClinic $groupSession, GroupSessionSuggestion $suggestion)
    {
        $request->validate([
            'browser_id' => 'nullable|string|max:36',
        ]);

        // Ensure suggestion belongs to this group session
        if ($suggestion->group_session_id !== $groupSession->id) {
            return response()->json(['error' => 'Suggestion does not belong to this group session'], 404);
        }

        // Determine voter identity
        $voterId = null;
        $voterIp = null;
        $browserId = null;

        if (auth()->check()) {
            $voterId = auth()->id();
        } elseif ($request->ip()) {
            $voterIp = $request->ip();
        } elseif ($request->input('browser_id')) {
            $browserId = $request->input('browser_id');
        } else {
            return response()->json(['error' => 'Unable to identify voter'], 400);
        }

        // Find and delete the vote
        $vote = $this->findVote($suggestion->id, $voterId, $voterIp, $browserId);

        if (!$vote) {
            return response()->json(['error' => 'Vote not found'], 404);
        }

        $vote->delete();

        // Decrement votes count
        $suggestion->decrement('votes_count');

        return response()->json(['success' => true, 'votes_count' => $suggestion->fresh()->votes_count]);
    }

    /**
     * Helper: Check if a duplicate vote exists for this suggestion from the given voter.
     */
    private function hasDuplicate(?int $voterId, ?string $voterIp, ?string $browserId, int $suggestionId): bool
    {
        $query = GroupSessionSuggestionVote::where('suggestion_id', $suggestionId);

        if ($voterId) {
            return $query->where('voter_id', $voterId)->exists();
        } elseif ($voterIp) {
            return $query->where('voter_ip', $voterIp)->exists();
        } elseif ($browserId) {
            return $query->where('browser_id', $browserId)->exists();
        }

        return false;
    }

    /**
     * Helper: Find a vote record by suggestion ID and voter identity.
     */
    private function findVote(int $suggestionId, ?int $voterId, ?string $voterIp, ?string $browserId)
    {
        $query = GroupSessionSuggestionVote::where('suggestion_id', $suggestionId);

        if ($voterId) {
            $query->where('voter_id', $voterId);
        } elseif ($voterIp) {
            $query->where('voter_ip', $voterIp);
        } elseif ($browserId) {
            $query->where('browser_id', $browserId);
        }

        return $query->first();
    }
}
