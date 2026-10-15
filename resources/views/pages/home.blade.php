<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Mwalimu – Find Verified Tutors in Kenya</title>
<meta name="description" content="Book verified 1:1 tutors across 80+ subjects. Choose 15, 30, 45 or 60-minute sessions and pay securely with Paystack.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@livewireStyles
<style>
:root{--ink:#15281F;--cream:#FBF5E6;--violet:#6D28D9;--coral:#E8522A;--gold:#D4A017;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{background:var(--cream);scroll-behavior:smooth;}
body{font-family:'DM Sans',sans-serif;color:var(--ink);background:var(--cream);line-height:1.6;}
h1,h2,h3,h4{font-family:'Fraunces',Georgia,serif;line-height:1.1;}
a{color:inherit;text-decoration:none;}[x-cloak]{display:none!important;}
.w{max-width:1360px;margin:0 auto;padding:0 24px;}
@media(min-width:1024px){.w{padding:0 48px;}}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 28px;border-radius:999px;font-weight:600;font-size:15px;cursor:pointer;transition:all .18s;border:none;font-family:inherit;text-decoration:none;}
.btn-v{background:var(--violet);color:#fff;}.btn-v:hover{background:#4C1D95;transform:translateY(-1px);box-shadow:0 6px 20px rgba(109,40,217,.3);}
.btn-o{background:transparent;color:var(--ink);border:2px solid var(--ink);}.btn-o:hover{background:var(--ink);color:var(--cream);}
.btn-w{background:#fff;color:var(--violet);font-weight:700;}.btn-w:hover{background:var(--cream);}
.btn-c{background:var(--coral);color:#fff;}.btn-c:hover{opacity:.9;}
.badge{display:inline-flex;align-items:center;padding:5px 13px;border-radius:999px;font-size:12px;font-weight:600;}
.bv{background:rgba(109,40,217,.1);color:var(--violet);}
.bm{background:rgba(16,185,129,.1);color:var(--mint);}
.card{background:#fff;border-radius:24px;border:1px solid var(--hair);box-shadow:0 2px 16px rgba(21,40,31,.06);}
.card-lift{transition:transform .2s,box-shadow .2s;}.card-lift:hover{transform:translateY(-5px);box-shadow:0 14px 36px rgba(21,40,31,.12);}
</style>
</head>
<body>

{{-- ANNOUNCEMENT BAR --}}
<div x-data="{ show: true }" x-show="show" x-cloak style="background:var(--ink);color:var(--cream);font-size:13px;padding:9px 24px;display:flex;align-items:center;justify-content:center;gap:8px;position:relative;">
    <span>🎓 Kenya's verified tutor marketplace — book 15, 30, 45 or 60-minute sessions</span>
    <button @click="show=false" style="position:absolute;right:16px;background:none;border:none;color:rgba(251,245,230,.5);cursor:pointer;font-size:20px;line-height:1;">&times;</button>
</div>

{{-- NAV --}}
<nav style="position:sticky;top:0;z-index:100;background:rgba(251,245,230,.92);backdrop-filter:blur(16px);border-bottom:1px solid var(--hair);" x-data="{ open:false }">
    <div class="w" style="display:flex;align-items:center;justify-content:space-between;height:72px;gap:16px;">
        <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:10px;font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);flex-shrink:0;">
            <svg width="38" height="38" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#15281F"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
            Mwalimu
        </a>
        <div style="display:none;align-items:center;gap:4px;" class="desk-nav">
            @foreach([['Find a Tutor', route('tutors.index')],['Courses', route('courses.index')],['Blog', route('blog.index')],['How It Works','#how']] as [$l,$h])
            <a href="{{ $h }}" style="padding:8px 16px;border-radius:999px;font-size:14px;font-weight:500;transition:background .15s;" onmouseover="this.style.background='rgba(21,40,31,.07)'" onmouseout="this.style.background='transparent'">{{ $l }}</a>
            @endforeach
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            @auth
            <a href="{{ route('dashboard') }}" class="btn btn-v" style="padding:10px 22px;font-size:14px;">Dashboard</a>
            @else
            <a href="{{ route('login') }}" style="padding:8px 18px;border-radius:999px;font-size:14px;font-weight:600;transition:background .15s;display:none;" class="desk-signin" onmouseover="this.style.background='rgba(21,40,31,.07)'" onmouseout="this.style.background='transparent'">Sign In</a>
            <a href="{{ route('register') }}" class="btn btn-v" style="padding:10px 22px;font-size:14px;">Get Started</a>
            @endauth
            <button @click="open=!open" style="display:flex;width:42px;height:42px;border-radius:999px;border:2px solid var(--hair);background:none;cursor:pointer;align-items:center;justify-content:center;" class="mob-ham">
                <svg x-show="!open" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="open" x-cloak width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    <div x-show="open" x-cloak x-transition style="position:absolute;left:0;right:0;top:100%;background:var(--cream);border-bottom:1px solid var(--hair);padding:16px 24px;display:flex;flex-direction:column;gap:4px;box-shadow:0 8px 32px rgba(21,40,31,.1);z-index:99;">
        @foreach([['Find a Tutor', route('tutors.index')],['Courses', route('courses.index')],['Blog', route('blog.index')],['How It Works','#how']] as [$l,$h])
        <a href="{{ $h }}" style="padding:13px 0;font-size:15px;font-weight:500;border-bottom:1px solid var(--hair);">{{ $l }}</a>
        @endforeach
        @guest
        <a href="{{ route('login') }}" style="padding:13px 0;font-size:15px;font-weight:600;color:var(--violet);">Sign In</a>
        @endguest
    </div>
    <style>.desk-nav,.desk-signin{display:none!important;}@media(min-width:1024px){.desk-nav{display:flex!important;}.desk-signin{display:block!important;}.mob-ham{display:none!important;}}</style>
</nav>

{{-- HERO --}}
<section style="padding:90px 0 70px;overflow:hidden;position:relative;">
    <div class="w">
        <div style="display:grid;grid-template-columns:1fr;gap:48px;align-items:center;" class="hero-grid">
            <div>
                <div class="badge bv" style="margin-bottom:22px;">🇰🇪 Kenya's #1 Tutor Marketplace</div>
                <h1 style="font-size:clamp(42px,6vw,74px);color:var(--ink);margin-bottom:22px;letter-spacing:-.02em;">
                    Find the perfect<br>tutor for <span style="color:var(--violet);">every subject.</span>
                </h1>
                <p style="font-size:18px;color:var(--muted);margin-bottom:32px;max-width:520px;line-height:1.75;">
                    Book verified 1:1 sessions in flexible blocks — 15, 30, 45 or 60 minutes. Pay securely with Paystack. Start learning today.
                </p>
                <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:36px;">
                    <a href="{{ route('tutors.index') }}" class="btn btn-v" style="font-size:16px;padding:15px 34px;">
                        Find a Tutor
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('register') }}?role=tutor" class="btn btn-o" style="font-size:16px;padding:15px 34px;">Become a Tutor</a>
                </div>
                <form action="{{ route('tutors.index') }}" method="GET" style="display:flex;gap:8px;max-width:480px;margin-bottom:40px;">
                    <div style="flex:1;position:relative;">
                        <svg style="position:absolute;left:16px;top:50%;transform:translateY(-50%);color:var(--muted);" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        <input type="text" name="search" placeholder="Search by subject or tutor name…"
                               style="width:100%;height:52px;padding:0 16px 0 44px;border-radius:999px;border:1.5px solid rgba(21,40,31,.2);background:#fff;font-size:15px;font-family:inherit;outline:none;">
                    </div>
                    <button type="submit" class="btn btn-v" style="padding:0 26px;height:52px;">Search</button>
                </form>
                <div style="display:flex;flex-wrap:wrap;gap:28px;padding-top:28px;border-top:1px solid var(--hair);">
                    @foreach([['500+','Verified Tutors'],['4.9★','Avg Rating'],['10,000+','Sessions'],['80+','Subjects']] as [$n,$l])
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="font-family:'Fraunces',serif;font-size:24px;font-weight:700;color:var(--ink);">{{ $n }}</span>
                        <span style="font-size:13px;color:var(--muted);">{{ $l }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Floating tutor cards --}}
            <div style="position:relative;height:420px;display:none;" class="hero-vis">
                @php
                $cards=[
                    ['Dr. Amara K.','Mathematics','4.9','#10B981','top:4%;left:8%;transform:rotate(-3deg)'],
                    ['Prof. James M.','Physics','4.8','#6D28D9','top:30%;left:42%;transform:rotate(2deg)'],
                    ['Ms. Fatuma A.','English','5.0','#E8522A','top:58%;left:5%;transform:rotate(-2deg)'],
                    ['Mr. David O.','Chemistry','4.7','#D4A017','top:72%;left:40%;transform:rotate(3deg)'],
                ];
                @endphp
                @foreach($cards as [$name,$subj,$rating,$color,$style])
                <div style="position:absolute;{{ $style }};background:#fff;border-radius:20px;border:1px solid var(--hair);padding:14px 16px;box-shadow:0 8px 28px rgba(21,40,31,.1);width:186px;">
                    <div style="width:42px;height:42px;border-radius:12px;background:{{ $color }};display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#fff;margin-bottom:8px;">{{ substr($name,0,1) }}</div>
                    <div style="font-family:'Fraunces',serif;font-size:14px;font-weight:700;color:var(--ink);">{{ $name }}</div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $subj }}</div>
                    <div style="display:flex;align-items:center;gap:6px;margin-top:7px;">
                        <span style="color:var(--gold);font-size:12px;">★</span>
                        <span style="font-size:12px;font-weight:600;color:var(--ink);">{{ $rating }}</span>
                        <span style="font-size:11px;font-weight:600;color:var(--mint);">✓ Verified</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <style>.hero-grid{grid-template-columns:1fr!important;}@media(min-width:1024px){.hero-grid{grid-template-columns:1fr 480px!important;}.hero-vis{display:block!important;}}</style>
</section>

{{-- DURATION BLOCKS --}}
<section style="background:var(--ink);padding:80px 0;">
    <div class="w">
        <div style="text-align:center;margin-bottom:44px;">
            <h2 style="font-size:clamp(28px,4vw,42px);color:#fff;margin-bottom:10px;">Choose your session length</h2>
            <p style="color:rgba(255,255,255,.55);font-size:17px;">Flexible blocks to fit any schedule or budget</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;" class="dur-grid">
            @foreach([
                ['15','Quick Focus','Perfect for a specific question, quick revision, or a short drill.','From KSh 375'],
                ['30','Half Hour','Great for targeted concept practice or working through a problem set.','From KSh 750'],
                ['45','Extended','Deeper dives into topics, structured lesson delivery, exam prep.','From KSh 1,125'],
                ['60','Full Hour','Maximum learning. Best value per minute. Ideal for weekly sessions.','From KSh 1,500'],
            ] as [$mins,$title,$desc,$price])
            <a href="{{ route('tutors.index') }}" style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:22px;padding:28px 22px;display:block;transition:background .2s;" onmouseover="this.style.background='rgba(255,255,255,.13)'" onmouseout="this.style.background='rgba(255,255,255,.07)'">
                <div style="width:46px;height:46px;background:rgba(109,40,217,.35);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div style="font-family:'Fraunces',serif;font-size:40px;font-weight:700;color:#fff;line-height:1;">{{ $mins }}<span style="font-size:17px;font-weight:400;color:rgba(255,255,255,.4);"> min</span></div>
                <div style="font-size:15px;font-weight:600;color:#fff;margin:10px 0 5px;">{{ $title }}</div>
                <div style="font-size:13px;color:rgba(255,255,255,.5);line-height:1.6;">{{ $desc }}</div>
                <div style="font-size:14px;font-weight:700;color:var(--gold);margin-top:16px;">{{ $price }}</div>
            </a>
            @endforeach
        </div>
    </div>
    <style>.dur-grid{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:768px){.dur-grid{grid-template-columns:repeat(4,1fr)!important;}}</style>
</section>

{{-- CATEGORIES --}}
<section style="padding:80px 0;">
    <div class="w">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:40px;gap:16px;flex-wrap:wrap;">
            <div>
                <h2 style="font-size:clamp(28px,4vw,42px);color:var(--ink);margin-bottom:8px;">Browse by subject</h2>
                <p style="font-size:16px;color:var(--muted);">Find a tutor who specialises in exactly what you need</p>
            </div>
            <a href="{{ route('tutors.index') }}" style="font-size:14px;font-weight:600;color:var(--violet);display:flex;align-items:center;gap:4px;white-space:nowrap;">
                View all tutors
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;" class="cat-grid">
            @forelse($categories as $cat)
            <a href="{{ route('tutors.index') }}?category={{ $cat->id }}" class="card card-lift" style="padding:24px;display:block;">
                <div style="font-size:36px;margin-bottom:12px;">{{ $cat->icon ?? '📚' }}</div>
                <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);transition:color .15s;">{{ $cat->name }}</h3>
                <p style="font-size:12px;color:var(--muted);margin-top:4px;">{{ $cat->subjects->count() }} subjects</p>
            </a>
            @empty
            @foreach([['📐','Mathematics','5 subjects'],['🔬','Sciences','4 subjects'],['🌍','Languages','5 subjects'],['💻','Programming','5 subjects'],['📊','Business','4 subjects'],['🎨','Arts & Music','5 subjects'],['📚','Humanities','4 subjects'],['📝','Test Prep','5 subjects']] as [$ic,$nm,$ct])
            <a href="{{ route('tutors.index') }}" class="card card-lift" style="padding:24px;display:block;">
                <div style="font-size:36px;margin-bottom:12px;">{{ $ic }}</div>
                <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);">{{ $nm }}</h3>
                <p style="font-size:12px;color:var(--muted);margin-top:4px;">{{ $ct }}</p>
            </a>
            @endforeach
            @endforelse
        </div>
    </div>
    <style>.cat-grid{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:640px){.cat-grid{grid-template-columns:repeat(3,1fr)!important;}}@media(min-width:1024px){.cat-grid{grid-template-columns:repeat(4,1fr)!important;}}</style>
</section>

{{-- HOW IT WORKS --}}
<section id="how" style="padding:80px 0;background:rgba(21,40,31,.03);">
    <div class="w">
        <div style="text-align:center;margin-bottom:48px;">
            <h2 style="font-size:clamp(28px,4vw,42px);color:var(--ink);margin-bottom:10px;">How Mwalimu works</h2>
            <p style="font-size:17px;color:var(--muted);max-width:480px;margin:0 auto;">Three simple steps to your first lesson</p>
        </div>
        <div style="display:grid;grid-template-columns:1fr;gap:20px;" class="steps-grid">
            @foreach([
                ['🔍','Find your tutor','Browse verified tutors by subject, price, and availability. Read real reviews from students and parents.'],
                ['📅','Book & pay','Pick your duration block (15–60 min) and a time that works. Pay securely with Paystack — M-Pesa or card.'],
                ['🎓','Join your session','Receive a unique access code by email. Enter it on session day to open your live Jitsi classroom instantly.'],
            ] as $i => [$icon,$title,$desc])
            <div class="card" style="padding:32px;text-align:center;position:relative;">
                <div style="position:absolute;top:-14px;left:-14px;width:32px;height:32px;background:var(--violet);color:#fff;border-radius:999px;font-size:13px;font-weight:700;display:flex;align-items:center;justify-content:center;">{{ $i+1 }}</div>
                <div style="width:64px;height:64px;background:rgba(109,40,217,.1);border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:30px;margin:0 auto 20px;">{{ $icon }}</div>
                <h3 style="font-size:21px;color:var(--ink);margin-bottom:10px;">{{ $title }}</h3>
                <p style="font-size:14px;color:var(--muted);line-height:1.8;">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
    <style>.steps-grid{grid-template-columns:1fr!important;}@media(min-width:768px){.steps-grid{grid-template-columns:repeat(3,1fr)!important;}}</style>
</section>

{{-- FEATURED TUTORS --}}
@if($featuredTutors->isNotEmpty())
<section style="padding:80px 0;">
    <div class="w">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:40px;gap:16px;flex-wrap:wrap;">
            <div>
                <h2 style="font-size:clamp(28px,4vw,42px);color:var(--ink);margin-bottom:8px;">Meet our top tutors</h2>
                <p style="font-size:16px;color:var(--muted);">Handpicked, verified experts ready to teach</p>
            </div>
            <a href="{{ route('tutors.index') }}" style="font-size:14px;font-weight:600;color:var(--violet);display:flex;align-items:center;gap:4px;white-space:nowrap;">
                See all tutors <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div style="display:grid;grid-template-columns:1fr;gap:18px;" class="tutors-grid">
            @foreach($featuredTutors as $tutor)
            @php $colors=['#6D28D9','#10B981','#E8522A','#D4A017','#3B82F6','#EC4899']; $c=$colors[$loop->index % 6]; @endphp
            <div class="card card-lift" style="padding:22px;">
                <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:12px;">
                    <div style="width:60px;height:60px;border-radius:16px;background:{{ $c }};display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;color:#fff;flex-shrink:0;">{{ substr($tutor->user->name,0,1) }}</div>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                            <span style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);">{{ $tutor->user->name }}</span>
                            <span style="font-size:11px;font-weight:600;color:var(--mint);">✓ Verified</span>
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:5px;">
                            @foreach($tutor->subjects->take(2) as $s)
                            <span class="badge bv" style="font-size:11px;padding:3px 9px;">{{ $s->name }}</span>
                            @endforeach
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;margin-top:7px;font-size:13px;color:var(--muted);">
                            <span style="color:var(--gold);font-weight:700;">★ {{ number_format($tutor->rating_avg,1) }}</span>
                            <span>({{ $tutor->total_reviews }} reviews)</span>
                            <span>·</span>
                            <span style="font-weight:700;color:var(--ink);">KSh {{ number_format($tutor->hourly_rate,0) }}/hr</span>
                        </div>
                    </div>
                </div>
                @if($tutor->bio)
                <p style="font-size:13px;color:var(--muted);line-height:1.6;margin-bottom:14px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $tutor->bio }}</p>
                @endif
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:6px;margin-bottom:14px;">
                    @foreach([15,30,45,60] as $mins)
                    <div style="text-align:center;border:1.5px solid var(--hair);border-radius:12px;padding:7px 4px;cursor:pointer;transition:all .15s;" onmouseover="this.style.borderColor='var(--violet)';this.style.background='rgba(109,40,217,.05)'" onmouseout="this.style.borderColor='var(--hair)';this.style.background='transparent'">
                        <div style="font-size:11px;font-weight:700;color:var(--ink);">{{ $mins }}m</div>
                        <div style="font-size:10px;color:var(--muted);margin-top:1px;">KSh {{ number_format($tutor->getPriceForDuration($mins),0) }}</div>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('tutors.show', \Illuminate\Support\Str::slug($tutor->user->name)) }}" class="btn btn-v" style="width:100%;font-size:14px;padding:11px;">Book Session</a>
            </div>
            @endforeach
        </div>
    </div>
    <style>.tutors-grid{grid-template-columns:1fr!important;}@media(min-width:640px){.tutors-grid{grid-template-columns:repeat(2,1fr)!important;}}@media(min-width:1024px){.tutors-grid{grid-template-columns:repeat(3,1fr)!important;}}</style>
</section>
@endif

{{-- BECOME A TUTOR CTA --}}
<section style="background:var(--violet);padding:80px 0;">
    <div class="w">
        <div style="display:grid;grid-template-columns:1fr;gap:48px;align-items:center;" class="cta-grid">
            <div>
                <span class="badge" style="background:rgba(255,255,255,.18);color:#fff;margin-bottom:22px;">For Tutors</span>
                <h2 style="font-size:clamp(30px,4vw,48px);color:#fff;margin-bottom:16px;">Earn doing what<br>you love</h2>
                <p style="font-size:17px;color:rgba(255,255,255,.75);line-height:1.75;margin-bottom:28px;max-width:460px;">
                    Sign up, upload your documents, get verified and start accepting bookings. Set your own rates and keep up to 85% of your earnings.
                </p>
                <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:32px;">
                    @foreach(['Upload documents once — verified for life','Set your own hourly rate in KSh','Get paid directly to M-Pesa or bank','Unique session access codes sent by email'] as $pt)
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:22px;height:22px;background:rgba(255,255,255,.2);border-radius:999px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <span style="font-size:15px;color:rgba(255,255,255,.9);">{{ $pt }}</span>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('register') }}?role=tutor" class="btn btn-w" style="font-size:16px;padding:15px 36px;">Become a Tutor Today</a>
            </div>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;">
                @foreach([['KSh 1,500','Earned today'],['12','Upcoming sessions'],['4.9 ★','Your rating'],['85%','You keep']] as [$val,$lbl])
                <div style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:20px;padding:22px;text-align:center;">
                    <div style="font-family:'Fraunces',serif;font-size:30px;font-weight:700;color:#fff;">{{ $val }}</div>
                    <div style="font-size:12px;color:rgba(255,255,255,.6);margin-top:4px;">{{ $lbl }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <style>.cta-grid{grid-template-columns:1fr!important;}@media(min-width:1024px){.cta-grid{grid-template-columns:1fr 1fr!important;}}</style>
</section>

{{-- TESTIMONIALS --}}
<section style="padding:80px 0;">
    <div class="w">
        <div style="text-align:center;margin-bottom:48px;">
            <h2 style="font-size:clamp(28px,4vw,42px);color:var(--ink);margin-bottom:10px;">What learners say</h2>
            <p style="font-size:17px;color:var(--muted);">Real results from real students and parents across Kenya</p>
        </div>
        <div style="display:grid;grid-template-columns:1fr;gap:18px;" class="testi-grid">
            @foreach([
                ['G','Grace W.','Parent','My daughter\'s Maths grade went from D to A in just 6 weeks. The 30-minute sessions fit perfectly into our evening routine — no long commute, no hassle.','after sessions with Dr. Amara'],
                ['B','Brian K.','Student, Form 4','I was really struggling with KCSE Chemistry. My tutor broke every concept down clearly and gave me past paper practice. I passed with an A.','after sessions with Mr. James'],
                ['M','Mercy N.','Parent','The session access code system is brilliant. My son gets his unique code by email and joins the class by himself. Very professional platform.','after sessions with Ms. Fatuma'],
            ] as [$init,$name,$role,$quote,$note])
            <div class="card" style="padding:28px;">
                <div style="color:var(--gold);font-size:18px;letter-spacing:3px;margin-bottom:16px;">★★★★★</div>
                <p style="font-size:15px;color:var(--ink);line-height:1.85;margin-bottom:22px;font-style:italic;">"{{ $quote }}"</p>
                <div style="display:flex;align-items:center;gap:12px;padding-top:18px;border-top:1px solid var(--hair);">
                    <div style="width:38px;height:38px;border-radius:999px;background:rgba(109,40,217,.12);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--violet);font-size:15px;flex-shrink:0;">{{ $init }}</div>
                    <div>
                        <div style="font-weight:700;font-size:14px;color:var(--ink);">{{ $name }}</div>
                        <div style="font-size:12px;color:var(--muted);margin-top:1px;">{{ $role }} · {{ $note }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <style>.testi-grid{grid-template-columns:1fr!important;}@media(min-width:768px){.testi-grid{grid-template-columns:repeat(3,1fr)!important;}}</style>
</section>

{{-- SESSION CODE EXPLAINER --}}
<section style="padding:80px 0;background:rgba(21,40,31,.03);">
    <div class="w">
        <div style="display:grid;grid-template-columns:1fr;gap:48px;align-items:center;" class="code-grid">
            <div>
                <div class="badge bv" style="margin-bottom:20px;">🔐 Secure Access</div>
                <h2 style="font-size:clamp(28px,4vw,42px);color:var(--ink);margin-bottom:16px;">Your unique session<br>access code</h2>
                <p style="font-size:16px;color:var(--muted);line-height:1.8;margin-bottom:28px;">After booking, both tutor and student receive a unique code by email. Enter your code on session day to instantly open your private Jitsi classroom — no app downloads, no account needed.</p>
                <div style="display:flex;flex-direction:column;gap:14px;">
                    @foreach([
                        ['📧','Emailed instantly','Codes are sent immediately when your booking is confirmed.'],
                        ['🔒','Unique per session','Each booking generates a new TUT-XXXXXX (tutor) and STU-XXXXXX (student) code.'],
                        ['🎥','Opens Jitsi live class','Enter your code on the booking page to launch your private video room.'],
                    ] as [$ic,$title,$desc])
                    <div style="display:flex;align-items:flex-start;gap:14px;">
                        <div style="width:44px;height:44px;background:rgba(109,40,217,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">{{ $ic }}</div>
                        <div>
                            <div style="font-weight:700;font-size:15px;color:var(--ink);">{{ $title }}</div>
                            <div style="font-size:13px;color:var(--muted);margin-top:3px;line-height:1.6;">{{ $desc }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div>
                <div style="background:#fff;border-radius:28px;border:2px solid var(--hair);padding:40px;text-align:center;box-shadow:0 8px 40px rgba(21,40,31,.08);">
                    <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:var(--muted);margin-bottom:16px;">Your Session Access Code</p>
                    <div style="font-family:monospace;font-size:40px;font-weight:800;color:var(--violet);letter-spacing:.12em;background:rgba(109,40,217,.06);border:2px solid rgba(109,40,217,.2);border-radius:16px;padding:20px 24px;margin-bottom:16px;">STU-M4N8RZ</div>
                    <p style="font-size:13px;color:var(--muted);line-height:1.6;">Use this code on the booking page to enter your live session. No download required.</p>
                    <div style="margin-top:24px;padding-top:20px;border-top:1px solid var(--hair);display:flex;justify-content:center;gap:24px;">
                        <div style="text-align:center;">
                            <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Student code</div>
                            <div style="font-family:monospace;font-size:16px;font-weight:700;color:var(--violet);">STU-XXXXXX</div>
                        </div>
                        <div style="width:1px;background:var(--hair);"></div>
                        <div style="text-align:center;">
                            <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Tutor code</div>
                            <div style="font-family:monospace;font-size:16px;font-weight:700;color:var(--mint);">TUT-XXXXXX</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>.code-grid{grid-template-columns:1fr!important;}@media(min-width:1024px){.code-grid{grid-template-columns:1fr 1fr!important;}}</style>
</section>

{{-- FINAL CTA --}}
<section style="background:var(--ink);padding:90px 0;text-align:center;">
    <div class="w">
        <h2 style="font-size:clamp(30px,5vw,56px);color:#fff;margin-bottom:16px;">Ready to start learning?</h2>
        <p style="font-size:18px;color:rgba(255,255,255,.6);margin-bottom:40px;max-width:500px;margin-left:auto;margin-right:auto;">
            Join thousands of students and parents who trust Mwalimu across Kenya.
        </p>
        <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:14px;">
            <a href="{{ route('tutors.index') }}" class="btn btn-v" style="font-size:16px;padding:16px 40px;">Find a Tutor</a>
            <a href="{{ route('register') }}?role=tutor" class="btn" style="background:transparent;color:rgba(255,255,255,.8);border:2px solid rgba(255,255,255,.3);font-size:16px;padding:16px 40px;">Become a Tutor</a>
        </div>
    </div>
</section>

{{-- FOOTER --}}
<footer style="background:var(--ink);border-top:1px solid rgba(255,255,255,.07);padding:64px 0 32px;">
    <div class="w">
        <div style="display:grid;grid-template-columns:1fr;gap:40px;margin-bottom:48px;" class="footer-grid">
            <div>
                <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:10px;font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:#fff;margin-bottom:14px;">
                    <svg width="30" height="30" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
                    Mwalimu
                </a>
                <p style="font-size:14px;color:rgba(255,255,255,.45);line-height:1.75;max-width:280px;">
                    Connecting learners and parents with verified tutors across Kenya. Book sessions in flexible time blocks.
                </p>
            </div>
            @foreach([
                ['For Students',[['Find a Tutor', route('tutors.index')],['Browse Courses', route('courses.index')],['Sign Up Free', route('register')],['How It Works','#how']]],
                ['For Tutors',[['Become a Tutor', route('register').'?role=tutor'],['Tutor Guidelines','#'],['Earnings','#'],['Verification','#']]],
                ['Support',[['Help Centre','#'],['Contact Us','#'],['Privacy Policy','#'],['Terms of Service','#']]],
            ] as [$heading, $links])
            <div>
                <h4 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.35);margin-bottom:16px;">{{ $heading }}</h4>
                @foreach($links as [$label,$url])
                <a href="{{ $url }}" style="display:block;font-size:14px;color:rgba(255,255,255,.6);margin-bottom:10px;transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.6)'">{{ $label }}</a>
                @endforeach
            </div>
            @endforeach
        </div>
        <div style="border-top:1px solid rgba(255,255,255,.08);padding-top:24px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
            <span style="font-size:12px;color:rgba(255,255,255,.3);">&copy; {{ date('Y') }} Mwalimu. All rights reserved.</span>
            <span style="font-size:12px;color:rgba(255,255,255,.3);">Made with ❤ for learners across Kenya</span>
        </div>
    </div>
    <style>.footer-grid{grid-template-columns:1fr!important;}@media(min-width:640px){.footer-grid{grid-template-columns:repeat(2,1fr)!important;}}@media(min-width:1024px){.footer-grid{grid-template-columns:2fr 1fr 1fr 1fr!important;}}</style>
</footer>

{{-- COOKIE BANNER --}}
<div x-data="{ show: !localStorage.getItem('cookie_ok') }" x-show="show" x-cloak
     style="position:fixed;bottom:24px;left:50%;transform:translateX(-50%);z-index:9999;width:calc(100% - 32px);max-width:440px;">
    <div style="background:#FFFDF5;border:2px solid var(--ink);border-radius:20px;padding:18px 20px;box-shadow:0 14px 40px rgba(21,40,31,.22);">
        <div style="display:flex;gap:12px;align-items:flex-start;">
            <span style="font-size:26px;flex-shrink:0;">🍪</span>
            <div>
                <p style="font-weight:700;font-size:15px;color:var(--ink);margin-bottom:4px;">We value your privacy</p>
                <p style="font-size:12px;color:var(--muted);line-height:1.5;">We use cookies to personalise your experience and improve the platform.</p>
            </div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:14px;">
            <button @click="localStorage.setItem('cookie_ok','none');show=false"
                    style="padding:9px 18px;border-radius:999px;font-size:13px;font-weight:600;background:#fff;color:var(--ink);border:1.5px solid var(--ink);cursor:pointer;font-family:inherit;">
                Necessary Only
            </button>
            <button @click="localStorage.setItem('cookie_ok','all');show=false"
                    style="padding:9px 20px;border-radius:999px;font-size:13px;font-weight:700;background:var(--violet);color:#fff;border:1.5px solid var(--ink);cursor:pointer;font-family:inherit;">
                Accept All
            </button>
        </div>
    </div>
</div>

@livewireScripts
</body>
</html>
