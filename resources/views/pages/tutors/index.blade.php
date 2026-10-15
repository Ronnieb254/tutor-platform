@extends('layouts.app')
@section('title', 'Find a Tutor')
@section('description', 'Browse verified tutors across Kenya. Filter by subject, price, and availability.')

@section('content')
<div class="section-container py-12">
    <div class="mb-8">
        <h1 class="font-serif text-4xl font-bold text-ink mb-2">Find your perfect tutor</h1>
        <p class="text-muted">{{ number_format(\App\Models\TutorProfile::approved()->count()) }} verified tutors ready to teach</p>
    </div>
    @livewire('tutors.tutor-search')
</div>
@endsection
