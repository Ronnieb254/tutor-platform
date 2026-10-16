@extends('layouts.app')

@section('title', 'Sessions')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Sessions</h1>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6 flex space-x-4 border-b border-gray-200">
        <a href="{{ route('sessions.index', ['filter' => 'upcoming']) }}" 
            class="px-4 py-2 border-b-2 text-sm font-medium {{ $filter === 'upcoming' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            Upcoming
        </a>
        <a href="{{ route('sessions.index', ['filter' => 'history']) }}" 
            class="px-4 py-2 border-b-2 text-sm font-medium {{ $filter === 'history' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            History
        </a>
        <a href="{{ route('sessions.index', ['filter' => 'all']) }}" 
            class="px-4 py-2 border-b-2 text-sm font-medium {{ $filter === 'all' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            All Sessions
        </a>
    </div>

    <!-- Sessions List -->
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
                                    <p class="text-sm text-gray-500">{{ $session->scheduled_at->format('l, F j, Y \a\t g:i A') }}</p>
                                    <p class="text-sm text-gray-500">Duration: {{ $session->duration_minutes }} minutes</p>
                                </div>
                        </div>
                        <div class="flex flex-col items-end space-y-2">
                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                @if($session->status === 'completed') bg-green-100 text-green-800
                                @elseif($session->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($session->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst($session->status) }}
                            </span>
                            @if($session->canJoin())
                                <a href="{{ route('sessions.join', $session->uuid) }}" 
                                    class="px-3 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700">
                                    Join Now
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
                        <div class="flex space-x-2">
                            @if($session->status === 'pending' && Auth::user()->isTutor())
                                <button onclick="confirmAction('{{ route('sessions.confirm', $session->uuid) }}', 'POST', 'Confirm Booking?', 'The student will be notified.')"
                                    class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                                    Confirm
                                </button>
                            @endif
                            
                            @if(!in_array($session->status, ['completed', 'cancelled']))
                                <button onclick="cancelBooking('{{ route('sessions.cancel', $session->uuid) }}')"
                                    class="px-4 py-2 border border-red-300 text-red-600 text-sm font-medium rounded-md hover:bg-red-50">
                                    Cancel
                                </button>
                            @endif
                        </div>
                        <a href="{{ route('sessions.show', $session->uuid) }}" 
                            class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No sessions found</h3>
                <p class="mt-2 text-gray-500">Get started by exploring available tutors or students.</p>
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
