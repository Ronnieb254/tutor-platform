<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>My Subjects – Mwalimu Tutor Portal</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>
:root{--ink:#15281F;--cream:#FBF5E6;--trust-blue:#0A64BC;--coral:#E8522A;--gold:#EEBE15;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);--sidebar:#111C16;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:'DM Sans',sans-serif;color:var(--ink);background:#F2F5F2;}
a{color:inherit;text-decoration:none;}[x-cloak]{display:none!important;}
button{font-family:inherit;cursor:pointer;}
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
.sb-item svg{flex-shrink:0;opacity:.75;}
.sb-item.active svg{opacity:1;}
.sb-badge{margin-left:auto;background:var(--coral);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:999px;min-width:18px;text-align:center;}
.sb-footer{padding:14px;border-top:1px solid rgba(255,255,255,.06);}
.sb-user{display:flex;align-items:center;gap:10px;}
.sb-avatar{width:34px;height:34px;border-radius:10px;object-fit:cover;flex-shrink:0;}
.sb-uname{font-size:13px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.sb-urole{font-size:11px;color:rgba(255,255,255,.35);}
.sb-logout{width:28px;height:28px;border-radius:8px;border:none;background:rgba(255,255,255,.08);color:rgba(255,255,255,.45);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.sb-logout:hover{background:rgba(255,255,255,.16);color:#fff;}
.mob-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:49;}
@media(max-width:1023px){.sidebar{position:fixed;left:-256px;top:0;bottom:0;transition:left .25s;z-index:50;}.sidebar.open{left:0;}.mob-overlay.show{display:block;}}
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;}
.topbar{height:60px;background:#fff;border-bottom:1px solid var(--hair);display:flex;align-items:center;justify-content:space-between;padding:0 24px;flex-shrink:0;}
.topbar-left{display:flex;align-items:center;gap:12px;}
.mob-toggle{display:none;width:34px;height:34px;border-radius:9px;border:1px solid var(--hair);background:#fff;align-items:center;justify-content:center;color:var(--muted);}
@media(max-width:1023px){.mob-toggle{display:flex;}}
.page-title{font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:var(--ink);}
.topbar-right{display:flex;align-items:center;gap:10px;}
.tb-btn{width:34px;height:34px;border-radius:9px;border:1px solid var(--hair);background:#fff;display:flex;align-items:center;justify-content:center;color:var(--muted);transition:all .15s;position:relative;}
.tb-btn:hover{border-color:rgba(10,100,188,.4);color:var(--trust-blue);}
.notif-dot{position:absolute;top:-2px;right:-2px;width:8px;height:8px;background:var(--coral);border-radius:50%;border:2px solid #fff;}
.content{flex:1;overflow-y:auto;padding:24px;}
.card{background:#fff;border-radius:16px;border:1px solid var(--hair);}
.flash{padding:12px 16px;border-radius:12px;font-size:13.5px;font-weight:500;margin-bottom:18px;display:flex;align-items:center;gap:8px;}
.flash-ok{background:#DCFCE7;color:#166534;border:1px solid #BBF7D0;}
.flash-err{background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:all .15s;font-family:inherit;text-decoration:none;}
.btn-o{background:#fff;color:var(--ink);border:1.5px solid var(--hair);}.btn-o:hover{border-color:var(--trust-blue);color:var(--trust-blue);}
.btn-sm{padding:6px 12px;font-size:12px;border-radius:8px;}
.sec-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;}
.sec-title{font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);}
.sec-sub{font-size:12px;color:var(--muted);margin-top:1px;}
.subject-card{background:#fff;border-radius:14px;border:1.5px solid var(--hair);padding:14px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px;transition:all .15s;cursor:pointer;}
.subject-card:hover{border-color:rgba(10,100,188,.3);background:rgba(10,100,188,.02);}
.subject-card.selected{background:rgba(10,100,188,.06);border-color:var(--trust-blue);}
.subject-card input[type=checkbox]{width:20px;height:20px;cursor:pointer;accent-color:var(--trust-blue);}
.subject-chip{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:999px;background:rgba(10,100,188,.1);color:var(--trust-blue);font-size:12px;font-weight:600;}
.subject-chip i{cursor:pointer;opacity:.6;transition:opacity .15s;}
.subject-chip i:hover{opacity:1;color:var(--coral);}
.save-fab{position:fixed;bottom:24px;right:24px;padding:14px 32px;border-radius:999px;background:var(--mint);color:#fff;border:none;font-size:15px;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(16,185,129,.3);transition:all .2s;z-index:100;display:inline-flex;align-items:center;gap:8px;}
.save-fab:hover{background:#059669;transform:translateY(-2px);box-shadow:0 8px 16px rgba(16,185,129,.4);}
.save-fab:disabled{background:rgba(0,0,0,.2);cursor:not-allowed;transform:none;box-shadow:none;}
.search-input{height:42px;padding:0 14px 0 38px;border-radius:10px;border:1.5px solid var(--hair);font-size:14px;outline:none;transition:border-color .15s;font-family:inherit;width:100%;}
.search-input:focus{border-color:var(--trust-blue);}
.search-wrap{position:relative;}
.search-wrap i{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:14px;}
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
            <a href="{{ route('tutor.subjects') }}" class="sb-item active">
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
            <a href="{{ route('tutor.dashboard') }}?tab=chats" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Messages
            </a>
            <a href="{{ route('tutor.dashboard') }}?tab=availability" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Availability
            </a>
        </div>
        <div class="sb-section">
            <div class="sb-label">Finance</div>
            <a href="{{ route('tutor.dashboard') }}?tab=earnings" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                Earnings &amp; Wallet
            </a>
            <a href="{{ route('tutor.dashboard') }}?tab=payouts" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Payout Requests
            </a>
        </div>
        @endif
        @if($isApproved)
        <div class="sb-section">
            <div class="sb-label">Examiner</div>
            <a href="{{ route('examiner.dashboard') }}" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                Examiner Clinics
            </a>
        </div>
        @endif
        <div class="sb-section">
            <div class="sb-label">Account</div>
            <a href="{{ route('tutor.dashboard') }}?tab=notifications" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                Notifications
                @if($unreadNotifs > 0)<span class="sb-badge">{{ $unreadNotifs }}</span>@endif
            </a>
            <a href="{{ route('tutor.dashboard') }}?tab=profile" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                My Profile
            </a>
            <a href="{{ route('home') }}" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                View Public Site
            </a>
        </div>
    </nav>
    <div class="sb-footer">
        <div class="sb-user">
            <img src="{{ $user->avatar_url }}" class="sb-avatar" alt="{{ $user->name }}">
            <div style="flex:1;min-width:0;">
                <div class="sb-uname">{{ $user->name }}</div>
                <div class="sb-urole">
                    @if($isApproved)
                    <span style="color:var(--mint);">&#10003; Verified Tutor</span>
                    @else
                    <span style="color:rgba(255,255,255,.35);">Pending Verification</span>
                    @endif
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sb-logout" title="Sign out">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>
{{-- MAIN --}}
<div class="main">
    <header class="topbar">
        <div class="topbar-left">
            <button class="mob-toggle" @click="sidebarOpen=!sidebarOpen">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="page-title">My Subjects</div>
        </div>
        <div class="topbar-right">
            <a href="{{ route('tutor.dashboard') }}?tab=notifications" class="tb-btn" title="Notifications">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                @if($unreadNotifs > 0)<span class="notif-dot"></span>@endif
            </a>
            <div style="display:flex;align-items:center;gap:8px;padding:4px 10px 4px 4px;border-radius:999px;border:1.5px solid var(--hair);">
                <img src="{{ $user->avatar_url }}" style="width:26px;height:26px;border-radius:999px;object-fit:cover;">
                <span style="font-size:13px;font-weight:600;">{{ Illuminate\Support\Str::limit($user->name,18) }}</span>
            </div>
        </div>
    </header>

    <div class="content">
        @if(session('success'))
        <div class="flash flash-ok">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="flash flash-err">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            {{ session('error') }}
        </div>
        @endif

        <div class="sec-hdr">
            <div>
                <div class="sec-title">Manage Your Subjects</div>
                <div class="sec-sub">Select the subjects you teach — these appear on your public profile for students to find.</div>
            </div>
            <a href="{{ route('tutor.dashboard') }}" class="btn btn-o btn-sm">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
                Back to Dashboard
            </a>
        </div>

        {{-- Selected subjects preview --}}
        <div class="card" style="margin-bottom:20px;">
            <div style="padding:18px 22px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;border-bottom:1px solid var(--hair);">
                <div>
                    <h3 style="font-family:'Fraunces',serif;font-size:17px;color:var(--ink);margin-bottom:2px;">
                        Currently Teaching <span id="subject-count" style="color:var(--trust-blue);">{{ $tutorProfile->subjects->count() }}</span> Subject{{ $tutorProfile->subjects->count() !== 1 ? 's' : '' }}
                    </h3>
                    <p style="font-size:13px;color:var(--muted);">Click a subject below to add or remove it</p>
                </div>
                <div style="width:100%;max-width:280px;">
                    <div class="search-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="search-subjects" placeholder="Search subjects..." class="search-input">
                    </div>
                </div>
            </div>
            <div style="padding:18px 22px;background:#F9FAF9;">
                <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:10px;">Your Selected Subjects</p>
                <div id="selected-preview" style="display:flex;flex-wrap:wrap;gap:6px;min-height:30px;">
                    @forelse($tutorProfile->subjects as $subject)
                    <span class="subject-chip" data-subject-id="{{ $subject->id }}">
                        {{ $subject->name }}
                        <i class="fa-solid fa-xmark" onclick="deselectSubject({{ $subject->id }})"></i>
                    </span>
                    @empty
                    <span style="font-size:13px;color:var(--muted);">No subjects selected yet. Choose from the categories below.</span>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Subject categories --}}
        <form id="subjects-form" method="POST" action="{{ auth()->user()->isAdmin() ? route('admin.tutors.subjects.store', $tutorProfile) : route('tutor.subjects.store') }}">
            @csrf
            <div id="subjects-container">
                @foreach($categories as $category)
                <div class="category-section" data-category="{{ strtolower($category->name) }}" style="margin-bottom:24px;">
                    <h4 style="font-family:'Fraunces',serif;font-size:16px;color:var(--ink);margin-bottom:12px;display:flex;align-items:center;gap:8px;">
                        <div style="width:28px;height:28px;border-radius:8px;background:rgba(212,160,23,.12);display:flex;align-items:center;justify-content:center;">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#EEBE15" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        {{ $category->name }}
                        <span style="font-size:12px;color:var(--muted);font-weight:400;">({{ $category->subjects->count() }})</span>
                    </h4>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:10px;">
                        @foreach($category->subjects as $subject)
                        <div class="subject-card {{ $tutorProfile->subjects->contains($subject->id) ? 'selected' : '' }}"
                             data-subject-id="{{ $subject->id }}"
                             data-subject-name="{{ strtolower($subject->name) }}"
                             onclick="toggleSubject({{ $subject->id }}, '{{ addslashes($subject->name) }}')">
                            <div style="display:flex;align-items:center;gap:10px;flex:1;">
                                <div style="width:36px;height:36px;border-radius:10px;background:rgba(10,100,188,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#0A64BC" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                </div>
                                <div>
                                    <div style="font-size:14px;font-weight:600;color:var(--ink);">{{ $subject->name }}</div>
                                    <div style="font-size:11px;color:var(--muted);">{{ $category->name }}</div>
                                </div>
                            </div>
                            <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}"
                                   {{ $tutorProfile->subjects->contains($subject->id) ? 'checked' : '' }}>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <button type="submit" class="save-fab" id="save-btn">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Save Changes
            </button>
        </form>
    </div>
</div>

</div>

<script>
const selectedSubjects = new Set({{ json_encode($tutorProfile->subjects->pluck('id')->toArray()) }});
const subjectNames = {};

document.querySelectorAll('.subject-card').forEach(card => {
    const id = parseInt(card.dataset.subjectId);
    const nameEl = card.querySelector('div > div > div > div:first-child');
    if (nameEl) subjectNames[id] = nameEl.textContent.trim();
});

function toggleSubject(id, name) {
    const card = document.querySelector('.subject-card[data-subject-id="' + id + '"]');
    const checkbox = card.querySelector('input[type="checkbox"]');
    if (selectedSubjects.has(id)) {
        selectedSubjects.delete(id);
        card.classList.remove('selected');
        checkbox.checked = false;
        delete subjectNames[id];
    } else {
        selectedSubjects.add(id);
        card.classList.add('selected');
        checkbox.checked = true;
        subjectNames[id] = name;
    }
    updatePreview();
    updateCount();
}

function deselectSubject(id) {
    selectedSubjects.delete(id);
    const card = document.querySelector('.subject-card[data-subject-id="' + id + '"]');
    if (card) {
        card.classList.remove('selected');
        card.querySelector('input[type="checkbox"]').checked = false;
    }
    delete subjectNames[id];
    updatePreview();
    updateCount();
}

function updatePreview() {
    const preview = document.getElementById('selected-preview');
    if (selectedSubjects.size === 0) {
        preview.innerHTML = '<span style="font-size:13px;color:var(--muted);">No subjects selected yet. Choose from the categories below.</span>';
        return;
    }
    preview.innerHTML = Array.from(selectedSubjects).map(function(id) {
        var name = subjectNames[id] || document.querySelector('.subject-card[data-subject-id="' + id + '"]')?.dataset.subjectName || 'Subject';
        return '<span class="subject-chip" data-subject-id="' + id + '">' + name + '<i class="fa-solid fa-xmark" onclick="deselectSubject(' + id + ')"></i></span>';
    }).join('');
}

function updateCount() {
    document.getElementById('subject-count').textContent = selectedSubjects.size;
}

document.getElementById('search-subjects').addEventListener('input', function(e) {
    var query = e.target.value.toLowerCase();
    document.querySelectorAll('.category-section').forEach(function(cat) {
        var hasVisible = false;
        cat.querySelectorAll('.subject-card').forEach(function(card) {
            var name = card.dataset.subjectName;
            if (name.includes(query)) { card.style.display = 'flex'; hasVisible = true; }
            else { card.style.display = 'none'; }
        });
        cat.style.display = hasVisible ? 'block' : 'none';
    });
});

document.getElementById('subjects-form').addEventListener('submit', function(e) {
    if (selectedSubjects.size === 0) {
        e.preventDefault();
        alert('Please select at least one subject');
        return false;
    }
});
</script>
</body>
</html>
