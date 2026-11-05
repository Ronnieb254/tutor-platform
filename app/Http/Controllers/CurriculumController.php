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
     * Store the selected curriculum track in session and redirect.
     */
    public function store(Request $request)
    {
        $request->validate([
            'track' => 'required|in:kenyan,international',
        ]);

        session(['curriculum_track' => $request->track]);

        return redirect()->route('tutors.index', ['track' => $request->track]);
    }
}
