<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\TutorProfile;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories    = Category::active()->with('subjects')->take(8)->get();
        $featuredTutors = TutorProfile::approved()
            ->with(['user', 'subjects'])
            ->orderByDesc('rating_avg')
            ->take(6)
            ->get();
        $recentPosts = BlogPost::published()->with('author')->latest('published_at')->take(3)->get();

        return view('pages.home', compact('categories', 'featuredTutors', 'recentPosts'));
    }

    public function tutors(Request $request)
    {
        return view('pages.tutors.index');
    }

    public function tutorProfile(string $slug)
    {
        $user = \App\Models\User::where('name', 'like', "%{$slug}%")->firstOrFail();
        $tutor = TutorProfile::where('user_id', $user->id)->with(['user', 'subjects.category', 'reviews.student'])->firstOrFail();

        return view('pages.tutors.show', compact('tutor'));
    }

    public function dashboard()
    {
        $user = auth()->user();
        if ($user->isAdmin())  return redirect()->route('admin.dashboard');
        if ($user->isTutor())  return redirect()->route('tutor.dashboard');
        return redirect()->route('student.dashboard');
    }
}
