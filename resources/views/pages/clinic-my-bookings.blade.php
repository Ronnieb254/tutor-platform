<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>My Clinic Bookings</title>
<style>
:root{--ink:#15281F;--cream:#FBF5E6;--trust-blue:#0A64BC;--coral:#E8522A;--gold:#EEBE15;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'DM Sans',sans-serif;color:var(--ink);background:#F2F5F2;}
a{color:inherit;text-decoration:none;}

.container{max-width:920px;margin:0 auto;padding:20px;}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;flex-wrap:wrap;gap:16px;}
.title{font-family:'Fraunces',serif;font-size:32px;font-weight:700;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;border:none;cursor:pointer;font-family:inherit;text-decoration:none;transition:all .15s;}
.btn-primary{background:var(--trust-blue);color:#fff;}
.btn-primary:hover{background:#120A8F;}

.booking-card{background:#fff;border-radius:14px;border:1px solid var(--hair);padding:20px;margin-bottom:16px;display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap;}
.booking-icon{width:48px;height:48px;background:rgba(10,100,188,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.booking-icon i{color:var(--trust-blue);font-size:22px;}

.booking-info{flex:1;min-width:250px;}
.booking-title{font-family:'Fraunces',serif;font-size:16px;font-weight:700;margin-bottom:6px;}
.booking-meta{font-size:13px;color:var(--muted);display:flex;flex-wrap:wrap;gap:16px;align-items:center;}
.booking-meta i{margin-right:4px;color:var(--trust-blue);}

.booking-status{display:flex;gap:10px;align-items:center;}
.status-badge{display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;text-transform:uppercase;}
.status-upcoming{background:rgba(10,100,188,.1);color:#0A64BC;}
.status-completed{background:rgba(16,185,129,.1);color:#10B981;}
.status-past{background:rgba(21,40,31,.06);color:rgba(21,40,31,.5);}

.code-box{background:#F9FAFB;border:1px solid var(--hair);border-radius:8px;padding:8px 12px;font-family:monospace;font-size:12px;font-weight:700;color:var(--trust-blue);text-align:center;}

.booking-action{display:flex;gap:8px;}
.btn-join{background:var(--mint);color:#fff;}
.btn-join:hover{opacity:.9;}
.btn-share{background:#F4F6F4;color:var(--muted);border:1px solid var(--hair);}
.btn-share:hover{border-color:var(--trust-blue);color:var(--trust-blue);}

.empty{text-align:center;padding:60px 20px;}
.empty-icon{font-size:48px;margin-bottom:16px;opacity:.3;}
.empty-title{font-family:'Fraunces',serif;font-size:22px;font-weight:700;margin-bottom:8px;color:var(--ink);}
.empty-text{color:var(--muted);font-size:14px;margin-bottom:24px;}

.pagination{display:flex;justify-content:center;gap:6px;margin-top:32px;flex-wrap:wrap;}
.page-link{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:8px;background:#fff;border:1px solid var(--hair);font-size:13px;font-weight:600;cursor:pointer;transition:all .15s;text-decoration:none;}
.page-link:hover{border-color:var(--trust-blue);color:var(--trust-blue);}
.page-link.active{background:var(--trust-blue);color:#fff;border-color:var(--trust-blue);}
.page-link.disabled{opacity:.4;cursor:not-allowed;}

.upcoming-section{background:rgba(10,100,188,.06);border-radius:12px;padding:16px;margin-bottom:24px;border-left:4px solid var(--trust-blue);}
.upcoming-count{font-size:32px;font-weight:700;color:var(--trust-blue);}
</style>
</head>
<body>
@include('partials.nav')

<div class="container">
    {{-- Header --}}
    <div class="header">
        <h1 class="title">My Clinic Bookings</h1>
        <a href="{{ route('examiner-clinic') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Book New Clinic
        </a>
    </div>

    {{-- Upcoming Count --}}
    @if($upcomingCount > 0)
    <div class="upcoming-section">
        <div style="font-size:12px;color:var(--muted);margin-bottom:6px;text-transform:uppercase;font-weight:700;letter-spacing:.06em;">Upcoming Sessions</div>
        <div class="upcoming-count">{{ $upcomingCount }} clinic{{ $upcomingCount !== 1 ? 's' : '' }}</div>
    </div>
    @endif

    {{-- Bookings List --}}
    @if($bookings->isNotEmpty())
        @foreach($bookings as $booking)
        @php
            $isUpcoming = $booking->clinic->scheduled_at->isFuture();
            $isPast = $booking->clinic->scheduled_at->isPast();
            $canJoin = $isUpcoming && $booking->clinic->scheduled_at->diffInMinutes(now(), false) <= 15;
        @endphp
        <div class="booking-card">
            <div class="booking-icon">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>

            <div class="booking-info">
                <div class="booking-title">{{ $booking->clinic->title }}</div>
                <div class="booking-meta">
                    <span><i class="fa-regular fa-calendar"></i> {{ $booking->clinic->scheduled_at->format('D, M j, Y') }}</span>
                    <span><i class="fa-solid fa-clock"></i> {{ $booking->clinic->scheduled_at->format('g:ia') }}</span>
                    <span><i class="fa-solid fa-hourglass-end"></i> {{ $booking->clinic->duration_minutes }}min</span>
                    @if($booking->clinic->tutor)
                    <span><i class="fa-solid fa-user-tie"></i> {{ $booking->clinic->tutor->name }}</span>
                    @endif
                </div>

                {{-- Session Code --}}
                <div style="margin-top:12px;display:flex;align-items:center;gap:10px;">
                    <span style="font-size:12px;font-weight:700;text-transform:uppercase;color:var(--muted);">Code:</span>
                    <div class="code-box" title="Your session access code">{{ $booking->session_code }}</div>
                </div>
            </div>

            {{-- Status & Actions --}}
            <div style="flex-shrink:0;display:flex;flex-direction:column;gap:8px;align-items:flex-end;">
                {{-- Status Badge --}}
                @if($canJoin)
                <span class="status-badge status-upcoming" style="background:rgba(16,185,129,.2);color:#10B981;">
                    <i class="fa-solid fa-circle fa-xs"></i> LIVE NOW
                </span>
                @elseif($isUpcoming)
                <span class="status-badge status-upcoming">
                    <i class="fa-solid fa-clock fa-xs"></i> Upcoming
                </span>
                @elseif($booking->attended)
                <span class="status-badge status-completed">
                    <i class="fa-solid fa-check fa-xs"></i> Attended
                </span>
                @else
                <span class="status-badge status-past">
                    <i class="fa-solid fa-check-circle fa-xs"></i> Completed
                </span>
                @endif

                {{-- Action Buttons --}}
                <div class="booking-action">
                    @if($canJoin || $isUpcoming)
                    <a href="{{ route('clinic.join', $booking->clinic) }}" class="btn btn-join" style="font-size:12px;padding:8px 14px;">
                        <i class="fa-solid fa-video"></i> Join Session
                    </a>
                    @endif
                    <button onclick="copyCode('{{ $booking->session_code }}')" class="btn btn-share" style="font-size:12px;padding:8px 14px;">
                        <i class="fa-solid fa-copy"></i> Copy Code
                    </button>
                </div>

                {{-- Payment Info --}}
                @if($booking->payment)
                <div style="font-size:11px;color:var(--muted);text-align:right;">
                    Paid: KSh {{ number_format($booking->payment->amount) }}
                    <br>{{ $booking->payment->paid_at->format('M j, Y') }}
                </div>
                @endif
            </div>
        </div>
        @endforeach

        {{-- Pagination --}}
        @if($bookings->hasPages())
        <div class="pagination">
            @if($bookings->onFirstPage())
            <span class="page-link disabled"><i class="fa-solid fa-chevron-left fa-xs"></i></span>
            @else
            <a href="{{ $bookings->previousPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-left fa-xs"></i></a>
            @endif

            @foreach($bookings->getUrlRange(max(1, $bookings->currentPage()-2), min($bookings->lastPage(), $bookings->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}" class="page-link {{ $page == $bookings->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach

            @if($bookings->hasMorePages())
            <a href="{{ $bookings->nextPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-right fa-xs"></i></a>
            @else
            <span class="page-link disabled"><i class="fa-solid fa-chevron-right fa-xs"></i></span>
            @endif
        </div>
        @endif
    @else
        <div class="empty">
            <div class="empty-icon">📅</div>
            <h2 class="empty-title">No Bookings Yet</h2>
            <p class="empty-text">You haven't booked any clinic sessions. Start learning from national examiners today!</p>
            <a href="{{ route('examiner-clinic') }}" class="btn btn-primary" style="width:auto;margin:0 auto;">
                <i class="fa-solid fa-calendar-check"></i> Browse Clinics
            </a>
        </div>
    @endif
</div>

<script>
function copyCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        // Show confirmation
        const btn = event.target.closest('button');
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check fa-xs"></i> Copied!';
        setTimeout(() => {
            btn.innerHTML = original;
        }, 2000);
    });
}
</script>

@include('partials.footer')
@livewireScripts
</body>
</html>
