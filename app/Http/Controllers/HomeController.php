<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Curriculum;
use App\Models\TutorProfile;
use App\Models\BlogPost;
use App\Models\ExaminerClinic;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Read curriculum from the middleware-set session (unified by CurriculumVariant middleware).
        $variant = session('curriculum_variant', session('curriculum_track', 'kenyan'));
        if (!in_array($variant, ['kenyan', 'international'])) {
            $variant = 'kenyan';
        }

        // Featured tutors: show tutors who teach this curriculum OR have no curriculum set (default = kenyan).
        $featuredTutors = TutorProfile::approved()
            ->with(['user', 'subjects'])
            ->where(fn($q) => $q
                ->whereHas('approvedCurricula', fn($c) => $c->where('track', $variant))
                ->orWhereDoesntHave('approvedCurricula')
            )
            ->orderByDesc('rating_avg')
            ->take(6)
            ->get();

        $recentPosts = BlogPost::published()->with('author')->latest('published_at')->take(3)->get();

        // Upcoming clinics filtered to the active curriculum track.
        $upcomingClinics = ExaminerClinic::upcoming()
            ->with(['tutor', 'subject'])
            ->when(\Schema::hasColumn('examiner_clinics', 'curriculum_id'), fn($q) => $q->where(function ($inner) use ($variant) {
                $inner->whereHas('curriculum', fn($c) => $c->where('track', $variant))
                      ->orWhereNull('curriculum_id');
            }))
            ->orderBy('scheduled_at')
            ->take(4)
            ->get();

        // Categories/subjects for Kenyan variant only (international uses different UX).
        $categories = $variant === 'kenyan'
            ? Category::active()->with([
                'subjects' => fn($q) => $q->whereHas('curriculum', fn($c) => $c->where('track', 'kenyan'))
                                           ->orWhereNull('curriculum_id')
              ])->take(8)->get()
            : [];

        $viewName = $variant === 'international' ? 'pages.home-international' : 'pages.home';

        return view($viewName, compact('categories', 'featuredTutors', 'recentPosts', 'upcomingClinics', 'variant'));
    }

    /**
     * How It Works page — uses a controller method (not a closure)
     * so that route:cache doesn't break.
     */
    public function howItWorks(Request $request)
    {
        $variant = $request->query('variant') ?: (session('curriculum_variant') ?? 'kenyan');
        return view('pages.how-it-works', compact('variant'));
    }

    public function tutors(Request $request)
    {
        // Get curriculum from query or localStorage
        $variant = $request->query('variant') ?: (session('curriculum_variant') ?? 'kenyan');
        $curriculum = $request->query('curriculum', $variant);

        // Store preferences in session
        if ($request->has('curriculum')) {
            session(['curriculum_variant' => $curriculum]);
        }

        return view('pages.tutors.index', compact('variant', 'curriculum'));
    }

    public function tutorProfile(int $id)
    {
        $track = session('curriculum_variant') ?? session('curriculum_track') ?? 'kenyan';
        $tutor = TutorProfile::with([
            'user',
            'subjects' => fn($q) => $q->where('is_active', true)
                ->where(fn($s) => $s->whereHas('curriculum', fn($c) => $c->where('track', $track))
                                   ->orWhereNull('curriculum_id')),
            'subjects.category',
            'reviews.student',
            'availabilities',
        ])->findOrFail($id);
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
