<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>{{ $post->title }} – Mwalimu Blog</title>
<meta name="description" content="{{ $post->excerpt ?? Str::limit(strip_tags($post->content), 160) }}">
<style>
.prose{font-size:16px;line-height:1.85;color:var(--ink);}
.prose h2{font-family:'Fraunces',serif;font-size:24px;margin:28px 0 12px;}
.prose h3{font-family:'Fraunces',serif;font-size:20px;margin:22px 0 10px;}
.prose p{margin-bottom:18px;}
.prose ul,.prose ol{margin:0 0 18px 24px;}
.prose li{margin-bottom:8px;}
.prose strong{font-weight:700;}
.prose blockquote{border-left:4px solid var(--trust-blue);padding:12px 20px;background:rgba(10,100,188,.05);border-radius:0 12px 12px 0;margin:20px 0;font-style:italic;color:var(--muted);}
</style>
</head>
<body>
@include('partials.nav')

<div class="w" style="padding-top:36px;padding-bottom:64px;">
    <div style="display:grid;grid-template-columns:1fr;gap:28px;" class="blog-show-g">
        <article>
            <div style="margin-bottom:20px;">
                <a href="{{ route('blog.index') }}" style="font-size:13px;color:var(--muted);display:inline-flex;align-items:center;gap:5px;margin-bottom:16px;transition:color .15s;" onmouseover="this.style.color='var(--trust-blue)'" onmouseout="this.style.color='var(--muted)'">
                    <i class="fa-solid fa-arrow-left fa-xs"></i> Back to Blog
                </a>
                @if($post->category)
                <span class="badge badge-v" style="display:block;width:fit-content;margin-bottom:14px;">{{ $post->category->name }}</span>
                @endif
                <h1 style="font-size:clamp(28px,5vw,44px);color:var(--ink);margin-bottom:16px;line-height:1.15;">{{ $post->title }}</h1>
                <div style="display:flex;align-items:center;gap:14px;padding:16px 0;border-top:1px solid var(--hair);border-bottom:1px solid var(--hair);flex-wrap:wrap;">
                    <img src="{{ $post->author->avatar_url }}" style="width:40px;height:40px;border-radius:12px;object-fit:cover;">
                    <div>
                        <div style="font-weight:700;font-size:14px;color:var(--ink);">{{ $post->author->name }}</div>
                        <div style="font-size:12px;color:var(--muted);">{{ $post->published_at->format('D, M j, Y') }}</div>
                    </div>
                </div>
            </div>
            @if($post->thumbnail)
            <img src="{{ asset('storage/'.$post->thumbnail) }}" alt="{{ $post->title }}" style="width:100%;border-radius:18px;margin-bottom:28px;max-height:420px;object-fit:cover;">
            @endif
            <div class="prose">{!! nl2br(e($post->content)) !!}</div>

            {{-- Tags / Share --}}
            <div style="margin-top:32px;padding-top:24px;border-top:1px solid var(--hair);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div></div>
                <div style="display:flex;gap:8px;">
                    <span style="font-size:13px;color:var(--muted);">Share:</span>
                    <button onclick="navigator.share?navigator.share({title:'{{ addslashes($post->title) }}',url:window.location.href}):navigator.clipboard.writeText(window.location.href)" class="btn btn-sm btn-o">
                        <i class="fa-solid fa-share-nodes"></i> Share
                    </button>
                </div>
            </div>
        </article>

        <aside>
            @if($related->isNotEmpty())
            <div class="card card-p" style="margin-bottom:16px;">
                <h3 style="font-family:'Fraunces',serif;font-size:17px;color:var(--ink);margin-bottom:16px;"><i class="fa-solid fa-book-open fa-sm" style="color:var(--trust-blue);margin-right:6px;"></i>Related Articles</h3>
                @foreach($related as $r)
                <a href="{{ route('blog.show', $r->slug) }}" style="display:flex;gap:12px;padding:12px 0;border-bottom:1px solid var(--hair);transition:opacity .15s;" onmouseover="this.style.opacity='.75'" onmouseout="this.style.opacity='1'">
                    <div style="width:60px;height:50px;border-radius:10px;background:rgba(10,100,188,.1);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">📝</div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:var(--ink);line-height:1.4;">{{ Str::limit($r->title,55) }}</div>
                        <div style="font-size:11px;color:var(--muted);margin-top:3px;">{{ $r->published_at->format('M j') }}</div>
                    </div>
                </a>
                @endforeach
            </div>
            @endif

            @if(!auth()->check() || auth()->user()->role !== 'tutor')
                <div class="card card-p" style="background:var(--ink);border-color:var(--ink);">
                <h3 style="font-family:'Fraunces',serif;font-size:16px;color:#fff;margin-bottom:8px;">Need 1-on-1 help?</h3>
                <p style="font-size:13px;color:rgba(255,255,255,.65);line-height:1.65;margin-bottom:14px;">Book a session with a TSC-registered tutor from KSh 300.</p>
                <a href="{{ route('tutors.index') }}" class="btn btn-v btn-sm" style="width:100%;justify-content:center;"><i class="fa-solid fa-magnifying-glass"></i> Find a Tutor</a>
            </div>
            @endif
        </aside>
    </div>
</div>
<style>.blog-show-g{grid-template-columns:1fr!important;}@media(min-width:1024px){.blog-show-g{grid-template-columns:1fr 300px!important;}}</style>
@include('partials.footer')
@livewireScripts
</body>
</html>
