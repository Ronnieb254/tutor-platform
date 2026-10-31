<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $status  = $request->input('status', '');
        $search  = $request->input('search', '');

        $bookings = Booking::with(['student', 'tutor', 'subject'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, fn($q) => $q->whereHas('student', fn($u) => $u->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"))
                ->orWhereHas('tutor', fn($u) => $u->where('name', 'like', "%{$search}%"))
            )
            ->latest()
            ->paginate(25);

        return view('pages.admin.bookings', compact('bookings', 'status', 'search'));
    }
}
