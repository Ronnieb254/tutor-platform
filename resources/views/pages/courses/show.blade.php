<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>{{ $course->title }} – Mwalimu</title>
<meta name="description" content="{{ Str::limit($course->description ?? $course->title, 160) }}">
</head>
<body>
@include('partials.nav')
<div class="w" style="padding-top:32px;padding-bottom:64px;">
    <a href="{{ route('courses.index') }}" style="font-size:13px;color:var(--muted);display:inline-flex;align-items:center;gap:5px;margin-bottom:20px;transition:color .15s;" onmouseover="this.style.color='var(--violet)'" onmouseout="this.style.color='var(--muted)'">
        <i class="fa-solid fa-arrow-left fa-xs"></i> All Courses
    </a>
    <div style="display:grid;grid-template-columns:1fr;gap:24px;" class="cshow-g">
        <div>
            @if($course->thumbnail)
            <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" style="width:100%;border-radius:18px;margin-bottom:24px;max-height:380px;object-fit:cover;">
            @endif
            <div class="card card-p">
                <h1 style="font-size:clamp(22px,4vw,34px);color:var(--ink);margin-bottom:10px;">{{ $course->title }}</h1>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap;">
                    @if($course->category)<span class="badge badge-v">{{ $course->category->name }}</span>@endif
                    <span style="font-size:12px;color:var(--muted);display:flex;align-items:center;gap:5px;">
                        <img src="{{ $course->tutor->avatar_url }}" style="width:20px;height:20px;border-radius:999px;object-fit:cover;">
                        {{ $course->tutor->name }}
                    </span>
                    <span style="font-size:12px;color:var(--muted);"><i class="fa-solid fa-play fa-xs"></i> {{ $course->total_lessons }} lessons</span>
                </div>
                @if($course->description)
                <p style="font-size:15px;color:var(--muted);line-height:1.8;">{{ $course->description }}</p>
                @endif
            </div>

            @if($course->lessons->isNotEmpty())
            <div class="card" style="margin-top:20px;overflow:hidden;">
                <div style="padding:18px 22px;border-bottom:1px solid var(--hair);">
                    <h3 style="font-family:'Fraunces',serif;font-size:18px;color:var(--ink);"><i class="fa-solid fa-list-check fa-sm" style="color:var(--violet);margin-right:8px;"></i>Course Lessons</h3>
                </div>
                @foreach($course->lessons as $lesson)
                <div style="display:flex;align-items:center;gap:14px;padding:14px 22px;border-bottom:1px solid rgba(21,40,31,.05);">
                    <div style="width:32px;height:32px;border-radius:9px;background:{{ $enrolled || $lesson->is_free_preview ? 'var(--violet)' : '#F4F6F4' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa-solid {{ $enrolled || $lesson->is_free_preview ? 'fa-play' : 'fa-lock' }}" style="color:{{ $enrolled || $lesson->is_free_preview ? '#fff' : 'var(--muted)' }};font-size:11px;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:14px;font-weight:500;color:var(--ink);">{{ $lesson->title }}</div>
                        @if($lesson->is_free_preview)<span class="badge badge-m" style="font-size:10px;margin-top:3px;">Free preview</span>@endif
                    </div>
                    <span style="font-size:12px;color:var(--muted);">{{ $lesson->formattedDuration() }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div>
            <div class="card card-p" style="position:sticky;top:88px;">
                <div style="text-align:center;margin-bottom:20px;">
                    <div style="font-family:'Fraunces',serif;font-size:36px;font-weight:800;color:{{ $course->is_free?'var(--mint)':'var(--violet)' }};">
                        {{ $course->is_free ? 'Free' : 'KSh '.number_format($course->price) }}
                    </div>
                </div>
                @if($enrolled)
                <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);border-radius:14px;padding:14px;text-align:center;margin-bottom:14px;">
                    <i class="fa-solid fa-circle-check" style="color:var(--mint);font-size:20px;margin-bottom:6px;display:block;"></i>
                    <span style="font-weight:700;color:var(--mint);">You're enrolled!</span>
                </div>
                @else
                @auth
                <form method="POST" action="{{ route('courses.enroll', $course->slug) }}" style="margin-bottom:12px;">
                    @csrf
                    <button type="submit" class="btn btn-v" style="width:100%;justify-content:center;padding:14px;font-size:15px;">
                        <i class="fa-solid fa-{{ $course->is_free ? 'play-circle' : 'credit-card' }}"></i>
                        {{ $course->is_free ? 'Enrol Free' : 'Enrol – KSh '.number_format($course->price) }}
                    </button>
                </form>
                @else
                <a href="{{ route('register') }}" class="btn btn-v" style="width:100%;justify-content:center;padding:14px;font-size:15px;margin-bottom:12px;display:flex;">
                    <i class="fa-solid fa-user-plus"></i> Sign Up to Enrol
                </a>
                @endauth
                @endif
                <div style="background:#F9FAF9;border-radius:12px;padding:14px;">
                    @foreach(['Full lifetime access to all lessons','Learn at your own pace','TSC-registered instructor','Certificate upon completion'] as $f)
                    <div style="display:flex;align-items:center;gap:7px;font-size:13px;color:var(--muted);margin-bottom:7px;">
                        <i class="fa-solid fa-circle-check" style="color:var(--mint);font-size:11px;"></i>{{ $f }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<style>.cshow-g{grid-template-columns:1fr!important;}@media(min-width:1024px){.cshow-g{grid-template-columns:1fr 340px!important;}}</style>
@include('partials.footer')
@livewireScripts
</body>
</html>
