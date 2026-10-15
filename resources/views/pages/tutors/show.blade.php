<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>{{ $tutor->user->name }} – Mwalimu</title>
<meta name="description" content="{{ Str::limit($tutor->bio ?? 'Verified tutor on Mwalimu Kenya', 160) }}">
<style>
.profile-hero{background:var(--ink);padding:44px 0 0;position:relative;overflow:hidden;}
.profile-hero::before{content:'';position:absolute;top:-80px;right:-80px;width:320px;height:320px;background:rgba(109,40,217,.12);border-radius:50%;}
.tab-btn{padding:14px 20px;font-size:14px;font-weight:600;background:none;border:none;cursor:pointer;color:rgba(255,255,255,.5);border-bottom:3px solid transparent;transition:all .15s;font-family:inherit;}
.tab-btn.on{color:#fff;border-bottom-color:var(--violet);}
.avail-row{display:flex;align-items:center;gap:14px;padding:12px 16px;border-radius:12px;border:1px solid var(--hair);margin-bottom:6px;}
.avail-row.on{background:rgba(109,40,217,.04);border-color:rgba(109,40,217,.15);}
.review-card{border-bottom:1px solid rgba(21,40,31,.06);padding:20px 0;}
.review-card:first-child{padding-top:0;}
.review-card:last-child{border-bottom:none;}
.book-widget{background:#fff;border-radius:20px;border:1px solid var(--hair);padding:24px;position:sticky;top:88px;box-shadow:0 4px 24px rgba(21,40,31,.08);}
</style>
</head>
<body>
@php
$colors=['#6D28D9','#10B981','#E8522A','#D4A017','#3B82F6','#EC4899'];
$avatarColor=$colors[$tutor->id%count($colors)];
$reviews=$tutor->reviews()->where('is_visible',true)->with('student')->latest()->paginate(6);
$prices=[15=>300,30=>500,45=>680,60=>800];
$sessionNames=[15=>'Quick Help',30=>'Concept Check',45=>'Guided Practice',60=>'Deep Dive'];
@endphp

@include('partials.nav')

{{-- PROFILE HERO --}}
<div class="profile-hero">
    <div class="w" style="position:relative;z-index:1;">
        <div style="margin-bottom:8px;">
            <a href="{{ route('tutors.index') }}" style="font-size:13px;color:rgba(255,255,255,.5);display:inline-flex;align-items:center;gap:6px;transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
                <i class="fa-solid fa-arrow-left fa-xs"></i> All Tutors
            </a>
        </div>
        <div style="display:flex;align-items:flex-start;gap:22px;padding-bottom:0;">
            <div style="width:88px;height:88px;border-radius:22px;background:{{ $avatarColor }};display:flex;align-items:center;justify-content:center;font-size:34px;font-weight:700;color:#fff;flex-shrink:0;border:3px solid rgba(255,255,255,.15);">{{ substr($tutor->user->name,0,1) }}</div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:8px;">
                    <h1 style="font-size:clamp(24px,4vw,36px);color:#fff;">{{ $tutor->user->name }}</h1>
                    <span class="badge badge-m"><i class="fa-solid fa-certificate fa-xs"></i> TSC Verified</span>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:5px;margin-bottom:10px;">
                    @foreach($tutor->subjects as $s)
                    <span style="background:rgba(255,255,255,.12);color:rgba(255,255,255,.85);padding:3px 12px;border-radius:999px;font-size:12px;font-weight:500;">{{ $s->name }}</span>
                    @endforeach
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:16px;font-size:13px;color:rgba(255,255,255,.6);">
                    <span><i class="fa-solid fa-star" style="color:var(--gold);"></i> <strong style="color:#fff;">{{ number_format($tutor->rating_avg,1) }}</strong> ({{ $tutor->total_reviews }} reviews)</span>
                    <span><i class="fa-solid fa-chalkboard fa-xs"></i> {{ $tutor->total_sessions }} sessions</span>
                    <span><i class="fa-solid fa-clock fa-xs"></i> {{ $tutor->experience_years }} years exp.</span>
                    @if($tutor->city)<span><i class="fa-solid fa-location-dot fa-xs"></i> {{ $tutor->city }}, Kenya</span>@endif
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div style="display:flex;margin-top:20px;border-top:1px solid rgba(255,255,255,.08);" x-data="{ tab: 'about' }">
            <button class="tab-btn" :class="tab==='about'?'on':''" @click="tab='about'">About</button>
            <button class="tab-btn" :class="tab==='availability'?'on':''" @click="tab='availability'">Availability</button>
            <button class="tab-btn" :class="tab==='reviews'?'on':''" @click="tab='reviews'">Reviews ({{ $tutor->total_reviews }})</button>
        </div>
    </div>
</div>

{{-- MAIN --}}
<div class="w" style="padding-top:32px;padding-bottom:60px;">
    <div style="display:grid;grid-template-columns:1fr;gap:24px;" class="show-g" x-data="{ tab: 'about' }">

        {{-- LEFT CONTENT --}}
        <div>
            {{-- Mobile tabs --}}
            <div style="display:flex;gap:6px;margin-bottom:20px;flex-wrap:wrap;" class="mob-tabs">
                <button @click="tab='about'" :style="tab==='about'?'background:var(--violet);color:#fff;border-color:var(--violet);':''" style="padding:8px 18px;border-radius:999px;border:1.5px solid var(--hair);background:#fff;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s;color:var(--ink);">About</button>
                <button @click="tab='availability'" :style="tab==='availability'?'background:var(--violet);color:#fff;border-color:var(--violet);':''" style="padding:8px 18px;border-radius:999px;border:1.5px solid var(--hair);background:#fff;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s;color:var(--ink);">Availability</button>
                <button @click="tab='reviews'" :style="tab==='reviews'?'background:var(--violet);color:#fff;border-color:var(--violet);':''" style="padding:8px 18px;border-radius:999px;border:1.5px solid var(--hair);background:#fff;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s;color:var(--ink);">Reviews</button>
            </div>

            {{-- ABOUT --}}
            <div x-show="tab==='about'">
                <div class="card card-p" style="margin-bottom:18px;">
                    <h2 style="font-size:19px;color:var(--ink);margin-bottom:14px;"><i class="fa-solid fa-user fa-sm" style="color:var(--violet);margin-right:8px;"></i>About {{ explode(' ',$tutor->user->name)[0] }}</h2>
                    <p style="font-size:15px;color:var(--ink);line-height:1.85;">{{ $tutor->bio ?? 'This tutor hasn\'t added a bio yet.' }}</p>
                    @if($tutor->languages)
                    <div style="margin-top:18px;padding-top:18px;border-top:1px solid var(--hair);">
                        <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:8px;">Languages</p>
                        <div style="display:flex;flex-wrap:wrap;gap:6px;">
                            @foreach($tutor->languages as $lang)
                            <span style="background:#F4F6F4;color:var(--ink);padding:4px 12px;border-radius:999px;font-size:13px;font-weight:500;">{{ $lang }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:18px;" class="stats-bar">
                    @foreach([[number_format($tutor->total_sessions),'Sessions','fa-chalkboard','#6D28D9'],[$tutor->total_reviews,'Reviews','fa-star','#D4A017'],[$tutor->experience_years.'yr','Experience','fa-clock','#10B981'],['< 2hr','Response','fa-reply','#E8522A']] as [$v,$l,$ic,$c])
                    <div class="card" style="padding:16px;text-align:center;">
                        <i class="fa-solid {{ $ic }}" style="color:{{ $c }};font-size:18px;margin-bottom:8px;display:block;"></i>
                        <div style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);">{{ $v }}</div>
                        <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $l }}</div>
                    </div>
                    @endforeach
                </div>
                <style>.stats-bar{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:640px){.stats-bar{grid-template-columns:repeat(4,1fr)!important;}}</style>
                <div class="card card-p">
                    <h3 style="font-size:17px;color:var(--ink);margin-bottom:14px;"><i class="fa-solid fa-book-open fa-sm" style="color:var(--violet);margin-right:8px;"></i>Subjects Taught</h3>
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;" class="subj-g">
                        @foreach($tutor->subjects as $s)
                        <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#F9FAF9;border-radius:12px;border:1px solid var(--hair);">
                            <span style="width:8px;height:8px;border-radius:50%;background:var(--violet);flex-shrink:0;"></span>
                            <span style="font-size:14px;font-weight:500;color:var(--ink);">{{ $s->name }}</span>
                        </div>
                        @endforeach
                    </div>
                    <style>.subj-g{grid-template-columns:1fr!important;}@media(min-width:640px){.subj-g{grid-template-columns:repeat(2,1fr)!important;}}</style>
                </div>
            </div>

            {{-- AVAILABILITY --}}
            <div x-show="tab==='availability'" x-cloak>
                <div class="card card-p">
                    <h2 style="font-size:19px;color:var(--ink);margin-bottom:6px;"><i class="fa-solid fa-calendar-week fa-sm" style="color:var(--violet);margin-right:8px;"></i>Weekly Availability</h2>
                    <p style="font-size:13px;color:var(--muted);margin-bottom:20px;">All times East Africa Time (EAT, UTC+3)</p>
                    @php $avails=$tutor->availabilities()->where('is_active',true)->orderBy('day_of_week')->get(); $days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']; @endphp
                    @if($avails->isEmpty())
                    <div style="text-align:center;padding:40px;color:var(--muted);">
                        <i class="fa-regular fa-calendar-xmark" style="font-size:36px;margin-bottom:10px;display:block;color:var(--hair);"></i>
                        <p>Availability not set yet. Book anyway — the tutor will confirm.</p>
                    </div>
                    @else
                    @foreach($days as $i => $day)
                    @php $av=$avails->firstWhere('day_of_week',$i); @endphp
                    <div class="avail-row {{ $av?'on':'' }}">
                        <span style="font-weight:600;font-size:14px;color:var(--ink);width:100px;flex-shrink:0;">{{ $day }}</span>
                        @if($av)
                        <span style="font-size:14px;font-weight:500;color:var(--violet);"><i class="fa-regular fa-clock fa-xs" style="margin-right:5px;"></i>{{ substr($av->start_time,0,5) }} – {{ substr($av->end_time,0,5) }}</span>
                        <span class="badge badge-m" style="margin-left:auto;"><i class="fa-solid fa-check fa-xs"></i> Available</span>
                        @else
                        <span style="font-size:13px;color:var(--muted);">Not available</span>
                        <span class="badge badge-dark" style="margin-left:auto;">Off</span>
                        @endif
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>

            {{-- REVIEWS --}}
            <div x-show="tab==='reviews'" x-cloak>
                <div class="card card-p" style="margin-bottom:16px;">
                    <div style="display:flex;align-items:center;gap:32px;flex-wrap:wrap;">
                        <div style="text-align:center;">
                            <div style="font-family:'Fraunces',serif;font-size:56px;font-weight:800;color:var(--ink);line-height:1;">{{ number_format($tutor->rating_avg,1) }}</div>
                            <div style="color:var(--gold);font-size:20px;letter-spacing:2px;margin:6px 0;">
                                @for($i=1;$i<=5;$i++)<i class="fa-solid fa-star" style="opacity:{{ $i<=$tutor->rating_avg?'1':'0.2' }};"></i>@endfor
                            </div>
                            <div style="font-size:13px;color:var(--muted);">{{ $tutor->total_reviews }} reviews</div>
                        </div>
                        <div style="flex:1;min-width:200px;">
                            @foreach([5,4,3,2,1] as $star)
                            @php $cnt=$tutor->reviews()->where('rating',$star)->count(); $pct=$tutor->total_reviews>0?round($cnt/$tutor->total_reviews*100):0; @endphp
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                                <span style="font-size:12px;color:var(--muted);width:10px;text-align:right;">{{ $star }}</span>
                                <i class="fa-solid fa-star" style="color:var(--gold);font-size:11px;"></i>
                                <div style="flex:1;height:7px;background:#F4F6F4;border-radius:999px;overflow:hidden;">
                                    <div style="height:100%;width:{{ $pct }}%;background:var(--gold);border-radius:999px;"></div>
                                </div>
                                <span style="font-size:12px;color:var(--muted);width:24px;">{{ $cnt }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="card card-p">
                    @forelse($reviews as $review)
                    <div class="review-card">
                        <div style="display:flex;align-items:flex-start;gap:14px;">
                            <div style="width:38px;height:38px;border-radius:11px;background:#{{ substr(md5($review->student->email),0,6) }};display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:#fff;flex-shrink:0;">{{ substr($review->student->name,0,1) }}</div>
                            <div style="flex:1;">
                                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:6px;">
                                    <span style="font-weight:700;font-size:14px;color:var(--ink);">{{ $review->student->name }}</span>
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <div>@for($i=1;$i<=5;$i++)<i class="fa-solid fa-star" style="color:{{ $i<=$review->rating?'var(--gold)':'#E5E7EB' }};font-size:12px;"></i>@endfor</div>
                                        <span style="font-size:12px;color:var(--muted);">{{ $review->created_at->format('M Y') }}</span>
                                    </div>
                                </div>
                                @if($review->comment)<p style="font-size:14px;color:var(--ink);line-height:1.75;">{{ $review->comment }}</p>@endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:36px;color:var(--muted);">
                        <i class="fa-regular fa-star" style="font-size:32px;margin-bottom:10px;display:block;color:var(--hair);"></i>
                        <p>No reviews yet. Be the first to book!</p>
                    </div>
                    @endforelse
                </div>
                @if($reviews->hasPages())
                <div style="margin-top:16px;display:flex;justify-content:center;">{{ $reviews->links() }}</div>
                @endif
            </div>
        </div>

        {{-- BOOKING WIDGET --}}
        <div>
            <div class="book-widget">
                <div style="text-align:center;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--hair);">
                    <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:6px;">Fixed Session Rates</div>
                    <div style="font-family:'Fraunces',serif;font-size:13px;color:var(--muted);">All sessions at fixed KSh pricing</div>
                </div>

                {{-- Duration selector --}}
                <div style="margin-bottom:18px;" x-data="{ dur: 60 }">
                    <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:8px;"><i class="fa-solid fa-clock fa-xs" style="color:var(--violet);margin-right:4px;"></i>Choose Duration</p>
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:6px;">
                        @foreach([15=>['Quick Help',300],30=>['Concept Check',500],45=>['Guided Practice',680],60=>['Deep Dive',800]] as $mins=>[$name,$price])
                        <button @click="dur={{ $mins }}"
                                :style="dur==={{ $mins }}?'border-color:var(--violet);background:rgba(109,40,217,.07);':''"
                                style="padding:9px 4px;border-radius:11px;border:1.5px solid var(--hair);background:#fff;cursor:pointer;text-align:center;transition:all .15s;font-family:inherit;">
                            <div style="font-size:12px;font-weight:700;color:var(--ink);">{{ $mins }}m</div>
                            <div style="font-size:10px;color:var(--violet);font-weight:600;margin-top:1px;">{{ $name }}</div>
                            <div style="font-size:11px;color:var(--muted);margin-top:1px;">KSh {{ $price }}</div>
                        </button>
                        @endforeach
                    </div>
                    <div style="text-align:center;margin-top:12px;padding:12px;background:rgba(109,40,217,.05);border-radius:12px;border:1px solid rgba(109,40,217,.1);">
                        <span style="font-family:'Fraunces',serif;font-size:26px;font-weight:800;color:var(--violet);">KSh <span x-text="{15:300,30:500,45:680,60:800}[dur]"></span></span>
                        <span style="font-size:13px;color:var(--muted);"> / session</span>
                    </div>
                </div>

                @auth
                <a href="{{ route('tutors.index') }}" class="btn btn-v" style="width:100%;justify-content:center;padding:14px;font-size:15px;margin-bottom:10px;">
                    <i class="fa-solid fa-calendar-check"></i> Book this Tutor
                </a>
                @else
                <a href="{{ route('register') }}" class="btn btn-v" style="width:100%;justify-content:center;padding:14px;font-size:15px;margin-bottom:10px;">
                    <i class="fa-solid fa-user-plus"></i> Sign Up to Book
                </a>
                <a href="{{ route('login') }}" class="btn btn-o" style="width:100%;justify-content:center;padding:12px;font-size:14px;margin-bottom:10px;">
                    <i class="fa-solid fa-right-to-bracket"></i> Already have an account?
                </a>
                @endauth

                <div style="background:#F9FAF9;border-radius:12px;padding:14px;">
                    <p style="font-size:12px;font-weight:700;color:var(--ink);margin-bottom:8px;"><i class="fa-solid fa-shield-check" style="color:var(--mint);margin-right:5px;"></i>How sessions work</p>
                    @foreach(['Pay securely via M-Pesa or card','Unique session code sent by email','Enter code to join private Jitsi classroom','100% money-back on first lesson'] as $pt)
                    <div style="display:flex;align-items:flex-start;gap:7px;font-size:12px;color:var(--muted);margin-bottom:5px;">
                        <i class="fa-solid fa-circle-check" style="color:var(--mint);margin-top:1px;flex-shrink:0;font-size:11px;"></i>{{ $pt }}
                    </div>
                    @endforeach
                </div>
            </div>

            <div style="display:flex;gap:8px;margin-top:10px;">
                <button onclick="navigator.share?navigator.share({title:'{{ $tutor->user->name }} on Mwalimu',url:window.location.href}):navigator.clipboard.writeText(window.location.href)" class="btn btn-sm" style="flex:1;justify-content:center;background:#F4F6F4;color:var(--ink);border:none;">
                    <i class="fa-solid fa-share-nodes"></i> Share
                </button>
                @auth
                <button class="btn btn-sm" style="flex:1;justify-content:center;background:#F4F6F4;color:var(--muted);border:none;" title="Save tutor">
                    <i class="fa-regular fa-heart"></i> Save
                </button>
                @endauth
            </div>
        </div>
    </div>
</div>
<style>.show-g{grid-template-columns:1fr!important;}@media(min-width:1024px){.show-g{grid-template-columns:1fr 360px!important;}}
.mob-tabs{display:flex!important;}@media(min-width:1024px){.mob-tabs{display:none!important;}}</style>

@include('partials.footer')
@livewireScripts
</body>
</html>
