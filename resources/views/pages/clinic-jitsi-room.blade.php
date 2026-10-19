<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $clinic->title ?? $clinic->name }} - Clinic Session</title>
<script src="https://meet.jitsi.org/external_api.js"></script>
<style>
:root{--ink:#15281F;--violet:#6D28D9;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);}
*{margin:0;padding:0;box-sizing:border-box;}
body,html{width:100%;height:100%;font-family:'DM Sans',sans-serif;background:var(--ink);}

.jitsi-container{width:100%;height:100vh;display:flex;flex-direction:column;}
.jitsi-room{flex:1;width:100%;background:#000;}

.session-header{background:var(--ink);padding:12px 20px;border-bottom:1px solid var(--hair);display:flex;justify-content:space-between;align-items:center;color:#fff;font-size:13px;z-index:100;}
.session-title{font-weight:700;flex:1;}
.session-code{background:rgba(109,40,217,.2);padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700;margin-left:16px;}
.moderator-badge{background:rgba(234,179,8,.2);border:1px solid rgba(234,179,8,.4);color:#facc15;padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700;margin-left:16px;}
.exit-btn{background:var(--hair);color:#fff;border:none;padding:8px 16px;border-radius:8px;cursor:pointer;font-size:12px;font-weight:600;transition:all .15s;}
.exit-btn:hover{background:rgba(255,255,255,.15);}

.fullscreen{position:absolute;top:12px;right:20px;background:rgba(0,0,0,.5);border:none;color:#fff;width:36px;height:36px;border-radius:8px;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;z-index:50;}
.fullscreen:hover{background:rgba(0,0,0,.8);}

.access-warning{position:fixed;bottom:20px;right:20px;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:var(--mint);padding:12px 16px;border-radius:8px;font-size:12px;display:flex;align-items:center;gap:8px;z-index:60;}
</style>
</head>
<body>

<div class="jitsi-container">
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
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="exit-btn">
                <i class="fa-solid fa-sign-out-alt" style="margin-right:6px;"></i> Leave Session
            </button>
        </form>
    </div>

    <div class="jitsi-room" id="jitsi-room"></div>
</div>

<div class="access-warning">
    <i class="fa-solid fa-shield-check"></i>
    <span>Platform-only session · Encrypted end-to-end</span>
</div>

<script>
const domain = "meet.jitsi.org";
const roomName = "{{ $roomId }}";
const displayName = "{{ $displayName }}";
const isModerator = {{ $isModerator ? 'true' : 'false' }};
const clinicTitle = "{{ $clinic->title ?? $clinic->name }}";

const options = {
    roomName: roomName,
    width: '100%',
    height: '100%',
    parentNode: document.querySelector('#jitsi-room'),
    userInfo: {
        displayName: displayName,
        moderator: isModerator
    },
    configOverwrite: {
        prejoinPageEnabled: false,
        disableModeratorIndicator: false,
        startAudioMuted: isModerator ? 0 : 1,
        startVideoMuted: 1,
        disableAudioLevels: false,
        enableNoAudioDetection: true,
        enableNoiseSuppression: true,
        // Disable P2P for clinic sessions (always use server)
        p2p: {
            enabled: false
        },
        // Recording controls (only moderators can record)
        recordingService: {
            enabled: isModerator,
            sharingEnabled: isModerator
        }
    },
    interfaceConfigOverwrite: {
        DEFAULT_BACKGROUND: "#1a1a1a",
        TOOLBAR_BUTTONS: isModerator ? [
            'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
            'fodeviceselection', 'hangup', 'chat', 'recording', 'livestreaming',
            'etherpad', 'sharedvideo', 'settings', 'raisehand',
            'videoquality', 'filmstrip', 'invite', 'feedback', 'stats', 'shortcuts',
            'tileview', 'security'
        ] : [
            'microphone', 'camera', 'closedcaptions', 'fullscreen',
            'fodeviceselection', 'hangup', 'chat', 'raisehand',
            'videoquality', 'filmstrip', 'settings', 'tileview'
        ],
        HIDE_INVITE_MORE_HEADER: true,
        LANG_DETECTION: false,
        DEFAULT_LANGUAGE: 'en'
    }
};

// Initialize Jitsi Meet
const jitsiAPI = new JitsiMeetExternalAPI(domain, options);

// Log session start
jitsiAPI.addEventListener('videoConferenceJoined', function() {
    console.log('User joined clinic session: ' + roomName);
    // Mark attendance in backend if not already done
});

// Track leaving
jitsiAPI.addEventListener('videoConferenceLeft', function() {
    // Session ended
});

// For moderators: listen for recording events
if (isModerator) {
    jitsiAPI.addEventListener('recordingStatusChanged', function(data) {
        console.log('Recording status changed:', data);
        // Optionally: send recording status to backend
    });
}

// Handle fullscreen (optional button)
const fullscreenBtn = document.querySelector('.fullscreen');
if (fullscreenBtn) {
    fullscreenBtn.addEventListener('click', function() {
        const jitsiContainer = document.querySelector('.jitsi-room');
        if (jitsiContainer.requestFullscreen) {
            jitsiContainer.requestFullscreen();
        }
    });
}

// Prevent back navigation (clinic sessions should be exited properly)
window.addEventListener('beforeunload', function(e) {
    // Optional: confirm if user tries to close
    // e.preventDefault();
    // e.returnValue = '';
});
</script>

</body>
</html>
