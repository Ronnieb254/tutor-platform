@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
            <p class="mt-2 text-gray-600">Stay updated on all your bookings and messages</p>
        </div>
        @if($notifications->count() > 0)
            <button onclick="confirmAction('{{ route('notifications.readAll') }}', 'POST', 'Mark all as read?', '')"
                class="px-4 py-2 bg-purple-600 text-white font-medium rounded-md hover:bg-purple-700">
                Mark All as Read
            </button>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="space-y-3">
        @forelse($notifications as $notification)
            <div class="bg-white rounded-lg shadow p-4 {{ !$notification->read_at ? 'border-l-4 border-purple-600' : '' }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <h3 class="font-semibold text-gray-900">
                                @switch($notification->data['type'])
                                    @case('booking_created')
                                        New Booking Request
                                        @break
                                    @case('booking_confirmed')
                                        Booking Confirmed
                                        @break
                                    @case('booking_cancelled')
                                        Booking Cancelled
                                        @break
                                    @case('new_message')
                                        New Message
                                        @break
                                    @case('session_reminder')
                                        Session Reminder
                                        @break
                                    @case('user_joined')
                                        User Joined Session
                                        @break
                                    @default
                                        Notification
                                @endswitch
                            </h3>
                            @if(!$notification->read_at)
                                <span class="inline-block h-2 w-2 bg-purple-600 rounded-full"></span>
                            @endif
                        </div>
                        <p class="text-gray-700 mt-1">{{ $notification->data['message'] }}</p>
                        <p class="text-xs text-gray-500 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    
                    <div class="flex items-center space-x-2 ml-4">
                        @if(!$notification->read_at)
                            <button onclick="fetch('{{ route('notifications.read', $notification->id) }}', {method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content, 'X-Requested-With': 'XMLHttpRequest'}}).then(() => location.reload())"
                                class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded hover:bg-gray-200">
                                Mark Read
                            </button>
                        @endif
                        <button onclick="confirmAction('{{ route('notifications.destroy', $notification->id) }}', 'DELETE', 'Delete notification?', '')"
                            class="text-gray-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                @if(isset($notification->data['booking_uuid']))
                    <div class="mt-3">
                        <a href="{{ route('sessions.show', $notification->data['booking_uuid']) }}" 
                            class="inline-block px-3 py-1 bg-purple-100 text-purple-700 text-sm font-medium rounded hover:bg-purple-200">
                            View Session →
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No notifications</h3>
                <p class="mt-2 text-gray-500">You're all caught up! New notifications will appear here.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

<script src="{{ asset('js/sweetalert-config.js') }}"></script>
@endsection
