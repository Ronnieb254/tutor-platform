@extends('layouts.guest')
@section('title', 'Complete Your Tutor Profile')

@section('content')
<div class="w-full max-w-2xl py-6">
    <div class="text-center mb-8">
        <h1 class="font-serif text-3xl font-bold text-ink mb-2">Set up your tutor profile</h1>
        <p class="text-muted">Complete these steps to go live and start accepting bookings.</p>
    </div>
    @livewire('tutor.tutor-onboarding')
</div>
@endsection
