@extends('layouts.app')

@section('title', 'Session Room - ' . $booking->uuid)

@section('content')
<div class="min-h-screen bg-gray-900">
    <!-- Header Bar -->
    <div class="bg-gray-800 border-b border-gray-700 px-4 py-3">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('bookings.show', $booking->uuid) }}" 
                   class="text-gray-400 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-white font-semibold">
                        {{ $booking->subject ? $booking->subject->name : 'Session' }} - {{ $booking->duration_minutes }} minutes
                    </h1>
                    <p class="text-sm text-gray-400">
                        @if(auth()->id() === $booking->tutor_id)
                            Student: {{ $booking->student->name }}
                        @else
                            Tutor: {{ $booking->tutor->name }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <p class="text-xs text-gray-400">Session Code</p>
                    <p class="text-sm font-mono text-white">
                        @if(auth()->id() === $booking->tutor_id)
                            {{ $booking->session_code_tutor }}
                        @else
                            {{ $booking->session_code_student }}
                        @endif
                    </p>
                </div>
                
                <button onclick="copySessionCode()" 
                        class="p-2 text-gray-400 hover:text-white transition"
                        title="Copy session code">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Jitsi Meet Container -->
    <div id="jitsi-container" class="w-full" style="height: calc(100vh - 64px);"></div>
</div>

@push('scripts')
<script src="https://meet.jit.si/external_api.js"></script>
<script>
    // Meeting configuration
    const domain = 'meet.jit.si';
    const meetingRoom = '{{ $booking->meeting_room }}';
    const userDisplayName = '{{ auth()->user()->name }}';
    const userEmail = '{{ auth()->user()->email }}';
    const isModerator = {{ auth()->id() === $booking->tutor_id ? 'true' : 'false' }};
    const sessionCode = '{{ auth()->id() === $booking->tutor_id ? $booking->session_code_tutor : $booking->session_code_student }}';

    // Jitsi Meet configuration options
    const options = {
        roomName: meetingRoom,
        width: '100%',
        height: '100%',
        parentNode: document.querySelector('#jitsi-container'),
        configOverwrite: {
            startWithAudioMuted: false,
            startWithVideoMuted: false,
            enableWelcomePage: false,
            prejoinPageEnabled: false,
            disableInviteFunctions: true,
            enableNoisyMicDetection: true,
            resolution: 720,
            constraints: {
                video: {
                    height: {
                        ideal: 720,
                        max: 1080,
                        min: 240
                    }
                }
            },
            // Recording controls - only moderator can record
            fileRecordingsEnabled: isModerator,
            liveStreamingEnabled: false,
            // UI customization
            toolbarButtons: [
                'microphone',
                'camera',
                'desktop',
                'fullscreen',
                'fodeviceselection',
                'hangup',
                'profile',
                'chat',
                'recording',
                'livestreaming',
                'etherpad',
                'sharedvideo',
                'settings',
                'raisehand',
                'videoquality',
                'filmstrip',
                'stats',
                'shortcuts',
                'tileview',
                'help',
                'mute-everyone',
                'security'
            ],
        },
        interfaceConfigOverwrite: {
            SHOW_JITSI_WATERMARK: false,
            SHOW_WATERMARK_FOR_GUESTS: false,
            SHOW_BRAND_WATERMARK: false,
            BRAND_WATERMARK_LINK: '',
            DEFAULT_BACKGROUND: '#1f2937',
            DISABLE_PRESENCE_STATUS: false,
            DISPLAY_WELCOME_PAGE_CONTENT: false,
            HIDE_INVITE_MORE_HEADER: true,
            MOBILE_APP_PROMO: false,
            SHOW_CHROME_EXTENSION_BANNER: false,
            DEFAULT_REMOTE_DISPLAY_NAME: 'Participant',
            APP_NAME: 'Mwalimu Session',
            NATIVE_APP_NAME: 'Mwalimu',
            PROVIDER_NAME: 'Mwalimu',
        },
        userInfo: {
            displayName: userDisplayName,
            email: userEmail
        }
    };

    // Initialize Jitsi
    const api = new JitsiMeetExternalAPI(domain, options);

    // Set moderator status
    if (isModerator) {
        api.addEventListener('videoConferenceJoined', () => {
            console.log('Joined as moderator');
        });
    }

    // Track when user leaves
    api.addEventListener('readyToClose', () => {
        window.location.href = '{{ route("bookings.show", $booking->uuid) }}';
    });

    // Handle errors
    api.addEventListener('errorOccurred', (error) => {
        console.error('Jitsi error:', error);
        alert('An error occurred with the video conference. Please try refreshing the page.');
    });

    // Copy session code function
    function copySessionCode() {
        const code = sessionCode;
        navigator.clipboard.writeText(code).then(() => {
            // Show temporary success message
            const btn = event.currentTarget;
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
            btn.classList.add('text-green-400');
            
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.classList.remove('text-green-400');
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy:', err);
            alert('Failed to copy session code');
        });
    }

    // Prevent accidental page close
    window.addEventListener('beforeunload', (e) => {
        e.preventDefault();
        e.returnValue = '';
    });

    // Cleanup on page unload
    window.addEventListener('unload', () => {
        if (api) {
            api.dispose();
        }
    });
</script>
@endpush

@endsection
