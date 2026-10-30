<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $clinic->title ?? $clinic->name }} - Clinic Session</title>
<style>
:root{--ink:#15281F;--trust-blue:#0A64BC;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);}
*{margin:0;padding:0;box-sizing:border-box;}
body,html{width:100%;height:100%;font-family:'DM Sans',sans-serif;background:var(--ink);}

.clinic-container{width:100%;height:100vh;display:flex;flex-direction:column;}
#jitsi-container{flex:1;width:100%;background:#000;}

.session-header{background:var(--ink);padding:12px 20px;border-bottom:1px solid var(--hair);display:flex;justify-content:space-between;align-items:center;color:#fff;font-size:13px;z-index:100;}
.session-title{font-weight:700;flex:1;}
.session-code{background:rgba(10,100,188,.2);padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700;margin-left:16px;}
.moderator-badge{background:rgba(234,179,8,.2);border:1px solid rgba(234,179,8,.4);color:#facc15;padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700;margin-left:16px;}
.exit-btn{background:var(--hair);color:#fff;border:none;padding:8px 16px;border-radius:8px;cursor:pointer;font-size:12px;font-weight:600;transition:all .15s;}
.exit-btn:hover{background:rgba(255,255,255,.15);}
</style>
</head>
<body>

<div class="clinic-container">
    <div class="session-header">
        <div style="display:flex;align-items:center;flex:1;">
            <div class="session-title">
                <i class="fa-solid fa-chalkboard-user"></i> {{ $clinic->title ?? $clinic->name }}
            </div>
            <span style="color:var(--muted);margin:0 12px;font-size:10px;">•</span>
            <span style="color:var(--muted);">{{ $clinic->scheduled_at->format('g:ia') }}</span>
            @if($isModerator)
                <span class="moderator-badge">
                    <i class="fa-solid fa-shield-check"></i> MODERATOR
                </span>
            @else
                <span class="session-code">{{ $sessionCode }}</span>
            @endif
        </div>
        <button onclick="leaveSession()" class="exit-btn">
            <i class="fa-solid fa-sign-out-alt" style="margin-right:6px;"></i> Leave Session
        </button>
    </div>

    <div id="jitsi-container"></div>
</div>

<script src="https://meet.jit.si/external_api.js"></script>
<script>
    const domain = 'meet.jit.si';
    const meetingRoom = '{{ $clinic->meeting_room }}';
    const userDisplayName = '{{ auth()->user()->name }}';
    const userEmail = '{{ auth()->user()->email }}';
    const isModerator = {{ $isModerator ? 'true' : 'false' }};

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
            fileRecordingsEnabled: isModerator,
            liveStreamingEnabled: false,
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
            APP_NAME: 'Mwalimu Clinic',
            NATIVE_APP_NAME: 'Mwalimu',
            PROVIDER_NAME: 'Mwalimu',
        },
        userInfo: {
            displayName: userDisplayName,
            email: userEmail
        }
    };

    const api = new JitsiMeetExternalAPI(domain, options);

    if (isModerator) {
        api.addEventListener('videoConferenceJoined', () => {
            console.log('Joined as moderator');
        });
    }

    api.addEventListener('readyToClose', () => {
        window.location.href = '{{ route("examiner-clinic", $clinic->slug) }}';
    });

    api.addEventListener('errorOccurred', (error) => {
        console.error('Jitsi error:', error);
        alert('An error occurred with the video conference. Please try refreshing the page.');
    });

    function leaveSession() {
        if (confirm('Are you sure you want to leave this session?')) {
            api.executeCommand('hangup');
            window.location.href = '{{ route("examiner-clinic", $clinic->slug) }}';
        }
    }

    window.addEventListener('beforeunload', (e) => {
        e.preventDefault();
        e.returnValue = '';
    });

    window.addEventListener('unload', () => {
        if (api) {
            api.dispose();
        }
    });
</script>

</body>
</html>

    @if(!empty($joinUrl))
    <iframe class="bbb-room"
            src="{{ $joinUrl }}"
            allow="camera; microphone; fullscreen; display-capture; autoplay"
            allowfullscreen>
    </iframe>
    @else
    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px;text-align:center;">
        <i class="fa-solid fa-video" style="font-size:48px;color:rgba(255,255,255,.15);margin-bottom:18px;"></i>
        <h3 style="font-family:'Fraunces',serif;font-size:22px;color:#fff;margin-bottom:8px;">Session Link Not Available</h3>
        <p style="font-size:14px;color:rgba(255,255,255,.5);max-width:380px;">The session room link is not ready yet. Please refresh the page or contact support if this persists.</p>
    </div>
    @endif
</div>

<div class="access-warning">
    <i class="fa-solid fa-shield-check"></i>
    <span>Platform-only session · Powered by Jitsi · Encrypted</span>
</div>

</body>
</html>
