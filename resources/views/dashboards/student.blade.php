@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}!</h1>
        <p class="mt-2 text-gray-600">Here's your learning overview</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Upcoming Sessions</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $upcomingSessions->count() }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Completed Sessions</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $totalSessions }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Unread Messages</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $unreadMessages }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Sessions -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Upcoming Sessions</h2>
        </div>
        <div class="p-6">
            @forelse($upcomingSessions as $session)
                <div class="mb-4 p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <img src="{{ $session->tutor->avatar_url }}" alt="{{ $session->tutor->name }}" 
                                class="h-12 w-12 rounded-full">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">{{ $session->subject->name }}</h3>
                                <p class="text-sm text-gray-500">with {{ $session->tutor->name }}</p>
                                <p class="text-xs text-gray-400">{{ $session->scheduled_at->format('M d, Y - g:i A') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($session->canJoin())
                                <a href="{{ route('sessions.join', $session->uuid) }}" 
                                    class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                                    Join Now
                                </a>
                            @endif
                            <a href="{{ route('sessions.show', $session->uuid) }}" 
                                class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">No upcoming sessions</p>
            @endforelse
            
            @if($upcomingSessions->count() > 0)
                <div class="mt-4 text-center">
                    <a href="{{ route('sessions.index') }}" class="text-purple-600 hover:text-purple-800 font-medium">
                        View All Sessions →
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Sessions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recent Sessions</h2>
        </div>
        <div class="p-6">
            @forelse($recentSessions as $session)
                <div class="mb-4 p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <img src="{{ $session->tutor->avatar_url }}" alt="{{ $session->tutor->name }}" 
                                class="h-10 w-10 rounded-full">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">{{ $session->subject->name }}</h3>
                                <p class="text-sm text-gray-500">with {{ $session->tutor->name }}</p>
                                <p class="text-xs text-gray-400">{{ $session->scheduled_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div>
                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                @if($session->status === 'completed') bg-green-100 text-green-800
                                @elseif($session->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($session->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">No recent sessions</p>
            @endforelse
            
            @if($recentSessions->count() > 0)
                <div class="mt-4 text-center">
                    <a href="{{ route('sessions.history') }}" class="text-purple-600 hover:text-purple-800 font-medium">
                        View All History →
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
