<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Examiner Dashboard – Mwalimu</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>
:root{--ink:#15281F;--cream:#FBF5E6;--Trust Blue:#0A64BC;--coral:#E8522A;--gold:#EEBE15;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);--sidebar:#111C16;}
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
.sb-nav{flex:1;padding:10px 8px;overflow-y:auto;}
.sb-item{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:10px;font-size:13.5px;font-weight:500;color:rgba(255,255,255,.58);transition:all .15s;margin-bottom:1px;cursor:pointer;border:none;background:none;width:100%;text-align:left;}
.sb-item:hover{background:rgba(255,255,255,.07);color:#fff;}
.sb-item.active{background:var(--Trust Blue);color:#fff;}
.sb-footer{padding:14px;border-top:1px solid rgba(255,255,255,.06);}
.sb-user{display:flex;align-items:center;gap:10px;}
.sb-avatar{width:34px;height:34px;border-radius:10px;background:var(--Trust Blue);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;flex-shrink:0;}
.sb-uname{font-size:13px;font-weight:600;color:#fff;}
.sb-urole{font-size:11px;color:rgba(255,255,255,.35);}
.sb-logout{width:28px;height:28px;border-radius:8px;border:none;background:rgba(255,255,255,.08);color:rgba(255,255,255,.45);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.sb-logout:hover{background:rgba(255,255,255,.16);color:#fff;}

/* Main Content */
.main{flex:1;overflow-y:auto;background:#F2F5F2;}
.top-bar{background:#fff;border-bottom:1px solid var(--hair);padding:14px 28px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:40;}
.top-title{font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);}
.btn{display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:10px;font-size:13.5px;font-weight:600;border:none;cursor:pointer;transition:all .15s;}
.btn-primary{background:var(--Trust Blue);color:#fff;}
.btn-primary:hover{background:#5B21B6;}
.btn-outline{background:#fff;color:var(--ink);border:1px solid var(--hair);}
.btn-outline:hover{background:#FAFCFA;}
.btn-success{background:var(--mint);color:#fff;}
.btn-success:hover{background:#059669;}
.btn-sm{padding:7px 12px;font-size:12.5px;}

/* Content Area */
.content{padding:28px;}
.card{background:#fff;border-radius:14px;border:1px solid var(--hair);overflow:hidden;transition:box-shadow .15s;}
.card:hover{box-shadow:0 2px 12px rgba(0,0,0,.04);}
.card-header{padding:16px 20px;border-bottom:1px solid var(--hair);display:flex;justify-content:between;align-items:center;background:#FAFCFA;}
.card-body{padding:20px;}
.card-footer{padding:14px 20px;border-top:1px solid var(--hair);background:#FAFCFA;}

/* Stats Grid */
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:24px;}
.stat{background:#fff;border:1px solid var(--hair);border-radius:14px;padding:18px;transition:box-shadow .15s;}
.stat:hover{box-shadow:0 2px 12px rgba(0,0,0,.04);}
.stat-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;}
.stat-lbl{font-size:12px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;}
.stat-val{font-family:'Fraunces',serif;font-size:26px;font-weight:700;color:var(--ink);margin-bottom:4px;}
.stat-sub{font-size:12px;color:var(--muted);}

/* Clinic Cards Grid */
.clinics-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:18px;margin-top:24px;}
.clinic-card{background:#fff;border:1px solid var(--hair);border-radius:14px;overflow:hidden;transition:all .15s;display:flex;flex-direction:column;}
.clinic-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.06);transform:translateY(-2px);}
.clinic-hdr{padding:12px 16px;border-bottom:1px solid var(--hair);display:flex;justify-content:space-between;align-items:center;background:#FAFCFA;}
.badge{display:inline-block;padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;}
.badge-primary{background:rgba(10,100,188,.1);color:var(--Trust Blue);}
.badge-success{background:rgba(16,185,129,.1);color:var(--mint);}
.badge-warning{background:rgba(212,160,23,.1);color:#EEBE15;}
.badge-secondary{background:var(--hair);color:var(--muted);}
.clinic-body{padding:18px;flex:1;}
.clinic-title{font-size:16px;font-weight:700;color:var(--ink);margin-bottom:12px;line-height:1.4;}
.clinic-meta{display:flex;flex-direction:column;gap:8px;margin-bottom:14px;}
.clinic-meta-item{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted);}
.clinic-meta-item svg{width:14px;height:14px;opacity:.7;flex-shrink:0;}
.clinic-enrollment{margin:14px 0;}
.enrollment-bar{height:8px;background:var(--hair);border-radius:999px;overflow:hidden;margin-top:6px;}
.enrollment-fill{height:100%;background:var(--mint);border-radius:999px;transition:width .3s;}
.clinic-price{font-size:18px;font-weight:700;color:var(--ink);margin-top:12px;}
.clinic-actions{padding:14px 16px;border-top:1px solid var(--hair);display:flex;gap:8px;}

/* Empty State */
.empty{text-align:center;padding:60px 20px;}
.empty-icon{font-size:64px;margin-bottom:20px;opacity:.3;}
.empty-title{font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);margin-bottom:8px;}
.empty-text{font-size:14px;color:var(--muted);margin-bottom:24px;}

/* Flash Messages */
.flash{padding:14px 18px;border-radius:10px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13.5px;}
.flash-success{background:#DCFCE7;color:#166534;border:1px solid#BBF7D0;}

/* Earnings Widget Adjustments */
.earnings-widget{margin-bottom:24px;}
.earnings-widget .card{border:1px solid var(--hair);}
.earnings-widget .card-header{background:linear-gradient(135deg,var(--mint),#059669);color:#fff;border:none;}
.earnings-widget h5{color:#fff;font-weight:700;}

/* Mobile responsive */
@media(max-width:768px){
    .sidebar{position:fixed;left:-256px;top:0;bottom:0;transition:left .25s;z-index:50;}
    .sidebar.open{left:0;}
    .stats{grid-template-columns:1fr;}
    .clinics-grid{grid-template-columns:1fr;}
    .top-bar{padding:12px 16px;}
    .content{padding:16px;}
}

/* SVG Icons */
svg{stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;fill:none;}
</style>
</head>
<body>
@php
$user = auth()->user();
@endphp

<div class="layout">
    {{-- Sidebar --}}
    <aside class="sidebar">
        <div class="sb-logo">
            <div class="sb-logo-text">Mwalimu</div>
        </div>
        
        <nav class="sb-nav">
            <a href="{{ route('examiner.dashboard') }}" class="sb-item active">
                <svg width="18" height="18" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('examiner.clinics.create') }}" class="sb-item">
                <svg width="18" height="18" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Create Clinic</span>
            </a>
            
            <a href="{{ route('tutor.dashboard') }}" class="sb-item">
                <svg width="18" height="18" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span>Tutor Dashboard</span>
            </a>
            
            <a href="{{ route('examiner-clinic') }}" class="sb-item">
                <svg width="18" height="18" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                <span>Browse Clinics</span>
            </a>
        </nav>

        <div class="sb-footer">
            <div class="sb-user">
                <div class="sb-avatar">{{ substr($user->name, 0, 2) }}</div>
                <div style="flex:1;min-width:0;">
                    <div class="sb-uname">{{ $user->name }}</div>
                    <div class="sb-urole">Examiner</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sb-logout" title="Logout">
                        <svg width="16" height="16" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="main">
        <div class="top-bar">
            <h1 class="top-title">Examiner Dashboard</h1>
            <a href="{{ route('examiner.clinics.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Create Clinic
            </a>
        </div>

        <div class="content">
            @if(session('success'))
                <div class="flash flash-success">
                    <svg width="18" height="18" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Stats Overview --}}
            @php
                $totalClinics = $clinics->total();
                $scheduledClinics = App\Models\ExaminerClinic::where('created_by', $user->id)->where('status', 'scheduled')->count();
                $totalEnrollments = App\Models\ClinicEnrollment::whereHas('clinic', function($q) use ($user) {
                    $q->where('created_by', $user->id);
                })->count();
                $totalRevenue = isset($earnings) ? $earnings['clinic_earnings'] : 0;
            @endphp

            <div class="stats">
                <div class="stat">
                    <div class="stat-icon" style="background:rgba(10,100,188,.1);">
                        <svg width="20" height="20" viewBox="0 0 24 24" stroke="#0A64BC"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    </div>
                    <div class="stat-lbl">Total Clinics</div>
                    <div class="stat-val">{{ $totalClinics }}</div>
                    <div class="stat-sub">{{ $scheduledClinics }} scheduled</div>
                </div>

                <div class="stat">
                    <div class="stat-icon" style="background:rgba(16,185,129,.1);">
                        <svg width="20" height="20" viewBox="0 0 24 24" stroke="#10B981"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="stat-lbl">Total Students</div>
                    <div class="stat-val">{{ $totalEnrollments }}</div>
                    <div class="stat-sub">across all clinics</div>
                </div>

                <div class="stat">
                    <div class="stat-icon" style="background:rgba(212,160,23,.1);">
                        <svg width="20" height="20" viewBox="0 0 24 24" stroke="#EEBE15"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <div class="stat-lbl">Clinic Earnings</div>
                    <div class="stat-val">KSh {{ number_format($totalRevenue, 0) }}</div>
                    <div class="stat-sub">40% commission rate</div>
                </div>

                <div class="stat">
                    <div class="stat-icon" style="background:rgba(232,82,42,.1);">
                        <svg width="20" height="20" viewBox="0 0 24 24" stroke="#E8522A"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <div class="stat-lbl">Avg. Enrollment</div>
                    @php
                        $avgEnrollment = $totalClinics > 0 ? round($totalEnrollments / $totalClinics, 1) : 0;
                    @endphp
                    <div class="stat-val">{{ $avgEnrollment }}</div>
                    <div class="stat-sub">per clinic</div>
                </div>
            </div>

            {{-- Earnings Widget --}}
            @if(isset($earnings))
                <div class="earnings-widget">
                    @include('components.earnings-widget', ['earnings' => $earnings])
                </div>
            @endif

            {{-- Clinics List --}}
            @if($clinics->isEmpty())
                <div class="card">
                    <div class="card-body">
                        <div class="empty">
                            <div class="empty-icon">📚</div>
                            <div class="empty-title">No Clinics Created Yet</div>
                            <div class="empty-text">Start by creating your first examiner clinic. Students can book and join once it's published.</div>
                            <a href="{{ route('examiner.clinics.create') }}" class="btn btn-primary">
                                <svg width="16" height="16" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Create Your First Clinic
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="clinics-grid">
                    @foreach($clinics as $clinic)
                        <div class="clinic-card">
                            <div class="clinic-hdr">
                                <span class="badge badge-primary">{{ $clinic->paper_type }}</span>
                                <span class="badge badge-{{ $clinic->status === 'scheduled' ? 'success' : ($clinic->status === 'live' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($clinic->status) }}
                                </span>
                            </div>

                            <div class="clinic-body">
                                <h3 class="clinic-title">{{ $clinic->name ?? $clinic->title }}</h3>

                                <div class="clinic-meta">
                                    <div class="clinic-meta-item">
                                        <svg width="14" height="14" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                        <span>{{ $clinic->subject->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="clinic-meta-item">
                                        <svg width="14" height="14" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        <span>{{ $clinic->scheduled_at->format('M d, Y') }}</span>
                                    </div>
                                    <div class="clinic-meta-item">
                                        <svg width="14" height="14" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        <span>{{ $clinic->scheduled_at->format('h:i A') }}</span>
                                    </div>
                                </div>

                                <div class="clinic-enrollment">
                                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                                        <span style="font-size:13px;font-weight:600;color:var(--muted);">
                                            <svg width="14" height="14" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                            Enrollment
                                        </span>
                                        <span style="font-size:14px;font-weight:700;color:var(--ink);">
                                            {{ $clinic->enrollments_count }} / {{ $clinic->max_students }}
                                        </span>
                                    </div>
                                    <div class="enrollment-bar">
                                        @php
                                            $percentage = $clinic->max_students > 0 ? ($clinic->enrollments_count / $clinic->max_students * 100) : 0;
                                        @endphp
                                        <div class="enrollment-fill" style="width:{{ $percentage }}%"></div>
                                    </div>
                                </div>

                                <div class="clinic-price">
                                    KSh {{ number_format($clinic->price, 2) }}
                                    <span style="font-size:13px;font-weight:500;color:var(--muted);margin-left:6px;">
                                        (KSh {{ number_format($clinic->price * 0.40, 2) }} yours)
                                    </span>
                                </div>
                            </div>

                            <div class="clinic-actions">
                                <a href="{{ route('examiner.clinics.show', $clinic->id) }}" class="btn btn-outline btn-sm" style="flex:1;">
                                    <svg width="14" height="14" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    View
                                </a>
                                @if($clinic->status === 'scheduled' || $clinic->status === 'live')
                                    <a href="{{ route('examiner.clinics.join', $clinic->id) }}" class="btn btn-success btn-sm" style="flex:1;">
                                        <svg width="14" height="14" viewBox="0 0 24 24"><path d="M23 7l-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
                                        Join
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($clinics->hasPages())
                    <div style="margin-top:28px;display:flex;justify-content:center;">
                        {{ $clinics->links() }}
                    </div>
                @endif
            @endif
        </div>
    </main>
</div>

</body>
</html>
