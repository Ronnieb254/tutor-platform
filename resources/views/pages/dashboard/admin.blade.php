@extends('layouts.admin')
@section('title', 'Admin Dashboard')

@section('content')
@php
$stats = [
    ['Total Users',    \App\Models\User::count(),                        '#6D28D9'],
    ['Active Tutors',  \App\Models\TutorProfile::approved()->count(),    '#10B981'],
    ['Bookings Today', \App\Models\Booking::whereDate('created_at', today())->count(), '#E8522A'],
    ['Revenue Today',  'KSh ' . number_format(\App\Models\Payment::where('status','success')->whereDate('paid_at', today())->sum('amount'), 0), '#D4A017'],
    ['Pending Tutors', \App\Models\TutorProfile::where('is_approved', false)->count(), '#F59E0B'],
    ['Open Payouts',   \App\Models\PayoutRequest::where('status','pending')->count(),  '#6366F1'],
];
@endphp
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    @foreach($stats as [$label, $value, $color])
    <div class="bg-white rounded-3xl border border-[rgba(21,40,31,0.08)] p-5">
        <p class="text-xs text-muted uppercase tracking-wide mb-2">{{ $label }}</p>
        <p class="font-serif text-2xl font-bold text-ink">{{ $value }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Bookings --}}
    <div class="bg-white rounded-3xl border border-[rgba(21,40,31,0.08)] p-6">
        <h3 class="font-serif text-lg font-semibold text-ink mb-4">Recent Bookings</h3>
        <div class="space-y-3">
            @foreach(\App\Models\Booking::with(['student','tutor'])->latest()->take(6)->get() as $booking)
            <div class="flex items-center gap-3">
                <img src="{{ $booking->student->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-ink truncate">{{ $booking->student->name }} → {{ $booking->tutor->name }}</p>
                    <p class="text-xs text-muted">{{ $booking->scheduled_at->format('M j, g:ia') }} · KSh {{ number_format($booking->total_amount, 0) }}</p>
                </div>
                <span class="text-xs font-semibold capitalize px-2 py-1 rounded-full
                    {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-700' : ($booking->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                    {{ $booking->status }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Tutors Pending Approval --}}
    <div class="bg-white rounded-3xl border border-[rgba(21,40,31,0.08)] p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-serif text-lg font-semibold text-ink">Pending Approvals</h3>
            <a href="{{ route('admin.tutors') }}" class="text-xs text-violet font-semibold hover:underline">View all</a>
        </div>
        @php $pending = \App\Models\TutorProfile::where('is_approved', false)->with('user')->latest()->take(5)->get(); @endphp
        @forelse($pending as $profile)
        <div class="flex items-center gap-3 mb-3">
            <img src="{{ $profile->user->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-ink">{{ $profile->user->name }}</p>
                <p class="text-xs text-muted">{{ $profile->city }}</p>
            </div>
            <a href="{{ route('admin.tutors') }}" class="text-xs text-violet hover:underline">Review</a>
        </div>
        @empty
        <p class="text-sm text-muted text-center py-4">No pending approvals 🎉</p>
        @endforelse
    </div>
</div>
@endsection
