@extends('layouts.app')
@section('title', $tutor->user->name . ' – Tutor Profile')

@section('content')
<div class="section-container py-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left: Profile Info --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Header --}}
            <div class="card p-6">
                <div class="flex items-start gap-5">
                    <img src="{{ $tutor->user->avatar_url }}" alt="{{ $tutor->user->name }}"
                         class="w-24 h-24 rounded-3xl object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="font-serif text-2xl font-bold text-ink">{{ $tutor->user->name }}</h1>
                            <span class="badge-mint">✓ Verified</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @foreach($tutor->subjects as $subject)
                            <span class="badge-violet">{{ $subject->name }}</span>
                            @endforeach
                        </div>
                        <div class="flex items-center flex-wrap gap-4 mt-3 text-sm">
                            <span class="flex items-center gap-1"><span class="text-gold">★</span> <strong>{{ number_format($tutor->rating_avg, 1) }}</strong> <span class="text-muted">({{ $tutor->total_reviews }} reviews)</span></span>
                            <span class="text-muted">·</span>
                            <span class="text-muted">{{ $tutor->total_sessions }} sessions</span>
                            <span class="text-muted">·</span>
                            <span class="text-muted">{{ $tutor->experience_years }}y experience</span>
                            @if($tutor->city)
                            <span class="text-muted">·</span>
                            <span class="text-muted">📍 {{ $tutor->city }}, {{ $tutor->country }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs --}}
            <div x-data="{ tab: 'about' }">
                <div class="flex gap-1 border-b border-hairline mb-5">
                    @foreach(['about' => 'About', 'reviews' => 'Reviews (' . $tutor->total_reviews . ')'] as $key => $label)
                    <button @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? 'border-b-2 border-violet text-violet font-semibold' : 'text-muted hover:text-ink'"
                            class="px-4 py-2.5 text-sm transition -mb-px">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>

                {{-- About --}}
                <div x-show="tab === 'about'">
                    <div class="card p-6">
                        <h3 class="font-serif text-lg font-semibold text-ink mb-3">About {{ $tutor->user->name }}</h3>
                        <p class="text-ink leading-relaxed">{{ $tutor->bio ?? 'No bio provided yet.' }}</p>

                        @if($tutor->languages)
                        <div class="mt-4">
                            <p class="text-xs font-bold uppercase tracking-widest text-muted mb-2">Languages</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($tutor->languages as $lang)
                                <span class="badge bg-ink/5 text-ink">{{ $lang }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Reviews --}}
                <div x-show="tab === 'reviews'" x-cloak>
                    @if($tutor->reviews->isEmpty())
                    <div class="card p-8 text-center">
                        <p class="text-muted">No reviews yet. Be the first!</p>
                    </div>
                    @else
                    <div class="space-y-4">
                        @foreach($tutor->reviews->where('is_visible', true)->take(10) as $review)
                        <div class="card p-5">
                            <div class="flex items-start gap-3">
                                <img src="{{ $review->student->avatar_url }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-semibold text-sm text-ink">{{ $review->student->name }}</p>
                                        <p class="text-xs text-muted">{{ $review->created_at->format('M Y') }}</p>
                                    </div>
                                    <div class="flex gap-0.5 my-1">
                                        @for($i = 1; $i <= 5; $i++)
                                        <span class="text-sm {{ $i <= $review->rating ? 'text-gold' : 'text-[rgba(21,40,31,0.1)]' }}">★</span>
                                        @endfor
                                    </div>
                                    @if($review->comment)
                                    <p class="text-sm text-ink leading-relaxed">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Booking Widget --}}
        <div class="lg:sticky lg:top-24 self-start">
            <div class="card p-6">
                <p class="text-xs font-bold uppercase tracking-widest text-muted mb-4">Book a Session</p>
                <div class="flex items-baseline gap-1 mb-5">
                    <span class="font-serif text-3xl font-bold text-ink">KSh {{ number_format($tutor->hourly_rate, 0) }}</span>
                    <span class="text-muted text-sm">/hour</span>
                </div>

                @auth
                @livewire('booking.booking-wizard', ['tutorProfile' => $tutor])
                @else
                <a href="{{ route('login') }}" class="w-full pill-btn-primary block text-center py-3 mb-3">
                    Sign in to Book
                </a>
                <a href="{{ route('register') }}" class="w-full pill-btn-outline block text-center py-3 text-sm">
                    Create Free Account
                </a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
