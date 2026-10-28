<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Group Sessions – Mwalimu Tutor Portal</title>
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
.sb-user{display:flex;align-items:center;gap:10px;}
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
.stat-card{background:#fff;border-radius:16px;border:1px solid var(--hair);padding:20px;display:flex;align-items:flex-start;gap:14px;}
.stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:all .15s;font-family:inherit;text-decoration:none;}
.btn-primary{background:#EEBE15;color:#15281F;}.btn-primary:hover{opacity:.9;}
.btn-blue{background:var(--trust-blue);color:#fff;}.btn-blue:hover{background:#120A8F;}
</style>
</head>
<body>
@php
$user    = auth()->user();
$profile = $user->tutorProfile;
$isApproved = $profile?->is_approved ?? false;
$pendingBookings = $user->bookingsAsTutor()->where('status','pending')->count();
$unreadNotifs = $user->unreadNotifications()->count();
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
            <a href="{{ route('tutor.dashboard') }}?tab=documents" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                My Documents
                @if(($profile?->verification_status ?? 'no_documents') === 'action_required')<span class="sb-badge">!</span>@endif
            </a>
            <a href="{{ route('tutor.subjects') }}" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                My Subjects
            </a>
        </div>
        @if($isApproved)
        <div class="sb-section">
            <div class="sb-label">Sessions</div>
            <a href="{{ route('tutor.dashboard') }}?tab=bookings" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Bookings
                @if($pendingBookings > 0)<span class="sb-badge">{{ $pendingBookings }}</span>@endif
            </a>
            <a href="{{ route('tutor.dashboard') }}?tab=history" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Session History
            </a>
        </div>
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
        <div class="sb-section">
            <div class="sb-label">Account</div>
            <a href="{{ route('tutor.dashboard') }}?tab=notifications" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                Notifications
                @if($unreadNotifs > 0)<span class="sb-badge">{{ $unreadNotifs }}</span>@endif
            </a>
            <a href="{{ route('tutor.dashboard') }}?tab=profile" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Profile Settings
            </a>
        </div>
    </nav>
    <div class="sb-footer">
        <div class="sb-user">
            <div style="width:34px;height:34px;border-radius:10px;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
            <div style="flex:1;min-width:0;"><div class="sb-uname">{{ $user->name }}</div><div class="sb-urole">Tutor</div></div>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="sb-logout" title="Logout"><svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></button>
            </form>
        </div>
    </div>
</aside>

{{-- MAIN --}}
<div class="main">
    <div class="topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button class="mob-toggle" @click="sidebarOpen=!sidebarOpen"><svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
            <span class="page-title">Group Sessions</span>
        </div>
        <a href="{{ route('tutor.group-sessions.create') }}" class="btn btn-primary" style="padding:8px 16px;font-size:13px;">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Session
        </a>
    </div>

    <div class="content">
        {{-- Flash --}}
        @if(session('success'))
        <div style="background:#DCFCE7;color:#166534;border:1px solid #BBF7D0;padding:12px 16px;border-radius:12px;font-size:13.5px;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div style="background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;padding:12px 16px;border-radius:12px;font-size:13.5px;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
        @endif

        {{-- STATS --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:28px;" class="stats-g">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(10,100,188,.1);"><i class="fa-solid fa-calendar-check" style="color:#0A64BC;"></i></div>
                <div><p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:2px;">Upcoming</p><p style="font-family:'Fraunces',serif;font-size:26px;font-weight:800;color:var(--ink);">{{ $upcomingCount }}</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(16,185,129,.1);"><i class="fa-solid fa-users" style="color:#10B981;"></i></div>
                <div><p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:2px;">Total Enrollments</p><p style="font-family:'Fraunces',serif;font-size:26px;font-weight:800;color:var(--ink);">{{ $totalEnrollments }}</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(212,160,23,.1);"><i class="fa-solid fa-coins" style="color:#EEBE15;"></i></div>
                <div><p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:2px;">Total Earnings</p><p style="font-family:'Fraunces',serif;font-size:26px;font-weight:800;color:var(--ink);">KSh {{ number_format($totalEarnings) }}</p></div>
            </div>
        </div>
        <style>.stats-g{grid-template-columns:1fr!important;}@media(min-width:640px){.stats-g{grid-template-columns:repeat(3,1fr)!important;}}</style>

        {{-- SESSIONS LIST --}}
        <div style="background:#fff;border-radius:16px;border:1px solid var(--hair);overflow:hidden;">
            <div style="padding:18px 20px;border-bottom:1px solid var(--hair);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                <div>
                    <h2 style="font-family:'Fraunces',serif;font-size:17px;color:var(--ink);">All Group Sessions</h2>
                    <p style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $sessions->total() }} session{{ $sessions->total() != 1 ? 's' : '' }} total</p>
                </div>
                <a href="{{ route('tutor.group-sessions.create') }}" class="btn btn-primary" style="padding:8px 16px;font-size:13px;">
                    <i class="fa-solid fa-plus fa-xs"></i> Create Session
                </a>
            </div>

            @if($sessions->isEmpty())
            <div style="padding:64px;text-align:center;">
                <div style="font-size:40px;margin-bottom:12px;"><i class="fa-solid fa-users" style="color:rgba(21,40,31,.12);"></i></div>
                <h3 style="font-family:'Fraunces',serif;font-size:19px;color:var(--ink);margin-bottom:8px;">No group sessions yet</h3>
                <p style="font-size:14px;color:var(--muted);margin-bottom:20px;">Create your first live group class and start earning.</p>
                <a href="{{ route('tutor.group-sessions.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus fa-xs"></i> Create Your First Session
                </a>
            </div>
            @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13.5px;">
                    <thead>
                        <tr style="background:rgba(21,40,31,.02);">
                            <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Session</th>
                            <th style="padding:11px 12px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Date</th>
                            <th style="padding:11px 12px;text-align:center;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Enrolled</th>
                            <th style="padding:11px 12px;text-align:right;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Price</th>
                            <th style="padding:11px 12px;text-align:center;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Status</th>
                            <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                        @php
                            $enrolled = $session->enrolledCount();
                            $max      = $session->max_students;
                        @endphp
                        <tr style="border-top:1px solid var(--hair);">
                            <td style="padding:14px 16px;">
                                <div style="display:flex;align-items:flex-start;gap:10px;">
                                    <div style="width:38px;height:38px;border-radius:10px;background:rgba(212,160,23,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="fa-solid fa-users" style="color:#EEBE15;font-size:15px;"></i>
                                    </div>
                                    <div>
                                        <p style="font-weight:600;color:var(--ink);margin-bottom:2px;">{{ Str::limit($session->title, 50) }}</p>
                                        @if($session->subject)<p style="font-size:11px;color:var(--muted);">{{ $session->subject->name }}</p>@endif
                                    </div>
                                </div>
                            </td>
                            <td style="padding:14px 12px;white-space:nowrap;color:var(--muted);">
                                {{ $session->scheduled_at->format('M j, Y') }}<br>
                                <span style="font-size:11px;">{{ $session->scheduled_at->format('g:ia') }} · {{ $session->duration_minutes }}min</span>
                            </td>
                            <td style="padding:14px 12px;text-align:center;">
                                <span style="font-weight:700;color:{{ $enrolled >= $max ? '#991B1B' : '#10B981' }};">{{ $enrolled }}/{{ $max }}</span>
                            </td>
                            <td style="padding:14px 12px;text-align:right;font-weight:700;color:#EEBE15;font-family:'Fraunces',serif;">
                                KSh {{ number_format($session->price) }}
                            </td>
                            <td style="padding:14px 12px;text-align:center;">
                                @php
                                    $statusMap = ['scheduled'=>['#DCFCE7','#166534','Scheduled'],'cancelled'=>['#FEE2E2','#991B1B','Cancelled'],'completed'=>['rgba(21,40,31,.06)','rgba(21,40,31,.5)','Completed'],'active'=>['rgba(10,100,188,.1)','#0A64BC','Live']];
                                    [$bg,$fc,$label] = $statusMap[$session->status] ?? ['rgba(21,40,31,.06)','rgba(21,40,31,.5)',$session->status];
                                @endphp
                                <span style="background:{{ $bg }};color:{{ $fc }};padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;">{{ $label }}</span>
                            </td>
                            <td style="padding:14px 16px;text-align:right;">
                                <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                                    <a href="{{ route('tutor.group-sessions.show', $session) }}" style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:8px;background:rgba(10,100,188,.08);color:#0A64BC;font-size:12px;font-weight:600;border:1px solid rgba(10,100,188,.15);">
                                        <i class="fa-solid fa-eye fa-xs"></i> View
                                    </a>
                                    @if($session->status === 'scheduled')
                                    <form method="POST" action="{{ route('tutor.group-sessions.cancel', $session) }}" onsubmit="return confirm('Cancel this group session?');">
                                        @csrf
                                        <button type="submit" style="display:inline-flex;align-items:center;gap:5px;padding:6px 10px;border-radius:8px;background:rgba(220,38,38,.06);color:#DC2626;font-size:12px;font-weight:600;border:1px solid rgba(220,38,38,.15);cursor:pointer;font-family:inherit;">
                                            <i class="fa-solid fa-ban fa-xs"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- pagination --}}
            @if($sessions->hasPages())
            <div style="padding:16px 20px;border-top:1px solid var(--hair);display:flex;justify-content:flex-end;gap:5px;flex-wrap:wrap;">
                @if($sessions->onFirstPage())
                <span style="padding:6px 14px;border-radius:8px;background:#F4F6F4;color:rgba(21,40,31,.35);font-size:12px;"><i class="fa-solid fa-chevron-left fa-xs"></i></span>
                @else
                <a href="{{ $sessions->previousPageUrl() }}" style="padding:6px 14px;border-radius:8px;background:#fff;color:var(--ink);font-size:12px;border:1px solid var(--hair);"><i class="fa-solid fa-chevron-left fa-xs"></i></a>
                @endif
                @foreach($sessions->getUrlRange(max(1,$sessions->currentPage()-2),min($sessions->lastPage(),$sessions->currentPage()+2)) as $pg => $url)
                <a href="{{ $url }}" style="padding:6px 14px;border-radius:8px;background:{{ $pg==$sessions->currentPage()?'#0A64BC':'#fff' }};color:{{ $pg==$sessions->currentPage()?'#fff':'var(--ink)' }};font-size:12px;border:1px solid {{ $pg==$sessions->currentPage()?'#0A64BC':'var(--hair)' }};">{{ $pg }}</a>
                @endforeach
                @if($sessions->hasMorePages())
                <a href="{{ $sessions->nextPageUrl() }}" style="padding:6px 14px;border-radius:8px;background:#fff;color:var(--ink);font-size:12px;border:1px solid var(--hair);"><i class="fa-solid fa-chevron-right fa-xs"></i></a>
                @else
                <span style="padding:6px 14px;border-radius:8px;background:#F4F6F4;color:rgba(21,40,31,.35);font-size:12px;"><i class="fa-solid fa-chevron-right fa-xs"></i></span>
                @endif
            </div>
            @endif
            @endif
        </div>
    </div>{{-- /content --}}
</div>{{-- /main --}}
</div>{{-- /layout --}}
@livewireScripts
</body>
</html>
