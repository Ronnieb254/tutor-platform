@extends('layouts.app')
@section('title', 'Booking Details')

@section('content')
<div class="section-container py-10">
    <div class="max-w-3xl mx-auto">

        {{-- Back --}}
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-muted hover:text-ink transition mb-6">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to Dashboard
        </a>

        {{-- Header --}}
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <h1 class="font-serif text-3xl font-bold text-ink">Session Details</h1>
                <p class="text-muted text-sm mt-1">Booking #{{ substr($booking->uuid, 0, 8) }}</p>
            </div>
            @php
            $statusColors = [
                'pending'   => 'bg-amber-100 text-amber-800 border-amber-300',
                'confirmed' => 'bg-green-100 text-green-800 border-green-300',
                'completed' => 'bg-blue-100 text-blue-800 border-blue-300',
                'cancelled' => 'bg-red-100 text-red-800 border-red-300',
            ];
            @endphp
            <span class="badge border {{ $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-700' }} capitalize text-sm px-4 py-2">
                {{ $booking->status }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Left: Details --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Tutor / Student info --}}
                <div class="card p-6">
                    @if(auth()->id() === $booking->student_id)
                    <p class="text-xs font-bold uppercase tracking-widest text-muted mb-3">Your Tutor</p>
                    <div class="flex items-center gap-4">
                        <img src="{{ $booking->tutor->avatar_url }}" class="w-14 h-14 rounded-2xl object-cover">
                        <div>
                            <p class="font-serif text-lg font-semibold text-ink">{{ $booking->tutor->name }}</p>
                            <p class="text-sm text-muted">{{ $booking->subject?->name ?? 'General Session' }}</p>
                            <span class="text-mint text-xs font-semibold">✓ Verified</span>
                        </div>
                    </div>
                    @else
                    <p class="text-xs font-bold uppercase tracking-widest text-muted mb-3">Student</p>
                    <div class="flex items-center gap-4">
                        <img src="{{ $booking->student->avatar_url }}" class="w-14 h-14 rounded-2xl object-cover">
                        <div>
                            <p class="font-serif text-lg font-semibold text-ink">{{ $booking->student->name }}</p>
                            <p class="text-sm text-muted">{{ $booking->subject?->name ?? 'General Session' }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Session Info --}}
                <div class="card p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-muted mb-4">Session Information</p>
                    <dl class="grid grid-cols-2 gap-4">
                        @foreach([
                            ['Date',     $booking->scheduled_at->format('l, F j, Y')],
                            ['Time',     $booking->scheduled_at->format('g:i a')],
                            ['Duration', $booking->duration_minutes . ' minutes'],
                            ['Subject',  $booking->subject?->name ?? 'General'],
                        ] as [$label, $value])
                        <div>
                            <dt class="text-xs text-muted mb-0.5">{{ $label }}</dt>
                            <dd class="font-semibold text-ink">{{ $value }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>

                {{-- Session Access Code --}}
                @if($booking->status === 'confirmed')
                @php
                $code = auth()->id() === $booking->tutor_id
                    ? $booking->session_code_tutor
                    : $booking->session_code_student;
                @endphp
                <div class="session-code-box">
                    <p class="text-sm font-semibold text-muted uppercase tracking-widest mb-2">Your Session Access Code</p>
                    <p class="session-code-text">{{ $code }}</p>
                    <p class="text-sm text-muted mt-2">Use this code below to enter the live session</p>
                </div>

                {{-- Join Session --}}
                <div class="card p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-muted mb-4">Live Session</p>
                    @livewire('booking.join-session', ['booking' => $booking])
                </div>
                @endif

                {{-- Chat --}}
                @if(in_array($booking->status, ['confirmed','completed']))
                <div class="card p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-muted mb-4">Messages</p>
                    @livewire('booking.chat-box', ['booking' => $booking])
                </div>
                @endif

                {{-- Review --}}
                @if($booking->status === 'completed' && auth()->id() === $booking->student_id && !$booking->review)
                <div class="card p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-muted mb-4">Leave a Review</p>
                    @livewire('booking.leave-review', ['booking' => $booking])
                </div>
                @elseif($booking->review)
                <div class="card p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-muted mb-3">Your Review</p>
                    <div class="flex gap-0.5 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                        <span class="text-lg {{ $i <= $booking->review->rating ? 'text-gold' : 'text-hairline' }}">★</span>
                        @endfor
                    </div>
                    <p class="text-sm text-ink">{{ $booking->review->comment }}</p>
                </div>
                @endif
            </div>

            {{-- Right: Payment --}}
            <div class="space-y-4">
                <div class="card p-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-muted mb-4">Payment</p>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-sm text-muted">Session fee</dt>
                            <dd class="text-sm font-semibold text-ink">KSh {{ number_format($booking->total_amount, 0) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-muted">Platform fee</dt>
                            <dd class="text-sm text-muted">KSh {{ number_format($booking->platform_fee, 0) }}</dd>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-hairline">
                            <dt class="font-semibold text-ink">
                                {{ auth()->id() === $booking->tutor_id ? 'Your earnings' : 'Total paid' }}
                            </dt>
                            <dd class="font-bold text-ink">
                                KSh {{ number_format(auth()->id() === $booking->tutor_id ? $booking->tutor_earning : $booking->total_amount, 0) }}
                            </dd>
                        </div>
                    </dl>
                    @if($booking->payment)
                    <div class="mt-3 pt-3 border-t border-hairline">
                        <p class="text-xs text-muted">Ref: {{ $booking->payment->paystack_reference }}</p>
                        @if($booking->payment->paid_at)
                        <p class="text-xs text-mint font-semibold mt-0.5">✓ Paid {{ $booking->payment->paid_at->format('M j, Y') }}</p>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Cancel --}}
                @if(in_array($booking->status, ['pending','confirmed']) && $booking->scheduled_at->isFuture())
                <form method="POST" action="{{ route('bookings.cancel', $booking->uuid) }}"
                      onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                    @csrf
                    <input type="hidden" name="reason" value="Cancelled by user">
                    <button type="submit" class="w-full pill-btn border-2 border-coral text-coral hover:bg-coral hover:text-white py-2.5 text-sm">
                        Cancel Booking
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
