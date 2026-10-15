<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Examiner's Clinic – Mwalimu</title>
<meta name="description" content="Learn directly from Kenya's national examiners and markers. Group sessions on exam strategy, marking schemes, and KNEC tips. KSh 1,000 per session.">
</head>
<body>
@php
use App\Models\ExaminerClinic;
$clinics  = ExaminerClinic::upcoming()->with(['tutor','subject'])->orderBy('scheduled_at')->get();
$subjects = \App\Models\Subject::active()->with('category')->get();
@endphp
@include('partials.nav')

{{-- HERO --}}
<section style="background:linear-gradient(135deg,#0D1F14,#15281F);padding:72px 0;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-80px;right:-80px;width:340px;height:340px;background:rgba(212,160,23,.08);border-radius:50%;pointer-events:none;"></div>
    <div style="position:absolute;bottom:-60px;left:-40px;width:240px;height:240px;background:rgba(109,40,217,.1);border-radius:50%;pointer-events:none;"></div>
    <div style="max-width:1360px;margin:0 auto;padding:0 24px;position:relative;z-index:1;">
        <div style="display:grid;grid-template-columns:1fr;gap:48px;align-items:center;" class="hero-g">
            <div>
                <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(212,160,23,.2);color:#FCD34D;border:1px solid rgba(212,160,23,.3);padding:6px 18px;border-radius:999px;font-size:13px;font-weight:700;margin-bottom:22px;">
                    <i class="fa-solid fa-star fa-xs"></i> The Examiner's Clinic
                </div>
                <h1 style="font-family:'Fraunces',serif;font-size:clamp(34px,5vw,58px);color:#fff;margin-bottom:16px;line-height:1.1;">
                    Learn from the experts who <span style="color:#D4A017;">set the exams.</span>
                </h1>
                <p style="font-size:17px;color:rgba(255,255,255,.65);max-width:560px;margin-bottom:28px;line-height:1.85;">
                    Most revision resources focus on <em style="font-style:italic;">what</em> to study. The Examiner's Clinic focuses on <strong style="color:#fff;">how to answer questions</strong> — delivered by national examiners and markers from top-performing schools.
                </p>
                <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:32px;">
                    <a href="#clinics" style="display:inline-flex;align-items:center;gap:8px;padding:13px 32px;border-radius:999px;background:#D4A017;color:#15281F;font-size:15px;font-weight:700;text-decoration:none;transition:all .18s;" onmouseover="this.style.opacity='.9';this.style.transform='translateY(-1px)'" onmouseout="this.style.opacity='1';this.style.transform='translateY(0)'">
                        <i class="fa-solid fa-calendar-check fa-sm"></i> Book a Clinic Spot
                    </a>
                    <a href="{{ route('register') }}?role=tutor" style="display:inline-flex;align-items:center;gap:8px;padding:13px 28px;border-radius:999px;background:rgba(255,255,255,.1);color:#fff;border:1.5px solid rgba(255,255,255,.25);font-size:15px;font-weight:600;text-decoration:none;transition:all .18s;" onmouseover="this.style.background='rgba(255,255,255,.18)'" onmouseout="this.style.background='rgba(255,255,255,.1)'">
                        <i class="fa-solid fa-chalkboard-user fa-sm"></i> Host a Clinic
                    </a>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:20px;">
                    @foreach([['fa-sterling-sign','KSh 1,000','Per 60-min session'],['fa-users','Group format','Up to 50 learners'],['fa-calendar-week','Weekly','Every subject'],['fa-shield-check','Secure','All sessions']] as [$ic,$val,$lbl])
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:34px;height:34px;background:rgba(212,160,23,.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid {{ $ic }}" style="color:#D4A017;font-size:13px;"></i>
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
                    <span style="font-family:'Fraunces',serif;font-size:36px;font-weight:800;color:#D4A017;">KSh 1,000</span>
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
            <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:24px;border-left:4px solid #D4A017;">
                <h3 style="font-size:15px;font-weight:700;color:#15281F;margin-bottom:16px;display:flex;align-items:center;gap:7px;"><i class="fa-solid fa-star fa-sm" style="color:#D4A017;"></i> Examiner's Clinic</h3>
                @foreach(['Focuses on HOW to answer','Strategic, marks-focused teaching','Direct insight into KNEC marking criteria','KSh 1,000 for a 60-minute group session'] as $item)
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
        <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:32px;flex-wrap:wrap;gap:16px;">
            <div>
                <h2 style="font-family:'Fraunces',serif;font-size:clamp(26px,4vw,38px);color:#15281F;margin-bottom:6px;">Upcoming Clinics</h2>
                <p style="color:rgba(21,40,31,.55);">Scheduled once every week per subject. Conducted by highest-rated national examiners.</p>
            </div>
        </div>

        @if($clinics->isEmpty())
        @php $demo=[
            ['Advanced Mathematics – Paper 2 Strategy','Dr. Amara Kimani','Mathematics','Thu, Nov 14 at 4:00 PM','12 spots left','#6D28D9'],
            ['KCSE English – Comprehension & Essay Writing','Ms. Sarah Njoroge','English','Sat, Nov 16 at 10:00 AM','23 spots left','#10B981'],
            ['Physics Paper 1 – Examiner Traps & Errors','Prof. James Mwangi','Physics','Mon, Nov 18 at 5:00 PM','8 spots left','#E8522A'],
            ['Chemistry – Organic Marking Scheme Analysis','Mr. David Otieno','Chemistry','Wed, Nov 20 at 4:30 PM','31 spots left','#D4A017'],
        ]; @endphp
        <div style="display:flex;flex-direction:column;gap:14px;">
            @foreach($demo as [$title,$tutor,$subject,$date,$spots,$color])
            <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:24px;display:flex;align-items:flex-start;gap:18px;flex-wrap:wrap;box-shadow:0 2px 16px rgba(21,40,31,.05);">
                <div style="width:54px;height:54px;border-radius:16px;background:{{ $color }}18;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-solid fa-chalkboard-user" style="color:{{ $color }};font-size:22px;"></i>
                </div>
                <div style="flex:1;min-width:200px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:8px;">
                        <h3 style="font-family:'Fraunces',serif;font-size:18px;color:#15281F;max-width:520px;">{{ $title }}</h3>
                        <span style="font-family:'Fraunces',serif;font-size:22px;font-weight:800;color:#D4A017;flex-shrink:0;">KSh 1,000</span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:10px;">
                        <span style="background:rgba(109,40,217,.08);color:#6D28D9;padding:3px 11px;border-radius:999px;font-size:11px;font-weight:600;display:flex;align-items:center;gap:5px;"><i class="fa-solid fa-user fa-xs"></i>{{ $tutor }}</span>
                        <span style="background:rgba(21,40,31,.06);color:#15281F;padding:3px 11px;border-radius:999px;font-size:11px;font-weight:600;display:flex;align-items:center;gap:5px;"><i class="fa-solid fa-book-open fa-xs"></i>{{ $subject }}</span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:16px;font-size:13px;color:rgba(21,40,31,.55);">
                        <span><i class="fa-regular fa-calendar fa-xs" style="color:#6D28D9;margin-right:4px;"></i>{{ $date }}</span>
                        <span><i class="fa-solid fa-clock fa-xs" style="color:#6D28D9;margin-right:4px;"></i>60 minutes</span>
                        <span style="color:#10B981;font-weight:600;"><i class="fa-solid fa-users fa-xs" style="margin-right:4px;"></i>{{ $spots }}</span>
                    </div>
                </div>
                <div style="flex-shrink:0;">
                    <a href="{{ route('register') }}" style="display:inline-flex;align-items:center;gap:7px;padding:11px 24px;border-radius:999px;background:#D4A017;color:#15281F;font-size:14px;font-weight:700;text-decoration:none;transition:all .18s;" onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                        <i class="fa-solid fa-ticket fa-sm"></i> Book Spot
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div style="display:flex;flex-direction:column;gap:14px;">
            @foreach($clinics as $clinic)
            <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:24px;display:flex;align-items:flex-start;gap:18px;flex-wrap:wrap;">
                <div style="width:54px;height:54px;border-radius:16px;background:rgba(212,160,23,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-solid fa-chalkboard-user" style="color:#D4A017;font-size:22px;"></i>
                </div>
                <div style="flex:1;min-width:200px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:8px;">
                        <h3 style="font-family:'Fraunces',serif;font-size:18px;color:#15281F;">{{ $clinic->title }}</h3>
                        <span style="font-family:'Fraunces',serif;font-size:22px;font-weight:800;color:#D4A017;">KSh {{ number_format($clinic->price) }}</span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:10px;">
                        <span style="background:rgba(109,40,217,.08);color:#6D28D9;padding:3px 11px;border-radius:999px;font-size:11px;font-weight:600;display:flex;align-items:center;gap:5px;"><i class="fa-solid fa-user fa-xs"></i>{{ $clinic->tutor->name }}</span>
                        @if($clinic->subject)<span style="background:rgba(21,40,31,.06);color:#15281F;padding:3px 11px;border-radius:999px;font-size:11px;font-weight:600;">{{ $clinic->subject->name }}</span>@endif
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:16px;font-size:13px;color:rgba(21,40,31,.55);">
                        <span><i class="fa-regular fa-calendar fa-xs" style="color:#6D28D9;margin-right:4px;"></i>{{ $clinic->scheduled_at->format('D, M j \a\t g:ia') }}</span>
                        <span><i class="fa-solid fa-clock fa-xs" style="color:#6D28D9;margin-right:4px;"></i>{{ $clinic->duration_minutes }} min</span>
                        <span style="color:#10B981;font-weight:600;"><i class="fa-solid fa-users fa-xs" style="margin-right:4px;"></i>{{ $clinic->spotsLeft() }} spots left</span>
                    </div>
                </div>
                <div style="flex-shrink:0;">
                    @auth
                    @if($clinic->isEnrolledBy(auth()->user()))
                    <span style="background:rgba(16,185,129,.1);color:#10B981;border:1px solid rgba(16,185,129,.3);padding:10px 20px;border-radius:999px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;">
                        <i class="fa-solid fa-check fa-xs"></i> Enrolled
                    </span>
                    @else
                    <form method="POST" action="/clinic/{{ $clinic->id }}/enroll">
                        @csrf
                        <button type="submit" style="display:inline-flex;align-items:center;gap:7px;padding:11px 24px;border-radius:999px;background:#D4A017;color:#15281F;font-size:14px;font-weight:700;border:none;cursor:pointer;font-family:inherit;">
                            <i class="fa-solid fa-ticket fa-sm"></i> Book Spot
                        </button>
                    </form>
                    @endif
                    @else
                    <a href="{{ route('register') }}" style="display:inline-flex;align-items:center;gap:7px;padding:11px 24px;border-radius:999px;background:#D4A017;color:#15281F;font-size:14px;font-weight:700;text-decoration:none;">
                        <i class="fa-solid fa-ticket fa-sm"></i> Book Spot
                    </a>
                    @endauth
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- FOR EXAMINERS --}}
<section id="examiners" style="padding:72px 0;background:rgba(21,40,31,.03);">
    <div style="max-width:960px;margin:0 auto;padding:0 24px;">
        <div style="text-align:center;margin-bottom:40px;">
            <h2 style="font-family:'Fraunces',serif;font-size:clamp(26px,4vw,38px);color:#15281F;margin-bottom:10px;">For Tutors & Examiners</h2>
            <p style="font-size:16px;color:rgba(21,40,31,.55);">Monetize your marking experience with zero administrative friction and instant payouts.</p>
        </div>
        <div style="display:grid;grid-template-columns:1fr;gap:14px;" class="ex-g">
            @foreach([
                ['fa-bolt','Instant M-Pesa Payouts','Payments are processed immediately upon completing your clinic session directly to your M-Pesa account. No delays, no follow-ups.'],
                ['fa-arrow-trend-up','Build Authority & Reputation','Hosting featured clinics puts your professional profile in front of hundreds of parents and learners, positioning you as a top-tier tutor for 1-on-1 bookings.'],
                ['fa-headset','Zero Administrative Overhead','You bring the expertise. Mwalimu handles student acquisition, M-Pesa ticketing, automated reminders, and classroom setup.'],
            ] as [$icon,$title,$desc])
            <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:24px;display:flex;gap:16px;align-items:flex-start;">
                <div style="width:48px;height:48px;background:rgba(109,40,217,.08);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-solid {{ $icon }}" style="color:#6D28D9;font-size:18px;"></i>
                </div>
                <div>
                    <h3 style="font-family:'Fraunces',serif;font-size:17px;color:#15281F;margin-bottom:6px;">{{ $title }}</h3>
                    <p style="font-size:14px;color:rgba(21,40,31,.6);line-height:1.75;">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <div style="text-align:center;margin-top:28px;">
            <a href="{{ route('register') }}?role=tutor" style="display:inline-flex;align-items:center;gap:8px;padding:14px 36px;border-radius:999px;background:#6D28D9;color:#fff;font-size:15px;font-weight:700;text-decoration:none;transition:all .18s;" onmouseover="this.style.background='#4C1D95';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#6D28D9';this.style.transform='translateY(0)'">
                <i class="fa-solid fa-chalkboard-user fa-sm"></i> Register as an Examiner
            </a>
        </div>
        <style>.ex-g{grid-template-columns:1fr!important;}@media(min-width:768px){.ex-g{grid-template-columns:repeat(3,1fr)!important;}}</style>
    </div>
</section>

@include('partials.footer')
@livewireScripts
</body>
</html>
