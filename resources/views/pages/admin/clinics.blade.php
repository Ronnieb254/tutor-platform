@extends('layouts.admin')
@section('title', 'Clinic Management')
@section('content')
<div style="max-width:1400px;margin:0 auto;padding:24px;">

    {{-- Page header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-bottom:24px;">
        <div>
            <h1 style="font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:var(--ink);margin-bottom:6px;">Examiner Clinic Management</h1>
            <p style="color:var(--muted);font-size:14px;">Create daily clinics, monitor bookings and overflow sessions.</p>
        </div>
        <a href="{{ route('admin.clinics.create') }}" style="display:inline-flex;align-items:center;gap:7px;padding:11px 22px;border-radius:10px;background:#0A64BC;color:#fff;font-size:14px;font-weight:600;text-decoration:none;">
            <i class="fa-solid fa-plus fa-sm"></i> Create Clinic
        </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
        <i class="fa-solid fa-circle-check" style="color:#059669;"></i>
        <span style="color:#059669;font-weight:600;">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
        <i class="fa-solid fa-circle-xmark" style="color:#dc2626;"></i>
        <span style="color:#dc2626;font-weight:600;">{{ session('error') }}</span>
    </div>
    @endif
    @if(session('info'))
    <div style="background:rgba(10,100,188,.1);border:1px solid rgba(10,100,188,.3);border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
        <i class="fa-solid fa-circle-info" style="color:#0A64BC;"></i>
        <span style="color:#0A64BC;font-weight:600;">{{ session('info') }}</span>
    </div>
    @endif

    {{-- Stats row --}}
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:22px;" class="stat-g">
        @foreach([
            ['Total Clinics',    $stats['total'],     '#0A64BC', 'fa-calendar-days',   'rgba(10,100,188,.1)'],
            ['Scheduled',        $stats['scheduled'], '#EEBE15', 'fa-calendar-check',  'rgba(212,160,23,.1)'],
            ['Today',            $stats['today'],     '#10B981', 'fa-circle-dot',       'rgba(16,185,129,.1)'],
            ['Full (30/30)',      $stats['full'],      '#E8522A', 'fa-users',            'rgba(232,82,42,.1)'],
        ] as [$label,$val,$color,$icon,$bg])
        <div style="background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.08);padding:18px 22px;display:flex;align-items:center;gap:14px;">
            <div style="width:44px;height:44px;border-radius:12px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid {{ $icon }}" style="color:{{ $color }};font-size:18px;"></i>
            </div>
            <div>
                <div style="font-size:24px;font-weight:800;color:var(--ink);">{{ $val }}</div>
                <div style="font-size:12px;color:var(--muted);">{{ $label }}</div>
            </div>
        </div>
        @endforeach
    </div>
    <style>.stat-g{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:768px){.stat-g{grid-template-columns:repeat(4,1fr)!important;}}</style>

    {{-- Filter bar --}}
    <form method="GET" style="background:#fff;border-radius:14px;border:1px solid rgba(21,40,31,.08);padding:14px 18px;margin-bottom:18px;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:2;min-width:160px;">
            <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:4px;display:block;">Search</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Title, subject…" style="width:100%;height:38px;padding:0 11px;border-radius:8px;border:1.5px solid rgba(21,40,31,.1);font-size:13px;font-family:inherit;">
        </div>
        <div style="min-width:140px;">
            <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:4px;display:block;">Status</label>
            <select name="status" onchange="this.form.submit()" style="width:100%;height:38px;padding:0 11px;border-radius:8px;border:1.5px solid rgba(21,40,31,.1);font-size:13px;font-family:inherit;background:#fff;">
                <option value="">All</option>
                <option value="scheduled" {{ $status==='scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="cancelled" {{ $status==='cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="completed" {{ $status==='completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        <div style="min-width:150px;">
            <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:4px;display:block;">Date</label>
            <input type="date" name="date" value="{{ $dateStr }}" onchange="this.form.submit()" style="width:100%;height:38px;padding:0 11px;border-radius:8px;border:1.5px solid rgba(21,40,31,.1);font-size:13px;font-family:inherit;">
        </div>
        <div style="display:flex;gap:6px;">
            <button type="submit" style="height:38px;padding:0 16px;border-radius:8px;background:#0A64BC;color:#fff;font-size:13px;font-weight:600;border:none;cursor:pointer;font-family:inherit;">Filter</button>
            @if($search || $status || $dateStr)
            <a href="{{ route('admin.clinics') }}" style="height:38px;padding:0 14px;border-radius:8px;background:#F4F6F4;color:var(--ink);font-size:13px;font-weight:500;border:none;text-decoration:none;display:inline-flex;align-items:center;">Clear</a>
            @endif
        </div>
    </form>

    {{-- Clinics table --}}
    <div style="background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.08);overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:rgba(21,40,31,.03);border-bottom:1px solid rgba(21,40,31,.08);">
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Clinic</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Scheduled</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Tutor</th>
                    <th style="padding:12px 16px;text-align:center;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Slots</th>
                    <th style="padding:12px 16px;text-align:center;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Status</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clinics as $clinic)
                @php
                    $isFull     = $clinic->enrollments_count >= $clinic->max_students;
                    $fillPct    = $clinic->max_students > 0 ? round(($clinic->enrollments_count / $clinic->max_students) * 100) : 0;
                    $isToday    = $clinic->scheduled_at->isToday();
                    $isPast     = $clinic->scheduled_at->isPast();
                @endphp
                <tr style="border-bottom:1px solid rgba(21,40,31,.05);{{ $isToday ? 'background:rgba(10,100,188,.03);' : '' }}">
                    <td style="padding:14px 16px;">
                        <div style="font-weight:600;color:var(--ink);margin-bottom:3px;">
                            {{ $clinic->title }}
                            @if($clinic->is_auto_created)
                            <span style="font-size:10px;background:rgba(212,160,23,.15);color:#B45309;padding:1px 7px;border-radius:999px;font-weight:700;margin-left:5px;">AUTO</span>
                            @endif
                        </div>
                        <div style="font-size:11px;color:var(--muted);display:flex;gap:8px;flex-wrap:wrap;">
                            @if($clinic->subject)
                            <span><i class="fa-solid fa-book-open fa-xs"></i> {{ $clinic->subject->name }}</span>
                            @endif
                            @if($clinic->paper_type)
                            <span><i class="fa-solid fa-file-lines fa-xs"></i> {{ $clinic->paper_type }}</span>
                            @endif
                            @if($clinic->batch_number > 1)
                            <span>Batch {{ $clinic->batch_number }}</span>
                            @endif
                        </div>
                    </td>
                    <td style="padding:14px 16px;">
                        <div style="font-weight:600;color:{{ $isToday ? '#0A64BC' : 'var(--ink)' }};">
                            {{ $clinic->scheduled_at->format('D, M j') }}
                        </div>
                        <div style="font-size:11px;color:var(--muted);">{{ $clinic->scheduled_at->format('g:ia') }} · {{ $clinic->duration_minutes }}min</div>
                    </td>
                    <td style="padding:14px 16px;">
                        <div style="font-size:13px;color:var(--ink);">{{ $clinic->tutor?->name ?? '—' }}</div>
                    </td>
                    <td style="padding:14px 16px;text-align:center;">
                        <div style="font-weight:700;color:{{ $isFull ? '#E8522A' : 'var(--ink)' }};">
                            {{ $clinic->enrollments_count }}/{{ $clinic->max_students }}
                        </div>
                        <div style="width:60px;height:4px;background:rgba(21,40,31,.08);border-radius:999px;overflow:hidden;margin:4px auto 0;">
                            <div style="width:{{ $fillPct }}%;height:100%;background:{{ $isFull ? '#E8522A' : ($fillPct>70 ? '#EEBE15' : '#10B981') }};border-radius:999px;"></div>
                        </div>
                    </td>
                    <td style="padding:14px 16px;text-align:center;">
                        @if($clinic->status === 'cancelled')
                        <span style="background:rgba(239,68,68,.1);color:#dc2626;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;">Cancelled</span>
                        @elseif($isPast && $clinic->status !== 'cancelled')
                        <span style="background:rgba(21,40,31,.08);color:var(--muted);padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;">Completed</span>
                        @elseif($isToday)
                        <span style="background:rgba(16,185,129,.1);color:#10B981;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;"><i class="fa-solid fa-circle fa-xs"></i> Today</span>
                        @else
                        <span style="background:rgba(10,100,188,.1);color:#0A64BC;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;">Scheduled</span>
                        @endif
                    </td>
                    <td style="padding:14px 16px;text-align:right;">
                        <div style="display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap;">
                            <a href="{{ route('admin.clinics.show', $clinic) }}" style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border-radius:7px;background:rgba(10,100,188,.1);color:#0A64BC;font-size:12px;font-weight:600;text-decoration:none;">
                                <i class="fa-solid fa-eye fa-xs"></i> View
                            </a>
                            @if($isFull && $clinic->status !== 'cancelled' && !$clinic->is_auto_created)
                            <form method="POST" action="{{ route('admin.clinics.overflow', $clinic) }}" style="display:inline;">
                                @csrf
                                <button type="submit" style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border-radius:7px;background:rgba(212,160,23,.15);color:#B45309;font-size:12px;font-weight:600;border:none;cursor:pointer;font-family:inherit;" title="Create next batch (auto)">
                                    <i class="fa-solid fa-plus fa-xs"></i> Overflow
                                </button>
                            </form>
                            @endif
                            @if($clinic->status !== 'cancelled' && !$isPast)
                            <form method="POST" action="{{ route('admin.clinics.cancel', $clinic) }}" style="display:inline;" onsubmit="return confirm('Cancel this clinic? Students will need to be notified manually.')">
                                @csrf
                                <button type="submit" style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border-radius:7px;background:rgba(239,68,68,.1);color:#dc2626;font-size:12px;font-weight:600;border:none;cursor:pointer;font-family:inherit;">
                                    <i class="fa-solid fa-ban fa-xs"></i> Cancel
                                </button>
                            </form>
                            @endif
                            @if($clinic->enrollments_count === 0 && $clinic->status !== 'cancelled')
                            <form method="POST" action="{{ route('admin.clinics.destroy', $clinic) }}" style="display:inline;" onsubmit="return confirm('Permanently delete this clinic?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border-radius:7px;background:rgba(239,68,68,.08);color:#dc2626;font-size:12px;font-weight:600;border:none;cursor:pointer;font-family:inherit;">
                                    <i class="fa-solid fa-trash fa-xs"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:48px;text-align:center;color:var(--muted);">
                        <i class="fa-solid fa-calendar-xmark" style="font-size:32px;margin-bottom:10px;display:block;opacity:.3;"></i>
                        No clinics found. <a href="{{ route('admin.clinics.create') }}" style="color:#0A64BC;font-weight:600;">Create one now.</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($clinics->hasPages())
    <div style="margin-top:20px;display:flex;justify-content:center;gap:5px;flex-wrap:wrap;">
        @if($clinics->onFirstPage())
        <span style="padding:7px 14px;border-radius:8px;background:#F4F6F4;color:rgba(21,40,31,.35);font-size:13px;"><i class="fa-solid fa-chevron-left fa-xs"></i></span>
        @else
        <a href="{{ $clinics->previousPageUrl() }}" style="padding:7px 14px;border-radius:8px;background:#fff;color:var(--ink);font-size:13px;border:1px solid rgba(21,40,31,.1);text-decoration:none;"><i class="fa-solid fa-chevron-left fa-xs"></i></a>
        @endif
        @foreach($clinics->getUrlRange(max(1,$clinics->currentPage()-2), min($clinics->lastPage(),$clinics->currentPage()+2)) as $page => $url)
        <a href="{{ $url }}" style="padding:7px 14px;border-radius:8px;background:{{ $page==$clinics->currentPage() ? '#0A64BC' : '#fff' }};color:{{ $page==$clinics->currentPage() ? '#fff' : 'var(--ink)' }};font-size:13px;border:1px solid {{ $page==$clinics->currentPage() ? '#0A64BC' : 'rgba(21,40,31,.1)' }};text-decoration:none;font-weight:600;">{{ $page }}</a>
        @endforeach
        @if($clinics->hasMorePages())
        <a href="{{ $clinics->nextPageUrl() }}" style="padding:7px 14px;border-radius:8px;background:#fff;color:var(--ink);font-size:13px;border:1px solid rgba(21,40,31,.1);text-decoration:none;"><i class="fa-solid fa-chevron-right fa-xs"></i></a>
        @else
        <span style="padding:7px 14px;border-radius:8px;background:#F4F6F4;color:rgba(21,40,31,.35);font-size:13px;"><i class="fa-solid fa-chevron-right fa-xs"></i></span>
        @endif
    </div>
    @endif
</div>
@endsection
