@extends('layouts.app')
@section('title', 'TutorLink – Find Verified Tutors in Kenya')

@section('content')

{{-- ── HERO ─────────────────────────────────────────────────────────────────── --}}
<section class="relative overflow-hidden bg-cream py-20 sm:py-32">
    <div class="section-container relative z-10">
        <div class="max-w-3xl">
            <span class="badge-violet mb-5 inline-flex">🇰🇪 Kenya's #1 Tutor Marketplace</span>
            <h1 class="font-serif text-5xl sm:text-6xl lg:text-7xl font-bold text-ink leading-[1.05] mb-6">
                Find the perfect tutor for<br>
                <span class="text-violet">every subject.</span>
            </h1>
            <p class="text-lg sm:text-xl text-muted leading-relaxed mb-8 max-w-xl">
                Book verified 1:1 sessions in flexible blocks — 15, 30, 45 or 60 minutes.
                Pay securely with M-Pesa or card. Start learning today.
            </p>
            <div class="flex flex-wrap items-center gap-3 mb-10">
                <a href="{{ route('tutors.index') }}" class="pill-btn-primary text-base px-8 py-4">
                    Find a Tutor
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('register') }}?role=tutor" class="pill-btn-outline text-base px-8 py-4">
                    Become a Tutor
                </a>
            </div>

            {{-- Search Bar --}}
            <form action="{{ route('tutors.index') }}" method="GET" class="flex gap-2 max-w-xl">
                <div class="flex-1 relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-muted w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="text" name="search" placeholder="Search by subject, tutor name…"
                           class="form-input pl-10" value="{{ request('search') }}">
                </div>
                <button type="submit" class="pill-btn-primary px-6">Search</button>
            </form>
        </div>
    </div>

    {{-- Decorative floating cards --}}
    <div class="absolute right-0 top-0 bottom-0 w-[42%] hidden lg:block pointer-events-none overflow-hidden">
        <div class="relative h-full">
            @php
            $demoTutors = [
                ['name' => 'Dr. Amara K.', 'subject' => 'Mathematics', 'rating' => '4.9', 'color' => '#10B981', 'style' => 'top:8%; left:15%; --r:-3deg'],
                ['name' => 'Prof. James M.', 'subject' => 'Physics', 'rating' => '4.8', 'color' => '#6D28D9', 'style' => 'top:28%; left:38%; --r:2deg'],
                ['name' => 'Ms. Fatuma A.', 'subject' => 'English', 'rating' => '5.0', 'color' => '#E8522A', 'style' => 'top:52%; left:8%; --r:-2deg'],
                ['name' => 'Mr. David O.', 'subject' => 'Chemistry', 'rating' => '4.7', 'color' => '#D4A017', 'style' => 'top:70%; left:35%; --r:3deg'],
            ];
            @endphp
            @foreach($demoTutors as $t)
            <div class="absolute w-44 bg-white rounded-3xl border border-hairline p-3.5 shadow-card"
                 style="position:absolute; {{ $t['style'] }}; transform: rotate(var(--r,0deg));">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-white text-lg mb-2"
                     style="background:{{ $t['color'] }}">
                    {{ substr($t['name'], 0, 1) }}
                </div>
                <p class="font-serif font-semibold text-ink text-sm leading-tight">{{ $t['name'] }}</p>
                <p class="text-xs text-muted mt-0.5">{{ $t['subject'] }}</p>
                <div class="flex items-center gap-1 mt-1.5">
                    <span class="text-gold text-xs">★</span>
                    <span class="text-xs font-semibold">{{ $t['rating'] }}</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-mint ml-1"></span>
                    <span class="text-xs text-mint font-medium">Verified</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Trust badges --}}
    <div class="section-container mt-14 relative z-10">
        <div class="flex flex-wrap gap-6">
            @foreach([['500+', 'Verified Tutors'], ['4.9★', 'Average Rating'], ['10,000+', 'Sessions Completed'], ['80+', 'Subjects Covered']] as [$num, $label])
            <div class="flex items-center gap-2.5">
                <span class="font-serif text-2xl font-bold text-ink">{{ $num }}</span>
                <span class="text-sm text-muted">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── SESSION DURATION BLOCKS ─────────────────────────────────────────────── --}}
<section class="py-16 bg-ink text-cream">
    <div class="section-container">
        <div class="text-center mb-10">
            <h2 class="font-serif text-3xl sm:text-4xl font-bold mb-3">Choose your session length</h2>
            <p class="text-cream/60 text-lg">Flexible blocks to fit any schedule or budget</p>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['15', 'Quick Focus', 'Perfect for revision, a specific topic, or a quick Q&A.', 'From KSh 375'],
                ['30', 'Half Hour', 'Ideal for targeted practice or concept clarification.', 'From KSh 750'],
                ['45', 'Extended', 'Great for deeper dives and exam preparation.', 'From KSh 1,125'],
                ['60', 'Full Hour', 'Maximum learning time. Best value per minute.', 'From KSh 1,500'],
            ] as [$mins, $title, $desc, $price])
            <div class="bg-cream/10 border border-cream/20 rounded-3xl p-6 hover:bg-cream/15 transition cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-violet/20 flex items-center justify-center mb-4">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#6D28D9" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <p class="font-serif text-3xl font-bold text-cream mb-0.5">{{ $mins }}<span class="text-lg font-normal text-cream/60"> min</span></p>
                <p class="font-semibold text-cream text-sm mb-2">{{ $title }}</p>
                <p class="text-cream/50 text-xs leading-relaxed mb-4">{{ $desc }}</p>
                <span class="text-sm font-bold text-gold">{{ $price }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── SUBJECT CATEGORIES ───────────────────────────────────────────────────── --}}
<section class="py-20 sm:py-28">
    <div class="section-container">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-ink mb-2">Browse by subject</h2>
                <p class="text-muted">Find a tutor who specialises in exactly what you need</p>
            </div>
            <a href="{{ route('tutors.index') }}" class="hidden sm:flex items-center gap-1 text-sm font-semibold text-violet hover:underline">
                View all <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($categories as $category)
            <a href="{{ route('tutors.index') }}?category={{ $category->id }}"
               class="card-hover p-5 group block">
                <div class="text-3xl mb-3">{{ $category->icon ?? '📚' }}</div>
                <p class="font-serif font-semibold text-ink group-hover:text-violet transition">{{ $category->name }}</p>
                <p class="text-xs text-muted mt-1">{{ $category->subjects->count() }} subjects</p>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ── HOW IT WORKS ─────────────────────────────────────────────────────────── --}}
<section id="how-it-works" class="py-20 bg-ink/5">
    <div class="section-container">
        <div class="text-center mb-14">
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-ink mb-3">How TutorLink works</h2>
            <p class="text-muted text-lg max-w-xl mx-auto">Three simple steps to your first lesson</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach([
                ['1', '🔍', 'Find your tutor', 'Browse verified tutors by subject, price, and availability. Read reviews from real students.'],
                ['2', '📅', 'Book a session', 'Pick your duration block (15–60 min) and a time that works for you. Pay securely with Paystack.'],
                ['3', '🎓', 'Start learning', 'Join your live session on Jitsi. Receive your access code by email. Learn and grow!'],
            ] as [$num, $icon, $title, $desc])
            <div class="relative">
                <div class="card p-8 text-center h-full">
                    <div class="w-14 h-14 rounded-2xl bg-violet/10 flex items-center justify-center text-3xl mx-auto mb-5">{{ $icon }}</div>
                    <div class="w-7 h-7 rounded-full bg-violet text-white text-xs font-bold flex items-center justify-center absolute -top-3 -left-3">{{ $num }}</div>
                    <h3 class="font-serif text-xl font-semibold text-ink mb-3">{{ $title }}</h3>
                    <p class="text-muted text-sm leading-relaxed">{{ $desc }}</p>
                </div>
                @if(!$loop->last)
                <div class="hidden md:block absolute top-1/2 -right-4 w-8 text-center text-muted z-10">→</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── FEATURED TUTORS ──────────────────────────────────────────────────────── --}}
@if($featuredTutors->isNotEmpty())
<section class="py-20 sm:py-28">
    <div class="section-container">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-ink mb-2">Meet our top tutors</h2>
                <p class="text-muted">Handpicked, verified experts ready to teach</p>
            </div>
            <a href="{{ route('tutors.index') }}" class="hidden sm:flex items-center gap-1 text-sm font-semibold text-violet hover:underline">
                See all tutors <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($featuredTutors as $tutor)
            <div class="card-hover p-5">
                <div class="flex items-start gap-4">
                    <img src="{{ $tutor->user->avatar_url }}" alt="{{ $tutor->user->name }}"
                         class="w-16 h-16 rounded-2xl object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="font-serif text-lg font-semibold text-ink">{{ $tutor->user->name }}</span>
                            <span class="text-mint text-xs font-semibold">✓ Verified</span>
                        </div>
                        <div class="flex flex-wrap gap-1 mt-1.5">
                            @foreach($tutor->subjects->take(2) as $subject)
                            <span class="badge-violet text-[11px]">{{ $subject->name }}</span>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-2 mt-2 text-sm">
                            <span class="text-gold font-semibold">★ {{ number_format($tutor->rating_avg, 1) }}</span>
                            <span class="text-muted">({{ $tutor->total_reviews }})</span>
                            <span class="text-muted">·</span>
                            <span class="font-semibold text-ink">KSh {{ number_format($tutor->hourly_rate, 0) }}/hr</span>
                        </div>
                    </div>
                </div>

                {{-- Duration blocks --}}
                <div class="grid grid-cols-4 gap-1.5 mt-4">
                    @foreach([15, 30, 45, 60] as $mins)
                    <div class="text-center rounded-xl border border-hairline py-1.5 hover:border-violet hover:bg-violet/5 cursor-pointer transition">
                        <p class="text-[11px] font-bold text-ink">{{ $mins }}m</p>
                        <p class="text-[10px] text-muted">KSh {{ number_format($tutor->getPriceForDuration($mins), 0) }}</p>
                    </div>
                    @endforeach
                </div>

                <a href="{{ route('tutors.show', Str::slug($tutor->user->name)) }}"
                   class="mt-3 w-full pill-btn-primary text-sm py-2.5 block text-center">
                    Book Session
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── TUTOR CTA ────────────────────────────────────────────────────────────── --}}
<section class="py-20 bg-violet">
    <div class="section-container">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="badge bg-white/20 text-white mb-4">For Tutors</span>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-white mb-4">Earn doing what you love</h2>
                <p class="text-white/75 text-lg leading-relaxed mb-6">
                    Sign up, upload your documents, get verified and start accepting bookings.
                    Set your own rates, choose your availability, keep up to 85% of your earnings.
                </p>
                <div class="flex flex-col gap-2.5 mb-8">
                    @foreach(['Upload documents once — verified for life', 'Set your own rates per subject', 'Get paid directly to M-Pesa or bank', 'Access code system for secure sessions']) as $point)
                    <div class="flex items-center gap-2.5 text-white/90 text-sm">
                        <div class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        {{ $point }}
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('register') }}?role=tutor" class="pill-btn bg-white text-violet hover:bg-cream font-bold text-base px-8 py-4">
                    Become a Tutor Today
                </a>
            </div>
            <div class="hidden lg:grid grid-cols-2 gap-4">
                @foreach([
                    ['KSh 1,500', 'Earned today', '#10B981'],
                    ['12', 'Upcoming sessions', '#6D28D9'],
                    ['4.9 ★', 'Your rating', '#D4A017'],
                    ['85%', 'You keep', '#E8522A'],
                ] as [$val, $label, $color])
                <div class="bg-white/10 border border-white/20 rounded-3xl p-5 text-center">
                    <p class="font-serif text-3xl font-bold text-white">{{ $val }}</p>
                    <p class="text-white/60 text-sm mt-1">{{ $label }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ── TESTIMONIALS ─────────────────────────────────────────────────────────── --}}
<section class="py-20 sm:py-28">
    <div class="section-container">
        <div class="text-center mb-12">
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-ink mb-3">What learners say</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                ['Grace W.', 'Parent', 'My daughter\'s Math grades improved from D to A in just 6 weeks. The 30-minute sessions fit perfectly into our evening routine.', '5', 'after sessions with Dr. Amara'],
                ['Brian K.', 'Student', 'I was struggling with KCSE Chemistry. My tutor broke everything down clearly and gave me practice questions. Passed with an A.', '5', 'after sessions with Mr. James'],
                ['Mercy N.', 'Parent', 'The access code system is brilliant — my son gets his code by email and joins the class independently. Very professional platform.', '5', 'after sessions with Ms. Fatuma'],
            ] as [$name, $role, $quote, $rating, $note])
            <div class="card p-6">
                <div class="star-rating mb-4">
                    @for($i = 0; $i < (int)$rating; $i++) ★ @endfor
                </div>
                <p class="text-ink leading-relaxed mb-5 text-sm">"{{ $quote }}"</p>
                <div class="flex items-center gap-3 pt-4 border-t border-hairline">
                    <div class="w-9 h-9 rounded-full bg-violet/20 flex items-center justify-center font-bold text-violet text-sm">
                        {{ substr($name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-semibold text-ink text-sm">{{ $name }}</p>
                        <p class="text-xs text-muted">{{ $role }} · {{ $note }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── FINAL CTA ────────────────────────────────────────────────────────────── --}}
<section class="py-20 bg-ink text-cream">
    <div class="section-container text-center">
        <h2 class="font-serif text-3xl sm:text-5xl font-bold mb-4">Ready to start learning?</h2>
        <p class="text-cream/60 text-xl mb-8 max-w-xl mx-auto">Join thousands of students and parents who trust TutorLink.</p>
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('tutors.index') }}" class="pill-btn bg-violet text-white hover:bg-violet-700 text-base px-8 py-4">
                Find a Tutor
            </a>
            <a href="{{ route('register') }}?role=tutor" class="pill-btn border-2 border-cream/30 text-cream hover:bg-cream/10 text-base px-8 py-4">
                Become a Tutor
            </a>
        </div>
    </div>
</section>

@endsection
