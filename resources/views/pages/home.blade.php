<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Mwalimu – Instant 1-on-1 Tutoring in Kenya</title>
<meta name="description" content="Instant 1-on-1 tutoring matched in seconds. TSC-registered teachers, secure classrooms, 100% money-back guarantee. Book, pay, and learn in one safe place.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@livewireStyles
<style>
:root{--ink:#15281F;--cream:#FBF5E6;--trust-blue:#0A64BC;--coral:#E8522A;--gold:#EEBE15;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{background:var(--cream);scroll-behavior:smooth;}
body{font-family:'DM Sans',sans-serif;color:var(--ink);background:var(--cream);line-height:1.6;}
h1,h2,h3,h4{font-family:'Fraunces',Georgia,serif;line-height:1.1;}
a{color:inherit;text-decoration:none;}[x-cloak]{display:none!important;}
.w{max-width:1360px;margin:0 auto;padding:0 24px;}
@media(min-width:1024px){.w{padding:0 48px;}}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 28px;border-radius:999px;font-weight:600;font-size:15px;cursor:pointer;transition:all .18s;border:none;font-family:inherit;text-decoration:none;}
.btn-v{background:var(--trust-blue);color:#fff;}.btn-v:hover{background:#120A8F;transform:translateY(-1px);box-shadow:0 6px 20px rgba(10,100,188,.3);}
.btn-o{background:transparent;color:var(--ink);border:2px solid var(--ink);}.btn-o:hover{background:var(--ink);color:var(--cream);}
.btn-coral{background:var(--coral);color:#fff;}.btn-coral:hover{opacity:.9;transform:translateY(-1px);}
.btn-mint{background:var(--mint);color:#fff;}.btn-mint:hover{background:#059669;}
.btn-w{background:#fff;color:var(--trust-blue);font-weight:700;}.btn-w:hover{background:var(--cream);}
.card{background:#fff;border-radius:22px;border:1px solid var(--hair);box-shadow:0 2px 16px rgba(21,40,31,.06);}
.card-lift{transition:transform .2s,box-shadow .2s;}.card-lift:hover{transform:translateY(-5px);box-shadow:0 14px 36px rgba(21,40,31,.12);}
.badge{display:inline-flex;align-items:center;gap:5px;padding:5px 13px;border-radius:999px;font-size:12px;font-weight:600;}
.bv{background:rgba(10,100,188,.1);color:var(--trust-blue);}
.bm{background:rgba(16,185,129,.1);color:var(--mint);}
.bc{background:rgba(232,82,42,.1);color:var(--coral);}
/* NAV */
nav.main{position:sticky;top:0;z-index:100;background:rgba(251,245,230,.92);backdrop-filter:blur(16px);border-bottom:1px solid var(--hair);}
.nav-inner{display:flex;align-items:center;justify-content:space-between;height:72px;gap:16px;}
.logo{display:flex;align-items:center;gap:10px;font-family:'Fraunces',serif;font-size:24px;font-weight:800;color:var(--ink);}
.logo em{font-style:normal;color:var(--trust-blue);}
.desk-links{display:none;align-items:center;gap:4px;}
@media(min-width:1024px){.desk-links{display:flex;}}
.desk-links a{padding:8px 16px;border-radius:999px;font-size:14px;font-weight:500;transition:background .15s;}
.desk-links a:hover{background:rgba(21,40,31,.07);}
</style>
</head>
<body>
@php
use App\Models\Category;
use App\Models\TutorProfile;
use App\Models\ExaminerClinic;
use App\Models\PlatformSetting;
$categories    = Category::active()->with('subjects')->take(8)->get();
$featuredTutors = TutorProfile::approved()->with(['user','subjects'])->orderByDesc('rating_avg')->take(6)->get();
$upcomingClinics = ExaminerClinic::upcoming()->with(['tutor','subject'])->orderBy('scheduled_at')->take(4)->get();
$prices = [20=>300,40=>500,60=>700,'60_group'=>500];
$sessionNames = [20=>'Quick Help',40=>'Guided Practice',60=>'Deep Dive','60_group'=>'Group Session'];
@endphp

{{-- ANNOUNCEMENT BAR --}}
<!-- <div x-data="{ show: true }" x-show="show" x-cloak style="background:var(--trust-blue);color:#fff;font-size:13px;padding:9px 24px;display:flex;align-items:center;justify-content:center;gap:8px;position:relative;">
    <i class="fa-solid fa-shield-check"></i>
    <span>All tutors are TSC-registered and background-checked. <strong>100% money-back guarantee</strong> on your first lesson.</span>
    <button @click="show=false" style="position:absolute;right:16px;background:none;border:none;color:rgba(255,255,255,.6);cursor:pointer;font-size:18px;line-height:1;">&times;</button>
</div> -->

@include('partials.nav')

{{-- HERO --}}
<section style="padding:80px 0 60px;overflow:hidden;position:relative;background:var(--cream);">
    <div style="position:absolute;top:-100px;right:-100px;width:500px;height:500px;background:rgba(10,100,188,.06);border-radius:50%;pointer-events:none;"></div>
    <div class="w">
        <div style="display:grid;grid-template-columns:1fr;gap:48px;align-items:center;" class="hero-g">
            <div>
                <div class="badge bv" style="margin-bottom:20px;font-size:13px;">
                    <i class="fa-solid fa-bolt"></i> Matched in seconds
                </div>
                <h1 style="font-size:clamp(40px,6vw,72px);color:var(--ink);margin-bottom:18px;letter-spacing:-.02em;">
                    Karibu Darasani.<br>
                    <span style="color:var(--trust-blue);">Learn with confidence,</span><br>every lesson.
                </h1>
                <p style="font-size:17px;color:var(--muted);margin-bottom:32px;max-width:540px;line-height:1.8;">
                    <strong style="color:var(--ink);">Instant 1-on-1 tutoring</strong> — tell us your subject and schedule. Mwalimu matches you with a vetted, TSC-registered expert teacher available right now or at your earliest convenience.
                </p>

                {{-- Quick booking CTA — always show for students/parents, hide for tutors/admins --}}
                @if(!auth()->check() || (auth()->check() && in_array(auth()->user()->role, ['student','parent'])))
                <div style="background:#fff;border-radius:20px;border:1px solid var(--hair);padding:20px;max-width:520px;margin-bottom:28px;box-shadow:0 4px 24px rgba(21,40,31,.08);">
                    <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:12px;"><i class="fa-solid fa-wand-magic-sparkles" style="color:var(--trust-blue);"></i> Book a session now</p>
                    <form action="{{ route('tutors.index') }}" method="GET" style="display:flex;gap:8px;flex-wrap:wrap;">
                        <select name="category" style="flex:1;min-width:140px;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid var(--hair);background:#fff;font-size:14px;font-family:inherit;outline:none;" onchange="this.form.submit()">
                            <option value="">Choose Subject</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('tutors.index') }}" class="btn btn-v" style="height:46px;padding:0 22px;border-radius:12px;font-size:14px;">
                            <i class="fa-solid fa-arrow-right"></i> Find Tutor
                        </a>
                    </form>
                </div>

                <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:36px;">
                    <a href="{{ route('tutors.index') }}" class="btn btn-v" style="font-size:15px;padding:14px 32px;">
                        <i class="fa-solid fa-magnifying-glass"></i> Find a Tutor
                    </a>
                    @if(!auth()->check())
                    <a href="{{ route('register') }}?role=tutor" class="btn btn-o" style="font-size:15px;padding:14px 32px;">
                        <i class="fa-solid fa-chalkboard-user"></i> Become a Tutor
                    </a>
                    @elseif(auth()->check() && auth()->user()->role === 'parent')
                    <a href="{{ route('register') }}?role=tutor" class="btn btn-o" style="font-size:15px;padding:14px 32px;">
                        <i class="fa-solid fa-chalkboard-user"></i> Become a Tutor
                    </a>
                    @endif
                </div>
                @elseif(auth()->check() && auth()->user()->role === 'admin')
                <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:36px;">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-v" style="font-size:15px;padding:14px 32px;">
                        <i class="fa-solid fa-gauge-high"></i> Admin Dashboard
                    </a>
                </div>
                @else
                <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:36px;">
                    <a href="{{ route('tutor.dashboard') }}" class="btn btn-v" style="font-size:15px;padding:14px 32px;">
                        <i class="fa-solid fa-gauge-high"></i> Go to Dashboard
                    </a>
                    {{-- <a href="{{ route('examiner.dashboard') }}" class="btn btn-o" style="font-size:15px;padding:14px 32px;">
                        <i class="fa-solid fa-chalkboard-user"></i> Examiner Dashboard
                    </a> --}}
                </div>
                @endif

                {{-- Trust stats --}}
                <div style="display:flex;flex-wrap:wrap;gap:24px;padding-top:24px;border-top:1px solid var(--hair);">
                    @foreach([
                        ['fa-certificate','TSC Registered','All tutors'],
                        ['fa-shield-halved','Background Checked','Every tutor'],
                        ['fa-rotate-left','100% Refund','First lesson'],
                        ['fa-lock','Secure Sessions','Always private'],
                    ] as [$ic,$title,$sub])
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:36px;height:36px;background:rgba(10,100,188,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid {{ $ic }}" style="color:var(--trust-blue);font-size:14px;"></i>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:700;color:var(--ink);">{{ $title }}</div>
                            <div style="font-size:11px;color:var(--muted);">{{ $sub }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Floating tutor cards --}}
            <div style="position:relative;height:400px;display:none;" class="hero-vis">
                @php
                $floatTutors=[
                    ['Dr. Amara K.','Mathematics','4.8','#0A64BC','top:5%;left:8%;transform:rotate(-3deg)','TSC Reg.'],
                    ['Prof. James M.','Physics','4.9','#10B981','top:32%;left:42%;transform:rotate(2deg)','Nat. Examiner'],
                    ['Ms. Fatuma A.','English','5.0','#E8522A','top:58%;left:5%;transform:rotate(-2deg)','TSC Reg.'],
                    ['Mr. David O.','Chemistry','4.7','#EEBE15','top:72%;left:38%;transform:rotate(3deg)','Nat. Marker'],
                ];
                @endphp
                @foreach($floatTutors as [$name,$subj,$rating,$color,$style,$badge])
                <div style="position:absolute;{{ $style }};background:#fff;border-radius:20px;border:1px solid var(--hair);padding:14px 16px;box-shadow:0 8px 28px rgba(21,40,31,.1);width:190px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                        <div style="width:40px;height:40px;border-radius:11px;background:{{ $color }};display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#fff;flex-shrink:0;">{{ substr($name,0,1) }}</div>
                        <div>
                            <div style="font-family:'Fraunces',serif;font-size:13px;font-weight:700;color:var(--ink);">{{ $name }}</div>
                            <div style="font-size:11px;color:var(--muted);">{{ $subj }}</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:12px;font-weight:600;color:var(--gold);"><i class="fa-solid fa-star fa-xs"></i> {{ $rating }}</span>
                        <span style="background:rgba(16,185,129,.1);color:var(--mint);padding:2px 8px;border-radius:999px;font-size:10px;font-weight:700;"><i class="fa-solid fa-check fa-xs"></i> {{ $badge }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- <style>.hero-g{grid-template-columns:1fr!important;}@media(min-width:1024px){.hero-g{grid-template-columns:1fr 460px!important;}.hero-vis{display:block!important;}}</style> -->
</section>

{{-- WHY MWALIMU --}}
<section style="padding:72px 0;background:var(--ink);">
    <div class="w">
        <div style="text-align:center;margin-bottom:44px;">
            <h2 style="font-size:clamp(26px,4vw,40px);color:#fff;margin-bottom:10px;">Learn with confidence, every lesson.</h2>
            <p style="color:rgba(255,255,255,.55);font-size:17px;">What makes Mwalimu different</p>
        </div>
        <div style="display:grid;grid-template-columns:1fr;gap:16px;" class="feat-g">
            @foreach([
                ['fa-user-graduate','Individual Attention','Every learner gets personal 1-on-1 attention from their tutor, greatly improving learning outcomes.','#0A64BC'],
                ['fa-shield-halved','Background-Checked Tutors','Every tutor passes verification and a background screening before they can be onboarded on Mwalimu.','#10B981'],
                ['fa-lock','Secure Classrooms','Lessons run in a secure space, assuring parents and learners of unmatched comfort and safety.','#E8522A'],
                ['fa-certificate','Verified Credentials','Subject expertise and teaching credentials are thoroughly reviewed by our academic team.','#EEBE15'],
                ['fa-user-secret','Privacy & Safety','Tutors and learners cannot chat or exchange contacts on the platform.','#3B82F6'],
                ['fa-rotate-left','100% Money-Back','Not satisfied with your first lesson? We\'ll refund it in full — no questions asked.','#EC4899'],
            ] as [$icon,$title,$desc,$color])
            <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:18px;padding:24px;transition:background .2s;" onmouseover="this.style.background='rgba(255,255,255,.12)'" onmouseout="this.style.background='rgba(255,255,255,.07)'">
                <div style="width:48px;height:48px;background:{{ $color }}33;border-radius:13px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                    <i class="fa-solid {{ $icon }}" style="color:{{ $color }};font-size:18px;"></i>
                </div>
                <h3 style="font-size:16px;color:#fff;margin-bottom:8px;">{{ $title }}</h3>
                <p style="font-size:13px;color:rgba(255,255,255,.55);line-height:1.7;">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
    <style>.feat-g{grid-template-columns:1fr!important;}@media(min-width:640px){.feat-g{grid-template-columns:repeat(2,1fr)!important;}}@media(min-width:1024px){.feat-g{grid-template-columns:repeat(3,1fr)!important;}}</style>
</section>

{{-- SESSION PRICING --}}
<section style="padding:72px 0;">
    <div class="w">
        <div style="text-align:center;margin-bottom:44px;">
            <h2 style="font-size:clamp(26px,4vw,40px);color:var(--ink);margin-bottom:10px;">Simple, fixed pricing</h2>
        </div>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;max-width:900px;margin:0 auto;" class="price-g">
            @foreach([
                ['20','Quick Help','⚡','KSh 300','Perfect for a quick question, specific topic or rapid revision drill.', false],
                ['40','Guided Practice','🎯','KSh 500','A full structured lesson with warm-up, teaching and Q&A.', false],
                ['60','Deep Dive','🏆','KSh 700','Maximum learning. Best value. Ideal for exam prep.', false],
                ['60','Group Session','👥','KSh 500','Learn together with other students. Share the cost, maximize value.', true],
            ] as [$mins,$title,$icon,$price,$desc,$isGroup])
            <div style="border:2px solid {{ $isGroup ? 'var(--hair)' : '#EEBE15' }};border-radius:20px;padding:28px;transition:all .2s;cursor:pointer;background:{{ $isGroup ? 'transparent' : 'rgba(238,190,21,.05)' }};" class="card-lift"
                 onmouseover="this.style.borderColor='{{ $isGroup ? 'var(--trust-blue)' : '#d4a800' }}'"
                 onmouseout="this.style.borderColor='{{ $isGroup ? 'var(--hair)' : '#EEBE15' }}'">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <div style="font-size:32px;">{{ $icon }}</div>
                    <span style="font-family:'Fraunces',serif;font-size:22px;font-weight:800;color:{{ $isGroup ? 'var(--trust-blue)' : '#EEBE15' }};">{{ $price }}</span>
                </div>
                <div style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);margin-bottom:4px;">{{ $mins }} min</div>
                <div style="font-size:14px;font-weight:600;color:var(--muted);margin-bottom:10px;">{{ $title }}</div>
                <p style="font-size:13px;color:var(--muted);line-height:1.65;">{{ $desc }}</p>
                @if(!auth()->check() || (auth()->check() && auth()->user()->role !== 'tutor' && auth()->user()->role !== 'admin'))
                @if($isGroup)
                <a href="{{ route('group-sessions.index') }}" style="display:inline-flex;align-items:center;gap:6px;margin-top:16px;font-size:13px;font-weight:600;color:var(--trust-blue);">
                    View sessions <i class="fa-solid fa-arrow-right fa-xs"></i>
                </a>
                @else
                <a href="{{ route('tutors.index') }}" style="display:inline-flex;align-items:center;gap:6px;margin-top:16px;font-size:13px;font-weight:600;color:#EEBE15;">
                    Book now <i class="fa-solid fa-arrow-right fa-xs"></i>
                </a>
                @endif
                @endif
            </div>
            @endforeach
        </div>
        <p style="text-align:center;font-size:13px;color:var(--muted);margin-top:20px;"><i class="fa-solid fa-circle-info fa-xs" style="color:var(--trust-blue);"></i> Booking only completes after payment is made and a session code is sent to both student and tutor.</p>
    </div>
    <style>.price-g{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:768px){.price-g{grid-template-columns:repeat(4,1fr)!important;}}</style>
</section>

{{-- HOW IT WORKS --}}
<section style="padding:72px 0;background:rgba(21,40,31,.03);">
    <div class="w">
        <div style="text-align:center;margin-bottom:44px;">
            <h2 style="font-size:clamp(26px,4vw,40px);color:var(--ink);margin-bottom:10px;">Book, pay, and learn in one safe place.</h2>
            <p style="color:var(--muted);font-size:17px;">Three steps, zero hassle</p>
        </div>
        <div style="display:grid;grid-template-columns:1fr;gap:20px;max-width:1000px;margin:0 auto;" class="steps-g">
            @foreach([
                ['1','fa-list-check','Select your session','Choose your level (JSS/SS/F3/F4), what you need help with, subject, topic, date, time, and duration. The platform shows you available, matched tutors instantly.','#0A64BC'],
                ['2','fa-mobile-screen-button','Pay via M-Pesa','Enter your phone number to prompt payment. Enter your M-Pesa PIN to complete. No account needed for learners — just your phone number.','#10B981'],
                ['3','fa-video','Join your secure session','Receive a one-time session code by SMS/email. Enter the code to access your private, secure classroom. No tutor can contact you outside the platform.','#E8522A'],
            ] as [$num,$icon,$title,$desc,$color])
            <div class="card" style="padding:28px;display:flex;gap:20px;align-items:flex-start;">
                <div style="width:52px;height:52px;background:{{ $color }};border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-solid {{ $icon }}" style="color:#fff;font-size:20px;"></i>
                </div>
                <div>
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:{{ $color }};margin-bottom:4px;">Step {{ $num }}</div>
                    <h3 style="font-size:19px;color:var(--ink);margin-bottom:8px;">{{ $title }}</h3>
                    <p style="font-size:14px;color:var(--muted);line-height:1.75;">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <div style="text-align:center;margin-top:32px;">
            <a href="{{ route('how-it-works') }}" class="btn btn-v"><i class="fa-solid fa-circle-play"></i> See Full Guide</a>
        </div>
    </div>
    <style>.steps-g{grid-template-columns:1fr!important;}@media(min-width:768px){.steps-g{grid-template-columns:repeat(3,1fr)!important;}}</style>
</section>



{{-- EXAMINER'S CLINIC --}}
<section style="padding:72px 0;background:var(--ink);">
    <div class="w">
        <div style="display:grid;grid-template-columns:1fr;gap:48px;align-items:center;" class="ec-g">
            <div>
                <div class="badge" style="background:rgba(212,160,23,.2);color:var(--gold);margin-bottom:20px;font-size:13px;">
                    <i class="fa-solid fa-star"></i> The Examiner's Clinic
                </div>
                <h2 style="font-size:clamp(28px,4vw,44px);color:#fff;margin-bottom:14px;">Learn directly from national examiners.</h2>
                <p style="font-size:16px;color:rgba(255,255,255,.65);line-height:1.8;margin-bottom:20px;">
                    Most revision resources focus on <em>what</em> to study. The Examiner's Clinic focuses on <strong style="color:#fff;">how to answer questions</strong> — taught by the very people who set, mark, and grade national exams.
                </p>
                <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:28px;">
                    @foreach(['Exam strategy & high-frequency exam questions','Examiner traps and marking scheme analysis','Common mistakes and how KNEC examiners award marks','Conducted by highest-rated National Examiners & Markers'] as $pt)
                    <div style="display:flex;align-items:flex-start;gap:10px;font-size:14px;color:rgba(255,255,255,.85);">
                        <i class="fa-solid fa-circle-check" style="color:var(--mint);margin-top:2px;flex-shrink:0;"></i>
                        {{ $pt }}
                    </div>
                    @endforeach
                </div>
                <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                    <a href="/examiner-clinic" class="btn btn-w"><i class="fa-solid fa-calendar-check"></i> View Upcoming Clinics</a>
                    <div style="color:rgba(255,255,255,.65);font-size:14px;"><span style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--gold);">KSh 500</span> /session · 60 min</div>
                </div>
            </div>
            
        </div>
    </div>
    <style>.ec-g{grid-template-columns:1fr!important;}@media(min-width:1024px){.ec-g{grid-template-columns:1fr 1fr!important;}}</style>
</section>

{{-- FOR TUTORS CTA — hidden for tutors and admins --}}
@if(!auth()->check() || (auth()->check() && !in_array(auth()->user()->role, ['tutor','admin'])))
<section style="background:var(--trust-blue);padding:72px 0;">
    <div class="w">
        <div style="display:grid;grid-template-columns:1fr;gap:48px;align-items:center;" class="tc-g">
            <div>
                <div class="badge" style="background:rgba(255,255,255,.2);color:#fff;margin-bottom:20px;"><i class="fa-solid fa-users"></i> For Tutors</div>
                <h2 style="font-size:clamp(28px,4vw,44px);color:#fff;margin-bottom:14px;">Join a verified community of Kenyan curriculum experts.</h2>
                <p style="font-size:16px;color:rgba(255,255,255,.75);line-height:1.8;margin-bottom:24px;">Register, get verified, and start earning on every completed lesson. Payments are processed instantly to your M-Pesa.</p>

                {{-- 3 Steps --}}
                <div style="display:flex;flex-direction:column;gap:14px;margin-bottom:28px;">
                    @foreach([
                        ['1','fa-pen-to-square','Register','Tell us who you are, subjects you teach, and your experience.'],
                        ['2','fa-id-card-clip','Get Verified','We check your credentials, ID, and TSC certificate — usually 72 hours, or 24 hours with Speedy Verification (KSh 1,000).'],
                        ['3','fa-money-bill-trend-up','Start Earning','Your profile goes live. Accept lesson requests and get paid per completed lesson.'],
                    ] as [$num,$icon,$title,$desc])
                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        <div style="width:36px;height:36px;background:rgba(255,255,255,.15);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid {{ $icon }}" style="color:#fff;font-size:14px;"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:14px;color:#fff;">{{ $num }}. {{ $title }}</div>
                            <div style="font-size:13px;color:rgba(255,255,255,.7);margin-top:2px;line-height:1.6;">{{ $desc }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    @if(!auth()->check() || auth()->user()->role === 'parent')
                    <a href="{{ route('register') }}?role=tutor" class="btn btn-w"><i class="fa-solid fa-chalkboard-user"></i> Register as a Tutor</a>
                    @endif
                    <a href="{{ route('how-it-works') }}" class="btn" style="background:rgba(255,255,255,.15);color:#fff;border:1.5px solid rgba(255,255,255,.3);">Learn More</a>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr;gap:12px;">
                @foreach([
                    ['fa-bolt','Weekly Payouts','Enjoy the convenience of flexible weekly payouts, issued immediately upon your request via M-Pesa. No rigid monthly waiting periods—you control when you get paid each week.'],
                    ['fa-star','Build Reputation','Featured clinic sessions put you in front of hundreds of parents & learners.'],
                    ['fa-headset','Zero Admin','Mwalimu handles student acquisition, payments, reminders and classroom setup.'],
                ] as [$icon,$title,$desc])
                <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:16px;padding:18px;">
                    <i class="fa-solid {{ $icon }}" style="color:rgba(255,255,255,.8);font-size:20px;margin-bottom:10px;display:block;"></i>
                    <div style="font-weight:700;font-size:14px;color:#fff;margin-bottom:4px;">{{ $title }}</div>
                    <div style="font-size:12px;color:rgba(255,255,255,.6);line-height:1.6;">{{ $desc }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <style>.tc-g{grid-template-columns:1fr!important;}@media(min-width:1024px){.tc-g{grid-template-columns:1fr 1fr!important;}}</style>
</section>
@endif

{{-- TESTIMONIALS --}}
<section style="padding:72px 0;">
    <div class="w">
        <div style="text-align:center;margin-bottom:44px;">
            <h2 style="font-size:clamp(26px,4vw,40px);color:var(--ink);margin-bottom:8px;">What parents & learners say</h2>
        </div>
        <div style="display:grid;grid-template-columns:1fr;gap:16px;" class="test-g">
            @foreach([
                ['G','Grace W.','Parent, Nairobi','My daughter improved from D to A in Maths in just 6 weeks. The 30-minute sessions fit perfectly after school. The tutor is TSC-registered and very professional.','after sessions with Dr. Amara'],
                ['B','Brian K.','Form 4 Student','I was panicking about KCSE Chemistry. My Mwalimu tutor broke down the marking scheme so clearly I could predict what the examiner wanted. Passed with an A.','after sessions with Mr. James'],
                ['M','Mercy N.','Parent, Mombasa','The one-time session code is genius — my son enters the code and the class opens. No sharing of numbers. No chatting outside. Very safe for a teenage learner.','after sessions with Ms. Fatuma'],
            ] as [$init,$name,$role,$quote,$note])
            <div class="card" style="padding:26px;">
                <div style="color:var(--gold);font-size:16px;letter-spacing:3px;margin-bottom:14px;"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                <p style="font-size:14px;color:var(--ink);line-height:1.85;margin-bottom:18px;font-style:italic;">"{{ $quote }}"</p>
                <div style="display:flex;align-items:center;gap:12px;padding-top:14px;border-top:1px solid var(--hair);">
                    <div style="width:38px;height:38px;border-radius:999px;background:rgba(10,100,188,.1);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--trust-blue);font-size:15px;flex-shrink:0;">{{ $init }}</div>
                    <div>
                        <div style="font-weight:700;font-size:14px;color:var(--ink);">{{ $name }}</div>
                        <div style="font-size:12px;color:var(--muted);">{{ $role }} · {{ $note }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <style>.test-g{grid-template-columns:1fr!important;}@media(min-width:768px){.test-g{grid-template-columns:repeat(3,1fr)!important;}}</style>
</section>

{{-- FINAL CTA --}}
<section style="background:var(--ink);padding:80px 0;text-align:center;">
    <div class="w">
        @if(auth()->check() && auth()->user()->role === 'tutor')
        <h2 style="font-size:clamp(28px,5vw,52px);color:#fff;margin-bottom:14px;">Welcome back, {{ auth()->user()->name }}!</h2>
        <p style="font-size:17px;color:rgba(255,255,255,.6);margin-bottom:36px;max-width:480px;margin-left:auto;margin-right:auto;">Head to your dashboard to manage sessions, view earnings, and connect with students.</p>
        <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;">
            <a href="{{ route('tutor.dashboard') }}" class="btn btn-v" style="font-size:16px;padding:15px 40px;"><i class="fa-solid fa-gauge-high"></i> Go to Dashboard</a>
            <a href="/examiner-clinic" class="btn" style="background:rgba(255,255,255,.1);color:#fff;border:2px solid rgba(255,255,255,.3);font-size:16px;padding:15px 40px;"><i class="fa-solid fa-calendar-check"></i> View Clinics</a>
        </div>
        @elseif(auth()->check() && auth()->user()->role === 'admin')
        <h2 style="font-size:clamp(28px,5vw,52px);color:#fff;margin-bottom:14px;">Welcome back, {{ auth()->user()->name }}!</h2>
        <p style="font-size:17px;color:rgba(255,255,255,.6);margin-bottom:36px;max-width:480px;margin-left:auto;margin-right:auto;">Head to the admin dashboard to manage the platform.</p>
        <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-v" style="font-size:16px;padding:15px 40px;"><i class="fa-solid fa-gauge-high"></i> Admin Dashboard</a>
        </div>
        @elseif(auth()->check() && in_array(auth()->user()->role, ['student','parent']))
        <h2 style="font-size:clamp(28px,5vw,52px);color:#fff;margin-bottom:14px;">Ready to learn?</h2>
        <p style="font-size:17px;color:rgba(255,255,255,.6);margin-bottom:36px;max-width:480px;margin-left:auto;margin-right:auto;">Find expert tutors and book your next session.</p>
        <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;">
            <a href="{{ route('tutors.index') }}" class="btn btn-v" style="font-size:16px;padding:15px 40px;"><i class="fa-solid fa-magnifying-glass"></i> Find a Tutor</a>
        </div>
        @elseif(!auth()->check())
        <h2 style="font-size:clamp(28px,5vw,52px);color:#fff;margin-bottom:14px;">Ready to learn?</h2>
        <p style="font-size:17px;color:rgba(255,255,255,.6);margin-bottom:36px;max-width:480px;margin-left:auto;margin-right:auto;">Join thousands of Kenyan students and parents who trust Mwalimu for expert, safe, verified tutoring.</p>
        <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;">
            <a href="{{ route('tutors.index') }}" class="btn btn-v" style="font-size:16px;padding:15px 40px;"><i class="fa-solid fa-magnifying-glass"></i> Find a Tutor</a>
            <a href="{{ route('register') }}?role=tutor" class="btn" style="background:rgba(255,255,255,.1);color:#fff;border:2px solid rgba(255,255,255,.3);font-size:16px;padding:15px 40px;"><i class="fa-solid fa-chalkboard-user"></i> Become a Tutor</a>
        </div>
        @endif
    </div>
</section>

<style>.blog-show-g{grid-template-columns:1fr!important;}@media(min-width:1024px){.blog-show-g{grid-template-columns:1fr 300px!important;}}</style>
@include('partials.footer')
@livewireScripts
</body>
</html>
