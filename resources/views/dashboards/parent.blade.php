@extends('layouts.app')

@section('title', 'Parent Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}!</h1>
        <p class="mt-2 text-gray-600">Monitor your children's learning progress</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-gray-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 4H9m6 16H9m6-7a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Children</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $children->count() }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-gray-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="flex-shrink-0 bg-gray-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <p class="text-sm text-gray-500">{{ $session->childProfile->name ?? 'Your child' }} with {{ $session->tutor->name }}</p>
                                <p class="text-xs text-gray-400">{{ $session->scheduled_at->format('M d, Y - g:i A') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('sessions.show', $session->uuid) }}" 
                                class="px-4 py-2 bg-black text-white text-sm font-medium rounded-md hover:bg-gray-800">
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
                    <a href="{{ route('sessions.index') }}" class="text-black hover:text-gray-700 font-medium">
                        View All Sessions →
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Children Profiles -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Your Children</h2>
        </div>
        <div class="p-6">
            @forelse($children as $child)
                <div class="mb-4 p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="h-12 w-12 rounded-full bg-black flex items-center justify-center">
                                <span class="text-lg font-semibold text-white">{{ substr($child->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $child->name }}</h3>
                                <p class="text-sm text-gray-500">Grade {{ $child->grade ?? 'Not specified' }}</p>
                            </div>
                        </div>
                        <button onclick="showSuccess('Coming Soon', 'Child management features coming soon.')"
                            class="px-4 py-2 bg-black text-white text-sm font-medium rounded-md hover:bg-gray-800">
                            Manage
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">No children added yet</p>
            @endforelse
            
            @if($children->count() > 0)
                <div class="mt-4 text-center">
                    <button onclick="showSuccess('Coming Soon', 'Add child feature coming soon.')"
                        class="text-black hover:text-gray-700 font-medium">
                        Add Another Child →
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="{{ asset('js/sweetalert-config.js') }}"></script>
@endsection
