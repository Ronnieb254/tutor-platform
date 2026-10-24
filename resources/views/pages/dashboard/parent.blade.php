@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-trust-blue-50/30 to-slate-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900 font-['Fraunces']">Parent Dashboard</h1>
            <p class="text-slate-600 mt-2">Manage your children's learning journey</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Children Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600">Total Children</p>
                        <p class="text-3xl font-bold text-trust-blue-600 mt-2">{{ $children->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-trust-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-trust-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Upcoming Sessions Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600">Upcoming Sessions</p>
                        <p class="text-3xl font-bold text-mint-600 mt-2">{{ $upcomingSessions->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-mint-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-mint-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Messages Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600">Unread Messages</p>
                        <p class="text-3xl font-bold text-coral-600 mt-2">{{ $unreadMessages }}</p>
                    </div>
                    <div class="w-12 h-12 bg-coral-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-coral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Children List -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-slate-900 font-['Fraunces']">My Children</h2>
                        <button onclick="document.getElementById('addChildModal').classList.remove('hidden')" class="px-4 py-2 bg-trust-blue-600 text-white rounded-lg hover:bg-trust-blue-700 transition-colors text-sm font-medium">
                            + Add Child
                        </button>
                    </div>

                    @if($children->isEmpty())
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">No children added yet</h3>
                            <p class="text-slate-600 mb-4">Add your first child to start booking tutoring sessions</p>
                            <button onclick="document.getElementById('addChildModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-trust-blue-600 text-white rounded-lg hover:bg-trust-blue-700 transition-colors">
                                Add Child Profile
                            </button>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($children as $child)
                                <div class="flex items-center justify-between p-4 border border-slate-200 rounded-xl hover:border-trust-blue-300 transition-colors">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-trust-blue-400 to-trust-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                            {{ strtoupper(substr($child->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-slate-900">{{ $child->name }}</h3>
                                            <p class="text-sm text-slate-600">
                                                @if($child->grade_level)Grade {{ $child->grade_level }} • @endif
                                                @if($child->age)Age {{ $child->age }}@endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('tutors.index') }}" class="px-3 py-2 text-sm bg-trust-blue-600 text-white rounded-lg hover:bg-trust-blue-700 transition-colors">
                                            Book Session
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Add Child Modal -->
                <div id="addChildModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-md w-full p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-slate-900 font-['Fraunces']">Add Child Profile</h3>
                            <button onclick="document.getElementById('addChildModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <form action="{{ route('student.children.store') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Child's Name *</label>
                                    <input type="text" name="name" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-trust-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Age</label>
                                    <input type="number" name="age" min="3" max="25" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-trust-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Grade Level</label>
                                    <input type="text" name="grade_level" placeholder="e.g., Grade 8, Form 3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-trust-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                                    <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-trust-blue-500 focus:border-transparent" placeholder="Any special requirements or notes..."></textarea>
                                </div>
                            </div>
                            <div class="flex items-center justify-end space-x-3 mt-6">
                                <button type="button" onclick="document.getElementById('addChildModal').classList.add('hidden')" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 bg-trust-blue-600 text-white rounded-lg hover:bg-trust-blue-700 transition-colors">
                                    Add Child
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Upcoming Sessions -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 font-['Fraunces']">Upcoming Sessions</h2>

                    @if($upcomingSessions->isEmpty())
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">No upcoming sessions</h3>
                            <p class="text-slate-600 mb-4">Book a session to get started</p>
                            <a href="{{ route('tutors.index') }}" class="inline-flex items-center px-4 py-2 bg-trust-blue-600 text-white rounded-lg hover:bg-trust-blue-700 transition-colors">
                                Find a Tutor
                            </a>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($upcomingSessions as $session)
                                <div class="border border-slate-200 rounded-xl p-4 hover:border-trust-blue-300 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <span class="px-3 py-1 bg-mint-100 text-mint-700 rounded-full text-xs font-medium">
                                                    {{ $session->subject->name ?? 'Subject' }}
                                                </span>
                                                <span class="px-3 py-1 bg-trust-blue-100 text-trust-blue-700 rounded-full text-xs font-medium">
                                                    {{ ucfirst($session->status) }}
                                                </span>
                                            </div>
                                            <h3 class="font-semibold text-slate-900 mb-1">
                                                Session with {{ $session->tutor->name ?? 'Tutor' }}
                                            </h3>
                                            <p class="text-sm text-slate-600 mb-2">
                                                For: {{ $session->childProfile->name ?? 'Child' }}
                                            </p>
                                            <div class="flex items-center space-x-4 text-sm text-slate-600">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    {{ $session->scheduled_at->format('M d, Y') }}
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $session->scheduled_at->format('h:i A') }}
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $session->duration }} min
                                                </span>
                                            </div>
                                        </div>
                                        <a href="/sessions" class="px-4 py-2 text-sm text-trust-blue-600 hover:bg-trust-blue-50 rounded-lg transition-colors">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-4 font-['Fraunces']">Quick Actions</h2>
                    <div class="space-y-3">
                        <a href="{{ route('tutors.index') }}" class="flex items-center p-3 border border-slate-200 rounded-lg hover:border-trust-blue-300 hover:bg-trust-blue-50 transition-colors">
                            <div class="w-10 h-10 bg-trust-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-trust-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-slate-900 text-sm">Find Tutors</div>
                                <div class="text-xs text-slate-600">Browse available tutors</div>
                            </div>
                        </a>

                        <a href="/sessions" class="flex items-center p-3 border border-slate-200 rounded-lg hover:border-mint-300 hover:bg-mint-50 transition-colors">
                            <div class="w-10 h-10 bg-mint-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-mint-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-slate-900 text-sm">My Sessions</div>
                                <div class="text-xs text-slate-600">View all sessions</div>
                            </div>
                        </a>

                        <a href="{{ route('clinic.my-bookings') }}" class="flex items-center p-3 border border-slate-200 rounded-lg hover:border-mint-300 hover:bg-mint-50 transition-colors">
                            <div class="w-10 h-10 bg-mint-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-mint-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-slate-900 text-sm">Exam Clinics</div>
                                <div class="text-xs text-slate-600">View clinic bookings</div>
                            </div>
                        </a>

                        <a href="{{ route('inbox.index') }}" class="flex items-center p-3 border border-slate-200 rounded-lg hover:border-coral-300 hover:bg-coral-50 transition-colors">
                            <div class="w-10 h-10 bg-coral-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-coral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-slate-900 text-sm">Messages</div>
                                <div class="text-xs text-slate-600">
                                    @if($unreadMessages > 0)
                                        <span class="text-coral-600 font-semibold">{{ $unreadMessages }} new</span>
                                    @else
                                        Chat with tutors
                                    @endif
                                </div>
                            </div>
                        </a>

                        <button onclick="document.getElementById('addChildModal').classList.remove('hidden')" class="w-full flex items-center p-3 border border-slate-200 rounded-lg hover:border-trust-blue-300 hover:bg-trust-blue-50 transition-colors">
                            <div class="w-10 h-10 bg-trust-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-trust-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div class="text-left">
                                <div class="font-medium text-slate-900 text-sm">Add Child</div>
                                <div class="text-xs text-slate-600">Create new profile</div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Help & Support -->
                <div class="bg-gradient-to-br from-trust-blue-600 to-trust-blue-700 rounded-2xl shadow-sm p-6 text-white">
                    <h2 class="text-lg font-bold mb-2 font-['Fraunces']">Need Help?</h2>
                    <p class="text-trust-blue-100 text-sm mb-4">
                        Our support team is here to assist you with any questions.
                    </p>
                    <a href="mailto:support@mwalimu.co.ke" class="inline-flex items-center px-4 py-2 bg-white text-trust-blue-600 rounded-lg hover:bg-trust-blue-50 transition-colors text-sm font-medium">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
