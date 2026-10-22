<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Find a Tutor – Mwalimu</title>
<meta name="description" content="Browse TSC-registered tutors across all Kenyan curriculum subjects. Book sessions from KSh 300.">
<style>
.hero-band{background:var(--ink);padding:52px 0;position:relative;overflow:hidden;}
.hero-band::after{content:'';position:absolute;bottom:-60px;right:-60px;width:280px;height:280px;background:rgba(10,100,188,.15);border-radius:50%;}
.filter-card{background:#fff;border-radius:20px;border:1px solid var(--hair);padding:20px;margin-bottom:20px;box-shadow:0 2px 16px rgba(21,40,31,.06);}
.f-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px;display:block;}
.f-input{height:42px;padding:0 13px;border-radius:10px;border:1.5px solid var(--hair);background:#fff;font-size:13.5px;font-family:inherit;color:var(--ink);outline:none;transition:border-color .15s;width:100%;}
.f-input:focus{border-color:var(--trust-blue);}
select.f-input{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' fill='none'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2315281F' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;padding-right:32px;}
.tc{background:#fff;border-radius:20px;border:1px solid var(--hair);padding:20px;transition:all .2s;}
.tc:hover{transform:translateY(-4px);box-shadow:0 12px 36px rgba(21,40,31,.1);border-color:rgba(10,100,188,.2);}
.level-tag{display:inline-flex;align-items:center;gap:4px;padding:2px 9px;border-radius:999px;font-size:10px;font-weight:700;background:rgba(21,40,31,.06);color:var(--muted);}
.dur-pills{display:grid;grid-template-columns:repeat(4,1fr);gap:5px;margin:12px 0;}
.dp{text-align:center;border:1.5px solid var(--hair);border-radius:9px;padding:5px 3px;transition:all .15s;cursor:pointer;}
.dp:hover{border-color:var(--trust-blue);background:rgba(10,100,188,.04);}
.dp-min{font-size:11px;font-weight:700;color:var(--ink);}
.dp-price{font-size:10px;color:var(--muted);}
.filter-tag{display:inline-flex;align-items:center;gap:5px;background:rgba(10,100,188,.1);color:var(--trust-blue);padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600;}
</style>
</head>
<body>
@php
use App\Models\Category; use App\Models\Subject; use App\Models\TutorProfile;
$search=$req=request('search',''); $catId=request('category',''); $subjId=request('subject','');
$minP=request('min_price',''); $maxP=request('max_price',''); $minR=request('min_rating','');
$sort=request('sort','rating_avg');
$categories=Category::active()->with('subjects')->get();
$subjects=$catId?Subject::where('category_id',$catId)->active()->get():collect();
$q=TutorProfile::approved()->with(['user','subjects'])
    ->when($search,fn($x)=>$x->search($search))
    ->when($subjId,fn($x)=>$x->whereHas('subjects',fn($s)=>$s->where('subjects.id',$subjId)))
    ->when($catId&&!$subjId,fn($x)=>$x->whereHas('subjects',fn($s)=>$s->where('category_id',$catId)))
    ->when($minP,fn($x)=>$x->where('hourly_rate','>=',$minP))
    ->when($maxP,fn($x)=>$x->where('hourly_rate','<=',$maxP))
    ->when($minR,fn($x)=>$x->where('rating_avg','>=',$minR));
$q=$sort==='price_asc'?$q->orderBy('hourly_rate'):($sort==='sessions'?$q->orderByDesc('total_sessions'):$q->orderByDesc('rating_avg'));
$tutors=$q->paginate(12)->withQueryString();
$colors=['#0A64BC','#10B981','#E8522A','#EEBE15','#3B82F6','#EC4899','#0EA5E9','#8B5CF6'];
$prices=[20=>300,40=>500,60=>700];
$sessionNames=[20=>'Quick Help',40=>'Guided Practice',60=>'Deep Dive'];
@endphp

@include('partials.nav')

{{-- HERO --}}
<div class="hero-band">
    <div class="w" style="position:relative;z-index:1;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:32px;flex-wrap:wrap;">
            <div>
                <div class="badge badge-white" style="margin-bottom:16px;"><i class="fa-solid fa-certificate"></i> TSC-Registered Tutors Only</div>
                <h1 style="font-size:clamp(30px,5vw,52px);color:#fff;margin-bottom:12px;">Find your perfect tutor</h1>
                <p style="font-size:16px;color:rgba(255,255,255,.6);max-width:460px;">TSC verified tutors across the Kenyan curriculum. Sessions from KSh 300.</p>
            </div>
            <!-- <div style="display:flex;flex-wrap:wrap;gap:8px;align-self:flex-end;max-width:480px;">
                @foreach($categories->take(6) as $cat)
                <a href="{{ route('tutors.index') }}?category={{ $cat->id }}"
                   style="display:flex;align-items:center;gap:5px;background:{{ $catId==$cat->id ? 'rgba(255,255,255,.25)' : 'rgba(255,255,255,.08)' }};border:1px solid rgba(255,255,255,.15);border-radius:999px;padding:6px 14px;font-size:13px;font-weight:500;color:rgba(255,255,255,.85);transition:all .15s;"
                   onmouseover="this.style.background='rgba(255,255,255,.18)'" onmouseout="this.style.background='{{ $catId==$cat->id ? 'rgba(255,255,255,.25)' : 'rgba(255,255,255,.08)' }}'">
                    {{ $cat->icon }} {{ $cat->name }}
                </a>
                @endforeach
            </div> -->
        </div>
    </div>
</div>

<div class="w" style="padding-top:28px;padding-bottom:60px;">

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('tutors.index') }}">
        <div class="filter-card">
            <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
                <div style="flex:2;min-width:190px;">
                    <span class="f-label"><i class="fa-solid fa-magnifying-glass fa-xs" style="color:var(--trust-blue);"></i> Search</span>
                    <div style="position:relative;">
                        <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:12px;pointer-events:none;"></i>
                        <input type="text" name="search" value="{{ $search }}" class="f-input" placeholder="Tutor name or subject…" style="padding-left:34px;">
                    </div>
                </div>
                <div style="min-width:160px;">
                    <span class="f-label"><i class="fa-solid fa-layer-group fa-xs" style="color:var(--trust-blue);"></i> Category</span>
                    <select name="category" class="f-input" onchange="this.form.submit()">
                        <option value="">All categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $catId==$cat->id?'selected':'' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if($subjects->isNotEmpty())
                <div style="min-width:150px;">
                    <span class="f-label"><i class="fa-solid fa-book-open fa-xs" style="color:var(--trust-blue);"></i> Subject</span>
                    <select name="subject" class="f-input">
                        <option value="">All subjects</option>
                        @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ $subjId==$s->id?'selected':'' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div style="min-width:140px;">
                    <span class="f-label"><i class="fa-solid fa-arrow-up-wide-short fa-xs" style="color:var(--trust-blue);"></i> Sort</span>
                    <select name="sort" class="f-input">
                        <option value="rating_avg" {{ $sort==='rating_avg'?'selected':'' }}>Highest Rated</option>
                        <option value="price_asc" {{ $sort==='price_asc'?'selected':'' }}>Lowest Price</option>
                        <option value="sessions" {{ $sort==='sessions'?'selected':'' }}>Most Sessions</option>
                    </select>
                </div>
                <div style="display:flex;gap:6px;align-items:flex-end;">
                    <button type="submit" class="btn btn-v btn-sm" style="height:42px;"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                    @if($search||$catId||$subjId||$minP||$maxP||$minR)
                    <a href="{{ route('tutors.index') }}" class="btn btn-sm" style="background:#F4F6F4;color:var(--ink);border:none;height:42px;"><i class="fa-solid fa-xmark"></i> Clear</a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- Active tags --}}
    @if($search||$catId||$subjId||$minP||$maxP||$minR)
    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:16px;">
        @if($search)<span class="filter-tag"><i class="fa-solid fa-magnifying-glass fa-xs"></i>"{{ $search }}"</span>@endif
        @if($catId)<span class="filter-tag"><i class="fa-solid fa-layer-group fa-xs"></i>{{ $categories->find($catId)?->name }}</span>@endif
        @if($minR)<span class="filter-tag"><i class="fa-solid fa-star fa-xs"></i>★ {{ $minR }}+</span>@endif
        @if($minP)<span class="filter-tag">From KSh {{ number_format($minP) }}</span>@endif
        @if($maxP)<span class="filter-tag">To KSh {{ number_format($maxP) }}</span>@endif
    </div>
    @endif

    {{-- Results bar --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
        <p style="font-size:14px;color:var(--muted);"><strong style="color:var(--ink);">{{ $tutors->total() }}</strong> tutor{{ $tutors->total()!=1?'s':'' }} found</p>
        <div style="display:flex;flex-wrap:wrap;gap:5px;">
            @foreach($categories->take(5) as $cat)
            <a href="{{ route('tutors.index') }}?category={{ $cat->id }}"
               style="padding:4px 12px;border-radius:999px;font-size:12px;font-weight:500;border:1px solid {{ $catId==$cat->id?'var(--trust-blue)':'var(--hair)' }};background:{{ $catId==$cat->id?'var(--trust-blue)':'#fff' }};color:{{ $catId==$cat->id?'#fff':'var(--ink)' }};transition:all .15s;">
                {{ $cat->icon }} {{ $cat->name }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- Tutors grid --}}
    @if($tutors->isEmpty())
    <div class="card" style="padding:64px;text-align:center;">
        <div style="font-size:48px;margin-bottom:14px;"><i class="fa-solid fa-magnifying-glass" style="color:var(--hair);"></i></div>
        <h3 style="font-size:22px;color:var(--ink);margin-bottom:8px;">No tutors found</h3>
        <p style="color:var(--muted);margin-bottom:20px;">Try adjusting your filters or search term.</p>
        <a href="{{ route('tutors.index') }}" class="btn btn-v"><i class="fa-solid fa-arrow-left"></i> Clear Filters</a>
    </div>
    @else
    <div style="display:grid;grid-template-columns:1fr;gap:16px;" class="tgrid">
        @foreach($tutors as $tutor)
        @php $col=$colors[($tutor->id-1)%count($colors)]; @endphp
        <div class="tc">
            <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:12px;">
                <div style="width:58px;height:58px;border-radius:16px;background:{{ $col }};display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;color:#fff;flex-shrink:0;">{{ substr($tutor->user->name,0,1) }}</div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
                        <div>
                            <!-- <div style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);">{{ $tutor->user->name }}</div> -->
                             <div style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);">
    {{ implode(' ', array_slice(explode(' ', trim($tutor->user->name)), 0, 2)) }}
</div>
                            <div style="display:flex;align-items:center;gap:6px;margin-top:3px;">
                                <span style="font-size:11px;font-weight:600;color:var(--mint);"><i class="fa-solid fa-certificate fa-xs"></i> TSC Verified</span>
                                <span style="color:var(--muted);font-size:11px;">· {{ $tutor->experience_years }}yr exp</span>
                            </div>
                        </div>
                        <!-- <div style="text-align:right;flex-shrink:0;">
                            <div style="font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:var(--ink);">KSh {{ number_format($tutor->hourly_rate) }}</div>
                            <div style="font-size:11px;color:var(--muted);">/hr</div>
                        </div> -->
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:6px;">
                        @foreach($tutor->subjects->take(3) as $s)
                        <span class="badge badge-v" style="font-size:10px;">{{ $s->name }}</span>
                        @endforeach
                        @if($tutor->subjects->count()>3)<span class="badge badge-dark" style="font-size:10px;">+{{ $tutor->subjects->count()-3 }}</span>@endif
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;margin-top:7px;font-size:12px;color:var(--muted);">
                        <span style="color:var(--gold);font-weight:700;"><i class="fa-solid fa-star fa-xs"></i> {{ number_format($tutor->rating_avg,1) }}</span>
                        <span>({{ $tutor->total_reviews }})</span>
                        <span>·</span><span>{{ $tutor->total_sessions }} sessions</span>
                        <!-- @if($tutor->city)<span>·</span><span><i class="fa-solid fa-location-dot fa-xs"></i> {{ $tutor->city }}</span>@endif -->
                    </div>
                </div>
            </div>
            @if($tutor->bio)
            <p style="font-size:13px;color:var(--muted);margin-bottom:12px;line-height:1.65;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $tutor->bio }}</p>
            @endif
            <!-- <div class="dur-pills">
                @foreach([20=>['Quick Help',300],40=>['Guided Practice',500],60=>['Deep Dive',700]] as $mins=>[$name,$price])
                @php
                $isFreeForUser = $mins === 15 && auth()->check()
                    ? \App\Models\TutorProfile::isFreeFirstSession(auth()->id(), $tutor->user_id)
                    : ($mins === 15); // show FREE to guests too
                @endphp
                <div class="dp" title="{{ $name }}" style="{{ $isFreeForUser ? 'border-color:rgba(16,185,129,.4);background:rgba(16,185,129,.04);' : '' }}">
                    <div class="dp-min">{{ $mins }}m</div>
                    <div style="font-size:9px;font-weight:700;margin:1px 0;line-height:1.2;color:{{ $isFreeForUser ? '#10B981' : 'var(--trust-blue)' }}">{{ $name }}</div>
                    @if($isFreeForUser)
                    <div style="font-size:9px;font-weight:800;color:#10B981;letter-spacing:.02em;">FREE</div>
                    @else
                    <div class="dp-price">KSh {{ $price }}</div>
                    @endif
                </div>
                @endforeach
            </div> -->
            <div style="display:flex;gap:8px;">
                <a href="{{ route('tutors.show', $tutor->id) }}" class="btn btn-v btn-sm" style="flex:1;justify-content:center;"><i class="fa-solid fa-calendar-check"></i> View & Book</a>
                @auth
                <a href="{{ route('tutors.show', $tutor->id) }}" class="btn btn-sm" style="background:#F4F6F4;color:var(--muted);border:none;" title="Save tutor">
                    <i class="fa-regular fa-heart"></i>
                </a>
                @endauth
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($tutors->hasPages())
    <div style="margin-top:32px;display:flex;justify-content:center;gap:5px;flex-wrap:wrap;">
        @if($tutors->onFirstPage())
        <span class="btn btn-sm" style="background:#F4F6F4;color:var(--muted);border:none;pointer-events:none;"><i class="fa-solid fa-chevron-left fa-xs"></i> Prev</span>
        @else
        <a href="{{ $tutors->previousPageUrl() }}" class="btn btn-sm btn-o"><i class="fa-solid fa-chevron-left fa-xs"></i> Prev</a>
        @endif
        @foreach($tutors->getUrlRange(max(1,$tutors->currentPage()-2),min($tutors->lastPage(),$tutors->currentPage()+2)) as $page=>$url)
        <a href="{{ $url }}" class="btn btn-sm" style="background:{{ $page==$tutors->currentPage()?'var(--trust-blue)':'#fff' }};color:{{ $page==$tutors->currentPage()?'#fff':'var(--ink)' }};border:1.5px solid {{ $page==$tutors->currentPage()?'var(--trust-blue)':'var(--hair)' }};">{{ $page }}</a>
        @endforeach
        @if($tutors->hasMorePages())
        <a href="{{ $tutors->nextPageUrl() }}" class="btn btn-sm btn-o">Next <i class="fa-solid fa-chevron-right fa-xs"></i></a>
        @else
        <span class="btn btn-sm" style="background:#F4F6F4;color:var(--muted);border:none;pointer-events:none;">Next <i class="fa-solid fa-chevron-right fa-xs"></i></span>
        @endif
    </div>
    @endif
    @endif
</div>
<style>.tgrid{grid-template-columns:1fr!important;}@media(min-width:640px){.tgrid{grid-template-columns:repeat(2,1fr)!important;}}@media(min-width:1024px){.tgrid{grid-template-columns:repeat(3,1fr)!important;}}</style>

@include('partials.footer')
@livewireScripts
</body>
</html>
