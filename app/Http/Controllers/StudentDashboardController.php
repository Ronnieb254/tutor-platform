<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StudentDashboardController extends Controller
{
    /**
     * Display the student/parent dashboard.
     */
    public function index()
    {
        try {
            $user = auth()->user();
            
            // Verify user has required relationships
            if (!method_exists($user, 'bookingsAsStudent')) {
                throw new \Exception('User model missing bookingsAsStudent relationship');
            }
            
            if (!method_exists($user, 'payments')) {
                throw new \Exception('User model missing payments relationship');
            }
            
            if (!method_exists($user, 'wishlist')) {
                throw new \Exception('User model missing wishlist relationship');
            }
            
            // Pre-load data with error handling
            $unreadNotifs = $user->unreadNotifications()->count();
            
            $upcomingBookings = $user->bookingsAsStudent()
                ->with(['tutor', 'subject'])
                ->upcoming()
                ->orderBy('scheduled_at')
                ->get();
            
            $pastBookings = $user->bookingsAsStudent()
                ->with(['tutor', 'subject', 'review'])
                ->history()
                ->latest('scheduled_at')
                ->take(50)
                ->get();
            
            $totalSpent = $user->payments()
                ->where('status', 'success')
                ->sum('amount');
            
            $totalSessions = $user->bookingsAsStudent()
                ->where('status', 'completed')
                ->count();
            
            $pendingCount = $user->bookingsAsStudent()
                ->where('status', 'pending')
                ->count();
            
            // Pass data explicitly to view
            return view('pages.dashboard.student', compact(
                'user',
                'unreadNotifs',
                'upcomingBookings',
                'pastBookings',
                'totalSpent',
                'totalSessions',
                'pendingCount'
            ));
            
        } catch (\Exception $e) {
            // Log detailed error for debugging
            Log::error('Student Dashboard Error', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // In production, show friendly error
            if (!config('app.debug')) {
                return view('errors.dashboard-error', [
                    'message' => 'Unable to load dashboard. Please try again or contact support.'
                ]);
            }
            
            // In debug mode, rethrow for detailed stack trace
            throw $e;
        }
    }
}
