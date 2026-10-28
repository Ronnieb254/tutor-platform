<?php

namespace App\Http\Controllers;

use App\Models\{User, Booking, Message};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return match($user->role) {
            'admin' => $this->adminDashboard(),
            'tutor' => $this->tutorDashboard(),
            'parent' => $this->parentDashboard(),
            default => $this->studentDashboard(),
        };
    }

    private function studentDashboard()
    {
        $user = Auth::user();

        $upcomingSessions = Booking::where('student_id', $user->id)
            ->upcoming()
            ->with(['tutor.tutorProfile', 'subject'])
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        $recentSessions = Booking::where('student_id', $user->id)
            ->history()
            ->with(['tutor', 'subject', 'review'])
            ->orderByDesc('scheduled_at')
            ->limit(5)
            ->get();

        $unreadMessages = Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $totalSessions = Booking::where('student_id', $user->id)
            ->completed()
            ->count();

        return view('pages.dashboard.student', compact(
            'upcomingSessions',
            'recentSessions',
            'unreadMessages',
            'totalSessions'
        ));
    }

    private function tutorDashboard()
    {
        $user = Auth::user();

        $upcomingSessions = Booking::where('tutor_id', $user->id)
            ->upcoming()
            ->with(['student', 'subject'])
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        $pendingBookings = Booking::where('tutor_id', $user->id)
            ->where('status', 'pending')
            ->with(['student', 'subject'])
            ->orderBy('scheduled_at')
            ->get();

        $unreadMessages = Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $totalSessions = Booking::where('tutor_id', $user->id)
            ->completed()
            ->count();

        $earningsThisMonth = Booking::where('tutor_id', $user->id)
            ->where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->sum('tutor_earning');

        $avgRating = $user->reviewsReceived()->avg('rating') ?? 0;

        // Payment history
        $paymentHistory = Booking::where('tutor_id', $user->id)
            ->where('status', 'completed')
            ->with(['student', 'subject'])
            ->orderByDesc('completed_at')
            ->limit(10)
            ->get();

        return view('pages.dashboard.tutor', compact(
            'upcomingSessions',
            'pendingBookings',
            'unreadMessages',
            'totalSessions',
            'earningsThisMonth',
            'avgRating',
            'paymentHistory'
        ));
    }

    private function parentDashboard()
    {
        $user = Auth::user();

        // Children linked via parent_id on the users table
        $children = $user->children()->get();
        $childIds = $children->pluck('id');

        // Sessions booked by the parent themselves OR on behalf of their children
        $allStudentIds = $childIds->push($user->id)->unique();

        $upcomingBookings = Booking::whereIn('student_id', $allStudentIds)
            ->upcoming()
            ->with(['tutor', 'subject', 'student'])
            ->orderBy('scheduled_at')
            ->get();

        $pastBookings = Booking::whereIn('student_id', $allStudentIds)
            ->history()
            ->with(['tutor', 'subject', 'student', 'review', 'refund'])
            ->latest('scheduled_at')
            ->take(50)
            ->get();

        $totalSpent = \App\Models\Payment::whereIn('user_id', $allStudentIds)
            ->where('status', 'success')
            ->sum('amount');

        $totalSessions = Booking::whereIn('student_id', $allStudentIds)
            ->where('status', 'completed')
            ->count();

        $pendingCount = Booking::whereIn('student_id', $allStudentIds)
            ->where('status', 'pending')
            ->count();

        $unreadNotifs = $user->unreadNotifications()->count();

        $unreadMessages = Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return view('pages.dashboard.parent', compact(
            'user',
            'children',
            'upcomingBookings',
            'pastBookings',
            'totalSpent',
            'totalSessions',
            'pendingCount',
            'unreadNotifs',
            'unreadMessages'
        ));
    }

    private function adminDashboard()
    {
        $totalUsers = User::count();
        $totalTutors = User::where('role', 'tutor')->count();
        $totalStudents = User::where('role', 'student')->count();

        $bookingsToday = Booking::whereDate('scheduled_at', today())->count();

        $totalBookings = Booking::count();

        $revenueThisMonth = Booking::where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->sum('platform_fee');

        $recentBookings = Booking::with(['student', 'tutor', 'subject'])
            ->latest()
            ->limit(10)
            ->get();

        // Count tutors with documents that are pending or have rejections
        $pendingVerifications = User::where('role', 'tutor')
            ->whereHas('tutorProfile', fn($q) => 
                $q->whereHas('documents', fn($d) => 
                    $d->where('status', 'pending')->orWhere('status', 'rejected')
                )
            )
            ->count();

        return view('pages.dashboard.admin', compact(
            'totalUsers',
            'totalTutors',
            'totalStudents',
            'bookingsToday',
            'totalBookings',
            'revenueThisMonth',
            'recentBookings',
            'pendingVerifications'
        ));
    }
}
