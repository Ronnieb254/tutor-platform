<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('category')->orderBy('name')->paginate(50);
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        
        return view('pages.admin.subjects', compact('subjects', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:10',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        Subject::create($validated);

        return back()->with('success', 'Subject created successfully.');
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:10',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $subject->update($validated);

        return back()->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        // Check if subject has bookings
        $bookingsCount = \App\Models\Booking::where('subject_id', $subject->id)->count();
        
        if ($bookingsCount > 0) {
            return back()->with('error', 'Cannot delete subject with existing bookings.');
        }

        $subject->delete();

        return back()->with('success', 'Subject deleted successfully.');
    }

    public function toggle(Subject $subject)
    {
        $subject->update(['is_active' => !$subject->is_active]);

        return response()->json([
            'status' => 'ok',
            'is_active' => $subject->is_active,
        ]);
    }
}
