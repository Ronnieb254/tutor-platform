<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')

<meta charset="utf-8">
 <link rel="icon" type="image/svg+xml" href="/favicon.svg">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Mwalimu - Expert Tutors for International Curriculum</title>
<meta name="description" content="Expert tutors for Cambridge, IB, Edexcel, and American curricula. Find qualified tutors for international exams and standards. 1-on-1 sessions with verified educators.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@livewireStyles
<style>
:root{--navy:#1A2D3D;--amber:#F59E0B;--white:#FFFFFF;--cambridge:#1D4F8C;--ib:#003D7A;--edexcel:#F59E0B;--american:#1E40AF;--muted:rgba(26,45,61,.6);--light:rgba(26,45,61,.1);}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{background:var(--white);scroll-behavior:smooth;}
body{font-family:'DM Sans',sans-serif;color:var(--navy);background:var(--white);line-height:1.6;}
h1,h2,h3,h4{font-family:'Fraunces',Georgia,serif;line-height:1.1;}
a{color:inherit;text-decoration:none;}[x-cloak]{display:none!important;}
.w{max-width:1360px;margin:0 auto;padding:0 24px;}
@media(min-width:1024px){.w{padding:0 48px;}}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 28px;border-radius:999px;font-weight:600;font-size:15px;cursor:pointer;transition:all .18s;border:none;font-family:inherit;text-decoration:none;}
.btn-primary{background:var(--navy);color:#fff;}.btn-primary:hover{background:#0f1e2d;transform:translateY(-1px);box-shadow:0 6px 20px rgba(26,45,61,.3);}
.btn-secondary{background:var(--amber);color:var(--navy);font-weight:700;}.btn-secondary:hover{opacity:.9;transform:translateY(-1px);}
.btn-white{background:#fff;color:var(--navy);border:2px solid var(--navy);}.btn-white:hover{background:var(--navy);color:#fff;}
.card{background:#fff;border-radius:22px;border:1px solid var(--light);box-shadow:0 2px 16px rgba(26,45,61,.06);}
.card-lift{transition:transform .2s,box-shadow .2s;}.card-lift:hover{transform:translateY(-5px);box-shadow:0 14px 36px rgba(26,45,61,.12);}
.badge{display:inline-flex;align-items:center;gap:5px;padding:5px 13px;border-radius:999px;font-size:12px;font-weight:600;}
.badge-navy{background:rgba(26,45,61,.1);color:var(--navy);}
</style>
</head>
<body>
@php
use App\Models\Category;
use App\Models\TutorProfile;
use App\Models\ExaminerClinic;
@endphp

@include('partials.nav')

{{-- HERO --}}
<section style="padding:80px 0 60px;background:var(--white);position:relative;overflow:hidden;">
 <div style="position:absolute;top:-100px;right:-100px;width:500px;height:500px;background:rgba(26,45,61,.04);border-radius:50%;pointer-events:none;"></div>
 <div class="w">
 <div style="display:grid;grid-template-columns:1fr;gap:48px;align-items:center;max-width:1200px;" class="hero-intl">
 
 {{-- Left: Headline + Tagline --}}
 <div>
 <div class="badge badge-navy" style="margin-bottom:20px;font-size:13px;">
 <i class="fa-solid fa-globe"></i> International Curriculum
 </div>
 <h1 style="font-size:clamp(40px,6vw,72px);color:var(--navy);margin-bottom:18px;letter-spacing:-.02em;font-family:'Fraunces',Georgia,serif;font-weight:800;">
 Expert tutors for international curriculum.
 </h1>
 <p style="font-size:17px;color:var(--muted);margin-bottom:32px;max-width:540px;line-height:1.8;">
 Connect with qualified tutors specializing in Cambridge, IB, Edexcel, and American curricula. Get 1-on-1 support for exams and coursework.
 </p>
 <div style="display:flex;flex-wrap:wrap;gap:12px;">
 <a href="{{ route('tutors.index') }}?curriculum=international" class="btn btn-primary">
 <i class="fa-solid fa-magnifying-glass"></i> Find an international tutor
 </a>
 <a href="#exam-boards" class="btn btn-white">
 <i class="fa-solid fa-graduation-cap"></i> Explore exam boards
 </a>
 </div>
 </div>

 {{-- Right: Find Your Tutor Panel --}}
 <div style="background:#fff;border-radius:20px;border:1px solid var(--light);padding:32px;box-shadow:0 8px 32px rgba(26,45,61,.1);max-width:480px;width:100%;">
 <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:18px;"><i class="fa-solid fa-sparkles fa-xs"></i> Find your tutor</p>
 <form action="{{ route('tutors.index') }}" method="GET" style="display:flex;flex-direction:column;gap:12px;">
 <div>
 <label style="display:block;font-size:13px;font-weight:600;color:var(--navy);margin-bottom:6px;">Curriculum</label>
 <select name="curriculum" style="width:100%;height:44px;padding:0 12px;border-radius:12px;border:1.5px solid var(--light);background:#fff;font-size:14px;font-family:inherit;outline:none;color:var(--navy);" required>
 <option value="international" selected>International (IGCSE, IB, Edexcel, AP)</option>
 </select>
 </div>
 <div>
 <label style="display:block;font-size:13px;font-weight:600;color:var(--navy);margin-bottom:6px;">Level</label>
 <select name="level" style="width:100%;height:44px;padding:0 12px;border-radius:12px;border:1.5px solid var(--light);background:#fff;font-size:14px;font-family:inherit;outline:none;color:var(--navy);">
 <option value="">All levels</option>
 <option value="primary">Primary</option>
 <option value="lower">Lower Secondary (Ages 11-14)</option>
 <option value="upper">Upper Secondary (Ages 14-16)</option>
 <option value="advanced">Advanced (Ages 16-18)</option>
 </select>
 </div>
 <div>
 <label style="display:block;font-size:13px;font-weight:600;color:var(--navy);margin-bottom:6px;">Subject</label>
 <select name="subject" style="width:100%;height:44px;padding:0 12px;border-radius:12px;border:1.5px solid var(--light);background:#fff;font-size:14px;font-family:inherit;outline:none;color:var(--navy);">
 <option value="">All subjects</option>
 <option value="Mathematics">Mathematics</option>
 <option value="Physics">Physics</option>
 <option value="Chemistry">Chemistry</option>
 <option value="Biology">Biology</option>
 <option value="English">English</option>
 <option value="Business Studies">Business Studies</option>
 <option value="Economics">Economics</option>
 <option value="Computer Science">Computer Science</option>
 <option value="French">French</option>
 <option value="History">History</option>
 <option value="Geography">Geography</option>
 </select>
 </div>
 <button type="submit" class="btn btn-primary" style="width:100%;margin-top:12px;">
 <i class="fa-solid fa-arrow-right"></i> Find tutors
 </button>
 </form>
 </div>

 </div>
 </div>
</section>

{{-- EXAM BOARDS GRID --}}
<section id="exam-boards" style="padding:72px 0;background:rgba(26,45,61,.02);">
 <div class="w">
 <div style="text-align:center;margin-bottom:44px;">
 <h2 style="font-size:clamp(28px,4vw,44px);color:var(--navy);margin-bottom:12px;font-family:'Fraunces',Georgia,serif;font-weight:800;">Browse by exam board</h2>
 <p style="font-size:16px;color:var(--muted);max-width:600px;margin:0 auto;">Find expert tutors for your curriculum. Each board offers specialized support for your level and subjects.</p>
 </div>
 
 <div style="display:grid;grid-template-columns:1fr;gap:20px;max-width:1200px;margin:0 auto;" class="exam-boards-grid">
 {{-- Cambridge --}}
 <div style="background:#fff;border-radius:20px;border:2px solid var(--light);padding:28px;transition:all .2s;" onmouseover="this.style.borderColor='var(--cambridge)';this.style.boxShadow='0 8px 24px rgba(29,79,140,.15)'" onmouseout="this.style.borderColor='var(--light)';this.style.boxShadow=''">
 <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
 <div style="width:48px;height:48px;background:var(--cambridge);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:#fff;">C</div>
 <div>
 <h3 style="font-size:20px;font-weight:800;color:var(--navy);font-family:'Fraunces',Georgia,serif;">Cambridge</h3>
 <p style="font-size:13px;color:var(--muted);">CAIE International</p>
 </div>
 </div>
 <p style="font-size:14px;color:var(--muted);margin-bottom:14px;line-height:1.6;">Expert support for Cambridge IGCSE, O Level, AS and A Level exams.</p>
 <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:18px;">
 <span style="background:rgba(29,79,140,.1);color:var(--cambridge);padding:5px 12px;border-radius:999px;font-size:12px;font-weight:600;">Primary</span>
 <span style="background:rgba(29,79,140,.1);color:var(--cambridge);padding:5px 12px;border-radius:999px;font-size:12px;font-weight:600;">Lower Secondary</span>
 <span style="background:rgba(29,79,140,.1);color:var(--cambridge);padding:5px 12px;border-radius:999px;font-size:12px;font-weight:600;">IGCSE</span>
 <span style="background:rgba(29,79,140,.1);color:var(--cambridge);padding:5px 12px;border-radius:999px;font-size:12px;font-weight:600;">AS & A Level</span>
 </div>
 <div style="display:flex;gap:10px;">
 <a href="{{ route('tutors.index') }}?exam_board=cambridge" class="btn btn-primary" style="flex:1;font-size:14px;padding:11px 16px;">Browse Cambridge tutors</a>
 <a href="#" class="btn" style="flex:0;padding:11px 16px;border:2px solid var(--cambridge);color:var(--cambridge);background:transparent;font-weight:600;font-size:14px;">Waitlist</a>
 </div>
 </div>

 {{-- IB --}}
 <div style="background:#fff;border-radius:20px;border:2px solid var(--light);padding:28px;transition:all .2s;" onmouseover="this.style.borderColor='var(--ib)';this.style.boxShadow='0 8px 24px rgba(0,61,122,.15)'" onmouseout="this.style.borderColor='var(--light)';this.style.boxShadow=''">
 <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
 <div style="width:48px;height:48px;background:var(--ib);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:#fff;">IB</div>
 <div>
 <h3 style="font-size:20px;font-weight:800;color:var(--navy);font-family:'Fraunces',Georgia,serif;">International Baccalaureate</h3>
 <p style="font-size:13px;color:var(--muted);">IB World Schools</p>
 </div>
 </div>
 <p style="font-size:14px;color:var(--muted);margin-bottom:14px;line-height:1.6;">Rigorous support for IB PYP, MYP, Diploma, and extended essay.</p>
 <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:18px;">
 <span style="background:rgba(0,61,122,.1);color:var(--ib);padding:5px 12px;border-radius:999px;font-size:12px;font-weight:600;">PYP (Ages 3-12)</span>
 <span style="background:rgba(0,61,122,.1);color:var(--ib);padding:5px 12px;border-radius:999px;font-size:12px;font-weight:600;">MYP (Ages 11-16)</span>
 <span style="background:rgba(0,61,122,.1);color:var(--ib);padding:5px 12px;border-radius:999px;font-size:12px;font-weight:600;">DP & EE</span>
 <span style="background:rgba(0,61,122,.1);color:var(--ib);padding:5px 12px;border-radius:999px;font-size:12px;font-weight:600;">Support</span>
 </div>
 <div style="display:flex;gap:10px;">
 <a href="{{ route('tutors.index') }}?exam_board=ib" class="btn btn-primary" style="flex:1;font-size:14px;padding:11px 16px;">Browse IB tutors</a>
 <a href="#" class="btn" style="flex:0;padding:11px 16px;border:2px solid var(--ib);color:var(--ib);background:transparent;font-weight:600;font-size:14px;">Waitlist</a>
 </div>
 </div>

 {{-- Edexcel --}}
 <div style="background:#fff;border-radius:20px;border:2px solid var(--light);padding:28px;transition:all .2s;" onmouseover="this.style.borderColor='var(--edexcel)';this.style.boxShadow='0 8px 24px rgba(245,158,15,.15)'" onmouseout="this.style.borderColor='var(--light)';this.style.boxShadow=''">
 <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
 <div style="width:48px;height:48px;background:var(--edexcel);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:var(--navy);">E</div>
 <div>
 <h3 style="font-size:20px;font-weight:800;color:var(--navy);font-family:'Fraunces',Georgia,serif;">Edexcel/Pearson</h3>
 <p style="font-size:13px;color:var(--muted);">International GCSE & A Level</p>
 </div>
 </div>
 <p style="font-size:14px;color:var(--muted);margin-bottom:14px;line-height:1.6;">Complete preparation for International GCSE and International A Level exams.</p>
 <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:18px;">
 <span style="background:rgba(245,158,15,.1);color:var(--edexcel);padding:5px 12px;border-radius:999px;font-size:12px;font-weight:600;">IGCSE (Grades 9-11)</span>
 <span style="background:rgba(245,158,15,.1);color:var(--edexcel);padding:5px 12px;border-radius:999px;font-size:12px;font-weight:600;">A Level (Grades 12+)</span>
 <span style="background:rgba(245,158,15,.1);color:var(--edexcel);padding:5px 12px;border-radius:999px;font-size:12px;font-weight:600;">Past Papers</span>
 <span style="background:rgba(245,158,15,.1);color:var(--edexcel);padding:5px 12px;border-radius:999px;font-size:12px;font-weight:600;">Library</span>
 </div>
 <div style="display:flex;gap:10px;">
 <a href="{{ route('tutors.index') }}?exam_board=edexcel" class="btn btn-primary" style="flex:1;font-size:14px;padding:11px 16px;">Browse Edexcel tutors</a>
 <a href="#" class="btn" style="flex:0;padding:11px 16px;border:2px solid var(--edexcel);color:var(--edexcel);background:transparent;font-weight:600;font-size:14px;">Waitlist</a>
 </div>
 </div>

 {{-- American --}}
 <div style="background:#fff;border-radius:20px;border:2px solid var(--light);padding:28px;transition:all .2s;" onmouseover="this.style.borderColor='var(--american)';this.style.boxShadow='0 8px 24px rgba(30,64,175,.15)'" onmouseout="this.style.borderColor='var(--light)';this.style.boxShadow=''">
 <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
 <div style="width:48px;height:48px;background:var(--american);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:#fff;">US</div>
 <div>
 <h3 style="font-size:20px;font-weight:800;color:var(--navy);font-family:'Fraunces',Georgia,serif;">American System</h3>
 <p style="font-size:13px;color:var(--muted);">US High School & College Prep</p>
 </div>
 </div>
 <p style="font-size:14px;color:var(--muted);margin-bottom:14px;line-height:1.6;">Comprehensive support for American high school, AP, SAT, and ACT preparation.</p>
 <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:18px;">
 <span style="background:rgba(30,64,175,.1);color:var(--american);padding:5px 12px;border-radius:999px;font-size:12px;font-weight:600;">High School (Grades 9-12)</span>
 <span style="background:rgba(30,64,175,.1);color:var(--american);padding:5px 12px;border-radius:999px;font-size:12px;font-weight:600;">AP</span>
 <span style="background:rgba(30,64,175,.1);color:var(--american);padding:5px 12px;border-radius:999px;font-size:12px;font-weight:600;">SAT & ACT</span>
 <span style="background:rgba(30,64,175,.1);color:var(--american);padding:5px 12px;border-radius:999px;font-size:12px;font-weight:600;">Support</span>
 </div>
 <div style="display:flex;gap:10px;">
 <a href="{{ route('tutors.index') }}?exam_board=american" class="btn btn-primary" style="flex:1;font-size:14px;padding:11px 16px;">Browse American tutors</a>
 <a href="#" class="btn" style="flex:0;padding:11px 16px;border:2px solid var(--american);color:var(--american);background:transparent;font-weight:600;font-size:14px;">Waitlist</a>
 </div>
 </div>

 </div>
 <style>
 .exam-boards-grid { grid-template-columns: 1fr !important; }
 @media(min-width:640px) { .exam-boards-grid { grid-template-columns: repeat(2, 1fr) !important; } }
 @media(min-width:1024px) { .exam-boards-grid { grid-template-columns: repeat(4, 1fr) !important; } }
 </style>
 </div>
</section>

{{-- SUBJECT BROWSE --}}
<section style="padding:72px 0;background:#fff;">
 <div class="w">
 <div style="text-align:center;margin-bottom:40px;">
 <h2 style="font-size:clamp(28px,4vw,44px);color:var(--navy);margin-bottom:8px;font-family:'Fraunces',Georgia,serif;font-weight:800;">Browse by subject</h2>
 <p style="font-size:16px;color:var(--muted);max-width:600px;margin:0 auto;">Find tutors specialized in your subject. All international exam boards covered.</p>
 </div>
 
 <div style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center;max-width:1000px;margin:0 auto;">
 @foreach(['Mathematics','Physics','Chemistry','Biology','English','Business Studies','Economics','Computer Science','French','History','Geography'] as $subject)
 <a href="{{ route('tutors.index') }}?subject={{ urlencode($subject) }}&curriculum=international"
 style="background:var(--navy);color:#fff;padding:12px 24px;border-radius:999px;font-size:14px;font-weight:600;text-decoration:none;transition:all .2s;display:flex;align-items:center;gap:8px;border:2px solid var(--navy);"
 onmouseover="this.style.background='transparent';this.style.color='var(--navy)'"
 onmouseout="this.style.background='var(--navy)';this.style.color='#fff'">
 <i class="fa-solid fa-book"></i> {{ $subject }}
 </a>
 @endforeach
 </div>
 </div>
</section>

{{-- FEATURES SECTION (Dark Navy) --}}
<section style="padding:72px 0;background:var(--navy);">
 <div class="w">
 <div style="display:grid;grid-template-columns:1fr;gap:20px;max-width:1100px;margin:0 auto;" class="features-grid">
 @foreach([
 ['fa-certificate','Exam Board Expertise','Expert tutors certified in Cambridge, IB, Edexcel, and American curricula.'],
 ['fa-file-pdf','Past Papers & Mark Schemes','Access comprehensive past paper libraries with detailed mark schemes.'],
 ['fa-bolt','Exam-Series Intensives','Intensive sessions focused on exam technique and high-frequency questions.'],
 ['fa-dollar-sign','Clear Pricing','Transparent pricing in KES or USD. No hidden fees, no surprises.'],
 ] as [$icon,$title,$desc])
 <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:18px;padding:28px;transition:background .2s;"
 onmouseover="this.style.background='rgba(255,255,255,.12)'" onmouseout="this.style.background='rgba(255,255,255,.08)'">
 <div style="width:48px;height:48px;background:var(--amber);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;flex-shrink:0;">
 <i class="fa-solid {{ $icon }}" style="font-size:22px;color:var(--navy);"></i>
 </div>
 <h3 style="font-size:16px;font-weight:800;color:#fff;margin-bottom:8px;font-family:'Fraunces',Georgia,serif;">{{ $title }}</h3>
 <p style="font-size:13px;color:rgba(255,255,255,.65);line-height:1.7;">{{ $desc }}</p>
 </div>
 @endforeach
 </div>
 <style>
 .features-grid { grid-template-columns: 1fr !important; }
 @media(min-width:640px) { .features-grid { grid-template-columns: repeat(2, 1fr) !important; } }
 @media(min-width:1024px) { .features-grid { grid-template-columns: repeat(4, 1fr) !important; } }
 </style>
 </div>
</section>

{{-- TRANSITION BANNER --}}
<section style="padding:60px 0;background:rgba(26,45,61,.02);">
 <div class="w">
 <div style="background:var(--navy);border-radius:24px;padding:40px;text-align:center;border:2px solid var(--amber);">
 <div class="badge badge-navy" style="background:rgba(245,158,15,.2);color:var(--amber);margin-bottom:16px;width:fit-content;margin-left:auto;margin-right:auto;"><i class="fa-solid fa-exchange"></i> Curriculum Transition</div>
 <h2 style="font-size:clamp(24px,4vw,38px);color:#fff;margin-bottom:14px;font-family:'Fraunces',Georgia,serif;font-weight:800;">Switching from CBC to IGCSE?</h2>
 <p style="font-size:15px;color:rgba(255,255,255,.75);margin-bottom:24px;max-width:700px;margin-left:auto;margin-right:auto;line-height:1.8;">
 Moving to an international curriculum can feel overwhelming. Our specialized tutors understand the transition, the content gaps, and exactly how to bridge them quickly. Get expert guidance tailored to your background.
 </p>
 <a href="#" class="btn btn-secondary"><i class="fa-solid fa-arrow-right"></i> See the transition guide</a>
 </div>
 </div>
</section>

@include('partials.footer')

</body>
</html>

<style>
@media(min-width:1024px){
 .hero-intl{grid-template-columns:1fr 480px!important;grid-auto-flow:column;}
}
</style>

@livewireScripts
