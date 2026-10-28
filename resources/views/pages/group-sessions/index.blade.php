<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Group Sessions – Mwalimu</title>
<meta name="description" content="Live group classes hosted by top tutors. Learn together, pay less per seat. Browse upcoming sessions and book your spot.">
</head>
<body>
@include('partials.nav')

{{-- HERO --}}
<section style="background:linear-gradient(135deg,#0D1F14,#15281F);padding:72px 0;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-80px;right:-80px;width:340px;height:340px;background:rgba(212,160,23,.08);border-radius:50%;pointer-events:none;"></div>
    <div style="position:absolute;bottom:-60px;left:-40px;width:240px;height:240px;background:rgba(10,100,188,.1);border-radius:50%;pointer-events:none;"></div>
    <div style="max-width:1360px;margin:0 auto;padding:0 24px;position:relative;z-index:1;">
        <div style="display:grid;grid-template-columns:1fr;gap:48px;align-items:center;" class="hero-g">
            <div>
                <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(212,160,23,.2);color:#FCD34D;border:1px solid rgba(212,160,23,.3);padding:6px 18px;border-radius:999px;font-size:13px;font-weight:700;margin-bottom:22px;">
                    <i class="fa-solid fa-users fa-xs"></i> Live Cohorts
                </div>
                <h1 style="font-family:'Fraunces',serif;font-size:clamp(34px,5vw,58px);color:#fff;margin-bottom:16px;line-height:1.1;">
                    Learn together, <span style="color:#EEBE15;">pay less.</span>
                </h1>
                <p style="font-size:17px;color:rgba(255,255,255,.65);max-width:560px;margin-bottom:28px;line-height:1.85;">
                    Small live classes hosted by our top mentors. Same energy as 1-on-1, lower per-seat price. Book a single session or join a multi-week series.
                </p>
                <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:32px;">
                    <a href="#sessions" style="display:inline-flex;align-items:center;gap:8px;padding:13px 32px;border-radius:999px;background:#EEBE15;color:#15281F;font-size:15px;font-weight:700;text-decoration:none;transition:all .18s;" onmouseover="this.style.opacity='.9';this.style.transform='translateY(-1px)'" onmouseout="this.style.opacity='1';this.style.transform='translateY(0)'">
                        <i class="fa-solid fa-calendar-check fa-sm"></i> Browse Sessions
                    </a>
                    @auth
                    <a href="{{ route('group-sessions.my-sessions') }}" style="display:inline-flex;align-items:center;gap:8px;padding:13px 28px;border-radius:999px;background:rgba(255,255,255,.1);color:#fff;border:1.5px solid rgba(255,255,255,.25);font-size:15px;font-weight:600;text-decoration:none;transition:all .18s;" onmouseover="this.style.background='rgba(255,255,255,.18)'" onmouseout="this.style.background='rgba(255,255,255,.1)'">
                        <i class="fa-solid fa-ticket fa-sm"></i> My Sessions
                    </a>
                    @else
                    <a href="{{ route('register') }}?role=tutor" style="display:inline-flex;align-items:center;gap:8px;padding:13px 28px;border-radius:999px;background:rgba(255,255,255,.1);color:#fff;border:1.5px solid rgba(255,255,255,.25);font-size:15px;font-weight:600;text-decoration:none;transition:all .18s;" onmouseover="this.style.background='rgba(255,255,255,.18)'" onmouseout="this.style.background='rgba(255,255,255,.1)'">
                        <i class="fa-solid fa-chalkboard-user fa-sm"></i> Host a Class
                    </a>
                    @endauth
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:20px;">
                    @foreach([['fa-users','Small groups','Up to 30 learners'],['fa-clock','60 minutes','Per session'],['fa-video','Live on Jitsi','HD video class'],['fa-arrow-rotate-left','Series ok','Multi-week cohorts']] as [$ic,$val,$lbl])
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:34px;height:34px;background:rgba(212,160,23,.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid {{ $ic }}" style="color:#EEBE15;font-size:13px;"></i>
                        </div>
                        <div>
                            <div style="font-size:14px;font-weight:700;color:#fff;">{{ $val }}</div>
                            <div style="font-size:11px;color:rgba(255,255,255,.45);">{{ $lbl }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:24px;padding:28px;">
                <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.4);margin-bottom:18px;">How group sessions work</p>
                @foreach(['Browse upcoming live classes by subject','Book your seat — pay once per session','Join the Jitsi room at the scheduled time','Learn together with up to 30 peers','Series? Enroll in each session individually'] as $item)
                <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:12px;">
                    <i class="fa-solid fa-circle-check" style="color:#10B981;margin-top:2px;flex-shrink:0;font-size:13px;"></i>
                    <span style="font-size:14px;color:rgba(255,255,255,.82);">{{ $item }}</span>
                </div>
                @endforeach
                <div style="margin-top:20px;padding-top:18px;border-top:1px solid rgba(255,255,255,.1);text-align:center;">
                    <span style="font-family:'Fraunces',serif;font-size:36px;font-weight:800;color:#EEBE15;">From KSh 500</span>
                    <span style="font-size:14px;color:rgba(255,255,255,.45);margin-left:8px;">/ per session</span>
                </div>
            </div>
        </div>
    </div>
    <style>.hero-g{grid-template-columns:1fr!important;}@media(min-width:1024px){.hero-g{grid-template-columns:1fr 440px!important;}}</style>
</section>

{{-- UPCOMING GROUP SESSIONS --}}
<section id="sessions" style="padding:72px 0;">
    <div style="max-width:1360px;margin:0 auto;padding:0 24px;">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:16px;">
            <div>
                <h2 style="font-family:'Fraunces',serif;font-size:clamp(26px,4vw,38px);color:#15281F;margin-bottom:6px;">Upcoming Group Sessions</h2>
                <p style="color:rgba(21,40,31,.55);">Live cohort classes. Max 30 learners per session — book your seat before it fills up.</p>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <form method="GET" action="{{ route('group-sessions.index') }}" style="background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.08);padding:16px 20px;margin-bottom:24px;display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;box-shadow:0 2px 12px rgba(21,40,31,.04);">
            <div style="flex:2;min-width:180px;">
                <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.55);margin-bottom:5px;display:block;"><i class="fa-solid fa-magnifying-glass fa-xs" style="color:#0A64BC;"></i> Search</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Subject or title…" style="width:100%;height:42px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:13.5px;font-family:inherit;color:#15281F;outline:none;transition:border-color .15s;" onfocus="this.style.borderColor='#0A64BC'" onblur="this.style.borderColor='rgba(21,40,31,.1)'">
            </div>
            <div style="min-width:160px;">
                <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.55);margin-bottom:5px;display:block;"><i class="fa-solid fa-book-open fa-xs" style="color:#0A64BC;"></i> Subject</label>
                <select name="subject" onchange="this.form.submit()" style="width:100%;height:42px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:13.5px;font-family:inherit;color:#15281F;outline:none;background:#fff;appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' fill='none'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2315281F' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 12px center;padding-right:32px;">
                    <option value="">All subjects</option>
                    @foreach($subjects as $s)
                    <option value="{{ $s->id }}" {{ (string)$subjectId === (string)$s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:140px;">
                <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.55);margin-bottom:5px;display:block;"><i class="fa-solid fa-arrow-down-wide-short fa-xs" style="color:#0A64BC;"></i> Sort</label>
                <select name="sort" onchange="this.form.submit()" style="width:100%;height:42px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:13.5px;font-family:inherit;color:#15281F;outline:none;background:#fff;appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' fill='none'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2315281F' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 12px center;padding-right:32px;">
                    <option value="date" {{ $sort === 'date' ? 'selected' : '' }}>Date (soonest)</option>
                    <option value="subject" {{ $sort === 'subject' ? 'selected' : '' }}>Subject A–Z</option>
                    <option value="price_low" {{ $sort === 'price_low' ? 'selected' : '' }}>Price (low to high)</option>
                    <option value="price_high" {{ $sort === 'price_high' ? 'selected' : '' }}>Price (high to low)</option>
                </select>
            </div>
            <div style="display:flex;gap:6px;">
                <button type="submit" style="height:42px;padding:0 18px;border-radius:10px;background:#0A64BC;color:#fff;font-size:13px;font-weight:600;border:none;cursor:pointer;font-family:inherit;"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
                @if($search || $subjectId)
                <a href="{{ route('group-sessions.index') }}" style="height:42px;padding:0 16px;border-radius:10px;background:#F4F6F4;color:#15281F;font-size:13px;font-weight:500;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-xmark fa-xs"></i> Clear</a>
                @endif
            </div>
        </form>

        <p style="font-size:14px;color:rgba(21,40,31,.55);margin-bottom:16px;"><strong style="color:#15281F;">{{ $sessions->total() }}</strong> session{{ $sessions->total() != 1 ? 's' : '' }} found</p>

        @if($sessions->isEmpty())
        <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:64px;text-align:center;">
            <div style="font-size:48px;margin-bottom:14px;"><i class="fa-solid fa-calendar-xmark" style="color:rgba(21,40,31,.12);"></i></div>
            <h3 style="font-family:'Fraunces',serif;font-size:22px;color:#15281F;margin-bottom:8px;">No group sessions scheduled</h3>
            <p style="color:rgba(21,40,31,.55);margin-bottom:20px;">No sessions match your filters. Try adjusting your search or check back soon.</p>
            <a href="{{ route('group-sessions.index') }}" style="display:inline-flex;align-items:center;gap:7px;padding:11px 24px;border-radius:999px;background:#0A64BC;color:#fff;font-size:14px;font-weight:600;text-decoration:none;"><i class="fa-solid fa-arrow-left fa-sm"></i> Clear Filters</a>
        </div>
        @else
        <div style="display:grid;grid-template-columns:1fr;gap:16px;" class="gs-grid">
            @foreach($sessions as $session)
            @php
                $isFull = $session->isFull();
                $enrolledCount = $session->enrolledCount();
                $maxStudents = $session->max_students;
                $fillPct = $maxStudents > 0 ? round(($enrolledCount / $maxStudents) * 100) : 0;
                $isSeries = \Schema::hasColumn('examiner_clinics','series_id') && $session->series_id && ($session->total_sessions ?? 1) > 1;
            @endphp
            <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:24px;display:flex;flex-direction:column;gap:14px;{{ $isFull ? 'opacity:.75;' : '' }}box-shadow:0 2px 12px rgba(21,40,31,.04);transition:transform .15s,box-shadow .15s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(21,40,31,.08)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 12px rgba(21,40,31,.04)'">
                {{-- Top: subject + series badge --}}
                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:7px;">
                        @if($session->subject)
                        <span style="background:rgba(21,40,31,.06);color:#15281F;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-book-open fa-xs"></i>{{ $session->subject->name }}</span>
                        @endif
                        @if($isSeries)
                        <span style="background:rgba(212,160,23,.12);color:#EEBE15;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-layer-group fa-xs"></i>Session {{ $session->session_number }}/{{ $session->total_sessions }}</span>
                        @endif
                    </div>
                    <span style="font-family:'Fraunces',serif;font-size:24px;font-weight:800;color:#EEBE15;">KSh {{ number_format($session->price) }}</span>
                </div>

                {{-- Title --}}
                <div>
                    <h3 style="font-family:'Fraunces',serif;font-size:19px;color:#15281F;line-height:1.3;margin-bottom:6px;">
                        <a href="{{ route('group-sessions.show', $session) }}" style="color:inherit;text-decoration:none;" onmouseover="this.style.color='#0A64BC'" onmouseout="this.style.color='#15281F'">{{ $session->title }}</a>
                    </h3>
                    @if($session->tutor)
                    <p style="font-size:13px;color:rgba(21,40,31,.55);display:flex;align-items:center;gap:6px;">
                        <i class="fa-solid fa-user fa-xs" style="color:#0A64BC;"></i> with {{ $session->tutor->name }}
                    </p>
                    @endif
                </div>

                {{-- Meta --}}
                <div style="display:flex;flex-wrap:wrap;gap:14px;font-size:13px;color:rgba(21,40,31,.55);">
                    <span><i class="fa-regular fa-calendar fa-xs" style="color:#0A64BC;margin-right:4px;"></i>{{ $session->scheduled_at->format('D, M j · g:ia') }}</span>
                    <span><i class="fa-solid fa-clock fa-xs" style="color:#0A64BC;margin-right:4px;"></i>{{ $session->duration_minutes }}m</span>
                </div>

                {{-- Capacity bar --}}
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                        <span style="font-size:12px;color:{{ $isFull ? '#991B1B' : '#10B981' }};font-weight:600;">
                            <i class="fa-solid fa-users fa-xs" style="margin-right:4px;"></i>{{ $enrolledCount }}/{{ $maxStudents }} enrolled
                        </span>
                        @if($isFull)
                        <span style="font-size:11px;font-weight:700;color:#991B1B;"><i class="fa-solid fa-lock fa-xs"></i> FULL</span>
                        @elseif($fillPct > 70)
                        <span style="font-size:11px;font-weight:700;color:#EEBE15;">Almost full</span>
                        @endif
                    </div>
                    <div style="width:100%;height:6px;background:rgba(21,40,31,.06);border-radius:999px;overflow:hidden;">
                        <div style="width:{{ $fillPct }}%;height:100%;background:{{ $isFull ? '#E8522A' : ($fillPct > 70 ? '#EEBE15' : '#10B981') }};border-radius:999px;transition:width .3s;"></div>
                    </div>
                </div>

                {{-- CTA --}}
                <div style="display:flex;gap:8px;margin-top:4px;">
                    @auth
                    @if($session->isEnrolledBy(auth()->user()))
                    <a href="{{ route('group-sessions.show', $session) }}" style="flex:1;display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:11px 20px;border-radius:999px;background:rgba(16,185,129,.1);color:#10B981;border:1px solid rgba(16,185,129,.3);font-size:13px;font-weight:600;text-decoration:none;">
                        <i class="fa-solid fa-check fa-xs"></i> Enrolled — View
                    </a>
                    @elseif($isFull)
                    <span style="flex:1;display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:11px 20px;border-radius:999px;background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;font-size:13px;font-weight:600;">
                        <i class="fa-solid fa-lock fa-xs"></i> Session Full
                    </span>
                    @else
                    <form method="POST" action="{{ route('group-sessions.enroll', $session) }}" style="flex:1;">
                        @csrf
                        <button type="submit" style="width:100%;display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:11px 20px;border-radius:999px;background:#EEBE15;color:#15281F;font-size:13px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .15s;" onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                            <i class="fa-solid fa-ticket fa-sm"></i> Book Seat — KSh {{ number_format($session->price) }}
                        </button>
                    </form>
                    @endif
                    @else
                    <a href="{{ route('register') }}" style="flex:1;display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:11px 20px;border-radius:999px;background:#EEBE15;color:#15281F;font-size:13px;font-weight:700;text-decoration:none;">
                        <i class="fa-solid fa-ticket fa-sm"></i> Book Seat
                    </a>
                    @endauth
                    <a href="{{ route('group-sessions.show', $session) }}" style="display:inline-flex;align-items:center;justify-content:center;padding:11px 18px;border-radius:999px;background:rgba(10,100,188,.08);color:#0A64BC;border:1px solid rgba(10,100,188,.2);font-size:13px;font-weight:600;text-decoration:none;">
                        <i class="fa-solid fa-arrow-right fa-sm"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        <style>.gs-grid{grid-template-columns:1fr!important;}@media(min-width:640px){.gs-grid{grid-template-columns:repeat(2,1fr)!important;}}@media(min-width:1024px){.gs-grid{grid-template-columns:repeat(3,1fr)!important;}}</style>

        {{-- PAGINATION --}}
        @if($sessions->hasPages())
        <div style="margin-top:32px;display:flex;justify-content:center;gap:5px;flex-wrap:wrap;">
            @if($sessions->onFirstPage())
            <span style="padding:8px 16px;border-radius:10px;background:#F4F6F4;color:rgba(21,40,31,.35);font-size:13px;font-weight:500;border:none;"><i class="fa-solid fa-chevron-left fa-xs"></i> Prev</span>
            @else
            <a href="{{ $sessions->previousPageUrl() }}" style="padding:8px 16px;border-radius:10px;background:#fff;color:#15281F;font-size:13px;font-weight:500;border:1.5px solid rgba(21,40,31,.1);text-decoration:none;"><i class="fa-solid fa-chevron-left fa-xs"></i> Prev</a>
            @endif
            @foreach($sessions->getUrlRange(max(1, $sessions->currentPage()-2), min($sessions->lastPage(), $sessions->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}" style="padding:8px 16px;border-radius:10px;background:{{ $page == $sessions->currentPage() ? '#0A64BC' : '#fff' }};color:{{ $page == $sessions->currentPage() ? '#fff' : '#15281F' }};font-size:13px;font-weight:600;border:1.5px solid {{ $page == $sessions->currentPage() ? '#0A64BC' : 'rgba(21,40,31,.1)' }};text-decoration:none;">{{ $page }}</a>
            @endforeach
            @if($sessions->hasMorePages())
            <a href="{{ $sessions->nextPageUrl() }}" style="padding:8px 16px;border-radius:10px;background:#fff;color:#15281F;font-size:13px;font-weight:500;border:1.5px solid rgba(21,40,31,.1);text-decoration:none;">Next <i class="fa-solid fa-chevron-right fa-xs"></i></a>
            @else
            <span style="padding:8px 16px;border-radius:10px;background:#F4F6F4;color:rgba(21,40,31,.35);font-size:13px;font-weight:500;border:none;">Next <i class="fa-solid fa-chevron-right fa-xs"></i></span>
            @endif
        </div>
        @endif
        @endif
    </div>
</section>

@include('partials.footer')
@livewireScripts
</body>
</html>
