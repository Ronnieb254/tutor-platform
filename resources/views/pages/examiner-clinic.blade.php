<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Examiner's Clinic – Mwalimu</title>
<meta name="description" content="Learn directly from Kenya's national examiners and markers. Group sessions per paper (PP1, PP2, PP3). KSh 500 per session. Max 30 students per session.">
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
                    <i class="fa-solid fa-star fa-xs"></i> The Examiner's Clinic
                </div>
                <h1 style="font-family:'Fraunces',serif;font-size:clamp(34px,5vw,58px);color:#fff;margin-bottom:16px;line-height:1.1;">
                    Learn from the experts who <span style="color:#EEBE15;">set the exams.</span>
                </h1>
                <p style="font-size:17px;color:rgba(255,255,255,.65);max-width:560px;margin-bottom:28px;line-height:1.85;">
                    Most revision resources focus on <em style="font-style:italic;">what</em> to study. The Examiner's Clinic focuses on <strong style="color:#fff;">how to answer questions</strong> — delivered by national examiners and markers, structured per paper (PP1, PP2, PP3).
                </p>
                <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:32px;">
                    <a href="#clinics" style="display:inline-flex;align-items:center;gap:8px;padding:13px 32px;border-radius:999px;background:#EEBE15;color:#15281F;font-size:15px;font-weight:700;text-decoration:none;transition:all .18s;" onmouseover="this.style.opacity='.9';this.style.transform='translateY(-1px)'" onmouseout="this.style.opacity='1';this.style.transform='translateY(0)'">
                        <i class="fa-solid fa-calendar-check fa-sm"></i> Book a Clinic Spot
                    </a>
                   
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:20px;">
                    @foreach([['fa-sterling-sign','KSh 500','Per 60-min session'],['fa-users','Max 30','Learners per session'],['fa-calendar-week','Daily','Every subject'],['fa-shield-check','Secure','All sessions']] as [$ic,$val,$lbl])
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
                <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.4);margin-bottom:18px;">What each clinic covers</p>
                @foreach(['Exam strategy & question time management','High-frequency exam questions by topic','Examiner traps — mistakes that lose marks','Marking scheme analysis — how marks are awarded','Common student errors and how to avoid them','How KNEC examiners grade papers'] as $item)
                <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:12px;">
                    <i class="fa-solid fa-circle-check" style="color:#10B981;margin-top:2px;flex-shrink:0;font-size:13px;"></i>
                    <span style="font-size:14px;color:rgba(255,255,255,.82);">{{ $item }}</span>
                </div>
                @endforeach
                <div style="margin-top:20px;padding-top:18px;border-top:1px solid rgba(255,255,255,.1);text-align:center;">
                    <span style="font-family:'Fraunces',serif;font-size:36px;font-weight:800;color:#EEBE15;">KSh 500</span>
                    <span style="font-size:14px;color:rgba(255,255,255,.45);margin-left:8px;">/ 60-minute session</span>
                </div>
            </div>
        </div>
    </div>
    <style>.hero-g{grid-template-columns:1fr!important;}@media(min-width:1024px){.hero-g{grid-template-columns:1fr 440px!important;}}</style>
</section>

{{-- VS COMPARISON --}}
<section style="padding:72px 0;background:rgba(21,40,31,.02);">
    <div style="max-width:860px;margin:0 auto;padding:0 24px;">
        <div style="text-align:center;margin-bottom:40px;">
            <h2 style="font-family:'Fraunces',serif;font-size:clamp(26px,4vw,38px);color:#15281F;margin-bottom:10px;">Why the Examiner's Clinic?</h2>
            <p style="font-size:16px;color:rgba(21,40,31,.55);">A fraction of the cost of private tuition, in a focused, high-impact group environment.</p>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;" class="vs-g">
            <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:24px;border-left:4px solid rgba(21,40,31,.12);">
                <h3 style="font-size:15px;font-weight:700;color:rgba(21,40,31,.5);margin-bottom:16px;display:flex;align-items:center;gap:7px;"><i class="fa-solid fa-book fa-sm"></i> Regular Revision</h3>
                @foreach(['Focuses on what to study','Content-heavy, little strategy','No insight into examiner expectations','Expensive private tuition per session'] as $item)
                <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;font-size:13px;color:rgba(21,40,31,.6);">
                    <i class="fa-solid fa-xmark" style="color:#DC2626;margin-top:2px;flex-shrink:0;"></i>{{ $item }}
                </div>
                @endforeach
            </div>
            <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:24px;border-left:4px solid #EEBE15;">
                <h3 style="font-size:15px;font-weight:700;color:#15281F;margin-bottom:16px;display:flex;align-items:center;gap:7px;"><i class="fa-solid fa-star fa-sm" style="color:#EEBE15;"></i> Examiner's Clinic</h3>
                @foreach(['Focuses on HOW to answer','Strategic, marks-focused teaching','Direct insight into KNEC marking criteria','KSh 500 for a 60-minute group session'] as $item)
                <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;font-size:13px;color:#15281F;">
                    <i class="fa-solid fa-circle-check" style="color:#10B981;margin-top:2px;flex-shrink:0;"></i>{{ $item }}
                </div>
                @endforeach
            </div>
        </div>
        <style>.vs-g{grid-template-columns:1fr!important;}@media(min-width:640px){.vs-g{grid-template-columns:1fr 1fr!important;}}</style>
    </div>
</section>

{{-- UPCOMING CLINICS --}}
<section id="clinics" style="padding:72px 0;">
    <div style="max-width:1360px;margin:0 auto;padding:0 24px;">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:16px;">
            <div>
                <h2 style="font-family:'Fraunces',serif;font-size:clamp(26px,4vw,38px);color:#15281F;margin-bottom:6px;">Upcoming Clinics</h2>
                <p style="color:rgba(21,40,31,.55);">Structured per paper (PP1, PP2, PP3). Max 30 students per session — new sessions open automatically when one fills up.</p>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <!-- <form method="GET" action="{{ route('examiner-clinic') }}" style="background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.08);padding:16px 20px;margin-bottom:24px;display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;box-shadow:0 2px 12px rgba(21,40,31,.04);">
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
                <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.55);margin-bottom:5px;display:block;"><i class="fa-solid fa-file-lines fa-xs" style="color:#0A64BC;"></i> Paper</label>
                <select name="paper" onchange="this.form.submit()" style="width:100%;height:42px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:13.5px;font-family:inherit;color:#15281F;outline:none;background:#fff;appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' fill='none'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2315281F' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 12px center;padding-right:32px;">
                    <option value="">All papers</option>
                    @foreach($paperTypes as $code => $label)
                    <option value="{{ $code }}" {{ $paperType === $code ? 'selected' : '' }}>{{ $label }} ({{ $code }})</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:6px;">
                <button type="submit" style="height:42px;padding:0 18px;border-radius:10px;background:#0A64BC;color:#fff;font-size:13px;font-weight:600;border:none;cursor:pointer;font-family:inherit;"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
                @if($search || $subjectId || $paperType)
                <a href="{{ route('examiner-clinic') }}" style="height:42px;padding:0 16px;border-radius:10px;background:#F4F6F4;color:#15281F;font-size:13px;font-weight:500;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-xmark fa-xs"></i> Clear</a>
                @endif
            </div>
        </form> -->

        <!-- {{-- RESULTS COUNT --}}
        <p style="font-size:14px;color:rgba(21,40,31,.55);margin-bottom:16px;"><strong style="color:#15281F;">{{ $clinics->total() }}</strong> clinic{{ $clinics->total() != 1 ? 's' : '' }} found</p> -->

        @if($clinics->isEmpty())
        <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:64px;text-align:center;">
            <div style="font-size:48px;margin-bottom:14px;"><i class="fa-solid fa-calendar-xmark" style="color:rgba(21,40,31,.12);"></i></div>
            <h3 style="font-family:'Fraunces',serif;font-size:22px;color:#15281F;margin-bottom:8px;">No clinics scheduled</h3>
            <p style="color:rgba(21,40,31,.55);margin-bottom:20px;">No clinics match your filters. Try adjusting your search or check back soon — new clinics are added weekly.</p>
            <a href="{{ route('examiner-clinic') }}" style="display:inline-flex;align-items:center;gap:7px;padding:11px 24px;border-radius:999px;background:#0A64BC;color:#fff;font-size:14px;font-weight:600;text-decoration:none;"><i class="fa-solid fa-arrow-left fa-sm"></i> Clear Filters</a>
        </div>
        @else
        <div style="display:flex;flex-direction:column;gap:14px;">
            @foreach($clinics as $clinic)
            @php
                $isFull = $clinic->isFull();
                $enrolledCount = $clinic->enrolledCount();
                $maxStudents = $clinic->max_students;
                $fillPct = $maxStudents > 0 ? round(($enrolledCount / $maxStudents) * 100) : 0;
            @endphp
            <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:24px;display:flex;align-items:flex-start;gap:18px;flex-wrap:wrap;{{ $isFull ? 'opacity:.75;' : '' }}box-shadow:0 2px 12px rgba(21,40,31,.04);">
                <div style="width:54px;height:54px;border-radius:16px;background:rgba(212,160,23,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-solid fa-chalkboard-user" style="color:#EEBE15;font-size:22px;"></i>
                </div>
                <div style="flex:1;min-width:200px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:8px;">
                        <div>
                            <h3 style="font-family:'Fraunces',serif;font-size:18px;color:#15281F;">{{ $clinic->title }}</h3>
                            @if($clinic->batch_number > 1)
                            <span style="font-size:11px;color:rgba(21,40,31,.4);font-weight:600;">Session {{ $clinic->batch_number }} (auto-opened)</span>
                            @endif
                        </div>
                        <span style="font-family:'Fraunces',serif;font-size:22px;font-weight:800;color:#EEBE15;flex-shrink:0;">KSh {{ number_format($clinic->price) }}</span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:10px;">
                        @if($clinic->tutor)
                        <span style="background:rgba(10,100,188,.08);color:#0A64BC;padding:3px 11px;border-radius:999px;font-size:11px;font-weight:600;display:flex;align-items:center;gap:5px;"><i class="fa-solid fa-user fa-xs"></i>{{ $clinic->tutor->name }}</span>
                        @endif
                        @if($clinic->subject)
                        <span style="background:rgba(21,40,31,.06);color:#15281F;padding:3px 11px;border-radius:999px;font-size:11px;font-weight:600;display:flex;align-items:center;gap:5px;"><i class="fa-solid fa-book-open fa-xs"></i>{{ $clinic->subject->name }}</span>
                        @endif
                        @if($clinic->paper_type)
                        <span style="background:rgba(212,160,23,.1);color:#EEBE15;padding:3px 11px;border-radius:999px;font-size:11px;font-weight:700;display:flex;align-items:center;gap:5px;"><i class="fa-solid fa-file-lines fa-xs"></i>{{ $clinic->paper_type }}</span>
                        @endif
                        @if($isFull)
                        <span style="background:#FEE2E2;color:#991B1B;padding:3px 11px;border-radius:999px;font-size:11px;font-weight:700;display:flex;align-items:center;gap:4px;"><i class="fa-solid fa-lock fa-xs"></i> FULL</span>
                        @endif
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:16px;font-size:13px;color:rgba(21,40,31,.55);align-items:center;">
                        <span><i class="fa-regular fa-calendar fa-xs" style="color:#0A64BC;margin-right:4px;"></i>{{ $clinic->scheduled_at->format('D, M j \a\t g:ia') }}</span>
                        <span><i class="fa-solid fa-clock fa-xs" style="color:#0A64BC;margin-right:4px;"></i>{{ $clinic->duration_minutes }} min</span>
                        <span style="color:{{ $isFull ? '#991B1B' : '#10B981' }};font-weight:600;">
                            <i class="fa-solid fa-users fa-xs" style="margin-right:4px;"></i>{{ $enrolledCount }}/{{ $maxStudents }} enrolled
                        </span>
                    </div>
                    {{-- Capacity bar --}}
                    <div style="margin-top:10px;width:100%;max-width:300px;height:6px;background:rgba(21,40,31,.06);border-radius:999px;overflow:hidden;">
                        <div style="width:{{ $fillPct }}%;height:100%;background:{{ $isFull ? '#E8522A' : ($fillPct > 70 ? '#EEBE15' : '#10B981') }};border-radius:999px;transition:width .3s;"></div>
                    </div>
                </div>
                <div style="flex-shrink:0;">
                    @auth
                    @if($clinic->isEnrolledBy(auth()->user()))
                    <span style="background:rgba(16,185,129,.1);color:#10B981;border:1px solid rgba(16,185,129,.3);padding:10px 20px;border-radius:999px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;">
                        <i class="fa-solid fa-check fa-xs"></i> Enrolled
                    </span>
                    @elseif($isFull)
                    <span style="background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;padding:10px 20px;border-radius:999px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;">
                        <i class="fa-solid fa-lock fa-xs"></i> Session Full
                    </span>
                    @else
                    <a href="{{ route('clinic.checkout', $clinic) }}"
                       style="display:inline-flex;align-items:center;gap:7px;padding:11px 24px;border-radius:999px;background:#EEBE15;color:#15281F;font-size:14px;font-weight:700;text-decoration:none;transition:all .15s;" onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                        <i class="fa-solid fa-ticket fa-sm"></i> Book Spot — KSh {{ number_format($clinic->price) }}
                    </a>
                    @endif
                    @else
                    <a href="{{ route('register') }}" style="display:inline-flex;align-items:center;gap:7px;padding:11px 24px;border-radius:999px;background:#EEBE15;color:#15281F;font-size:14px;font-weight:700;text-decoration:none;">
                        <i class="fa-solid fa-ticket fa-sm"></i> Book Spot
                    </a>
                    @endauth
                </div>
            </div>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        @if($clinics->hasPages())
        <div style="margin-top:32px;display:flex;justify-content:center;gap:5px;flex-wrap:wrap;">
            @if($clinics->onFirstPage())
            <span style="padding:8px 16px;border-radius:10px;background:#F4F6F4;color:rgba(21,40,31,.35);font-size:13px;font-weight:500;border:none;"><i class="fa-solid fa-chevron-left fa-xs"></i> Prev</span>
            @else
            <a href="{{ $clinics->previousPageUrl() }}" style="padding:8px 16px;border-radius:10px;background:#fff;color:#15281F;font-size:13px;font-weight:500;border:1.5px solid rgba(21,40,31,.1);text-decoration:none;"><i class="fa-solid fa-chevron-left fa-xs"></i> Prev</a>
            @endif
            @foreach($clinics->getUrlRange(max(1, $clinics->currentPage()-2), min($clinics->lastPage(), $clinics->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}" style="padding:8px 16px;border-radius:10px;background:{{ $page == $clinics->currentPage() ? '#0A64BC' : '#fff' }};color:{{ $page == $clinics->currentPage() ? '#fff' : '#15281F' }};font-size:13px;font-weight:600;border:1.5px solid {{ $page == $clinics->currentPage() ? '#0A64BC' : 'rgba(21,40,31,.1)' }};text-decoration:none;">{{ $page }}</a>
            @endforeach
            @if($clinics->hasMorePages())
            <a href="{{ $clinics->nextPageUrl() }}" style="padding:8px 16px;border-radius:10px;background:#fff;color:#15281F;font-size:13px;font-weight:500;border:1.5px solid rgba(21,40,31,.1);text-decoration:none;">Next <i class="fa-solid fa-chevron-right fa-xs"></i></a>
            @else
            <span style="padding:8px 16px;border-radius:10px;background:#F4F6F4;color:rgba(21,40,31,.35);font-size:13px;font-weight:500;border:none;">Next <i class="fa-solid fa-chevron-right fa-xs"></i></span>
            @endif
        </div>
        @endif
        @endif
    </div>
</section>

{{-- FOR EXAMINERS --}}
<section id="examiners" style="padding:72px 0;background:rgba(21,40,31,.03);">
    <div style="max-width:960px;margin:0 auto;padding:0 24px;">
        <div style="text-align:center;margin-bottom:40px;">
            <h2 style="font-family:'Fraunces',serif;font-size:clamp(26px,4vw,38px);color:#15281F;margin-bottom:10px;">For Tutors & Examiners</h2>
            <p style="font-size:16px;color:rgba(21,40,31,.55);">Monetize your experience with zero administrative friction and weekly payouts upon request.</p>
        </div>
        <div style="display:grid;grid-template-columns:1fr;gap:14px;" class="ex-g">
            @foreach([
                ['fa-bolt','Weekly Payouts on Request','Earnings from your clinic sessions are paid out weekly via M-Pesa. Simply request your payout from your dashboard — no delays, no follow-ups.'],
                ['fa-arrow-trend-up','Build Authority & Reputation','Hosting featured clinics puts your professional profile in front of hundreds of parents and learners, positioning you as a top-tier tutor for 1-on-1 bookings.'],
                ['fa-headset','Zero Administrative Overhead','You bring the expertise. Mwalimu handles student acquisition, M-Pesa ticketing, automated reminders, and classroom setup.'],
            ] as [$icon,$title,$desc])
            <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:24px;display:flex;gap:16px;align-items:flex-start;">
                <div style="width:48px;height:48px;background:rgba(10,100,188,.08);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-solid {{ $icon }}" style="color:#0A64BC;font-size:18px;"></i>
                </div>
                <div>
                    <h3 style="font-family:'Fraunces',serif;font-size:17px;color:#15281F;margin-bottom:6px;">{{ $title }}</h3>
                    <p style="font-size:14px;color:rgba(21,40,31,.6);line-height:1.75;">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
       
        <style>.ex-g{grid-template-columns:1fr!important;}@media(min-width:768px){.ex-g{grid-template-columns:repeat(3,1fr)!important;}}</style>
    </div>
</section>

@include('partials.footer')
@livewireScripts
</body>
</html>
