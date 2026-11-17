<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    /**
     * Show the curriculum selection page.
     */
    public function show()
    {
        return view('pages.select-curriculum');
    }

    /**
     * Store the selected curriculum in both session keys and redirect.
     * This is the canonical write path — both the selection page form
     * and the navbar switcher should POST here.
     */
    public function store(Request $request)
    {
        // Accept both 'track' (selection form) and 'variant' (navbar switcher).
        $track = $request->input('track') ?? $request->input('variant');

        if (!in_array($track, ['kenyan', 'international'])) {
            return back()->withErrors(['track' => 'Please choose a valid curriculum.']);
        }

        session([
            'curriculum_track'   => $track,
            'curriculum_variant' => $track,
        ]);

        if (auth()->check()) {
            try {
                auth()->user()->update(['curriculum_preference' => $track]);
            } catch (\Throwable) {
                // Silently skip if column does not exist yet.
            }
        }

        $redirect = $request->input('redirect', route('home'));

        return redirect($redirect)->with('success', 'Curriculum updated.');
    }

    /**
     * Lightweight AJAX endpoint: syncs a variant from the client
     * (localStorage) to the server session. Called on page load by
     * curriculum-switcher.js when localStorage differs from the
     * server-known value. Returns JSON so the JS can confirm.
     *
     * POST /curriculum/sync  { variant: 'kenyan' | 'international' }
     */
    public function sync(Request $request)
    {
        $variant = $request->input('variant');

        if (!in_array($variant, ['kenyan', 'international'])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid variant.'], 422);
        }

        session([
            'curriculum_track'   => $variant,
            'curriculum_variant' => $variant,
        ]);

        if (auth()->check()) {
            try {
                auth()->user()->update(['curriculum_preference' => $variant]);
            } catch (\Throwable) {
                // Silently skip if column does not exist yet.
            }
        }

        return response()->json(['status' => 'ok', 'variant' => $variant]);
    }
}
