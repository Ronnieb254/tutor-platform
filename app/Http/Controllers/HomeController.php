<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\TutorProfile;
use App\Models\BlogPost;
use App\Models\ExaminerClinic;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories     = Category::active()->with('subjects')->take(8)->get();
        $featuredTutors = TutorProfile::approved()
            ->with(['user', 'subjects'])
            ->orderByDesc('rating_avg')
            ->take(6)
            ->get();
        $recentPosts       = BlogPost::published()->with('author')->latest('published_at')->take(3)->get();
        $upcomingClinics   = ExaminerClinic::upcoming()->with(['tutor','subject'])->orderBy('scheduled_at')->take(4)->get();

        return view('pages.home', compact('categories', 'featuredTutors', 'recentPosts', 'upcomingClinics'));
    }

    /**
     * How It Works page — uses a controller method (not a closure)
     * so that route:cache doesn't break.
     */
    public function howItWorks()
    {
        return view('pages.how-it-works');
    }

    public function tutors(Request $request)
    {
        return view('pages.tutors.index');
    }

    public function tutorProfile(int $id)
    {
        $tutor = TutorProfile::with(['user', 'subjects.category', 'reviews.student', 'availabilities'])
            ->findOrFail($id);
        return view('pages.tutors.show', compact('tutor'));
    }

    public function tutorProfileBySlug(string $slug)
    {
        // Convert slug back: "ms-fatuma-abdi" → search by words
        $words = array_filter(explode('-', $slug), fn($w) => strlen($w) > 2);
        $query = \App\Models\User::where('role', 'tutor');
        foreach ($words as $word) {
            $query->where('name', 'like', "%{$word}%");
        }
        $user = $query->first();

        if (!$user) {
            abort(404, 'Tutor not found');
        }

        $tutor = TutorProfile::where('user_id', $user->id)
            ->with(['user', 'subjects.category', 'reviews.student', 'availabilities'])
            ->firstOrFail();

        // Redirect to canonical ID-based URL
        return redirect()->route('tutors.show', $tutor->id);
    }

    public function dashboard()
    {
        $user = auth()->user();
        if ($user->isAdmin())  return redirect()->route('admin.dashboard');
        if ($user->isTutor())  return redirect()->route('tutor.dashboard');
        return redirect()->route('student.dashboard');
    }
}
