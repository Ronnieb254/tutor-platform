<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Library – Mwalimu</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>
:root{--ink:#15281F;--cream:#FBF5E6;--trust-blue:#0A64BC;--coral:#E8522A;--mint:#10B981;--gold:#EEBE15;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'DM Sans',sans-serif;color:var(--ink);background:#F2F5F2;}
a{color:inherit;text-decoration:none;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:10px;font-size:14px;font-weight:600;border:none;cursor:pointer;transition:all .15s;font-family:inherit;text-decoration:none;}
.btn-v{background:var(--trust-blue);color:#fff;}.btn-v:hover{background:#0850a0;}
.btn-o{background:#fff;color:var(--ink);border:1.5px solid var(--hair);}.btn-o:hover{border-color:var(--trust-blue);color:var(--trust-blue);}
.btn-sm{padding:6px 14px;font-size:12px;border-radius:8px;}
.bdg{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;}
.bdg-blue{background:#DBEAFE;color:#1E40AF;}
.bdg-green{background:#DCFCE7;color:#166534;}
.bdg-amber{background:#FEF9C3;color:#854D0E;}
.bdg-purple{background:#EDE9FE;color:#5B21B6;}
.bdg-pink{background:#FCE7F3;color:#9D174D;}
.bdg-gray{background:#F3F4F6;color:#6B7280;}

/* Hero */
.hero{background:linear-gradient(135deg,var(--ink) 0%,#1a3a2a 100%);padding:80px 24px;text-align:center;position:relative;overflow:hidden;}
.hero::before{content:'';position:absolute;top:0;left:0;right:0;bottom:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");}
.hero-inner{position:relative;max-width:640px;margin:0 auto;}
.hero h1{font-family:'Fraunces',serif;font-size:48px;font-weight:800;color:#fff;margin-bottom:16px;line-height:1.1;}
.hero p{font-size:18px;color:rgba(255,255,255,.7);margin-bottom:32px;line-height:1.6;}
.hero-search{display:flex;max-width:480px;margin:0 auto;gap:0;}
.hero-search input{flex:1;height:52px;padding:0 20px;border-radius:12px 0 0 12px;border:none;font-size:15px;font-family:inherit;outline:none;}
.hero-search button{height:52px;padding:0 24px;border-radius:0 12px 12px 0;border:none;background:var(--trust-blue);color:#fff;font-weight:600;font-size:14px;cursor:pointer;}
.hero-search button:hover{background:#0850a0;}

/* Layout */
.container{max-width:1200px;margin:0 auto;padding:40px 24px;}
.layout{display:grid;grid-template-columns:260px 1fr;gap:28px;align-items:start;}
@media(max-width:768px){.layout{grid-template-columns:1fr;}}

/* Sidebar */
.sidebar{background:#fff;border-radius:16px;border:1px solid var(--hair);padding:20px;position:sticky;top:20px;}
.filter-title{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:10px;}
.filter-item{display:flex;align-items:center;justify-content:space-between;padding:8px 12px;border-radius:8px;cursor:pointer;font-size:14px;font-weight:500;color:var(--ink);transition:all .15s;margin-bottom:2px;text-decoration:none;}
.filter-item:hover,.filter-item.active{background:rgba(10,100,188,.08);color:var(--trust-blue);}
.filter-item .count{font-size:11px;font-weight:700;color:var(--muted);}
.type-icon{width:28px;height:28px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;}
.divider{height:1px;background:var(--hair);margin:14px 0;}

/* Cards */
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;}
.resource-card{background:#fff;border-radius:16px;border:1px solid var(--hair);overflow:hidden;transition:all .2s;display:flex;flex-direction:column;}
.resource-card:hover{box-shadow:0 8px 32px rgba(21,40,31,.1);transform:translateY(-3px);border-color:rgba(10,100,188,.2);}
.card-thumb{height:160px;background:linear-gradient(135deg,#0A64BC 0%,#1a3a8a 100%);display:flex;align-items:center;justify-content:center;font-size:48px;position:relative;}
.card-thumb img{width:100%;height:100%;object-fit:cover;}
.card-type-badge{position:absolute;top:12px;left:12px;}
.card-body{padding:16px;flex:1;display:flex;flex-direction:column;}
.card-category{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--trust-blue);margin-bottom:6px;}
.card-title{font-family:'Fraunces',serif;font-size:16px;font-weight:700;color:var(--ink);margin-bottom:6px;line-height:1.4;}
.card-excerpt{font-size:13px;color:var(--muted);line-height:1.5;margin-bottom:12px;flex:1;}
.card-meta{display:flex;align-items:center;justify-content:space-between;font-size:12px;color:var(--muted);border-top:1px solid var(--hair);padding-top:10px;margin-top:auto;}
.card-author{display:flex;align-items:center;gap:6px;}
.card-avatar{width:22px;height:22px;border-radius:6px;background:var(--trust-blue);display:flex;align-items:center;justify-content:center;color:#fff;font-size:9px;font-weight:700;}

/* Toolbar */
.toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;}
.sort-select{height:38px;padding:0 12px;border:1px solid var(--hair);border-radius:9px;font-size:13px;font-family:inherit;color:var(--ink);background:#fff;cursor:pointer;outline:none;}

/* Empty */
.empty{text-align:center;padding:80px 24px;}
.empty-icon{font-size:64px;margin-bottom:16px;}
.empty h3{font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);margin-bottom:8px;}
.empty p{font-size:14px;color:var(--muted);}

/* Type helpers */
/* .type-pdf{background:#FEF2F2;color:#DC2626;} */
.type-video{background:#EDE9FE;color:#7C3AED;}
.type-notes{background:#FEF9C3;color:#D97706;}
.type-past_paper{background:#DCFCE7;color:#16A34A;}
.type-article{background:#DBEAFE;color:#2563EB;}

/* Pagination */
.pagination{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:32px;}
.pagination a,.pagination span{width:38px;height:38px;display:flex;align-items:center;justify-content:center;border-radius:10px;font-size:14px;font-weight:600;transition:all .15s;}
.pagination a{background:#fff;border:1px solid var(--hair);color:var(--ink);}.pagination a:hover{border-color:var(--trust-blue);color:var(--trust-blue);}
.pagination .active{background:var(--trust-blue);color:#fff;border:1px solid var(--trust-blue);}
</style>
</head>
<body>
@include('partials.nav')

{{-- Hero --}}
<section class="hero">
    <div class="hero-inner">
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);border-radius:999px;padding:6px 14px;margin-bottom:20px;">
            <span style="font-size:16px;">📚</span>
            <span style="font-size:13px;font-weight:600;color:rgba(255,255,255,.8);">Learning Resource Hub</span>
        </div>
        <h1>The Mwalimu Library</h1>
        <p>Curated past papers, and study notes in PDFs, and top rated video lessons — all in one place.</p>
        <form method="GET" action="{{ route('library.index') }}" class="hero-search">
            <input type="text" name="search" placeholder="Search resources, subjects, topics…" value="{{ request('search') }}">
            <button type="submit">Search</button>
        </form>
    </div>
</section>

<div class="container">
    <div class="layout">

        {{-- Sidebar filters --}}
        <aside class="sidebar">
            <div class="filter-title">Resource Type</div>
            <a href="{{ route('library.index', request()->except('type')) }}" class="filter-item {{ !request('type') ? 'active' : '' }}">
                <span>All Resources</span>
                <span class="count">{{ $resources->total() }}</span>
            </a>
            @php
                $typeIcons = ['article'=>'📄','video'=>'🎥','notes'=>'📝','past_paper'=>'📋'];
                $typeLabels = ['article'=>'Articles','video'=>'Videos','notes'=>'Notes','past_paper'=>'Past Papers'];
            @endphp
            @foreach($resourceTypes as $type)
            <a href="{{ route('library.index', array_merge(request()->all(), ['type'=>$type])) }}" class="filter-item {{ request('type') === $type ? 'active' : '' }}">
                <span style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:15px;">{{ $typeIcons[$type] ?? '📄' }}</span>
                    {{ $typeLabels[$type] ?? ucfirst(str_replace('_',' ',$type)) }}
                </span>
            </a>
            @endforeach

            <div class="divider"></div>

            <div class="filter-title">Category</div>
            <a href="{{ route('library.index', request()->except('category')) }}" class="filter-item {{ !request('category') ? 'active' : '' }}">
                All Categories
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('library.index', array_merge(request()->all(), ['category'=>$cat->id])) }}" class="filter-item {{ request('category') == $cat->id ? 'active' : '' }}">
                <span>{{ $cat->icon ? $cat->icon.' ' : '' }}{{ $cat->name }}</span>
                <span class="count">{{ $cat->posts_count }}</span>
            </a>
            @endforeach
           
        </aside>

        {{-- Main content --}}
        <div>
            <div class="toolbar">
                <div style="font-size:14px;color:var(--muted);">
                    <strong style="color:var(--ink);">{{ $resources->total() }}</strong> resource{{ $resources->total() != 1 ? 's' : '' }} found
                    @if(request()->hasAny(['search','type','category','grade']))
                        — <a href="{{ route('library.index') }}" style="color:var(--trust-blue);font-weight:600;">Clear filters</a>
                    @endif
                </div>
                @auth
                <a href="{{ route('tutors.index') }}" class="btn btn-v btn-sm">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Book a Tutor
                </a>
                @endauth
            </div>

            @forelse($resources as $resource)
                @if($loop->first)<div class="grid">@endif

                <a href="{{ route('library.show', $resource->slug) }}" class="resource-card">
                    <div class="card-thumb">
                        @if($resource->thumbnail)
                            <img src="{{ asset('storage/'.$resource->thumbnail) }}" alt="{{ $resource->title }}">
                        @else
                            @php $icons = ['article'=>'📄','video'=>'🎥','notes'=>'📝','past_paper'=>'📋']; @endphp
                            {{ $icons[$resource->resource_type] ?? '📄' }}
                        @endif
                        @php
                            $typeClasses = ['article'=>'bdg-blue','video'=>'bdg-purple','notes'=>'bdg-amber','past_paper'=>'bdg-green'];
                            $typeLabelMap = ['article'=>'Article','video'=>'Video','notes'=>'Notes','past_paper'=>'Past Paper'];
                        @endphp
                        <span class="card-type-badge bdg {{ $typeClasses[$resource->resource_type] ?? 'bdg-gray' }}">
                            {{ $typeLabelMap[$resource->resource_type] ?? ucfirst($resource->resource_type) }}
                        </span>
                    </div>
                    <div class="card-body">
                        @if($resource->category)
                        <div class="card-category">{{ $resource->category->name }}</div>
                        @endif
                        <div class="card-title">{{ $resource->title }}</div>
                        @if($resource->excerpt)
                        <div class="card-excerpt">{{ Str::limit($resource->excerpt, 90) }}</div>
                        @endif
                        <div class="card-meta">
                            <div class="card-author">
                                <div class="card-avatar">{{ substr($resource->author->name, 0, 1) }}</div>
                                <span>{{ $resource->author->name }}</span>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;">
                                @if($resource->grade_level)
                                <span class="bdg bdg-gray" style="font-size:10px;">{{ $resource->grade_level }}</span>
                                @endif
                                @if($resource->download_count > 0)
                                <span style="display:flex;align-items:center;gap:3px;">
                                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    {{ $resource->download_count }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>

                @if($loop->last)</div>@endif
            @empty
                <div class="empty">
                    <div class="empty-icon">📚</div>
                    <h3>No resources found</h3>
                    <p>Try adjusting your filters or search terms.</p>
                    @if(request()->hasAny(['search','type','category','grade']))
                    <a href="{{ route('library.index') }}" class="btn btn-o" style="margin-top:20px;">Clear Filters</a>
                    @endif
                </div>
            @endforelse

            {{-- Pagination --}}
            @if($resources->hasPages())
            <div class="pagination">
                @if($resources->onFirstPage())
                    <span style="opacity:.3;">&larr;</span>
                @else
                    <a href="{{ $resources->previousPageUrl() }}">&larr;</a>
                @endif

                @foreach($resources->getUrlRange(max(1, $resources->currentPage()-2), min($resources->lastPage(), $resources->currentPage()+2)) as $page => $url)
                    @if($page == $resources->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($resources->hasMorePages())
                    <a href="{{ $resources->nextPageUrl() }}">&rarr;</a>
                @else
                    <span style="opacity:.3;">&rarr;</span>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

@include('partials.footer')
</body>
</html>
