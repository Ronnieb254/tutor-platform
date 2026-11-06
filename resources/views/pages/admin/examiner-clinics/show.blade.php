@extends('layouts.admin')
@section('title', 'Clinic Detail')
@section('content')

<div style="display:flex;align-items:center;gap:14px;margin-bottom:24px;">
    <a href="{{ route('admin.examiner-clinics.index') }}" class="btn btn-outline btn-sm">Ã¢â€ Â Back</a>
    <div>
        <h2 style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);">{{ $examinerClinic->title }}</h2>
        <p style="font-size:13px;color:var(--muted);margin-top:2px;">
            @if($examinerClinic->curriculum_id)
            <span style="background:rgba(10,100,188,.08);color:#0A64BC;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;">{{ $examinerClinic->curriculum->name }}</span>
            @endif
        </p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger" style="margin-bottom:16px;">{{ session('error') }}</div>
@endif

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;" class="detail-grid">
    <div>
        {{-- Details --}}
        <div class="card card-p" style="margin-bottom:16px;">
            <h3 style="font-family:'Fraunces',serif;font-size:16px;font-weight:700;color:var(--ink);margin-bottom:14px;">Clinic Details</h3>
            <dl style="display:grid;grid-template-columns:1fr 2fr;gap:8px;font-size:13px;">
                <dt style="font-weight:600;color:var(--muted);">Subject</dt>
                <dd>{{ $examinerClinic->subject?->name ?? 'Ã¢â‚¬â€' }}</dd>
                <dt style="font-weight:600;color:var(--muted);">Curriculum</dt>
                <dd>{{ $examinerClinic->curriculum?->name ?? 'All tracks' }}</dd>
                <dt style="font-weight:600;color:var(--muted);">Scheduled</dt>
                <dd>{{ $examinerClinic->scheduled_at->format('D, M j, Y \a\t g:ia') }}</dd>
                <dt style="font-weight:600;color:var(--muted);">Duration</dt>
                <dd>{{ $examinerClinic->duration_minutes }} minutes</dd>
                <dt style="font-weight:600;color:var(--muted);">Status</dt>
                <dd>{{ ucwords(str_replace('_', ' ', $examinerClinic->status)) }}</dd>
                <dt style="font-weight:600;color:var(--muted);">Enrolled</dt>
                <dd>{{ $stats['total_enrolled'] }} / {{ $examinerClinic->max_students }}</dd>
            </dl>
        </div>

        {{-- Enrollments --}}
        <div class="card card-p">
            <h3 style="font-family:'Fraunces',serif;font-size:16px;font-weight:700;color:var(--ink);margin-bottom:14px;">Enrolled Students ({{ $stats['total_enrolled'] }})</h3>
            @forelse($examinerClinic->enrollments as $enrollment)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--hair);font-size:13px;">
                <div>
                    <div style="font-weight:600;">{{ $enrollment->user?->name ?? 'Unknown' }}</div>
                    <div style="font-size:11px;color:var(--muted);">{{ $enrollment->user?->email }}</div>
                </div>
                <span style="font-size:11px;background:#F3F4F6;color:#6B7280;padding:2px 8px;border-radius:999px;">{{ $enrollment->session_code }}</span>
            </div>
            @empty
            <p style="font-size:13px;color:var(--muted);">No enrollments yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Actions --}}
    <div style="height:fit-content;">
        <div class="card card-p">
            <h4 style="font-size:13px;font-weight:700;color:var(--ink);margin-bottom:12px;">Actions</h4>
            @if(!$examinerClinic->is_auto_created && !in_array($examinerClinic->status,['completed','cancelled']))
            <a href="{{ route('admin.examiner-clinics.edit', $examinerClinic) }}" class="btn btn-outline" style="width:100%;margin-bottom:8px;justify-content:center;">Edit Clinic</a>
            @endif
            @if($examinerClinic->status === 'scheduled')
            <form method="POST" action="{{ route('admin.examiner-clinics.start', $examinerClinic) }}" style="margin-bottom:8px;">
                @csrf
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Ã¢â€“Â¶ Start Session</button>
            </form>
            <form method="POST" action="{{ route('admin.examiner-clinics.cancel', $examinerClinic) }}">
                @csrf
                <button type="submit" class="btn btn-secondary" style="width:100%;justify-content:center;color:#991B1B;"
                        onclick="return confirm('Cancel this clinic?')">Cancel Clinic</button>
            </form>
            @elseif($examinerClinic->status === 'in_progress')
            <form method="POST" action="{{ route('admin.examiner-clinics.complete', $examinerClinic) }}">
                @csrf
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Ã¢Å“â€¦ Mark Complete</button>
            </form>
            @endif
        </div>
    </div>
</div>

<style>.detail-grid{grid-template-columns:1fr!important;}@media(min-width:900px){.detail-grid{grid-template-columns:2fr 1fr!important;}}</style>
@endsection
