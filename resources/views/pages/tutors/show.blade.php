<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>{{ $tutor->user->name }} – Verified Tutor | Mwalimu</title>
<meta name="description" content="{{ Str::limit($tutor->bio ?? 'Book a session with '.$tutor->user->name.' on Mwalimu.', 160) }}">
<style>
/* ── Page-level styles ── */
.profile-hero{background:linear-gradient(135deg,#111C16 0%,#15281F 60%,#1a2e22 100%);padding:48px 0 0;position:relative;overflow:hidden;}
.profile-hero::before{content:'';position:absolute;top:-120px;right:-80px;width:400px;height:400px;background:rgba(10,100,188,.12);border-radius:50%;pointer-events:none;}
.profile-hero::after{content:'';position:absolute;bottom:-60px;left:20%;width:200px;height:200px;background:rgba(212,160,23,.06);border-radius:50%;pointer-events:none;}

/* Tabs */
.ptab{padding:14px 20px;font-size:14px;font-weight:600;background:none;border:none;cursor:pointer;color:rgba(255,255,255,.45);border-bottom:3px solid transparent;transition:all .18s;font-family:inherit;white-space:nowrap;}
.ptab.on,.ptab:hover{color:#fff;border-bottom-color:#0A64BC;}

/* Session tier cards */
.tier-card{border:2px solid rgba(21,40,31,.12);border-radius:16px;padding:16px;text-align:left;cursor:pointer;transition:all .2s;background:#fff;display:block;width:100%;}
.tier-card:hover,.tier-card.selected{border-color:#0A64BC;background:rgba(10,100,188,.04);}
.tier-card.selected{background:rgba(10,100,188,.06);}

/* Review rating bars */
.rbar-track{height:7px;background:#F4F6F4;border-radius:999px;overflow:hidden;flex:1;}
.rbar-fill{height:100%;background:#EEBE15;border-radius:999px;transition:width .5s ease;}

/* Booking steps */
.bstep{display:flex;flex-direction:column;gap:4px;align-items:center;}
.bstep-dot{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;transition:all .2s;}

/* Payment form */
.pay-field{width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);background:#fff;font-size:14px;font-family:inherit;color:#15281F;outline:none;transition:all .15s;}
.pay-field:focus{border-color:#0A64BC;box-shadow:0 0 0 3px rgba(10,100,188,.1);}
</style>
</head>
<body>
@php
$prices    = [20=>300,40=>500,60=>700];
$sessNames = [20=>'Quick Help',40=>'Guided Practice',60=>'Deep Dive'];
$sessDesc  = [20=>'Rapid revision or a quick question',40=>'Full structured lesson with Q&A',60=>'Maximum learning, best value'];
// Free first 20-min session check
$isFreeFirstSession = auth()->check()
    ? \App\Models\TutorProfile::isFreeFirstSession(auth()->id(), $tutor->user_id)
    : true; // Show free badge to guests too to encourage signup
$colors    = ['#0A64BC','#10B981','#E8522A','#EEBE15','#3B82F6'];
$avColor   = $colors[$tutor->id % count($colors)];
$reviews   = $tutor->reviews()->where('is_visible',true)->with('student')->latest()->paginate(8);
@endphp

@include('partials.nav')

{{-- ── PROFILE HERO ────────────────────────────────────────────────── --}}
<div class="profile-hero">
    <div style="max-width:1360px;margin:0 auto;padding:0 24px @media(min-width:1024px){padding:0 48px}">

        {{-- Breadcrumb --}}
        <div style="margin-bottom:20px;">
            <a href="{{ route('tutors.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:rgba(255,255,255,.5);text-decoration:none;transition:color .15s;" onmouseover="this.style.color='rgba(255,255,255,.85)'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
                <i class="fa-solid fa-arrow-left fa-xs"></i> All Tutors
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
                        <i class="fa-solid fa-certificate fa-xs"></i> TSC Verified
                    </span>
                    @if($tutor->is_tsc_registered ?? false)
                    <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(212,160,23,.2);color:#FCD34D;border:1px solid rgba(212,160,23,.25);padding:4px 12px;border-radius:999px;font-size:12px;font-weight:700;">
                        <i class="fa-solid fa-star fa-xs"></i> National Examiner
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
                        <i class="fa-solid fa-star" style="color:#EEBE15;font-size:13px;"></i>
                        <strong style="color:#fff;">{{ number_format($tutor->rating_avg,1) }}</strong>
                        <span>({{ $tutor->total_reviews }} reviews)</span>
                    </span>
                    <span style="display:flex;align-items:center;gap:5px;">
                        <i class="fa-solid fa-chalkboard fa-xs" style="opacity:.6;"></i>
                        <strong style="color:#fff;">{{ number_format($tutor->total_sessions) }}</strong> sessions
                    </span>
                    <span style="display:flex;align-items:center;gap:5px;">
                        <i class="fa-solid fa-clock fa-xs" style="opacity:.6;"></i>
                        <strong style="color:#fff;">{{ $tutor->experience_years }}</strong> years exp.
                    </span>
                    <!-- @if($tutor->city)
                    <span style="display:flex;align-items:center;gap:5px;">
                        <i class="fa-solid fa-location-dot fa-xs" style="opacity:.6;"></i>
                        {{ $tutor->city }}, Kenya
                    </span>
                    @endif -->
                    <span style="display:flex;align-items:center;gap:5px;">
                        <i class="fa-solid fa-reply fa-xs" style="opacity:.6;"></i>
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
        <div style="display:flex;gap:0;margin-top:28px;overflow-x:auto;" x-data="">
            <button class="ptab on" onclick="showTab('about',this)">
                <i class="fa-solid fa-user fa-xs" style="margin-right:5px;"></i>About
            </button>
            <button class="ptab" onclick="showTab('availability',this)">
                <i class="fa-solid fa-calendar-week fa-xs" style="margin-right:5px;"></i>Availability
            </button>
            <button class="ptab" onclick="showTab('reviews',this)">
                <i class="fa-solid fa-star fa-xs" style="margin-right:5px;"></i>Reviews ({{ $tutor->total_reviews }})
            </button>
            <button class="ptab" onclick="showTab('book',this)" id="book-tab-btn" style="color:#EEBE15;border-bottom-color:#EEBE15;">
                <i class="fa-solid fa-calendar-check fa-xs" style="margin-right:5px;"></i>Book Session
            </button>
        </div>
    </div>
</div>

{{-- ── MAIN CONTENT ─────────────────────────────────────────────────── --}}
<div style="max-width:1360px;margin:0 auto;padding:32px 24px 64px;" class="w">
    <div style="display:grid;grid-template-columns:1fr;gap:24px;" class="show-grid">

        {{-- LEFT: Tab content --}}
        <div>

            {{-- ── ABOUT TAB ── --}}
            <div id="tab-about">
                {{-- Stats cards --}}
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:20px;" class="stat-mini-g">
                    @foreach([
                        ['fa-chalkboard','#0A64BC',number_format($tutor->total_sessions),'Sessions Completed'],
                        ['fa-star','#EEBE15',$tutor->total_reviews,'Total Reviews'],
                        ['fa-clock','#10B981',$tutor->experience_years.'yr','Experience'],
                        ['fa-reply','#E8522A','<2hr','Response Time'],
                    ] as [$ic,$col,$val,$lbl])
                    <div style="background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.08);padding:18px;display:flex;align-items:center;gap:14px;">
                        <div style="width:42px;height:42px;border-radius:12px;background:{{ $col }}18;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid {{ $ic }}" style="color:{{ $col }};font-size:16px;"></i>
                        </div>
                        <div>
                            <div style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:#15281F;">{!! $val !!}</div>
                            <div style="font-size:12px;color:rgba(21,40,31,.55);">{{ $lbl }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <style>.stat-mini-g{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:640px){.stat-mini-g{grid-template-columns:repeat(4,1fr)!important;}}</style>

                {{-- Bio --}}
                <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:28px;margin-bottom:20px;">
                    <h2 style="font-family:'Fraunces',serif;font-size:20px;color:#15281F;margin-bottom:14px;display:flex;align-items:center;gap:8px;">
                        <i class="fa-solid fa-user" style="color:#0A64BC;font-size:16px;"></i>
                        About {{ explode(' ',$tutor->user->name)[0] }}
                    </h2>
                    <p style="font-size:15px;color:#15281F;line-height:1.9;">{{ $tutor->bio ?? 'This tutor hasn\'t added a bio yet.' }}</p>

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
                <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:28px;">
                    <h3 style="font-family:'Fraunces',serif;font-size:18px;color:#15281F;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                        <i class="fa-solid fa-book-open" style="color:#0A64BC;font-size:15px;"></i> Subjects Taught
                    </h3>
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;" class="subj-g">
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

            {{-- ── AVAILABILITY TAB ── --}}
            <div id="tab-availability" style="display:none;">
                <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:28px;">
                    <h2 style="font-family:'Fraunces',serif;font-size:20px;color:#15281F;margin-bottom:6px;display:flex;align-items:center;gap:8px;">
                        <i class="fa-solid fa-calendar-week" style="color:#0A64BC;font-size:16px;"></i> Weekly Schedule
                    </h2>
                    <p style="font-size:13px;color:rgba(21,40,31,.5);margin-bottom:22px;">East Africa Time (EAT, UTC+3)</p>
                    @php
                    $avails = $tutor->availabilities()->where('is_active',true)->orderBy('day_of_week')->get();
                    $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
                    @endphp
                    @if($avails->isEmpty())
                    <div style="text-align:center;padding:40px;color:rgba(21,40,31,.45);">
                        <i class="fa-regular fa-calendar-xmark" style="font-size:36px;margin-bottom:12px;display:block;color:rgba(21,40,31,.15);"></i>
                        <p style="font-size:14px;">Availability not set. Book anyway — the tutor will confirm a time that works.</p>
                    </div>
                    @else
                    @foreach($days as $i => $day)
                    @php $av = $avails->firstWhere('day_of_week',$i); @endphp
                    <div style="display:flex;align-items:center;gap:16px;padding:14px 16px;border-radius:13px;margin-bottom:7px;background:{{ $av ? 'rgba(10,100,188,.04)' : '#FAFCFA' }};border:1px solid {{ $av ? 'rgba(10,100,188,.15)' : 'rgba(21,40,31,.07)' }};">
                        <span style="font-weight:600;font-size:14px;color:#15281F;width:96px;flex-shrink:0;">{{ $day }}</span>
                        @if($av)
                        <span style="font-size:14px;font-weight:500;color:#0A64BC;display:flex;align-items:center;gap:6px;">
                            <i class="fa-regular fa-clock fa-xs"></i>
                            {{ substr($av->start_time,0,5) }} – {{ substr($av->end_time,0,5) }}
                        </span>
                        <span style="margin-left:auto;background:rgba(16,185,129,.1);color:#10B981;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;display:flex;align-items:center;gap:4px;">
                            <i class="fa-solid fa-check fa-xs"></i> Available
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

            {{-- ── REVIEWS TAB ── --}}
            <div id="tab-reviews" style="display:none;">
                {{-- Rating overview --}}
                <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:28px;margin-bottom:16px;">
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
                                <i class="fa-solid fa-star" style="color:#EEBE15;font-size:11px;"></i>
                                <div class="rbar-track"><div class="rbar-fill" style="width:{{ $pct }}%;"></div></div>
                                <span style="font-size:12px;color:rgba(21,40,31,.4);width:20px;">{{ $cnt }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Review list --}}
                @forelse($reviews as $review)
                <div style="background:#fff;border-radius:18px;border:1px solid rgba(21,40,31,.08);padding:22px;margin-bottom:12px;">
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
                <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:48px;text-align:center;">
                    <i class="fa-regular fa-star" style="font-size:36px;color:rgba(21,40,31,.12);display:block;margin-bottom:12px;"></i>
                    <p style="color:rgba(21,40,31,.5);font-size:14px;">No reviews yet. Book a session and be the first!</p>
                </div>
                @endforelse
                @if($reviews->hasPages())
                <div style="margin-top:16px;">{{ $reviews->links() }}</div>
                @endif
            </div>

            {{-- ── BOOK SESSION TAB ── --}}
            <div id="tab-book" style="display:none;" x-data="{
                step: 1,
                dur: 20,
                date: '',
                time: '',
                subject: '',
                notes: '',
                processing: false,
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
                                <template x-if="step > {{ $i+1 }}"><i class="fa-solid fa-check fa-xs"></i></template>
                                <template x-if="step <= {{ $i+1 }}"><span>{{ $i+1 }}</span></template>
                            </div>
                            <span style="font-size:11px;font-weight:600;" :style="step === {{ $i+1 }} ? 'color:#0A64BC' : 'color:rgba(21,40,31,.4)'">{{ $label }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- STEP 1: Choose session --}}
                <div x-show="step === 1">
                    <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:28px;margin-bottom:16px;">
                        <h3 style="font-family:'Fraunces',serif;font-size:19px;color:#15281F;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
                            <i class="fa-solid fa-clock" style="color:#0A64BC;font-size:16px;"></i> 1. Choose Session Type
                        </h3>
                        <div style="display:grid;grid-template-columns:1fr;gap:10px;" class="tier-grid">
                            @foreach([20=>['⚡','Quick Help'],40=>['🎯','Guided Practice'],60=>['🏆','Deep Dive']] as $mins => [$icon,$tname])
                            @php $actualPrice = ($mins===20 && $isFreeFirstSession) ? 0 : $prices[$mins]; @endphp
                            <button @click="dur={{ $mins }}"
                                    :class="dur==={{ $mins }} ? 'tier-card selected' : 'tier-card'"
                                    style="position:relative;">
                                @if($mins===20 && $isFreeFirstSession)
                                <span style="position:absolute;top:-10px;right:10px;background:#10B981;color:#fff;font-size:10px;font-weight:800;padding:3px 10px;border-radius:999px;letter-spacing:.05em;">FREE FIRST SESSION</span>
                                @endif
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <span style="font-size:20px;">{{ $icon }}</span>
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

                    <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:28px;margin-bottom:16px;">
                        <h3 style="font-family:'Fraunces',serif;font-size:19px;color:#15281F;margin-bottom:18px;display:flex;align-items:center;gap:8px;">
                            <i class="fa-solid fa-calendar" style="color:#0A64BC;font-size:16px;"></i> 2. Pick Date & Time
                        </h3>
                        <div style="margin-bottom:16px;">
                            <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:block;margin-bottom:6px;">Date</label>
                            <input type="date" x-model="date" @change="fetchSlots()" :min="new Date(Date.now()+86400000).toISOString().split('T')[0]"
                                   style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;background:#fff;"
                                   onfocus="this.style.borderColor='#0A64BC'" onblur="this.style.borderColor='rgba(21,40,31,.18)'">
                        </div>

                        {{-- Time slot picker --}}
                        <div style="margin-bottom:14px;">
                            <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:block;margin-bottom:6px;">
                                Available Time Slots (EAT)
                            </label>

                            {{-- Loading state --}}
                            <template x-if="loadingSlots">
                                <div style="display:flex;align-items:center;gap:8px;padding:20px;color:rgba(21,40,31,.5);font-size:14px;">
                                    <i class="fa-solid fa-spinner fa-spin" style="color:#0A64BC;"></i> Checking availability...
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
                                    <i class="fa-regular fa-calendar-xmark" style="font-size:24px;color:rgba(21,40,31,.2);margin-bottom:8px;display:block;"></i>
                                    <p style="font-size:13px;color:rgba(21,40,31,.5);">No available slots for this date. Try another day.</p>
                                </div>
                            </template>

                            {{-- No date selected --}}
                            <template x-if="!loadingSlots && !date">
                                <div style="padding:20px;text-align:center;background:#F9FAF9;border-radius:12px;border:1px solid rgba(21,40,31,.08);">
                                    <i class="fa-regular fa-calendar" style="font-size:24px;color:rgba(21,40,31,.2);margin-bottom:8px;display:block;"></i>
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
                                <option value="">Select a subject…</option>
                                @foreach($tutor->subjects as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div>
                            <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:block;margin-bottom:6px;">Notes for tutor (optional)</label>
                            <textarea x-model="notes" rows="2" placeholder="Topics to cover, past exam areas, specific questions…"
                                      style="width:100%;padding:12px 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;resize:none;line-height:1.6;background:#fff;"
                                      onfocus="this.style.borderColor='#0A64BC'" onblur="this.style.borderColor='rgba(21,40,31,.18)'"></textarea>
                        </div>
                    </div>

                    <button @click="step=2" :disabled="!date || !time"
                            style="width:100%;height:52px;border-radius:999px;background:#0A64BC;color:#fff;font-size:15px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;"
                            :style="(!date||!time)?'opacity:.5;cursor:not-allowed;':''"
                            onmouseover="if(this.date&&this.time)this.style.background='#120A8F'" onmouseout="this.style.background='#0A64BC'">
                        Continue to Review
                        <i class="fa-solid fa-arrow-right fa-sm"></i>
                    </button>
                    <p style="text-align:center;font-size:12px;color:rgba(21,40,31,.4);margin-top:10px;">
                        <i class="fa-solid fa-rotate-left fa-xs" style="color:#10B981;"></i> 100% money-back on your first lesson
                    </p>
                </div>

                {{-- STEP 2: Review & Pay --}}
                <div x-show="step === 2" x-cloak>
                    <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:28px;margin-bottom:16px;">
                        <h3 style="font-family:'Fraunces',serif;font-size:19px;color:#15281F;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
                            <i class="fa-solid fa-receipt" style="color:#0A64BC;font-size:16px;"></i> Session Summary
                        </h3>

                        {{-- Summary rows --}}
                        <div style="background:#F9FAF9;border-radius:14px;padding:18px;margin-bottom:18px;">
                            @foreach([
                                ['fa-user','Tutor',$tutor->user->name],
                                ['fa-clock','Session','<span x-text="dur+\' min — \'+sessionName"></span>'],
                                ['fa-calendar','Date','<span x-text="date ? new Date(date).toDateString() : \'—\'"></span>'],
                                ['fa-clock','Time','<span x-text="time || \'—\'"></span> EAT'],
                            ] as [$ic,$label,$val])
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid rgba(21,40,31,.07);">
                                <span style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(21,40,31,.55);">
                                    <i class="fa-solid {{ $ic }} fa-xs" style="width:12px;text-align:center;"></i>{{ $label }}
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
                                <i class="fa-solid fa-key fa-xs"></i> After payment
                            </p>
                            <p style="font-size:12px;color:rgba(21,40,31,.6);line-height:1.65;">Both you and {{ explode(' ',$tutor->user->name)[0] }} will receive a unique session access code by email. You'll use your <strong style="color:#15281F;">STU-XXXXXX</strong> code to join the live class.</p>
                        </div>

                        {{-- Payment section --}}
                        @auth
                        <div style="background:#F9FAF9;border-radius:14px;padding:18px;">
                            <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.45);margin-bottom:12px;">
                                <i class="fa-solid fa-credit-card fa-xs" style="color:#0A64BC;"></i> Pay Securely via Paystack
                            </p>
                            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px;">
                                @foreach(['M-Pesa','Visa','Mastercard'] as $pm)
                                <span style="background:#fff;border:1px solid rgba(21,40,31,.12);border-radius:8px;padding:5px 12px;font-size:12px;font-weight:600;color:#15281F;display:flex;align-items:center;gap:5px;">
                                    <i class="fa-solid {{ $pm==='M-Pesa'?'fa-mobile-screen-button':($pm==='Visa'?'fa-credit-card':'fa-credit-card') }}" style="color:#0A64BC;font-size:11px;"></i>
                                    {{ $pm }}
                                </span>
                                @endforeach
                            </div>
                            <button @click="processPayment($data)"
                                    :disabled="processing"
                                    :style="isFree ? 'background:#10B981;' : 'background:#0A64BC;'"
                                    style="width:100%;height:52px;border-radius:999px;color:#fff;font-size:15px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;">
                                <template x-if="!processing">
                                    <span style="display:flex;align-items:center;gap:8px;">
                                        <i :class="isFree ? 'fa-solid fa-gift fa-xs' : 'fa-solid fa-lock fa-xs'"></i>
                                        <span x-text="isFree ? 'Book Free Session' : 'Pay KSh ' + price"></span>
                                    </span>
                                </template>
                                <template x-if="processing">
                                    <span style="display:flex;align-items:center;gap:8px;">
                                        <i class="fa-solid fa-spinner fa-spin fa-xs"></i> Processing…
                                    </span>
                                </template>
                            </button>
                        </div>
                        @else
                        <div style="text-align:center;padding:20px;">
                            <p style="font-size:14px;color:rgba(21,40,31,.6);margin-bottom:14px;">Sign in to complete your booking</p>
                            <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
                                <a href="{{ route('login') }}" style="display:inline-flex;align-items:center;gap:7px;padding:12px 24px;border-radius:999px;background:#0A64BC;color:#fff;font-size:14px;font-weight:600;text-decoration:none;">
                                    <i class="fa-solid fa-right-to-bracket fa-xs"></i> Sign In
                                </a>
                                <a href="{{ route('register') }}" style="display:inline-flex;align-items:center;gap:7px;padding:12px 24px;border-radius:999px;border:2px solid rgba(21,40,31,.2);color:#15281F;font-size:14px;font-weight:600;text-decoration:none;">
                                    <i class="fa-solid fa-user-plus fa-xs"></i> Create Account
                                </a>
                            </div>
                        </div>
                        @endauth

                        <div style="display:flex;justify-content:flex-start;margin-top:12px;">
                            <button @click="step=1" style="display:flex;align-items:center;gap:6px;background:none;border:none;color:rgba(21,40,31,.5);font-size:13px;cursor:pointer;font-family:inherit;padding:0;transition:color .15s;" onmouseover="this.style.color='#15281F'" onmouseout="this.style.color='rgba(21,40,31,.5)'">
                                <i class="fa-solid fa-arrow-left fa-xs"></i> Back
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 3: Confirmed --}}
                <div x-show="step === 3" x-cloak>
                    <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:40px;text-align:center;">
                        <div style="width:68px;height:68px;border-radius:50%;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
                            <i class="fa-solid fa-circle-check" style="font-size:30px;color:#10B981;"></i>
                        </div>
                        <h2 style="font-family:'Fraunces',serif;font-size:26px;color:#15281F;margin-bottom:8px;">Booking Confirmed!</h2>
                        <p style="font-size:14px;color:rgba(21,40,31,.6);margin-bottom:28px;">Payment received. Your session codes have been emailed to you. Redirecting to your session in 3 seconds…</p>

                        {{-- Session code --}}
                        <div style="background:rgba(10,100,188,.06);border:2px solid rgba(10,100,188,.25);border-radius:20px;padding:24px;margin-bottom:24px;">
                            <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(21,40,31,.45);margin-bottom:10px;display:flex;align-items:center;justify-content:center;gap:6px;">
                                <i class="fa-solid fa-key" style="color:#0A64BC;"></i> Your Session Access Code
                            </p>
                            <p style="font-family:monospace;font-size:36px;font-weight:800;color:#0A64BC;letter-spacing:.12em;line-height:1;" x-text="studentCode">STU-XXXXXX</p>
                            <p style="font-size:12px;color:rgba(21,40,31,.5);margin-top:10px;">Enter this code on session day to join your private live class</p>
                        </div>

                        {{-- What happens next --}}
                        <div style="background:#F9FAF9;border-radius:14px;padding:16px;text-align:left;margin-bottom:24px;">
                            <p style="font-size:12px;font-weight:700;color:#15281F;margin-bottom:10px;"><i class="fa-solid fa-list-check" style="color:#0A64BC;margin-right:5px;"></i> What happens next</p>
                            @foreach(['Your tutor has been notified and received their own TUT-XXXXXX code','A confirmation email with all details has been sent to you','On session day, go to your dashboard → click Join Session → enter your code','Your private classroom opens instantly in your browser — no download required'] as $step)
                            <div style="display:flex;align-items:flex-start;gap:8px;font-size:13px;color:rgba(21,40,31,.7);margin-bottom:7px;">
                                <i class="fa-solid fa-circle-check" style="color:#10B981;margin-top:2px;flex-shrink:0;font-size:11px;"></i>{{ $step }}
                            </div>
                            @endforeach
                        </div>

                        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                            <a :href="bookingRef ? '/bookings/' + bookingRef : '{{ route('dashboard') }}'"
                               style="display:inline-flex;align-items:center;gap:7px;padding:13px 28px;border-radius:999px;background:#10B981;color:#fff;font-size:15px;font-weight:700;text-decoration:none;">
                                <i class="fa-solid fa-video fa-xs"></i> Go to My Session
                            </a>
                            <a href="{{ route('dashboard') }}" style="display:inline-flex;align-items:center;gap:7px;padding:13px 24px;border-radius:999px;border:1.5px solid rgba(21,40,31,.18);color:#15281F;font-size:14px;font-weight:600;text-decoration:none;">
                                <i class="fa-solid fa-gauge-high fa-xs"></i> Dashboard
                            </a>
                            <a href="{{ route('tutors.index') }}" style="display:inline-flex;align-items:center;gap:7px;padding:13px 24px;border-radius:999px;border:1.5px solid rgba(21,40,31,.18);color:#15281F;font-size:14px;font-weight:600;text-decoration:none;">
                                <i class="fa-solid fa-magnifying-glass fa-xs"></i> Browse More Tutors
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Sticky booking card (desktop) --}}
        <div style="height:fit-content;position:sticky;top:88px;" class="book-sidebar">
            <div style="background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.1);padding:26px;box-shadow:0 4px 28px rgba(21,40,31,.08);">
                <div style="text-align:center;margin-bottom:20px;padding-bottom:18px;border-bottom:1px solid rgba(21,40,31,.08);">
                    <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.45);margin-bottom:8px;">Session Rates</div>
                    <div style="font-family:'Fraunces',serif;font-size:36px;font-weight:800;color:#0A64BC;">KSh 300</div>
                    <div style="font-size:13px;color:rgba(21,40,31,.5);">starting from per session</div>
                </div>

                {{-- Tier quick reference --}}
                <!-- <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:20px;">
                    @foreach([20=>['⚡','Quick Help',300],40=>['🎯','Guided Practice',500],60=>['🏆','Deep Dive',700]] as $mins=>[$ic,$n,$p])
                    @php $sidePrice = ($mins===20 && $isFreeFirstSession) ? 0 : $p; @endphp
                    <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:12px;background:{{ $mins===15&&$isFreeFirstSession?'rgba(16,185,129,.06)':'#F9FAF9' }};border:1px solid {{ $mins===15&&$isFreeFirstSession?'rgba(16,185,129,.2)':'rgba(21,40,31,.07)' }};position:relative;">
                        @if($mins===15 && $isFreeFirstSession)
                        <span style="position:absolute;top:-8px;right:8px;background:#10B981;color:#fff;font-size:9px;font-weight:800;padding:2px 8px;border-radius:999px;letter-spacing:.04em;">FREE</span>
                        @endif
                        <span style="font-size:16px;">{{ $ic }}</span>
                        <div style="flex:1;">
                            <div style="font-size:13px;font-weight:600;color:#15281F;">{{ $n }}</div>
                            <div style="font-size:11px;color:rgba(21,40,31,.45);">{{ $mins }} min</div>
                        </div>
                        @if($sidePrice === 0)
                        <div style="text-align:right;">
                            <div style="font-family:'Fraunces',serif;font-size:16px;font-weight:800;color:#10B981;">FREE</div>
                            <div style="font-size:10px;text-decoration:line-through;color:rgba(21,40,31,.3);">KSh {{ $p }}</div>
                        </div>
                        @else
                        <div style="font-family:'Fraunces',serif;font-size:16px;font-weight:800;color:#0A64BC;">KSh {{ $p }}</div>
                        @endif
                    </div>
                    @endforeach
                </div> -->

                @if(!auth()->check() || (auth()->check() && !in_array(auth()->user()->role, ['tutor','admin'])))
                <button onclick="document.getElementById('book-tab-btn').click();document.getElementById('tab-book').scrollIntoView({behavior:'smooth'})"
                        style="width:100%;height:52px;border-radius:999px;background:#0A64BC;color:#fff;font-size:15px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:12px;"
                        onmouseover="this.style.background='#120A8F';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#0A64BC';this.style.transform='translateY(0)'">
                    <i class="fa-solid fa-calendar-check fa-sm"></i> Book a Session
                </button>
                @endif

                <div style="display:flex;flex-direction:column;gap:7px;">
                    @foreach(['fa-certificate green TSC-Registered & Verified','fa-lock green Secure, private classroom','fa-rotate-left green 100% money-back first lesson','fa-key green Session code sent by email'] as $item)
                    @php [$ic,,$text] = explode(' ',$item,3); @endphp
                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:rgba(21,40,31,.6);">
                        <i class="fa-solid fa-{{ $ic }}" style="color:#10B981;font-size:11px;width:13px;text-align:center;"></i>{{ $text }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.show-grid{grid-template-columns:1fr!important;}
@media(min-width:1024px){.show-grid{grid-template-columns:1fr 360px!important;}}
.book-sidebar{display:none!important;}
@media(min-width:1024px){.book-sidebar{display:block!important;}}
.desk-price{display:none!important;}
@media(min-width:768px){.desk-price{display:flex!important;}}
</style>

@include('partials.footer')

<script src="https://js.paystack.co/v1/inline.js"></script>
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

function processPayment(data) {
    if (!data || !data.date || !data.time) {
        alert('Please select a date and time.');
        return;
    }

    data.processing = true;

    // Safety fallback: if processing takes more than 45 seconds, unstick the UI
    const safetyTimeout = setTimeout(() => {
        if (data.processing) {
            clearTimeout(safetyTimeout);
        data.processing = false;
            data.step = 3;
            alert('Payment may still be processing. Check your dashboard — your session will appear once confirmed.');
        }
    }, 45000);

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
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'ok' && res.free) {
            clearTimeout(safetyTimeout);
        data.processing = false;
            data.studentCode = res.student_code || '';
            data.bookingRef  = res.booking_uuid || '';
            data.step = 3;
            if (res.booking_url) {
                setTimeout(() => { window.location.href = res.booking_url; }, 3000);
            }
            return;
        }
        if (res.status === 'ok') {
            const handler = PaystackPop.setup({
                key: '{{ config('paystack.public_key') }}',
                email: '{{ auth()->user()?->email }}',
                amount: data.price * 100,
                currency: 'KES',
                ref: res.reference,
                metadata: { booking_id: res.booking_id },
                onSuccess: function(transaction) {
                    // Confirm booking after payment - with 20s timeout
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 20000);

                    fetch('/bookings/confirm', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ reference: transaction.reference }),
                        signal: controller.signal
                    })
                    .then(r => {
                        clearTimeout(timeoutId);
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(confirmRes => {
                        clearTimeout(safetyTimeout);
        data.processing = false;
                        if (confirmRes.status === 'ok') {
                            data.studentCode = confirmRes.student_code || '';
                            data.bookingRef  = confirmRes.booking_uuid || '';
                            data.step = 3;
                            if (confirmRes.booking_url) {
                                setTimeout(() => {
                                    window.location.href = confirmRes.booking_url;
                                }, 3000);
                            }
                        } else {
                            // Payment went through but confirm returned non-ok
                            // Still move to step 3 — webhook will confirm async
                            data.step = 3;
                            console.warn('Confirm warning:', confirmRes.message);
                        }
                    })
                    .catch((err) => {
                        clearTimeout(timeoutId);
                        // Payment succeeded on Paystack side — don't show error to user
                        // The Paystack webhook will confirm the booking asynchronously
                        clearTimeout(safetyTimeout);
        data.processing = false;
                        data.step = 3;
                        console.warn('Confirm fetch failed/timed out, relying on webhook:', err);
                        // Show a gentle message that payment was received
                        alert('Payment received successfully! Your session is being confirmed. You will receive your session code shortly.');
                    });
                },
                onCancel: function() {
                    clearTimeout(safetyTimeout);
        data.processing = false;
                }
            });
            handler.openIframe();
        } else {
            clearTimeout(safetyTimeout);
        data.processing = false;
            alert(res.message || 'Could not process booking. Please try again.');
        }
    })
    .catch(err => {
        clearTimeout(safetyTimeout);
        data.processing = false;
        console.error(err);
        alert('Network error. Please try again.');
    });
}
</script>
@livewireScripts
</body>
</html>
