<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Session Room â‚¬" Mwalimu</title>
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

/* Timer panel */
#timer-panel{display:none;align-items:center;gap:16px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:8px 16px;flex-shrink:0;}
#timer-panel .tp-label{font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.35);}
#timer-panel .tp-val{font-family:monospace;font-size:15px;font-weight:700;color:#fff;}
#timer-panel .tp-val.remaining-danger{color:#E8522A;}
#progress-bar-wrap{width:80px;height:4px;background:rgba(255,255,255,.12);border-radius:999px;overflow:hidden;}
#progress-bar{height:100%;background:#10B981;border-radius:999px;transition:width .8s linear,background .5s;}

/* Attendance dots */
.att-dot{width:8px;height:8px;border-radius:50%;background:#6b7280;display:inline-block;margin-right:4px;transition:background .4s;}

/* Closing buffer banner */
#closing-banner{display:none;position:fixed;top:0;left:0;right:0;z-index:9998;background:linear-gradient(90deg,#E8522A,#c74020);color:#fff;padding:10px 24px;font-size:13px;font-weight:700;align-items:center;justify-content:center;gap:12px;}
</style>
</head>
<body>

<div class="room-container">

 {{-- Closing-buffer banner (hidden until 5 min left) --}}
 <div id="closing-banner">
 <i class="fas fa-exclamation-triangle"></i>
 <span>Session ending soon â‚¬" closing buffer active. You may finish your lesson.</span>
 @if(auth()->id() === $booking->tutor_id)
 <button onclick="document.getElementById('closing-banner').style.display='none'" style="background:rgba(255,255,255,.2);border:none;color:#fff;padding:4px 14px;border-radius:999px;cursor:pointer;font-size:12px;font-weight:600;">Dismiss</button>
 @endif
 </div>

 {{-- Session Header --}}
 <div class="session-header">
 <div class="left">
 <a href="{{ route('bookings.show', $booking->uuid) }}" class="back-btn">
 <span style="font-size:inherit;">←</span> Back
 </a>
 <div class="session-title">
 {{ $booking->subject ? $booking->subject->name : 'Session' }} · {{ $booking->duration_minutes }} min
 </div>
 <div class="session-meta">
 <span class="dot"></span>
 <span class="time">
 @if(auth()->id() === $booking->tutor_id)
 Student: {{ $booking->student->name }}
 @else
 Tutor: {{ $booking->tutor->name }}
 @endif
 </span>
 @if(auth()->id() === $booking->tutor_id)
 <span class="moderator-badge"><i class="fas fa-shield-alt"></i> MODERATOR</span>
 @endif
 <span class="recording-indicator" id="recIndicator">
 <i class="fas fa-circle" style="color:#ff4444;"></i> REC
 </span>
 </div>
 </div>

 <div style="display:flex;align-items:center;gap:12px;flex-shrink:0;">

 {{-- Teaching timer (shown once both parties join) --}}
 <div id="timer-panel">
 <div>
 <div class="tp-label">Teaching</div>
 <div class="tp-val" id="elapsed-timer">00:00</div>
 </div>
 <div>
 <div class="tp-label">Remaining</div>
 <div class="tp-val" id="remaining-timer">--:--</div>
 </div>
 <div>
 <div class="tp-label" id="timer-status">Waitingâ‚¬Â¦</div>
 <div id="progress-bar-wrap"><div id="progress-bar" style="width:0%;"></div></div>
 </div>
 </div>

 {{-- Attendance dots --}}
 <div style="font-size:11px;color:rgba(255,255,255,.45);display:flex;align-items:center;gap:10px;flex-shrink:0;">
 <span><span class="att-dot" id="tutor-dot"></span>Tutor</span>
 <span><span class="att-dot" id="student-dot"></span>Student</span>
 </div>

 <div style="text-align:right;">
 <p style="font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.35);margin-bottom:2px;">Session Code</p>
 <p style="font-size:13px;font-family:monospace;font-weight:700;color:#fff;letter-spacing:.05em;">
 @if(auth()->id() === $booking->tutor_id)
 {{ $booking->session_code_tutor }}
 @else
 {{ $booking->session_code_student }}
 @endif
 </p>
 </div>
 <button onclick="copySessionCode()" class="copy-btn" id="copyBtn" title="Copy session code">
 <i class="fas fa-copy"></i>
 </button>
 <button onclick="leaveSession()" class="exit-btn">
 <i class="fas fa-phone"></i> Leave
 </button>
 </div>
 </div>

 {{-- Jitsi Meet Container --}}
 <div id="jitsi-container"></div>
</div>

<script src="https://meet.jit.si/external_api.js"></script>
<script>
 const domain = 'meet.jit.si';
 const meetingRoom = '{{ $booking->meeting_room }}';
 const userDisplayName = '{{ addslashes(auth()->user()->name) }}';
 const userEmail = '{{ addslashes(auth()->user()->email) }}';
 const isModerator = {{ auth()->id() === $booking->tutor_id ? 'true' : 'false' }};
 const sessionCode = '{{ auth()->id() === $booking->tutor_id ? $booking->session_code_tutor : $booking->session_code_student }}';
 const bookingUrl = '{{ route("bookings.show", $booking->uuid) }}';
 const attendanceUrl = '{{ route("bookings.attendance", $booking->uuid) }}';
 const joinedUrl = '{{ route("bookings.joined", $booking->uuid) }}';
 const leftUrl = '{{ route("bookings.left", $booking->uuid) }}';
 const csrfToken = document.querySelector('meta[name=csrf-token]').content;

 // Scheduled session parameters
 const scheduledSeconds = {{ $booking->duration_minutes * 60 }};
 const closingBufferSeconds = 300; // 5 min
 @php
 $sessionStartTs = $booking->scheduled_at->timestamp;
 @endphp
 const sessionStartTs = {{ $sessionStartTs }};

 // "â‚¬"â‚¬ Timer state "â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬
 let teachingTimer = null;
 let elapsedSeconds = 0;
 let bothJoined = false;
 let hasJoined = false;
 let inClosingBuffer = false;

 function formatTime(secs) {
 const h = Math.floor(secs / 3600);
 const m = Math.floor((secs % 3600) / 60);
 const s = secs % 60;
 if (h > 0) return `${h}h ${String(m).padStart(2,'0')}m ${String(s).padStart(2,'0')}s`;
 return `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
 }

 function updateTimerUI() {
 const remaining = Math.max(0, scheduledSeconds - elapsedSeconds);
 const elEl = document.getElementById('elapsed-timer');
 const remEl = document.getElementById('remaining-timer');
 const progEl = document.getElementById('progress-bar');

 if (elEl) elEl.textContent = formatTime(elapsedSeconds);
 if (remEl) remEl.textContent = formatTime(remaining);
 if (progEl) {
 const pct = Math.min(100, (elapsedSeconds / scheduledSeconds) * 100);
 progEl.style.width = pct + '%';
 // Colour: green " ' amber (last 20%) " ' red (closing buffer)
 if (remaining <= closingBufferSeconds) {
 progEl.style.background = '#E8522A';
 } else if (remaining <= scheduledSeconds * 0.2) {
 progEl.style.background = '#EEBE15';
 } else {
 progEl.style.background = '#10B981';
 }
 }

 // Show closing-buffer warning
 if (remaining <= closingBufferSeconds && !inClosingBuffer) {
 inClosingBuffer = true;
 showToast('ÂÂ° Session ending in ' + formatTime(remaining) + ' â‚¬" closing buffer started.', 'warning');
 document.getElementById('closing-banner').style.display = 'flex';
 }

 // Auto-end when remaining hits 0
 if (remaining === 0 && bothJoined) {
 clearInterval(teachingTimer);
 // Notify backend the session time has ended
 fetch(leftUrl, {
 method: 'POST',
 headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
 });
 }
 }

 function startTeachingTimer(serverElapsed) {
 if (teachingTimer) return; // already running
 elapsedSeconds = serverElapsed || 0;
 updateTimerUI();
 teachingTimer = setInterval(() => {
 elapsedSeconds++;
 updateTimerUI();
 }, 1000);
 document.getElementById('timer-panel').style.display = 'flex';
 document.getElementById('timer-status').textContent = 'Teaching timer runningâ‚¬Â¦';
 }

 // Poll attendance status every 10 s
 function pollAttendance() {
 fetch(attendanceUrl)
 .then(r => r.json())
 .then(data => {
 const tutorDot = document.getElementById('tutor-dot');
 const stuDot = document.getElementById('student-dot');

 if (tutorDot) tutorDot.style.background = data.tutor_joined ? '#10B981' : '#6b7280';
 if (stuDot) stuDot.style.background = data.student_joined ? '#10B981' : '#6b7280';

 if (data.both_joined && !bothJoined) {
 bothJoined = true;
 startTeachingTimer(data.elapsed_seconds);
 } else if (bothJoined && teachingTimer) {
 // Keep elapsed in sync with server (drift correction)
 if (Math.abs(data.elapsed_seconds - elapsedSeconds) > 5) {
 elapsedSeconds = data.elapsed_seconds;
 }
 }
 })
 .catch(() => {}); // silently ignore
 }

 // Toast helper
 function showToast(msg, type) {
 const t = document.createElement('div');
 t.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;padding:12px 20px;border-radius:12px;font-size:14px;font-weight:600;color:#fff;max-width:360px;box-shadow:0 4px 20px rgba(0,0,0,.3);';
 t.style.background = type === 'warning' ? '#EEBE15' : (type === 'error' ? '#E8522A' : '#10B981');
 t.style.color = type === 'warning' ? '#15281F' : '#fff';
 t.textContent = msg;
 document.body.appendChild(t);
 setTimeout(() => t.remove(), 6000);
 }

 const options = {
 roomName: meetingRoom,
 width: '100%',
 height: '100%',
 parentNode: document.querySelector('#jitsi-container'),
 @if(app(\App\Services\JitsiService::class)->isConfigured() && env('JITSI_USE_JWT'))
 jwt: '{{ $jitsiJwt ?? "" }}',
 @endif
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
 desktopSharingChromeDesktopSources: ['screen', 'window', 'tab'],
 desktopSharingChromeSources: ['screen', 'window', 'tab'],
 desktopSharingChromeExtId: null,
 desktopSharingFirefoxDisabled: false,
 fileRecordingsEnabled: true,
 liveStreamingEnabled: false,
 whiteboardEnabled: true,
 etherpadEnabled: true,
 breakoutRoomsEnabled: true,
 },
 interfaceConfigOverwrite: {
 SHOW_JITSI_WATERMARK: false,
 SHOW_WATERMARK_FOR_GUESTS: false,
 SHOW_BRAND_WATERMARK: false,
 SHOW_POWERED_BY: false,
 DEFAULT_BACKGROUND: '#15281F',
 DISPLAY_WELCOME_PAGE_CONTENT: false,
 DISPLAY_INVITE_MORE_HEADER: false,
 HIDE_INVITE_MORE_HEADER: true,
 MOBILE_APP_PROMO: false,
 SHOW_CHROME_EXTENSION_BANNER: false,
 RECENT_LIST_ENABLED: false,
 SETTINGS_SECTIONS: ['devices', 'language', 'moderator', 'profile', 'calendar'],
 VIDEO_LAYOUT_FIT: 'both',
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

 // "â‚¬"â‚¬ Joined event "â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬"â‚¬
 api.addEventListener('videoConferenceJoined', () => {
 if (!hasJoined) {
 hasJoined = true;
 fetch(joinedUrl, {
 method: 'POST',
 headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
 })
 .then(r => r.json())
 .then(data => {
 if (data.both_joined) {
 startTeachingTimer(0);
 }
 });
 pollAttendance();
 setInterval(pollAttendance, 10000);
 }
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

 // Participant joined/left events
 api.addEventListener('participantJoined', () => { pollAttendance(); });
 api.addEventListener('participantLeft', () => {
 fetch(leftUrl, {
 method: 'POST',
 headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
 });
 pollAttendance();
 });

 // Leave handling
 api.addEventListener('readyToClose', () => {
 fetch(leftUrl, {
 method: 'POST',
 headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
 }).finally(() => { window.location.href = bookingUrl; });
 });
 api.addEventListener('hangup', () => {
 window.location.href = bookingUrl;
 });

 api.addEventListener('errorOccurred', (error) => {
 console.error('Jitsi error:', error);
 });

 // Copy session code
 function copySessionCode() {
 navigator.clipboard.writeText(sessionCode).then(() => {
 const btn = document.getElementById('copyBtn');
 btn.innerHTML = '<i class="fas fa-check-circle"></i>';
 btn.classList.add('copied');
 setTimeout(() => {
 btn.innerHTML = '<i class="fas fa-copy"></i>';
 btn.classList.remove('copied');
 }, 2000);
 }).catch(() => showToast('Failed to copy: ' + sessionCode, 'error'));
 }

 // Leave session
 function leaveSession() {
 try { api.executeCommand('hangup'); } catch(e) {}
 setTimeout(() => { window.location.href = bookingUrl; }, 500);
 }

 // Cleanup
 window.addEventListener('beforeunload', () => {
 try { api.dispose(); } catch(err) {}
 });
</script>

</body>
</html>
