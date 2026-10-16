@extends('layouts.app')

@section('title', 'Tutor Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}!</h1>
        <p class="mt-2 text-gray-600">Manage your tutoring schedule and students</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Earnings This Month</dt>
                        <dd class="text-2xl font-semibold text-gray-900">KES {{ number_format($earningsThisMonth, 2) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-orange-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Bookings</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $pendingBookings->count() }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-pink-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Average Rating</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ number_format($avgRating, 1) }}/5</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Pending Bookings -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Pending Bookings</h2>
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full">{{ $pendingBookings->count() }} new</span>
                </div>
                <div class="p-6">
                    @forelse($pendingBookings as $booking)
                        <div class="mb-4 p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $booking->student->avatar_url }}" alt="{{ $booking->student->name }}" 
                                        class="h-10 w-10 rounded-full">
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ $booking->student->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $booking->subject->name }}</p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500">{{ $booking->scheduled_at->format('M d, Y g:i A') }}</span>
                            </div>
                            <div class="mt-3 flex space-x-2">
                                <button onclick="confirmAction('{{ route('sessions.confirm', $booking->uuid) }}', 'POST', 'Confirm Booking?', 'This student will be notified immediately.')"
                                    class="px-3 py-1 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700">
                                    Confirm
                                </button>
                                <button onclick="confirmAction('{{ route('sessions.cancel', $booking->uuid) }}', 'POST', 'Cancel Booking?', 'The student will be notified about the cancellation.')"
                                    class="px-3 py-1 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700">
                                    Decline
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">No pending bookings</p>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Sessions -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Upcoming Sessions</h2>
                </div>
                <div class="p-6">
                    @forelse($upcomingSessions as $session)
                        <div class="mb-4 p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <img src="{{ $session->student->avatar_url }}" alt="{{ $session->student->name }}" 
                                        class="h-12 w-12 rounded-full">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900">{{ $session->student->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $session->subject->name }}</p>
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
                                        Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">No upcoming sessions</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Stats Summary -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Stats</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Sessions</span>
                        <span class="font-semibold text-gray-900">{{ $totalSessions }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Unread Messages</span>
                        <span class="font-semibold text-gray-900">{{ $unreadMessages }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Earnings</span>
                        <span class="font-semibold text-gray-900">KES {{ number_format($earningsThisMonth, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('sessions.index') }}" class="block w-full px-4 py-2 bg-purple-600 text-white text-center text-sm font-medium rounded-md hover:bg-purple-700">
                        View All Sessions
                    </a>
                    <a href="{{ route('sessions.history') }}" class="block w-full px-4 py-2 border border-gray-300 text-gray-700 text-center text-sm font-medium rounded-md hover:bg-gray-50">
                        Session History
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <div class="bg-white rounded-lg shadow mt-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Payment Received History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completed Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount Earned</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($paymentHistory as $booking)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img src="{{ $booking->student->avatar_url }}" alt="{{ $booking->student->name }}" 
                                        class="h-8 w-8 rounded-full mr-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $booking->student->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $booking->subject->name }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-500">{{ $booking->duration_minutes }} min</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-500">{{ $booking->completed_at->format('M d, Y') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-green-600">KES {{ number_format($booking->tutor_earning, 2) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No payment history yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
