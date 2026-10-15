<?php

namespace App\Livewire\Booking;

use App\Models\Booking;
use App\Models\TutorProfile;
use Livewire\Component;

class LeaveReview extends Component
{
    public Booking $booking;
    public int    $rating  = 5;
    public string $comment = '';
    public bool   $submitted = false;

    public function mount(Booking $booking) { $this->booking = $booking; }

    public function submit()
    {
        $this->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $this->booking->review()->create([
            'student_id' => auth()->id(),
            'tutor_id'   => $this->booking->tutor_id,
            'rating'     => $this->rating,
            'comment'    => $this->comment,
            'is_visible' => true,
        ]);

        // Update tutor rating avg
        $tutorProfile = TutorProfile::where('user_id', $this->booking->tutor_id)->first();
        if ($tutorProfile) {
            $avg = $tutorProfile->reviews()->visible()->avg('rating');
            $tutorProfile->update([
                'rating_avg'   => round($avg, 2),
                'total_reviews'=> $tutorProfile->reviews()->visible()->count(),
            ]);
        }

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.booking.leave-review');
    }
}
