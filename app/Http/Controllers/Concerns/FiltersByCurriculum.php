<?php

namespace App\Http\Controllers\Concerns;

/**
 * Shared helper for controllers that need to filter DB queries
 * by the user's currently-selected curriculum track.
 *
 * Usage:
 *   use FiltersByCurriculum;
 *   ...
 *   $track = $this->curriculumTrack();         // 'kenyan' | 'international' | ''
 *   $query->when(..., fn($q) => $this->applyCurriculumFilter($q, $track));
 */
trait FiltersByCurriculum
{
    /**
     * Return the active curriculum track from the session.
     * Returns '' (empty string) when none is set, so callers
     * can use it in ->when($track, ...) and skip filtering gracefully.
     */
    protected function curriculumTrack(): string
    {
        $valid = ['kenyan', 'international'];

        // Check session (both keys for backward compat).
        $track = session('curriculum_variant') ?? session('curriculum_track') ?? '';

        // Fall back to the authenticated user's saved preference.
        if (!$track && auth()->check()) {
            try {
                $pref = auth()->user()->getAttribute('curriculum_preference');
                if ($pref && in_array($pref, $valid)) {
                    $track = $pref;
                }
            } catch (\Throwable) {
                // Column may not exist yet — ignore.
            }
        }

        return in_array($track, $valid) ? $track : 'kenyan';
    }

    /**
     * Apply a curriculum track filter to a query that has a
     * curriculum_id column (via a belongs-to Curriculum with a track column).
     * Rows with curriculum_id = NULL are always included (legacy / untagged).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $track   e.g. 'kenyan' or 'international'
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyCurriculumFilter($query, string $track)
    {
        return $query->where(function ($q) use ($track) {
            $q->whereHas('curriculum', fn($c) => $c->where('track', $track))
              ->orWhereNull('curriculum_id');
        });
    }

    /**
     * Filter a Subject query by track.
     * Returns all subjects when $track is empty (graceful fallback).
     */
    protected function subjectsForTrack(string $track = '')
    {
        $q = \App\Models\Subject::active()->with('category')->orderBy('name');

        if ($track) {
            $q->whereHas('curriculum', fn($c) => $c->where('track', $track));
        }

        return $q->get();
    }
}
