<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Courses – Mwalimu</title>
<meta name="description" content="Browse pre-recorded courses from TSC-registered tutors. Learn at your own pace across the Kenyan curriculum.">
</head>
<body>
@php
use App\Models\Course; use App\Models\Category;
$courses    = Course::published()->with(['tutor','category'])->latest()->paginate(12);
$categories = Category::active()->withCount('subjects')->get();
$colors     = ['#0A64BC','#10B981','#E8522A','#EEBE15','#3B82F6','#EC4899'];
@endphp
@include('partials.nav')

{{-- HERO --}}
<section style="background:var(--ink);padding:52px 0;position:relative;overflow:hidden;">
    <div style="position:absolute;bottom:-60px;right:-40px;width:260px;height:260px;background:rgba(10,100,188,.15);border-radius:50%;"></div>
    <div class="w" style="position:relative;z-index:1;">
        <div class="badge badge-white" style="margin-bottom:16px;"><i class="fa-solid fa-play-circle"></i> Pre-Recorded Courses</div>
        <h1 style="font-size:clamp(30px,5vw,50px);color:#fff;margin-bottom:12px;">Learn at your own pace</h1>
        <p style="font-size:16px;color:rgba(255,255,255,.6);max-width:480px;">Video courses from TSC-registered tutors and national examiners — covering the full Kenyan curriculum.</p>
    </div>
</section>

<div class="w" style="padding-top:48px;padding-bottom:64px;">
    @if($courses->isEmpty())
    <div class="card" style="padding:80px;text-align:center;">
        <i class="fa-solid fa-photo-film" style="font-size:52px;color:var(--hair);margin-bottom:18px;display:block;"></i>
        <h2 style="font-family:'Fraunces',serif;font-size:26px;color:var(--ink);margin-bottom:10px;">Courses coming soon</h2>
        <p style="color:var(--muted);font-size:16px;margin-bottom:24px;max-width:400px;margin-left:auto;margin-right:auto;">Our tutors are preparing high-quality course content. In the meantime, book a live 1-on-1 session.</p>
        <a href="{{ route('tutors.index') }}" class="btn btn-v"><i class="fa-solid fa-magnifying-glass"></i> Find a Tutor Instead</a>
    </div>
    @else
    <div style="display:grid;grid-template-columns:1fr;gap:20px;" class="course-g">
        @foreach($courses as $course)
        <a href="{{ route('courses.show', $course->slug) }}" style="background:#fff;border-radius:20px;border:1px solid var(--hair);overflow:hidden;display:block;transition:all .2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 36px rgba(21,40,31,.1)'" onmouseout="this.style.transform='none';this.style.boxShadow='none'">
            @if($course->thumbnail)
            <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" style="width:100%;height:200px;object-fit:cover;">
            @else
            <div style="width:100%;height:180px;background:{{ $colors[$loop->index%count($colors)] }}22;display:flex;align-items:center;justify-content:center;font-size:52px;">
                {{ ['📐','🔬','📚','✏️','🎯','💡'][$loop->index%6] }}
            </div>
            @endif
            <div style="padding:20px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:8px;flex-wrap:wrap;">
                    @if($course->category)<span class="badge badge-v" style="font-size:10px;">{{ $course->category->name }}</span>@endif
                    <span style="background:rgba(21,40,31,.06);color:var(--muted);padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">{{ $course->level }}</span>
                </div>
                <h3 style="font-family:'Fraunces',serif;font-size:18px;color:var(--ink);margin-bottom:6px;line-height:1.3;">{{ $course->title }}</h3>
                <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--muted);margin-bottom:12px;">
                    <img src="{{ $course->tutor->avatar_url }}" style="width:20px;height:20px;border-radius:999px;object-fit:cover;">
                    <span>{{ $course->tutor->name }}</span>
                    <span>·</span>
                    <span><i class="fa-solid fa-play fa-xs"></i> {{ $course->total_lessons }} lessons</span>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:{{ $course->is_free?'var(--mint)':'var(--trust-blue)' }};">
                        {{ $course->is_free ? 'Free' : 'KSh '.number_format($course->price) }}
                    </span>
                    <span class="btn btn-v btn-sm"><i class="fa-solid fa-arrow-right"></i> View Course</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @if($courses->hasPages())
    <div style="margin-top:28px;display:flex;justify-content:center;">{{ $courses->links() }}</div>
    @endif
    @endif
</div>
<style>.course-g{grid-template-columns:1fr!important;}@media(min-width:640px){.course-g{grid-template-columns:repeat(2,1fr)!important;}}@media(min-width:1024px){.course-g{grid-template-columns:repeat(3,1fr)!important;}}</style>
@include('partials.footer')
@livewireScripts
</body>
</html>
