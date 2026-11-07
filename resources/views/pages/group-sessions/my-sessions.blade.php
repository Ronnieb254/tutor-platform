<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>My Group Sessions â‚¬" Mwalimu</title>
</head>
<body>
@include('partials.nav')

<div style="background:#F9FAF9;border-bottom:1px solid rgba(21,40,31,.07);padding:14px 0;">
    <div style="max-width:960px;margin:0 auto;padding:0 24px;display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(21,40,31,.5);">
        <a href="{{ route('home') }}" style="color:rgba(21,40,31,.5);text-decoration:none;">Home</a>
        <span style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>
        <a href="{{ route('group-sessions.index') }}" style="color:rgba(21,40,31,.5);text-decoration:none;">Group Sessions</a>
        <span style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>
        <span style="color:#15281F;font-weight:600;">My Sessions</span>
    </div>
</div>

<div style="max-width:960px;margin:0 auto;padding:40px 24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:28px;">
        <div>
            <h1 style="font-family:'Fraunces',serif;font-size:32px;color:#15281F;margin-bottom:4px;">My Group Sessions</h1>
            <p style="color:rgba(21,40,31,.55);font-size:15px;">All the live classes you've enrolled in.</p>
        </div>
        <a href="{{ route('group-sessions.index') }}" style="display:inline-flex;align-items:center;gap:7px;padding:10px 22px;border-radius:999px;background:#EEBE15;color:#15281F;font-size:14px;font-weight:700;text-decoration:none;">
            <i class="fas fa-plus"></i> Browse More
        </a>
    </div>

    @if(session('success'))
    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#065F46;padding:14px 18px;border-radius:12px;margin-bottom:20px;font-size:14px;">
        <i class="fas fa-check-circle"></i>{{ session('success') }}
    </div>
    @endif

    @if($enrollments->isEmpty())
    <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:64px;text-align:center;">
        <div style="font-size:48px;margin-bottom:14px;"><i class="fas fa-user"></i></div>
        <h3 style="font-family:'Fraunces',serif;font-size:22px;color:#15281F;margin-bottom:8px;">No sessions yet</h3>
        <p style="color:rgba(21,40,31,.55);margin-bottom:20px;">You haven't enrolled in any group sessions. Browse upcoming classes and book your first seat.</p>
        <a href="{{ route('group-sessions.index') }}" style="display:inline-flex;align-items:center;gap:7px;padding:12px 28px;border-radius:999px;background:#EEBE15;color:#15281F;font-size:14px;font-weight:700;text-decoration:none;">
            <i class="fas fa-check-circle"></i> Browse Sessions
        </a>
    </div>
    @else
    <div style="display:flex;flex-direction:column;gap:12px;">
        @foreach($enrollments as $enrollment)
        @php
            $session = $enrollment->clinic;
            $canJoin = $session && auth()->check() && $session->canJoin(auth()->user());
            $isPast  = $session && $session->scheduled_at->isPast();
        @endphp
        @if(!$session) @continue @endif
        <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:22px;display:flex;align-items:flex-start;gap:18px;flex-wrap:wrap;box-shadow:0 2px 12px rgba(21,40,31,.04);">
            {{-- Icon --}}
            <div style="width:50px;height:50px;border-radius:14px;background:rgba(212,160,23,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-user"></i>
            </div>
            {{-- Info --}}
            <div style="flex:1;min-width:180px;">
                <h3 style="font-family:'Fraunces',serif;font-size:17px;color:#15281F;margin-bottom:4px;">{{ $session->title }}</h3>
                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:8px;">
                    @if($session->subject)
                    <span style="font-size:11px;font-weight:600;color:#0A64BC;background:rgba(10,100,188,.07);padding:2px 10px;border-radius:999px;">{{ $session->subject->name }}</span>
                    @endif
                    @if($session->tutor)
                    <span style="font-size:11px;font-weight:500;color:rgba(21,40,31,.5);">with {{ $session->tutor->name }}</span>
                    @endif
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:14px;font-size:13px;color:rgba(21,40,31,.55);">
                    <span><i class="fas fa-calendar"></i>{{ $session->scheduled_at->format('D, M j \a\t g:ia') }}</span>
                    <span><i class="fas fa-clock"></i>{{ $session->duration_minutes }}min</span>
                    @if($enrollment->session_code)
                    <span><span style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>Code: <strong style="color:#15281F;">{{ $enrollment->session_code }}</strong></span>
                    @endif
                </div>
            </div>
            {{-- Actions --}}
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0;">
                @if($session->status === 'cancelled')
                <span style="background:#FEE2E2;color:#991B1B;padding:8px 16px;border-radius:999px;font-size:12px;font-weight:700;"><span style="font-size:inherit;"><i class="fas fa-ban"></i></span> Cancelled</span>
                @elseif($isPast)
                <span style="background:rgba(21,40,31,.06);color:rgba(21,40,31,.45);padding:8px 16px;border-radius:999px;font-size:12px;font-weight:600;">Completed</span>
                @elseif($canJoin)
                <a href="{{ route('clinic.join', $session) }}" style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:999px;background:#0A64BC;color:#fff;font-size:13px;font-weight:700;text-decoration:none;">
                    <i class="fas fa-video"></i> Join Now
                </a>
                @else
                <span style="font-size:12px;color:rgba(21,40,31,.45);font-weight:500;">Starts {{ $session->scheduled_at->diffForHumans() }}</span>
                @endif
                <a href="{{ route('group-sessions.show', $session) }}" style="font-size:12px;color:#0A64BC;text-decoration:none;font-weight:500;">View details " "”</a>
            </div>
        </div>
        @endforeach
    </div>

    @if($enrollments->hasPages())
    <div style="margin-top:28px;display:flex;justify-content:center;gap:5px;flex-wrap:wrap;">
        @if($enrollments->onFirstPage())
        <span style="padding:8px 16px;border-radius:10px;background:#F4F6F4;color:rgba(21,40,31,.35);font-size:13px;"><span style="font-size:inherit;"><i class="fas fa-question-circle"></i></span> Prev</span>
        @else
        <a href="{{ $enrollments->previousPageUrl() }}" style="padding:8px 16px;border-radius:10px;background:#fff;color:#15281F;font-size:13px;border:1.5px solid rgba(21,40,31,.1);text-decoration:none;"><span style="font-size:inherit;"><i class="fas fa-question-circle"></i></span> Prev</a>
        @endif
        @if($enrollments->hasMorePages())
        <a href="{{ $enrollments->nextPageUrl() }}" style="padding:8px 16px;border-radius:10px;background:#fff;color:#15281F;font-size:13px;border:1.5px solid rgba(21,40,31,.1);text-decoration:none;">Next <span style="font-size:inherit;"><i class="fas fa-question-circle"></i></span></a>
        @else
        <span style="padding:8px 16px;border-radius:10px;background:#F4F6F4;color:rgba(21,40,31,.35);font-size:13px;">Next <span style="font-size:inherit;"><i class="fas fa-question-circle"></i></span></span>
        @endif
    </div>
    @endif
    @endif
</div>

@include('partials.footer')
@livewireScripts
</body>
</html>
