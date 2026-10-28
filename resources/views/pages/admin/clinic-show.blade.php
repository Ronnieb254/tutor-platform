@extends('layouts.admin')
@section('title', 'Clinic – ' . ($clinic->title ?? 'Detail'))
@section('content')
<div style="max-width:1100px;margin:0 auto;padding:24px;">

    <div style="margin-bottom:20px;">
        <a href="{{ route('admin.clinics') }}" style="font-size:13px;color:var(--muted);text-decoration:none;display:inline-flex;align-items:center;gap:5px;margin-bottom:10px;">
            <i class="fa-solid fa-arrow-left fa-xs"></i> All Clinics
        </a>
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:14px;flex-wrap:wrap;">
            <div>
                <h1 style="font-family:'Fraunces',serif;font-size:26px;font-weight:700;color:var(--ink);margin-bottom:4px;">
                    {{ $clinic->title }}
                    @if($clinic->is_auto_created)
                    <span style="font-size:12px;background:rgba(212,160,23,.15);color:#B45309;padding:2px 10px;border-radius:999px;font-weight:700;vertical-align:middle;margin-left:6px;">AUTO-OVERFLOW</span>
                    @endif
                </h1>
                <div style="display:flex;flex-wrap:wrap;gap:10px;font-size:13px;color:var(--muted);">
                    @if($clinic->subject)<span><i class="fa-solid fa-book-open fa-xs" style="color:#0A64BC;"></i> {{ $clinic->subject->name }}</span>@endif
                    @if($clinic->paper_type)<span><i class="fa-solid fa-file-lines fa-xs" style="color:#0A64BC;"></i> {{ $clinic->paper_type }}</span>@endif
                    <span><i class="fa-regular fa-calendar fa-xs" style="color:#0A64BC;"></i> {{ $clinic->scheduled_at->format('D, M j, Y \a\t g:ia') }}</span>
                    <span><i class="fa-solid fa-clock fa-xs" style="color:#0A64BC;"></i> {{ $clinic->duration_minutes }} min</span>
                </div>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                {{-- Join as moderator --}}
                <a href="{{ route('examiner.clinics.join', $clinic) }}" style="display:inline-flex;align-items:center;gap:6px;padding:10px 18px;border-radius:10px;background:#10B981;color:#fff;font-size:13px;font-weight:600;text-decoration:none;">
                    <i class="fa-solid fa-video fa-sm"></i> Join Session
                </a>
                @if($clinic->isFull() && !$clinic->hasOverflowClinic() && !$clinic->is_auto_created)
                <form method="POST" action="{{ route('admin.clinics.overflow', $clinic) }}">
                    @csrf
                    <button type="submit" style="display:inline-flex;align-items:center;gap:6px;padding:10px 18px;border-radius:10px;background:rgba(212,160,23,.2);color:#B45309;font-size:13px;font-weight:600;border:none;cursor:pointer;font-family:inherit;">
                        <i class="fa-solid fa-plus fa-sm"></i> Create Overflow
                    </button>
                </form>
                @endif
                @if($clinic->status !== 'cancelled' && $clinic->scheduled_at->isFuture())
                <form method="POST" action="{{ route('admin.clinics.cancel', $clinic) }}" onsubmit="return confirm('Cancel this clinic?')">
                    @csrf
                    <button type="submit" style="display:inline-flex;align-items:center;gap:6px;padding:10px 18px;border-radius:10px;background:rgba(239,68,68,.1);color:#dc2626;font-size:13px;font-weight:600;border:none;cursor:pointer;font-family:inherit;">
                        <i class="fa-solid fa-ban fa-sm"></i> Cancel
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);border-radius:12px;padding:12px 16px;margin-bottom:18px;color:#059669;font-weight:600;">
        <i class="fa-solid fa-circle-check fa-sm"></i> {{ session('success') }}
    </div>
    @endif

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:18px;" class="detail-g">

        {{-- Left: Enrollment list --}}
        <div>
            <div style="background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.08);overflow:hidden;">
                <div style="padding:16px 20px;border-bottom:1px solid rgba(21,40,31,.08);display:flex;align-items:center;justify-content:space-between;">
                    <h2 style="font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:var(--ink);">
                        Enrolled Students
                    </h2>
                    <span style="font-size:13px;font-weight:700;color:{{ $clinic->isFull() ? '#E8522A' : '#10B981' }};">
                        {{ $clinic->enrollments->count() }} / {{ $clinic->max_students }} slots
                    </span>
                </div>

                {{-- Capacity bar --}}
                @php $fillPct = $clinic->max_students > 0 ? round(($clinic->enrollments->count() / $clinic->max_students) * 100) : 0; @endphp
                <div style="margin:0;height:6px;background:rgba(21,40,31,.06);">
                    <div style="width:{{ $fillPct }}%;height:100%;background:{{ $clinic->isFull() ? '#E8522A' : ($fillPct>70 ? '#EEBE15' : '#10B981') }};transition:width .3s;"></div>
                </div>

                @if($clinic->enrollments->isEmpty())
                <div style="padding:40px;text-align:center;color:var(--muted);">
                    <i class="fa-solid fa-user-group" style="font-size:28px;opacity:.3;margin-bottom:10px;display:block;"></i>
                    No students enrolled yet.
                </div>
                @else
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr style="background:rgba(21,40,31,.02);">
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">#</th>
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Student</th>
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Session Code</th>
                            <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Attended</th>
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Enrolled</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clinic->enrollments as $i => $enrollment)
                        <tr style="border-top:1px solid rgba(21,40,31,.05);">
                            <td style="padding:12px 16px;color:var(--muted);">{{ $i + 1 }}</td>
                            <td style="padding:12px 16px;">
                                <div style="font-weight:600;color:var(--ink);">{{ $enrollment->student?->name ?? '—' }}</div>
                                <div style="font-size:11px;color:var(--muted);">{{ $enrollment->student?->email }}</div>
                            </td>
                            <td style="padding:12px 16px;">
                                <code style="background:#F4F6F4;padding:3px 8px;border-radius:6px;font-size:12px;font-weight:700;color:#0A64BC;">{{ $enrollment->session_code }}</code>
                            </td>
                            <td style="padding:12px 16px;text-align:center;">
                                @if($enrollment->attended)
                                <span style="color:#10B981;"><i class="fa-solid fa-circle-check"></i></span>
                                @else
                                <span style="color:rgba(21,40,31,.2);"><i class="fa-solid fa-circle-minus"></i></span>
                                @endif
                            </td>
                            <td style="padding:12px 16px;font-size:12px;color:var(--muted);">{{ $enrollment->created_at->format('M j, g:ia') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            {{-- Overflow / child sessions --}}
            @if($clinic->childSessions->isNotEmpty())
            <div style="background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.08);overflow:hidden;margin-top:16px;">
                <div style="padding:14px 20px;border-bottom:1px solid rgba(21,40,31,.08);">
                    <h2 style="font-family:'Fraunces',serif;font-size:16px;font-weight:700;color:var(--ink);">Auto-Created Overflow Sessions</h2>
                </div>
                @foreach($clinic->childSessions as $child)
                <div style="padding:14px 20px;border-bottom:1px solid rgba(21,40,31,.05);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                    <div>
                        <div style="font-weight:600;font-size:13px;color:var(--ink);">{{ $child->title }}</div>
                        <div style="font-size:12px;color:var(--muted);">{{ $child->scheduled_at->format('D, M j \a\t g:ia') }} · {{ $child->enrollments_count ?? $child->enrollments()->count() }}/30 enrolled</div>
                    </div>
                    <a href="{{ route('admin.clinics.show', $child) }}" style="font-size:12px;color:#0A64BC;text-decoration:none;font-weight:600;">View <i class="fa-solid fa-arrow-right fa-xs"></i></a>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Right: Meta panel --}}
        <div style="display:flex;flex-direction:column;gap:14px;">

            {{-- Status card --}}
            <div style="background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.08);padding:18px;">
                <h3 style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:14px;">Session Info</h3>
                @foreach([
                    ['Status',        ucfirst($clinic->status ?? 'scheduled')],
                    ['Batch',         'Batch ' . $clinic->batch_number],
                    ['Price',         'KSh ' . number_format($clinic->price)],
                    ['Duration',      $clinic->duration_minutes . ' minutes'],
                    ['Max Students',  '30'],
                    ['Meeting Room',  $clinic->meeting_room ?? '—'],
                ] as [$k,$v])
                <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid rgba(21,40,31,.05);font-size:13px;">
                    <span style="color:var(--muted);">{{ $k }}</span>
                    <span style="font-weight:600;color:var(--ink);">{{ $v }}</span>
                </div>
                @endforeach
            </div>

            {{-- Tutor card --}}
            @if($clinic->tutor)
            <div style="background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.08);padding:18px;">
                <h3 style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:10px;">Tutor</h3>
                <div style="font-weight:600;color:var(--ink);">{{ $clinic->tutor->name }}</div>
                <div style="font-size:12px;color:var(--muted);">{{ $clinic->tutor->email }}</div>
                <a href="{{ route('examiner.clinics.join', $clinic) }}" style="display:block;margin-top:12px;text-align:center;padding:9px;border-radius:10px;background:#10B981;color:#fff;font-size:13px;font-weight:600;text-decoration:none;">
                    <i class="fa-solid fa-video fa-sm"></i> Launch Session
                </a>
            </div>
            @endif

            {{-- Parent clinic link --}}
            @if($clinic->parentClinic)
            <div style="background:rgba(212,160,23,.06);border:1px solid rgba(212,160,23,.2);border-radius:16px;padding:16px;">
                <h3 style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#B45309;margin-bottom:8px;">Overflow from</h3>
                <a href="{{ route('admin.clinics.show', $clinic->parentClinic) }}" style="font-size:13px;font-weight:600;color:#B45309;text-decoration:none;">
                    {{ $clinic->parentClinic->title }} <i class="fa-solid fa-arrow-up-right-from-square fa-xs"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
    <style>.detail-g{grid-template-columns:1fr!important;}@media(min-width:900px){.detail-g{grid-template-columns:2fr 1fr!important;}}</style>
</div>
@endsection
