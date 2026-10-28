<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>My Resources – Mwalimu</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--ink:#15281F;--cream:#FBF5E6;--trust-blue:#0A64BC;--coral:#E8522A;--mint:#10B981;--gold:#EEBE15;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);--sidebar:#111C16;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:'DM Sans',sans-serif;color:var(--ink);background:#F2F5F2;}
a{color:inherit;text-decoration:none;}
button{font-family:inherit;cursor:pointer;}
.layout{display:flex;height:100vh;overflow:hidden;}
.sidebar{width:256px;background:var(--sidebar);display:flex;flex-direction:column;flex-shrink:0;}
.sb-logo{padding:22px 18px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;gap:10px;}
.sb-logo-text{font-family:'Fraunces',serif;font-size:19px;font-weight:700;color:#fff;}
.sb-nav{flex:1;padding:10px 8px;overflow-y:auto;}
.sb-section{margin-bottom:18px;}
.sb-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.28);padding:0 10px;margin-bottom:5px;}
.sb-item{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:10px;font-size:13.5px;font-weight:500;color:rgba(255,255,255,.58);transition:all .15s;margin-bottom:1px;cursor:pointer;border:none;background:none;width:100%;text-align:left;text-decoration:none;}
.sb-item:hover{background:rgba(255,255,255,.07);color:#fff;}
.sb-item.active{background:var(--trust-blue);color:#fff;}
.sb-footer{padding:14px;border-top:1px solid rgba(255,255,255,.06);}
.sb-user{display:flex;align-items:center;gap:10px;}
.sb-avatar{width:34px;height:34px;border-radius:10px;object-fit:cover;flex-shrink:0;}
.sb-uname{font-size:13px;font-weight:600;color:#fff;}
.sb-urole{font-size:11px;color:rgba(255,255,255,.35);}
.sb-logout{width:28px;height:28px;border-radius:8px;border:none;background:rgba(255,255,255,.08);color:rgba(255,255,255,.45);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;}
.topbar{height:60px;background:#fff;border-bottom:1px solid var(--hair);display:flex;align-items:center;justify-content:space-between;padding:0 24px;flex-shrink:0;}
.page-title{font-family:'Fraunces',serif;font-size:18px;font-weight:700;}
.content{flex:1;overflow-y:auto;padding:24px;}
.card{background:#fff;border-radius:16px;border:1px solid var(--hair);}
.card-p{padding:20px;}
.stat{background:#fff;border-radius:14px;border:1px solid var(--hair);padding:18px;}
.stat-lbl{font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;}
.stat-val{font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:var(--ink);}
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:all .15s;font-family:inherit;text-decoration:none;}
.btn-v{background:var(--trust-blue);color:#fff;}.btn-v:hover{background:#0850a0;}
.btn-o{background:#fff;color:var(--ink);border:1.5px solid var(--hair);}.btn-o:hover{border-color:var(--trust-blue);color:var(--trust-blue);}
.btn-sm{padding:6px 12px;font-size:12px;border-radius:8px;}
.bdg{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;}
.bdg-blue{background:#DBEAFE;color:#1E40AF;}
.bdg-green{background:#DCFCE7;color:#166534;}
.bdg-amber{background:#FEF9C3;color:#854D0E;}
.bdg-red{background:#FEE2E2;color:#991B1B;}
.bdg-purple{background:#EDE9FE;color:#5B21B6;}
.bdg-gray{background:#F3F4F6;color:#6B7280;}
.flash{padding:12px 16px;border-radius:12px;font-size:13.5px;font-weight:500;margin-bottom:18px;display:flex;align-items:center;gap:8px;}
.flash-ok{background:#DCFCE7;color:#166534;border:1px solid #BBF7D0;}
.flash-err{background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;}
table{width:100%;border-collapse:collapse;}
thead th{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);padding:10px 14px;text-align:left;border-bottom:1px solid var(--hair);}
tbody tr{border-bottom:1px solid rgba(21,40,31,.05);}
tbody tr:hover{background:#FAFCFA;}
tbody tr:last-child{border-bottom:none;}
td{padding:12px 14px;font-size:13.5px;vertical-align:middle;}
@media(max-width:1023px){.sidebar{display:none;}}
</style>
</head>
<body>
<div class="layout">
<aside class="sidebar">
    <div class="sb-logo">
        <svg width="30" height="30" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#0A64BC"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
        <div><div class="sb-logo-text">Mwalimu</div><div style="font-size:10px;color:rgba(255,255,255,.3);">Tutor Portal</div></div>
    </div>
    <nav class="sb-nav">
        <div class="sb-section">
            <div class="sb-label">Tutor Tools</div>
            <a href="{{ route('tutor.dashboard') }}" class="sb-item"><svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Dashboard</a>
            <a href="{{ route('tutor.library.index') }}" class="sb-item active">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                My Resources
            </a>
            <a href="{{ route('tutor.library.create') }}" class="sb-item"><svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Upload Resource</a>
        </div>
        <div class="sb-section">
            <div class="sb-label">Sessions</div>
            <a href="{{ route('tutor.group-sessions.index') }}" class="sb-item"><svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>Group Sessions</a>
        </div>
        <div class="sb-section">
            <div class="sb-label">Navigation</div>
            <a href="{{ route('library.index') }}" class="sb-item"><svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>Public Library</a>
            <a href="{{ route('home') }}" class="sb-item"><svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>Back to Site</a>
        </div>
    </nav>
    <div class="sb-footer">
        <div class="sb-user">
            <img src="{{ auth()->user()->avatar_url }}" class="sb-avatar" alt="">
            <div style="flex:1;min-width:0;"><div class="sb-uname">{{ auth()->user()->name }}</div><div class="sb-urole">Tutor</div></div>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="sb-logout"><svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></button>
            </form>
        </div>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <div class="page-title">📚 My Resources</div>
        <a href="{{ route('tutor.library.create') }}" class="btn btn-v">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Upload Resource
        </a>
    </header>

    <div class="content">
        @if(session('success'))
        <div class="flash flash-ok">✓ {{ session('success') }}</div>
        @endif

        {{-- Stats --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px;" class="stats-g">
            <div class="stat">
                <div class="stat-lbl">Total Resources</div>
                <div class="stat-val">{{ $stats['total'] }}</div>
            </div>
            <div class="stat">
                <div class="stat-lbl">Published</div>
                <div class="stat-val" style="color:var(--mint);">{{ $stats['published'] }}</div>
            </div>
            <div class="stat">
                <div class="stat-lbl">Total Downloads</div>
                <div class="stat-val" style="color:var(--trust-blue);">{{ number_format($stats['download_count']) }}</div>
            </div>
        </div>
        <style>.stats-g{grid-template-columns:1fr!important;}@media(min-width:500px){.stats-g{grid-template-columns:repeat(3,1fr)!important;}}</style>

        {{-- Resources table --}}
        <div class="card">
            <div style="padding:16px 20px;border-bottom:1px solid var(--hair);">
                <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;">My Uploaded Resources</h3>
            </div>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Resource</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Grade</th>
                            <th>Downloads</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $typeIcons = ['article'=>'📄','pdf'=>'📕','video'=>'🎥','notes'=>'📝','past_paper'=>'📋'];
                            $typeClasses = ['article'=>'bdg-blue','pdf'=>'bdg-red','video'=>'bdg-purple','notes'=>'bdg-amber','past_paper'=>'bdg-green'];
                            $typeLabels = ['article'=>'Article','pdf'=>'PDF','video'=>'Video','notes'=>'Notes','past_paper'=>'Past Paper'];
                        @endphp
                        @forelse($resources as $resource)
                        <tr>
                            <td style="max-width:260px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:36px;height:36px;border-radius:9px;background:rgba(10,100,188,.08);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
                                        {{ $typeIcons[$resource->resource_type] ?? '📄' }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;font-size:13.5px;">{{ Str::limit($resource->title, 45) }}</div>
                                        <div style="font-size:12px;color:var(--muted);">{{ $resource->created_at->format('M j, Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="bdg {{ $typeClasses[$resource->resource_type] ?? 'bdg-gray' }}">{{ $typeLabels[$resource->resource_type] ?? ucfirst($resource->resource_type) }}</span>
                            </td>
                            <td style="font-size:13px;color:var(--muted);">{{ $resource->category?->name ?? '—' }}</td>
                            <td style="font-size:13px;color:var(--muted);">{{ $resource->grade_level ?? '—' }}</td>
                            <td style="font-weight:600;">{{ number_format($resource->download_count) }}</td>
                            <td>
                                @if(!$resource->is_approved)
                                    <span class="bdg bdg-amber">Pending</span>
                                @elseif($resource->is_published)
                                    <span class="bdg bdg-green">Live</span>
                                @else
                                    <span class="bdg bdg-gray">Draft</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    @if($resource->is_published && $resource->is_approved)
                                    <a href="{{ route('library.show', $resource->slug) }}" target="_blank" class="btn btn-o btn-sm">View</a>
                                    @endif
                                    <form method="POST" action="{{ route('tutor.library.destroy', $resource) }}" onsubmit="return confirm('Delete this resource?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm" style="background:#FEF2F2;color:#991B1B;border:none;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:60px;">
                                <div style="font-size:40px;margin-bottom:10px;">📚</div>
                                <p style="font-size:15px;font-weight:600;color:var(--ink);margin-bottom:6px;">No resources yet</p>
                                <p style="font-size:13px;color:var(--muted);margin-bottom:18px;">Share study materials, past papers, or notes with students.</p>
                                <a href="{{ route('tutor.library.create') }}" class="btn btn-v">Upload Your First Resource</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($resources->hasPages())
            <div style="padding:16px 20px;border-top:1px solid var(--hair);">
                {{ $resources->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
</div>
</body>
</html>
