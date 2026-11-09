<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Tutor Dashboard - Mwalimu</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
.main{flex:1;min-height:0;display:flex;flex-direction:column;overflow:hidden;}
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
.content{flex:1;min-height:0;overflow-y:auto;padding:24px;padding-bottom:48px;}

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

/* -- Responsive overrides (mobile-first additions, desktop untouched) -- */
@media(max-width:639px){
  /* booking mini-stat grid: 2-col on small mobile */
  .booking-stats-g{grid-template-columns:repeat(2,1fr)!important;}
  /* session card row meta: stack nicely */
  .content{padding:14px;}
}
@media(max-width:479px){
  /* any 2-col quick/stat grids ? 1 col on very small screens */
  .quick-g{grid-template-columns:1fr!important;}
  .stats-g{grid-template-columns:1fr!important;}
}
</style>
</head>
<body>
@php
$user    = auth()->user();
$profile = $user->tutorProfile;
$isApproved = $profile?->is_approved ?? false;
$verificationStatus = $profile ? $profile->getVerificationStatusAttribute() : 'no_documents';
$pendingBookings = $user->bookingsAsTutor()->where('status','pending')->count();
$unreadNotifs = $user->unreadNotifications()->count();
$subjectCount = $profile ? $profile->subjects()->count() : 0;
$needsSubjectsBadge = !$isApproved && $subjectCount === 0;
$activeTab = request('tab','overview');
@endphp

<div class="layout" x-data="{ sidebarOpen: false, tab: '{{ $activeTab }}' }">

{{-- Sidebar overlay --}}
<div class="mob-overlay" :class="sidebarOpen ? 'show' : ''" @click="sidebarOpen=false"></div>

{{-- SIDEBAR --}}
<aside class="sidebar" :class="sidebarOpen ? 'open' : ''">
    <div class="sb-logo">
        <img src="/images/logo.svg" alt="Mwalimu" style="height:32px;width:auto;">
        <div>
           
            <div class="sb-logo-sub">Tutor Portal</div>
        </div>
    </div>

    <nav class="sb-nav">
        <div class="sb-section">
            <div class="sb-label">Main</div>
            <button class="sb-item" :class="tab==='overview'?'active':''" @click="tab='overview';sidebarOpen=false">
                <i class="fa-solid fa-gauge-high fa-fw" style="font-size:14px;"></i>
                Overview
            </button>
            <button class="sb-item" :class="tab==='documents'?'active':''" @click="tab='documents';sidebarOpen=false">
                <i class="fa-solid fa-file-lines fa-fw" style="font-size:14px;"></i>
                My Documents
                @if($verificationStatus === 'action_required')
                <span class="sb-badge">!</span>
                @endif
            </button>
            <a href="{{ route('tutor.subjects') }}" class="sb-item">
                <i class="fa-solid fa-book-open fa-fw" style="font-size:14px;"></i>
                My Subjects
                @if($needsSubjectsBadge)<span class="sb-badge">!</span>@endif
            </a>
        </div>

        @if($isApproved)
        <div class="sb-section">
            <div class="sb-label">Sessions</div>
            <button class="sb-item" :class="tab==='bookings'?'active':''" @click="tab='bookings';sidebarOpen=false">
                <i class="fa-solid fa-calendar-check fa-fw" style="font-size:14px;"></i>
                Bookings
                @if($pendingBookings > 0)<span class="sb-badge">{{ $pendingBookings }}</span>@endif
            </button>
            <button class="sb-item" :class="tab==='history'?'active':''" @click="tab='history';sidebarOpen=false">
                <i class="fa-solid fa-clock-rotate-left fa-fw" style="font-size:14px;"></i>
                Session History
            </button>
            <button class="sb-item" :class="tab==='chats'?'active':''" @click="tab='chats';sidebarOpen=false">
                <i class="fa-solid fa-comments fa-fw" style="font-size:14px;"></i>
                Messages
            </button>
            <button class="sb-item" :class="tab==='availability'?'active':''" @click="tab='availability';sidebarOpen=false">
                <i class="fa-solid fa-calendar-days fa-fw" style="font-size:14px;"></i>
                Availability
            </button>
        </div>

        <div class="sb-section">
            <div class="sb-label">Finance</div>
            <button class="sb-item" :class="tab==='earnings'?'active':''" @click="tab='earnings';sidebarOpen=false">
                <i class="fa-solid fa-wallet fa-fw" style="font-size:14px;"></i>
                Earnings & Wallet
            </button>
        </div>
        @endif

        <div class="sb-section">
            <div class="sb-label">Account</div>
            <button class="sb-item" :class="tab==='notifications'?'active':''" @click="tab='notifications';sidebarOpen=false">
                <i class="fa-solid fa-bell fa-fw" style="font-size:14px;"></i>
                Notifications
                @if($unreadNotifs > 0)<span class="sb-badge">{{ $unreadNotifs }}</span>@endif
            </button>
            <button class="sb-item" :class="tab==='profile'?'active':''" @click="tab='profile';sidebarOpen=false">
                <i class="fa-solid fa-circle-user fa-fw" style="font-size:14px;"></i>
                My Profile
            </button>
            <a href="{{ route('home') }}" class="sb-item">
                <i class="fa-solid fa-globe fa-fw" style="font-size:14px;"></i>
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
                    <span style="color:var(--mint);"><i class="fa-solid fa-circle-check" style="color:var(--mint);margin-right:3px;"></i>Verified Tutor</span>
                    @else
                    <span style="color:rgba(255,255,255,.35);">Pending Verification</span>
                    @endif
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sb-logout" title="Sign out">
                    <i class="fa-solid fa-right-from-bracket" style="font-size:11px;"></i>
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
                <i class="fa-solid fa-bars" style="font-size:14px;"></i>
            </button>
            <div class="page-title" x-text="{
                overview:'Dashboard Overview',documents:'My Documents',bookings:'Bookings',history:'Session History',
                chats:'Messages',availability:'Availability',earnings:'Earnings & Wallet',
                notifications:'Notifications',profile:'My Profile'
            }[tab] || 'Dashboard'"></div>
        </div>
        <div class="topbar-right">
            <button class="tb-btn" @click="tab='notifications'" title="Notifications">
                <i class="fa-solid fa-bell" style="font-size:14px;"></i>
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
            <i class="fa-solid fa-circle-check" style="font-size:14px;"></i>
            {{ session('success') }}
        </div>
        @endif

{{-- ----------------------- OVERVIEW TAB ----------------------- --}}
<div x-show="tab==='overview'" x-cloak>
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
            @if($verificationStatus==='action_required')
            <div style="font-size:22px;flex-shrink:0;"><i class="fa-solid fa-triangle-exclamation" style="color:var(--coral);"></i></div>
            @elseif($verificationStatus==='under_review')
            <div style="font-size:22px;flex-shrink:0;"><i class="fa-solid fa-clock" style="color:#D97706;"></i></div>
            @else
            <div style="font-size:22px;flex-shrink:0;"><i class="fa-solid fa-file-lines" style="color:#92400E;"></i></div>
            @endif
        </div>
        <div>
            <div style="font-weight:700;font-size:15px;color:var(--ink);">
                {{ $verificationStatus==='action_required' ? 'Action Required - Document Rejected' : ($verificationStatus==='under_review' ? 'Profile Under Review' : 'Upload All Required Documents') }}
            </div>
            <div style="font-size:13px;color:var(--muted);margin-top:3px;line-height:1.6;">
                @if($verificationStatus==='action_required')
                    One or more documents were rejected. Please re-upload the required documents to continue.
                    <button @click="tab='documents'" style="background:none;border:none;color:var(--coral);font-weight:700;cursor:pointer;font-size:13px;font-family:inherit;padding:0;margin-left:6px;">View Documents ?</button>
                @elseif($verificationStatus==='under_review')
                    Our team is reviewing your profile and documents. You'll receive an email once approved - usually within 24-48 hours.
                @else
                    You need to upload <strong>all 4 required documents</strong> before we can review your profile.
                    @if($missingCount > 0)
                        <span style="color:var(--coral);font-weight:700;">{{ $missingCount }} remaining: {{ implode(', ', $missingNames) }}</span>
                    @endif
                    <button @click="tab='documents'" style="background:none;border:none;color:#92400E;font-weight:700;cursor:pointer;font-size:13px;font-family:inherit;padding:0;margin-left:6px;">Upload Now ?</button>
                @endif
            </div>
        </div>
    </div>

    {{-- REQUIREMENTS CHECKLIST - Show when unverified --}}
    @php
        $docsUploadedCount = $docs->count();
        $docsComplete = $docsUploadedCount >= 4;
        $subjectsCount = $profile ? $profile->subjects()->count() : 0;
        $subjectsComplete = $subjectsCount >= 1;
        $availabilityCount = $profile ? $profile->availabilities()->count() : 0;
        $availabilityComplete = $availabilityCount > 0;
    @endphp
    <div style="background:#fff;border:1.5px solid #e5e7eb;border-radius:12px;padding:20px;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <i class="fa-solid fa-checklist" style="font-size:18px;color:#0A64BC;"></i>
            <div style="font-weight:700;font-size:15px;color:var(--ink);">Requirements before review</div>
        </div>
        <div style="display:flex;flex-direction:column;gap:12px;">
            {{-- Documents Requirement --}}
            <div style="display:flex;align-items:center;gap:12px;padding:10px;border-radius:8px;background:{{ $docsComplete ? '#f0fdf4' : '#fef2f2' }};">
                <div style="width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:{{ $docsComplete ? '#10B981' : '#ef4444' }};color:#fff;font-size:14px;flex-shrink:0;">
                    <i class="fa-solid {{ $docsComplete ? 'fa-check' : 'fa-xmark' }}"></i>
                </div>
                <div style="flex:1;">
                    <div style="font-weight:600;font-size:13px;color:var(--ink);">4 required documents uploaded</div>
                    <div style="font-size:12px;color:var(--muted);">{{ $docsUploadedCount }}/4 documents</div>
                </div>
            </div>

            {{-- Subjects Requirement --}}
            <div style="display:flex;align-items:center;gap:12px;padding:10px;border-radius:8px;background:{{ $subjectsComplete ? '#f0fdf4' : '#fef2f2' }};">
                <div style="width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:{{ $subjectsComplete ? '#10B981' : '#ef4444' }};color:#fff;font-size:14px;flex-shrink:0;">
                    <i class="fa-solid {{ $subjectsComplete ? 'fa-check' : 'fa-xmark' }}"></i>
                </div>
                <div style="flex:1;">
                    <div style="font-weight:600;font-size:13px;color:var(--ink);">Teaching subjects selected</div>
                    <div style="font-size:12px;color:var(--muted);">{{ $subjectsCount }} subject(s) added</div>
                </div>
            </div>

            {{-- Availability Requirement --}}
            <div style="display:flex;align-items:center;gap:12px;padding:10px;border-radius:8px;background:{{ $availabilityComplete ? '#f0fdf4' : '#fef2f2' }};">
                <div style="width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:{{ $availabilityComplete ? '#10B981' : '#ef4444' }};color:#fff;font-size:14px;flex-shrink:0;">
                    <i class="fa-solid {{ $availabilityComplete ? 'fa-check' : 'fa-xmark' }}"></i>
                </div>
                <div style="flex:1;">
                    <div style="font-weight:600;font-size:13px;color:var(--ink);">Teaching availability set</div>
                    <div style="font-size:12px;color:var(--muted);">{{ $availabilityCount }} day(s) available</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ONBOARDING CARDS - Show when unverified --}}
    @if(!$isApproved)
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:32px;margin-top:28px;" class="onboard-g">
        {{-- Card 1: Upload Documents --}}
        <button @click="tab='documents'" style="background:linear-gradient(135deg,#FEF9C3 0%,#FFFBEB 100%);border:2px solid #FCD34D;border-radius:18px;padding:24px;text-align:left;cursor:pointer;transition:all .3s;display:flex;flex-direction:column;gap:16px;height:100%;font-family:inherit;align-items:flex-start;box-shadow:0 2px 8px rgba(252,211,77,.1);"
        onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(252,211,77,.25)';this.style.borderColor='#FBD34E'"
        onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 8px rgba(252,211,77,.1)';this.style.borderColor='#FCD34D'">
            <div style="width:56px;height:56px;border-radius:14px;background:#FDE68A;display:flex;align-items:center;justify-content:center;font-size:24px;box-shadow:0 2px 8px rgba(252,211,77,.2);">
                <i class="fa-solid fa-file-upload"></i>
            </div>
            <div>
                <div style="font-weight:700;font-size:16px;color:var(--ink);margin-bottom:6px;line-height:1.3;">Upload Documents</div>
                <div style="font-size:13px;color:var(--muted);line-height:1.5;">Submit your National ID, TSC Certificate, and other required documents for verification.</div>
            </div>
            <div style="margin-top:auto;width:100%;">
                <span style="display:inline-flex;align-items:center;gap:6px;background:#FCD34D;color:#78350F;padding:9px 16px;border-radius:9px;font-size:12px;font-weight:700;transition:all .2s;">
                    Get Started <i class="fa-solid fa-arrow-right"></i>
                </span>
            </div>
        </button>

        {{-- Card 2: Add Subjects --}}
        <a href="{{ route('tutor.subjects') }}" style="background:linear-gradient(135deg,#F0FDF4 0%,#F7FEF5 100%);border:2px solid #86EFAC;border-radius:18px;padding:24px;text-align:left;cursor:pointer;transition:all .3s;display:flex;flex-direction:column;gap:16px;height:100%;font-family:inherit;align-items:flex-start;text-decoration:none;color:inherit;box-shadow:0 2px 8px rgba(134,239,172,.1);"
        onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(134,239,172,.25)';this.style.borderColor='#87F45E'"
        onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 8px rgba(134,239,172,.1)';this.style.borderColor='#86EFAC'">
            <div style="width:56px;height:56px;border-radius:14px;background:#BBEF7F;display:flex;align-items:center;justify-content:center;font-size:24px;box-shadow:0 2px 8px rgba(134,239,172,.2);">
                <i class="fa-solid fa-book"></i>
            </div>
            <div>
                <div style="font-weight:700;font-size:16px;color:var(--ink);margin-bottom:6px;line-height:1.3;">Add Subjects to Teach</div>
                <div style="font-size:13px;color:var(--muted);line-height:1.5;">Select the subjects and topics you're qualified to teach. This helps students find you.</div>
            </div>
            <div style="margin-top:auto;width:100%;">
                <span style="display:inline-flex;align-items:center;gap:6px;background:#BBEF7F;color:#166534;padding:9px 16px;border-radius:9px;font-size:12px;font-weight:700;transition:all .2s;">
                    Get Started <i class="fa-solid fa-arrow-right"></i>
                </span>
            </div>
        </a>

        {{-- Card 3: Set Availability --}}
        <button @click="tab='availability'" style="background:linear-gradient(135deg,#DBEAFE 0%,#F0F9FF 100%);border:2px solid #93C5FD;border-radius:18px;padding:24px;text-align:left;cursor:pointer;transition:all .3s;display:flex;flex-direction:column;gap:16px;height:100%;font-family:inherit;align-items:flex-start;box-shadow:0 2px 8px rgba(147,197,253,.1);"
        onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(147,197,253,.25)';this.style.borderColor='#94D5FE'"
        onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 8px rgba(147,197,253,.1)';this.style.borderColor='#93C5FD'">
            <div style="width:56px;height:56px;border-radius:14px;background:#BFDBFE;display:flex;align-items:center;justify-content:center;font-size:24px;box-shadow:0 2px 8px rgba(147,197,253,.2);">
                <i class="fa-solid fa-calendar"></i>
            </div>
            <div>
                <div style="font-weight:700;font-size:16px;color:var(--ink);margin-bottom:6px;line-height:1.3;">Set Your Availability</div>
                <div style="font-size:13px;color:var(--muted);line-height:1.5;">Add your teaching schedule so students can book sessions at times that work for you.</div>
            </div>
            <div style="margin-top:auto;width:100%;">
                <span style="display:inline-flex;align-items:center;gap:6px;background:#93C5FD;color:#1E40AF;padding:9px 16px;border-radius:9px;font-size:12px;font-weight:700;transition:all .2s;">
                    Get Started <i class="fa-solid fa-arrow-right"></i>
                </span>
            </div>
        </button>
    </div>
    <style>
        @media(max-width:768px){
            .onboard-g{grid-template-columns:1fr!important;}
        }
    </style>
    @else
    <div class="status-banner status-approved" style="margin-bottom:20px;">
        <div style="font-size:24px;flex-shrink:0;"><i class="fa-solid fa-circle-check" style="color:#10B981;"></i></div>
        <div>
            <div style="font-weight:700;font-size:15px;color:#166534;">Your profile is live!</div>
            <div style="font-size:13px;color:#4ADE80;margin-top:2px;">Students can find and book sessions with you. {{ $pendingBookings > 0 ? "You have $pendingBookings pending booking(s)." : '' }}</div>
        </div>
    </div>
    @endif

    {{-- Stats - Show only for approved tutors --}}
    @if($isApproved)
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:20px;" class="stats-g">
        <div class="stat">
            <div class="stat-icon" style="background:rgba(10,100,188,.1);">
                <i class="fa-solid fa-wallet" style="color:#0A64BC;font-size:18px;"></i>
            </div>
            <div class="stat-lbl">Wallet Balance</div>
            <div class="stat-val">KSh {{ number_format($wallet?->balance ?? 0) }}</div>
            <div class="stat-sub">This month: KSh {{ number_format($thisMonth) }}</div>
        </div>
        
        <div class="stat">
            <div class="stat-icon" style="background:rgba(16,185,129,.1);">
                <i class="fa-solid fa-calendar-check" style="color:#10B981;font-size:18px;"></i>
            </div>
            <div class="stat-lbl">Upcoming Sessions</div>
            <div class="stat-val">{{ $upcoming->count() }}</div>
            <div class="stat-sub">{{ $pendingBookings }} pending confirmation</div>
        </div>
        <div class="stat">
            <div class="stat-icon" style="background:rgba(232,82,42,.1);">
                <i class="fa-solid fa-circle-check" style="color:#E8522A;font-size:18px;"></i>
            </div>
            <div class="stat-lbl">Completed Sessions</div>
            <div class="stat-val">{{ $completedCount }}</div>
            <div class="stat-sub">Rating:  {{ number_format($profile?->rating_avg ?? 0,1) }} ({{ $profile?->total_reviews ?? 0 }} reviews)</div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;" class="quick-g">
        @foreach([
            ['documents','fa-solid fa-file-lines','Documents','Check your verification status'],
            ['bookings','fa-solid fa-calendar-check','Bookings','View all session bookings'],
            ['earnings','fa-solid fa-wallet','Earnings','Check wallet & history'],
            ['availability','fa-solid fa-clock','Availability','Set your schedule'],
        ] as [$t,$ic,$label,$desc])
        <button @click="tab='{{ $t }}'" style="background:#fff;border:1px solid var(--hair);border-radius:14px;padding:18px;text-align:left;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:14px;" onmouseover="this.style.borderColor='rgba(10,100,188,.4)';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='var(--hair)';this.style.transform='translateY(0)'">
            <div style="font-size:26px;width:48px;height:48px;background:rgba(10,100,188,.08);border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="{{ $ic }}"></i></div>
            <div>
                <div style="font-weight:700;font-size:14px;color:var(--ink);">{{ $label }}</div>
                <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $desc }}</div>
            </div>
        </button>
        @endforeach
    </div>
        
    </div>
    <style>.stats-g{grid-template-columns:1fr!important;}@media(min-width:640px){.stats-g{grid-template-columns:repeat(2,1fr)!important;}}@media(min-width:1024px){.stats-g{grid-template-columns:repeat(3,1fr)!important;}}</style>

    {{-- Earnings Widget --}}
    @if(isset($earnings))
        @include('components.earnings-widget', ['earnings' => $earnings])
    @endif
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
                    <div style="font-size:12px;color:var(--muted);">{{ $b->subject?->name ?? 'General' }} - {{ $b->duration_minutes }}min</div>
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
    <!-- <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;" class="quick-g">
        @foreach([
            ['documents','fa-solid fa-file-lines','Documents','Check your verification status'],
            ['bookings','fa-solid fa-calendar-check','Bookings','View all session bookings'],
            ['earnings','fa-solid fa-wallet','Earnings','Check wallet & history'],
            ['availability','fa-solid fa-clock','Availability','Set your schedule'],
        ] as [$t,$ic,$label,$desc])
        <button @click="tab='{{ $t }}'" style="background:#fff;border:1px solid var(--hair);border-radius:14px;padding:18px;text-align:left;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:14px;" onmouseover="this.style.borderColor='rgba(10,100,188,.4)';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='var(--hair)';this.style.transform='translateY(0)'">
            <div style="font-size:26px;width:48px;height:48px;background:rgba(10,100,188,.08);border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="{{ $ic }}"></i></div>
            <div>
                <div style="font-weight:700;font-size:14px;color:var(--ink);">{{ $label }}</div>
                <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $desc }}</div>
            </div>
        </button>
        @endforeach
    </div> -->
    <style>.quick-g{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:768px){.quick-g{grid-template-columns:repeat(4,1fr)!important;}}</style>
</div>

{{-- ----------------------- DOCUMENTS TAB ----------------------- --}}
@include('pages.dashboard.partials.documents')

{{-- ----------------------- BOOKINGS TAB ----------------------- --}}
<div x-show="tab==='bookings'" x-cloak>
    @php
    $bFilter = request('bstatus','all');
    $allBookings = $user->bookingsAsTutor()->with(['student','subject'])->latest('scheduled_at')->get();
    $upcomingB = $allBookings->whereIn('status',['pending','confirmed'])->where('scheduled_at','>',now());
    $completedB = $allBookings->where('status','completed');
    $cancelledB = $allBookings->where('status','cancelled');
    @endphp

    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;" class="booking-stats-g">
        @foreach([['All',$allBookings->count(),'bdg-gray'],['Upcoming',$upcomingB->count(),'bdg-blue'],['Completed',$completedB->count(),'bdg-green'],['Cancelled',$cancelledB->count(),'bdg-amber']] as [$l,$c,$b])
        <div class="stat" style="padding:14px 16px;text-align:center;">
            <div style="font-family:'Fraunces',serif;font-size:26px;font-weight:700;color:var(--ink);">{{ $c }}</div>
            <div style="font-size:12px;color:var(--muted);">{{ $l }}</div>
        </div>
        @endforeach
    </div>
    <style>.booking-stats-g{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:640px){.booking-stats-g{grid-template-columns:repeat(4,1fr)!important;}}</style>

    {{-- Upcoming --}}
    @if($upcomingB->isNotEmpty())
    <div class="card" style="margin-bottom:20px;">
        <div style="padding:16px 20px;border-bottom:1px solid var(--hair);">
            <div class="sec-title">Upcoming Sessions</div>
        </div>
        @foreach($upcomingB->sortBy('scheduled_at') as $b)
        <div style="padding:14px 20px;border-bottom:1px solid rgba(21,40,31,.05);display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <div class="av" style="width:44px;height:44px;background:#0A64BC;border-radius:12px;font-size:16px;">{{ substr($b->student->name,0,1) }}</div>
            <div style="flex:1;min-width:160px;">
                <div style="font-weight:600;font-size:14px;">{{ $b->student->name }}</div>
                <div style="font-size:12px;color:var(--muted);">{{ $b->subject?->name ?? 'General' }} - {{ $b->duration_minutes }}min - KSh {{ number_format($b->tutor_earning) }}</div>
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
            <a href="{{ route('bookings.show',$b->uuid) }}" class="btn btn-mint btn-sm"><i class="fa-solid fa-arrow-right-to-bracket fa-fw"></i> {{$b->canJoin() ? 'Join Session' : 'Open Session'}}</a>
            @else
            <a href="{{ route('bookings.show',$b->uuid) }}" class="btn btn-o btn-sm"><i class="fa-solid fa-eye fa-fw"></i> View</a>
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
        <div style="overflow-x:auto;" class="tbl-wrap">
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
                            <span style="color:var(--muted);font-size:12px;">-</span>
                            @endif
                        </td>
                        <td>
                            @php $sc=['pending'=>'bdg-amber','confirmed'=>'bdg-blue','completed'=>'bdg-green','cancelled'=>'bdg-gray','no_show'=>'bdg-red'][$b->status]??'bdg-gray'; @endphp
                            <span class="bdg {{ $sc }}">{{ ucfirst($b->status) }}</span>
                        </td>
                        <td>
                            @if($b->status === 'confirmed')
                            <a href="{{ route('bookings.show',$b->uuid) }}" class="btn btn-mint btn-sm"><i class="fa-solid fa-arrow-right-to-bracket fa-fw"></i> {{ $b->canJoin() ? 'Join' : 'Open' }}</a>
                            @else
                            <a href="{{ route('bookings.show',$b->uuid) }}" class="btn btn-o btn-sm"><i class="fa-solid fa-eye fa-fw"></i> View</a>
                            @endif
                        </td>
                    </tr>
                    @empty
<tr>
    <td colspan="8">
        <div style="padding:48px 20px;text-align:center;">
            <i class="fa-solid fa-calendar-xmark" style="font-size:40px;color:rgba(21,40,31,.15);margin-bottom:12px;display:block;"></i>
            <div style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);margin-bottom:6px;">No sessions yet</div>
            <div style="font-size:13px;color:var(--muted);">Your bookings will appear here once students schedule sessions with you</div>
        </div>
    </td>
</tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ----------------------- SESSION HISTORY TAB ----------------------- --}}
<div x-show="tab==='history'" x-cloak>
    @php
    $allBookings = $user->bookingsAsTutor()->with(['student','subject'])->latest('scheduled_at')->get();
    $completedB = $allBookings->where('status','completed');
    $cancelledB = $allBookings->where('status','cancelled');
    $historyBookings = $completedB->concat($cancelledB);
    @endphp

    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:20px;" class="history-stats-g">
        <div class="stat" style="padding:14px 16px;text-align:center;">
            <div style="font-family:'Fraunces',serif;font-size:26px;font-weight:700;color:var(--ink);">{{ $completedB->count() }}</div>
            <div style="font-size:12px;color:var(--muted);">Completed</div>
        </div>
        <div class="stat" style="padding:14px 16px;text-align:center;">
            <div style="font-family:'Fraunces',serif;font-size:26px;font-weight:700;color:var(--ink);">{{ $cancelledB->count() }}</div>
            <div style="font-size:12px;color:var(--muted);">Cancelled</div>
        </div>
    </div>

    {{-- Session history table --}}
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid var(--hair);">
            <div class="sec-title">Session History</div>
            <div class="sec-sub">Your completed and cancelled sessions</div>
        </div>
        <div style="overflow-x:auto;" class="tbl-wrap">
            <table>
                <thead><tr>
                    <th>Student</th><th>Subject</th><th>Date & Time</th><th>Duration</th><th>Earnings</th><th>Code</th><th>Status</th><th></th>
                </tr></thead>
                <tbody>
                    @forelse($historyBookings as $b)
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
                            <span style="color:var(--muted);font-size:12px;"></span>
                            @endif
                        </td>
                        <td>
                            @php $sc=['completed'=>'bdg-green','cancelled'=>'bdg-gray','no_show'=>'bdg-red'][$b->status]??'bdg-gray'; @endphp
                            <span class="bdg {{ $sc }}">{{ ucfirst($b->status) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('bookings.show',$b->uuid) }}" class="btn btn-o btn-sm"><i class="fa-solid fa-eye fa-fw"></i> View</a>
                        </td>
                    </tr>
                    @empty
<tr>
    <td colspan="8">
        <div style="padding:48px 20px;text-align:center;">
            <i class="fa-solid fa-calendar-xmark" style="font-size:40px;color:rgba(21,40,31,.15);margin-bottom:12px;display:block;"></i>
            <div style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);margin-bottom:6px;">No session history yet</div>
            <div style="font-size:13px;color:var(--muted);">Your completed and cancelled sessions will appear here</div>
        </div>
    </td>
</tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ----------------------- CHATS TAB ----------------------- --}}
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
                    <div style="font-size:32px;margin-bottom:8px;"></div>
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
                    <div style="font-size:12px;color:var(--muted);">{{ $firstBooking->subject?->name ?? 'General' }} - {{ $firstBooking->scheduled_at->format('M j, g:ia') }}</div>
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
                <input type="text" name="body" placeholder="Type a message-" required
                       style="flex:1;height:44px;padding:0 16px;border-radius:12px;border:1.5px solid var(--hair);background:#fff;font-size:14px;font-family:inherit;outline:none;"
                       onfocus="this.style.borderColor='var(--trust-blue)'" onblur="this.style.borderColor='var(--hair)'">
                <button type="submit" class="btn btn-v" style="height:44px;padding:0 20px;">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Send
                </button>
            </form>
            @else
            <div style="flex:1;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:12px;color:var(--muted);">
                <div style="font-size:48px;"></div>
                <div style="font-size:16px;font-weight:600;color:var(--ink);">No messages yet</div>
                <div style="font-size:14px;">Messages will appear here once a booking is confirmed</div>
            </div>
            @endif
        </div>
    </div>
    <style>.chat-layout{grid-template-columns:1fr!important;}@media(min-width:768px){.chat-layout{grid-template-columns:300px 1fr!important;}}</style>
</div>

{{-- ----------------------- EARNINGS TAB ----------------------- --}}
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
            <div style="font-size:13px;color:rgba(255,255,255,.5);">{{ $wallet?->currency ?? 'KES' }} - Updated {{ now()->format('M j, Y') }}</div>
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
            <div style="overflow-x:auto;" class="tbl-wrap">
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
                                @else -
                                @endif
                            </td>
                            <td style="font-size:12px;color:var(--muted);white-space:nowrap;">{{ $tx->created_at->format('M j, Y') }}</td>
                            <td>
                                <span class="bdg {{ $tx->type==='credit' ? 'bdg-green' : 'bdg-red' }}">
                                    {{ $tx->type==='credit' ? '? Credit' : '? Debit' }}
                                </span>
                            </td>
                            <td style="font-weight:700;color:{{ $tx->type==='credit' ? 'var(--mint)' : 'var(--coral)' }};">
                                {{ $tx->type==='credit' ? '+' : '-' }}KSh {{ number_format($tx->amount) }}
                            </td>
                            <td style="font-size:13px;color:var(--muted);">KSh {{ number_format($tx->balance_after) }}</td>
                        </tr>
                        @empty
<tr>
    <td colspan="6">
        <div style="padding:48px 20px;text-align:center;">
            <i class="fa-solid fa-receipt" style="font-size:40px;color:rgba(21,40,31,.15);margin-bottom:12px;display:block;"></i>
            <div style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);margin-bottom:6px;">No transactions yet</div>
            <div style="font-size:13px;color:var(--muted);">Your earnings will appear here once students complete payments</div>
        </div>
    </td>
</tr>
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

{{-- ----------------------- NOTIFICATIONS TAB ----------------------- --}}
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
            $icons = ['new_booking'=>['fa-calendar','rgba(10,100,188,.1)','#0A64BC'],'booking_confirmed'=>['fa-check-circle','rgba(16,185,129,.1)','#10B981'],'booking_cancelled'=>['fa-times-circle','rgba(232,82,42,.1)','#E8522A'],'payout_processed'=>['fa-money-bill-wave','rgba(212,160,23,.1)','#D4A017']];
            [$notifIcon,$notifBg,$notifColor] = $icons[$data['type'] ?? ''] ?? ['fa-bell','rgba(21,40,31,.05)','#15281F'];
            @endphp
            <div style="width:44px;height:44px;border-radius:12px;background:{{ $notifBg }};display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;"><i class="fas {{ $notifIcon }}" style="color:{{ $notifColor }};"></i></div>
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
                        <div style="font-size:12px;color:var(--muted);margin-top:4px;"><i class="fas fa-calendar" style="margin-right:4px;"></i>{{ \Carbon\Carbon::parse($data['scheduled_at'])->format('D, M j \a\t g:ia') }}</div>
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
            <div style="font-size:44px;margin-bottom:12px;"><i class="fa-solid fa-bell-slash"></i></div>
            <div style="font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:var(--ink);margin-bottom:6px;">No notifications yet</div>
            <div style="font-size:14px;color:var(--muted);">You'll be notified here when students book sessions with you</div>
        </div>
        @endforelse
    </div>
    @if($notifications instanceof \Illuminate\Pagination\LengthAwarePaginator && $notifications->hasPages())
    <div style="margin-top:16px;">{{ $notifications->links() }}</div>
    @endif
</div>

{{-- ----------------------- AVAILABILITY TAB ----------------------- --}}
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
                <i class="fa-solid fa-floppy-disk fa-fw"></i>
                Save Availability
            </button>
        </div>
    </form>
</div>

{{-- ----------------------- PROFILE TAB ----------------------- --}}
<div x-show="tab==='profile'" x-cloak>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;" class="profile-grid">
        {{-- Personal Info --}}
        <div class="card card-p">
            <div class="sec-title" style="margin-bottom:20px;">Personal Information</div>
            <form method="POST" action="/tutor/profile/update">
                @csrf @method('PATCH')
                <input type="hidden" name="_redirect" value="{{ route('tutor.dashboard') }}?tab=profile">
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

        {{-- Passport photo update --}}
        <div class="card card-p">
            <div class="sec-title" style="margin-bottom:6px;">Profile Photo</div>
            <p style="font-size:13px;color:var(--muted);margin-bottom:18px;">Update your passport photo. This is shown on your public tutor profile.</p>
            <form method="POST" action="{{ route('tutor.documents.upload') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="passport_photo">
                <input type="hidden" name="_redirect" value="{{ route('tutor.dashboard') }}?tab=profile">
                {{-- Current photo --}}
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:18px;">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                         style="width:72px;height:72px;border-radius:14px;object-fit:cover;border:2px solid var(--hair);">
                    <div>
                        <div style="font-size:14px;font-weight:600;color:var(--ink);">{{ $user->name }}</div>
                        <div style="font-size:12px;color:var(--muted);margin-top:2px;">JPG, PNG or PDF - Max 5 MB - Must be a clear headshot</div>
                    </div>
                </div>
                <label style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:10px;background:var(--trust-blue);color:#fff;font-size:13px;font-weight:600;cursor:pointer;transition:opacity .15s;"
                       onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fa-solid fa-upload fa-sm"></i> Upload New Photo
                    <input type="file" name="document" accept=".jpg,.jpeg,.png,.pdf" style="display:none;" onchange="this.closest('form').submit()">
                </label>
            </form>
        </div>

        {{-- Bio & Experience --}}
        <div class="card card-p">
            <div class="sec-title" style="margin-bottom:20px;">Teaching Profile</div>
            <form method="POST" action="/tutor/profile/tutor-update">
                @csrf @method('PATCH')
                <input type="hidden" name="_redirect" value="{{ route('tutor.dashboard') }}?tab=profile">
                <div style="margin-bottom:12px;">
                    <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">Bio</label>
                    <textarea name="bio" rows="4" maxlength="255" placeholder="Tell students about your experience and teaching style-"
                              x-data="{ count: {{ strlen($profile?->bio ?? '') }} }"
                              x-on:input="count = $el.value.length"
                              style="width:100%;padding:12px 14px;border-radius:11px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;resize:none;line-height:1.6;"
                              onfocus="this.style.borderColor='var(--trust-blue)'" onblur="this.style.borderColor='var(--hair)'">{{ $profile?->bio }}</textarea>
                    <p style="font-size:11px;color:var(--muted);margin-top:4px;text-align:right;">
                        <span x-text="count">{{ strlen($profile?->bio ?? '') }}</span> / 255
                    </p>
                </div>
                <button type="submit" class="btn btn-v" style="width:100%;justify-content:center;">
                    <i class="fa-solid fa-floppy-disk fa-sm"></i> Save Teaching Profile
                </button>
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

// Document viewer popup (uses SweetAlert2)
window.viewDocPopup = function(url, isPdf, docName) {
    const content = isPdf
        ? `<iframe src="${url}" style="width:100%;height:68vh;border:none;border-radius:8px;"></iframe>`
        : `<img src="${url}" style="max-width:100%;max-height:68vh;border-radius:8px;object-fit:contain;" onerror="this.outerHTML='<p style=\'color:#E8522A;padding:20px\'>Could not load image.</p>'">`;
    Swal.fire({
        title: docName || 'Document',
        html: content,
        width: '820px',
        showConfirmButton: false,
        showCloseButton: true,
        padding: '20px',
    });
};
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

