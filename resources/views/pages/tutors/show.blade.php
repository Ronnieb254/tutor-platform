<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>{{ $tutor->user->name }} - Verified Tutor | Mwalimu</title>
<meta name="description" content="{{ Str::limit($tutor->bio ?? 'Book a session with '.$tutor->user->name.' on Mwalimu.', 160) }}">
<style>
/*  Page-level styles  */
.profile-hero{background:linear-gradient(135deg,#111C16 0%,#15281F 60%,#1a2e22 100%);padding:56px 0 0;position:relative;overflow:hidden;}
.profile-hero::before{content:'';position:absolute;top:-120px;right:-80px;width:400px;height:400px;background:rgba(10,100,188,.15);border-radius:50%;pointer-events:none;}
.profile-hero::after{content:'';position:absolute;bottom:-60px;left:20%;width:200px;height:200px;background:rgba(212,160,23,.08);border-radius:50%;pointer-events:none;}

/* Tabs */
.ptab{padding:16px 24px;font-size:15px;font-weight:600;background:none;border:none;cursor:pointer;color:rgba(255,255,255,.5);border-bottom:3px solid transparent;transition:all .25s;font-family:inherit;white-space:nowrap;}
.ptab.on,.ptab:hover{color:#fff;border-bottom-color:#0A64BC;}
.ptab:hover{color:#fff;}

/* Session tier cards */
.tier-card{border:2px solid rgba(21,40,31,.15);border-radius:18px;padding:20px;text-align:left;cursor:pointer;transition:all .3s ease;background:#fff;display:block;width:100%;box-shadow:0 2px 8px rgba(21,40,31,.04);}
.tier-card:hover{border-color:#0A64BC;background:rgba(10,100,188,.04);box-shadow:0 8px 24px rgba(10,100,188,.12);transform:translateY(-2px);}
.tier-card.selected{border-color:#0A64BC;background:rgba(10,100,188,.08);box-shadow:0 8px 24px rgba(10,100,188,.15);}

/* Review rating bars */
.rbar-track{height:8px;background:#F4F6F4;border-radius:999px;overflow:hidden;flex:1;}
.rbar-fill{height:100%;background:#EEBE15;border-radius:999px;transition:width .5s ease;}

/* Booking steps */
.bstep{display:flex;flex-direction:column;gap:4px;align-items:center;}
.bstep-dot{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;transition:all .3s;}

/* Payment form */
.pay-field{width:100%;height:48px;padding:0 16px;border-radius:14px;border:1.5px solid rgba(21,40,31,.15);background:#fff;font-size:15px;font-family:inherit;color:#15281F;outline:none;transition:all .2s;}
.pay-field:focus{border-color:#0A64BC;box-shadow:0 0 0 3px rgba(10,100,188,.12);background:#fff;}

/* Professional card styling */
.prof-card{background:#fff;border-radius:18px;border:1px solid rgba(21,40,31,.08);padding:28px;box-shadow:0 2px 12px rgba(21,40,31,.05);transition:all .3s;}
.prof-card:hover{box-shadow:0 8px 28px rgba(21,40,31,.12);}

/* Professional button */
.prof-btn{border-radius:14px;color:#fff;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .3s;display:flex;align-items:center;justify-content:center;gap:12px;}
.prof-btn:hover{transform:translateY(-2px);}

/* Stat cards enhanced */
.stat-card{background:linear-gradient(135deg,rgba(255,255,255,.95),#fff);border-radius:16px;border:1px solid rgba(21,40,31,.08);padding:28px;display:flex;align-items:center;gap:18px;box-shadow:0 4px 16px rgba(21,40,31,.06);transition:all .3s;}
.stat-card:hover{box-shadow:0 8px 32px rgba(21,40,31,.12);transform:translateY(-2px);}

/* Content spacing */
.content-spacing{gap:28px;}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
</head>
<body style="display:flex;flex-direction:column;min-height:100vh;margin:0;padding:0;">
@php
$prices = [20=>300,40=>600,60=>900];
$sessNames = [20=>'Quick Help',40=>'Guided Practice',60=>'Deep Dive'];
$sessDesc = [20=>'Rapid revision or a quick question',40=>'Full structured lesson with Q&A',60=>'Maximum learning, best value'];
// No free first session - 100% money-back guarantee if unsatisfied
$isFreeFirstSession = false;
$colors = ['#0A64BC','#10B981','#E8522A','#EEBE15','#3B82F6'];
$avColor = $colors[$tutor->id % count($colors)];
$reviews = $tutor->reviews()->where('is_visible',true)->with('student')->latest()->paginate(8);
@endphp

@include('partials.nav')

{{--  PROFILE HERO  --}}
<div class="profile-hero">
 <div style="max-width:1360px;margin:0 auto;padding:0 24px;">

 {{-- Breadcrumb --}}
 <div style="margin-bottom:20px;">
 <a href="{{ route('tutors.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:rgba(255,255,255,.5);text-decoration:none;transition:color .15s;" onmouseover="this.style.color='rgba(255,255,255,.85)'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
 <span style="font-size:inherit;"></span> All Tutors
 </a>
 </div>

 {{-- Profile header --}}
 <div style="display:flex;align-items:flex-start;gap:22px;flex-wrap:wrap;">
 {{-- Avatar --}}
 <div style="width:96px;height:96px;border-radius:22px;background:{{ $avColor }};display:flex;align-items:center;justify-content:center;font-family:'Fraunces',serif;font-size:38px;font-weight:700;color:#fff;flex-shrink:0;border:3px solid rgba(255,255,255,.15);">
 {{ strtoupper(substr($tutor->user->name,0,1)) }}
 </div>

 {{-- Info --}}
 <div style="flex:1;min-width:0;">
 <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:8px;">
 <h1 style="font-family:'Fraunces',serif;font-size:clamp(24px,4vw,38px);font-weight:800;color:#fff;line-height:1.1;">{{ $tutor->user->name }}</h1>
 <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(16,185,129,.2);color:#4ADE80;border:1px solid rgba(16,185,129,.3);padding:4px 12px;border-radius:999px;font-size:12px;font-weight:700;">
 <i class="fa-solid fa-scroll"></i> TSC Verified
 </span>
 @if($tutor->is_tsc_registered ?? false)
 <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(212,160,23,.2);color:#FCD34D;border:1px solid rgba(212,160,23,.25);padding:4px 12px;border-radius:999px;font-size:12px;font-weight:700;">
 <i class="fa-solid fa-star"></i> National Examiner
 </span>
 @endif
 </div>

 {{-- Subjects --}}
 <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px;">
 @foreach($tutor->subjects as $s)
 <span style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.85);padding:4px 13px;border-radius:999px;font-size:12px;font-weight:500;border:1px solid rgba(255,255,255,.12);">{{ $s->name }}</span>
 @endforeach
 </div>

 {{-- Stats row --}}
 <div style="display:flex;flex-wrap:wrap;gap:20px;font-size:14px;color:rgba(255,255,255,.65);">
 <span style="display:flex;align-items:center;gap:5px;">
 <i class="fa-solid fa-star"></i>
 <strong style="color:#fff;">{{ number_format($tutor->rating_avg,1) }}</strong>
 <span>({{ $tutor->total_reviews }} reviews)</span>
 </span>
 <span style="display:flex;align-items:center;gap:5px;">
 <i class="fa-solid fa-circle-info"></i>
 <strong style="color:#fff;">{{ number_format($tutor->total_sessions) }}</strong> sessions
 </span>
 <!-- @if($tutor->city)
 <span style="display:flex;align-items:center;gap:5px;">
 <i class="fa-solid fa-location-dot"></i>
 {{ $tutor->city }}, Kenya
 </span>
 @endif -->
 <span style="display:flex;align-items:center;gap:5px;">
 <i class="fa-solid fa-clock"></i>
 Responds within 2hr
 </span>
 </div>
 </div>

 {{-- Desktop price badge --}}
 <div style="display:none;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0;" class="desk-price">
 <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:14px;padding:12px 18px;text-align:center;">
 <div style="font-size:11px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px;">From</div>
 <div style="font-family:'Fraunces',serif;font-size:28px;font-weight:800;color:#EEBE15;">KSh 300</div>
 <div style="font-size:11px;color:rgba(255,255,255,.4);">per session</div>
 </div>
 </div>
 </div>

 {{-- Tab bar --}}
 <div style="overflow-x:auto;display:flex;-webkit-overflow-scrolling:touch;">
 <div style="display:flex;gap:0;margin-top:28px;" x-data="">
 <button class="ptab on" onclick="showTab('about',this)">
 <i class="fa-solid fa-user"></i>  About
 </button>
  <button class="ptab" onclick="showTab('book',this)" id="book-tab-btn" style="color:#EEBE15;border-bottom-color:#EEBE15;">
 <i class="fa-solid fa-circle-check"></i>  Book Session
 </button>
 <button class="ptab" onclick="showTab('availability',this)">
 <i class="fa-solid fa-calendar"></i>  Availability
 </button>
 <button class="ptab" onclick="showTab('reviews',this)">
 <i class="fa-solid fa-star"></i>  Reviews ({{ $tutor->total_reviews }})
 </button>

 </div>
 </div>
 </div>
</div>

{{--  MAIN CONTENT  --}}
<div style="max-width:1360px;margin:0 auto;padding:32px 24px 64px;flex:1;width:100%;" class="w">
 <div style="display:grid;grid-template-columns:1fr;gap:24px;" class="show-grid session-layout">

 {{-- LEFT: Tab content --}}
 <div>

 {{--  ABOUT TAB  --}}
 <div id="tab-about">
 {{-- Book Session Button --}}
 @if(!auth()->check() || (auth()->check() && !in_array(auth()->user()->role, ['tutor','admin'])))
 <button onclick="document.getElementById('book-tab-btn').click();document.getElementById('tab-book').scrollIntoView({behavior:'smooth'})"
 style="width:100%;height:88px;background:linear-gradient(135deg,#0A64BC 0%,#0052A3 100%);color:#fff;font-size:22px;font-weight:800;border:none;cursor:pointer;font-family:inherit;transition:all .3s;display:flex;align-items:center;justify-content:center;gap:14px;border-radius:16px;box-shadow:0 10px 36px rgba(10,100,188,.4);margin-bottom:40px;border:3px solid #0A64BC;"
 onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 48px rgba(10,100,188,.5)'" 
 onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 10px 36px rgba(10,100,188,.4)'">
 <i class="fa-solid fa-calendar-check" style="font-size:28px;"></i> Book a Session Now
 </button>
 @endif

 {{-- Stats cards --}}
 <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-bottom:32px;" class="stat-mini-g content-spacing">
 @foreach([
 ['fa-chalkboard','#0A64BC',number_format($tutor->total_sessions),'Sessions Completed'],
 ['fa-star','#EEBE15',$tutor->total_reviews,'Total Reviews'],
 ['fa-reply','#E8522A','<2hr','Response Time'],
 ] as [$ic,$col,$val,$lbl])
 <div class="stat-card" style="background:linear-gradient(135deg,rgba(255,255,255,.95),#fff);">
 <div style="width:56px;height:56px;border-radius:14px;background:{{ $col }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:26px;color:#fff;box-shadow:0 4px 12px {{ $col }}40;">
 <i class="fa-solid {{ $ic }}"></i>
 </div>
 <div style="flex:1;">
 <div style="font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:#15281F;line-height:1;">{!! $val !!}</div>
 <div style="font-size:13px;color:rgba(21,40,31,.6);margin-top:4px;font-weight:500;">{{ $lbl }}</div>
 </div>
 </div>
 @endforeach
 </div>
 <style>.stat-mini-g{grid-template-columns:repeat(1,1fr)!important;}@media(min-width:640px){.stat-mini-g{grid-template-columns:repeat(3,1fr)!important;}}@media(min-width:1024px){.stat-mini-g{grid-template-columns:repeat(3,1fr)!important;}}</style>

 {{-- Bio --}}
 <div class="prof-card" style="margin-bottom:32px;">
 <h2 style="font-family:'Fraunces',serif;font-size:22px;color:#15281F;margin:0 0 16px;display:flex;align-items:center;gap:10px;">
 <i class="fa-solid fa-user" style="color:#0A64BC;"></i>
 About {{ explode(' ',$tutor->user->name)[0] }}
 </h2>
 <p style="font-size:16px;color:#15281F;line-height:1.9;margin:0;">{{ $tutor->bio ?? 'This tutor hasn\'t added a bio yet.' }}</p>

 @if($tutor->languages)
 <div style="margin-top:20px;padding-top:18px;border-top:1px solid rgba(21,40,31,.08);">
 <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(21,40,31,.45);margin-bottom:10px;">Languages</p>
 <div style="display:flex;flex-wrap:wrap;gap:6px;">
 @foreach($tutor->languages as $lang)
 <span style="background:#F4F6F4;color:#15281F;padding:5px 14px;border-radius:999px;font-size:13px;font-weight:500;">{{ $lang }}</span>
 @endforeach
 </div>
 </div>
 @endif
 </div>

 {{-- Subjects --}}
 <div class="prof-card">
 <h3 style="font-family:'Fraunces',serif;font-size:20px;color:#15281F;margin:0 0 20px;display:flex;align-items:center;gap:10px;">
 <i class="fa-solid fa-book-open" style="color:#10B981;"></i> Subjects Taught
 </h3>
 <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;" class="subj-g">
 @foreach($tutor->subjects as $s)
 <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#F9FAF9;border-radius:12px;border:1px solid rgba(21,40,31,.07);">
 <span style="width:8px;height:8px;border-radius:50%;background:#0A64BC;flex-shrink:0;"></span>
 <span style="font-size:14px;font-weight:500;color:#15281F;">{{ $s->name }}</span>
 @if($s->category)<span style="font-size:11px;color:rgba(21,40,31,.4);margin-left:auto;">{{ $s->category->name }}</span>@endif
 </div>
 @endforeach
 </div>
 <style>.subj-g{grid-template-columns:1fr!important;}@media(min-width:640px){.subj-g{grid-template-columns:repeat(2,1fr)!important;}}</style>
 </div>
 </div>

 {{--  AVAILABILITY TAB  --}}
 <div id="tab-availability" style="display:none;">
 <div class="prof-card">
 <h2 style="font-family:'Fraunces',serif;font-size:22px;color:#15281F;margin:0 0 8px;display:flex;align-items:center;gap:10px;">
 <i class="fa-solid fa-calendar" style="color:#E8522A;"></i> Weekly Schedule
 </h2>
 <p style="font-size:14px;color:rgba(21,40,31,.55);margin:0 0 24px;">Nairobi, Kenya Time (EAT – UTC+3)</p>
 @php
 $avails = $tutor->availabilities()->where('is_active',true)->orderBy('day_of_week')->get();
 $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
 @endphp
 @if($avails->isEmpty())
 <div style="text-align:center;padding:40px;color:rgba(21,40,31,.45);">
 <i class="fa-solid fa-circle-xmark"></i>
 <p style="font-size:14px;">Availability not set. Book anyway - the tutor will confirm a time that works.</p>
 </div>
 @else
 @foreach($days as $i => $day)
 @php $av = $avails->firstWhere('day_of_week',$i); @endphp
 <div style="display:flex;align-items:center;gap:16px;padding:14px 16px;border-radius:13px;margin-bottom:7px;background:{{ $av ? 'rgba(10,100,188,.04)' : '#FAFCFA' }};border:1px solid {{ $av ? 'rgba(10,100,188,.15)' : 'rgba(21,40,31,.07)' }};">
 <span style="font-weight:600;font-size:14px;color:#15281F;width:96px;flex-shrink:0;">{{ $day }}</span>
 @if($av)
 <span style="font-size:14px;font-weight:500;color:#0A64BC;display:flex;align-items:center;gap:6px;">
 <i class="fa-solid fa-clock"></i>
 {{ substr($av->start_time,0,5) }} - {{ substr($av->end_time,0,5) }}
 </span>
 <span style="margin-left:auto;background:rgba(16,185,129,.1);color:#10B981;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;display:flex;align-items:center;gap:4px;">
 <i class="fa-solid fa-circle-check"></i> Available
 </span>
 @else
 <span style="font-size:13px;color:rgba(21,40,31,.4);">Not available</span>
 <span style="margin-left:auto;background:#F3F4F6;color:#9CA3AF;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;">Off</span>
 @endif
 </div>
 @endforeach
 @endif
 </div>
 </div>

 {{--  REVIEWS TAB  --}}
 <div id="tab-reviews" style="display:none;">
 {{-- Rating overview --}}
 <div class="prof-card" style="margin-bottom:24px;">
 <div style="display:flex;align-items:center;gap:32px;flex-wrap:wrap;">
 <div style="text-align:center;flex-shrink:0;">
 <div style="font-family:'Fraunces',serif;font-size:64px;font-weight:800;color:#15281F;line-height:1;">{{ number_format($tutor->rating_avg,1) }}</div>
 <div style="display:flex;gap:3px;justify-content:center;margin:8px 0;">
 @for($i=1;$i<=5;$i++)
 <i class="fa-solid fa-star" style="color:{{ $i<=$tutor->rating_avg ? '#EEBE15' : '#E5E7EB' }};font-size:18px;"></i>
 @endfor
 </div>
 <div style="font-size:13px;color:rgba(21,40,31,.5);">{{ $tutor->total_reviews }} reviews</div>
 </div>
 <div style="flex:1;min-width:200px;">
 @foreach([5,4,3,2,1] as $star)
 @php $cnt=$tutor->reviews()->where('rating',$star)->count(); $pct=$tutor->total_reviews>0?round($cnt/$tutor->total_reviews*100):0; @endphp
 <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
 <span style="font-size:13px;color:rgba(21,40,31,.5);width:12px;text-align:right;">{{ $star }}</span>
 <i class="fa-solid fa-star"></i>
 <div class="rbar-track"><div class="rbar-fill" style="width:{{ $pct }}%;"></div></div>
 <span style="font-size:12px;color:rgba(21,40,31,.4);width:20px;">{{ $cnt }}</span>
 </div>
 @endforeach
 </div>
 </div>
 </div>

 {{-- Review list --}}
 @forelse($reviews as $review)
 <div class="prof-card" style="margin-bottom:16px;">
 <div style="display:flex;align-items:flex-start;gap:14px;">
 <div style="width:40px;height:40px;border-radius:12px;background:#{{ substr(md5($review->student->email),0,6) }};display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#fff;flex-shrink:0;">{{ strtoupper(substr($review->student->name,0,1)) }}</div>
 <div style="flex:1;">
 <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:6px;">
 <div>
 <span style="font-weight:700;font-size:14px;color:#15281F;">{{ $review->student->name }}</span>
 <span style="font-size:12px;color:rgba(21,40,31,.4);margin-left:8px;">{{ $review->created_at->format('M Y') }}</span>
 </div>
 <div style="display:flex;gap:2px;">
 @for($i=1;$i<=5;$i++)<i class="fa-solid fa-star" style="color:{{ $i<=$review->rating ? '#EEBE15' : '#E5E7EB' }};font-size:13px;"></i>@endfor
 </div>
 </div>
 @if($review->comment)
 <p style="font-size:14px;color:#15281F;line-height:1.8;">{{ $review->comment }}</p>
 @endif
 </div>
 </div>
 </div>
 @empty
 <div class="prof-card" style="text-align:center;padding:48px 28px;">
 <i class="fa-solid fa-star" style="font-size:40px;color:rgba(21,40,31,.2);margin-bottom:12px;display:block;"></i>
 <p style="color:rgba(21,40,31,.55);font-size:15px;margin:0;">No reviews yet. Book a session and be the first!</p>
 </div>
 @endforelse
 @if($reviews->hasPages())
 <div style="margin-top:16px;">{{ $reviews->links() }}</div>
 @endif
 </div>

 {{--  BOOK SESSION TAB  --}}
 <div id="tab-book" style="display:none;" x-data="{
 step: 1,
 dur: 20,
 date: '',
 time: '',
 subject: '',
 notes: '',
 notesTouched: false,
 phone: '',
 mpesaCode: '',
 pendingVerification: false,
 pendingBookingUuid: '',
 processing: false,
 stkSent: false,
 stkMsg: '',
 confirmed: false,
 studentCode: '',
 bookingRef: '',
 slots: [],
 loadingSlots: false,
 prices: {{ json_encode($prices) }},
 get price() {
 const base = this.prices[this.dur] || 300;
 if (this.dur === 20 && {{ $isFreeFirstSession ? 'true' : 'false' }}) return 0;
 return base;
 },
 get isFree() { return this.price === 0; },
 get sessionName() {
 const names = {{ json_encode($sessNames) }};
 return names[this.dur] || 'Quick Help';
 },
 async fetchSlots() {
 if (!this.date) { this.slots = []; return; }
 this.loadingSlots = true;
 this.slots = [];
 this.time = '';
 try {
 const resp = await fetch('/api/tutors/{{ $tutor->id }}/availability?date=' + this.date, {
 headers: { 'Accept': 'application/json' }
 });
 const data = await resp.json();
 this.slots = data.slots || [];
 } catch(e) {
 console.error('Failed to fetch slots:', e);
 } finally {
 this.loadingSlots = false;
 }
 }
 }">

 {{-- Progress bar --}}
 <!-- <div style="display:flex;align-items:center;justify-content:center;gap:4;margin-bottom:28px;"> -->
 <div style="display:flex;align-items:center;justify-content:center;gap:16px;margin-bottom:28px;">
 @foreach(['Choose Session','Review & Pay','Confirmed'] as $i => $label)
 <div style="display:flex;align-items:center;">
 @if($i > 0)<div :style="step > {{ $i }} ? 'background:#0A64BC' : 'background:rgba(21,40,31,.1)'" style="height:2px;width:48px;transition:background .3s;"></div>@endif
 <div style="display:flex;flex-direction:column;align-items:center;gap:5px;">
 <div :style="step > {{ $i+1 }} ? 'background:#10B981;color:#fff' : step === {{ $i+1 }} ? 'background:#0A64BC;color:#fff' : 'background:rgba(21,40,31,.08);color:rgba(21,40,31,.4)'"
 style="width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;transition:all .3s;">
 <template x-if="step > {{ $i+1 }}"><i class="fa-solid fa-circle-check"></i></template>
 <template x-if="step <= {{ $i+1 }}"><span>{{ $i+1 }}</span></template>
 </div>
 <span style="font-size:11px;font-weight:600;" :style="step === {{ $i+1 }} ? 'color:#0A64BC' : 'color:rgba(21,40,31,.4)'">{{ $label }}</span>
 </div>
 </div>
 @endforeach
 </div>

 {{-- STEP 1: Choose session --}}
 <div x-show="step === 1">
 <div class="prof-card" style="margin-bottom:20px;">
 <h3 style="font-family:'Fraunces',serif;font-size:20px;color:#15281F;margin:0 0 24px;display:flex;align-items:center;gap:10px;">
 <i class="fa-solid fa-clock" style="color:#0A64BC;"></i> 1. Choose Session Type
 </h3>
 <div style="display:grid;grid-template-columns:1fr;gap:12px;" class="tier-grid">
 @foreach([20=>['fa-solid fa-zap', 'Quick Help','#0A64BC'],40=>['fa-solid fa-chalkboard', 'Guided Practice','#10B981'],60=>['fa-solid fa-book-open','Deep Dive','#E8522A']] as $mins => [$icon,$tname,$color])
 @php $actualPrice = ($mins===20 && $isFreeFirstSession) ? 0 : $prices[$mins]; @endphp
 <button @click="dur={{ $mins }}"
 :class="dur==={{ $mins }} ? 'tier-card selected' : 'tier-card'"
 style="position:relative;">
 @if($mins===20 && $isFreeFirstSession)
 <span style="position:absolute;top:-10px;right:10px;background:#10B981;color:#fff;font-size:10px;font-weight:800;padding:3px 10px;border-radius:999px;letter-spacing:.05em;">FREE FIRST SESSION</span>
 @endif
 <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
 <div style="display:flex;align-items:center;gap:8px;">
 <div style="width:40px;height:40px;border-radius:12px;background:{{ $color }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
 <i class="{{ $icon }}" style="font-size:18px;color:#fff;"></i>
 </div>
 <div>
 <div style="font-size:14px;font-weight:700;color:#15281F;">{{ $tname }}</div>
 <div style="font-size:11px;color:rgba(21,40,31,.5);">{{ $mins }} min</div>
 </div>
 </div>
 <div :style="dur==={{ $mins }} ? 'color:#0A64BC;' : 'color:#15281F;'" style="font-family:'Fraunces',serif;font-weight:800;text-align:right;">
 @if($actualPrice === 0)
 <span style="font-size:20px;color:#10B981;">FREE</span>
 <div style="font-size:11px;text-decoration:line-through;color:rgba(21,40,31,.35);">KSh {{ $prices[$mins] }}</div>
 @else
 <span style="font-size:18px;">KSh {{ $actualPrice }}</span>
 @endif
 </div>
 </div>
 <div style="font-size:12px;color:rgba(21,40,31,.5);">{{ $sessDesc[$mins] }}</div>
 </button>
 @endforeach
 </div>
 <style>.tier-grid{grid-template-columns:1fr!important;}@media(min-width:640px){.tier-grid{grid-template-columns:repeat(3,1fr)!important;}}</style>
 </div>

 <div class="prof-card" style="margin-bottom:20px;">
 <h3 style="font-family:'Fraunces',serif;font-size:20px;color:#15281F;margin:0 0 20px;display:flex;align-items:center;gap:10px;">
 <i class="fa-solid fa-calendar" style="color:#10B981;"></i> 2. Pick Date & Time
 </h3>
 <div style="margin-bottom:18px;">
 <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:block;margin-bottom:6px;">Date</label>
 <input type="date" x-model="date" @change="fetchSlots()" :min="new Date().toISOString().split('T')[0]"
 style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;background:#fff;"
 onfocus="this.style.borderColor='#0A64BC'" onblur="this.style.borderColor='rgba(21,40,31,.18)'">
 </div>

 {{-- Time slot picker --}}
 <div style="margin-bottom:14px;">
 <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:block;margin-bottom:6px;">
 Available Time Slots (Nairobi, Kenya  EAT UTC+3)
 </label>

 {{-- Loading state --}}
 <template x-if="loadingSlots">
 <div style="display:flex;align-items:center;gap:8px;padding:20px;color:rgba(21,40,31,.5);font-size:14px;">
 <span style="font-size:inherit;"><i class="fa-solid fa-hourglass-half"></i></span> Checking availability...
 </div>
 </template>

 {{-- Slots available --}}
 <template x-if="!loadingSlots && slots.length > 0">
 <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;" class="slot-grid">
 <template x-for="slot in slots" :key="slot.time">
 <button @click="time = slot.time"
 :style="time === slot.time ? 'background:#0A64BC;color:#fff;border-color:#0A64BC;' : 'background:#F9FAF9;color:#15281F;border-color:rgba(21,40,31,.12);'"
 style="padding:10px 6px;border-radius:10px;border:1.5px solid;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s;font-family:inherit;text-align:center;"
 x-text="slot.label"
 onmouseover="if(this.style.background !== '#0A64BC') this.style.borderColor='#0A64BC'"
 onmouseout="if(this.style.background !== '#0A64BC') this.style.borderColor='rgba(21,40,31,.12)'">
 </button>
 </template>
 </div>
 </template>

 {{-- No slots --}}
 <template x-if="!loadingSlots && date && slots.length === 0">
 <div style="padding:20px;text-align:center;background:#F9FAF9;border-radius:12px;border:1px solid rgba(21,40,31,.08);">
 <i class="fa-solid fa-circle-xmark"></i>
 <p style="font-size:13px;color:rgba(21,40,31,.5);">No available slots for this date. Try another day.</p>
 </div>
 </template>

 {{-- No date selected --}}
 <template x-if="!loadingSlots && !date">
 <div style="padding:20px;text-align:center;background:#F9FAF9;border-radius:12px;border:1px solid rgba(21,40,31,.08);">
 <i class="fa-solid fa-calendar"></i>
 <p style="font-size:13px;color:rgba(21,40,31,.5);">Select a date to see available time slots.</p>
 </div>
 </template>
 </div>
 <style>.slot-grid{grid-template-columns:repeat(3,1fr)!important;}@media(min-width:480px){.slot-grid{grid-template-columns:repeat(4,1fr)!important;}}@media(min-width:768px){.slot-grid{grid-template-columns:repeat(5,1fr)!important;}}</style>

 @if($tutor->subjects->isNotEmpty())
 <div style="margin-bottom:14px;">
 <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:block;margin-bottom:6px;">Subject (optional)</label>
 <select x-model="subject"
 style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;background:#fff;appearance:none;"
 onfocus="this.style.borderColor='#0A64BC'" onblur="this.style.borderColor='rgba(21,40,31,.18)'">
 <option value="">Select a subject</option>
 @foreach($tutor->subjects as $s)
 <option value="{{ $s->id }}">{{ $s->name }}</option>
 @endforeach
 </select>
 </div>
 @endif

 <div>
 <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:block;margin-bottom:6px;">Notes for tutor <span style="color:#E8522A;">*</span></label>
 <textarea x-model="notes" rows="2" placeholder="Topics to cover, past exam areas, specific questions"
 required
 style="width:100%;padding:12px 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;resize:none;line-height:1.6;background:#fff;"
 onfocus="this.style.borderColor='#0A64BC'" onblur="this.style.borderColor='rgba(21,40,31,.18)'"></textarea>
 <p x-show="notesTouched && !notes.trim()" style="font-size:12px;color:#E8522A;margin-top:4px;">Please describe what you need help with.</p>
 </div>
 </div>

 <button @click="notes.trim() ? (step=2) : (notesTouched=true)" :disabled="!date || !time || !notes.trim()"
 style="width:100%;height:88px;background:linear-gradient(135deg,#10B981 0%,#059669 100%);color:#fff;font-size:20px;font-weight:800;border:none;cursor:pointer;font-family:inherit;transition:all .3s;display:flex;align-items:center;justify-content:center;gap:14px;border-radius:16px;box-shadow:0 10px 36px rgba(16,185,129,.4);margin-bottom:16px;border:3px solid #10B981;"
 :style="(!date||!time||!notes.trim())?'opacity:.3;cursor:not-allowed;box-shadow:none;background:linear-gradient(135deg,#999 0%,#666 100%);border-color:#999':''"
 @mouseover="!(!date||!time||!notes.trim()) && (this.style.transform='translateY(-4px)',this.style.boxShadow='0 16px 48px rgba(16,185,129,.5)')"
 @mouseout="(!date||!time||!notes.trim()) ? (this.style.boxShadow='none') : (this.style.transform='translateY(0)',this.style.boxShadow='0 10px 36px rgba(16,185,129,.4)')">
 <i class="fa-solid fa-arrow-right" style="font-size:24px;"></i> Continue to Review
 </button>
 <p style="text-align:center;font-size:13px;color:rgba(21,40,31,.6);margin:16px 0 0;font-weight:500;display:flex;align-items:center;justify-content:center;gap:6px;">
 <i class="fa-solid fa-shield-halved" style="color:#10B981;"></i> 100% money-back if unsatisfied with your first lesson
 </p>
 </div>

 {{-- STEP 2: Review & Pay --}}
 <div x-show="step === 2" x-cloak>
 <div class="prof-card" style="margin-bottom:20px;">
 <h3 style="font-family:'Fraunces',serif;font-size:20px;color:#15281F;margin:0 0 22px;display:flex;align-items:center;gap:10px;">
 <i class="fa-solid fa-receipt" style="color:#E8522A;"></i> Session Summary
 </h3>

 {{-- Summary rows --}}
 <div style="background:#F9FAF9;border-radius:14px;padding:18px;margin-bottom:18px;">
 @foreach([
 ['fa-user','Tutor',$tutor->user->name],
 ['fa-clock','Session','<span x-text="dur+\' min - \'+sessionName"></span>'],
 ['fa-calendar','Date','<span x-text="date ? new Date(date).toDateString() : \'-\'"></span>'],
 ['fa-clock','Time','<span x-text="time || \'-\'"></span> EAT'],
 ] as [$ic,$label,$val])
 <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid rgba(21,40,31,.07);">
 <span style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(21,40,31,.55);">
 <i class="fa-solid {{ $ic }}"></i>{{ $label }}
 </span>
 <span style="font-size:13px;font-weight:600;color:#15281F;">{!! $val !!}</span>
 </div>
 @endforeach
 <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 0 0;">
 <span style="font-size:15px;font-weight:700;color:#15281F;">Total</span>
 <template x-if="isFree">
 <div style="text-align:right;">
 <span style="font-family:'Fraunces',serif;font-size:26px;font-weight:800;color:#10B981;">FREE</span>
 <div style="font-size:12px;text-decoration:line-through;color:rgba(21,40,31,.35);">KSh 300</div>
 </div>
 </template>
 <template x-if="!isFree">
 <span style="font-family:'Fraunces',serif;font-size:26px;font-weight:800;color:#0A64BC;">KSh <span x-text="price"></span></span>
 </template>
 </div>
 </div>

 {{-- Code notice --}}
 <div style="background:rgba(10,100,188,.05);border:1px solid rgba(10,100,188,.15);border-radius:14px;padding:14px 16px;margin-bottom:18px;">
 <p style="font-size:13px;font-weight:700;color:#0A64BC;margin-bottom:4px;display:flex;align-items:center;gap:6px;">
 <span style="font-size:inherit;"><i class="fa-solid fa-key"></i></span> After payment
 </p>
 <p style="font-size:12px;color:rgba(21,40,31,.6);line-height:1.65;">Both you and {{ explode(' ',$tutor->user->name)[0] }} will receive a unique session access code by email. You'll use your <strong style="color:#15281F;">STU-XXXXXX</strong> code to join the live class.</p>
 </div>

 {{-- Payment section --}}
 @auth
 <div style="background:#F9FAF9;border-radius:14px;padding:18px;">
 <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.45);margin-bottom:12px;">
 <span style="font-size:inherit;"><i class="fa-solid fa-mobile-screen"></i></span> Pay Now
 </p>

 {{-- I&M Bank Paybill Instructions --}}
 <div x-show="!isFree" style="background:#F0FDF4;border:1.5px solid #10B981;border-radius:14px;padding:18px;margin-bottom:16px;">
 <div style="font-weight:700;font-size:13px;color:#065F46;margin-bottom:12px;">
 <i class="fa-solid fa-mobile-screen-button"></i> Pay via M-Pesa Paybill
 </div>
 <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
 <div style="background:#fff;border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(16,185,129,.3);">
 <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:rgba(21,40,31,.45);margin-bottom:4px;">Paybill Number</div>
 <div style="font-family:'Fraunces',serif;font-size:26px;font-weight:800;color:#15281F;">542542</div>
 </div>
 <div style="background:#fff;border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(16,185,129,.3);">
 <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:rgba(21,40,31,.45);margin-bottom:4px;">Account Number</div>
 <div style="font-family:'Fraunces',serif;font-size:26px;font-weight:800;color:#0A64BC;">313126</div>
 </div>
 </div>
 <div style="font-size:12px;color:rgba(21,40,31,.55);text-align:center;margin-bottom:10px;">
 Account Name: <strong style="color:#15281F;">OBARE OKOTH JACOB</strong>
 </div>
 <div style="background:rgba(16,185,129,.08);border-radius:8px;padding:10px 14px;font-size:12px;color:#065F46;line-height:1.6;">
 <strong>Steps:</strong> M-Pesa  Lipa na M-Pesa  Pay Bill  Paybill: <strong>542542</strong>  Account: <strong>313126</strong>  Amount: KSh <span x-text="price"></span>
 </div>
 </div>

 {{-- Transaction Code Input --}}
 <div style="margin-bottom:14px;" x-show="!isFree">
 <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.6);text-transform:uppercase;letter-spacing:.07em;display:block;margin-bottom:6px;">
 M-Pesa Transaction Code <span style="color:#E8522A;">*</span>
 </label>
 <input type="text" x-model="mpesaCode"
 placeholder="e.g. RKA7X1Y2Z3"
 style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:15px;font-family:monospace;letter-spacing:.08em;text-transform:uppercase;outline:none;box-sizing:border-box;"
 onfocus="this.style.borderColor='#0A64BC'" onblur="this.style.borderColor='rgba(21,40,31,.18)'"
 oninput="this.value=this.value.toUpperCase()" maxlength="20">
 <p style="font-size:11px;color:rgba(21,40,31,.5);margin-top:4px;">Enter the code from your M-Pesa confirmation SMS (e.g. RKA7X1Y2Z3)</p>
 </div>

 <button @click="processPayment($data)"
 :disabled="processing"
 :style="isFree ? 'background:linear-gradient(135deg,#10B981 0%,#059669 100%);' : 'background:linear-gradient(135deg,#0A64BC 0%,#0052A3 100%);'"
 style="width:100%;height:88px;border-radius:16px;color:#fff;font-size:22px;font-weight:800;border:3px solid;border-color:inherit;cursor:pointer;font-family:inherit;transition:all .3s;display:flex;align-items:center;justify-content:center;gap:12px;box-shadow:0 10px 36px rgba(10,100,188,.4);margin-bottom:16px;"
 :style="(isFree ? 'border-color:#10B981;box-shadow:0 10px 36px rgba(16,185,129,.4);' : 'border-color:#0A64BC;box-shadow:0 10px 36px rgba(10,100,188,.4);')"
 @mouseover="this.style.transform='translateY(-4px)';this.style.boxShadow=(isFree ? '0 16px 48px rgba(16,185,129,.5)' : '0 16px 48px rgba(10,100,188,.5)')"
 @mouseout="this.style.transform='translateY(0)';this.style.boxShadow=(isFree ? '0 10px 36px rgba(16,185,129,.4)' : '0 10px 36px rgba(10,100,188,.4)')">
 <template x-if="!processing">
 <span style="display:flex;align-items:center;gap:12px;">
 <i :class="isFree ? 'fa-solid fa-gift' : 'fa-solid fa-paper-plane'" style="font-size:26px;"></i>
 <span x-text="isFree ? 'Book Free Session' : 'Submit Payment'" style="font-size:inherit;"></span>
 </span>
 </template>
 <template x-if="processing">
 <span style="display:flex;align-items:center;gap:12px;">
 <i class="fa-solid fa-hourglass-half" style="font-size:26px;animation:spin 1s linear infinite;"></i> <span style="font-size:inherit;">Processing Payment</span>
 </span>
 </template>
 </button>
 </div>
 @else
 <div style="text-align:center;padding:20px;">
 <p style="font-size:14px;color:rgba(21,40,31,.6);margin-bottom:14px;">Sign in to complete your booking</p>
 <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
 <a href="{{ route('login') }}" style="display:inline-flex;align-items:center;gap:7px;padding:12px 24px;border-radius:999px;background:#0A64BC;color:#fff;font-size:14px;font-weight:600;text-decoration:none;">
 <i class="fa-solid fa-right-to-bracket"></i> Sign In
 </a>
 <a href="{{ route('register') }}" style="display:inline-flex;align-items:center;gap:7px;padding:12px 24px;border-radius:999px;border:2px solid rgba(21,40,31,.2);color:#15281F;font-size:14px;font-weight:600;text-decoration:none;">
 <i class="fa-solid fa-user"></i> Create Account
 </a>
 </div>
 </div>
 @endauth

 <div style="display:flex;justify-content:flex-start;margin-top:16px;">
 <button @click="step=1" style="display:flex;align-items:center;gap:6px;background:none;border:none;color:rgba(21,40,31,.55);font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;padding:8px 12px;transition:all .2s;border-radius:8px;" onmouseover="this.style.color='#15281F';this.style.background='rgba(21,40,31,.05)'" onmouseout="this.style.color='rgba(21,40,31,.55)';this.style.background='none'">
 <i class="fa-solid fa-arrow-left"></i> Back
 </button>
 </div>
 </div>
 </div>

 {{-- STEP 3: Confirmed or Pending --}}
 <div x-show="step === 3" x-cloak>
 <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:40px;text-align:center;">

 {{-- PENDING VERIFICATION STATE --}}
 <div x-show="pendingVerification">
 <div style="font-size:52px;margin-bottom:16px;"></div>
 <h2 style="font-family:'Fraunces',serif;font-size:24px;color:#15281F;margin-bottom:8px;">Booking Pending Confirmation</h2>
 <p style="font-size:14px;color:rgba(21,40,31,.6);line-height:1.7;margin-bottom:20px;">
 Your payment is being verified by our team.<br>
 This usually takes <strong>under 5 minutes</strong>.<br>
 You'll receive your session codes by email once confirmed.
 </p>
 <div style="background:#FEF3C7;border-radius:12px;padding:14px 16px;font-size:13px;color:#92400E;margin-bottom:20px;text-align:left;">
 <i class="fa-solid fa-triangle-exclamation"></i>
 <strong>Keep your M-Pesa SMS</strong>  your transaction code may be requested during verification.
 </div>
 <a :href="'{{ url('/bookings/') }}/' + pendingBookingUuid" x-show="pendingBookingUuid"
 style="display:inline-flex;align-items:center;gap:8px;padding:13px 28px;background:#0A64BC;color:#fff;border-radius:999px;font-weight:700;font-size:14px;text-decoration:none;">
 <i class="fa-solid fa-eye"></i> View Booking Status
 </a>
 </div>

 {{-- CONFIRMED STATE --}}
 <div x-show="!pendingVerification">
 <div style="width:68px;height:68px;border-radius:50%;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
 <i class="fa-solid fa-circle-check"></i>
 </div>
 <h2 style="font-family:'Fraunces',serif;font-size:26px;color:#15281F;margin-bottom:8px;">Booking Confirmed!</h2>
 <p style="font-size:14px;color:rgba(21,40,31,.6);margin-bottom:28px;">Payment received. Your session codes have been emailed to you. Redirecting to your session in 3 seconds</p>

 {{-- Session code --}}
 <div style="background:rgba(10,100,188,.06);border:2px solid rgba(10,100,188,.25);border-radius:20px;padding:24px;margin-bottom:24px;">
 <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(21,40,31,.45);margin-bottom:10px;display:flex;align-items:center;justify-content:center;gap:6px;">
 <span style="font-size:inherit;"><i class="fa-solid fa-key"></i></span> Your Session Access Code
 </p>
 <p style="font-family:monospace;font-size:36px;font-weight:800;color:#0A64BC;letter-spacing:.12em;line-height:1;" x-text="studentCode">STU-XXXXXX</p>
 <p style="font-size:12px;color:rgba(21,40,31,.5);margin-top:10px;">Enter this code on session day to join your private live class</p>
 </div>

 {{-- What happens next --}}
 <div style="background:#F9FAF9;border-radius:14px;padding:16px;text-align:left;margin-bottom:24px;">
 <p style="font-size:12px;font-weight:700;color:#15281F;margin-bottom:10px;"><span style="font-size:inherit;"><i class="fa-solid fa-clipboard"></i></span> What happens next</p>
 @foreach(['Your tutor has been notified and received their own TUT-XXXXXX code','A confirmation email with all details has been sent to you','On session day, go to your dashboard  click Join Session  enter your code','Your private classroom opens instantly in your browser - no download required'] as $step)
 <div style="display:flex;align-items:flex-start;gap:8px;font-size:13px;color:rgba(21,40,31,.7);margin-bottom:7px;">
 <i class="fa-solid fa-circle-check"></i>{{ $step }}
 </div>
 @endforeach
 </div>

 <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
 <a :href="bookingRef ? '/bookings/' + bookingRef : '{{ route('dashboard') }}'"
 style="display:inline-flex;align-items:center;gap:7px;padding:13px 28px;border-radius:999px;background:#10B981;color:#fff;font-size:15px;font-weight:700;text-decoration:none;">
 <i class="fa-solid fa-video"></i> Go to My Session
 </a>
 <a href="{{ route('dashboard') }}" style="display:inline-flex;align-items:center;gap:7px;padding:13px 24px;border-radius:999px;border:1.5px solid rgba(21,40,31,.18);color:#15281F;font-size:14px;font-weight:600;text-decoration:none;">
 <i class="fa-solid fa-chart-bar"></i> Dashboard
 </a>
 <a href="{{ route('tutors.index') }}" style="display:inline-flex;align-items:center;gap:7px;padding:13px 24px;border-radius:999px;border:1.5px solid rgba(21,40,31,.18);color:#15281F;font-size:14px;font-weight:600;text-decoration:none;">
 <i class="fa-solid fa-magnifying-glass"></i> Browse More Tutors
 </a>
 </div>
 </div>
 </div>{{-- /confirmed state --}}
 </div>{{-- /card --}}
 </div>{{-- /step 3 --}}

 {{-- RIGHT: Sticky booking card (desktop) - REMOVED --}}
 </div>
</div>


<style>.blog-show-g{grid-template-columns:1fr!important;}@media(min-width:1024px){.blog-show-g{grid-template-columns:1fr 300px!important;}}</style>
@include('partials.footer')
<script src="https://meet.jit.si/external_api.js" defer></script>
<script>
function showTab(name, btn) {
 ['about','availability','reviews','book'].forEach(t => {
 const el = document.getElementById('tab-'+t);
 if (el) el.style.display = t === name ? 'block' : 'none';
 });
 document.querySelectorAll('.ptab').forEach(b => {
 b.classList.remove('on');
 b.style.color = 'rgba(255,255,255,.45)';
 b.style.borderBottomColor = 'transparent';
 });
 if (btn) {
 btn.classList.add('on');
 btn.style.color = name === 'book' ? '#EEBE15' : '#fff';
 btn.style.borderBottomColor = name === 'book' ? '#EEBE15' : '#0A64BC';
 }
}

function showError(title, message) {
 // Remove any existing error toast
 const existing = document.getElementById('pay-error-toast');
 if (existing) existing.remove();

 const toast = document.createElement('div');
 toast.id = 'pay-error-toast';
 toast.style.cssText = 'position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;background:#FEF2F2;border:1.5px solid #DC2626;border-radius:12px;padding:16px 24px;max-width:400px;width:90%;box-shadow:0 8px 32px rgba(0,0,0,.15);display:flex;align-items:flex-start;gap:12px;';
 toast.innerHTML = '<i class="fa-solid fa-circle-exclamation" style="color:#DC2626;font-size:20px;flex-shrink:0;margin-top:2px;"></i><div><div style="font-weight:700;color:#991B1B;font-size:14px;margin-bottom:4px;">' + title + '</div><div style="font-size:13px;color:#7F1D1D;">' + message + '</div></div>';
 document.body.appendChild(toast);
 setTimeout(function() { toast.remove(); }, 5000);
}

function processPayment(data) {
 if (!data || !data.date || !data.time) {
 showError('Missing Information', 'Please select a date and time.');
 return;
 }
 if (!data.isFree && (!data.mpesaCode || data.mpesaCode.trim().length < 6)) {
 showError('Transaction Code Required', 'Please enter the M-Pesa transaction code from your confirmation SMS.');
 return;
 }

 data.processing = true;

 fetch('/bookings', {
 method: 'POST',
 headers: {
 'Content-Type': 'application/json',
 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
 'Accept': 'application/json',
 },
 body: JSON.stringify({
 tutor_profile_id: {{ $tutor->id }},
 duration_minutes: data.dur,
 scheduled_at: data.date + ' ' + data.time,
 subject_id: data.subject || null,
 notes: data.notes,
 mpesa_code: data.mpesaCode ? data.mpesaCode.trim().toUpperCase() : '',
 })
 })
 .then(r => r.json())
 .then(res => {
 data.processing = false;
 if (res.status === 'ok' && res.free) {
 data.studentCode = res.student_code || '';
 data.bookingRef  = res.booking_uuid || '';
 data.step = 3;
 if (res.booking_url) {
 setTimeout(() => { window.location.href = res.booking_url; }, 3000);
 }
 return;
 }
 if (res.status === 'ok' && res.mode === 'manual') {
 window.location.href = res.pending_url || '/dashboard';
 return;
 }
 showError('Booking Failed', res.message || 'Could not process booking. Please try again.');
 })
 .catch(() => {
 data.processing = false;
 showError('Network Error', 'Please check your connection and try again.');
 });
}
</script>
<style>.session-layout{grid-template-columns:1fr!important;}@media(min-width:1024px){.session-layout{grid-template-columns:1fr 380px!important;}}</style>
@livewireScripts
</body>
</html>

