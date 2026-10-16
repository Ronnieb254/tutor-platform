@extends('layouts.app')

@section('title', 'Session History')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Session History</h1>
        <p class="mt-2 text-gray-600">Your past sessions and ratings</p>
    </div>

    <!-- History List -->
    <div class="space-y-4">
        @forelse($sessions as $session)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            @if(Auth::user()->isTutor())
                                <img src="{{ $session->student->avatar_url }}" alt="{{ $session->student->name }}" 
                                    class="h-14 w-14 rounded-full">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $session->subject->name }}</h3>
                                    <p class="text-gray-600">Student: <strong>{{ $session->student->name }}</strong></p>
                            @else
                                <img src="{{ $session->tutor->avatar_url }}" alt="{{ $session->tutor->name }}" 
                                    class="h-14 w-14 rounded-full">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $session->subject->name }}</h3>
                                    <p class="text-gray-600">Tutor: <strong>{{ $session->tutor->name }}</strong></p>
                            @endif
                                    <p class="text-sm text-gray-500">{{ $session->scheduled_at->format('M d, Y - g:i A') }}</p>
                                    <p class="text-sm text-gray-500">Duration: {{ $session->duration_minutes }} minutes</p>
                                </div>
                        </div>
                        <div class="flex flex-col items-end space-y-2">
                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                @if($session->status === 'completed') bg-green-100 text-green-800
                                @elseif($session->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($session->status) }}
                            </span>
                            @if($session->status === 'completed')
                                <span class="text-sm font-medium text-yellow-600 flex items-center">
                                    ⭐ 
                                    @if($session->review)
                                        {{ $session->review->rating }}/5
                                    @else
                                        Not rated
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        @if($session->status === 'completed' && !$session->review)
                            <button onclick="showSuccess('Coming Soon', 'Review functionality coming soon.')"
                                class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700">
                                Leave Review
                            </button>
                        @elseif($session->review)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-gray-900">{{ $session->review->reviewer->name }}</span>
                                    <span class="text-yellow-600 font-semibold">⭐ {{ $session->review->rating }}/5</span>
                                </div>
                                <p class="text-sm text-gray-700">{{ $session->review->comment }}</p>
                            </div>
                        @elseif($session->status === 'cancelled')
                            @if($session->cancellation_reason)
                                <div class="bg-red-50 p-4 rounded-lg">
                                    <p class="text-sm text-red-700"><strong>Cancellation Reason:</strong> {{ $session->cancellation_reason }}</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No session history</h3>
                <p class="mt-2 text-gray-500">Your completed and cancelled sessions will appear here.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($sessions->hasPages())
        <div class="mt-8">
            {{ $sessions->links() }}
        </div>
    @endif
</div>

<script src="{{ asset('js/sweetalert-config.js') }}"></script>
@endsection
