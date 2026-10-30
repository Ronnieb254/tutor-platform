<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>{{ $clinic->title ?? $clinic->name }} - Clinic Session</title>
<style>
:root{--ink:#15281F;--trust-blue:#0A64BC;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);}
*{margin:0;padding:0;box-sizing:border-box;}
body,html{width:100%;height:100%;font-family:'DM Sans',sans-serif;background:var(--ink);overflow:hidden;}

.room-container{width:100%;height:100vh;display:flex;flex-direction:column;}
#jitsi-container{flex:1;width:100%;background:#000;}

.session-header{background:linear-gradient(135deg,#111C16,#15281F);padding:12px 24px;border-bottom:1px solid rgba(255,255,255,.08);display:flex;justify-content:space-between;align-items:center;color:#fff;font-size:13px;z-index:100;flex-shrink:0;}
.session-header .left{display:flex;align-items:center;gap:16px;flex:1;min-width:0;}
.back-btn{display:inline-flex;align-items:center;gap:6px;color:rgba(255,255,255,.5);text-decoration:none;font-size:13px;transition:color .15s;flex-shrink:0;}
.back-btn:hover{color:rgba(255,255,255,.85);}
.session-title{font-weight:700;font-family:'Fraunces',serif;font-size:16px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.session-meta{display:flex;align-items:center;gap:12px;flex-shrink:0;}
.session-meta .dot{width:4px;height:4px;border-radius:50%;background:rgba(255,255,255,.2);}
.session-meta .time{color:rgba(255,255,255,.4);font-size:12px;}
.session-code{background:rgba(10,100,188,.2);border:1px solid rgba(10,100,188,.3);padding:4px 12px;border-radius:8px;font-size:11px;font-weight:700;font-family:monospace;letter-spacing:.05em;}
.moderator-badge{background:rgba(234,179,8,.15);border:1px solid rgba(234,179,8,.3);color:#facc15;padding:4px 12px;border-radius:8px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;}
.copy-btn{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);color:#fff;width:36px;height:36px;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .15s;flex-shrink:0;}
.copy-btn:hover{background:rgba(255,255,255,.15);}
.copy-btn.copied{background:rgba(16,185,129,.2);border-color:rgba(16,185,129,.4);color:#10B981;}
.exit-btn{background:rgba(234,85,42,.15);border:1px solid rgba(234,85,42,.3);color:#ff7849;padding:8px 18px;border-radius:999px;cursor:pointer;font-size:13px;font-weight:600;transition:all .15s;flex-shrink:0;display:inline-flex;align-items:center;gap:6px;font-family:inherit;}
.exit-btn:hover{background:rgba(234,85,42,.25);}
.recording-indicator{display:none;align-items:center;gap:6px;background:rgba(234,85,42,.15);border:1px solid rgba(234,85,42,.3);color:#ff7849;padding:4px 12px;border-radius:8px;font-size:11px;font-weight:700;animation:pulse-rec 1.5s infinite;}
.recording-indicator.active{display:inline-flex;}
@keyframes pulse-rec{0%,100%{opacity:1;}50%{opacity:.5;}}
</style>
</head>
<body>

<div class="room-container">
    {{-- Session Header --}}
    <div class="session-header">
        <div class="left">
            <a href="{{ route('examiner-clinic', $clinic->slug) }}" class="back-btn">
                <i class="fa-solid fa-arrow-left fa-xs"></i> Back
            </a>
            <div class="session-title">
                <i class="fa-solid fa-chalkboard-user fa-xs" style="color:rgba(10,100,188,.5);margin-right:4px;"></i>
                {{ $clinic->title ?? $clinic->name }}
            </div>
            <div class="session-meta">
                <span class="dot"></span>
                <span class="time">{{ $clinic->scheduled_at->format('D, M j · g:ia') }}</span>
                @if($isModerator)
                    <span class="moderator-badge"><i class="fa-solid fa-shield-halved fa-xs"></i> MODERATOR</span>
                @endif
                <span class="recording-indicator" id="recIndicator">
                    <i class="fa-solid fa-circle fa-xs"></i> REC
                </span>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:12px;flex-shrink:0;">
            @if(!$isModerator && !empty($sessionCode))
            <div style="text-align:right;">
                <p style="font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.35);margin-bottom:2px;">Session Code</p>
                <p style="font-size:13px;font-family:monospace;font-weight:700;color:#fff;letter-spacing:.05em;">{{ $sessionCode }}</p>
            </div>
            <button onclick="copySessionCode()" class="copy-btn" id="copyBtn" title="Copy session code">
                <i class="fa-solid fa-copy fa-xs"></i>
            </button>
            @endif
            <button onclick="leaveSession()" class="exit-btn">
                <i class="fa-solid fa-phone-slash fa-xs"></i> Leave
            </button>
        </div>
    </div>

    {{-- Jitsi Meet Container --}}
    <div id="jitsi-container"></div>
</div>

<script src="https://meet.jit.si/external_api.js"></script>
<script>
    const domain = 'meet.jit.si';
    const meetingRoom = '{{ $clinic->meeting_room }}';
    const userDisplayName = '{{ addslashes(auth()->user()->name) }}';
    const userEmail = '{{ addslashes(auth()->user()->email) }}';
    const isModerator = {{ $isModerator ? 'true' : 'false' }};
    const sessionCode = '{{ $sessionCode ?? '' }}';
    const clinicUrl = '{{ route("examiner-clinic", $clinic->slug) }}';

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
            enableNoisyMicDetection: true,
            resolution: 720,
            constraints: {
                video: {
                    height: { ideal: 720, max: 1080, min: 240 },
                    width: { ideal: 1280, max: 1920, min: 320 }
                }
            },
            // Screen sharing — all sources
            desktopSharingChromeDesktopSources: ['screen', 'window', 'tab'],
            desktopSharingChromeSources: ['screen', 'window', 'tab'],
            desktopSharingChromeExtId: null,
            desktopSharingFirefoxDisabled: false,
            desktopSharingFirefoxMaxExtensionIframeSize: 600,
            // Recording (moderators only)
            fileRecordingsEnabled: true,
            liveStreamingEnabled: false,
            // All collaboration tools
            whiteboardEnabled: true,
            etherpadEnabled: true,
            breakoutRoomsEnabled: true,
        },
        interfaceConfigOverwrite: {
            SHOW_JITSI_WATERMARK: false,
            SHOW_WATERMARK_FOR_GUESTS: false,
            SHOW_BRAND_WATERMARK: false,
            SHOW_POWERED_BY: false,
            BRAND_WATERMARK_LINK: '',
            JITSI_WATERMARK_LINK: '',
            DEFAULT_BACKGROUND: '#15281F',
            DEFAULT_LOCAL_DISPLAY_NAME: 'me',
            DEFAULT_REMOTE_DISPLAY_NAME: 'Participant',
            DISABLE_PRESENCE_STATUS: false,
            DISPLAY_WELCOME_PAGE_CONTENT: false,
            DISPLAY_INVITE_MORE_HEADER: false,
            HIDE_INVITE_MORE_HEADER: true,
            MOBILE_APP_PROMO: false,
            SHOW_CHROME_EXTENSION_BANNER: false,
            SHOW_DEEP_LINKING_IMAGE: false,
            RECENT_LIST_ENABLED: false,
            SETTINGS_SECTIONS: ['devices', 'language', 'moderator', 'profile', 'calendar'],
            VIDEO_LAYOUT_FIT: 'both',
            // Full toolbar — all tools visible and available
            TOOLBAR_BUTTONS: [
                'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                'fodeviceselection', 'hangup', 'profile', 'chat', 'recording',
                'etherpad', 'sharedvideo', 'settings', 'raisehand', 'videoquality',
                'filmstrip', 'stats', 'shortcuts', 'tileview', 'videobackgroundblur',
                'download', 'help', 'mute-everyone', 'mute-video-everyone',
                'security', 'toggle-camera', 'whiteboard', 'participants-pane',
            ],
            TOOLBAR_ALWAYS_VISIBLE: false,
            INITIAL_TOOLBAR_TIMEOUT: 5000,
            TOOLBAR_TIMEOUT: 4000,
        },
        userInfo: {
            displayName: userDisplayName,
            email: userEmail,
        },
    };

    const api = new JitsiMeetExternalAPI(domain, options);

    api.addEventListener('videoConferenceJoined', () => {
        console.log('Joined as ' + (isModerator ? 'moderator' : 'participant'));
    });

    // Recording status
    api.addEventListener('recordingStatusChanged', (e) => {
        const indicator = document.getElementById('recIndicator');
        if (e.on && isModerator) {
            indicator.classList.add('active');
        } else {
            indicator.classList.remove('active');
        }
    });

    api.addEventListener('readyToClose', () => {
        window.location.href = clinicUrl;
    });
    api.addEventListener('hangup', () => {
        window.location.href = clinicUrl;
    });

    api.addEventListener('errorOccurred', (error) => {
        console.error('Jitsi error:', error);
    });

    function copySessionCode() {
        navigator.clipboard.writeText(sessionCode).then(() => {
            const btn = document.getElementById('copyBtn');
            btn.innerHTML = '<i class="fa-solid fa-check fa-xs"></i>';
            btn.classList.add('copied');
            setTimeout(() => {
                btn.innerHTML = '<i class="fa-solid fa-copy fa-xs"></i>';
                btn.classList.remove('copied');
            }, 2000);
        }).catch(() => alert('Failed to copy: ' + sessionCode));
    }

    function leaveSession() {
        try { api.executeCommand('hangup'); } catch(e) {}
        setTimeout(() => { window.location.href = clinicUrl; }, 500);
    }

    window.addEventListener('beforeunload', (e) => {
        try { api.dispose(); } catch(err) {}
    });
</script>

</body>
</html>
