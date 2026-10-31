@extends('layouts.admin')
@section('title', 'Group Sessions')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <h2 style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);">Group Sessions</h2>
        <p style="font-size:13px;color:var(--muted);margin-top:2px;">Create and manage admin-hosted group learning sessions</p>
    </div>
    <a href="{{ route('admin.group-sessions.create') }}"
       style="display:inline-flex;align-items:center;gap:7px;padding:11px 22px;border-radius:10px;background:#0A64BC;color:#fff;font-size:14px;font-weight:600;text-decoration:none;">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Create Group Session
    </a>
</div>

{{-- Stats --}}
<div class="stats-grid" style="margin-bottom:20px;">
    @foreach([
        ['Total','total','#0A64BC'],
        ['Scheduled','scheduled','#EEBE15'],
        ['In Progress','in_progress','#10B981'],
        ['Completed','completed','#6366F1'],
        ['Cancelled','cancelled','#E8522A'],
    ] as [$lbl, $key, $col])
    <a href="?status={{ $key === 'total' ? '' : $key }}"
       style="background:#fff;border-radius:14px;border:1.5px solid {{ ($status===$key || ($key==='total' && !$status)) ? $col : 'var(--hairline)' }};padding:16px 20px;text-decoration:none;display:block;">
        <div style="font-family:'Fraunces',serif;font-size:26px;font-weight:700;color:{{ $col }};">{{ $stats[$key] ?? 0 }}</div>
        <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $lbl }}</div>
    </a>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.group-sessions.index') }}"
      style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px;align-items:flex-end;">
    <div style="flex:1;min-width:180px;">
        <label class="form-label">Search</label>
        <input type="text" name="search" value="{{ $search }}" placeholder="Title or description…" class="form-input" style="height:38px;">
    </div>
    <div>
        <label class="form-label">Status</label>
        <select name="status" class="form-input form-select" onchange="this.form.submit()">
            <option value="">All</option>
            @foreach(['scheduled','in_progress','completed','cancelled'] as $s)
            <option value="{{ $s }}" {{ $status===$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label">Subject</label>
        <select name="subject_id" class="form-input form-select" onchange="this.form.submit()">
            <option value="">All subjects</option>
            @foreach($subjects as $sub)
            <option value="{{ $sub->id }}" {{ $subjectId==$sub->id?'selected':'' }}>{{ $sub->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Search</button>
    @if($search || $status || $subjectId)
    <a href="{{ route('admin.group-sessions.index') }}" class="btn btn-outline">Clear</a>
    @endif
</form>

{{-- Table --}}
<div class="card">
    <div style="padding:16px 24px;border-bottom:1px solid var(--hairline);display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:14px;color:var(--muted);">{{ $sessions->total() }} session{{ $sessions->total()!==1?'s':'' }}</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Title / Subject</th>
                    <th>Tutor</th>
                    <th>Scheduled</th>
                    <th>Slots</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $session->title }}</div>
                        @if($session->subject)<div style="font-size:11px;color:var(--muted);">{{ $session->subject->name }}</div>@endif
                    </td>
                    <td style="font-size:13px;color:var(--muted);">{{ $session->tutor?->name ?? '—' }}</td>
                    <td style="white-space:nowrap;font-size:13px;">
                        <div>{{ $session->scheduled_at->format('M j, Y') }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $session->scheduled_at->format('g:i a') }}</div>
                    </td>
                    <td>
                        @php $enrolled = $session->enrollments_count ?? 0; $cap = $session->max_students ?? 30; @endphp
                        <div style="font-size:13px;font-weight:600;">{{ $enrolled }} / {{ $cap }}</div>
                        <div style="background:var(--hairline);border-radius:4px;height:4px;width:80px;overflow:hidden;margin-top:3px;">
                            <div style="background:#0A64BC;height:100%;width:{{ $cap > 0 ? min(100,round($enrolled/$cap*100)) : 0 }}%;border-radius:4px;"></div>
                        </div>
                    </td>
                    <td style="font-weight:600;font-size:13px;">KSh {{ number_format($session->price ?? 0) }}</td>
                    <td>
                        @php $bc=['scheduled'=>'badge-blue','in_progress'=>'badge-green','completed'=>'badge-amber','cancelled'=>'badge-red'][$session->status]??'badge-gray'; @endphp
                        <span class="badge {{ $bc }}">{{ ucwords(str_replace('_',' ',$session->status)) }}</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <a href="{{ route('admin.group-sessions.show', $session) }}" class="btn btn-outline btn-sm">View</a>
                            @if($session->status === 'scheduled')
                            <a href="{{ route('admin.group-sessions.edit', $session) }}" class="btn btn-outline btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.group-sessions.start', $session) }}" style="display:inline;">
                                @csrf
                                <button class="btn btn-success btn-sm" onclick="return confirm('Start this session?')">Start</button>
                            </form>
                            @endif
                            @if(!in_array($session->status, ['completed','cancelled']))
                            <form method="POST" action="{{ route('admin.group-sessions.cancel', $session) }}" style="display:inline;">
                                @csrf
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Cancel this session?')">Cancel</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:40px;">No group sessions found. <a href="{{ route('admin.group-sessions.create') }}" style="color:#0A64BC;">Create one</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($sessions->hasPages())
    <div style="padding:16px 24px;border-top:1px solid var(--hairline);">{{ $sessions->withQueryString()->links() }}</div>
    @endif
</div>

@endsection
