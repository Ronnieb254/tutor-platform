<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Session #{{ substr($booking->uuid,0,8) }} – Mwalimu</title>
<style>
.session-hero{background:linear-gradient(135deg,#111C16,#15281F);padding:48px 0;position:relative;overflow:hidden;}
.session-hero::before{content:'';position:absolute;top:-60px;right:-60px;width:260px;height:260px;background:rgba(109,40,217,.1);border-radius:50%;pointer-events:none;}
.code-box{border:2px solid rgba(109,40,217,.3);border-radius:20px;padding:24px;text-align:center;background:rgba(109,40,217,.06);}
.code-text{font-family:monospace;font-size:40px;font-weight:800;color:#6D28D9;letter-spacing:.12em;line-height:1;}
.join-btn{display:inline-flex;align-items:center;gap:10px;padding:16px 40px;border-radius:999px;font-size:16px;font-weight:700;cursor:pointer;transition:all .2s;border:none;font-family:inherit;text-decoration:none;}
.join-btn-live{background:#10B981;color:#fff;box-shadow:0 0 0 0 rgba(16,185,129,.4);animation:pulse-green 2s infinite;}
@keyframes pulse-green{0%,100%{box-shadow:0 0 0 0 rgba(16,185,129,.4);}50%{box-shadow:0 0 0 14px rgba(16,185,129,0);}}
.status-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 16px;border-radius:999px;font-size:13px;font-weight:700;}
/* Jitsi container */
#jitsi-container{width:100%;border-radius:20px;overflow:hidden;background:#111C16;position:relative;}
.jitsi-placeholder{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 20px;text-align:center;}
/* Chat */
.chat-bubble{max-width:75%;padding:10px 15px;border-radius:16px;font-size:14px;line-height:1.6;}
.chat-mine{background:#6D28D9;color:#fff;border-radius:16px 4px 16px 16px;margin-left:auto;}
.chat-theirs{background:#F4F6F4;color:#15281F;border-radius:4px 16px 16px 16px;}
/* Timeline */
.timeline-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;margin-top:5px;}
.timeline-line{width:2px;background:rgba(21,40,31,.1);flex:1;margin:4px 0;}
/* Countdown */
#countdown{font-family:'Fraunces',serif;font-size:32px;font-weight:800;color:#6D28D9;letter-spacing:.05em;}
</style>
</head>
<body>
@php
use Carbon\Carbon;
$isStudent = auth()->id() === $booking->student_id;
$isTutor   = auth()->id() === $booking->tutor_id;
$myCode    = $isTutor ? $booking->session_code_tutor : $booking->session_code_student;
$otherName = $isTutor ? $booking->student->name : $booking->tutor->name;
$sessionStart   = $booking->scheduled_at;
$sessionEnd     = $sessionStart->copy()->addMinutes($booking->duration_minutes);
$minutesUntil   = now()->diffInMinutes($sessionStart, false);
$canJoin        = $booking->canJoin();
$sessionPrices  = [15=>'Quick Help',30=>'Concept Check',45=>'Guided Practice',60=>'Deep Dive'];
$sessionName    = $sessionPrices[$booking->duration_minutes] ?? 'Session';
$jitsiRoom      = $roomName ?? $booking->meeting_room ?? ('Mwalimu-session-' . $booking->uuid);
$jitsiDomain    = config('jitsi.domain','meet.jit.si');
@endphp

@include('partials.nav')

{{-- HERO --}}
<div class="session-hero">
    <div style="max-width:1360px;margin:0 auto;padding:0 24px;position:relative;z-index:1;">
        <a href="{{ route('dashboard') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:rgba(255,255,255,.5);text-decoration:none;margin-bottom:18px;transition:color .15s;" onmouseover="this.style.color='rgba(255,255,255,.85)'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
            <i class="fa-solid fa-arrow-left fa-xs"></i> Dashboard
        </a>
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:24px;flex-wrap:wrap;">
            <div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;flex-wrap:wrap;">
                    @php
                    $statusConfig = [
                        'pending'   => ['bdg amber','fa-clock','Pending Confirmation'],
                        'confirmed' => ['bdg blue','fa-circle-check','Confirmed'],
                        'completed' => ['bdg green','fa-flag-checkered','Completed'],
                        'cancelled' => ['bdg red','fa-ban','Cancelled'],
                    ];
                    [$bclass,$bicon,$blabel] = $statusConfig[$booking->status] ?? ['bdg gray','fa-circle','Unknown'];
                    @endphp
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:999px;font-size:12px;font-weight:700;background:rgba(255,255,255,.15);color:#fff;">
                        <i class="fa-solid fa-{{ $bicon }} fa-xs"></i> {{ $blabel }}
                    </span>
                    @if($canJoin && $booking->status === 'confirmed')
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:999px;font-size:12px;font-weight:700;background:rgba(16,185,129,.2);color:#4ADE80;border:1px solid rgba(16,185,129,.3);">
                        <span style="width:7px;height:7px;background:#4ADE80;border-radius:50%;animation:pulse-green 1.5s infinite;"></span>
                        Live Now
                    </span>
                    @endif
                    @if($booking->tutor?->tutorProfile?->speedy_verification_paid ?? false)
                    <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:999px;font-size:11px;font-weight:700;background:rgba(212,160,23,.2);color:#FCD34D;border:1px solid rgba(212,160,23,.25);">
                        <i class="fa-solid fa-star fa-xs"></i> TSC Verified
                    </span>
                    @endif
                </div>
                <h1 style="font-family:'Fraunces',serif;font-size:clamp(24px,4vw,36px);color:#fff;margin-bottom:8px;">
                    {{ $sessionName }} with {{ $otherName }}
                </h1>
                <div style="display:flex;flex-wrap:wrap;gap:16px;font-size:14px;color:rgba(255,255,255,.6);">
                    <span><i class="fa-solid fa-calendar fa-xs" style="color:#6D28D9;margin-right:5px;"></i>{{ $sessionStart->format('D, M j, Y') }}</span>
                    <span><i class="fa-solid fa-clock fa-xs" style="color:#6D28D9;margin-right:5px;"></i>{{ $sessionStart->format('g:i a') }} – {{ $sessionEnd->format('g:i a') }} EAT</span>
                    <span><i class="fa-solid fa-hourglass-half fa-xs" style="color:#6D28D9;margin-right:5px;"></i>{{ $booking->duration_minutes }} minutes</span>
                    @if($booking->subject)<span><i class="fa-solid fa-book-open fa-xs" style="color:#6D28D9;margin-right:5px;"></i>{{ $booking->subject->name }}</span>@endif
                    <span><i class="fa-solid fa-money-bill fa-xs" style="color:#D4A017;margin-right:5px;"></i>KSh {{ number_format($booking->total_amount) }}</span>
                </div>
            </div>
            <div style="text-align:center;flex-shrink:0;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.4);margin-bottom:8px;">Session ID</div>
                <div style="font-family:monospace;font-size:15px;color:rgba(255,255,255,.6);">#{{ substr($booking->uuid,0,8) }}</div>
            </div>
        </div>
    </div>
</div>

{{-- MAIN LAYOUT --}}
<div style="max-width:1360px;margin:0 auto;padding:32px 24px 64px;">
    <div style="display:grid;grid-template-columns:1fr;gap:24px;" class="session-grid">

        {{-- LEFT COLUMN --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- ── SESSION JOIN PANEL ── --}}
            @if($booking->status === 'confirmed')
            <div style="background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.08);overflow:hidden;box-shadow:0 4px 24px rgba(21,40,31,.06);" x-data="{
                codeInput: '{{ $myCode }}',
                codeError: '',
                verified: false,
                jitsiLoaded: false,
                launching: false,
                myCode: '{{ $myCode }}',
                verify() {
                    const entered = this.codeInput.trim().toUpperCase();
                    if (!entered) { this.codeError = 'Please enter your session code'; return; }
                    if (entered !== this.myCode.toUpperCase()) {
                        this.codeError = 'Incorrect code. Check your confirmation email.';
                        return;
                    }
                    this.codeError = '';
                    this.verified = true;
                    this.$nextTick(() => this.launchJitsi());
                },
                launchJitsi() {
                    this.launching = true;
                    setTimeout(() => {
                        const domain = '{{ $jitsiDomain }}';
                        const room   = '{{ $jitsiRoom }}';
                        const container = document.getElementById('jitsi-container');
                        if (!container) return;

                        // Remove placeholder
                        const placeholder = document.getElementById('jitsi-placeholder');
                        if (placeholder) placeholder.remove();

                        const options = {
                            roomName: room,
                            parentNode: container,
                            width: '100%',
                            height: 600,
                            userInfo: {
                                displayName: '{{ auth()->user()->name }}',
                                email: '{{ auth()->user()->email }}'
                            },
                            configOverwrite: {
                                startWithAudioMuted: false,
                                startWithVideoMuted: false,
                                disableDeepLinking: true,
                                prejoinPageEnabled: false,
                                enableWelcomePage: false,
                                subject: '{{ $sessionName }} – Mwalimu Session',
                            },
                            interfaceConfigOverwrite: {
                                SHOW_JITSI_WATERMARK: false,
                                SHOW_BRAND_WATERMARK: false,
                                SHOW_WATERMARK_FOR_GUESTS: false,
                                TOOLBAR_BUTTONS: [
                                    'microphone','camera','closedcaptions','desktop',
                                    'fullscreen','fodeviceselection','hangup','chat',
                                    'recording','raisehand','videoquality','filmstrip',
                                    'tileview','shortcuts','mute-everyone','security'
                                ],
                                MOBILE_APP_PROMO: false,
                                DEFAULT_BACKGROUND: '#111C16',
                            }
                        };

                        @if(!empty(config('jitsi.app_id')) && !empty(config('jitsi.app_secret')))
                        {{-- JWT token already generated server-side --}}
                        options.jwt = '{{ $jwtToken ?? '' }}';
                        @endif

                        const api = new JitsiMeetExternalAPI(domain, options);

                        api.addEventListener('readyToClose', () => {
                            container.innerHTML = '<div style=\'padding:48px;text-align:center;color:rgba(255,255,255,.6);\'><i class=\'fa-solid fa-flag-checkered\' style=\'font-size:40px;display:block;margin-bottom:14px;\'></i><p style=\'font-size:16px;\'>Session ended. Thank you!</p></div>';
                        });

                        api.addEventListener('participantJoined', () => {
                            console.log('Participant joined the session');
                        });

                        this.jitsiLoaded = true;
                        this.launching = false;

                        // Mark joined in backend
                        fetch('/bookings/{{ $booking->uuid }}/joined', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Content-Type': 'application/json'
                            }
                        });
                    }, 500);
                }
            }">
                {{-- Header --}}
                <div style="padding:20px 24px;border-bottom:1px solid rgba(21,40,31,.08);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                    <div>
                        <h2 style="font-family:'Fraunces',serif;font-size:19px;color:#15281F;">
                            <i class="fa-solid fa-video" style="color:#6D28D9;margin-right:8px;font-size:16px;"></i>Live Session Room
                        </h2>
                        <p style="font-size:13px;color:rgba(21,40,31,.5);margin-top:2px;">
                            Powered by Jitsi Meet · Private · Encrypted · No download needed
                        </p>
                    </div>
                    @if($canJoin)
                    <span style="display:flex;align-items:center;gap:7px;background:rgba(16,185,129,.1);color:#10B981;border:1px solid rgba(16,185,129,.25);padding:8px 16px;border-radius:999px;font-size:13px;font-weight:700;">
                        <span style="width:8px;height:8px;background:#10B981;border-radius:50%;"></span>
                        Session is Open
                    </span>
                    @else
                    <span style="display:flex;align-items:center;gap:7px;background:rgba(21,40,31,.06);color:rgba(21,40,31,.5);padding:8px 16px;border-radius:999px;font-size:13px;font-weight:600;">
                        <i class="fa-solid fa-clock fa-xs"></i>
                        Opens {{ $minutesUntil > 0 ? 'in '.$minutesUntil.' min' : 'shortly' }}
                    </span>
                    @endif
                </div>

                {{-- Code entry or Jitsi container --}}
                <div>
                    {{-- Before code is verified --}}
                    <div x-show="!verified">
                        @if($canJoin)
                        {{-- Session is open — show code entry --}}
                        <div style="padding:32px;text-align:center;">
                            <div style="width:72px;height:72px;border-radius:999px;background:rgba(109,40,217,.1);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
                                <i class="fa-solid fa-key" style="font-size:28px;color:#6D28D9;"></i>
                            </div>
                            <h3 style="font-family:'Fraunces',serif;font-size:22px;color:#15281F;margin-bottom:6px;">Enter Your Session Code</h3>
                            <p style="font-size:14px;color:rgba(21,40,31,.55);margin-bottom:24px;max-width:400px;margin-left:auto;margin-right:auto;">
                                You're logged in for this booking. Confirm your session code below, then join the classroom.
                                {{ $isTutor ? 'Your tutor code starts with TUT-.' : 'Your student code starts with STU-.' }}
                            </p>
                            <div style="max-width:320px;margin:0 auto;">
                                <input type="text"
                                       x-model="codeInput"
                                       @keydown.enter="verify()"
                                       placeholder="{{ $isTutor ? 'TUT-XXXXXX' : 'STU-XXXXXX' }}"
                                       style="width:100%;height:56px;text-align:center;font-family:monospace;font-size:22px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;border-radius:16px;border:2px solid rgba(21,40,31,.18);background:#fff;outline:none;transition:all .18s;color:#15281F;margin-bottom:10px;"
                                       onfocus="this.style.borderColor='#6D28D9';this.style.boxShadow='0 0 0 3px rgba(109,40,217,.1)'"
                                       onblur="this.style.borderColor='rgba(21,40,31,.18)';this.style.boxShadow='none'">
                                <p x-show="codeError" x-text="codeError" style="font-size:13px;color:#E8522A;margin-bottom:10px;display:flex;align-items:center;justify-content:center;gap:5px;"><i class="fa-solid fa-triangle-exclamation fa-xs"></i></p>
                                <button @click="verify()"
                                        style="width:100%;height:52px;border-radius:999px;background:#10B981;color:#fff;font-size:15px;font-weight:700;border:none;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s;"
                                        onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10B981'">
                                    <i class="fa-solid fa-video fa-sm"></i> Join Live Session
                                </button>
                            </div>

                            {{-- Demo: show code hint --}}
                            <div style="margin-top:20px;padding:14px;background:#F9FAF9;border-radius:14px;display:inline-block;text-align:left;">
                                <p style="font-size:12px;color:rgba(21,40,31,.5);margin-bottom:5px;"><i class="fa-solid fa-envelope fa-xs" style="color:#6D28D9;"></i> Your code was emailed to <strong>{{ auth()->user()->email }}</strong></p>
                                <p style="font-size:12px;color:rgba(21,40,31,.4);">Can't find it? Check your spam folder or contact support.</p>
                            </div>
                        </div>
                        @else
                        {{-- Session not yet open --}}
                        <div id="jitsi-container" style="min-height:420px;background:#111C16;border-radius:0 0 22px 22px;">
                            <div id="jitsi-placeholder" class="jitsi-placeholder">
                                <i class="fa-solid fa-satellite-dish" style="font-size:48px;color:rgba(255,255,255,.15);margin-bottom:18px;"></i>
                                <h3 style="font-family:'Fraunces',serif;font-size:22px;color:#fff;margin-bottom:8px;">Session Room Waiting</h3>
                                <p style="font-size:14px;color:rgba(255,255,255,.5);margin-bottom:24px;max-width:380px;">This room opens 60 minutes before your session starts. Your access code is already on this page.</p>
                                <div style="background:rgba(255,255,255,.08);border-radius:16px;padding:16px 24px;display:inline-block;">
                                    <p style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.35);margin-bottom:6px;">Time until session</p>
                                    <div id="countdown">--:--:--</div>
                                </div>
                                <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:24px;justify-content:center;">
                                    @foreach([
                                        ['fa-video','Join 60 min before'],
                                        ['fa-key','Enter your '.($isTutor?'TUT':'STU').'-XXXXXX code'],
                                        ['fa-globe','No download required'],
                                    ] as [$ic,$tip])
                                    <span style="display:flex;align-items:center;gap:6px;background:rgba(255,255,255,.08);padding:7px 14px;border-radius:999px;font-size:12px;color:rgba(255,255,255,.6);">
                                        <i class="fa-solid {{ $ic }} fa-xs"></i>{{ $tip }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Jitsi launching --}}
                    <div x-show="launching" x-cloak style="background:#111C16;padding:60px;text-align:center;border-radius:0 0 22px 22px;">
                        <div style="width:52px;height:52px;border:3px solid rgba(109,40,217,.2);border-top-color:#6D28D9;border-radius:50%;animation:spin 1s linear infinite;margin:0 auto 16px;"></div>
                        <p style="font-size:15px;color:rgba(255,255,255,.7);">Launching your private classroom…</p>
                    </div>

                    {{-- Jitsi container (loads after code verified) --}}
                    <div id="jitsi-container" x-show="verified" x-cloak style="border-radius:0 0 22px 22px;overflow:hidden;"></div>
                </div>
            </div>

            {{-- ── YOUR SESSION CODE ── --}}
            <div style="background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.08);padding:28px;">
                <h3 style="font-family:'Fraunces',serif;font-size:18px;color:#15281F;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
                    <i class="fa-solid fa-key" style="color:#6D28D9;font-size:15px;"></i> Your Session Code
                </h3>
                <div class="code-box" style="margin-bottom:18px;">
                    <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(21,40,31,.4);margin-bottom:10px;">
                        {{ $isTutor ? 'Tutor Code' : 'Student Code' }}
                    </p>
                    <div class="code-text">{{ $myCode ?? 'Pending…' }}</div>
                    <p style="font-size:12px;color:rgba(21,40,31,.5);margin-top:10px;">Use this code to enter your live session</p>
                    @if($myCode)
                    <button onclick="navigator.clipboard.writeText('{{ $myCode }}');this.innerHTML='<i class=\'fa-solid fa-check fa-xs\'></i> Copied!';setTimeout(()=>this.innerHTML='<i class=\'fa-solid fa-copy fa-xs\'></i> Copy Code',2000)"
                            style="margin-top:12px;background:none;border:1.5px solid rgba(109,40,217,.3);color:#6D28D9;padding:7px 18px;border-radius:999px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s;">
                        <i class="fa-solid fa-copy fa-xs"></i> Copy Code
                    </button>
                    @endif
                </div>

                {{-- How to join steps --}}
                <div style="background:#F9FAF9;border-radius:14px;padding:16px;">
                    <p style="font-size:12px;font-weight:700;color:#15281F;margin-bottom:10px;display:flex;align-items:center;gap:6px;"><i class="fa-solid fa-list-check" style="color:#6D28D9;"></i> How to join</p>
                    @foreach([
                        '1. Come back to this page when your session starts',
                        '2. Click the "Enter Your Session Code" field',
                        '3. Type your '.($isTutor?'TUT':'STU').'-XXXXXX code',
                        '4. Click "Join Live Session"',
                        '5. Your private Jitsi classroom opens instantly',
                    ] as $s)
                    <p style="font-size:13px;color:rgba(21,40,31,.65);margin-bottom:5px;display:flex;align-items:flex-start;gap:7px;">
                        <i class="fa-solid fa-circle-dot fa-xs" style="color:#6D28D9;margin-top:3px;flex-shrink:0;"></i>{{ $s }}
                    </p>
                    @endforeach
                </div>
            </div>
            @elseif($booking->status === 'pending')
            <div style="background:#FEF9C3;border:1.5px solid #FDE68A;border-radius:22px;padding:28px;text-align:center;">
                <i class="fa-solid fa-clock" style="font-size:40px;color:#D4A017;display:block;margin-bottom:14px;"></i>
                <h3 style="font-family:'Fraunces',serif;font-size:20px;color:#15281F;margin-bottom:8px;">Awaiting Confirmation</h3>
                <p style="font-size:14px;color:rgba(21,40,31,.6);">Your booking is pending payment confirmation. Session codes will be sent once confirmed.</p>
            </div>
            @elseif($booking->status === 'completed')
            <div style="background:#DCFCE7;border:1.5px solid #BBF7D0;border-radius:22px;padding:28px;text-align:center;">
                <i class="fa-solid fa-flag-checkered" style="font-size:40px;color:#10B981;display:block;margin-bottom:14px;"></i>
                <h3 style="font-family:'Fraunces',serif;font-size:20px;color:#15281F;margin-bottom:8px;">Session Completed</h3>
                <p style="font-size:14px;color:rgba(21,40,31,.6);">This session has been completed.
                    @if($isStudent && !$booking->review) Leave a review below to help other students. @endif
                </p>
            </div>
            @elseif($booking->status === 'cancelled')
            <div style="background:#FEE2E2;border:1.5px solid #FECACA;border-radius:22px;padding:28px;text-align:center;">
                <i class="fa-solid fa-ban" style="font-size:40px;color:#E8522A;display:block;margin-bottom:14px;"></i>
                <h3 style="font-family:'Fraunces',serif;font-size:20px;color:#15281F;margin-bottom:8px;">Session Cancelled</h3>
                <p style="font-size:14px;color:rgba(21,40,31,.6);">This session was cancelled. {{ $booking->cancellation_reason ?? '' }}</p>
                <a href="{{ route('tutors.index') }}" style="display:inline-flex;align-items:center;gap:7px;margin-top:16px;padding:11px 24px;border-radius:999px;background:#6D28D9;color:#fff;font-size:14px;font-weight:600;text-decoration:none;">
                    <i class="fa-solid fa-magnifying-glass fa-xs"></i> Book a New Session
                </a>
            </div>
            @endif

            {{-- ── MESSAGES ── --}}
            @if(in_array($booking->status, ['confirmed','completed']))
            <div style="background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.08);overflow:hidden;" x-data="chatRoom()">
                <div style="padding:18px 22px;border-bottom:1px solid rgba(21,40,31,.08);display:flex;align-items:center;gap:12px;">
                    <div style="width:38px;height:38px;border-radius:11px;background:#{{ substr(md5(($isStudent ? $booking->tutor : $booking->student)->email),0,6) }};display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#fff;flex-shrink:0;">{{ strtoupper(substr($otherName,0,1)) }}</div>
                    <div>
                        <div style="font-family:'Fraunces',serif;font-size:16px;font-weight:700;color:#15281F;">{{ $otherName }}</div>
                        <div style="font-size:12px;color:rgba(21,40,31,.5);">{{ $isTutor ? 'Student' : 'Tutor' }} · Session messages</div>
                    </div>
                </div>
                <div id="messages-scroll" style="height:300px;overflow-y:auto;padding:18px;display:flex;flex-direction:column;gap:12px;">
                    @php $messages = $booking->messages()->with('sender')->oldest()->get(); @endphp
                    @forelse($messages as $msg)
                    @php $mine = $msg->sender_id === auth()->id(); @endphp
                    <div style="display:flex;flex-direction:{{ $mine ? 'row-reverse' : 'row' }};gap:8px;align-items:flex-end;">
                        @if(!$mine)<div style="width:28px;height:28px;border-radius:8px;background:#{{ substr(md5($msg->sender->email),0,6) }};display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;">{{ strtoupper(substr($msg->sender->name,0,1)) }}</div>@endif
                        <div>
                            <div class="chat-bubble {{ $mine ? 'chat-mine' : 'chat-theirs' }}">{{ $msg->body }}</div>
                            <div style="font-size:11px;color:rgba(21,40,31,.35);margin-top:3px;{{ $mine ? 'text-align:right' : '' }}">{{ $msg->created_at->format('g:ia') }}</div>
                        </div>
                    </div>
                    @empty
                    <div style="flex:1;display:flex;align-items:center;justify-content:center;color:rgba(21,40,31,.35);font-size:14px;">No messages yet. Start the conversation!</div>
                    @endforelse
                </div>
                <form @submit.prevent="sendMessage" style="padding:14px 18px;border-top:1px solid rgba(21,40,31,.08);display:flex;gap:8px;">
                    <input type="text" x-model="newMsg" placeholder="Type a message…"
                           style="flex:1;height:44px;padding:0 15px;border-radius:12px;border:1.5px solid rgba(21,40,31,.15);font-size:14px;font-family:inherit;outline:none;background:#fff;"
                           onfocus="this.style.borderColor='#6D28D9'" onblur="this.style.borderColor='rgba(21,40,31,.15)'">
                    <button type="submit" style="height:44px;padding:0 18px;border-radius:12px;background:#6D28D9;color:#fff;border:none;cursor:pointer;font-family:inherit;font-weight:600;display:flex;align-items:center;gap:6px;transition:background .15s;" onmouseover="this.style.background='#4C1D95'" onmouseout="this.style.background='#6D28D9'">
                        <i class="fa-solid fa-paper-plane fa-xs"></i> Send
                    </button>
                </form>
            </div>
            @endif

            {{-- ── LEAVE REVIEW ── --}}
            @if($booking->status === 'completed' && $isStudent && !$booking->review)
            <div style="background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.08);padding:28px;" x-data="{ rating:5, comment:'', submitted:false }">
                <h3 style="font-family:'Fraunces',serif;font-size:18px;color:#15281F;margin-bottom:18px;display:flex;align-items:center;gap:8px;">
                    <i class="fa-solid fa-star" style="color:#D4A017;font-size:15px;"></i> Leave a Review
                </h3>
                <template x-if="!submitted">
                    <form @submit.prevent="
                        fetch('/bookings/{{ $booking->uuid }}/review',{
                            method:'POST',
                            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('[name=csrf-token]').content},
                            body:JSON.stringify({rating,comment})
                        }).then(()=>{submitted=true})
                    ">
                        <div style="display:flex;gap:6px;margin-bottom:16px;" x-data="{ hover:0 }">
                            @for($i=1;$i<=5;$i++)
                            <button type="button" @click="rating={{ $i }}" @mouseenter="hover={{ $i }}" @mouseleave="hover=0"
                                    style="font-size:32px;background:none;border:none;cursor:pointer;transition:transform .1s;"
                                    :style="(hover>={{ $i }}||(hover===0&&rating>={{ $i }}))?'color:#D4A017;transform:scale(1.1)':'color:rgba(21,40,31,.15)'">★</button>
                            @endfor
                            <span style="font-size:14px;color:rgba(21,40,31,.5);align-self:center;margin-left:8px;" x-text="rating+'/5'"></span>
                        </div>
                        <textarea x-model="comment" rows="3" placeholder="Share your experience with {{ $booking->tutor->name }}…"
                                  style="width:100%;padding:12px 14px;border-radius:13px;border:1.5px solid rgba(21,40,31,.15);font-size:14px;font-family:inherit;outline:none;resize:none;line-height:1.65;margin-bottom:14px;"
                                  onfocus="this.style.borderColor='#6D28D9'" onblur="this.style.borderColor='rgba(21,40,31,.15)'"></textarea>
                        <button type="submit" style="padding:12px 28px;border-radius:999px;background:#D4A017;color:#15281F;font-size:14px;font-weight:700;border:none;cursor:pointer;font-family:inherit;">
                            <i class="fa-solid fa-star fa-xs"></i> Submit Review
                        </button>
                    </form>
                </template>
                <template x-if="submitted">
                    <div style="text-align:center;padding:20px;">
                        <i class="fa-solid fa-circle-check" style="font-size:32px;color:#10B981;display:block;margin-bottom:10px;"></i>
                        <p style="font-size:15px;font-weight:600;color:#15281F;">Thank you for your review!</p>
                    </div>
                </template>
            </div>
            @elseif($booking->review)
            <div style="background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.08);padding:24px;">
                <h3 style="font-family:'Fraunces',serif;font-size:17px;color:#15281F;margin-bottom:14px;"><i class="fa-solid fa-star" style="color:#D4A017;margin-right:6px;"></i>Your Review</h3>
                <div style="display:flex;gap:2px;margin-bottom:8px;">
                    @for($i=1;$i<=5;$i++)<i class="fa-solid fa-star" style="color:{{ $i<=$booking->review->rating?'#D4A017':'#E5E7EB' }};font-size:18px;"></i>@endfor
                </div>
                <p style="font-size:14px;color:#15281F;line-height:1.75;">{{ $booking->review->comment }}</p>
            </div>
            @endif
        </div>

        {{-- RIGHT SIDEBAR --}}
        <div style="height:fit-content;position:sticky;top:88px;display:flex;flex-direction:column;gap:16px;">

            {{-- Session details --}}
            <div style="background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.08);padding:24px;">
                <h3 style="font-family:'Fraunces',serif;font-size:17px;color:#15281F;margin-bottom:16px;display:flex;align-items:center;gap:7px;">
                    <i class="fa-solid fa-circle-info" style="color:#6D28D9;font-size:14px;"></i> Session Details
                </h3>
                <dl style="display:flex;flex-direction:column;gap:0;">
                    @foreach([
                        ['fa-user',''.($isTutor?'Student':'Tutor'),$otherName],
                        ['fa-book-open','Subject',$booking->subject?->name ?? 'General'],
                        ['fa-calendar','Date',$sessionStart->format('D, M j, Y')],
                        ['fa-clock','Time',$sessionStart->format('g:i a').' – '.$sessionEnd->format('g:i a').' EAT'],
                        ['fa-hourglass-half','Duration',$booking->duration_minutes.' minutes · '.$sessionName],
                        ['fa-money-bill','Amount Paid','KSh '.number_format($booking->total_amount)],
                    ] as [$icon,$label,$val])
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(21,40,31,.06);gap:8px;">
                        <span style="display:flex;align-items:center;gap:7px;font-size:13px;color:rgba(21,40,31,.5);flex-shrink:0;">
                            <i class="fa-solid fa-{{ $icon }}" style="width:13px;text-align:center;font-size:11px;"></i>{{ $label }}
                        </span>
                        <span style="font-size:13px;font-weight:600;color:#15281F;text-align:right;">{{ $val }}</span>
                    </div>
                    @endforeach
                </dl>

                @if($booking->student_notes)
                <div style="margin-top:14px;padding:12px 14px;background:#F9FAF9;border-radius:12px;">
                    <p style="font-size:11px;font-weight:700;color:rgba(21,40,31,.4);text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;">Student Notes</p>
                    <p style="font-size:13px;color:#15281F;line-height:1.65;">{{ $booking->student_notes }}</p>
                </div>
                @endif
            </div>

            {{-- Payment info --}}
            <div style="background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.08);padding:24px;">
                <h3 style="font-family:'Fraunces',serif;font-size:17px;color:#15281F;margin-bottom:16px;display:flex;align-items:center;gap:7px;">
                    <i class="fa-solid fa-credit-card" style="color:#6D28D9;font-size:14px;"></i> Payment
                </h3>
                @if($booking->payment)
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <div style="display:flex;justify-content:space-between;font-size:13px;">
                        <span style="color:rgba(21,40,31,.5);">Status</span>
                        <span style="font-weight:600;color:#10B981;"><i class="fa-solid fa-circle-check fa-xs"></i> Paid</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:13px;">
                        <span style="color:rgba(21,40,31,.5);">Amount</span>
                        <span style="font-weight:700;color:#15281F;">KSh {{ number_format($booking->total_amount) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:13px;">
                        <span style="color:rgba(21,40,31,.5);">Reference</span>
                        <code style="font-size:11px;background:#F4F6F4;padding:2px 7px;border-radius:5px;color:#15281F;">{{ Str::limit($booking->payment->paystack_reference,18) }}</code>
                    </div>
                    @if($booking->payment->paid_at)
                    <div style="display:flex;justify-content:space-between;font-size:13px;">
                        <span style="color:rgba(21,40,31,.5);">Paid on</span>
                        <span style="font-weight:500;color:#15281F;">{{ $booking->payment->paid_at->format('M j, Y') }}</span>
                    </div>
                    @endif
                </div>
                @else
                <p style="font-size:13px;color:rgba(21,40,31,.5);">Payment pending</p>
                @endif
            </div>

            {{-- Cancel --}}
            @if(in_array($booking->status,['pending','confirmed']) && $sessionStart->isFuture())
            <form method="POST" action="{{ route('bookings.cancel',$booking->uuid) }}" onsubmit="return confirm('Cancel this booking? This action cannot be undone.')">
                @csrf
                <input type="hidden" name="reason" value="Cancelled by {{ $isTutor ? 'tutor' : 'student' }}">
                <button type="submit" style="width:100%;padding:12px;border-radius:14px;background:#FEE2E2;color:#991B1B;border:1.5px solid #FECACA;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s;" onmouseover="this.style.background='#FECACA'" onmouseout="this.style.background='#FEE2E2'">
                    <i class="fa-solid fa-ban fa-xs"></i> Cancel Booking
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

<style>
.session-grid{grid-template-columns:1fr!important;}
@media(min-width:1024px){.session-grid{grid-template-columns:1fr 360px!important;}}
@keyframes spin{to{transform:rotate(360deg)}}
</style>

@include('partials.footer')

<script src="https://meet.jit.si/external_api.js"></script>
<script>
// Countdown timer
(function() {
    const el = document.getElementById('countdown');
    if (!el) return;
    const target = new Date('{{ $sessionStart->toISOString() }}');
    const tick = () => {
        const diff = target - new Date();
        if (diff <= 0) { el.textContent = 'Starting now!'; return; }
        const h = Math.floor(diff / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        el.textContent = (h>0?String(h).padStart(2,'0')+':':'')+String(m).padStart(2,'0')+':'+String(s).padStart(2,'0');
        setTimeout(tick, 1000);
    };
    tick();
})();

// Chat
function chatRoom() {
    return {
        newMsg: '',
        sendMessage() {
            if (!this.newMsg.trim()) return;
            fetch('/bookings/{{ $booking->uuid }}/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ body: this.newMsg })
            }).then(r => r.json()).then(data => {
                if (data.status === 'ok') {
                    const scroll = document.getElementById('messages-scroll');
                    const mine = document.createElement('div');
                    mine.style.cssText = 'display:flex;flex-direction:row-reverse;gap:8px;align-items:flex-end;';
                    mine.innerHTML = `<div><div class="chat-bubble chat-mine">${this.newMsg}</div><div style="font-size:11px;color:rgba(21,40,31,.35);margin-top:3px;text-align:right;">just now</div></div>`;
                    scroll.appendChild(mine);
                    scroll.scrollTop = scroll.scrollHeight;
                    this.newMsg = '';
                }
            });
        }
    }
}

// Scroll chat to bottom on load
document.addEventListener('DOMContentLoaded', () => {
    const scroll = document.getElementById('messages-scroll');
    if (scroll) scroll.scrollTop = scroll.scrollHeight;
});
</script>
@livewireScripts
</body>
</html>
