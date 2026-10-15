@extends('layouts.app')
@section('title', 'Courses')

@section('content')
<div class="section-container py-12">
    <div class="mb-8">
        <h1 class="font-serif text-4xl font-bold text-ink mb-2">Browse Courses</h1>
        <p class="text-muted">Pre-recorded lessons from our verified tutors</p>
    </div>
    @if($courses->isEmpty())
    <div class="card p-16 text-center">
        <div class="text-5xl mb-4">📚</div>
        <p class="font-serif text-xl font-semibold text-ink">Coming soon</p>
        <p class="text-muted mt-2">Our tutors are preparing course content. Check back soon!</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($courses as $course)
        <a href="{{ route('courses.show', $course->slug) }}" class="card-hover block overflow-hidden">
            @if($course->thumbnail)
            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-44 object-cover">
            @else
            <div class="w-full h-44 bg-violet/10 flex items-center justify-center">
                <span class="text-5xl">📖</span>
            </div>
            @endif
            <div class="p-5">
                @if($course->category)
                <span class="badge-violet text-[11px]">{{ $course->category->name }}</span>
                @endif
                <h3 class="font-serif font-semibold text-ink mt-2 line-clamp-2">{{ $course->title }}</h3>
                <p class="text-xs text-muted mt-1">by {{ $course->tutor->name }}</p>
                <div class="flex items-center justify-between mt-3">
                    <span class="text-xs text-muted">{{ $course->total_lessons }} lessons</span>
                    <span class="font-bold text-ink">{{ $course->is_free ? 'Free' : 'KSh ' . number_format($course->price, 0) }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    <div class="mt-8">{{ $courses->links() }}</div>
    @endif
</div>
@endsection
