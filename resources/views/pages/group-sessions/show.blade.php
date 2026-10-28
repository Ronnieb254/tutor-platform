<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>{{ $groupSession->title }} – Mwalimu</title>
<meta name="description" content="{{ Str::limit($groupSession->description, 160) }}">
</head>
<body>
@include('partials.nav')

@php
    $isFull        = $groupSession->isFull();
    $enrolledCount = $groupSession->enrolledCount();
    $maxStudents   = $groupSession->max_students;
    $fillPct       = $maxStudents > 0 ? round(($enrolledCount / $maxStudents) * 100) : 0;
    $isSeries      = \Schema::hasColumn('examiner_clinics','series_id') && $groupSession->series_id && ($groupSession->total_sessions ?? 1) > 1;
    $isEnrolled    = auth()->check() && $groupSession->isEnrolledBy(auth()->user());
    $canJoin       = auth()->check() && $groupSession->canJoin(auth()->user());
@endphp

{{-- BREADCRUMB --}}
<div style="background:#F9FAF9;border-bottom:1px solid rgba(21,40,31,.07);padding:14px 0;">
    <div style="max-width:1100px;margin:0 auto;padding:0 24px;display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(21,40,31,.5);">
        <a href="{{ route('home') }}" style="color:rgba(21,40,31,.5);text-decoration:none;">Home</a>
        <i class="fa-solid fa-chevron-right fa-xs"></i>
        <a href="{{ route('group-sessions.index') }}" style="color:rgba(21,40,31,.5);text-decoration:none;">Group Sessions</a>
        <i class="fa-solid fa-chevron-right fa-xs"></i>
        <span style="color:#15281F;font-weight:600;">{{ Str::limit($groupSession->title, 50) }}</span>
    </div>
</div>

<div style="max-width:1100px;margin:0 auto;padding:40px 24px;" class="gs-show-wrap">

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#065F46;padding:14px 18px;border-radius:12px;margin-bottom:20px;display:flex;align-items:flex-start;gap:10px;">
        <i class="fa-solid fa-circle-check" style="color:#10B981;margin-top:1px;flex-shrink:0;"></i>
        <p style="margin:0;font-size:14px;">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div style="background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.2);color:#991B1B;padding:14px 18px;border-radius:12px;margin-bottom:20px;display:flex;align-items:flex-start;gap:10px;">
        <i class="fa-solid fa-circle-exclamation" style="color:#DC2626;margin-top:1px;flex-shrink:0;"></i>
        <p style="margin:0;font-size:14px;">{{ session('error') }}</p>
    </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr;gap:28px;" class="gs-cols">

        {{-- LEFT: MAIN CONTENT --}}
        <div>
            {{-- Subject + series badge --}}
            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px;">
                @if($groupSession->subject)
                <span style="background:rgba(21,40,31,.06);color:#15281F;padding:5px 14px;border-radius:999px;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-book-open fa-xs"></i>{{ $groupSession->subject->name }}</span>
                @endif
                @if($isSeries)
                <span style="background:rgba(212,160,23,.12);color:#B7870A;padding:5px 14px;border-radius:999px;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-layer-group fa-xs"></i>Session {{ $groupSession->session_number }} of {{ $groupSession->total_sessions }}</span>
                @endif
                @if($groupSession->status === 'cancelled')
                <span style="background:#FEE2E2;color:#991B1B;padding:5px 14px;border-radius:999px;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-ban fa-xs"></i>Cancelled</span>
                @elseif($isFull)
                <span style="background:#FEE2E2;color:#991B1B;padding:5px 14px;border-radius:999px;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-lock fa-xs"></i>Full</span>
                @else
                <span style="background:rgba(16,185,129,.1);color:#065F46;padding:5px 14px;border-radius:999px;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-circle fa-xs"></i>Open</span>
                @endif
            </div>

            <h1 style="font-family:'Fraunces',serif;font-size:clamp(26px,4vw,40px);color:#15281F;line-height:1.2;margin-bottom:14px;">{{ $groupSession->title }}</h1>

            @if($groupSession->tutor)
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(10,100,188,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-solid fa-user" style="color:#0A64BC;font-size:14px;"></i>
                </div>
                <div>
                    <p style="font-size:14px;font-weight:600;color:#15281F;margin:0;">{{ $groupSession->tutor->name }}</p>
                    <p style="font-size:12px;color:rgba(21,40,31,.5);margin:0;">Tutor</p>
                </div>
            </div>
            @endif

            {{-- Meta row --}}
            <div style="display:flex;flex-wrap:wrap;gap:16px;margin-bottom:24px;padding:18px;background:#F9FAF9;border-radius:14px;border:1px solid rgba(21,40,31,.07);">
                <div style="display:flex;align-items:center;gap:8px;font-size:14px;color:#15281F;">
                    <div style="width:32px;height:32px;background:rgba(10,100,188,.08);border-radius:8px;display:flex;align-items:center;justify-content:center;"><i class="fa-regular fa-calendar fa-xs" style="color:#0A64BC;"></i></div>
                    <div><p style="font-size:11px;color:rgba(21,40,31,.45);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin:0;">Date & Time</p><p style="font-size:14px;font-weight:600;color:#15281F;margin:0;">{{ $groupSession->scheduled_at->format('D, M j \a\t g:ia') }}</p></div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;font-size:14px;color:#15281F;">
                    <div style="width:32px;height:32px;background:rgba(10,100,188,.08);border-radius:8px;display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-clock fa-xs" style="color:#0A64BC;"></i></div>
                    <div><p style="font-size:11px;color:rgba(21,40,31,.45);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin:0;">Duration</p><p style="font-size:14px;font-weight:600;color:#15281F;margin:0;">{{ $groupSession->duration_minutes }} minutes</p></div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;font-size:14px;color:#15281F;">
                    <div style="width:32px;height:32px;background:rgba(10,100,188,.08);border-radius:8px;display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-users fa-xs" style="color:#0A64BC;"></i></div>
                    <div><p style="font-size:11px;color:rgba(21,40,31,.45);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin:0;">Seats</p><p style="font-size:14px;font-weight:600;color:{{ $isFull ? '#991B1B' : '#10B981' }};margin:0;">{{ $enrolledCount }}/{{ $maxStudents }}</p></div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;font-size:14px;color:#15281F;">
                    <div style="width:32px;height:32px;background:rgba(212,160,23,.1);border-radius:8px;display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-tag fa-xs" style="color:#EEBE15;"></i></div>
                    <div><p style="font-size:11px;color:rgba(21,40,31,.45);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin:0;">Price</p><p style="font-family:'Fraunces',serif;font-size:18px;font-weight:800;color:#EEBE15;margin:0;">KSh {{ number_format($groupSession->price) }}</p></div>
                </div>
            </div>

            {{-- Description --}}
            <div style="margin-bottom:24px;">
                <h2 style="font-size:16px;font-weight:700;color:#15281F;margin-bottom:10px;">About this session</h2>
                <div style="font-size:15px;color:rgba(21,40,31,.7);line-height:1.85;">{{ $groupSession->description }}</div>
            </div>

            {{-- Topics covered --}}
            @if(!empty($groupSession->topics_covered) && count($groupSession->topics_covered))
            <div style="margin-bottom:24px;">
                <h2 style="font-size:16px;font-weight:700;color:#15281F;margin-bottom:10px;">Topics covered</h2>
                <div style="display:flex;flex-wrap:wrap;gap:7px;">
                    @foreach($groupSession->topics_covered as $topic)
                    <span style="background:rgba(10,100,188,.08);color:#0A64BC;padding:5px 13px;border-radius:8px;font-size:13px;font-weight:500;">{{ $topic }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Capacity bar --}}
            <div style="margin-bottom:24px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:7px;">
                    <span style="font-size:13px;font-weight:600;color:rgba(21,40,31,.6);">Session capacity</span>
                    <span style="font-size:13px;font-weight:700;color:{{ $isFull ? '#991B1B' : '#10B981' }};">{{ $fillPct }}% filled</span>
                </div>
                <div style="width:100%;height:8px;background:rgba(21,40,31,.06);border-radius:999px;overflow:hidden;">
                    <div style="width:{{ $fillPct }}%;height:100%;background:{{ $isFull ? '#E8522A' : ($fillPct > 70 ? '#EEBE15' : '#10B981') }};border-radius:999px;transition:width .3s;"></div>
                </div>
                <p style="font-size:12px;color:rgba(21,40,31,.45);margin-top:6px;">{{ $groupSession->spotsLeft() }} seat{{ $groupSession->spotsLeft() != 1 ? 's' : '' }} remaining</p>
            </div>

            {{-- Series sessions (if part of a series) --}}
            @if($isSeries && $seriesSessions->count() > 1)
            <div>
                <h2 style="font-size:16px;font-weight:700;color:#15281F;margin-bottom:12px;"><i class="fa-solid fa-layer-group fa-xs" style="color:#EEBE15;margin-right:6px;"></i>All sessions in this series</h2>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    @foreach($seriesSessions as $s)
                    @php $isThis = $s->id === $groupSession->id; @endphp
                    <div style="padding:14px 16px;border-radius:12px;border:{{ $isThis ? '2px solid #EEBE15' : '1px solid rgba(21,40,31,.08)' }};background:{{ $isThis ? 'rgba(212,160,23,.04)' : '#fff' }};display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                        <div>
                            <p style="font-size:13px;font-weight:700;color:#15281F;margin:0 0 2px;">Session {{ $s->session_number }}: {{ $s->scheduled_at->format('D, M j \a\t g:ia') }}</p>
                            <p style="font-size:12px;color:rgba(21,40,31,.5);margin:0;">{{ $s->duration_minutes }}min · {{ $s->enrolledCount() }}/{{ $s->max_students }} enrolled</p>
                        </div>
                        @if($isThis)
                        <span style="font-size:12px;font-weight:700;color:#B7870A;background:rgba(212,160,23,.12);padding:4px 12px;border-radius:999px;">Current</span>
                        @else
                        <a href="{{ route('group-sessions.show', $s) }}" style="font-size:12px;font-weight:600;color:#0A64BC;text-decoration:none;">View ↗</a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT: BOOKING SIDEBAR --}}
        <div>
            <div style="position:sticky;top:20px;background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.1);padding:28px;box-shadow:0 4px 20px rgba(21,40,31,.07);">
                <div style="text-align:center;margin-bottom:20px;">
                    <span style="font-family:'Fraunces',serif;font-size:38px;font-weight:800;color:#EEBE15;">KSh {{ number_format($groupSession->price) }}</span>
                    <p style="font-size:13px;color:rgba(21,40,31,.5);margin:4px 0 0;">per session</p>
                </div>

                @if($groupSession->status === 'cancelled')
                <div style="background:#FEE2E2;color:#991B1B;padding:14px;border-radius:12px;text-align:center;font-size:14px;font-weight:600;margin-bottom:16px;">
                    <i class="fa-solid fa-ban"></i> This session has been cancelled.
                </div>

                @elseif($isEnrolled)
                <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);padding:14px;border-radius:12px;text-align:center;margin-bottom:16px;">
                    <p style="font-size:14px;font-weight:700;color:#065F46;margin:0 0 4px;"><i class="fa-solid fa-circle-check"></i> You're enrolled!</p>
                    <p style="font-size:13px;color:rgba(6,95,70,.7);margin:0;">Check your email for the session code.</p>
                </div>
                @if($canJoin)
                <a href="{{ route('clinic.join', $groupSession) }}" style="display:block;text-align:center;padding:14px;border-radius:12px;background:#0A64BC;color:#fff;font-size:15px;font-weight:700;text-decoration:none;margin-bottom:12px;transition:all .15s;" onmouseover="this.style.background='#120A8F'" onmouseout="this.style.background='#0A64BC'">
                    <i class="fa-solid fa-video"></i> Join Live Session
                </a>
                @else
                <div style="background:#F9FAF9;border:1px solid rgba(21,40,31,.08);padding:14px;border-radius:12px;text-align:center;margin-bottom:12px;">
                    <p style="font-size:13px;color:rgba(21,40,31,.55);margin:0;"><i class="fa-solid fa-clock"></i> Session starts {{ $groupSession->scheduled_at->diffForHumans() }}</p>
                    <p style="font-size:12px;color:rgba(21,40,31,.4);margin:6px 0 0;">Join button unlocks 15 min before start.</p>
                </div>
                @endif

                @elseif($isFull)
                <div style="background:#FEE2E2;border:1px solid #FECACA;padding:16px;border-radius:12px;text-align:center;font-size:14px;color:#991B1B;font-weight:600;margin-bottom:16px;">
                    <i class="fa-solid fa-lock"></i> Session Full
                    <p style="font-size:12px;font-weight:400;margin:6px 0 0;">All {{ $maxStudents }} seats are taken.</p>
                </div>
                <a href="{{ route('group-sessions.index') }}" style="display:block;text-align:center;padding:13px;border-radius:12px;background:rgba(21,40,31,.06);color:#15281F;font-size:14px;font-weight:600;text-decoration:none;">
                    <i class="fa-solid fa-arrow-left fa-xs"></i> Browse Other Sessions
                </a>

                @elseif(auth()->check())
                <form method="POST" action="{{ route('group-sessions.enroll', $groupSession) }}">
                    @csrf
                    <button type="submit" style="width:100%;padding:14px;border-radius:12px;background:#EEBE15;color:#15281F;font-size:15px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .15s;margin-bottom:12px;" onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                        <i class="fa-solid fa-ticket"></i> Book Seat — KSh {{ number_format($groupSession->price) }}
                    </button>
                </form>

                @else
                <a href="{{ route('login') }}?redirect={{ urlencode(request()->url()) }}" style="display:block;text-align:center;padding:14px;border-radius:12px;background:#EEBE15;color:#15281F;font-size:15px;font-weight:700;text-decoration:none;margin-bottom:12px;">
                    <i class="fa-solid fa-right-to-bracket"></i> Log in to Book
                </a>
                <p style="text-align:center;font-size:13px;color:rgba(21,40,31,.5);margin:0;">
                    Don't have an account? <a href="{{ route('register') }}" style="color:#0A64BC;font-weight:600;">Sign up free</a>
                </p>
                @endif

                <div style="margin-top:20px;padding-top:16px;border-top:1px solid rgba(21,40,31,.07);">
                    @foreach([['fa-shield-check','Secure M-Pesa payment'],['fa-users','Up to '.$maxStudents.' learners'],['fa-video','Live on Jitsi'],['fa-clock','15-min early access']] as [$ic,$lbl])
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                        <i class="fa-solid {{ $ic }}" style="color:#10B981;font-size:12px;width:14px;text-align:center;flex-shrink:0;"></i>
                        <span style="font-size:13px;color:rgba(21,40,31,.6);">{{ $lbl }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.gs-show-wrap .gs-cols { grid-template-columns: 1fr !important; }
@media(min-width: 900px) { .gs-show-wrap .gs-cols { grid-template-columns: 1fr 360px !important; } }
</style>

@include('partials.footer')
@livewireScripts
</body>
</html>
