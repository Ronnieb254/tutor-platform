<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<title>{{ $groupSession->title }} – Live Session · Mwalimu</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
:root {
    --ink: #15281F;
    --blue: #0A64BC;
    --mint: #10B981;
    --gold: #EEBE15;
    --coral: #E8522A;
    --muted: rgba(21,40,31,.52);
    --hair: rgba(21,40,31,.09);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html, body {
    width: 100%; height: 100%;
    font-family: 'DM Sans', sans-serif;
    background: var(--ink);
    overflow: hidden;
}

/* ── Layout ───────────────────────────────────────── */
.room-wrap { display: flex; flex-direction: column; width: 100%; height: 100vh; }

#jitsi-container { flex: 1; width: 100%; background: #0a0a0a; min-height: 0; }

/* ── Top bar ──────────────────────────────────────── */
.topbar {
    flex-shrink: 0;
    background: linear-gradient(135deg, #0D1A12, #15281F);
    border-bottom: 1px solid rgba(255,255,255,.07);
    padding: 10px 20px;
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; color: #fff; z-index: 100;
}

.tb-left  { display: flex; align-items: center; gap: 14px; flex: 1; min-width: 0; }
.tb-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }

.back-btn {
    display: inline-flex; align-items: center; gap: 5px;
    color: rgba(255,255,255,.45); font-size: 12px; text-decoration: none;
    transition: color .15s; flex-shrink: 0;
}
.back-btn:hover { color: rgba(255,255,255,.85); }

.session-name {
    font-family: 'Fraunces', serif; font-size: 15px; font-weight: 700;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

.live-badge {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(16,185,129,.15); border: 1px solid rgba(16,185,129,.3);
    color: #10B981; padding: 3px 10px; border-radius: 999px;
    font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em;
    flex-shrink: 0;
}
.live-badge .pulse {
    width: 6px; height: 6px; border-radius: 50%; background: #10B981;
    animation: pulse 1.6s infinite;
}
@keyframes pulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:.4; transform:scale(.7); } }

.mod-badge {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(234,179,8,.13); border: 1px solid rgba(234,179,8,.28);
    color: #facc15; padding: 3px 10px; border-radius: 999px;
    font-size: 10px; font-weight: 700; flex-shrink: 0;
}

.rec-badge {
    display: none; align-items: center; gap: 5px;
    background: rgba(232,82,42,.13); border: 1px solid rgba(232,82,42,.3);
    color: #ff7849; padding: 3px 10px; border-radius: 999px;
    font-size: 10px; font-weight: 700;
    animation: recpulse 1.5s infinite;
}
.rec-badge.on { display: inline-flex; }
@keyframes recpulse { 0%,100%{opacity:1;} 50%{opacity:.45;} }

/* ── Timer panel ─────────────────────────────────── */
.timer-panel {
    display: none; align-items: center; gap: 14px;
    background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1);
    border-radius: 10px; padding: 7px 14px; flex-shrink: 0;
}
.timer-panel.show { display: flex; }

.tp-block { text-align: center; }
.tp-lbl { font-size: 9px; text-transform: uppercase; letter-spacing: .08em; color: rgba(255,255,255,.32); }
.tp-val { font-family: monospace; font-size: 14px; font-weight: 700; color: #fff; margin-top: 1px; }
.tp-val.danger { color: var(--coral); }

#prog-wrap { width: 72px; height: 4px; background: rgba(255,255,255,.1); border-radius: 999px; overflow: hidden; }
#prog-bar  { height: 100%; background: var(--mint); border-radius: 999px; transition: width .9s linear, background .5s; }

/* ── Attendance row ──────────────────────────────── */
.att-row {
    display: flex; align-items: center; gap: 10px;
    font-size: 11px; color: rgba(255,255,255,.4); flex-shrink: 0;
}
.att-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: #4b5563; display: inline-block; margin-right: 3px;
    transition: background .4s;
}
.att-dot.on { background: var(--mint); }

/* ── Code pill ───────────────────────────────────── */
.code-pill {
    background: rgba(10,100,188,.18); border: 1px solid rgba(10,100,188,.3);
    padding: 4px 12px; border-radius: 8px;
    font-size: 11px; font-weight: 700; font-family: monospace; letter-spacing: .06em;
    color: #93c5fd; cursor: pointer; transition: background .15s; flex-shrink: 0;
}
.code-pill:hover { background: rgba(10,100,188,.3); }
.code-pill.copied { background: rgba(16,185,129,.18); border-color: rgba(16,185,129,.3); color: #6ee7b7; }

/* ── Exit button ─────────────────────────────────── */
.exit-btn {
    background: rgba(232,82,42,.13); border: 1px solid rgba(232,82,42,.28);
    color: #ff7849; padding: 7px 16px; border-radius: 999px;
    font-size: 12px; font-weight: 600; cursor: pointer;
    transition: all .15s; display: inline-flex; align-items: center; gap: 5px;
    font-family: inherit;
}
.exit-btn:hover { background: rgba(232,82,42,.22); }

/* ── Closing banner ──────────────────────────────── */
#closing-banner {
    display: none; position: fixed; top: 0; left: 0; right: 0; z-index: 9998;
    background: linear-gradient(90deg, var(--coral), #c74020);
    color: #fff; padding: 9px 20px; font-size: 12px; font-weight: 700;
    align-items: center; justify-content: center; gap: 10px;
}
#closing-banner.show { display: flex; }

/* ── Toast ───────────────────────────────────────── */
.toast {
    position: fixed; bottom: 24px; right: 24px; z-index: 9999;
    padding: 11px 18px; border-radius: 12px; font-size: 13px; font-weight: 600;
    color: #fff; max-width: 340px;
    box-shadow: 0 4px 20px rgba(0,0,0,.3);
    animation: fadein .2s ease;
}
@keyframes fadein { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }

/* ── Participant count ───────────────────────────── */
.part-count {
    font-size: 11px; color: rgba(255,255,255,.4);
    display: flex; align-items: center; gap: 4px; flex-shrink: 0;
}
</style>
</head>
<body>

@php
    $user         = auth()->user();
    $appId        = env('JITSI_APP_ID', '');
    $roomName     = $groupSession->meeting_room ?? ('MwalimuGroup-' . $groupSession->id);
    $scheduledSec = $groupSession->duration_minutes * 60;
    $closingBuf   = 300; // 5 min warning
    $backUrl      = $isModerator
        ? route('tutor.group-sessions.show', $groupSession)
        : route('group-sessions.my-sessions');
@endphp

{{-- Closing-time banner --}}
<div id="closing-banner">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <span>Session ending soon — closing buffer active.</span>
    @if($isModerator)
    <button onclick="document.getElementById('closing-banner').classList.remove('show')"
            style="background:rgba(255,255,255,.18);border:none;color:#fff;padding:3px 12px;border-radius:999px;cursor:pointer;font-size:11px;font-weight:600;font-family:inherit;">
        Dismiss
    </button>
    @endif
</div>

<div class="room-wrap">

    {{-- ── Top bar ─────────────────────────────────────────── --}}
    <div class="topbar">

        <div class="tb-left">
            <a href="{{ $backUrl }}" class="back-btn">
                <i class="fa-solid fa-chevron-left"></i> Back
            </a>

            <div class="live-badge"><span class="pulse"></span> Live</div>

            <span class="session-name">
                {{ $groupSession->title }}
                @if($groupSession->subject) · {{ $groupSession->subject->name }} @endif
            </span>

            @if($isModerator)
            <span class="mod-badge"><i class="fa-solid fa-shield-halved"></i> Host</span>
            @endif

            <span class="rec-badge" id="recBadge">
                <i class="fa-solid fa-circle" style="font-size:7px;color:#ff4444;"></i> REC
            </span>
        </div>

        <div class="tb-right">

            {{-- Session timer --}}
            <div class="timer-panel" id="timerPanel">
                <div class="tp-block">
                    <div class="tp-lbl">Elapsed</div>
                    <div class="tp-val" id="elapsed">00:00</div>
                </div>
                <div class="tp-block">
                    <div class="tp-lbl">Remaining</div>
                    <div class="tp-val" id="remaining">--:--</div>
                </div>
                <div class="tp-block">
                    <div id="prog-wrap"><div id="prog-bar" style="width:0%"></div></div>
                    <div class="tp-lbl" id="timerStatus" style="margin-top:4px;">Waiting</div>
                </div>
            </div>

            {{-- Attendance indicators --}}
            <div class="att-row">
                <span><span class="att-dot" id="dotTutor"></span>Tutor</span>
                <span>
                    <span class="att-dot" id="dotStudents"></span>
                    <span id="stuCount">0</span> student<span id="stuPlural">s</span>
                </span>
            </div>

            {{-- Participant counter (from Jitsi events) --}}
            <div class="part-count" id="partCount" style="display:none;">
                <i class="fa-solid fa-users"></i>
                <span id="partNum">1</span>
            </div>

            {{-- Session code --}}
            @if($sessionCode)
            <button class="code-pill" id="codePill" onclick="copyCode()" title="Click to copy your session code">
                {{ $sessionCode }}
            </button>
            @endif

            <button class="exit-btn" onclick="leaveRoom()">
                <i class="fa-solid fa-phone-slash"></i> Leave
            </button>
        </div>
    </div>

    {{-- ── Jitsi container ─────────────────────────────────── --}}
    <div id="jitsi-container"></div>

</div>{{-- /room-wrap --}}

<script src="https://8x8.vc/{{ $appId }}/external_api.js"></script>
<script>
// ── Config ───────────────────────────────────────────────────────────────────
const DOMAIN          = '8x8.vc';
const ROOM_NAME       = '{{ $appId }}/{{ $roomName }}';
const DISPLAY_NAME    = '{{ addslashes($user->name) }}{{ $isModerator ? " (Host)" : "" }}';
const USER_EMAIL      = '{{ addslashes($user->email) }}';
const IS_MODERATOR    = {{ $isModerator ? 'true' : 'false' }};
const SESSION_CODE    = '{{ $sessionCode ?? "" }}';
const SCHEDULED_SECS  = {{ $scheduledSec }};
const CLOSING_BUF     = {{ $closingBuf }};
const BACK_URL        = '{{ $backUrl }}';
const CSRF            = document.querySelector('meta[name=csrf-token]').content;

const JOINED_URL      = '{{ route("group-sessions.joined",    $groupSession) }}';
const ATTEND_URL      = '{{ route("group-sessions.attendance", $groupSession) }}';

// ── Timer state ───────────────────────────────────────────────────────────────
let timerInterval  = null;
let elapsedSecs    = 0;
let sessionStarted = false;
let closingShown   = false;
let hasJoined      = false;
let participantCnt = 1;

function fmt(s) {
    const h = Math.floor(s / 3600), m = Math.floor((s % 3600) / 60), sec = s % 60;
    if (h > 0) return `${h}h ${String(m).padStart(2,'0')}m ${String(sec).padStart(2,'0')}s`;
    return `${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`;
}

function tickTimer() {
    elapsedSecs++;
    const rem = Math.max(0, SCHEDULED_SECS - elapsedSecs);

    document.getElementById('elapsed').textContent   = fmt(elapsedSecs);
    document.getElementById('remaining').textContent = fmt(rem);

    const pct = Math.min(100, (elapsedSecs / SCHEDULED_SECS) * 100);
    const bar = document.getElementById('prog-bar');
    bar.style.width = pct + '%';
    bar.style.background = rem <= CLOSING_BUF ? '#E8522A' : (rem <= SCHEDULED_SECS * .2 ? '#EEBE15' : '#10B981');

    const remEl = document.getElementById('remaining');
    remEl.classList.toggle('danger', rem <= CLOSING_BUF);

    if (rem <= CLOSING_BUF && !closingShown) {
        closingShown = true;
        document.getElementById('closing-banner').classList.add('show');
        toast('⏰ Session ending in ' + fmt(rem) + ' — wrap up soon.', 'warning');
    }

    if (rem === 0) clearInterval(timerInterval);
}

function startTimer(serverElapsed) {
    if (timerInterval) return;
    elapsedSecs = serverElapsed || 0;
    document.getElementById('timerPanel').classList.add('show');
    document.getElementById('timerStatus').textContent = 'Session running';
    timerInterval = setInterval(tickTimer, 1000);
}

// ── Attendance polling ────────────────────────────────────────────────────────
function pollAttendance() {
    fetch(ATTEND_URL, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(d => {
            document.getElementById('dotTutor').classList.toggle('on', d.tutor_joined);
            document.getElementById('dotStudents').classList.toggle('on', d.students_joined > 0);
            document.getElementById('stuCount').textContent   = d.students_joined;
            document.getElementById('stuPlural').textContent  = d.students_joined === 1 ? '' : 's';

            if (d.both_joined && !sessionStarted) {
                sessionStarted = true;
                startTimer(d.elapsed_seconds || 0);
            } else if (sessionStarted && timerInterval) {
                // Drift correction (>5 s off)
                if (Math.abs(d.elapsed_seconds - elapsedSecs) > 5) elapsedSecs = d.elapsed_seconds;
            }
        })
        .catch(() => {});
}

// ── Copy code ─────────────────────────────────────────────────────────────────
function copyCode() {
    if (!SESSION_CODE) return;
    navigator.clipboard.writeText(SESSION_CODE).then(() => {
        const p = document.getElementById('codePill');
        if (p) {
            p.classList.add('copied');
            setTimeout(() => p.classList.remove('copied'), 2000);
        }
    }).catch(() => toast('Code: ' + SESSION_CODE, 'info'));
}

// ── Toast ─────────────────────────────────────────────────────────────────────
function toast(msg, type) {
    const el = document.createElement('div');
    el.className = 'toast';
    el.style.background = type === 'warning' ? '#EEBE15' : (type === 'error' ? '#E8522A' : '#10B981');
    el.style.color = type === 'warning' ? '#15281F' : '#fff';
    el.textContent = msg;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 5500);
}

// ── Leave ─────────────────────────────────────────────────────────────────────
function leaveRoom() {
    try { api.executeCommand('hangup'); } catch(e) {}
    setTimeout(() => window.location.href = BACK_URL, 500);
}

// ── Jitsi init ────────────────────────────────────────────────────────────────
const options = {
    roomName: ROOM_NAME,
    width:    '100%',
    height:   '100%',
    parentNode: document.getElementById('jitsi-container'),

    @if(config('services.jitsi.use_jwt') && !empty(env('JITSI_APP_ID')) && !empty(env('JITSI_APP_SECRET')))
    @php
        try {
            $jitsiSvc = app(\App\Services\JitsiService::class);
            $gsJwt = $jitsiSvc->makeJWT(
                $roomName,
                $user->name . ($isModerator ? ' (Host)' : ''),
                $isModerator,
                ['email' => $user->email]
            );
        } catch (\Throwable $e) {
            $gsJwt = null;
        }
    @endphp
    @if(!empty($gsJwt))
    jwt: '{{ $gsJwt }}',
    @endif
    @endif

    configOverwrite: {
        startWithAudioMuted: !IS_MODERATOR,
        startWithVideoMuted: false,
        enableWelcomePage:   false,
        prejoinPageEnabled:  false,
        enableNoisyMicDetection: true,
        // ── Video quality ───────────────────────────────────────
        resolution: 720,
        constraints: {
            video: {
                height:    { ideal: 720, max: 720, min: 360 },
                width:     { ideal: 1280, max: 1280, min: 640 },
                frameRate: { ideal: 30, max: 30 },
            },
        },
        maxFullResolutionParticipants: -1,
        videoQuality: {
            codecPreferenceOrder:       ['VP8', 'H264', 'VP9'],
            mobileCodecPreferenceOrder: ['VP8', 'H264'],
            enableAdaptiveMode: true,
        },
        // ── Collaboration ───────────────────────────────────────
        fileRecordingsEnabled: IS_MODERATOR,
        liveStreamingEnabled:  false,
        whiteboardEnabled:     true,
        etherpadEnabled:       true,
        breakoutRoomsEnabled:  IS_MODERATOR,
        // ── Desktop sharing ─────────────────────────────────────
        desktopSharingChromeDesktopSources: ['screen', 'window', 'tab'],
        desktopSharingChromeSources:        ['screen', 'window', 'tab'],
        desktopSharingChromeExtId: null,
        desktopSharingFirefoxDisabled: false,
    },

    interfaceConfigOverwrite: {
        SHOW_JITSI_WATERMARK:         false,
        SHOW_WATERMARK_FOR_GUESTS:    false,
        SHOW_BRAND_WATERMARK:         false,
        SHOW_POWERED_BY:              false,
        DEFAULT_BACKGROUND:           '#0D1A12',
        DISPLAY_WELCOME_PAGE_CONTENT: false,
        DISPLAY_INVITE_MORE_HEADER:   false,
        HIDE_INVITE_MORE_HEADER:      true,
        MOBILE_APP_PROMO:             false,
        SHOW_CHROME_EXTENSION_BANNER: false,
        RECENT_LIST_ENABLED:          false,
        VIDEO_LAYOUT_FIT:             'both',
        TOOLBAR_BUTTONS: IS_MODERATOR ? [
            'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
            'fodeviceselection', 'hangup', 'chat', 'recording', 'etherpad',
            'sharedvideo', 'settings', 'raisehand', 'videoquality', 'filmstrip',
            'stats', 'tileview', 'security', 'toggle-camera',
            'whiteboard', 'participants-pane', 'mute-everyone', 'mute-video-everyone',
        ] : [
            'microphone', 'camera', 'closedcaptions', 'fullscreen',
            'fodeviceselection', 'hangup', 'chat', 'raisehand',
            'videoquality', 'filmstrip', 'settings', 'tileview', 'toggle-camera',
        ],
    },

    userInfo: { displayName: DISPLAY_NAME, email: USER_EMAIL },
};

const api = new JitsiMeetExternalAPI(DOMAIN, options);

// ── Event listeners ───────────────────────────────────────────────────────────

api.addEventListener('videoConferenceJoined', () => {
    // Request 720p
    api.executeCommand('setVideoQuality', 720);

    if (!hasJoined) {
        hasJoined = true;

        fetch(JOINED_URL, {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' },
        })
        .then(r => r.json())
        .then(d => {
            if (d.both_joined) startTimer(0);
        });

        pollAttendance();
        setInterval(pollAttendance, 10000);
    }
});

api.addEventListener('participantJoined', (p) => {
    participantCnt++;
    document.getElementById('partNum').textContent = participantCnt;
    document.getElementById('partCount').style.display = 'flex';
    pollAttendance();
});

api.addEventListener('participantLeft', (p) => {
    participantCnt = Math.max(1, participantCnt - 1);
    document.getElementById('partNum').textContent = participantCnt;
    pollAttendance();
});

api.addEventListener('recordingStatusChanged', (e) => {
    document.getElementById('recBadge').classList.toggle('on', !!e.on);
});

api.addEventListener('readyToClose', () => {
    window.location.href = BACK_URL;
});

api.addEventListener('errorOccurred', (e) => {
    console.error('Jitsi error:', e);
});

// ── Cleanup ───────────────────────────────────────────────────────────────────
window.addEventListener('beforeunload', () => { try { api.dispose(); } catch(e) {} });
</script>
</body>
</html>
