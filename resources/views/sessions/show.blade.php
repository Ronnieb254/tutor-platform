@extends('layouts.app')

@section('title', 'Session Details')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Session Header -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-8 border-b border-gray-200">
            <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4">
                    @if(Auth::user()->isTutor())
                        <img src="{{ $booking->student->avatar_url }}" alt="{{ $booking->student->name }}" 
                            class="h-16 w-16 rounded-full">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $booking->subject->name }}</h1>
                            <p class="text-gray-600 mt-1">Student: <strong>{{ $booking->student->name }}</strong></p>
                    @else
                        <img src="{{ $booking->tutor->avatar_url }}" alt="{{ $booking->tutor->name }}" 
                            class="h-16 w-16 rounded-full">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $booking->subject->name }}</h1>
                            <p class="text-gray-600 mt-1">Tutor: <strong>{{ $booking->tutor->name }}</strong></p>
                    @endif
                            <p class="text-sm text-gray-500 mt-1">{{ $booking->scheduled_at->format('l, F j, Y \a\t g:i A') }}</p>
                        </div>
                </div>
                <div class="text-right">
                    <span class="px-4 py-2 text-sm font-medium rounded-full
                        @if($booking->status === 'completed') bg-green-100 text-green-800
                        @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                        @else bg-blue-100 text-blue-800
                        @endif">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
            </div>

            <!-- Session Details -->
            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Duration</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $booking->duration_minutes }} min</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Amount</p>
                    <p class="text-lg font-semibold text-gray-900">KES {{ number_format($booking->total_amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Student Joined</p>
                    <p class="text-lg font-semibold {{ $booking->student_joined ? 'text-green-600' : 'text-gray-400' }}">
                        {{ $booking->student_joined ? '✓ Yes' : '✗ No' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tutor Joined</p>
                    <p class="text-lg font-semibold {{ $booking->tutor_joined ? 'text-green-600' : 'text-gray-400' }}">
                        {{ $booking->tutor_joined ? '✓ Yes' : '✗ No' }}
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex space-x-3">
                @if($booking->canJoin())
                    <a href="{{ route('sessions.join', $booking->uuid) }}" 
                        class="px-4 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700">
                        Join Session
                    </a>
                @endif

                @if($booking->status === 'pending' && Auth::user()->isTutor())
                    <button onclick="confirmAction('{{ route('sessions.confirm', $booking->uuid) }}', 'POST', 'Confirm Booking?', 'The student will be notified.')"
                        class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700">
                        Confirm Booking
                    </button>
                @endif

                @if(!in_array($booking->status, ['completed', 'cancelled']))
                    <button onclick="cancelBooking('{{ route('sessions.cancel', $booking->uuid) }}')"
                        class="px-4 py-2 border border-red-300 text-red-600 font-medium rounded-md hover:bg-red-50">
                        Cancel Session
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Messages Section -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Messages</h2>
                </div>
                
                <!-- Messages Container -->
                <div id="messagesContainer" class="h-96 overflow-y-auto p-6 space-y-4 bg-gray-50">
                    @forelse($booking->messages as $message)
                        <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xs {{ $message->sender_id === Auth::id() ? 'bg-purple-600 text-white' : 'bg-white border border-gray-200' }} rounded-lg p-4">
                                <p class="text-sm">{{ $message->body }}</p>
                                <p class="text-xs {{ $message->sender_id === Auth::id() ? 'text-purple-100' : 'text-gray-500' }} mt-1">
                                    {{ $message->created_at->format('g:i A') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">No messages yet. Start the conversation!</p>
                    @endforelse
                </div>

                <!-- Message Input -->
                <div class="px-6 py-4 border-t border-gray-200">
                    <form id="messageForm" class="flex space-x-2">
                        @csrf
                        <input type="text" id="messageInput" name="body" placeholder="Type your message..." 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                            required>
                        <button type="submit" class="px-6 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700">
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Session Info & Notes -->
        <div>
            <!-- Session Info -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Session Info</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Subject</p>
                        <p class="font-medium text-gray-900">{{ $booking->subject->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date & Time</p>
                        <p class="font-medium text-gray-900">{{ $booking->scheduled_at->format('M d, Y - g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Duration</p>
                        <p class="font-medium text-gray-900">{{ $booking->duration_minutes }} minutes</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Amount</p>
                        <p class="font-medium text-gray-900">KES {{ number_format($booking->total_amount, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($booking->student_notes)
                <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">Student Notes</h3>
                    <p class="text-sm text-blue-700">{{ $booking->student_notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="{{ asset('js/sweetalert-config.js') }}"></script>
<script>
document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('messageInput');
    const body = messageInput.value.trim();
    
    if (!body) return;
    
    const formData = new FormData();
    formData.append('body', body);
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    
    fetch('{{ route('messages.store', $booking->uuid) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            // Reload messages
            location.reload();
        } else {
            showError('Error', data.message || 'Failed to send message.');
        }
    })
    .catch(error => {
        showError('Error', 'An unexpected error occurred.');
    });
});
</script>
@endsection
