<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ Str::limit($groupSession->title, 50) }} – Mwalimu Tutor Portal</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>
:root{--ink:#15281F;--trust-blue:#0A64BC;--coral:#E8522A;--gold:#EEBE15;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);--sidebar:#111C16;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:'DM Sans',sans-serif;color:var(--ink);background:#F2F5F2;}
a{color:inherit;text-decoration:none;}
.layout{display:flex;height:100vh;overflow:hidden;}
.sidebar{width:256px;background:var(--sidebar);display:flex;flex-direction:column;flex-shrink:0;position:relative;z-index:50;}
.sb-logo{padding:22px 18px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;gap:10px;}
.sb-logo-text{font-family:'Fraunces',serif;font-size:19px;font-weight:700;color:#fff;}
.sb-logo-sub{font-size:10px;color:rgba(255,255,255,.3);margin-top:1px;}
.sb-nav{flex:1;padding:10px 8px;overflow-y:auto;}
.sb-section{margin-bottom:18px;}
.sb-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.28);padding:0 10px;margin-bottom:5px;}
.sb-item{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:10px;font-size:13.5px;font-weight:500;color:rgba(255,255,255,.58);transition:all .15s;margin-bottom:1px;cursor:pointer;border:none;background:none;width:100%;text-align:left;}
.sb-item:hover{background:rgba(255,255,255,.07);color:#fff;}
.sb-item.active{background:var(--trust-blue);color:#fff;}
.sb-badge{margin-left:auto;background:var(--coral);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:999px;min-width:18px;text-align:center;}
.sb-footer{padding:14px;border-top:1px solid rgba(255,255,255,.06);}
.sb-uname{font-size:13px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.sb-urole{font-size:11px;color:rgba(255,255,255,.35);}
.sb-logout{width:28px;height:28px;border-radius:8px;border:none;background:rgba(255,255,255,.08);color:rgba(255,255,255,.45);display:flex;align-items:center;justify-content:center;flex-shrink:0;cursor:pointer;}
.sb-logout:hover{background:rgba(255,255,255,.16);color:#fff;}
.mob-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:49;}
@media(max-width:1023px){.sidebar{position:fixed;left:-256px;top:0;bottom:0;transition:left .25s;z-index:50;}.sidebar.open{left:0;}.mob-overlay.show{display:block;}}
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;}
.topbar{height:60px;background:#fff;border-bottom:1px solid var(--hair);display:flex;align-items:center;justify-content:space-between;padding:0 24px;flex-shrink:0;}
.mob-toggle{display:none;width:34px;height:34px;border-radius:9px;border:1px solid var(--hair);background:#fff;align-items:center;justify-content:center;color:var(--muted);cursor:pointer;}
@media(max-width:1023px){.mob-toggle{display:flex;}}
.page-title{font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:var(--ink);}
.content{flex:1;overflow-y:auto;padding:24px;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:all .15s;font-family:inherit;text-decoration:none;}
.btn-ghost{background:#fff;color:var(--ink);border:1.5px solid var(--hair);}.btn-ghost:hover{border-color:var(--trust-blue);color:var(--trust-blue);}
.btn-blue{background:var(--trust-blue);color:#fff;}.btn-blue:hover{background:#120A8F;}
.btn-danger{background:rgba(220,38,38,.08);color:#DC2626;border:1px solid rgba(220,38,38,.2);}.btn-danger:hover{background:#FEE2E2;}
</style>
</head>
<body>
@php
$user    = auth()->user();
$profile = $user->tutorProfile;
$isApproved = $profile?->is_approved ?? false;
$pendingBookings = $user->bookingsAsTutor()->where('status','pending')->count();
$unreadNotifs = $user->unreadNotifications()->count();
$isFull = $groupSession->isFull();
$enrolledCount = $groupSession->enrolledCount();
$fillPct = $groupSession->max_students > 0 ? round(($enrolledCount / $groupSession->max_students) * 100) : 0;
$canJoin = $groupSession->canJoin($user);
@endphp
<div class="layout" x-data="{ sidebarOpen: false }">
<div class="mob-overlay" :class="sidebarOpen ? 'show' : ''" @click="sidebarOpen=false"></div>

{{-- SIDEBAR --}}
<aside class="sidebar" :class="sidebarOpen ? 'open' : ''">
    <div class="sb-logo">
        <svg width="30" height="30" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#0A64BC"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
        <div><div class="sb-logo-text">Mwalimu</div><div class="sb-logo-sub">Tutor Portal</div></div>
    </div>
    <nav class="sb-nav">
        <div class="sb-section">
            <div class="sb-label">Main</div>
            <a href="{{ route('tutor.dashboard') }}" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Overview
            </a>
        </div>
        @if($isApproved)
        <div class="sb-section">
            <div class="sb-label">Group Classes</div>
            <a href="{{ route('tutor.group-sessions.index') }}" class="sb-item active">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                My Group Sessions
            </a>
            <a href="{{ route('tutor.group-sessions.create') }}" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                Create Session
            </a>
        </div>
        <div class="sb-section">
            <div class="sb-label">Examiner</div>
            <a href="{{ route('examiner.dashboard') }}" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                Examiner Clinics
            </a>
        </div>
        @endif
        <div class="sb-section">
            <div class="sb-label">Finance</div>
            <a href="{{ route('tutor.dashboard') }}?tab=earnings" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                Earnings &amp; Wallet
            </a>
        </div>
    </nav>
    <div class="sb-footer">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:34px;height:34px;border-radius:10px;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
            <div style="flex:1;min-width:0;"><div class="sb-uname">{{ $user->name }}</div><div class="sb-urole">Tutor</div></div>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="sb-logout"><svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></button>
            </form>
        </div>
    </div>
</aside>

{{-- MAIN --}}
<div class="main">
    <div class="topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button class="mob-toggle" @click="sidebarOpen=!sidebarOpen"><svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
            <span class="page-title" style="font-size:15px;">{{ Str::limit($groupSession->title, 45) }}</span>
        </div>
        <div style="display:flex;gap:8px;">
            @if($canJoin)
            <a href="{{ route('clinic.join', $groupSession) }}" class="btn btn-blue" style="padding:7px 14px;font-size:12px;">
                <i class="fa-solid fa-video fa-xs"></i> Join as Moderator
            </a>
            @endif
            <a href="{{ route('tutor.group-sessions.index') }}" class="btn btn-ghost" style="padding:7px 14px;font-size:12px;">
                <i class="fa-solid fa-arrow-left fa-xs"></i> Back
            </a>
        </div>
    </div>

    <div class="content">
        @if(session('success'))
        <div style="background:#DCFCE7;color:#166534;border:1px solid #BBF7D0;padding:12px 16px;border-radius:12px;font-size:13.5px;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
        @endif

        <div style="display:grid;grid-template-columns:1fr 320px;gap:20px;" class="show-cols">

            {{-- LEFT --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- Session info card --}}
                <div style="background:#fff;border-radius:16px;border:1px solid var(--hair);padding:22px;">
                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px;">
                        @if($groupSession->subject)
                        <span style="background:rgba(10,100,188,.08);color:#0A64BC;padding:3px 12px;border-radius:999px;font-size:11px;font-weight:700;">{{ $groupSession->subject->name }}</span>
                        @endif
                        @php
                            $statusMap=['scheduled'=>['#DCFCE7','#166534','Scheduled'],'cancelled'=>['#FEE2E2','#991B1B','Cancelled'],'completed'=>['rgba(21,40,31,.06)','rgba(21,40,31,.5)','Completed'],'active'=>['rgba(10,100,188,.1)','#0A64BC','Live']];
                            [$sbg,$sfc,$slbl]=$statusMap[$groupSession->status]??['rgba(21,40,31,.06)','rgba(21,40,31,.5)',$groupSession->status];
                        @endphp
                        <span style="background:{{ $sbg }};color:{{ $sfc }};padding:3px 12px;border-radius:999px;font-size:11px;font-weight:700;">{{ $slbl }}</span>
                    </div>
                    <h2 style="font-family:'Fraunces',serif;font-size:22px;color:var(--ink);margin-bottom:14px;">{{ $groupSession->title }}</h2>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px;" class="meta-g">
                        @foreach([['fa-calendar','Date',$groupSession->scheduled_at->format('M j, Y')],['fa-clock','Time',$groupSession->scheduled_at->format('g:ia')],['fa-hourglass','Duration',$groupSession->duration_minutes.' min']] as [$ic,$lbl,$val])
                        <div style="background:rgba(21,40,31,.02);border-radius:10px;padding:12px;">
                            <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:3px;"><i class="fa-regular {{ $ic }} fa-xs" style="color:#0A64BC;margin-right:4px;"></i>{{ $lbl }}</p>
                            <p style="font-size:15px;font-weight:700;color:var(--ink);">{{ $val }}</p>
                        </div>
                        @endforeach
                    </div>
                    <style>.meta-g{grid-template-columns:1fr!important;}@media(min-width:400px){.meta-g{grid-template-columns:repeat(3,1fr)!important;}}</style>
                    @if($groupSession->description)
                    <p style="font-size:14px;color:var(--muted);line-height:1.75;">{{ $groupSession->description }}</p>
                    @endif
                    @if($groupSession->status === 'scheduled')
                    <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--hair);">
                        <form method="POST" action="{{ route('tutor.group-sessions.cancel', $groupSession) }}" onsubmit="return confirm('Are you sure you want to cancel this session? Enrolled students will be notified.');">
                            @csrf
                            <button type="submit" class="btn btn-danger" style="padding:8px 16px;font-size:12px;">
                                <i class="fa-solid fa-ban fa-xs"></i> Cancel Session
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                {{-- Enrolled students --}}
                <div style="background:#fff;border-radius:16px;border:1px solid var(--hair);overflow:hidden;">
                    <div style="padding:16px 20px;border-bottom:1px solid var(--hair);display:flex;align-items:center;justify-content:space-between;">
                        <h3 style="font-family:'Fraunces',serif;font-size:16px;color:var(--ink);">Enrolled Students</h3>
                        <span style="font-size:12px;font-weight:700;color:{{ $isFull ? '#991B1B' : '#10B981' }};">{{ $enrolledCount }}/{{ $groupSession->max_students }}</span>
                    </div>
                    @if($groupSession->enrollments->isEmpty())
                    <div style="padding:32px;text-align:center;">
                        <i class="fa-solid fa-user-slash" style="color:rgba(21,40,31,.12);font-size:28px;margin-bottom:10px;display:block;"></i>
                        <p style="font-size:13px;color:var(--muted);">No students enrolled yet.</p>
                    </div>
                    @else
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;font-size:13px;">
                            <thead>
                                <tr style="background:rgba(21,40,31,.02);">
                                    <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Student</th>
                                    <th style="padding:10px 12px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Session Code</th>
                                    <th style="padding:10px 12px;text-align:right;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Paid</th>
                                    <th style="padding:10px 12px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Enrolled At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupSession->enrollments as $enroll)
                                <tr style="border-top:1px solid var(--hair);">
                                    <td style="padding:12px 16px;">
                                        <div style="display:flex;align-items:center;gap:8px;">
                                            <div style="width:28px;height:28px;border-radius:8px;background:rgba(10,100,188,.08);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fa-solid fa-user fa-xs" style="color:#0A64BC;"></i></div>
                                            <div>
                                                <p style="font-weight:600;color:var(--ink);margin:0;">{{ $enroll->student?->name ?? 'Unknown' }}</p>
                                                <p style="font-size:11px;color:var(--muted);margin:0;">{{ $enroll->student?->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding:12px;font-family:monospace;font-size:13px;font-weight:700;color:var(--ink);">{{ $enroll->session_code ?? '—' }}</td>
                                    <td style="padding:12px;text-align:right;font-weight:700;color:#EEBE15;font-family:'Fraunces',serif;">
                                        KSh {{ number_format($enroll->payment?->amount ?? $groupSession->price) }}
                                    </td>
                                    <td style="padding:12px;font-size:12px;color:var(--muted);">{{ $enroll->created_at->format('M j, Y g:ia') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>

            {{-- RIGHT: sidebar summary --}}
            <div>
                <div style="background:#fff;border-radius:16px;border:1px solid var(--hair);padding:20px;position:sticky;top:20px;">
                    <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:14px;">Quick Stats</p>
                    @foreach([['Seats Filled',$enrolledCount.'/'.$groupSession->max_students,'#10B981'],['Price per Seat','KSh '.number_format($groupSession->price),'#EEBE15'],['Est. Earnings','KSh '.number_format($enrolledCount * $groupSession->price),'#0A64BC'],['Your Cut (40%)','KSh '.number_format($enrolledCount * $groupSession->price * 0.40),'#EEBE15']] as [$lbl,$val,$col])
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--hair);">
                        <span style="font-size:13px;color:var(--muted);">{{ $lbl }}</span>
                        <span style="font-size:14px;font-weight:700;color:{{ $col }};">{{ $val }}</span>
                    </div>
                    @endforeach

                    <div style="margin-top:16px;">
                        <p style="font-size:12px;color:var(--muted);margin-bottom:8px;">Capacity</p>
                        <div style="width:100%;height:8px;background:rgba(21,40,31,.06);border-radius:999px;overflow:hidden;">
                            <div style="width:{{ $fillPct }}%;height:100%;background:{{ $isFull ? '#E8522A' : ($fillPct > 70 ? '#EEBE15' : '#10B981') }};border-radius:999px;"></div>
                        </div>
                        <p style="font-size:11px;color:var(--muted);margin-top:5px;">{{ $fillPct }}% filled</p>
                    </div>

                    @if($canJoin)
                    <a href="{{ route('clinic.join', $groupSession) }}" class="btn btn-blue" style="width:100%;justify-content:center;margin-top:16px;">
                        <i class="fa-solid fa-video fa-xs"></i> Join as Moderator
                    </a>
                    @endif

                    {{-- series siblings --}}
                    @if($seriesSessions->count() > 1)
                    <div style="margin-top:18px;padding-top:14px;border-top:1px solid var(--hair);">
                        <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:10px;">Series Sessions</p>
                        @foreach($seriesSessions as $s)
                        @php $isThis = $s->id === $groupSession->id; @endphp
                        <div style="padding:8px 10px;border-radius:9px;background:{{ $isThis ? 'rgba(212,160,23,.08)' : 'rgba(21,40,31,.02)' }};border:1px solid {{ $isThis ? 'rgba(212,160,23,.25)' : 'transparent' }};margin-bottom:5px;display:flex;justify-content:space-between;align-items:center;gap:8px;">
                            <div>
                                <p style="font-size:12px;font-weight:600;color:var(--ink);margin:0;">Session {{ $s->session_number }}</p>
                                <p style="font-size:11px;color:var(--muted);margin:0;">{{ $s->scheduled_at->format('M j') }} · {{ $s->enrolledCount() }}/{{ $s->max_students }}</p>
                            </div>
                            @if(!$isThis)
                            <a href="{{ route('tutor.group-sessions.show', $s) }}" style="font-size:11px;color:#0A64BC;font-weight:600;">View</a>
                            @else
                            <span style="font-size:11px;color:#B7870A;font-weight:600;">Current</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <style>.show-cols{grid-template-columns:1fr!important;}@media(min-width:900px){.show-cols{grid-template-columns:1fr 320px!important;}}</style>
    </div>{{-- /content --}}
</div>{{-- /main --}}
</div>{{-- /layout --}}
@livewireScripts
</body>
</html>
