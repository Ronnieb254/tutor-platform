<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Examiner Dashboard – Mwalimu</title>
<style>
/* Page-specific dashboard styles (self-contained; design tokens come from partials.head) */
.session-hero{background:linear-gradient(135deg,#111C16,#15281F);padding:48px 0;position:relative;overflow:hidden;}
.session-hero::before{content:'';position:absolute;top:-60px;right:-60px;width:260px;height:260px;background:rgba(10,100,188,.1);border-radius:50%;pointer-events:none;}
.session-hero::after{content:'';position:absolute;bottom:-80px;left:-40px;width:220px;height:220px;background:rgba(16,185,129,.06);border-radius:50%;pointer-events:none;}
/* Start-session pulse button (mirrors booking show .join-btn-live) */
.start-btn{display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:999px;font-size:13px;font-weight:700;border:none;cursor:pointer;font-family:inherit;text-decoration:none;background:#10B981;color:#fff;box-shadow:0 0 0 0 rgba(16,185,129,.4);animation:pulse-green 2s infinite;transition:background .18s;}
.start-btn:hover{background:#059669;}
@keyframes pulse-green{0%,100%{box-shadow:0 0 0 0 rgba(16,185,129,.4);}50%{box-shadow:0 0 0 14px rgba(16,185,129,0);}}
/* Outline / detail button */
.detail-btn{display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:999px;font-size:13px;font-weight:600;border:1.5px solid rgba(21,40,31,.15);background:#fff;color:#15281F;text-decoration:none;transition:all .15s;}
.detail-btn:hover{border-color:rgba(10,100,188,.4);background:#F9FAF9;}
/* Capacity bar */
.cap-bar{height:6px;background:rgba(21,40,31,.06);border-radius:999px;overflow:hidden;}
.cap-fill{height:100%;border-radius:999px;transition:width .3s;}
/* Timeline dots for recent enrollments */
.tl-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;margin-top:5px;}
.tl-line{width:2px;background:rgba(21,40,31,.08);flex:1;margin:3px 0;}
/* Stat grid responsive */
.dash-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;}
@media(max-width:768px){.dash-stats{grid-template-columns:1fr;}}
/* Clinic list responsive */
.clinic-row{display:flex;align-items:flex-start;gap:18px;flex-wrap:wrap;}
@media(max-width:600px){.clinic-row{flex-direction:column;}}
.clinic-actions{display:flex;gap:10px;flex-shrink:0;flex-wrap:wrap;}
.pill{padding:3px 11px;border-radius:999px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;}
</style>
</head>
<body>
@php $user = auth()->user(); @endphp

@include('partials.nav')

{{-- ─── HERO ─── --}}
<div class="session-hero">
    <div style="max-width:1360px;margin:0 auto;padding:0 24px;position:relative;z-index:1;">
        <a href="{{ route('dashboard') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:rgba(255,255,255,.5);text-decoration:none;margin-bottom:18px;transition:color .15s;" onmouseover="this.style.color='rgba(255,255,255,.85)'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
            <i class="fa-solid fa-arrow-left fa-xs"></i> Dashboard
        </a>
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:24px;flex-wrap:wrap;">
            <div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap;">
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:999px;font-size:12px;font-weight:700;background:rgba(16,185,129,.2);color:#4ADE80;border:1px solid rgba(16,185,129,.3);">
                        <i class="fa-solid fa-circle-check fa-xs"></i> Approved Examiner
                    </span>
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:999px;font-size:12px;font-weight:700;background:rgba(212,160,23,.2);color:#FCD34D;border:1px solid rgba(212,160,23,.25);">
                        <i class="fa-solid fa-star fa-xs"></i> KNEC Examiner
                    </span>
                </div>
                <h1 style="font-family:'Fraunces',serif;font-size:clamp(26px,4vw,40px);color:#fff;margin-bottom:8px;">
                    Welcome back, {{ $user->name }}
                </h1>
                <p style="font-size:15px;color:rgba(255,255,255,.6);max-width:520px;">
                    Run your examiner clinics, track enrollments and earnings, and start live sessions — all in one place.
                </p>
            </div>
            <a href="{{ route('examiner.clinics.create') }}" class="btn btn-mint" style="flex-shrink:0;">
                <i class="fa-solid fa-plus fa-sm"></i> Create New Clinic
            </a>
        </div>
    </div>
</div>

{{-- ─── MAIN ─── --}}
<div style="max-width:1360px;margin:0 auto;padding:32px 24px 64px;">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flash flash-ok"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="flash flash-info"><i class="fa-solid fa-circle-info"></i> {{ session('info') }}</div>
    @endif
    @if(session('error'))
        <div class="flash flash-err"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>
    @endif

    {{-- ─── STATS ROW ─── --}}
    <div class="dash-stats" style="margin-bottom:36px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(10,100,188,.1);color:#0A64BC;"><i class="fa-solid fa-chalkboard-user"></i></div>
            <div class="stat-label">Total Clinics</div>
            <div class="stat-value">{{ $totalClinics }}</div>
            <div class="stat-sub">{{ $upcomingCount }} upcoming</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,.1);color:#10B981;"><i class="fa-solid fa-users"></i></div>
            <div class="stat-label">Total Students Enrolled</div>
            <div class="stat-value">{{ $totalStudents }}</div>
            <div class="stat-sub">across all your clinics</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(212,160,23,.12);color:#B8920F;"><i class="fa-solid fa-calendar-days"></i></div>
            <div class="stat-label">Upcoming Sessions</div>
            <div class="stat-value">{{ $upcomingCount }}</div>
            <div class="stat-sub">scheduled ahead</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,.1);color:#10B981;"><i class="fa-solid fa-sack-dollar"></i></div>
            <div class="stat-label">Clinic Earnings</div>
            <div class="stat-value">KSh {{ number_format($clinicEarnings, 0) }}</div>
            @if($clinicEarnings > 0)
                <div class="stat-sub">Your 40% share after platform fee</div>
            @else
                <div class="stat-sub">Earnings appear once students book paid clinics</div>
            @endif
        </div>
    </div>

    @if($totalClinics === 0)
        {{-- ─── NO CLINICS EMPTY STATE ─── --}}
        <div style="background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.08);padding:64px 24px;text-align:center;box-shadow:0 2px 16px rgba(21,40,31,.06);">
            <div style="width:88px;height:88px;border-radius:24px;background:rgba(10,100,188,.08);display:flex;align-items:center;justify-content:center;margin:0 auto 22px;">
                <i class="fa-solid fa-chalkboard-user" style="font-size:38px;color:#0A64BC;"></i>
            </div>
            <h2 style="font-family:'Fraunces',serif;font-size:26px;color:#15281F;margin-bottom:10px;">Create your first clinic</h2>
            <p style="font-size:15px;color:rgba(21,40,31,.55);max-width:480px;margin:0 auto 26px;line-height:1.7;">
                You're approved as an examiner. Set up your first examiner clinic — choose a subject and paper (PP1, PP2, PP3), set the time, and students can start booking right away.
            </p>
            <a href="{{ route('examiner.clinics.create') }}" class="btn btn-mint" style="font-size:15px;padding:13px 32px;">
                <i class="fa-solid fa-plus fa-sm"></i> Create Your First Clinic
            </a>
        </div>
    @else
        {{-- ─── UPCOMING CLINICS ─── --}}
        <div style="margin-bottom:40px;">
            <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:20px;">
                <div>
                    <h2 style="font-family:'Fraunces',serif;font-size:clamp(22px,3vw,30px);color:#15281F;margin-bottom:4px;">Upcoming Clinics</h2>
                    <p style="font-size:14px;color:rgba(21,40,31,.55);">{{ $upcomingClinics->count() }} clinic{{ $upcomingClinics->count() != 1 ? 's' : '' }} scheduled</p>
                </div>
                <a href="{{ route('examiner.clinics.create') }}" class="btn btn-v btn-sm"><i class="fa-solid fa-plus fa-xs"></i> New Clinic</a>
            </div>

            @if($upcomingClinics->isEmpty())
                <div style="background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.08);padding:48px 24px;text-align:center;">
                    <div style="font-size:44px;margin-bottom:12px;color:rgba(21,40,31,.12);"><i class="fa-solid fa-calendar-xmark"></i></div>
                    <h3 style="font-family:'Fraunces',serif;font-size:20px;color:#15281F;margin-bottom:6px;">No upcoming clinics</h3>
                    <p style="color:rgba(21,40,31,.55);margin-bottom:18px;">Schedule a new clinic so students can book a spot.</p>
                    <a href="{{ route('examiner.clinics.create') }}" class="btn btn-mint"><i class="fa-solid fa-plus fa-sm"></i> Create New Clinic</a>
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:14px;">
                    @foreach($upcomingClinics as $clinic)
                        @php
                            $enrolled = $clinic->enrollments_count;
                            $capacity = $clinic->max_students ?: 30;
                            $fillPct  = $capacity > 0 ? round(($enrolled / $capacity) * 100) : 0;
                            $isFull   = $enrolled >= $capacity;
                            $canStart = in_array($clinic->status, ['scheduled', 'live']);
                            $clinicTitle = $clinic->title ?? $clinic->name ?? 'Untitled Clinic';
                        @endphp
                        <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:24px;box-shadow:0 2px 12px rgba(21,40,31,.04);">
                            <div class="clinic-row">
                                <div style="width:54px;height:54px;border-radius:16px;background:rgba(212,160,23,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fa-solid fa-chalkboard-user" style="color:#EEBE15;font-size:22px;"></i>
                                </div>
                                <div style="flex:1;min-width:200px;">
                                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:8px;">
                                        <div>
                                            <h3 style="font-family:'Fraunces',serif;font-size:18px;color:#15281F;">{{ $clinicTitle }}</h3>
                                            @if($clinic->batch_number > 1)
                                                <span style="font-size:11px;color:rgba(21,40,31,.4);font-weight:600;">Session {{ $clinic->batch_number }} (auto-opened)</span>
                                            @endif
                                        </div>
                                        @php
                                            $statusCfg = [
                                                'scheduled' => ['rgba(10,100,188,.1)', '#0A64BC', 'fa-clock', 'Scheduled'],
                                                'live'      => ['rgba(16,185,129,.1)', '#10B981', 'fa-circle-dot', 'Live'],
                                                'completed' => ['rgba(21,40,31,.06)', 'rgba(21,40,31,.55)', 'fa-flag-checkered', 'Completed'],
                                                'cancelled' => ['rgba(232,82,42,.1)', '#E8522A', 'fa-ban', 'Cancelled'],
                                            ];
                                            [$sbg, $scol, $sic, $slbl] = $statusCfg[$clinic->status] ?? ['rgba(21,40,31,.06)', 'rgba(21,40,31,.55)', 'fa-circle', ucfirst($clinic->status)];
                                        @endphp
                                        <span class="pill" style="background:{{ $sbg }};color:{{ $scol }};"><i class="fa-solid {{ $sic }} fa-xs"></i> {{ $slbl }}</span>
                                    </div>
                                    <div style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:10px;">
                                        @if($clinic->subject)
                                            <span class="pill" style="background:rgba(21,40,31,.06);color:#15281F;"><i class="fa-solid fa-book-open fa-xs"></i>{{ $clinic->subject->name }}</span>
                                        @endif
                                        @if($clinic->paper_type)
                                            <span class="pill" style="background:rgba(212,160,23,.1);color:#EEBE15;font-weight:700;"><i class="fa-solid fa-file-lines fa-xs"></i>{{ $clinic->paper_type }}</span>
                                        @endif
                                        @if($isFull)
                                            <span class="pill" style="background:#FEE2E2;color:#991B1B;font-weight:700;"><i class="fa-solid fa-lock fa-xs"></i> FULL</span>
                                        @endif
                                    </div>
                                    <div style="display:flex;flex-wrap:wrap;gap:16px;font-size:13px;color:rgba(21,40,31,.55);align-items:center;margin-bottom:10px;">
                                        <span><i class="fa-regular fa-calendar fa-xs" style="color:#0A64BC;margin-right:4px;"></i>{{ $clinic->scheduled_at->format('D, M j \a\t g:ia') }}</span>
                                        <span><i class="fa-solid fa-clock fa-xs" style="color:#0A64BC;margin-right:4px;"></i>{{ $clinic->duration_minutes }} min</span>
                                        <span style="color:{{ $isFull ? '#991B1B' : '#10B981' }};font-weight:600;"><i class="fa-solid fa-users fa-xs" style="margin-right:4px;"></i>{{ $enrolled }}/{{ $capacity }} enrolled</span>
                                        <span><i class="fa-solid fa-money-bill fa-xs" style="color:#EEBE15;margin-right:4px;"></i>KSh {{ number_format($clinic->price, 0) }}</span>
                                    </div>
                                    <div class="cap-bar" style="width:100%;max-width:320px;">
                                        <div class="cap-fill" style="width:{{ $fillPct }}%;background:{{ $isFull ? '#E8522A' : ($fillPct > 70 ? '#EEBE15' : '#10B981') }};"></div>
                                    </div>
                                </div>
                                <div class="clinic-actions">
                                    <a href="{{ route('examiner.clinics.show', $clinic) }}" class="detail-btn"><i class="fa-solid fa-eye fa-xs"></i> View Details</a>
                                    @if($canStart)
                                        <a href="{{ route('examiner.clinics.join', $clinic) }}" class="start-btn"><i class="fa-solid fa-video fa-xs"></i> Start Session</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ─── PAST CLINICS ─── --}}
        <div style="margin-bottom:40px;">
            <div style="margin-bottom:20px;">
                <h2 style="font-family:'Fraunces',serif;font-size:clamp(22px,3vw,30px);color:#15281F;margin-bottom:4px;">Past Clinics</h2>
                <p style="font-size:14px;color:rgba(21,40,31,.55);">Clinics that have already taken place</p>
            </div>
            <div style="background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.08);padding:28px;box-shadow:0 2px 16px rgba(21,40,31,.06);">
                @if($pastClinics->isEmpty())
                    <div style="text-align:center;padding:36px 12px;">
                        <div style="font-size:40px;margin-bottom:10px;color:rgba(21,40,31,.12);"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        <p style="font-size:15px;color:rgba(21,40,31,.55);">No past clinics yet. Once a clinic's scheduled time passes, it will show up here.</p>
                    </div>
                @else
                    <div class="tbl-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Clinic</th>
                                    <th>Date</th>
                                    <th>Enrolled</th>
                                    <th>Earnings</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pastClinics as $clinic)
                                    @php
                                        $clinicTitle = $clinic->title ?? $clinic->name ?? 'Untitled Clinic';
                                        $enrolled = $clinic->enrollments_count;
                                        $clinicEarn = $enrolled * ($clinic->price * 0.40);
                                    @endphp
                                    <tr>
                                        <td>
                                            <div style="display:flex;align-items:center;gap:10px;">
                                                <div style="width:36px;height:36px;border-radius:10px;background:rgba(21,40,31,.06);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                    <i class="fa-solid fa-chalkboard-user" style="color:rgba(21,40,31,.5);font-size:14px;"></i>
                                                </div>
                                                <div>
                                                    <div style="font-weight:600;color:#15281F;">{{ $clinicTitle }}</div>
                                                    <div style="font-size:12px;color:rgba(21,40,31,.5);">{{ $clinic->subject->name ?? 'N/A' }}{{ $clinic->paper_type ? ' · ' . $clinic->paper_type : '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="color:rgba(21,40,31,.65);">{{ $clinic->scheduled_at->format('M j, Y') }}</td>
                                        <td><span style="font-weight:600;color:#15281F;">{{ $enrolled }}</span><span style="color:rgba(21,40,31,.4);">/{{ $clinic->max_students ?: 30 }}</span></td>
                                        <td style="font-weight:700;color:#10B981;">KSh {{ number_format($clinicEarn, 0) }}</td>
                                        <td style="text-align:right;">
                                            <a href="{{ route('examiner.clinics.show', $clinic) }}" style="font-size:13px;font-weight:600;color:#0A64BC;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">View <i class="fa-solid fa-arrow-right fa-xs"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- ─── RECENT ENROLLMENTS ─── --}}
        <div>
            <div style="margin-bottom:20px;">
                <h2 style="font-family:'Fraunces',serif;font-size:clamp(22px,3vw,30px);color:#15281F;margin-bottom:4px;">Recent Enrollments</h2>
                <p style="font-size:14px;color:rgba(21,40,31,.55);">Students who recently booked your clinics</p>
            </div>
            <div style="background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.08);padding:28px;box-shadow:0 2px 16px rgba(21,40,31,.06);">
                @if($recentEnrollments->isEmpty())
                    <div style="text-align:center;padding:36px 12px;">
                        <div style="font-size:40px;margin-bottom:10px;color:rgba(21,40,31,.12);"><i class="fa-solid fa-user-plus"></i></div>
                        <p style="font-size:15px;color:rgba(21,40,31,.55);">No enrollments yet. Once students book your clinics, they'll appear here.</p>
                    </div>
                @else
                    <div style="display:flex;flex-direction:column;gap:4px;">
                        @foreach($recentEnrollments as $enrollment)
                            <div style="display:flex;align-items:flex-start;gap:14px;padding:12px 0;border-bottom:1px solid rgba(21,40,31,.05);">
                                <div class="tl-dot" style="background:#10B981;margin-top:8px;"></div>
                                <div style="width:38px;height:38px;border-radius:11px;background:#{{ substr(md5($enrollment->student?->email ?? 'x'),0,6) }};display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#fff;flex-shrink:0;">
                                    {{ strtoupper(substr($enrollment->student?->name ?? '?', 0, 1)) }}
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-weight:600;color:#15281F;font-size:14px;">{{ $enrollment->student?->name ?? 'Unknown student' }}</div>
                                    <div style="font-size:13px;color:rgba(21,40,31,.55);margin-top:2px;">
                                        enrolled in <strong style="color:#15281F;">{{ $enrollment->clinic?->title ?? $enrollment->clinic?->name ?? 'a clinic' }}</strong>
                                    </div>
                                </div>
                                <div style="font-size:12px;color:rgba(21,40,31,.45);flex-shrink:0;text-align:right;white-space:nowrap;">
                                    {{ $enrollment->created_at?->format('M j, Y') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

@include('partials.footer')
</body>
</html>
