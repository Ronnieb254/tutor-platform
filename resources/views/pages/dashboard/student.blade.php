@extends('layouts.app')
@section('title', 'My Dashboard')

@section('content')
<div class="section-container py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-serif text-3xl font-bold text-ink">Hello, {{ auth()->user()->name }} 👋</h1>
            <p class="text-muted mt-1">Manage your sessions and track your learning progress.</p>
        </div>
        <a href="{{ route('tutors.index') }}" class="pill-btn-primary">Find a Tutor</a>
    </div>
    @livewire('student.student-dashboard')
</div>
@endsection
