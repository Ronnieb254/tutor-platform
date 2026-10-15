@extends('layouts.app')
@section('title', 'Tutor Dashboard')

@section('content')
<div class="section-container py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-serif text-3xl font-bold text-ink">Dashboard</h1>
            <p class="text-muted mt-1">Manage your bookings, earnings, and profile.</p>
        </div>
    </div>
    @livewire('tutor.tutor-dashboard')
</div>
@endsection
