<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Clinic Details – {{ $clinic->title ?? $clinic->name }}</title>
</head>
<body>
@include('partials.nav')

<section style="padding:48px 0 80px;background:#F2F5F2;min-height:calc(100vh - 64px);">
<div style="max-width:1100px;margin:0 auto;padding:0 20px;">

    {{-- Breadcrumb --}}
    <a href="{{ route('examiner.dashboard') }}" style="font-size:13px;color:rgba(21,40,31,.5);text-decoration:none;display:inline-flex;align-items:center;gap:5px;margin-bottom:18px;">
        <i class="fa-solid fa-arrow-left fa-xs"></i> Examiner Dashboard
    </a>

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-bottom:24px;">
        <div>
            <h1 style="font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:#15281F;margin-bottom:5px;">
                {{ $clinic->title ?? $clinic->name }}
                @if($clinic->is_auto_created)
                <span style="font-size:11px;background:rgba(212,160,23,.15);color:#B45309;padding:2px 10px;border-radius:999px;font-weight:700;vertical-align:middle;margin-left:6px;">AUTO BATCH {{ $clinic->batch_number }}</span>
                @endif
            </h1>
            <div style="display:flex;flex-wrap:wrap;gap:10px;font-size:13px;color:rgba(21,40,31,.55);">
                @if($clinic->subject)<span><i class="fa-solid fa-book-open fa-xs" style="color:#0A64BC;"></i> {{ $clinic->subject->name }}</span>@endif
                @if($clinic->paper_type)<span><i class="fa-solid fa-file-lines fa-xs" style="color:#0A64BC;"></i> {{ $clinic->paper_type }}</span>@endif
                <span><i class="fa-regular fa-calendar fa-xs" style="color:#0A64BC;"></i> {{ $clinic->scheduled_at->format('D, M j, Y \a\t g:ia') }}</span>
                <span><i class="fa-solid fa-clock fa-xs" style="color:#0A64BC;"></i> {{ $clinic->duration_minutes ?? 60 }} min</span>
            </div>
        </div>

        {{-- Join / status action --}}
        @php
            $canJoinNow  = $clinic->scheduled_at->diffInMinutes(now(), false) >= -15; // 15 min early
            $isCancelled = $clinic->status === 'cancelled';
        @endphp
        @if($isCancelled)
        <span style="display:inline-flex;align-items:center;gap:6px;padding:11px 22px;border-radius:10px;background:#FEE2E2;color:#991B1B;font-size:14px;font-weight:700;">
            <i class="fa-solid fa-ban fa-sm"></i> Cancelled
        </span>
        @elseif($canJoinNow)
        <a href="{{ route('examiner.clinics.join', $clinic) }}" style="display:inline-flex;align-items:center;gap:7px;padding:12px 24px;border-radius:10px;background:#10B981;color:#fff;font-size:14px;font-weight:700;text-decoration:none;transition:opacity .15s;" onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
            <i class="fa-solid fa-video fa-sm"></i> Launch Session in Jitsi
        </a>
        @else
        <div style="text-align:right;">
            <div style="font-size:12px;color:rgba(21,40,31,.45);margin-bottom:5px;">Session starts in</div>
            <div id="countdown" style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:#0A64BC;">—</div>
            <a href="{{ route('examiner.clinics.join', $clinic) }}" style="display:inline-flex;align-items:center;gap:6px;margin-top:8px;padding:10px 20px;border-radius:10px;background:#0A64BC;color:#fff;font-size:13px;font-weight:600;text-decoration:none;opacity:.75;">
                <i class="fa-solid fa-video fa-xs"></i> Join Early
            </a>
        </div>
        @endif
    </div>

    @if(session('success'))
    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);border-radius:12px;padding:12px 16px;margin-bottom:18px;color:#059669;font-weight:600;">
        <i class="fa-solid fa-circle-check fa-sm"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Grid: details + enrollments --}}
    <div style="display:grid;grid-template-columns:1fr 320px;gap:18px;" class="detail-g">

        {{-- Enrollment list --}}
        <div style="background:#fff;border-radius:18px;border:1px solid rgba(21,40,31,.08);overflow:hidden;">
            <div style="padding:16px 22px;border-bottom:1px solid rgba(21,40,31,.08);display:flex;align-items:center;justify-content:space-between;">
                <h2 style="font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:#15281F;">Enrolled Students</h2>
                <span style="font-weight:700;font-size:14px;color:{{ $clinic->isFull() ? '#E8522A' : '#10B981' }};">
                    {{ $clinic->enrollments->count() }}/{{ $clinic->max_students }}
                </span>
            </div>

            {{-- Capacity bar --}}
            @php $pct = $clinic->max_students > 0 ? round(($clinic->enrollments->count() / $clinic->max_students) * 100) : 0; @endphp
            <div style="height:5px;background:rgba(21,40,31,.06);">
                <div style="width:{{ $pct }}%;height:100%;background:{{ $clinic->isFull() ? '#E8522A' : ($pct>70 ? '#EEBE15' : '#10B981') }};transition:width .4s;"></div>
            </div>

            @if($clinic->enrollments->isEmpty())
            <div style="padding:48px;text-align:center;">
                <i class="fa-solid fa-user-group" style="font-size:30px;color:rgba(21,40,31,.15);margin-bottom:12px;display:block;"></i>
                <p style="color:rgba(21,40,31,.45);font-size:14px;">No students have enrolled yet.</p>
            </div>
            @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr style="background:rgba(21,40,31,.02);">
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.45);">#</th>
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.45);">Student</th>
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.45);">Code</th>
                            <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.45);">Attended</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clinic->enrollments as $i => $en)
                        <tr style="border-top:1px solid rgba(21,40,31,.04);">
                            <td style="padding:12px 16px;color:rgba(21,40,31,.4);">{{ $i + 1 }}</td>
                            <td style="padding:12px 16px;">
                                <div style="font-weight:600;color:#15281F;">{{ $en->student?->name ?? '—' }}</div>
                                <div style="font-size:11px;color:rgba(21,40,31,.45);">{{ $en->student?->email }}</div>
                            </td>
                            <td style="padding:12px 16px;">
                                <code style="background:#F4F6F4;padding:3px 8px;border-radius:6px;font-size:11px;font-weight:700;color:#0A64BC;">{{ $en->session_code }}</code>
                            </td>
                            <td style="padding:12px 16px;text-align:center;">
                                @if($en->attended)
                                <i class="fa-solid fa-circle-check" style="color:#10B981;"></i>
                                @else
                                <i class="fa-solid fa-circle-minus" style="color:rgba(21,40,31,.15);"></i>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Right sidebar --}}
        <div style="display:flex;flex-direction:column;gap:14px;">

            {{-- Stats card --}}
            <div style="background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.08);padding:20px;">
                <h3 style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.45);margin-bottom:14px;">Quick Stats</h3>
                @foreach([
                    ['Enrolled',  $clinic->enrollments->count() . ' / ' . $clinic->max_students, '#15281F'],
                    ['Attended',  $clinic->enrollments->where('attended', true)->count(), '#10B981'],
                    ['Revenue',   'KSh ' . number_format($clinic->enrollments->count() * $clinic->price), '#0A64BC'],
                    ['Price',     'KSh ' . number_format($clinic->price), '#15281F'],
                    ['Batch',     'Batch ' . $clinic->batch_number, '#15281F'],
                ] as [$k,$v,$c])
                <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid rgba(21,40,31,.05);font-size:13px;">
                    <span style="color:rgba(21,40,31,.55);">{{ $k }}</span>
                    <span style="font-weight:700;color:{{ $c }};">{{ $v }}</span>
                </div>
                @endforeach
            </div>

            {{-- Session info --}}
            <div style="background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.08);padding:20px;">
                <h3 style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.45);margin-bottom:12px;">Session</h3>
                <div style="font-size:13px;color:rgba(21,40,31,.55);margin-bottom:6px;">Meeting Room</div>
                <code style="font-size:11px;background:#F4F6F4;padding:6px 10px;border-radius:8px;display:block;word-break:break-all;color:#0A64BC;font-weight:600;">{{ $clinic->meeting_room ?? $clinic->session_code ?? 'TBD' }}</code>
                <p style="font-size:11px;color:rgba(21,40,31,.4);margin-top:8px;">The Jitsi room name. Students are redirected here automatically when they click Join.</p>
            </div>

            {{-- Overflow info --}}
            @if($clinic->hasOverflowClinic())
            <div style="background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.2);border-radius:14px;padding:16px;">
                <h3 style="font-size:12px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;"><i class="fa-solid fa-circle-check fa-xs"></i> Overflow Created</h3>
                @php $overflow = $clinic->childSessions()->latest()->first(); @endphp
                @if($overflow)
                <div style="font-size:13px;font-weight:600;color:#15281F;margin-bottom:3px;">{{ $overflow->title }}</div>
                <div style="font-size:12px;color:rgba(21,40,31,.55);">{{ $overflow->scheduled_at->format('D, M j \a\t g:ia') }}</div>
                <a href="{{ route('examiner.clinics.show', $overflow) }}" style="font-size:12px;color:#059669;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-top:8px;">View overflow <i class="fa-solid fa-arrow-right fa-xs"></i></a>
                @endif
            </div>
            @elseif($clinic->isFull())
            <div style="background:rgba(232,82,42,.06);border:1px solid rgba(232,82,42,.2);border-radius:14px;padding:16px;font-size:13px;color:#B45309;">
                <i class="fa-solid fa-users fa-sm"></i> <strong>Session is full</strong> – overflow will be auto-created when a student books, or you can trigger it manually from the admin panel.
            </div>
            @endif

            {{-- Parent clinic link --}}
            @if($clinic->parentClinic)
            <div style="background:rgba(10,100,188,.05);border:1px solid rgba(10,100,188,.15);border-radius:14px;padding:14px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#0A64BC;margin-bottom:6px;">Overflow from</div>
                <a href="{{ route('examiner.clinics.show', $clinic->parentClinic) }}" style="font-size:13px;font-weight:600;color:#0A64BC;text-decoration:none;">
                    {{ $clinic->parentClinic->title }} <i class="fa-solid fa-arrow-up-right-from-square fa-xs"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
    <style>.detail-g{grid-template-columns:1fr!important;}@media(min-width:900px){.detail-g{grid-template-columns:1fr 320px!important;}}</style>
</div>
</section>

<script>
// Countdown timer
@if(!$canJoinNow && !$isCancelled)
(function(){
    var target = new Date("{{ $clinic->scheduled_at->toIso8601String() }}").getTime();
    var el = document.getElementById('countdown');
    if (!el) return;
    function tick(){
        var diff = target - Date.now();
        if(diff <= 0){ el.textContent = 'Starting…'; return; }
        var h = Math.floor(diff/3600000);
        var m = Math.floor((diff%3600000)/60000);
        var s = Math.floor((diff%60000)/1000);
        el.textContent = (h > 0 ? h+'h ' : '') + m+'m ' + s+'s';
        setTimeout(tick, 1000);
    }
    tick();
})();
@endif
</script>

@include('partials.footer')
@livewireScripts
</body>
</html>
