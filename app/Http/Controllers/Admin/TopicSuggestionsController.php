<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GroupSessionSuggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopicSuggestionsController extends Controller
{
    /**
     * Show topic suggestions dashboard with analytics
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $sortBy = $request->get('sort', 'recent');
        $timeframe = $request->get('timeframe', '30'); // days

        // Base query
        $query = GroupSessionSuggestion::query();

        // Filter by timeframe
        if ($timeframe) {
            $query->where('created_at', '>=', now()->subDays((int)$timeframe));
        }

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('topic', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        if ($sortBy === 'popular') {
            $query->withCount('votes')
                  ->orderBy('votes_count', 'desc')
                  ->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $suggestions = $query->paginate(15);

        // Analytics - Top topics
        $topTopics = GroupSessionSuggestion::query()
            ->where('created_at', '>=', now()->subDays((int)$timeframe))
            ->select('topic', DB::raw('COUNT(*) as count'))
            ->groupBy('topic')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Analytics - Total stats
        $totalSuggestions = GroupSessionSuggestion::where('created_at', '>=', now()->subDays((int)$timeframe))->count();
        $uniqueTopics = GroupSessionSuggestion::where('created_at', '>=', now()->subDays((int)$timeframe))
            ->distinct('topic')
            ->count('topic');
        $suggestionsWithDescription = GroupSessionSuggestion::where('created_at', '>=', now()->subDays((int)$timeframe))
            ->whereNotNull('description')
            ->where('description', '!=', '')
            ->count();
        $totalUsers = GroupSessionSuggestion::where('created_at', '>=', now()->subDays((int)$timeframe))
            ->distinct('user_id')
            ->count('user_id');

        // Recent activity
        $recentActivity = GroupSessionSuggestion::where('created_at', '>=', now()->subDays((int)$timeframe))
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.group-sessions.topic-suggestions', compact(
            'suggestions',
            'topTopics',
            'totalSuggestions',
            'uniqueTopics',
            'suggestionsWithDescription',
            'totalUsers',
            'recentActivity',
            'search',
            'sortBy',
            'timeframe'
        ));
    }

    /**
     * View suggestion details
     */
    public function show(GroupSessionSuggestion $suggestion)
    {
        $suggestion->load('user');
        return view('admin.group-sessions.topic-suggestion-detail', compact('suggestion'));
    }

    /**
     * Delete a suggestion
     */
    public function destroy(GroupSessionSuggestion $suggestion)
    {
        $suggestion->delete();

        return back()->with('success', 'Suggestion deleted successfully.');
    }

    /**
     * Export suggestions as CSV
     */
    public function export(Request $request)
    {
        $timeframe = $request->get('timeframe', '30');

        $suggestions = GroupSessionSuggestion::where('created_at', '>=', now()->subDays((int)$timeframe))
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'topic-suggestions-' . now()->format('Y-m-d-His') . '.csv';
        $handle = fopen('php://memory', 'w+');

        // Header
        fputcsv($handle, [
            'Date',
            'Topic',
            'Description',
            'User',
            'Email',
            'IP Address',
            'User ID'
        ]);

        // Data
        foreach ($suggestions as $suggestion) {
            fputcsv($handle, [
                $suggestion->created_at->format('Y-m-d H:i:s'),
                $suggestion->topic,
                $suggestion->description,
                $suggestion->user?->name ?? 'Guest',
                $suggestion->email,
                $suggestion->ip_address,
                $suggestion->user_id ?? 'N/A',
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response()->streamDownload(
            function () use ($csv) {
                echo $csv;
            },
            $filename,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
}
