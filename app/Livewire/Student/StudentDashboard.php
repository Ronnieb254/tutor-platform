<?php

namespace App\Livewire\Student;

use Livewire\Component;

class StudentDashboard extends Component
{
    public string $tab = 'upcoming';

    public function render()
    {
        $user = auth()->user();

        $upcoming = $user->bookingsAsStudent()
            ->with(['tutor', 'subject'])
            ->upcoming()
            ->orderBy('scheduled_at')
            ->take(5)
            ->get();

        $past = $user->bookingsAsStudent()
            ->with(['tutor', 'subject', 'review'])
            ->where('status', 'completed')
            ->latest('scheduled_at')
            ->take(10)
            ->get();

        $totalSpent = $user->payments()->where('status', 'success')->sum('amount');
        $totalSessions = $user->bookingsAsStudent()->where('status', 'completed')->count();

        return view('livewire.student.student-dashboard', compact('upcoming', 'past', 'totalSpent', 'totalSessions'));
    }
}
