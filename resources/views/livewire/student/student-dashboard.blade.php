<div class="space-y-6">
    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['Upcoming', $upcoming->count(), '#6D28D9', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['Completed', $totalSessions, '#10B981', 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
            ['Total Spent', 'KSh ' . number_format($totalSpent, 0), '#E8522A', 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
            ['Find Tutors', '80+ subjects', '#D4A017', 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
        ] as [$label, $value, $color, $icon])
        <div class="card p-5">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center mb-3" style="background:{{ $color }}20">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="{{ $color }}" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                </svg>
            </div>
            <p class="text-xs text-muted uppercase tracking-wide">{{ $label }}</p>
            <p class="font-serif text-2xl font-bold text-ink mt-1">{{ $value }}</p>
        </div>
        @endforeach
    </div>

    {{-- Upcoming Sessions --}}
    <div class="card p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-serif text-xl font-semibold text-ink">Upcoming Sessions</h3>
            <a href="{{ route('tutors.index') }}" class="text-sm text-violet font-semibold hover:underline">+ Book a session</a>
        </div>
        @if($upcoming->isEmpty())
        <div class="text-center py-8">
            <div class="text-4xl mb-3">📅</div>
            <p class="font-serif text-lg font-semibold text-ink mb-2">No upcoming sessions</p>
            <p class="text-muted text-sm mb-5">Find a tutor and book your first session.</p>
            <a href="{{ route('tutors.index') }}" class="pill-btn-primary">Find a Tutor</a>
        </div>
        @else
        <div class="space-y-3">
            @foreach($upcoming as $booking)
            <div class="flex items-center gap-4 p-4 rounded-2xl bg-violet/5 border border-violet/15 hover:bg-violet/10 transition">
                <img src="{{ $booking->tutor->avatar_url }}" class="w-12 h-12 rounded-2xl object-cover flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-ink">{{ $booking->tutor->name }}</p>
                    <p class="text-xs text-muted">{{ $booking->subject?->name ?? 'General' }} · {{ $booking->duration_minutes }} min</p>
                    <p class="text-xs text-violet font-semibold mt-1">{{ $booking->scheduled_at->format('D, M j \a\t g:ia') }}</p>
                </div>
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    @if($booking->canJoin())
                    <a href="{{ route('bookings.show', $booking->uuid) }}" class="pill-btn bg-mint text-white text-xs px-3 py-1.5 rounded-full font-semibold">
                        Join Now
                    </a>
                    @else
                    <a href="{{ route('bookings.show', $booking->uuid) }}" class="badge-violet text-xs px-3 py-1.5 rounded-full font-semibold">
                        View
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Past Sessions --}}
    @if($past->isNotEmpty())
    <div class="card p-6">
        <h3 class="font-serif text-xl font-semibold text-ink mb-5">Past Sessions</h3>
        <div class="space-y-3">
            @foreach($past as $booking)
            <div class="flex items-center gap-4 p-3 rounded-2xl hover:bg-cream/50 transition">
                <img src="{{ $booking->tutor->avatar_url }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm text-ink">{{ $booking->tutor->name }}</p>
                    <p class="text-xs text-muted">{{ $booking->subject?->name ?? 'General' }} · {{ $booking->scheduled_at->format('M j, Y') }}</p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    @if(!$booking->review)
                    <a href="{{ route('bookings.show', $booking->uuid) }}" class="text-xs text-gold font-semibold hover:underline">Leave review</a>
                    @else
                    <div class="text-gold text-xs">★ {{ $booking->review->rating }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
