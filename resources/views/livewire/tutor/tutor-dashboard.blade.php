<div class="space-y-6">
    {{-- Verification Status Banner --}}
    @if($profile && !$profile->is_approved)
    @php $status = $profile->verification_status; @endphp
    <div class="rounded-2xl border-2 p-4 flex items-start gap-4
        {{ $status === 'action_required' ? 'border-coral bg-coral/5' : 'border-gold bg-gold/5' }}">
        <div class="text-2xl">{{ $status === 'action_required' ? '⚠️' : '⏳' }}</div>
        <div>
            <p class="font-semibold text-ink">
                {{ $status === 'action_required' ? 'Action Required' : 'Profile Under Review' }}
            </p>
            <p class="text-sm text-muted mt-0.5">
                @if($status === 'action_required')
                    One or more of your documents were rejected. Please re-upload the required documents.
                @else
                    Our team is reviewing your profile and documents. You'll be notified once approved — usually within 24-48 hours.
                @endif
            </p>
        </div>
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['Wallet Balance', 'KSh ' . number_format($wallet?->balance ?? 0, 0), '#6D28D9', 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2'],
            ['This Month', 'KSh ' . number_format($earningsThisMonth, 0), '#10B981', 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
            ['Upcoming', $upcoming->count() . ' sessions', '#E8522A', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['Pending', $pendingBookings . ' new', '#D4A017', 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
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
        <h3 class="font-serif text-xl font-semibold text-ink mb-5">Upcoming Sessions</h3>
        @if($upcoming->isEmpty())
        <p class="text-muted text-sm text-center py-6">No upcoming sessions yet.</p>
        @else
        <div class="space-y-3">
            @foreach($upcoming as $booking)
            <div class="flex items-center gap-4 p-3 rounded-2xl bg-cream/50 hover:bg-cream transition">
                <img src="{{ $booking->student->avatar_url }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm text-ink">{{ $booking->student->name }}</p>
                    <p class="text-xs text-muted">{{ $booking->subject?->name ?? 'General' }} · {{ $booking->duration_minutes }}min</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-sm font-semibold text-ink">{{ $booking->scheduled_at->format('M j') }}</p>
                    <p class="text-xs text-muted">{{ $booking->scheduled_at->format('g:ia') }}</p>
                </div>
                <a href="{{ route('bookings.show', $booking->uuid) }}"
                   class="badge-violet text-xs px-3 py-1.5 rounded-full font-semibold hover:bg-violet hover:text-white transition flex-shrink-0">
                    View
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Recent completed --}}
    @if($recent->isNotEmpty())
    <div class="card p-6">
        <h3 class="font-serif text-xl font-semibold text-ink mb-5">Recent Sessions</h3>
        <div class="space-y-3">
            @foreach($recent as $booking)
            <div class="flex items-center gap-4 p-3 rounded-2xl hover:bg-cream/50 transition">
                <img src="{{ $booking->student->avatar_url }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm text-ink">{{ $booking->student->name }}</p>
                    <p class="text-xs text-muted">{{ $booking->subject?->name ?? 'General' }} · {{ $booking->duration_minutes }}min</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-mint">+KSh {{ number_format($booking->tutor_earning, 0) }}</p>
                    <p class="text-xs text-muted">{{ $booking->completed_at?->format('M j') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
