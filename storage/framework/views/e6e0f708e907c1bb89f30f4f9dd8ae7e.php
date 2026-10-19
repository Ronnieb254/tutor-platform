<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<title>My Dashboard – Mwalimu</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

<style>
:root{--ink:#15281F;--cream:#FBF5E6;--violet:#6D28D9;--coral:#E8522A;--gold:#D4A017;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);--sidebar:#111C16;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:'DM Sans',sans-serif;color:var(--ink);background:#F2F5F2;}
a{color:inherit;text-decoration:none;}[x-cloak]{display:none!important;}
button{font-family:inherit;cursor:pointer;}

.layout{display:flex;height:100vh;overflow:hidden;}

/* Sidebar */
.sidebar{width:256px;background:var(--sidebar);display:flex;flex-direction:column;flex-shrink:0;}
.sb-logo{padding:22px 18px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;gap:10px;}
.sb-logo-text{font-family:'Fraunces',serif;font-size:19px;font-weight:700;color:#fff;}
.sb-logo-sub{font-size:10px;color:rgba(255,255,255,.3);margin-top:1px;}
.sb-nav{flex:1;padding:10px 8px;overflow-y:auto;}
.sb-section{margin-bottom:18px;}
.sb-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.28);padding:0 10px;margin-bottom:5px;}
.sb-item{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:10px;font-size:13.5px;font-weight:500;color:rgba(255,255,255,.58);transition:all .15s;margin-bottom:1px;cursor:pointer;border:none;background:none;width:100%;text-align:left;}
.sb-item:hover{background:rgba(255,255,255,.07);color:#fff;}
.sb-item.active{background:var(--violet);color:#fff;}
.sb-item svg{flex-shrink:0;}
.sb-badge{margin-left:auto;background:var(--coral);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:999px;}
.sb-footer{padding:14px;border-top:1px solid rgba(255,255,255,.06);}
.sb-user{display:flex;align-items:center;gap:10px;}
.sb-avatar{width:34px;height:34px;border-radius:10px;object-fit:cover;flex-shrink:0;}
.sb-uname{font-size:13px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.sb-urole{font-size:11px;color:rgba(255,255,255,.35);}
.sb-logout{width:28px;height:28px;border-radius:8px;border:none;background:rgba(255,255,255,.08);color:rgba(255,255,255,.45);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.sb-logout:hover{background:rgba(255,255,255,.16);color:#fff;}

/* Mobile */
.mob-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:49;}
@media(max-width:1023px){
    .sidebar{position:fixed;left:-256px;top:0;bottom:0;transition:left .25s;z-index:50;}
    .sidebar.open{left:0;}
    .mob-overlay.show{display:block;}
}

/* Main */
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;}
.topbar{height:60px;background:#fff;border-bottom:1px solid var(--hair);display:flex;align-items:center;justify-content:space-between;padding:0 24px;flex-shrink:0;}
.mob-toggle{display:none;width:34px;height:34px;border-radius:9px;border:1px solid var(--hair);background:#fff;align-items:center;justify-content:center;color:var(--muted);}
@media(max-width:1023px){.mob-toggle{display:flex;}}
.page-title{font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:var(--ink);}
.topbar-right{display:flex;align-items:center;gap:10px;}
.tb-btn{width:34px;height:34px;border-radius:9px;border:1px solid var(--hair);background:#fff;display:flex;align-items:center;justify-content:center;color:var(--muted);transition:all .15s;position:relative;}
.tb-btn:hover{border-color:rgba(109,40,217,.4);color:var(--violet);}
.notif-dot{position:absolute;top:-2px;right:-2px;width:8px;height:8px;background:var(--coral);border-radius:50%;border:2px solid #fff;}

.content{flex:1;overflow-y:auto;padding:24px;}

/* Cards */
.card{background:#fff;border-radius:16px;border:1px solid var(--hair);}
.card-p{padding:20px;}

/* Stats */
.stat{background:#fff;border-radius:14px;border:1px solid var(--hair);padding:18px;}
.stat-icon{width:42px;height:42px;border-radius:11px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;}
.stat-lbl{font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;}
.stat-val{font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:var(--ink);line-height:1;}
.stat-sub{font-size:12px;color:var(--muted);margin-top:5px;}

/* Badges */
.bdg{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;}
.bdg-green{background:#DCFCE7;color:#166534;}
.bdg-amber{background:#FEF9C3;color:#854D0E;}
.bdg-red{background:#FEE2E2;color:#991B1B;}
.bdg-violet{background:rgba(109,40,217,.1);color:var(--violet);}
.bdg-blue{background:#DBEAFE;color:#1E40AF;}
.bdg-mint{background:rgba(16,185,129,.1);color:var(--mint);}
.bdg-gray{background:#F3F4F6;color:#6B7280;}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:all .15s;font-family:inherit;text-decoration:none;}
.btn-v{background:var(--violet);color:#fff;}.btn-v:hover{background:#4C1D95;}
.btn-o{background:#fff;color:var(--ink);border:1.5px solid var(--hair);}.btn-o:hover{border-color:var(--violet);color:var(--violet);}
.btn-sm{padding:6px 12px;font-size:12px;border-radius:8px;}
.btn-mint{background:var(--mint);color:#fff;}
.btn-coral{background:var(--coral);color:#fff;}

/* Table */
table{width:100%;border-collapse:collapse;}
thead th{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);padding:10px 14px;text-align:left;border-bottom:1px solid var(--hair);white-space:nowrap;}
tbody tr{border-bottom:1px solid rgba(21,40,31,.05);transition:background .1s;}
tbody tr:hover{background:#FAFCFA;}
tbody tr:last-child{border-bottom:none;}
td{padding:12px 14px;font-size:13.5px;vertical-align:middle;}

/* Avatar */
.av{display:flex;align-items:center;justify-content:center;border-radius:10px;font-weight:700;color:#fff;flex-shrink:0;}

/* Sec header */
.sec-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;}
.sec-title{font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);}

/* Session code */
.code-box{background:rgba(109,40,217,.06);border:1.5px solid rgba(109,40,217,.2);border-radius:10px;padding:6px 12px;text-align:center;}
.code-lbl{font-size:10px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;}
.code-val{font-family:monospace;font-size:15px;font-weight:800;color:var(--violet);}

/* Chat */
.msg{display:flex;gap:10px;margin-bottom:14px;}
.msg.mine{flex-direction:row-reverse;}
.msg-bubble{max-width:75%;padding:10px 14px;border-radius:16px;font-size:13.5px;line-height:1.6;}
.msg.theirs .msg-bubble{background:#F4F6F4;color:var(--ink);border-radius:4px 16px 16px 16px;}
.msg.mine .msg-bubble{background:var(--violet);color:#fff;border-radius:16px 4px 16px 16px;}

/* Flash */
.flash{padding:12px 16px;border-radius:12px;font-size:13.5px;font-weight:500;margin-bottom:18px;display:flex;align-items:center;gap:8px;}
.flash-ok{background:#DCFCE7;color:#166534;border:1px solid #BBF7D0;}
.flash-err{background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;}

/* Tutor card */
.tutor-card{display:flex;align-items:center;gap:14px;padding:14px;border-radius:12px;border:1px solid var(--hair);background:#fff;transition:all .2s;}
.tutor-card:hover{border-color:rgba(109,40,217,.3);box-shadow:0 4px 16px rgba(21,40,31,.08);}
</style>
</head>
<body>
<?php
$user = auth()->user();
$unreadNotifs = $user->unreadNotifications()->count();
$upcomingBookings = $user->bookingsAsStudent()->with(['tutor','subject'])->upcoming()->orderBy('scheduled_at')->get();
$pastBookings = $user->bookingsAsStudent()->with(['tutor','subject','review'])->history()->latest('scheduled_at')->take(50)->get();
$totalSpent = $user->payments()->where('status','success')->sum('amount');
$totalSessions = $user->bookingsAsStudent()->where('status','completed')->count();
$pendingCount = $user->bookingsAsStudent()->where('status','pending')->count();
?>

<div class="layout" x-data="{ sidebarOpen: false, tab: '<?php echo e(request('tab','overview')); ?>' }">

<div class="mob-overlay" :class="sidebarOpen ? 'show' : ''" @click="sidebarOpen=false"></div>


<aside class="sidebar" :class="sidebarOpen ? 'open' : ''">
    <div class="sb-logo">
        <svg width="30" height="30" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
        <div>
            <div class="sb-logo-text">Mwalimu</div>
            <div class="sb-logo-sub"><?php echo e($user->isParent() ? 'Parent Portal' : 'Student Portal'); ?></div>
        </div>
    </div>

    <nav class="sb-nav">
        <div class="sb-section">
            <div class="sb-label">Main</div>
            <button class="sb-item" :class="tab==='overview'?'active':''" @click="tab='overview';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Overview
            </button>
            <a href="<?php echo e(route('tutors.index')); ?>" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                Find a Tutor
            </a>
        </div>

        <div class="sb-section">
            <div class="sb-label">Sessions</div>
            <button class="sb-item" :class="tab==='upcoming'?'active':''" @click="tab='upcoming';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Upcoming Sessions
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($upcomingBookings->count() > 0): ?><span class="sb-badge"><?php echo e($upcomingBookings->count()); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </button>
            <button class="sb-item" :class="tab==='history'?'active':''" @click="tab='history';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 2v10l4 4"/><circle cx="12" cy="12" r="10"/></svg>
                Session History
            </button>
            <button class="sb-item" :class="tab==='chats'?'active':''" @click="tab='chats';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Messages
            </button>
        </div>

        <div class="sb-section">
            <div class="sb-label">Finance</div>
            <button class="sb-item" :class="tab==='payments'?'active':''" @click="tab='payments';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                Payment History
            </button>
        </div>

        <div class="sb-section">
            <div class="sb-label">Account</div>
            <button class="sb-item" :class="tab==='notifications'?'active':''" @click="tab='notifications';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                Notifications
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadNotifs > 0): ?><span class="sb-badge"><?php echo e($unreadNotifs); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->isParent()): ?>
            <button class="sb-item" :class="tab==='children'?'active':''" @click="tab='children';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Child Profiles
            </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <button class="sb-item" :class="tab==='wishlist'?'active':''" @click="tab='wishlist';sidebarOpen=false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                Saved Tutors
            </button>
            <a href="<?php echo e(route('home')); ?>" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                Back to Site
            </a>
        </div>
    </nav>

    <div class="sb-footer">
        <div class="sb-user">
            <img src="<?php echo e($user->avatar_url); ?>" class="sb-avatar" alt="">
            <div style="flex:1;min-width:0;">
                <div class="sb-uname"><?php echo e($user->name); ?></div>
                <div class="sb-urole"><?php echo e(ucfirst($user->role)); ?></div>
            </div>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="sb-logout" title="Sign out">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>


<div class="main">
    <header class="topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button class="mob-toggle" @click="sidebarOpen=!sidebarOpen">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="page-title" x-text="{overview:'Dashboard',upcoming:'Upcoming Sessions',history:'Session History',chats:'Messages',payments:'Payment History',notifications:'Notifications',children:'Child Profiles',wishlist:'Saved Tutors'}[tab]||'Dashboard'"></div>
        </div>
        <div class="topbar-right">
            <button class="tb-btn" @click="tab='notifications'">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadNotifs > 0): ?><span class="notif-dot"></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </button>
            <a href="<?php echo e(route('tutors.index')); ?>" class="btn btn-v btn-sm">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Book a Tutor
            </a>
        </div>
    </header>

    <div class="content">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="flash flash-ok"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg><?php echo e(session('success')); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="flash flash-err"><?php echo e(session('error')); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<div x-show="tab==='overview'">
    <div style="margin-bottom:20px;">
        <h2 style="font-family:'Fraunces',serif;font-size:26px;font-weight:700;color:var(--ink);">Hello, <?php echo e(explode(' ',$user->name)[0]); ?> 👋</h2>
        <p style="font-size:14px;color:var(--muted);margin-top:4px;"><?php echo e(now()->format('l, F j, Y')); ?></p>
    </div>

    
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:20px;" class="stats-g">
        <div class="stat">
            <div class="stat-icon" style="background:rgba(109,40,217,.1);">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#6D28D9" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="stat-lbl">Upcoming Sessions</div>
            <div class="stat-val"><?php echo e($upcomingBookings->count()); ?></div>
            <div class="stat-sub"><?php echo e($pendingCount); ?> awaiting confirmation</div>
        </div>
        <div class="stat">
            <div class="stat-icon" style="background:rgba(16,185,129,.1);">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#10B981" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="stat-lbl">Completed Sessions</div>
            <div class="stat-val"><?php echo e($totalSessions); ?></div>
            <div class="stat-sub">Across all subjects</div>
        </div>
        <div class="stat">
            <div class="stat-icon" style="background:rgba(232,82,42,.1);">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E8522A" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            </div>
            <div class="stat-lbl">Total Spent</div>
            <div class="stat-val" style="font-size:22px;">KSh <?php echo e(number_format($totalSpent)); ?></div>
            <div class="stat-sub">All time payments</div>
        </div>
        <div class="stat">
            <div class="stat-icon" style="background:rgba(212,160,23,.1);">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4A017" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </div>
            <div class="stat-lbl">Saved Tutors</div>
            <div class="stat-val"><?php echo e($user->wishlist()->count()); ?></div>
            <div class="stat-sub"><button @click="tab='wishlist'" style="background:none;border:none;color:var(--violet);font-size:12px;cursor:pointer;font-family:inherit;">View wishlist →</button></div>
        </div>
    </div>
    <style>.stats-g{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:768px){.stats-g{grid-template-columns:repeat(4,1fr)!important;}}</style>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($upcomingBookings->isNotEmpty()): ?>
    <div class="card" style="margin-bottom:20px;">
        <div style="padding:16px 20px;border-bottom:1px solid var(--hair);display:flex;align-items:center;justify-content:space-between;">
            <div class="sec-title">Upcoming Sessions</div>
            <button @click="tab='upcoming'" class="btn btn-o btn-sm">View All</button>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $upcomingBookings->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid rgba(21,40,31,.05);">
            <div class="av" style="width:44px;height:44px;background:#6D28D9;border-radius:12px;font-size:16px;flex-shrink:0;"><?php echo e(substr($b->tutor->name,0,1)); ?></div>
            <div style="flex:1;min-width:0;">
                <div style="font-weight:600;font-size:14px;"><?php echo e($b->tutor->name); ?></div>
                <div style="font-size:12px;color:var(--muted);"><?php echo e($b->subject?->name ?? 'General'); ?> · <?php echo e($b->duration_minutes); ?>min</div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
                <div style="font-weight:600;font-size:13px;color:var(--ink);"><?php echo e($b->scheduled_at->format('M j')); ?></div>
                <div style="font-size:12px;color:var(--muted);"><?php echo e($b->scheduled_at->format('g:ia')); ?></div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($b->session_code_student): ?>
            <div class="code-box" style="flex-shrink:0;">
                <div class="code-lbl">Your Code</div>
                <div class="code-val"><?php echo e($b->session_code_student); ?></div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($b->status === 'confirmed'): ?>
            <a href="<?php echo e(route('bookings.show',$b->uuid)); ?>" class="btn btn-mint btn-sm"><?php echo e($b->canJoin() ? 'Join Now' : 'Open Session'); ?></a>
            <?php else: ?>
            <a href="<?php echo e(route('bookings.show',$b->uuid)); ?>" class="btn btn-o btn-sm">View</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php else: ?>
    <div class="card card-p" style="text-align:center;padding:48px;margin-bottom:20px;">
        <div style="font-size:48px;margin-bottom:12px;">📅</div>
        <h3 style="font-family:'Fraunces',serif;font-size:20px;color:var(--ink);margin-bottom:8px;">No upcoming sessions</h3>
        <p style="font-size:14px;color:var(--muted);margin-bottom:20px;">Book a session with one of our verified tutors to get started.</p>
        <a href="<?php echo e(route('tutors.index')); ?>" class="btn btn-v">Find a Tutor</a>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;" class="quick-g">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
            ['upcoming','📅','My Sessions','View all booked sessions'],
            ['chats','💬','Messages','Chat with your tutors'],
            ['payments','💳','Payments','View payment history'],
            ['wishlist','❤️','Saved Tutors','Your favourite tutors'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$t,$ic,$label,$desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <button @click="tab='<?php echo e($t); ?>'" style="background:#fff;border:1px solid var(--hair);border-radius:14px;padding:18px;text-align:left;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:14px;" onmouseover="this.style.borderColor='rgba(109,40,217,.4)';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='var(--hair)';this.style.transform='none'">
            <div style="font-size:26px;width:48px;height:48px;background:rgba(109,40,217,.08);border-radius:12px;display:flex;align-items:center;justify-content:center;"><?php echo e($ic); ?></div>
            <div>
                <div style="font-weight:700;font-size:14px;color:var(--ink);"><?php echo e($label); ?></div>
                <div style="font-size:12px;color:var(--muted);margin-top:2px;"><?php echo e($desc); ?></div>
            </div>
        </button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <style>.quick-g{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:768px){.quick-g{grid-template-columns:repeat(4,1fr)!important;}}</style>
</div>


<div x-show="tab==='upcoming'" x-cloak>
    <div style="margin-bottom:16px;display:flex;justify-content:flex-end;">
        <a href="<?php echo e(route('tutors.index')); ?>" class="btn btn-v">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Book New Session
        </a>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $upcomingBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="card" style="margin-bottom:14px;padding:20px;">
        <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap;">
            <div class="av" style="width:52px;height:52px;background:#6D28D9;border-radius:14px;font-size:20px;flex-shrink:0;"><?php echo e(substr($b->tutor->name,0,1)); ?></div>
            <div style="flex:1;min-width:180px;">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px;">
                    <span style="font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:var(--ink);"><?php echo e($b->tutor->name); ?></span>
                    <?php $sc=['pending'=>'bdg-amber','confirmed'=>'bdg-blue','completed'=>'bdg-green','cancelled'=>'bdg-gray'][$b->status]??'bdg-gray'; ?>
                    <span class="bdg <?php echo e($sc); ?>"><?php echo e(ucfirst($b->status)); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($b->canJoin()): ?><span class="bdg bdg-mint">🔴 Live Now</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div style="font-size:13px;color:var(--muted);"><?php echo e($b->subject?->name ?? 'General Session'); ?> · <?php echo e($b->duration_minutes); ?> minutes</div>
                <div style="display:flex;flex-wrap:wrap;gap:16px;margin-top:10px;">
                    <div style="display:flex;align-items:center;gap:6px;font-size:13px;">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="var(--muted)" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span style="color:var(--ink);font-weight:500;"><?php echo e($b->scheduled_at->format('D, M j, Y')); ?></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;font-size:13px;">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="var(--muted)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span style="color:var(--ink);font-weight:500;"><?php echo e($b->scheduled_at->format('g:i a')); ?></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;font-size:13px;">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="var(--muted)" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        <span style="color:var(--ink);font-weight:600;">KSh <?php echo e(number_format($b->total_amount)); ?></span>
                    </div>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($b->session_code_student): ?>
            <div style="background:rgba(109,40,217,.06);border:2px solid rgba(109,40,217,.2);border-radius:14px;padding:14px 18px;text-align:center;flex-shrink:0;">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:6px;">Your Session Code</div>
                <div style="font-family:monospace;font-size:22px;font-weight:800;color:var(--violet);"><?php echo e($b->session_code_student); ?></div>
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">Enter this to join your session</div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div style="display:flex;flex-direction:column;gap:8px;flex-shrink:0;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($b->status === 'confirmed'): ?>
                <a href="<?php echo e(route('bookings.show',$b->uuid)); ?>" class="btn btn-mint"><?php echo e($b->canJoin() ? '🎥 Join Session' : 'Open Session'); ?></a>
                <?php else: ?>
                <a href="<?php echo e(route('bookings.show',$b->uuid)); ?>" class="btn btn-v">View Booking</a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($b->status,['pending','confirmed']) && $b->scheduled_at->isFuture()): ?>
                <form method="POST" action="<?php echo e(route('bookings.cancel',$b->uuid)); ?>" onsubmit="return confirm('Cancel this booking?')">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="reason" value="Cancelled by student">
                    <button type="submit" class="btn btn-sm" style="width:100%;background:#FEF2F2;color:#991B1B;border:none;">Cancel</button>
                </form>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($b->scheduled_at->isFuture()): ?>
        <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--hair);display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted);">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Session starts <?php echo e($b->scheduled_at->diffForHumans()); ?>

        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="card card-p" style="text-align:center;padding:64px;">
        <div style="font-size:48px;margin-bottom:12px;">📅</div>
        <h3 style="font-family:'Fraunces',serif;font-size:20px;color:var(--ink);margin-bottom:8px;">No upcoming sessions</h3>
        <p style="color:var(--muted);font-size:14px;margin-bottom:20px;">Find a tutor and book your first session today.</p>
        <a href="<?php echo e(route('tutors.index')); ?>" class="btn btn-v">Browse Tutors</a>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<div x-show="tab==='history'" x-cloak>
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid var(--hair);">
            <div class="sec-title">Session History</div>
            <div style="font-size:12px;color:var(--muted);margin-top:1px;"><?php echo e($pastBookings->count()); ?> past sessions</div>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead><tr>
                    <th>Tutor</th><th>Subject</th><th>Date</th><th>Duration</th><th>Paid</th><th>Status</th><th>Review</th><th></th>
                </tr></thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pastBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="av" style="width:32px;height:32px;background:#6D28D9;border-radius:9px;font-size:13px;"><?php echo e(substr($b->tutor->name,0,1)); ?></div>
                                <div>
                                    <div style="font-weight:600;font-size:13.5px;"><?php echo e($b->tutor->name); ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--muted);"><?php echo e($b->subject?->name ?? 'General'); ?></td>
                        <td style="white-space:nowrap;font-size:13px;"><?php echo e($b->scheduled_at->format('M j, Y g:ia')); ?></td>
                        <td><?php echo e($b->duration_minutes); ?>min</td>
                        <td style="font-weight:600;">KSh <?php echo e(number_format($b->total_amount)); ?></td>
                        <td>
                            <?php $sc=['pending'=>'bdg-amber','confirmed'=>'bdg-blue','completed'=>'bdg-green','cancelled'=>'bdg-red','no_show'=>'bdg-gray'][$b->status]??'bdg-gray'; ?>
                            <span class="bdg <?php echo e($sc); ?>"><?php echo e(ucfirst(str_replace('_',' ',$b->status))); ?></span>
                        </td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($b->status === 'completed' && $b->review): ?>
                            <div style="display:flex;gap:1px;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i=1;$i<=5;$i++): ?><span style="color:<?php echo e($i<=$b->review->rating ? 'var(--gold)' : 'var(--hair)'); ?>;font-size:13px;">★</span><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php elseif($b->status === 'completed'): ?>
                            <a href="<?php echo e(route('bookings.show',$b->uuid)); ?>" style="font-size:12px;color:var(--violet);font-weight:600;">Leave review</a>
                            <?php else: ?>
                            <span style="color:var(--muted);">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td><a href="<?php echo e(route('bookings.show',$b->uuid)); ?>" class="btn btn-o btn-sm">View</a></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--muted);">No session history yet</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div x-show="tab==='chats'" x-cloak>
    <?php
    $chatBookings = $user->bookingsAsStudent()
        ->with(['tutor','messages' => fn($q) => $q->latest()->limit(1)])
        ->whereIn('status',['confirmed','completed'])
        ->latest()->get();
    ?>
    <div style="display:grid;grid-template-columns:300px 1fr;gap:16px;height:calc(100vh - 160px);min-height:500px;" class="chat-layout">
        <div class="card" style="display:flex;flex-direction:column;overflow:hidden;">
            <div style="padding:14px 16px;border-bottom:1px solid var(--hair);">
                <div class="sec-title">Conversations</div>
                <div style="font-size:12px;color:var(--muted);"><?php echo e($chatBookings->count()); ?> chats</div>
            </div>
            <div style="flex:1;overflow-y:auto;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $chatBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $lastMsg = $cb->messages->first(); $unread = $cb->messages()->where('receiver_id',$user->id)->whereNull('read_at')->count(); ?>
                <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;cursor:pointer;border-bottom:1px solid rgba(21,40,31,.04);transition:background .1s;" onmouseover="this.style.background='#F9FAF9'" onmouseout="this.style.background='transparent'">
                    <div class="av" style="width:40px;height:40px;background:#6D28D9;border-radius:11px;font-size:15px;flex-shrink:0;"><?php echo e(substr($cb->tutor->name,0,1)); ?></div>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:4px;">
                            <span style="font-weight:600;font-size:13.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo e($cb->tutor->name); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unread > 0): ?><span class="bdg bdg-violet" style="font-size:10px;padding:2px 6px;flex-shrink:0;"><?php echo e($unread); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div style="font-size:12px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo e($lastMsg ? Str::limit($lastMsg->body,40) : 'No messages yet'); ?></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="padding:40px 16px;text-align:center;color:var(--muted);">
                    <div style="font-size:32px;margin-bottom:8px;">💬</div>
                    <div style="font-size:14px;">No conversations yet</div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <div class="card" style="display:flex;flex-direction:column;overflow:hidden;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($chatBookings->isNotEmpty()): ?>
            <?php $fb = $chatBookings->first(); ?>
            <div style="padding:14px 20px;border-bottom:1px solid var(--hair);display:flex;align-items:center;gap:12px;">
                <div class="av" style="width:36px;height:36px;background:#6D28D9;border-radius:10px;font-size:13px;"><?php echo e(substr($fb->tutor->name,0,1)); ?></div>
                <div>
                    <div style="font-weight:700;font-size:14px;"><?php echo e($fb->tutor->name); ?></div>
                    <div style="font-size:12px;color:var(--muted);"><?php echo e($fb->subject?->name ?? 'General'); ?></div>
                </div>
                <a href="<?php echo e(route('bookings.show',$fb->uuid)); ?>" class="btn btn-o btn-sm" style="margin-left:auto;">View Booking</a>
            </div>
            <?php $messages = $fb->messages()->with('sender')->oldest()->get(); ?>
            <div style="flex:1;overflow-y:auto;padding:20px;" id="chat-messages">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $isMine = $msg->sender_id === $user->id; ?>
                <div class="msg <?php echo e($isMine ? 'mine' : 'theirs'); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isMine): ?><div class="av" style="width:30px;height:30px;background:#6D28D9;border-radius:8px;font-size:11px;flex-shrink:0;"><?php echo e(substr($msg->sender->name,0,1)); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div>
                        <div class="msg-bubble"><?php echo e($msg->body); ?></div>
                        <div style="font-size:11px;color:var(--muted);margin-top:3px;<?php echo e($isMine ? 'text-align:right;' : ''); ?>"><?php echo e($msg->created_at->format('g:ia')); ?></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="text-align:center;color:var(--muted);font-size:14px;padding:40px;">Start the conversation with your tutor!</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <form method="POST" action="/bookings/<?php echo e($fb->id); ?>/messages" style="padding:16px 20px;border-top:1px solid var(--hair);display:flex;gap:10px;">
                <?php echo csrf_field(); ?>
                <input type="text" name="body" placeholder="Type a message…" required style="flex:1;height:44px;padding:0 16px;border-radius:12px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;" onfocus="this.style.borderColor='var(--violet)'" onblur="this.style.borderColor='var(--hair)'">
                <button type="submit" class="btn btn-v" style="height:44px;padding:0 20px;">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>Send
                </button>
            </form>
            <?php else: ?>
            <div style="flex:1;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:12px;color:var(--muted);">
                <div style="font-size:48px;">💬</div>
                <div style="font-size:16px;font-weight:600;color:var(--ink);">No messages yet</div>
                <div style="font-size:14px;text-align:center;">Messages appear here once a booking is confirmed</div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <style>.chat-layout{grid-template-columns:1fr!important;}@media(min-width:768px){.chat-layout{grid-template-columns:280px 1fr!important;}}</style>
</div>


<div x-show="tab==='payments'" x-cloak>
    <?php
    $payments = $user->payments()->with('booking.tutor')->where('status','success')->latest('paid_at')->paginate(20);
    ?>

    
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px;" class="pay-stats">
        <div class="stat">
            <div class="stat-icon" style="background:rgba(109,40,217,.1);">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#6D28D9" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            </div>
            <div class="stat-lbl">Total Spent</div>
            <div class="stat-val" style="font-size:22px;">KSh <?php echo e(number_format($totalSpent)); ?></div>
        </div>
        <div class="stat">
            <div class="stat-icon" style="background:rgba(16,185,129,.1);">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#10B981" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="stat-lbl">Successful Payments</div>
            <div class="stat-val"><?php echo e($payments->total()); ?></div>
        </div>
        <div class="stat">
            <div class="stat-icon" style="background:rgba(212,160,23,.1);">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4A017" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="stat-lbl">This Month</div>
            <div class="stat-val" style="font-size:22px;">KSh <?php echo e(number_format($user->payments()->where('status','success')->whereMonth('paid_at',now()->month)->sum('amount'))); ?></div>
        </div>
    </div>
    <style>.pay-stats{grid-template-columns:1fr!important;}@media(min-width:768px){.pay-stats{grid-template-columns:repeat(3,1fr)!important;}}</style>

    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid var(--hair);">
            <div class="sec-title">Payment History</div>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead><tr>
                    <th>Tutor</th><th>Reference</th><th>Date</th><th>Session</th><th>Amount</th><th>Status</th><th></th>
                </tr></thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="av" style="width:30px;height:30px;background:#6D28D9;border-radius:8px;font-size:12px;"><?php echo e(substr($payment->booking?->tutor->name ?? 'T',0,1)); ?></div>
                                <?php echo e($payment->booking?->tutor->name ?? '—'); ?>

                            </div>
                        </td>
                        <td><code style="background:#F4F6F4;padding:2px 8px;border-radius:6px;font-size:11px;"><?php echo e(Str::limit($payment->paystack_reference,18)); ?></code></td>
                        <td style="font-size:13px;white-space:nowrap;"><?php echo e($payment->paid_at?->format('M j, Y') ?? '—'); ?></td>
                        <td style="font-size:13px;color:var(--muted);">
                            <?php echo e($payment->booking?->duration_minutes); ?>min · <?php echo e($payment->booking?->subject?->name ?? 'General'); ?>

                        </td>
                        <td style="font-weight:700;color:var(--ink);">KSh <?php echo e(number_format($payment->amount)); ?></td>
                        <td><span class="bdg bdg-green">✓ Paid</span></td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->booking): ?>
                            <a href="<?php echo e(route('bookings.show',$payment->booking->uuid)); ?>" class="btn btn-o btn-sm">View</a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--muted);">No payments yet</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payments->hasPages()): ?>
        <div style="padding:16px 20px;border-top:1px solid var(--hair);"><?php echo e($payments->links()); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>


<div x-show="tab==='notifications'" x-cloak>
    <?php $notifications = $user->notifications()->latest()->paginate(20); ?>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div>
            <h2 style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--ink);">Notifications</h2>
            <p style="font-size:13px;color:var(--muted);"><?php echo e($user->unreadNotifications()->count()); ?> unread</p>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->unreadNotifications()->count() > 0): ?>
        <form method="POST" action="/student/notifications/mark-all-read"><?php echo csrf_field(); ?>
            <button class="btn btn-o btn-sm">Mark all read</button>
        </form>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="card">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php $isUnread = is_null($notif->read_at); $data = $notif->data; ?>
        <div style="display:flex;align-items:flex-start;gap:14px;padding:16px 20px;border-bottom:1px solid rgba(21,40,31,.05);background:<?php echo e($isUnread ? 'rgba(109,40,217,.04)' : 'transparent'); ?>;">
            <?php $icons=['new_booking'=>['📅','rgba(109,40,217,.1)'],'booking_confirmed'=>['✅','rgba(16,185,129,.1)'],'booking_cancelled'=>['❌','rgba(232,82,42,.1)']]; [$ic,$bg]=$icons[$data['type']??'']??['🔔','rgba(21,40,31,.05)']; ?>
            <div style="width:44px;height:44px;border-radius:12px;background:<?php echo e($bg); ?>;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;"><?php echo e($ic); ?></div>
            <div style="flex:1;">
                <div style="font-weight:<?php echo e($isUnread?'700':'500'); ?>;font-size:14px;color:var(--ink);"><?php echo e($data['message'] ?? 'New notification'); ?></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($data['code'])): ?>
                <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(109,40,217,.08);border:1px solid rgba(109,40,217,.2);border-radius:8px;padding:4px 10px;margin-top:6px;">
                    <span style="font-size:11px;color:var(--muted);">Your access code:</span>
                    <code style="font-size:14px;font-weight:800;color:var(--violet);"><?php echo e($data['code']); ?></code>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($data['scheduled_at'])): ?>
                <div style="font-size:12px;color:var(--muted);margin-top:4px;">📅 <?php echo e(\Carbon\Carbon::parse($data['scheduled_at'])->format('D, M j \a\t g:ia')); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div style="font-size:11px;color:var(--muted);margin-top:6px;"><?php echo e($notif->created_at->diffForHumans()); ?></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($data['url'])): ?>
                <a href="<?php echo e($data['url']); ?>" class="btn btn-mint btn-sm" style="margin-top:8px;">Join / View Session</a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isUnread): ?><div style="width:8px;height:8px;background:var(--violet);border-radius:50%;margin-top:6px;flex-shrink:0;"></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div style="padding:64px;text-align:center;">
            <div style="font-size:44px;margin-bottom:12px;">🔔</div>
            <div style="font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:var(--ink);margin-bottom:6px;">No notifications yet</div>
            <div style="font-size:14px;color:var(--muted);">You'll be notified here when your booking is confirmed</div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>


<div x-show="tab==='wishlist'" x-cloak>
    <?php $wishlist = $user->wishlist()->with(['user','subjects'])->get(); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($wishlist->isEmpty()): ?>
    <div class="card card-p" style="text-align:center;padding:64px;">
        <div style="font-size:48px;margin-bottom:12px;">❤️</div>
        <h3 style="font-family:'Fraunces',serif;font-size:20px;color:var(--ink);margin-bottom:8px;">No saved tutors yet</h3>
        <p style="color:var(--muted);font-size:14px;margin-bottom:20px;">Click the heart icon on any tutor's profile to save them here.</p>
        <a href="<?php echo e(route('tutors.index')); ?>" class="btn btn-v">Browse Tutors</a>
    </div>
    <?php else: ?>
    <div style="display:grid;grid-template-columns:1fr;gap:14px;" class="wl-grid">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $wishlist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="tutor-card">
            <div class="av" style="width:56px;height:56px;background:#<?php echo e(substr(md5($profile->user->email),0,6)); ?>;border-radius:14px;font-size:20px;flex-shrink:0;"><?php echo e(substr($profile->user->name,0,1)); ?></div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                    <span style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);"><?php echo e($profile->user->name); ?></span>
                    <span style="font-size:11px;font-weight:600;color:var(--mint);">✓ Verified</span>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:6px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $profile->subjects->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="bdg bdg-violet"><?php echo e($s->name); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div style="display:flex;gap:12px;font-size:13px;color:var(--muted);">
                    <span>★ <?php echo e(number_format($profile->rating_avg,1)); ?> (<?php echo e($profile->total_reviews); ?>)</span>
                    <span>·</span>
                    <span style="font-weight:600;color:var(--ink);">KSh <?php echo e(number_format($profile->hourly_rate)); ?>/hr</span>
                    <span>·</span>
                    <span><?php echo e($profile->experience_years); ?>y exp</span>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:8px;flex-shrink:0;">
                <a href="<?php echo e(route('tutors.show', $profile->id)); ?>" class="btn btn-v btn-sm">Book Session</a>
                <a href="<?php echo e(route('tutors.show', $profile->id)); ?>" class="btn btn-o btn-sm">View Profile</a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <style>.wl-grid{grid-template-columns:1fr!important;}@media(min-width:768px){.wl-grid{grid-template-columns:repeat(2,1fr)!important;}}</style>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->isParent()): ?>
<div x-show="tab==='children'" x-cloak>
    <?php $children = $user->childProfiles()->get(); ?>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <h2 style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--ink);">Child Profiles</h2>
        <button onclick="document.getElementById('add-child-modal').style.display='flex'" class="btn btn-v">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Child
        </button>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($children->isEmpty()): ?>
    <div class="card card-p" style="text-align:center;padding:48px;">
        <div style="font-size:44px;margin-bottom:12px;">👨‍👩‍👧</div>
        <h3 style="font-family:'Fraunces',serif;font-size:18px;color:var(--ink);margin-bottom:8px;">No child profiles yet</h3>
        <p style="color:var(--muted);font-size:14px;margin-bottom:20px;">Add a child profile to book sessions on their behalf.</p>
        <button onclick="document.getElementById('add-child-modal').style.display='flex'" class="btn btn-v">Add Child Profile</button>
    </div>
    <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;" class="ch-grid">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card card-p">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:52px;height:52px;border-radius:14px;background:rgba(109,40,217,.1);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">👦</div>
                <div style="flex:1;">
                    <div style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);"><?php echo e($child->name); ?></div>
                    <div style="font-size:13px;color:var(--muted);margin-top:3px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($child->age): ?> Age <?php echo e($child->age); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($child->grade_level): ?> · <?php echo e($child->grade_level); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($child->notes): ?>
            <p style="font-size:13px;color:var(--muted);margin-top:10px;padding-top:10px;border-top:1px solid var(--hair);"><?php echo e($child->notes); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div style="margin-top:12px;">
                <a href="<?php echo e(route('tutors.index')); ?>" class="btn btn-v btn-sm" style="width:100%;justify-content:center;">Book Session for <?php echo e(explode(' ',$child->name)[0]); ?></a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <style>.ch-grid{grid-template-columns:1fr!important;}@media(min-width:640px){.ch-grid{grid-template-columns:repeat(2,1fr)!important;}}</style>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div id="add-child-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;align-items:center;justify-content:center;padding:16px;">
        <div style="background:#fff;border-radius:20px;padding:32px;width:100%;max-width:420px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <h3 style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--ink);">Add Child Profile</h3>
                <button onclick="document.getElementById('add-child-modal').style.display='none'" style="background:none;border:none;font-size:22px;color:var(--muted);cursor:pointer;">&times;</button>
            </div>
            <form method="POST" action="/student/children">
                <?php echo csrf_field(); ?>
                <div style="margin-bottom:12px;">
                    <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">Child's Name</label>
                    <input type="text" name="name" required placeholder="e.g. James Kamau" style="width:100%;height:44px;padding:0 14px;border-radius:11px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
                    <div>
                        <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">Age</label>
                        <input type="number" name="age" min="3" max="25" placeholder="e.g. 12" style="width:100%;height:44px;padding:0 14px;border-radius:11px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;">
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">Grade / Level</label>
                        <input type="text" name="grade_level" placeholder="e.g. Form 3" style="width:100%;height:44px;padding:0 14px;border-radius:11px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;">
                    </div>
                </div>
                <div style="margin-bottom:20px;">
                    <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">Notes (optional)</label>
                    <textarea name="notes" rows="2" placeholder="Learning goals, subjects they need help with…" style="width:100%;padding:10px 14px;border-radius:11px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;resize:none;"></textarea>
                </div>
                <div style="display:flex;gap:10px;">
                    <button type="button" onclick="document.getElementById('add-child-modal').style.display='none'" class="btn btn-o" style="flex:1;">Cancel</button>
                    <button type="submit" class="btn btn-v" style="flex:1;">Add Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    </div>
</div>
</div>

<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

<script>
const chat = document.getElementById('chat-messages');
if(chat) chat.scrollTop = chat.scrollHeight;
</script>
</body>
</html>
<?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/pages/dashboard/student.blade.php ENDPATH**/ ?>