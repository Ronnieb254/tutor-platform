<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') – Mwalimu Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @livewireStyles
    <style>
        :root {
            --ink:#15281F; --cream:#FBF5E6; --violet:#0A64BC; --coral:#E8522A;
            --gold:#EEBE15; --mint:#10B981; --muted:rgba(21,40,31,0.5);
            --hairline:rgba(21,40,31,0.1); --sidebar:#111C16; --sidebar-w:260px;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        html,body{height:100%;font-family:'DM Sans',sans-serif;color:var(--ink);background:#F4F6F4;}
        a{color:inherit;text-decoration:none;}
        [x-cloak]{display:none!important;}
        button{font-family:inherit;cursor:pointer;}

        /* Layout */
        .admin-wrap{display:flex;height:100vh;overflow:hidden;}

        /* Sidebar */
        .sidebar{width:var(--sidebar-w);background:var(--sidebar);display:flex;flex-direction:column;flex-shrink:0;transition:width .25s;}
        .sidebar-logo{padding:24px 20px;border-bottom:1px solid rgba(255,255,255,0.06);display:flex;align-items:center;gap:10px;}
        .sidebar-logo span{font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:#fff;}
        .sidebar-logo small{display:block;font-size:10px;color:rgba(255,255,255,0.35);font-weight:400;margin-top:1px;}
        .sidebar-nav{flex:1;padding:12px 8px;overflow-y:auto;}
        .nav-section{margin-bottom:20px;}
        .nav-section-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,0.3);padding:0 12px;margin-bottom:6px;}
        .nav-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;font-size:13.5px;font-weight:500;color:rgba(255,255,255,0.6);transition:all .15s;margin-bottom:2px;}
        .nav-item:hover{background:rgba(255,255,255,0.07);color:#fff;}
        .nav-item.active{background:var(--violet);color:#fff;}
        .nav-item svg{flex-shrink:0;opacity:.7;}
        .nav-item.active svg{opacity:1;}
        .nav-badge{margin-left:auto;background:var(--coral);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:999px;}
        .sidebar-footer{padding:16px;border-top:1px solid rgba(255,255,255,0.06);}
        .sidebar-user{display:flex;align-items:center;gap:10px;}
        .sidebar-user img{width:34px;height:34px;border-radius:10px;object-fit:cover;flex-shrink:0;}
        .sidebar-user-info{flex:1;min-width:0;}
        .sidebar-user-name{font-size:13px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .sidebar-user-role{font-size:11px;color:rgba(255,255,255,0.35);}
        .sidebar-user-btn{width:28px;height:28px;border-radius:8px;border:none;background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.5);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .sidebar-user-btn:hover{background:rgba(255,255,255,0.14);color:#fff;}

        /* Main */
        .admin-main{flex:1;display:flex;flex-direction:column;overflow:hidden;}

        /* Topbar */
        .topbar{height:64px;background:#fff;border-bottom:1px solid var(--hairline);display:flex;align-items:center;justify-content:space-between;padding:0 28px;flex-shrink:0;}
        .topbar-left{display:flex;align-items:center;gap:16px;}
        .topbar-title{font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--ink);}
        .topbar-breadcrumb{font-size:13px;color:var(--muted);}
        .topbar-right{display:flex;align-items:center;gap:12px;}
        .topbar-btn{width:36px;height:36px;border-radius:10px;border:1px solid var(--hairline);background:#fff;display:flex;align-items:center;justify-content:center;color:var(--muted);transition:all .15s;position:relative;}
        .topbar-btn:hover{border-color:rgba(10,100,188,0.4);color:var(--violet);}
        .topbar-notif-badge{position:absolute;top:-3px;right:-3px;width:14px;height:14px;background:var(--coral);border-radius:999px;font-size:8px;font-weight:700;color:#fff;display:flex;align-items:center;justify-content:center;border:2px solid #fff;}

        /* Page content */
        .admin-content{flex:1;overflow-y:auto;padding:28px;}

        /* Cards */
        .card{background:#fff;border-radius:16px;border:1px solid var(--hairline);}
        .card-p{padding:24px;}

        /* Stat cards */
        .stats-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:24px;}
        @media(min-width:1200px){.stats-grid{grid-template-columns:repeat(4,1fr);}}
        .stat-card{background:#fff;border-radius:16px;border:1px solid var(--hairline);padding:20px;position:relative;overflow:hidden;}
        .stat-card::after{content:'';position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;opacity:.06;}
        .stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;}
        .stat-label{font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;}
        .stat-value{font-family:'Fraunces',serif;font-size:32px;font-weight:700;color:var(--ink);line-height:1;}
        .stat-trend{font-size:12px;margin-top:6px;display:flex;align-items:center;gap:4px;}
        .trend-up{color:var(--mint);}
        .trend-down{color:var(--coral);}

        /* Tables */
        .table-wrap{overflow-x:auto;}
        table{width:100%;border-collapse:collapse;}
        thead th{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);padding:10px 16px;text-align:left;border-bottom:1px solid var(--hairline);white-space:nowrap;}
        tbody tr{border-bottom:1px solid rgba(21,40,31,0.05);transition:background .1s;}
        tbody tr:hover{background:#FAFCFA;}
        tbody tr:last-child{border-bottom:none;}
        td{padding:13px 16px;font-size:13.5px;color:var(--ink);vertical-align:middle;}

        /* Badges */
        .badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;}
        .badge-green{background:#DCFCE7;color:#166534;}
        .badge-amber{background:#FEF9C3;color:#854D0E;}
        .badge-red{background:#FEE2E2;color:#991B1B;}
        .badge-blue{background:#DBEAFE;color:#1E40AF;}
        .badge-violet{background:rgba(10,100,188,0.1);color:var(--violet);}
        .badge-gray{background:#F3F4F6;color:#6B7280;}

        /* Buttons */
        .btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:all .15s;font-family:inherit;}
        .btn-primary{background:var(--violet);color:#fff;}
        .btn-primary:hover{background:#120A8F;}
        .btn-sm{padding:6px 12px;font-size:12px;border-radius:8px;}
        .btn-outline{background:#fff;color:var(--ink);border:1.5px solid var(--hairline);}
        .btn-outline:hover{border-color:var(--violet);color:var(--violet);}
        .btn-danger{background:#FEE2E2;color:#991B1B;}
        .btn-danger:hover{background:#FECACA;}
        .btn-success{background:#DCFCE7;color:#166534;}
        .btn-success:hover{background:#BBF7D0;}

        /* Form elements */
        .form-input{width:100%;height:40px;padding:0 12px;border-radius:10px;border:1.5px solid rgba(21,40,31,0.15);background:#fff;font-size:13.5px;font-family:inherit;color:var(--ink);outline:none;transition:border-color .15s;}
        .form-input:focus{border-color:var(--violet);box-shadow:0 0 0 3px rgba(10,100,188,0.1);}
        .form-select{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%2315281F' stroke-width='2'%3E%3Cpath d='M2 4l4 4 4-4'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;}
        .form-label{font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;}

        /* Page header */
        .page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;gap:16px;flex-wrap:wrap;}
        .page-header h1{font-family:'Fraunces',serif;font-size:24px;font-weight:700;color:var(--ink);}
        .page-header p{font-size:14px;color:var(--muted);margin-top:2px;}

        /* Avatar */
        .avatar{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#fff;flex-shrink:0;}

        /* Flash */
        .flash{padding:12px 16px;border-radius:12px;font-size:13.5px;font-weight:500;margin-bottom:20px;display:flex;align-items:center;gap:8px;}
        .flash-success{background:#DCFCE7;color:#166534;border:1px solid #BBF7D0;}
        .flash-error{background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;}

        /* Mobile sidebar toggle */
        .sidebar-mobile-toggle{display:none;width:36px;height:36px;border-radius:10px;border:1px solid var(--hairline);background:#fff;align-items:center;justify-content:center;color:var(--muted);}
        @media(max-width:1023px){
            .sidebar{position:fixed;left:-260px;top:0;bottom:0;z-index:200;transition:left .25s;}
            .sidebar.open{left:0;}
            .sidebar-mobile-toggle{display:flex;}
            .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
            .sidebar-overlay.show{display:block;}
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="admin-wrap" x-data="{ sidebarOpen: false }">

    {{-- Sidebar overlay (mobile) --}}
    <div class="sidebar-overlay" :class="sidebarOpen ? 'show' : ''" @click="sidebarOpen=false"></div>

    {{-- Sidebar --}}
    <aside class="sidebar" :class="sidebarOpen ? 'open' : ''">
        <div class="sidebar-logo">
            <svg width="30" height="30" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#0A64BC"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
            <div>
                <span>Mwalimu</span>
                <small>Admin Panel</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-label">Overview</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                    Dashboard
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-label">People</div>
                <a href="{{ route('admin.tutors') }}" class="nav-item {{ request()->routeIs('admin.tutors') ? 'active' : '' }}">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Tutor Approvals
                    @php $pendingCount = \App\Models\TutorProfile::where('is_approved',false)->count(); @endphp
                    @if($pendingCount > 0)<span class="nav-badge">{{ $pendingCount }}</span>@endif
                </a>
                <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    All Users
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-label">Finance</div>
                <a href="{{ route('admin.payouts') }}" class="nav-item {{ request()->routeIs('admin.payouts') ? 'active' : '' }}">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    Payout Requests
                    @php $payoutCount = \App\Models\PayoutRequest::where('status','pending')->count(); @endphp
                    @if($payoutCount > 0)<span class="nav-badge">{{ $payoutCount }}</span>@endif
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-label">Content</div>
                <a href="{{ route('admin.categories') }}" class="nav-item {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Categories
                </a>
                <a href="{{ route('admin.blog') }}" class="nav-item {{ request()->routeIs('admin.blog') ? 'active' : '' }}">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Blog Posts
                </a>
                <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    Settings
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-label">Site</div>
                <a href="{{ route('home') }}" target="_blank" class="nav-item">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    View Site
                </a>
            </div>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <img src="{{ auth()->user()->avatar_url }}" alt="">
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">Administrator</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="sidebar-user-btn" title="Sign Out" type="submit">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <div class="admin-main">
        {{-- Topbar --}}
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-mobile-toggle" @click="sidebarOpen=!sidebarOpen">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <div class="topbar-title">@yield('title', 'Dashboard')</div>
                    <div class="topbar-breadcrumb">Admin &rsaquo; @yield('title', 'Dashboard')</div>
                </div>
            </div>
            <div class="topbar-right">
                <button class="topbar-btn" title="Notifications">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                    @if($unread > 0)<span class="topbar-notif-badge">{{ $unread }}</span>@endif
                </button>
                <div x-data="{ open: false }" class="relative" style="position:relative;">
                    <button class="topbar-btn" @click="open=!open" @click.outside="open=false" style="display:flex;align-items:center;gap:6px;width:auto;padding:0 10px 0 4px;border-radius:10px;">
                        <img src="{{ auth()->user()->avatar_url }}" style="width:28px;height:28px;border-radius:8px;object-fit:cover;">
                        <span style="font-size:13px;font-weight:600;color:var(--ink);">{{ Str::limit(auth()->user()->name,16) }}</span>
                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition
                         style="position:absolute;right:0;top:calc(100% + 6px);width:180px;background:#fff;border:1px solid var(--hairline);border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.1);padding:6px;z-index:100;">
                        <a href="{{ route('home') }}" target="_blank" style="display:flex;align-items:center;gap:8px;padding:9px 10px;border-radius:8px;font-size:13px;color:var(--ink);" onmouseover="this.style.background='#F4F6F4'" onmouseout="this.style.background='transparent'">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            View Site
                        </a>
                        <div style="height:1px;background:var(--hairline);margin:4px 0;"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="width:100%;display:flex;align-items:center;gap:8px;padding:9px 10px;border-radius:8px;font-size:13px;color:#991B1B;background:none;border:none;cursor:pointer;font-family:inherit;" onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='transparent'">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="admin-content">
            @if(session('success'))
            <div class="flash flash-success">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="flash flash-error">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
            @endif
            @yield('content')
        </div>
    </div>
</div>
@livewireScripts
@stack('scripts')
</body>
</html>
