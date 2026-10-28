<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $resource->meta_title ?? $resource->title }} – Mwalimu Library</title>
@if($resource->meta_description)
<meta name="description" content="{{ $resource->meta_description }}">
@endif
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
.bdg{display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;}
.bdg-blue{background:#DBEAFE;color:#1E40AF;}
.bdg-red{background:#FEE2E2;color:#991B1B;}
.bdg-purple{background:#EDE9FE;color:#5B21B6;}
.bdg-amber{background:#FEF9C3;color:#854D0E;}
.bdg-green{background:#DCFCE7;color:#166534;}
.bdg-gray{background:#F3F4F6;color:#6B7280;}
.container{max-width:1100px;margin:0 auto;padding:40px 24px;}
.layout{display:grid;grid-template-columns:1fr 320px;gap:28px;align-items:start;}
@media(max-width:900px){.layout{grid-template-columns:1fr;}}
.card{background:#fff;border-radius:16px;border:1px solid var(--hair);}
.card-p{padding:24px;}

/* Hero banner */
.resource-hero{background:linear-gradient(135deg,var(--ink) 0%,#1a3a2a 100%);border-radius:16px;padding:40px;margin-bottom:28px;display:flex;align-items:center;gap:28px;}
.resource-hero-icon{width:80px;height:80px;border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:36px;flex-shrink:0;}
.resource-hero h1{font-family:'Fraunces',serif;font-size:28px;font-weight:800;color:#fff;margin-bottom:10px;line-height:1.3;}
.resource-hero-meta{display:flex;flex-wrap:wrap;gap:10px;align-items:center;}
@media(max-width:600px){.resource-hero{flex-direction:column;text-align:center;}.resource-hero-meta{justify-content:center;}}

/* Breadcrumb */
.breadcrumb{display:flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);margin-bottom:24px;}
.breadcrumb a{color:var(--trust-blue);}

/* Content */
.prose{font-size:15px;line-height:1.8;color:var(--ink);}
.prose h2{font-family:'Fraunces',serif;font-size:22px;font-weight:700;margin:28px 0 12px;}
.prose h3{font-family:'Fraunces',serif;font-size:18px;font-weight:700;margin:22px 0 10px;}
.prose p{margin-bottom:16px;}
.prose ul,.prose ol{margin:0 0 16px 20px;}
.prose li{margin-bottom:6px;}
.prose blockquote{border-left:3px solid var(--trust-blue);padding-left:16px;color:var(--muted);margin:20px 0;font-style:italic;}
.prose a{color:var(--trust-blue);text-decoration:underline;}

/* Download card */
.download-card{background:var(--ink);border-radius:16px;padding:24px;color:#fff;margin-bottom:20px;}
.download-card h3{font-family:'Fraunces',serif;font-size:18px;margin-bottom:6px;}
.download-card p{font-size:13px;color:rgba(255,255,255,.6);margin-bottom:20px;}
.download-btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:14px;background:var(--trust-blue);color:#fff;border-radius:12px;font-weight:700;font-size:15px;border:none;cursor:pointer;text-decoration:none;transition:all .2s;}
.download-btn:hover{background:#0850a0;}

/* Meta card */
.meta-list{list-style:none;}
.meta-list li{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--hair);font-size:13.5px;}
.meta-list li:last-child{border-bottom:none;}
.meta-label{color:var(--muted);font-weight:500;}
.meta-value{font-weight:600;color:var(--ink);}

/* Related */
.related-card{display:flex;gap:14px;padding:14px;border-radius:12px;border:1px solid var(--hair);background:#fff;transition:all .2s;margin-bottom:10px;}
.related-card:hover{border-color:rgba(10,100,188,.3);box-shadow:0 4px 12px rgba(21,40,31,.06);}
.related-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;background:rgba(10,100,188,.08);}
.related-title{font-weight:700;font-size:14px;color:var(--ink);line-height:1.4;margin-bottom:4px;}
.related-meta{font-size:12px;color:var(--muted);}
</style>
</head>
<body>
@include('partials.nav')

<div class="container">

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="{{ route('library.index') }}">Library</a>
        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span>{{ Str::limit($resource->title, 40) }}</span>
    </div>

    {{-- Hero banner --}}
    @php
        $typeIcons = ['article'=>'📄','pdf'=>'📕','video'=>'🎥','notes'=>'📝','past_paper'=>'📋'];
        $typeClasses = ['article'=>'bdg-blue','pdf'=>'bdg-red','video'=>'bdg-purple','notes'=>'bdg-amber','past_paper'=>'bdg-green'];
        $typeLabels = ['article'=>'Article','pdf'=>'PDF','video'=>'Video','notes'=>'Notes','past_paper'=>'Past Paper'];
        $bgColors = ['article'=>'rgba(37,99,235,.15)','pdf'=>'rgba(220,38,38,.15)','video'=>'rgba(124,58,237,.15)','notes'=>'rgba(217,119,6,.15)','past_paper'=>'rgba(22,163,74,.15)'];
    @endphp
    <div class="resource-hero">
        <div class="resource-hero-icon" style="background:{{ $bgColors[$resource->resource_type] ?? 'rgba(10,100,188,.15)' }};">
            {{ $typeIcons[$resource->resource_type] ?? '📄' }}
        </div>
        <div>
            <h1>{{ $resource->title }}</h1>
            <div class="resource-hero-meta">
                <span class="bdg {{ $typeClasses[$resource->resource_type] ?? 'bdg-gray' }}">
                    {{ $typeLabels[$resource->resource_type] ?? ucfirst($resource->resource_type) }}
                </span>
                @if($resource->category)
                <span class="bdg bdg-gray">{{ $resource->category->name }}</span>
                @endif
                @if($resource->grade_level)
                <span class="bdg bdg-gray">{{ $resource->grade_level }}</span>
                @endif
                @if($resource->subject)
                <span class="bdg" style="background:rgba(16,185,129,.15);color:#065f46;">{{ $resource->subject->name }}</span>
                @endif
                <span style="font-size:13px;color:rgba(255,255,255,.5);">
                    By {{ $resource->author->name }} · {{ $resource->published_at?->format('M j, Y') }}
                </span>
            </div>
        </div>
    </div>

    <div class="layout">
        {{-- Main content --}}
        <div>
            @if($resource->excerpt)
            <div class="card card-p" style="margin-bottom:20px;background:rgba(10,100,188,.04);border-color:rgba(10,100,188,.15);">
                <p style="font-size:15px;font-style:italic;color:var(--muted);line-height:1.7;">{{ $resource->excerpt }}</p>
            </div>
            @endif

            @if($resource->content)
            <div class="card card-p" style="margin-bottom:20px;">
                <div class="prose">{!! $resource->content !!}</div>
            </div>
            @endif

            @if($resource->resource_type === 'video' && $resource->file_path)
            <div class="card card-p" style="margin-bottom:20px;">
                <h3 style="font-family:'Fraunces',serif;font-size:18px;font-weight:700;margin-bottom:16px;">Watch Video</h3>
                <video controls style="width:100%;border-radius:10px;background:#000;">
                    <source src="{{ asset('storage/'.$resource->file_path) }}">
                    Your browser doesn't support video playback.
                </video>
            </div>
            @endif

            {{-- Related resources --}}
            @if($related->isNotEmpty())
            <div class="card card-p">
                <h3 style="font-family:'Fraunces',serif;font-size:18px;font-weight:700;margin-bottom:16px;">Related Resources</h3>
                @foreach($related as $r)
                <a href="{{ route('library.show', $r->slug) }}" class="related-card" style="display:flex;">
                    <div class="related-icon">{{ $typeIcons[$r->resource_type] ?? '📄' }}</div>
                    <div>
                        <div class="related-title">{{ $r->title }}</div>
                        <div class="related-meta">
                            <span class="bdg {{ $typeClasses[$r->resource_type] ?? 'bdg-gray' }}" style="font-size:10px;padding:2px 8px;">{{ $typeLabels[$r->resource_type] ?? ucfirst($r->resource_type) }}</span>
                            @if($r->grade_level) &nbsp;·&nbsp; {{ $r->grade_level }}@endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <aside>
            {{-- Download / Access --}}
            @if($resource->file_path)
            <div class="download-card">
                <h3>
                    {{ $resource->resource_type === 'video' ? '🎥 Watch Video' : '📥 Download ' . ($typeLabels[$resource->resource_type] ?? 'File') }}
                </h3>
                <p>
                    @if($resource->file_size)
                        File size: {{ number_format($resource->file_size / 1048576, 1) }} MB
                    @endif
                    · {{ $resource->download_count }} download{{ $resource->download_count != 1 ? 's' : '' }}
                </p>
                @auth
                <a href="{{ route('library.download', $resource->slug) }}" class="download-btn">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download Now
                </a>
                @else
                <a href="{{ route('login') }}" class="download-btn">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    Login to Download
                </a>
                @endauth
            </div>
            @endif

            {{-- Resource details --}}
            <div class="card card-p" style="margin-bottom:20px;">
                <h3 style="font-family:'Fraunces',serif;font-size:16px;font-weight:700;margin-bottom:14px;">Resource Details</h3>
                <ul class="meta-list">
                    <li>
                        <span class="meta-label">Type</span>
                        <span class="bdg {{ $typeClasses[$resource->resource_type] ?? 'bdg-gray' }}" style="font-size:11px;">{{ $typeLabels[$resource->resource_type] ?? ucfirst($resource->resource_type) }}</span>
                    </li>
                    @if($resource->category)
                    <li>
                        <span class="meta-label">Category</span>
                        <span class="meta-value">{{ $resource->category->name }}</span>
                    </li>
                    @endif
                    @if($resource->subject)
                    <li>
                        <span class="meta-label">Subject</span>
                        <span class="meta-value">{{ $resource->subject->name }}</span>
                    </li>
                    @endif
                    @if($resource->grade_level)
                    <li>
                        <span class="meta-label">Grade</span>
                        <span class="meta-value">{{ $resource->grade_level }}</span>
                    </li>
                    @endif
                    <li>
                        <span class="meta-label">Posted by</span>
                        <span class="meta-value">{{ $resource->author->name }}</span>
                    </li>
                    <li>
                        <span class="meta-label">Published</span>
                        <span class="meta-value">{{ $resource->published_at?->format('M j, Y') }}</span>
                    </li>
                    <li>
                        <span class="meta-label">Downloads</span>
                        <span class="meta-value">{{ number_format($resource->download_count) }}</span>
                    </li>
                </ul>
            </div>

            {{-- CTA --}}
            <div class="card card-p" style="text-align:center;background:linear-gradient(135deg,rgba(10,100,188,.05) 0%,rgba(10,100,188,.1) 100%);border-color:rgba(10,100,188,.2);">
                <div style="font-size:32px;margin-bottom:10px;">🎓</div>
                <h3 style="font-family:'Fraunces',serif;font-size:16px;font-weight:700;color:var(--ink);margin-bottom:6px;">Need a tutor?</h3>
                <p style="font-size:13px;color:var(--muted);margin-bottom:16px;">Find verified tutors who can help you master this subject.</p>
                <a href="{{ route('tutors.index') }}" class="btn btn-v" style="width:100%;justify-content:center;">
                    Find a Tutor
                </a>
            </div>
        </aside>
    </div>
</div>

@include('partials.footer')
</body>
</html>
