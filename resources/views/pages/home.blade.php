@extends('layouts.app')
@section('title', 'TutorLink – Find Verified Tutors in Kenya')

@section('content')

{{-- ── HERO ──────────────────────────────────────────────────────────────────────── --}}
<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <div>
                <span class="badge badge-violet" style="margin-bottom:20px;">🇰🇪 Kenya's #1 Tutor Marketplace</span>
                <h1>Find the perfect tutor for <span>every subject.</span></h1>
                <p>Book verified 1:1 sessions in flexible blocks — 15, 30, 45 or 60 minutes. Pay securely with Paystack. Start learning today.</p>

                <div class="hero-btns">
                    <a href="{{ route('tutors.index') }}" class="btn btn-primary">
                        Find a Tutor
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('register') }}?role=tutor" class="btn btn-outline">Become a Tutor</a>
                </div>

                <form action="{{ route('tutors.index') }}" method="GET" class="hero-search">
                    <input type="text" name="search" placeholder="Search by subject or tutor name…" value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary" style="padding:0 24px;">Search</button>
                </form>

                <div class="hero-trust">
                    @foreach([['500+','Verified Tutors'],['4.9★','Avg Rating'],['10,000+','Sessions'],['80+','Subjects']] as [$num,$label])
                    <div class="trust-item">
                        <span class="trust-num">{{ $num }}</span>
                        <span class="trust-label">{{ $label }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Floating tutor cards --}}
            <div class="hero-visual">
                @php
                $floatCards = [
                    ['Dr. Amara K.', 'Mathematics', '4.9', '#10B981', 'top:5%;left:10%;transform:rotate(-3deg)'],
                    ['Prof. James M.', 'Physics', '4.8', '#6D28D9', 'top:30%;left:40%;transform:rotate(2deg)'],
                    ['Ms. Fatuma A.', 'English', '5.0', '#E8522A', 'top:58%;left:5%;transform:rotate(-2deg)'],
                    ['Mr. David O.', 'Chemistry', '4.7', '#D4A017', 'top:72%;left:38%;transform:rotate(3deg)'],
                ];
                @endphp
                @foreach($floatCards as [$name, $subject, $rating, $color, $style])
                <div class="tutor-float-card" style="position:absolute;{{ $style }};">
                    <div class="avatar" style="background:{{ $color }}">{{ substr($name,0,1) }}</div>
                    <div class="name">{{ $name }}</div>
                    <div class="subject">{{ $subject }}</div>
                    <div class="rating">
                        <span style="color:#D4A017">★</span>
                        <span style="font-weight:600;font-size:12px">{{ $rating }}</span>
                        <span style="width:6px;height:6px;border-radius:50%;background:#10B981;margin-left:4px;display:inline-block;"></span>
                        <span style="color:#10B981;font-size:11px;font-weight:600;">Verified</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ── SESSION DURATION BLOCKS ──────────────────────────────────────────────────── --}}
<div class="duration-section">
    <div class="container">
        <div style="text-align:center;margin-bottom:0;">
            <h2 style="color:#fff;font-size:clamp(26px,4vw,38px);margin-bottom:10px;">Choose your session length</h2>
            <p style="color:rgba(255,255,255,0.6);font-size:16px;">Flexible blocks to fit any schedule or budget</p>
        </div>
        <div class="duration-grid">
            @foreach([
                ['15','Quick Focus','Perfect for revision or a quick Q&A session.','From KSh 375'],
                ['30','Half Hour','Ideal for targeted practice or concept clarity.','From KSh 750'],
                ['45','Extended','Great for deeper dives and exam preparation.','From KSh 1,125'],
                ['60','Full Hour','Maximum learning time. Best value per minute.','From KSh 1,500'],
            ] as [$mins, $title, $desc, $price])
            <a href="{{ route('tutors.index') }}" class="duration-card" style="display:block;">
                <div class="icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#6D28D9" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="mins">{{ $mins }}<span> min</span></div>
                <div class="title">{{ $title }}</div>
                <div class="desc">{{ $desc }}</div>
                <div class="price">{{ $price }}</div>
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- ── SUBJECT CATEGORIES ───────────────────────────────────────────────────────── --}}
<section class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2>Browse by subject</h2>
                <p>Find a tutor who specialises in exactly what you need</p>
            </div>
            <a href="{{ route('tutors.index') }}" class="view-all">
                View all
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="categories-grid">
            @forelse($categories as $category)
            <a href="{{ route('tutors.index') }}?category={{ $category->id }}" class="card card-hover category-card" style="display:block;">
                <div class="icon">{{ $category->icon ?? '📚' }}</div>
                <h3>{{ $category->name }}</h3>
                <p>{{ $category->subjects->count() }} subjects</p>
            </a>
            @empty
            @foreach([['📐','Mathematics','5 subjects'],['🔬','Sciences','4 subjects'],['🌍','Languages','5 subjects'],['💻','Programming','5 subjects'],['📊','Business','4 subjects'],['🎨','Arts & Music','5 subjects'],['📚','Humanities','4 subjects'],['📝','Test Prep','5 subjects']] as [$icon,$name,$count])
            <a href="{{ route('tutors.index') }}" class="card card-hover category-card" style="display:block;">
                <div class="icon">{{ $icon }}</div>
                <h3>{{ $name }}</h3>
                <p>{{ $count }}</p>
            </a>
            @endforeach
            @endforelse
        </div>
    </div>
</section>

{{-- ── HOW IT WORKS ─────────────────────────────────────────────────────────────── --}}
<section class="how-section" id="how-it-works">
    <div class="container">
        <div style="text-align:center;margin-bottom:0;">
            <h2 style="font-size:clamp(26px,4vw,40px);color:var(--ink);margin-bottom:8px;">How TutorLink works</h2>
            <p style="color:var(--muted);font-size:16px;max-width:480px;margin:0 auto;">Three simple steps to your first lesson</p>
        </div>
        <div class="steps-grid">
            @foreach([
                ['🔍','Find your tutor','Browse verified tutors by subject, price, and availability. Read real reviews from students.'],
                ['📅','Book a session','Pick your duration block (15–60 min) and a time that works. Pay securely with Paystack.'],
                ['🎓','Start learning','Join your live session. Your unique access code arrives by email. Learn and grow!'],
            ] as $i => [$icon, $title, $desc])
            <div class="card step-card">
                <div class="step-num">{{ $i+1 }}</div>
                <div class="step-icon">{{ $icon }}</div>
                <h3>{{ $title }}</h3>
                <p>{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── FEATURED TUTORS ──────────────────────────────────────────────────────────── --}}
@if($featuredTutors->isNotEmpty())
<section class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2>Meet our top tutors</h2>
                <p>Handpicked, verified experts ready to teach</p>
            </div>
            <a href="{{ route('tutors.index') }}" class="view-all">
                See all
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="tutors-grid">
            @foreach($featuredTutors as $tutor)
            <div class="card card-hover tutor-card">
                <div class="tutor-card-top">
                    <div class="tutor-avatar" style="background:{{ ['#6D28D9','#10B981','#E8522A','#D4A017','#3B82F6','#EC4899'][($loop->index % 6)] }}">
                        {{ substr($tutor->user->name, 0, 1) }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                            <span class="tutor-name">{{ $tutor->user->name }}</span>
                            <span class="tutor-verified">✓ Verified</span>
                        </div>
                        <div class="tutor-subjects">
                            @foreach($tutor->subjects->take(2) as $subject)
                            <span class="badge badge-violet">{{ $subject->name }}</span>
                            @endforeach
                        </div>
                        <div class="tutor-meta">
                            <span class="star">★</span>
                            <span style="font-weight:600;color:var(--ink)">{{ number_format($tutor->rating_avg,1) }}</span>
                            <span>({{ $tutor->total_reviews }})</span>
                            <span>·</span>
                            <span class="tutor-rate">KSh {{ number_format($tutor->hourly_rate,0) }}/hr</span>
                        </div>
                    </div>
                </div>
                @if($tutor->bio)
                <p class="tutor-bio">{{ $tutor->bio }}</p>
                @endif
                <div class="duration-pills">
                    @foreach([15,30,45,60] as $mins)
                    <div class="duration-pill">
                        <div class="d-min">{{ $mins }}m</div>
                        <div class="d-price">KSh {{ number_format($tutor->getPriceForDuration($mins),0) }}</div>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('tutors.show', \Illuminate\Support\Str::slug($tutor->user->name)) }}"
                   class="btn btn-primary" style="width:100%;justify-content:center;margin-top:16px;font-size:14px;padding:10px;">
                    Book Session
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── BECOME A TUTOR CTA ───────────────────────────────────────────────────────── --}}
<section class="tutor-cta">
    <div class="container">
        <div class="tutor-cta-grid">
            <div>
                <span class="badge" style="background:rgba(255,255,255,0.2);color:#fff;margin-bottom:20px;">For Tutors</span>
                <h2>Earn doing what you love</h2>
                <p>Sign up, upload your documents, get verified and start accepting bookings. Set your own rates and keep up to 85% of your earnings.</p>
                <div class="cta-points">
                    @foreach(['Upload documents once — verified for life','Set your own rates per subject','Get paid directly to M-Pesa or bank','Unique access code system for every session'] as $point)
                    <div class="cta-point">
                        <div class="cta-point-dot">
                            <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        {{ $point }}
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('register') }}?role=tutor" class="btn btn-white">Become a Tutor Today</a>
            </div>
            <div class="cta-stats">
                    @foreach([['KSh 1,500','Earned today'],['12','Upcoming sessions'],['4.9 ★','Your rating'],['85%','You keep']] as [$val,$label])
                <div class="cta-stat">
                    <div class="cta-stat-num">{{ $val }}</div>
                    <div class="cta-stat-label">{{ $label }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ── TESTIMONIALS ─────────────────────────────────────────────────────────────── --}}
<section class="section">
    <div class="container">
        <div style="text-align:center;margin-bottom:40px;">
            <h2 style="font-size:clamp(26px,4vw,40px);color:var(--ink);">What learners say</h2>
        </div>
        <div class="testimonials-grid">
            @foreach([
                ['Grace W.','Parent','My daughter\'s Math grades improved from D to A in just 6 weeks. The 30-minute sessions fit perfectly into our evening routine.','after sessions with Dr. Amara','G'],
                ['Brian K.','Student','I was struggling with KCSE Chemistry. My tutor broke everything down clearly. Passed with an A!','after sessions with Mr. James','B'],
                ['Mercy N.','Parent','The access code system is brilliant — my son gets his code by email and joins the class independently. Very professional.','after sessions with Ms. Fatuma','M'],
            ] as [$name, $role, $quote, $note, $initial])
            <div class="card testimonial-card">
                <div class="stars">★★★★★</div>
                <p class="testimonial-text">"{{ $quote }}"</p>
                <div class="testimonial-author">
                    <div class="author-avatar">{{ $initial }}</div>
                    <div>
                        <div class="author-name">{{ $name }}</div>
                        <div class="author-note">{{ $role }} · {{ $note }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── FINAL CTA ────────────────────────────────────────────────────────────────── --}}
<section class="final-cta">
    <div class="container">
        <h2>Ready to start learning?</h2>
        <p>Join thousands of students and parents who trust TutorLink across Kenya.</p>
        <div class="final-cta-btns">
            <a href="{{ route('tutors.index') }}" class="btn btn-primary" style="font-size:16px;padding:14px 36px;">Find a Tutor</a>
            <a href="{{ route('register') }}?role=tutor" class="btn" style="background:transparent;color:rgba(255,255,255,0.8);border:2px solid rgba(255,255,255,0.3);font-size:16px;padding:14px 36px;">Become a Tutor</a>
        </div>
    </div>
</section>

@endsection
