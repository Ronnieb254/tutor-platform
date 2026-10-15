@extends('layouts.app')
@section('title', 'Blog')

@section('content')
<div class="section-container py-12">
    <div class="mb-10">
        <h1 class="font-serif text-4xl font-bold text-ink mb-2">Learning Resources</h1>
        <p class="text-muted">Tips, guides, and insights from our tutors</p>
    </div>
    @if($posts->isEmpty())
    <div class="card p-16 text-center">
        <div class="text-5xl mb-4">✍️</div>
        <p class="font-serif text-xl font-semibold text-ink">No posts yet</p>
        <p class="text-muted mt-2">Check back soon for articles and study tips.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $post)
        <a href="{{ route('blog.show', $post->slug) }}" class="card-hover block overflow-hidden group">
            @if($post->thumbnail)
            <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}" class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-300">
            @else
            <div class="w-full h-44 bg-gradient-to-br from-violet/10 to-coral/10 flex items-center justify-center">
                <span class="text-4xl">📝</span>
            </div>
            @endif
            <div class="p-5">
                @if($post->category)
                <span class="badge-coral text-[11px]">{{ $post->category->name }}</span>
                @endif
                <h3 class="font-serif font-semibold text-ink mt-2 line-clamp-2 group-hover:text-violet transition">{{ $post->title }}</h3>
                @if($post->excerpt)
                <p class="text-xs text-muted mt-2 line-clamp-2">{{ $post->excerpt }}</p>
                @endif
                <div class="flex items-center gap-2 mt-3 text-xs text-muted">
                    <img src="{{ $post->author->avatar_url }}" class="w-5 h-5 rounded-full object-cover">
                    <span>{{ $post->author->name }}</span>
                    <span>·</span>
                    <span>{{ $post->published_at->format('M j, Y') }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    <div class="mt-8">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
