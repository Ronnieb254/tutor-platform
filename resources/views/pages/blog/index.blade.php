<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Blog – Mwalimu</title>
<meta name="description" content="Study tips, exam strategies, and learning guides from Kenya's top tutors and national examiners.">
<style>
.post-card{background:#fff;border-radius:20px;border:1px solid var(--hair);overflow:hidden;transition:all .2s;display:block;}
.post-card:hover{transform:translateY(-5px);box-shadow:0 14px 36px rgba(21,40,31,.1);}
.post-thumb{width:100%;height:200px;object-fit:cover;}
.post-thumb-placeholder{width:100%;height:200px;display:flex;align-items:center;justify-content:center;font-size:48px;}
</style>
</head>
<body>
@php use App\Models\BlogPost; use App\Models\BlogCategory;
$posts = BlogPost::published()->with(['author','category'])->latest('published_at')->paginate(12);
$cats  = BlogCategory::withCount(['posts' => fn($q) => $q->published()])->get();
$colors = ['#0A64BC','#10B981','#E8522A','#EEBE15','#3B82F6','#EC4899'];
@endphp
@include('partials.nav')

{{-- HERO --}}
<section style="background:var(--ink);padding:52px 0;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-60px;right:-60px;width:240px;height:240px;background:rgba(10,100,188,.15);border-radius:50%;"></div>
    <div class="w" style="position:relative;z-index:1;">
        <div class="badge badge-white" style="margin-bottom:16px;"><i class="fa-solid fa-newspaper"></i> Learning Resources</div>
        <h1 style="font-size:clamp(30px,5vw,50px);color:#fff;margin-bottom:12px;">Study tips & exam strategies</h1>
        <p style="font-size:16px;color:rgba(255,255,255,.6);max-width:480px;">From Kenya's top tutors and national examiners — practical guides to help you score higher.</p>
    </div>
</section>

<div class="w" style="padding-top:48px;padding-bottom:64px;">
    <div style="display:grid;grid-template-columns:1fr;gap:28px;" class="blog-layout">

        {{-- POSTS GRID --}}
        <div>
            @if($posts->isEmpty())
            <div class="card" style="padding:64px;text-align:center;">
                <i class="fa-solid fa-pen-nib" style="font-size:48px;color:var(--hair);margin-bottom:16px;display:block;"></i>
                <h3 style="font-size:20px;color:var(--ink);margin-bottom:8px;">Coming soon</h3>
                <p style="color:var(--muted);">Study guides and exam tips are on their way. Check back soon!</p>
            </div>
            @else
            <div style="display:grid;grid-template-columns:1fr;gap:20px;" class="posts-g">
                @foreach($posts as $post)
                <a href="{{ route('blog.show', $post->slug) }}" class="post-card">
                    @if($post->thumbnail)
                    <img src="{{ asset('storage/'.$post->thumbnail) }}" alt="{{ $post->title }}" class="post-thumb">
                    @else
                    <div class="post-thumb-placeholder" style="background:{{ $colors[$loop->index%count($colors)] }}22;">
                        {{ ['📐','🔬','📚','✏️','🎯','📝'][$loop->index%6] }}
                    </div>
                    @endif
                    <div style="padding:20px;">
                        @if($post->category)
                        <span class="badge badge-v" style="margin-bottom:10px;">{{ $post->category->name }}</span>
                        @endif
                        <h2 style="font-size:18px;color:var(--ink);margin-bottom:8px;line-height:1.3;">{{ $post->title }}</h2>
                        @if($post->excerpt)
                        <p style="font-size:13px;color:var(--muted);line-height:1.65;margin-bottom:14px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $post->excerpt }}</p>
                        @endif
                        <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--muted);border-top:1px solid var(--hair);padding-top:12px;">
                            <img src="{{ $post->author->avatar_url }}" style="width:24px;height:24px;border-radius:999px;object-fit:cover;">
                            <span>{{ $post->author->name }}</span>
                            <span>·</span>
                            <span><i class="fa-regular fa-calendar fa-xs"></i> {{ $post->published_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @if($posts->hasPages())
            <div style="margin-top:28px;display:flex;justify-content:center;">{{ $posts->links() }}</div>
            @endif
            @endif
        </div>

        {{-- SIDEBAR --}}
        <aside>
            <div class="card card-p" style="margin-bottom:16px;">
                <h3 style="font-size:16px;font-family:'Fraunces',serif;color:var(--ink);margin-bottom:14px;"><i class="fa-solid fa-layer-group fa-sm" style="color:var(--violet);margin-right:6px;"></i>Categories</h3>
                @forelse($cats as $cat)
                <a href="{{ route('blog.index') }}?category={{ $cat->id }}" style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--hair);font-size:14px;color:var(--ink);transition:color .15s;" onmouseover="this.style.color='var(--violet)'" onmouseout="this.style.color='var(--ink)'">
                    <span>{{ $cat->name }}</span>
                    <span style="background:#F4F6F4;color:var(--muted);padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;">{{ $cat->posts_count }}</span>
                </a>
                @empty
                <p style="font-size:13px;color:var(--muted);">No categories yet.</p>
                @endforelse
            </div>

            <div class="card card-p" style="background:var(--violet);border-color:var(--violet);">
                <h3 style="font-size:16px;font-family:'Fraunces',serif;color:#fff;margin-bottom:8px;"><i class="fa-solid fa-star" style="color:var(--gold);margin-right:6px;"></i>Examiner's Clinic</h3>
                <p style="font-size:13px;color:rgba(255,255,255,.75);margin-bottom:14px;line-height:1.65;">Learn directly from the national examiners who set and mark KCSE papers. Only KSh 500 per session.</p>
                <a href="{{ route('examiner-clinic') }}" class="btn btn-gold btn-sm" style="width:100%;justify-content:center;"><i class="fa-solid fa-calendar-check"></i> View Clinics</a>
            </div>
        </aside>
    </div>
</div>
<style>.blog-layout{grid-template-columns:1fr!important;}@media(min-width:1024px){.blog-layout{grid-template-columns:1fr 300px!important;}}.posts-g{grid-template-columns:1fr!important;}@media(min-width:640px){.posts-g{grid-template-columns:repeat(2,1fr)!important;}}@media(min-width:1024px){.posts-g{grid-template-columns:repeat(2,1fr)!important;}}</style>
@include('partials.footer')
@livewireScripts
</body>
</html>
