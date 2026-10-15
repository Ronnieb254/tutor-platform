<?php

namespace App\Livewire\Tutor;

use Livewire\Component;

class TutorDashboard extends Component
{
    public string $tab = 'upcoming';

    public function render()
    {
        $user    = auth()->user();
        $profile = $user->tutorProfile;

        $upcoming = $user->bookingsAsTutor()
            ->with(['student', 'subject'])
            ->upcoming()
            ->orderBy('scheduled_at')
            ->take(5)
            ->get();

        $recent = $user->bookingsAsTutor()
            ->with(['student', 'subject'])
            ->where('status', 'completed')
            ->latest('completed_at')
            ->take(8)
            ->get();

        $wallet          = $user->wallet;
        $earningsThisMonth = $user->wallet?->transactions()
            ->where('type', 'credit')
            ->whereMonth('created_at', now()->month)
            ->sum('amount') ?? 0;

        $pendingBookings = $user->bookingsAsTutor()->where('status', 'pending')->count();

        return view('livewire.tutor.tutor-dashboard', compact(
            'upcoming', 'recent', 'wallet', 'earningsThisMonth', 'pendingBookings', 'profile'
        ));
    }
}
