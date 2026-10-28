<?php

namespace App\Http\Controllers;

use App\Models\{User, Booking, Message, Payment, Refund};
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

        $upcomingBookings = Booking::where('student_id', $user->id)
            ->upcoming()
            ->with(['tutor', 'subject'])
            ->orderBy('scheduled_at')
            ->get();

        $pastBookings = Booking::where('student_id', $user->id)
            ->history()
            ->with(['tutor', 'subject', 'review'])
            ->latest('scheduled_at')
            ->take(50)
            ->get();

        $unreadMessages = Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $unreadNotifs = $user->unreadNotifications()->count();

        $totalSessions = Booking::where('student_id', $user->id)
            ->completed()
            ->count();

        $pendingCount = Booking::where('student_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $totalSpent = Payment::where('user_id', $user->id)
            ->where('status', 'success')
            ->sum('amount');

        $spentThisMonth = Payment::where('user_id', $user->id)
            ->where('status', 'success')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $wishlistCount = $user->wishlist()->count();

        $wishlist = $user->wishlist()->with(['user', 'subjects'])->get();

        $notifications = $user->notifications()->latest()->paginate(20);

        return view('pages.dashboard.student', compact(
            'user',
            'upcomingBookings',
            'pastBookings',
            'unreadMessages',
            'unreadNotifs',
            'totalSessions',
            'pendingCount',
            'totalSpent',
            'spentThisMonth',
            'wishlistCount',
            'wishlist',
            'notifications'
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
        
        // Preload refund service eligibility to avoid N+1 queries in view
        $refundService = app(\App\Services\RefundService::class);
        foreach ($pastBookings as $booking) {
            $booking->canRequestRefund = $refundService->isEligibleForFirst20MinRefund($booking);
            $booking->hasRefund = $booking->relationLoaded('refund') && $booking->refund !== null;
        }

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

        $payments = \App\Models\Payment::where('user_id', $user->id)
            ->with(['booking.tutor'])
            ->where('status', 'success')
            ->latest('paid_at')
            ->paginate(20, ['*'], 'payments_page');

        $refunds = \App\Models\Refund::where('user_id', $user->id)
            ->with(['booking.tutor'])
            ->latest()
            ->paginate(10, ['*'], 'refunds_page');

        $totalRefunded = \App\Models\Refund::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        $spentThisMonth = \App\Models\Payment::where('user_id', $user->id)
            ->where('status', 'success')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $wishlist = $user->wishlist()->with(['user', 'subjects'])->get();

        $notifications = $user->notifications()->latest()->paginate(20);

        return view('pages.dashboard.parent', compact(
            'user',
            'children',
            'upcomingBookings',
            'pastBookings',
            'totalSpent',
            'totalSessions',
            'pendingCount',
            'unreadNotifs',
            'unreadMessages',
            'payments',
            'refunds',
            'totalRefunded',
            'spentThisMonth',
            'wishlist',
            'notifications'
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
