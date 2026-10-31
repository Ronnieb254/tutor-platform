@extends('layouts.admin')
@section('title', 'Group Session – ' . $groupSession->title)

@section('content')

@php
$statusColor = ['scheduled'=>'badge-blue','in_progress'=>'badge-green','completed'=>'badge-amber','cancelled'=>'badge-red'][$groupSession->status] ?? 'badge-gray';
$statusLabel = ucwords(str_replace('_',' ',$groupSession->status));
@endphp

{{-- Header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap;">
    <div>
        <a href="{{ route('admin.group-sessions.index') }}" style="display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--muted);text-decoration:none;margin-bottom:8px;">
            ← All Group Sessions
        </a>
        <h2 style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);">{{ $groupSession->title }}</h2>
        <div style="display:flex;align-items:center;gap:10px;margin-top:6px;flex-wrap:wrap;">
            <span class="badge {{ $statusColor }}">{{ $statusLabel }}</span>
            @if($groupSession->subject)<span style="font-size:13px;color:var(--muted);">{{ $groupSession->subject->name }}</span>@endif
            <span style="font-size:13px;color:var(--muted);">{{ $groupSession->scheduled_at->format('D, M j, Y \a\t g:i a') }}</span>
        </div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        @if($groupSession->status === 'scheduled')
        <a href="{{ route('admin.group-sessions.edit', $groupSession) }}" class="btn btn-outline">Edit</a>
        <form method="POST" action="{{ route('admin.group-sessions.start', $groupSession) }}" style="display:inline;">
            @csrf
            <button class="btn btn-success" onclick="return confirm('Start this session now?')">▶ Start Session</button>
        </form>
        @endif
        @if($groupSession->status === 'in_progress')
        <form method="POST" action="{{ route('admin.group-sessions.complete', $groupSession) }}" style="display:inline;">
            @csrf
            <button class="btn btn-primary" onclick="return confirm('Mark session as completed?')">✓ Mark Complete</button>
        </form>
        @endif
        @if(!in_array($groupSession->status, ['completed','cancelled']))
        <form method="POST" action="{{ route('admin.group-sessions.cancel', $groupSession) }}" style="display:inline;">
            @csrf
            <button class="btn btn-danger" onclick="return confirm('Cancel this group session?')">✕ Cancel</button>
        </form>
        @endif
        @if($groupSession->status === 'scheduled' && $groupSession->enrollments->count() === 0)
        <form method="POST" action="{{ route('admin.group-sessions.destroy', $groupSession) }}" style="display:inline;">
            @csrf @method('DELETE')
            <button class="btn btn-danger" style="background:transparent;color:#E8522A;border:1px solid #E8522A;" onclick="return confirm('Permanently delete this session?')">Delete</button>
        </form>
        @endif
    </div>
</div>

@if(session('success'))<div style="background:#D1FAE5;border:1px solid #6EE7B7;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#065F46;font-size:13px;">✓ {{ session('success') }}</div>@endif
@if(session('error'))<div style="background:#FEE2E2;border:1px solid #FECACA;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#991B1B;font-size:13px;">⚠ {{ session('error') }}</div>@endif

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;" class="show-grid">

    {{-- Left: Enrolled Students --}}
    <div class="card">
        <div style="padding:20px 24px;border-bottom:1px solid var(--hairline);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <h3 style="font-family:'Fraunces',serif;font-size:16px;font-weight:700;color:var(--ink);">Enrolled Students</h3>
                <p style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $stats['enrolled'] }} / {{ $stats['capacity'] }} spots filled</p>
            </div>
            <div style="background:var(--hairline);border-radius:999px;width:100px;height:8px;overflow:hidden;">
                <div style="background:#0A64BC;height:100%;width:{{ $stats['capacity']>0?min(100,round($stats['enrolled']/$stats['capacity']*100)):0 }}%;border-radius:999px;"></div>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Session Code</th>
                        <th>Enrolled</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groupSession->enrollments as $i => $enrollment)
                    <tr>
                        <td style="color:var(--muted);font-size:13px;">{{ $i + 1 }}</td>
                        <td>
                            <div style="font-weight:600;font-size:13px;">{{ $enrollment->student?->name ?? '—' }}</div>
                            <div style="font-size:11px;color:var(--muted);">{{ $enrollment->student?->email }}</div>
                        </td>
                        <td><span style="font-family:monospace;font-size:12px;background:rgba(10,100,188,.08);color:#0A64BC;padding:3px 8px;border-radius:6px;">{{ $enrollment->session_code ?? '—' }}</span></td>
                        <td style="font-size:12px;color:var(--muted);">{{ $enrollment->created_at->format('M j, g:ia') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:32px;color:var(--muted);">No students enrolled yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Right: Session Meta --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Session Info --}}
        <div class="card" style="padding:20px;">
            <h4 style="font-family:'Fraunces',serif;font-size:14px;font-weight:700;color:var(--ink);margin-bottom:14px;">Session Info</h4>
            @foreach([
                ['Status',         $statusLabel],
                ['Session Code',   $groupSession->session_code],
                ['Meeting Room',   $groupSession->meeting_room],
                ['Price',          'KSh ' . number_format($groupSession->price ?? 0)],
                ['Duration',       ($groupSession->duration_minutes ?? 60) . ' minutes'],
                ['Max Students',   $groupSession->max_students ?? 30],
                ['Scheduled',      $groupSession->scheduled_at->format('D, M j, Y \a\t g:i a')],
                ['Created By',     $groupSession->creator?->name ?? 'System'],
            ] as [$label, $value])
            <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--hairline);font-size:13px;">
                <span style="color:var(--muted);">{{ $label }}</span>
                <span style="font-weight:600;color:var(--ink);text-align:right;max-width:170px;word-break:break-all;">{{ $value }}</span>
            </div>
            @endforeach
        </div>

        {{-- Tutor --}}
        @if($groupSession->tutor)
        <div class="card" style="padding:20px;">
            <h4 style="font-family:'Fraunces',serif;font-size:14px;font-weight:700;color:var(--ink);margin-bottom:12px;">Assigned Tutor</h4>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                <div style="width:38px;height:38px;border-radius:10px;background:#0A64BC;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#fff;flex-shrink:0;">{{ substr($groupSession->tutor->name,0,1) }}</div>
                <div>
                    <div style="font-weight:600;font-size:14px;color:var(--ink);">{{ $groupSession->tutor->name }}</div>
                    <div style="font-size:12px;color:var(--muted);">{{ $groupSession->tutor->email }}</div>
                </div>
            </div>
            @if(in_array($groupSession->status, ['scheduled','in_progress']))
            <a href="{{ route('examiner.clinics.join', $groupSession) }}"
               style="display:flex;align-items:center;justify-content:center;gap:7px;padding:10px;border-radius:10px;background:#10B981;color:#fff;font-size:13px;font-weight:700;text-decoration:none;">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                Join Session (Moderator)
            </a>
            @endif
        </div>
        @endif

        {{-- Subject --}}
        @if($groupSession->subject)
        <div class="card" style="padding:16px 20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:6px;">Subject</div>
            <div style="font-weight:600;color:var(--ink);">{{ $groupSession->subject->name }}</div>
        </div>
        @endif

        {{-- Description --}}
        @if($groupSession->description)
        <div class="card" style="padding:16px 20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:6px;">Description</div>
            <div style="font-size:13px;color:var(--ink);line-height:1.7;">{{ $groupSession->description }}</div>
        </div>
        @endif
    </div>
</div>

<style>
@media(max-width:900px){.show-grid{grid-template-columns:1fr!important;}}
</style>

@endsection
