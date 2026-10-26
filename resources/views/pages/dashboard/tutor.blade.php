<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Tutor Dashboard – Mwalimu</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@livewireStyles
<style>
:root{--ink:#15281F;--cream:#FBF5E6;--trust-blue:#0A64BC;--coral:#E8522A;--gold:#EEBE15;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);--sidebar:#111C16;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:'DM Sans',sans-serif;color:var(--ink);background:#F2F5F2;}
a{color:inherit;text-decoration:none;}[x-cloak]{display:none!important;}
button{font-family:inherit;cursor:pointer;}

/* Layout */
.layout{display:flex;height:100vh;overflow:hidden;}

/* Sidebar */
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

/* Mobile sidebar */
.mob-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:49;}
@media(max-width:1023px){
    .sidebar{position:fixed;left:-256px;top:0;bottom:0;transition:left .25s;z-index:50;}
    .sidebar.open{left:0;}
    .mob-overlay.show{display:block;}
}

/* Main */
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

/* Content */
.content{flex:1;overflow-y:auto;padding:24px;}

/* Cards */
.card{background:#fff;border-radius:16px;border:1px solid var(--hair);}
.card-p{padding:20px;}

/* Stat cards */
.stat{background:#fff;border-radius:14px;border:1px solid var(--hair);padding:18px;position:relative;overflow:hidden;}
.stat-icon{width:42px;height:42px;border-radius:11px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;}
.stat-lbl{font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;}
.stat-val{font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:var(--ink);line-height:1;}
.stat-sub{font-size:12px;color:var(--muted);margin-top:5px;}

/* Status banners */
.status-banner{border-radius:14px;padding:16px 20px;display:flex;align-items:flex-start;gap:14px;margin-bottom:20px;}
.status-pending{background:#FEF9C3;border:1px solid #FDE68A;}
.status-rejected{background:#FEF2F2;border:1px solid #FECACA;}
.status-approved{background:#F0FDF4;border:1px solid #BBF7D0;}

/* Badges */
.bdg{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;}
.bdg-green{background:#DCFCE7;color:#166534;}
.bdg-amber{background:#FEF9C3;color:#854D0E;}
.bdg-red{background:#FEE2E2;color:#991B1B;}
.bdg-trust-blue{background:rgba(10,100,188,.1);color:var(--trust-blue);}
.bdg-blue{background:#DBEAFE;color:#1E40AF;}
.bdg-gray{background:#F3F4F6;color:#6B7280;}
.bdg-mint{background:rgba(16,185,129,.1);color:var(--mint);}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:all .15s;font-family:inherit;}
.btn-v{background:var(--trust-blue);color:#fff;}.btn-v:hover{background:#120A8F;}
.btn-o{background:#fff;color:var(--ink);border:1.5px solid var(--hair);}.btn-o:hover{border-color:var(--trust-blue);color:var(--trust-blue);}
.btn-sm{padding:6px 12px;font-size:12px;border-radius:8px;}
.btn-danger{background:#FEE2E2;color:#991B1B;}
.btn-mint{background:var(--mint);color:#fff;}

/* Table */
table{width:100%;border-collapse:collapse;}
thead th{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);padding:10px 14px;text-align:left;border-bottom:1px solid var(--hair);white-space:nowrap;}
tbody tr{border-bottom:1px solid rgba(21,40,31,.05);transition:background .1s;}
tbody tr:hover{background:#FAFCFA;}
tbody tr:last-child{border-bottom:none;}
td{padding:12px 14px;font-size:13.5px;vertical-align:middle;}

/* Avatar */
.av{display:flex;align-items:center;justify-content:center;border-radius:10px;font-weight:700;color:#fff;flex-shrink:0;}

/* Section header */
.sec-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;}
.sec-title{font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);}
.sec-sub{font-size:12px;color:var(--muted);margin-top:1px;}

/* Doc upload card */
.doc-card{border:2px dashed var(--hair);border-radius:14px;padding:20px;display:flex;align-items:center;justify-content:space-between;gap:12px;transition:border-color .15s;}
.doc-card:hover{border-color:rgba(10,100,188,.4);}
.doc-card.uploaded{border-style:solid;border-color:var(--hair);background:#FAFCFA;}
.doc-card.approved{border-color:var(--mint);background:#F0FDF4;}
.doc-card.rejected{border-color:var(--coral);background:#FEF2F2;}

/* Chat bubble */
.msg{display:flex;gap:10px;margin-bottom:14px;}
.msg.mine{flex-direction:row-reverse;}
.msg-bubble{max-width:75%;padding:10px 14px;border-radius:16px;font-size:13.5px;line-height:1.6;}
.msg.theirs .msg-bubble{background:#F4F6F4;color:var(--ink);border-radius:4px 16px 16px 16px;}
.msg.mine .msg-bubble{background:var(--trust-blue);color:#fff;border-radius:16px 4px 16px 16px;}
.msg-time{font-size:11px;color:var(--muted);margin-top:3px;}

/* Flash */
.flash{padding:12px 16px;border-radius:12px;font-size:13.5px;font-weight:500;margin-bottom:18px;display:flex;align-items:center;gap:8px;}
.flash-ok{background:#DCFCE7;color:#166534;border:1px solid #BBF7D0;}
</style>
</head>
<body>
@php
$user    = auth()->user();
$profile = $user->tutorProfile;
$isApproved = $profile?->is_approved ?? false;
$verificationStatus = $profile?->verification_status ?? 'no_documents';
$pendingBookings = $user->bookingsAsTutor()->where('status','pending')->count();
$unreadNotifs = $user->unreadNotifications()->count();
$activeTab = request('tab','overview');
@endphp

<div class="layout" x-data="{ sidebarOpen: false, tab: '{{ $activeTab }}' }">

{{-- Sidebar overlay --}}
<div class="mob-overlay" :class="sidebarOpen ? 'show' : ''" @click="sidebarOpen=false"></div>

{{-- SIDEBAR --}}
<aside class="sidebar" :class="sidebarOpen ? 'open' : ''">
    <div class="sb-logo">
        <svg width="30" height="30" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#0A64BC"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
        <div>
            <div class="sb-logo-text">Mwalimu</div>
            <div class="sb-logo-sub">Tutor Portal</div>
        </div>
    </div>

    <nav class="sb-nav">
        <div class="sb-section">
            <div class="sb-label">Main</div>
            <button class="sb-item" :class="tab==='overview'?'active':''" @click="tab='overview';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Overview
            </button>
            <button class="sb-item" :class="tab==='documents'?'active':''" @click="tab='documents';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                My Documents
                @if($verificationStatus === 'action_required')
                <span class="sb-badge">!</span>
                @endif
            </button>
            <a href="{{ route('tutor.subjects') }}" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                My Subjects
            </a>
        </div>

        @if($isApproved)
        <div class="sb-section">
            <div class="sb-label">Sessions</div>
            <button class="sb-item" :class="tab==='bookings'?'active':''" @click="tab='bookings';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Bookings
                @if($pendingBookings > 0)<span class="sb-badge">{{ $pendingBookings }}</span>@endif
            </button>
            <button class="sb-item" :class="tab==='history'?'active':''" @click="tab='history';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Session History
            </button>
            <button class="sb-item" :class="tab==='chats'?'active':''" @click="tab='chats';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Messages
            </button>
            <button class="sb-item" :class="tab==='availability'?'active':''" @click="tab='availability';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Availability
            </button>
        </div>

        <div class="sb-section">
            <div class="sb-label">Finance</div>
            <button class="sb-item" :class="tab==='earnings'?'active':''" @click="tab='earnings';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                Earnings & Wallet
            </button>
            <button class="sb-item" :class="tab==='payouts'?'active':''" @click="tab='payouts';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Payout Requests
            </button>
        </div>
        @endif

        <div class="sb-section">
            <div class="sb-label">Account</div>
            <button class="sb-item" :class="tab==='notifications'?'active':''" @click="tab='notifications';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                Notifications
                @if($unreadNotifs > 0)<span class="sb-badge">{{ $unreadNotifs }}</span>@endif
            </button>
            <button class="sb-item" :class="tab==='profile'?'active':''" @click="tab='profile';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                My Profile
            </button>
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
                    <span style="color:var(--mint);">✓ Verified Tutor</span>
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
    {{-- Topbar --}}
    <header class="topbar">
        <div class="topbar-left">
            <button class="mob-toggle" @click="sidebarOpen=!sidebarOpen">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="page-title" x-text="{
                overview:'Dashboard Overview',documents:'My Documents',bookings:'Bookings',history:'Session History',
                chats:'Messages',availability:'Availability',earnings:'Earnings & Wallet',
                payouts:'Payout Requests',notifications:'Notifications',profile:'My Profile'
            }[tab] || 'Dashboard'"></div>
        </div>
        <div class="topbar-right">
            <button class="tb-btn" @click="tab='notifications'" title="Notifications">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                @if($unreadNotifs > 0)<span class="notif-dot"></span>@endif
            </button>
            <div style="display:flex;align-items:center;gap:8px;padding:4px 10px 4px 4px;border-radius:999px;border:1.5px solid var(--hair);">
                <img src="{{ $user->avatar_url }}" style="width:26px;height:26px;border-radius:999px;object-fit:cover;">
                <span style="font-size:13px;font-weight:600;">{{ Str::limit($user->name,18) }}</span>
            </div>
        </div>
    </header>

    {{-- Content --}}
    <div class="content">
        @if(session('success'))
        <div class="flash flash-ok">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
        @endif

{{-- ═══════════════════════ OVERVIEW TAB ═══════════════════════ --}}
<div x-show="tab==='overview'">
    @php
    $wallet = $user->wallet;
    $thisMonth = $wallet?->transactions()->where('type','credit')->whereMonth('created_at',now()->month)->sum('amount') ?? 0;
    $upcoming = $user->bookingsAsTutor()->with(['student','subject'])->upcoming()->orderBy('scheduled_at')->take(5)->get();
    $completedCount = $user->bookingsAsTutor()->where('status','completed')->count();
    @endphp

    {{-- Verification Banner --}}
    @if(!$isApproved)
    @php
        $docs = $profile?->documents ?? collect();
        $uploadedTypes = $docs->pluck('type')->toArray();
        $requiredTypes = ['national_id', 'tsc_certificate', 'degree_certificate', 'passport_photo'];
        $missingTypes = array_diff($requiredTypes, $uploadedTypes);
        $missingCount = count($missingTypes);
        $docNames = ['national_id' => 'National ID', 'tsc_certificate' => 'TSC Certificate', 'degree_certificate' => 'Academic Certificate', 'passport_photo' => 'Passport Photo'];
        $missingNames = array_map(fn($t) => $docNames[$t], $missingTypes);
    @endphp
    <div class="status-banner {{ $verificationStatus==='action_required' ? 'status-rejected' : ($verificationStatus==='under_review' ? 'status-pending' : 'status-pending') }}">
        <div style="font-size:28px;flex-shrink:0;">
            {{ $verificationStatus==='action_required' ? '⚠️' : ($verificationStatus==='under_review' ? '⏳' : '📄') }}
        </div>
        <div>
            <div style="font-weight:700;font-size:15px;color:var(--ink);">
                {{ $verificationStatus==='action_required' ? 'Action Required — Document Rejected' : ($verificationStatus==='under_review' ? 'Profile Under Review' : 'Upload All Required Documents') }}
            </div>
            <div style="font-size:13px;color:var(--muted);margin-top:3px;line-height:1.6;">
                @if($verificationStatus==='action_required')
                    One or more documents were rejected. Please re-upload the required documents to continue.
                    <button @click="tab='documents'" style="background:none;border:none;color:var(--coral);font-weight:700;cursor:pointer;font-size:13px;font-family:inherit;padding:0;margin-left:6px;">View Documents →</button>
                @elseif($verificationStatus==='under_review')
                    Our team is reviewing your profile and documents. You'll receive an email once approved — usually within 24–48 hours.
                @else
                    You need to upload <strong>all 3 required documents</strong> before we can review your profile.
                    @if($missingCount > 0)
                        <span style="color:var(--coral);font-weight:700;">{{ $missingCount }} remaining: {{ implode(', ', $missingNames) }}</span>
                    @endif
                    <button @click="tab='documents'" style="background:none;border:none;color:#92400E;font-weight:700;cursor:pointer;font-size:13px;font-family:inherit;padding:0;margin-left:6px;">Upload Now →</button>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="status-banner status-approved" style="margin-bottom:20px;">
        <div style="font-size:24px;flex-shrink:0;">✅</div>
        <div>
            <div style="font-weight:700;font-size:15px;color:#166534;">Your profile is live!</div>
            <div style="font-size:13px;color:#4ADE80;margin-top:2px;">Students can find and book sessions with you. {{ $pendingBookings > 0 ? "You have $pendingBookings pending booking(s)." : '' }}</div>
        </div>
    </div>
    @endif

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:20px;" class="stats-g">
        <div class="stat">
            <div class="stat-icon" style="background:rgba(10,100,188,.1);">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#0A64BC" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            </div>
            <div class="stat-lbl">Wallet Balance</div>
            <div class="stat-val">KSh {{ number_format($wallet?->balance ?? 0) }}</div>
            <div class="stat-sub">This month: KSh {{ number_format($thisMonth) }}</div>
        </div>
        <div class="stat">
            <div class="stat-icon" style="background:rgba(16,185,129,.1);">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#10B981" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="stat-lbl">Upcoming Sessions</div>
            <div class="stat-val">{{ $upcoming->count() }}</div>
            <div class="stat-sub">{{ $pendingBookings }} pending confirmation</div>
        </div>
        <div class="stat">
            <div class="stat-icon" style="background:rgba(232,82,42,.1);">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E8522A" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="stat-lbl">Completed Sessions</div>
            <div class="stat-val">{{ $completedCount }}</div>
            <div class="stat-sub">Rating: ★ {{ number_format($profile?->rating_avg ?? 0,1) }} ({{ $profile?->total_reviews ?? 0 }} reviews)</div>
        </div>
        <div class="stat">
            <div class="stat-icon" style="background:rgba(212,160,23,.1);">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#EEBE15" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <div class="stat-lbl">My Rate</div>
            <div class="stat-val" style="font-size:22px;">KSh {{ number_format($profile?->hourly_rate ?? 0) }}/hr</div>
            <div class="stat-sub">{{ $profile?->experience_years ?? 0 }} years experience</div>
        </div>
    </div>
    <style>.stats-g{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:768px){.stats-g{grid-template-columns:repeat(4,1fr)!important;}}</style>

    {{-- Earnings Widget --}}
    @if(isset($earnings))
        @include('components.earnings-widget', ['earnings' => $earnings])
    @endif

    @if($isApproved && $upcoming->isNotEmpty())
    <div class="card" style="margin-bottom:20px;">
        <div style="padding:18px 20px;border-bottom:1px solid var(--hair);" class="sec-hdr">
            <div>
                <div class="sec-title">Upcoming Sessions</div>
                <div class="sec-sub">Your next scheduled sessions</div>
            </div>
            <button @click="tab='bookings'" class="btn btn-o btn-sm">View All</button>
        </div>
        <div style="padding:8px;">
            @foreach($upcoming as $b)
            <div style="display:flex;align-items:center;gap:12px;padding:12px;border-radius:10px;transition:background .1s;" onmouseover="this.style.background='#F4F6F4'" onmouseout="this.style.background='transparent'">
                <div class="av" style="width:40px;height:40px;background:#{{ ['0A64BC','10B981','E8522A','EEBE15'][$loop->index%4] }};border-radius:11px;font-size:15px;">{{ substr($b->student->name,0,1) }}</div>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;font-size:13.5px;">{{ $b->student->name }}</div>
                    <div style="font-size:12px;color:var(--muted);">{{ $b->subject?->name ?? 'General' }} · {{ $b->duration_minutes }}min</div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-size:13px;font-weight:600;color:var(--ink);">{{ $b->scheduled_at->format('M j') }}</div>
                    <div style="font-size:12px;color:var(--muted);">{{ $b->scheduled_at->format('g:ia') }}</div>
                </div>
                <div style="flex-shrink:0;">
                    @if($b->status === 'confirmed')
                    <a href="{{ route('bookings.show',$b->uuid) }}" class="btn btn-mint btn-sm">{{ $b->canJoin() ? 'Join Now' : 'Open Session' }}</a>
                    @else
                    <span class="bdg bdg-blue">{{ ucfirst($b->status) }}</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Quick links --}}
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;" class="quick-g">
        @foreach([
            ['documents','📄','Documents','Check your verification status'],
            ['bookings','📅','Bookings','View all session bookings'],
            ['earnings','💰','Earnings','Check wallet & history'],
            ['availability','🕐','Availability','Set your schedule'],
        ] as [$t,$ic,$label,$desc])
        <button @click="tab='{{ $t }}'" style="background:#fff;border:1px solid var(--hair);border-radius:14px;padding:18px;text-align:left;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:14px;" onmouseover="this.style.borderColor='rgba(10,100,188,.4)';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='var(--hair)';this.style.transform='translateY(0)'">
            <div style="font-size:26px;width:48px;height:48px;background:rgba(10,100,188,.08);border-radius:12px;display:flex;align-items:center;justify-content:center;">{{ $ic }}</div>
            <div>
                <div style="font-weight:700;font-size:14px;color:var(--ink);">{{ $label }}</div>
                <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $desc }}</div>
            </div>
        </button>
        @endforeach
    </div>
    <style>.quick-g{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:768px){.quick-g{grid-template-columns:repeat(4,1fr)!important;}}</style>
</div>

{{-- ═══════════════════════ DOCUMENTS TAB ═══════════════════════ --}}
{{-- 📁 pages/dashboard/partials/documents.blade.php --}}
@include('pages.dashboard.partials.documents')

{{-- ═══════════════════════ BOOKINGS TAB ═══════════════════════ --}}
<div x-show="tab==='bookings' || tab==='history'" x-cloak>
    @php
    $bFilter = request('bstatus','all');
    $allBookings = $user->bookingsAsTutor()->with(['student','subject'])->latest('scheduled_at')->get();
    $upcomingB = $allBookings->whereIn('status',['pending','confirmed'])->where('scheduled_at','>',now());
    $completedB = $allBookings->where('status','completed');
    $cancelledB = $allBookings->where('status','cancelled');
    @endphp

    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
        @foreach([['All',$allBookings->count(),'bdg-gray'],['Upcoming',$upcomingB->count(),'bdg-blue'],['Completed',$completedB->count(),'bdg-green'],['Cancelled',$cancelledB->count(),'bdg-amber']] as [$l,$c,$b])
        <div class="stat" style="padding:14px 16px;text-align:center;">
            <div style="font-family:'Fraunces',serif;font-size:26px;font-weight:700;color:var(--ink);">{{ $c }}</div>
            <div style="font-size:12px;color:var(--muted);">{{ $l }}</div>
        </div>
        @endforeach
    </div>

    {{-- Upcoming --}}
    @if($upcomingB->isNotEmpty())
    <div class="card" style="margin-bottom:20px;" x-show="tab==='bookings'">
        <div style="padding:16px 20px;border-bottom:1px solid var(--hair);">
            <div class="sec-title">Upcoming Sessions</div>
        </div>
        @foreach($upcomingB->sortBy('scheduled_at') as $b)
        <div style="padding:14px 20px;border-bottom:1px solid rgba(21,40,31,.05);display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <div class="av" style="width:44px;height:44px;background:#0A64BC;border-radius:12px;font-size:16px;">{{ substr($b->student->name,0,1) }}</div>
            <div style="flex:1;min-width:160px;">
                <div style="font-weight:600;font-size:14px;">{{ $b->student->name }}</div>
                <div style="font-size:12px;color:var(--muted);">{{ $b->subject?->name ?? 'General' }} · {{ $b->duration_minutes }}min · KSh {{ number_format($b->tutor_earning) }}</div>
            </div>
            <div style="text-align:right;">
                <div style="font-weight:600;font-size:13px;">{{ $b->scheduled_at->format('D, M j') }}</div>
                <div style="font-size:12px;color:var(--muted);">{{ $b->scheduled_at->format('g:ia') }}</div>
            </div>
            @php $sc=['pending'=>'bdg-amber','confirmed'=>'bdg-blue','completed'=>'bdg-green','cancelled'=>'bdg-gray'][$b->status]??'bdg-gray'; @endphp
            <span class="bdg {{ $sc }}">{{ ucfirst($b->status) }}</span>
            @if($b->session_code_tutor)
            <div style="background:rgba(10,100,188,.08);border:1px solid rgba(10,100,188,.2);border-radius:10px;padding:6px 12px;text-align:center;">
                <div style="font-size:10px;color:var(--muted);font-weight:600;">SESSION CODE</div>
                <div style="font-family:monospace;font-size:14px;font-weight:800;color:var(--trust-blue);">{{ $b->session_code_tutor }}</div>
            </div>
            @endif
            @if($b->status === 'confirmed')
            <a href="{{ route('bookings.show',$b->uuid) }}" class="btn btn-mint btn-sm">{{ $b->canJoin() ? 'Join Session' : 'Open Session' }}</a>
            @else
            <a href="{{ route('bookings.show',$b->uuid) }}" class="btn btn-o btn-sm">View</a>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    {{-- All bookings table --}}
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid var(--hair);">
            <div class="sec-title">Session History</div>
            <div class="sec-sub">Every session you have taught or have scheduled</div>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead><tr>
                    <th>Student</th><th>Subject</th><th>Date & Time</th><th>Duration</th><th>Earnings</th><th>Code</th><th>Status</th><th></th>
                </tr></thead>
                <tbody>
                    @forelse($allBookings as $b)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div class="av" style="width:30px;height:30px;background:#0A64BC;border-radius:8px;font-size:12px;">{{ substr($b->student->name,0,1) }}</div>
                                {{ $b->student->name }}
                            </div>
                        </td>
                        <td style="color:var(--muted);">{{ $b->subject?->name ?? 'General' }}</td>
                        <td style="white-space:nowrap;font-size:13px;">{{ $b->scheduled_at->format('M j, Y g:ia') }}</td>
                        <td>{{ $b->duration_minutes }}min</td>
                        <td style="font-weight:600;color:var(--mint);">KSh {{ number_format($b->tutor_earning) }}</td>
                        <td>
                            @if($b->session_code_tutor)
                            <code style="background:rgba(10,100,188,.08);color:var(--trust-blue);padding:2px 7px;border-radius:6px;font-size:12px;font-weight:700;">{{ $b->session_code_tutor }}</code>
                            @else
                            <span style="color:var(--muted);font-size:12px;">—</span>
                            @endif
                        </td>
                        <td>
                            @php $sc=['pending'=>'bdg-amber','confirmed'=>'bdg-blue','completed'=>'bdg-green','cancelled'=>'bdg-gray','no_show'=>'bdg-red'][$b->status]??'bdg-gray'; @endphp
                            <span class="bdg {{ $sc }}">{{ ucfirst($b->status) }}</span>
                        </td>
                        <td>
                            @if($b->status === 'confirmed')
                            <a href="{{ route('bookings.show',$b->uuid) }}" class="btn btn-mint btn-sm">{{ $b->canJoin() ? 'Join' : 'Open' }}</a>
                            @else
                            <a href="{{ route('bookings.show',$b->uuid) }}" class="btn btn-o btn-sm">View</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--muted);">No bookings yet. Once students book sessions with you, they'll appear here.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════════════════════ CHATS TAB ═══════════════════════ --}}
<div x-show="tab==='chats'" x-cloak>
    @php
    $chatBookings = $user->bookingsAsTutor()
        ->with(['student','messages' => fn($q) => $q->latest()->limit(1)])
        ->whereIn('status',['confirmed','completed'])
        ->latest()
        ->get();
    @endphp

    <div style="display:grid;grid-template-columns:320px 1fr;gap:16px;height:calc(100vh - 160px);min-height:500px;" class="chat-layout">

        {{-- Conversation list --}}
        <div class="card" style="display:flex;flex-direction:column;overflow:hidden;" x-data="{ activeBookingId: {{ $chatBookings->first()?->id ?? 'null' }} }">
            <div style="padding:14px 16px;border-bottom:1px solid var(--hair);">
                <div class="sec-title">Conversations</div>
                <div style="font-size:12px;color:var(--muted);margin-top:1px;">{{ $chatBookings->count() }} active chats</div>
            </div>
            <div style="flex:1;overflow-y:auto;">
                @forelse($chatBookings as $cb)
                @php $lastMsg = $cb->messages->first(); $unread = $cb->messages()->where('receiver_id',$user->id)->whereNull('read_at')->count(); @endphp
                <div @click="activeBookingId={{ $cb->id }}"
                     :style="activeBookingId==={{ $cb->id }} ? 'background:rgba(10,100,188,.06);' : ''"
                     style="display:flex;align-items:center;gap:12px;padding:14px 16px;cursor:pointer;border-bottom:1px solid rgba(21,40,31,.04);transition:background .1s;"
                     onmouseover="if(this.getAttribute('data-active')!=='true')this.style.background='#F9FAF9'" onmouseout="if(this.getAttribute('data-active')!=='true')this.style.background='transparent'">
                    <div class="av" style="width:42px;height:42px;background:#{{ substr(md5($cb->student->email),0,6) }};border-radius:12px;font-size:16px;flex-shrink:0;">{{ substr($cb->student->name,0,1) }}</div>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:4px;">
                            <span style="font-weight:600;font-size:13.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $cb->student->name }}</span>
                            @if($unread > 0)<span class="bdg bdg-trust-blue" style="font-size:10px;padding:2px 6px;flex-shrink:0;">{{ $unread }}</span>@endif
                        </div>
                        <div style="font-size:12px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px;">
                            {{ $lastMsg ? Str::limit($lastMsg->body, 40) : $cb->subject?->name.' session' }}
                        </div>
                        <div style="font-size:11px;color:rgba(21,40,31,.35);margin-top:2px;">{{ $lastMsg ? $lastMsg->created_at->diffForHumans() : $cb->scheduled_at->format('M j') }}</div>
                    </div>
                </div>
                @empty
                <div style="padding:40px 20px;text-align:center;color:var(--muted);">
                    <div style="font-size:32px;margin-bottom:8px;">💬</div>
                    <div style="font-size:14px;">No conversations yet</div>
                    <div style="font-size:12px;margin-top:4px;">Messages appear here after a booking is confirmed</div>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Chat window --}}
        <div class="card" style="display:flex;flex-direction:column;overflow:hidden;" x-data="{ activeBookingId: {{ $chatBookings->first()?->id ?? 'null' }} }">
            @if($chatBookings->isNotEmpty())
            @php $firstBooking = $chatBookings->first(); @endphp
            <div style="padding:14px 20px;border-bottom:1px solid var(--hair);display:flex;align-items:center;gap:12px;">
                <div class="av" style="width:38px;height:38px;background:#0A64BC;border-radius:10px;font-size:14px;">{{ substr($firstBooking->student->name,0,1) }}</div>
                <div>
                    <div style="font-weight:700;font-size:14px;">{{ $firstBooking->student->name }}</div>
                    <div style="font-size:12px;color:var(--muted);">{{ $firstBooking->subject?->name ?? 'General' }} · {{ $firstBooking->scheduled_at->format('M j, g:ia') }}</div>
                </div>
                <a href="{{ route('bookings.show', $firstBooking->uuid) }}" class="btn btn-o btn-sm" style="margin-left:auto;">View Booking</a>
            </div>
            @php $messages = $firstBooking->messages()->with('sender')->oldest()->get(); @endphp
            <div style="flex:1;overflow-y:auto;padding:20px;display:flex;flex-direction:column;gap:0;" id="chat-messages">
                @forelse($messages as $msg)
                @php $isMine = $msg->sender_id === $user->id; @endphp
                <div class="msg {{ $isMine ? 'mine' : 'theirs' }}">
                    @if(!$isMine)
                    <div class="av" style="width:32px;height:32px;background:#0A64BC;border-radius:9px;font-size:12px;flex-shrink:0;">{{ substr($msg->sender->name,0,1) }}</div>
                    @endif
                    <div>
                        <div class="msg-bubble">{{ $msg->body }}</div>
                        <div class="msg-time" style="{{ $isMine ? 'text-align:right;' : '' }}">{{ $msg->created_at->format('g:ia') }}</div>
                    </div>
                </div>
                @empty
                <div style="flex:1;display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:14px;">No messages yet. Start the conversation!</div>
                @endforelse
            </div>
            <form method="POST" action="/bookings/{{ $firstBooking->id }}/messages" style="padding:16px 20px;border-top:1px solid var(--hair);display:flex;gap:10px;">
                @csrf
                <input type="text" name="body" placeholder="Type a message…" required
                       style="flex:1;height:44px;padding:0 16px;border-radius:12px;border:1.5px solid var(--hair);background:#fff;font-size:14px;font-family:inherit;outline:none;"
                       onfocus="this.style.borderColor='var(--trust-blue)'" onblur="this.style.borderColor='var(--hair)'">
                <button type="submit" class="btn btn-v" style="height:44px;padding:0 20px;">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Send
                </button>
            </form>
            @else
            <div style="flex:1;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:12px;color:var(--muted);">
                <div style="font-size:48px;">💬</div>
                <div style="font-size:16px;font-weight:600;color:var(--ink);">No messages yet</div>
                <div style="font-size:14px;">Messages will appear here once a booking is confirmed</div>
            </div>
            @endif
        </div>
    </div>
    <style>.chat-layout{grid-template-columns:1fr!important;}@media(min-width:768px){.chat-layout{grid-template-columns:300px 1fr!important;}}</style>
</div>

{{-- ═══════════════════════ EARNINGS TAB ═══════════════════════ --}}
<div x-show="tab==='earnings'" x-cloak>
    @php
    $wallet = $user->wallet;
    $transactions = $wallet?->transactions()->latest()->paginate(20) ?? collect();
    $totalEarned = $wallet?->transactions()->where('type','credit')->sum('amount') ?? 0;
    $totalWithdrawn = $wallet?->transactions()->where('type','debit')->sum('amount') ?? 0;
    $thisMonth = $wallet?->transactions()->where('type','credit')->whereMonth('created_at',now()->month)->sum('amount') ?? 0;
    $lastMonth = $wallet?->transactions()->where('type','credit')->whereMonth('created_at',now()->subMonth()->month)->sum('amount') ?? 0;
    @endphp

    {{-- Wallet balance hero --}}
    <div style="background:linear-gradient(135deg,var(--ink) 0%,#1e3a2b 100%);border-radius:20px;padding:32px;margin-bottom:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-40px;right:-40px;width:160px;height:160px;background:rgba(10,100,188,.2);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-20px;right:80px;width:80px;height:80px;background:rgba(10,100,188,.15);border-radius:50%;"></div>
        <div style="position:relative;z-index:1;">
            <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.45);margin-bottom:8px;">Available Balance</div>
            <div style="font-family:'Fraunces',serif;font-size:48px;font-weight:800;color:#fff;line-height:1;margin-bottom:4px;">KSh {{ number_format($wallet?->balance ?? 0) }}</div>
            <div style="font-size:13px;color:rgba(255,255,255,.5);">{{ $wallet?->currency ?? 'KES' }} · Updated {{ now()->format('M j, Y') }}</div>
            <div style="display:flex;flex-wrap:wrap;gap:24px;margin-top:24px;padding-top:20px;border-top:1px solid rgba(255,255,255,.1);">
                @foreach([['Total Earned','KSh '.number_format($totalEarned)],['Withdrawn','KSh '.number_format($totalWithdrawn)],['This Month','KSh '.number_format($thisMonth)],['Last Month','KSh '.number_format($lastMonth)]] as [$l,$v])
                <div>
                    <div style="font-size:11px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.06em;">{{ $l }}</div>
                    <div style="font-size:17px;font-weight:700;color:#fff;margin-top:3px;">{{ $v }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 340px;gap:16px;" class="earn-grid">
        {{-- Transaction history --}}
        <div class="card">
            <div style="padding:16px 20px;border-bottom:1px solid var(--hair);">
                <div class="sec-title">Payment History</div>
                <div style="font-size:12px;color:var(--muted);margin-top:1px;">All earnings and deductions</div>
            </div>
            <div style="overflow-x:auto;">
                <table>
                    <thead><tr>
                        <th>Description</th><th>Reference</th><th>Date</th><th>Type</th><th>Amount</th><th>Balance</th>
                    </tr></thead>
                    <tbody>
                        @forelse($transactions as $tx)
                        <tr>
                            <td style="max-width:200px;">
                                <div style="font-weight:500;font-size:13px;">{{ $tx->description }}</div>
                            </td>
                            <td style="font-size:12px;color:var(--muted);white-space:nowrap;">
                                @if($tx->reference)
                                <code style="background:#F4F6F4;padding:2px 6px;border-radius:5px;font-size:11px;">{{ Str::limit($tx->reference,16) }}</code>
                                @else —
                                @endif
                            </td>
                            <td style="font-size:12px;color:var(--muted);white-space:nowrap;">{{ $tx->created_at->format('M j, Y') }}</td>
                            <td>
                                <span class="bdg {{ $tx->type==='credit' ? 'bdg-green' : 'bdg-red' }}">
                                    {{ $tx->type==='credit' ? '↑ Credit' : '↓ Debit' }}
                                </span>
                            </td>
                            <td style="font-weight:700;color:{{ $tx->type==='credit' ? 'var(--mint)' : 'var(--coral)' }};">
                                {{ $tx->type==='credit' ? '+' : '-' }}KSh {{ number_format($tx->amount) }}
                            </td>
                            <td style="font-size:13px;color:var(--muted);">KSh {{ number_format($tx->balance_after) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--muted);">No transactions yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Request payout --}}
        <div style="display:flex;flex-direction:column;gap:14px;">
            <div class="card card-p">
                <div class="sec-title" style="margin-bottom:16px;">Request Payout</div>
                <form method="POST" action="/tutor/payouts">
                    @csrf
                    <div style="margin-bottom:12px;">
                        <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">Amount (KSh)</label>
                        <input type="number" name="amount" min="500" max="{{ $wallet?->balance ?? 0 }}"
                               placeholder="Min KSh 500"
                               style="width:100%;height:44px;padding:0 14px;border-radius:11px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;"
                               onfocus="this.style.borderColor='var(--trust-blue)'" onblur="this.style.borderColor='var(--hair)'" required>
                        <p style="font-size:11px;color:var(--muted);margin-top:4px;">Available: KSh {{ number_format($wallet?->balance ?? 0) }}</p>
                    </div>
                    <div style="margin-bottom:12px;">
                        <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">Bank Name</label>
                        <input type="text" name="bank_name" placeholder="e.g. KCB, Equity, M-Pesa"
                               style="width:100%;height:44px;padding:0 14px;border-radius:11px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;"
                               onfocus="this.style.borderColor='var(--trust-blue)'" onblur="this.style.borderColor='var(--hair)'" required>
                    </div>
                    <div style="margin-bottom:12px;">
                        <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">Account / Phone Number</label>
                        <input type="text" name="account_number" placeholder="e.g. 0712345678"
                               style="width:100%;height:44px;padding:0 14px;border-radius:11px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;"
                               onfocus="this.style.borderColor='var(--trust-blue)'" onblur="this.style.borderColor='var(--hair)'" required>
                    </div>
                    <div style="margin-bottom:16px;">
                        <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">Account Name</label>
                        <input type="text" name="account_name" placeholder="Name on account"
                               style="width:100%;height:44px;padding:0 14px;border-radius:11px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;"
                               onfocus="this.style.borderColor='var(--trust-blue)'" onblur="this.style.borderColor='var(--hair)'" required>
                    </div>
                    <button type="submit" class="btn btn-v" style="width:100%;justify-content:center;padding:12px;">Submit Payout Request</button>
                </form>
            </div>

            {{-- Recent payout requests --}}
            @php $payoutRequests = $user->payoutRequests()->latest()->take(5)->get(); @endphp
            @if($payoutRequests->isNotEmpty())
            <div class="card card-p">
                <div class="sec-title" style="margin-bottom:12px;">Recent Requests</div>
                @foreach($payoutRequests as $pr)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(21,40,31,.05);">
                    <div>
                        <div style="font-weight:600;font-size:13px;">KSh {{ number_format($pr->amount) }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $pr->created_at->format('M j, Y') }}</div>
                    </div>
                    @php $pc=['pending'=>'bdg-amber','approved'=>'bdg-trust-blue','paid'=>'bdg-green','rejected'=>'bdg-red'][$pr->status]??'bdg-gray'; @endphp
                    <span class="bdg {{ $pc }}">{{ ucfirst($pr->status) }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    <style>.earn-grid{grid-template-columns:1fr!important;}@media(min-width:1024px){.earn-grid{grid-template-columns:1fr 320px!important;}}</style>
</div>

{{-- ═══════════════════════ NOTIFICATIONS TAB ═══════════════════════ --}}
<div x-show="tab==='notifications'" x-cloak>
    @php
    $notifications = $user->notifications()->latest()->paginate(20);
    @endphp

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
            <h2 style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--ink);">Notifications</h2>
            <p style="font-size:13px;color:var(--muted);margin-top:2px;">{{ $user->unreadNotifications()->count() }} unread</p>
        </div>
        @if($user->unreadNotifications()->count() > 0)
        <form method="POST" action="/tutor/notifications/mark-all-read">
            @csrf
            <button class="btn btn-o btn-sm">Mark all read</button>
        </form>
        @endif
    </div>

    <div class="card" style="overflow:hidden;">
        @forelse($notifications as $notif)
        @php $isUnread = is_null($notif->read_at); $data = $notif->data; @endphp
        <div style="display:flex;align-items:flex-start;gap:14px;padding:16px 20px;border-bottom:1px solid rgba(21,40,31,.05);background:{{ $isUnread ? 'rgba(10,100,188,.04)' : 'transparent' }};transition:background .1s;"
             onmouseover="this.style.background='#F9FAF9'" onmouseout="this.style.background='{{ $isUnread ? 'rgba(10,100,188,.04)' : 'transparent' }}'">
            {{-- Icon based on type --}}
            @php
            $icons = ['new_booking'=>['📅','rgba(10,100,188,.1)'],'booking_confirmed'=>['✅','rgba(16,185,129,.1)'],'booking_cancelled'=>['❌','rgba(232,82,42,.1)'],'payout_processed'=>['💰','rgba(212,160,23,.1)']];
            [$notifIcon,$notifBg] = $icons[$data['type'] ?? ''] ?? ['🔔','rgba(21,40,31,.05)'];
            @endphp
            <div style="width:44px;height:44px;border-radius:12px;background:{{ $notifBg }};display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">{{ $notifIcon }}</div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                    <div>
                        <div style="font-weight:{{ $isUnread ? '700' : '500' }};font-size:14px;color:var(--ink);">{{ $data['message'] ?? 'New notification' }}</div>
                        @if(!empty($data['code']))
                        <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(10,100,188,.08);border:1px solid rgba(10,100,188,.2);border-radius:8px;padding:4px 10px;margin-top:6px;">
                            <span style="font-size:11px;color:var(--muted);">Session code:</span>
                            <code style="font-size:13px;font-weight:800;color:var(--trust-blue);">{{ $data['code'] }}</code>
                        </div>
                        @endif
                        @if(!empty($data['scheduled_at']))
                        <div style="font-size:12px;color:var(--muted);margin-top:4px;">📅 {{ \Carbon\Carbon::parse($data['scheduled_at'])->format('D, M j \a\t g:ia') }}</div>
                        @endif
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <div style="font-size:11px;color:var(--muted);white-space:nowrap;">{{ $notif->created_at->diffForHumans() }}</div>
                        @if($isUnread)<div style="width:8px;height:8px;background:var(--trust-blue);border-radius:50%;margin:4px 0 0 auto;"></div>@endif
                        @if(!empty($data['url']))
                        <a href="{{ $data['url'] }}" class="btn btn-mint btn-sm" style="margin-top:8px;">Join / View</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div style="padding:64px;text-align:center;">
            <div style="font-size:44px;margin-bottom:12px;">🔔</div>
            <div style="font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:var(--ink);margin-bottom:6px;">No notifications yet</div>
            <div style="font-size:14px;color:var(--muted);">You'll be notified here when students book sessions with you</div>
        </div>
        @endforelse
    </div>
    @if($notifications instanceof \Illuminate\Pagination\LengthAwarePaginator && $notifications->hasPages())
    <div style="margin-top:16px;">{{ $notifications->links() }}</div>
    @endif
</div>

{{-- ═══════════════════════ AVAILABILITY TAB ═══════════════════════ --}}
<div x-show="tab==='availability'" x-cloak>
    @php $avails = $profile?->availabilities()->get()->keyBy('day_of_week') ?? collect(); @endphp
    <div style="margin-bottom:20px;">
        <h2 style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--ink);margin-bottom:6px;">Weekly Availability</h2>
        <p style="font-size:14px;color:var(--muted);">Set the days and hours when students can book sessions with you.</p>
    </div>
    <form method="POST" action="/tutor/availability">
        @csrf
        <div class="card card-p" style="margin-bottom:16px;">
            @php $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']; @endphp
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($days as $i => $day)
                @php $av = $avails->get($i); $isActive = $av?->is_active ?? false; @endphp
                <div style="display:flex;align-items:center;gap:14px;padding:14px 16px;border-radius:12px;background:{{ $isActive ? 'rgba(10,100,188,.05)' : '#F9FAF9' }};border:1px solid {{ $isActive ? 'rgba(10,100,188,.2)' : 'var(--hair)' }};">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;width:120px;flex-shrink:0;">
                        <input type="checkbox" name="days[{{ $i }}][active]" value="1" {{ $isActive ? 'checked' : '' }}
                               style="width:16px;height:16px;accent-color:var(--trust-blue);cursor:pointer;"
                               onchange="let row=this.closest('div');row.style.background=this.checked?'rgba(10,100,188,.05)':'#F9FAF9';row.style.borderColor=this.checked?'rgba(10,100,188,.2)':'var(--hair)'">
                        <span style="font-weight:600;font-size:14px;color:var(--ink);">{{ $day }}</span>
                    </label>
                    <div style="display:flex;align-items:center;gap:8px;flex:1;">
                        <input type="time" name="days[{{ $i }}][start]" value="{{ $av?->start_time ?? '09:00' }}"
                               style="height:38px;padding:0 10px;border-radius:9px;border:1.5px solid var(--hair);font-size:13px;font-family:inherit;outline:none;background:#fff;">
                        <span style="font-size:13px;color:var(--muted);">to</span>
                        <input type="time" name="days[{{ $i }}][end]" value="{{ $av?->end_time ?? '18:00' }}"
                               style="height:38px;padding:0 10px;border-radius:9px;border:1.5px solid var(--hair);font-size:13px;font-family:inherit;outline:none;background:#fff;">
                    </div>
                    @if($isActive)
                    <span class="bdg bdg-mint" style="flex-shrink:0;">Available</span>
                    @else
                    <span class="bdg bdg-gray" style="flex-shrink:0;">Off</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        <div style="display:flex;justify-content:flex-end;">
            <button type="submit" class="btn btn-v" style="padding:12px 32px;font-size:15px;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Availability
            </button>
        </div>
    </form>
</div>

{{-- ═══════════════════════ PROFILE TAB ═══════════════════════ --}}
<div x-show="tab==='profile'" x-cloak>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;" class="profile-grid">
        {{-- Personal Info --}}
        <div class="card card-p">
            <div class="sec-title" style="margin-bottom:20px;">Personal Information</div>
            <form method="POST" action="/tutor/profile/update">
                @csrf @method('PATCH')
                <div style="margin-bottom:12px;">
                    <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">Full Name</label>
                    <input type="text" name="name" value="{{ $user->name }}"
                           style="width:100%;height:44px;padding:0 14px;border-radius:11px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;"
                           onfocus="this.style.borderColor='var(--trust-blue)'" onblur="this.style.borderColor='var(--hair)'">
                </div>
                <div style="margin-bottom:12px;">
                    <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">Email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           style="width:100%;height:44px;padding:0 14px;border-radius:11px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;background:#F9FAF9;color:var(--muted);">
                </div>
                <div style="margin-bottom:12px;">
                    <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">Phone</label>
                    <input type="tel" name="phone" value="{{ $user->phone }}" placeholder="+254 7XX XXX XXX"
                           style="width:100%;height:44px;padding:0 14px;border-radius:11px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;"
                           onfocus="this.style.borderColor='var(--trust-blue)'" onblur="this.style.borderColor='var(--hair)'">
                </div>
                <div style="margin-bottom:16px;">
                    <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">City</label>
                    <input type="text" name="city" value="{{ $profile?->city }}" placeholder="e.g. Nairobi"
                           style="width:100%;height:44px;padding:0 14px;border-radius:11px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;"
                           onfocus="this.style.borderColor='var(--trust-blue)'" onblur="this.style.borderColor='var(--hair)'">
                </div>
                <button type="submit" class="btn btn-v" style="width:100%;justify-content:center;">Save Changes</button>
            </form>
        </div>

        {{-- Tutor profile details --}}
        <div class="card card-p">
            <div class="sec-title" style="margin-bottom:20px;">Teaching Profile</div>
            <form method="POST" action="/tutor/profile/tutor-update">
                @csrf @method('PATCH')
                <div style="margin-bottom:12px;">
                    <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">Bio</label>
                    <textarea name="bio" rows="4" placeholder="Tell students about your experience and teaching style…"
                              style="width:100%;padding:12px 14px;border-radius:11px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;resize:vertical;line-height:1.6;"
                              onfocus="this.style.borderColor='var(--trust-blue)'" onblur="this.style.borderColor='var(--hair)'">{{ $profile?->bio }}</textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
                    <div>
                        <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">Hourly Rate (KSh)</label>
                        <input type="number" name="hourly_rate" value="{{ $profile?->hourly_rate }}" min="500"
                               style="width:100%;height:44px;padding:0 14px;border-radius:11px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;"
                               onfocus="this.style.borderColor='var(--trust-blue)'" onblur="this.style.borderColor='var(--hair)'">
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">Exp. Years</label>
                        <input type="number" name="experience_years" value="{{ $profile?->experience_years }}" min="0"
                               style="width:100%;height:44px;padding:0 14px;border-radius:11px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;"
                               onfocus="this.style.borderColor='var(--trust-blue)'" onblur="this.style.borderColor='var(--hair)'">
                    </div>
                </div>
                @if($profile)
                <div style="margin-bottom:16px;padding:12px 14px;background:#F9FAF9;border-radius:11px;border:1px solid var(--hair);">
                    <div style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">Session Pricing</div>
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;">
                        @foreach(\App\Models\TutorProfile::sessionPrices() as $key => $session)
                        <div style="text-align:center;background:#fff;border-radius:9px;border:1px solid var(--hair);padding:8px 4px;">
                            <div style="font-size:11px;font-weight:700;color:var(--ink);">{{ $session['icon'] }} {{ $session['duration'] }}min</div>
                            <div style="font-size:13px;font-weight:700;color:var(--trust-blue);margin-top:2px;">KSh {{ $session['price'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                <button type="submit" class="btn btn-v" style="width:100%;justify-content:center;">Update Profile</button>
            </form>
        </div>
    </div>
    <style>.profile-grid{grid-template-columns:1fr!important;}@media(min-width:768px){.profile-grid{grid-template-columns:1fr 1fr!important;}}</style>
</div>

    </div>{{-- end content --}}
</div>{{-- end main --}}
</div>{{-- end layout --}}

@livewireScripts
<script>
// Auto-scroll chat to bottom
const chatEl = document.getElementById('chat-messages');
if (chatEl) chatEl.scrollTop = chatEl.scrollHeight;
</script>
</body>
</html>


<script>
// SweetAlert2 Toast Configuration
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 4000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

// Auto-show flash messages
@if(session('success'))
    Toast.fire({
        icon: 'success',
        title: '{{ session('success') }}'
    });
@endif

@if(session('error'))
    Toast.fire({
        icon: 'error',
        title: '{{ session('error') }}'
    });
@endif

@if($errors->any())
    Toast.fire({
        icon: 'error',
        title: '{{ $errors->first() }}'
    });
@endif
</script>
