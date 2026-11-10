@extends('layouts.admin')
@section('title', 'Group Session Suggestions Management')
@section('content')
<div style="max-width:1400px;margin:0 auto;padding:24px;">
 <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:16px;">
 <div>
 <h1 style="font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:var(--ink);margin-bottom:6px;">Group Session Suggestions</h1>
 <p style="color:var(--muted);font-size:14px;">Manage topic suggestions and voting</p>
 </div>
 </div>

 @if(session('success'))
 <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:12px;">
 <span style="font-size:20px;">✓</span>
 <span style="color:#059669;font-weight:600;">{{ session('success') }}</span>
 </div>
 @endif

 @if(session('error'))
 <div style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:12px;">
 <span style="font-size:20px;">!</span>
 <span style="color:#dc2626;font-weight:600;">{{ session('error') }}</span>
 </div>
 @endif

 {{-- Filters --}}
 <div style="background:#fff;border-radius:14px;border:1px solid var(--hair);padding:16px;margin-bottom:20px;">
 <form method="GET" action="{{ route('admin.group-sessions.suggestions') }}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;align-items:end;">
 <div>
 <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Search</label>
 <input type="text" name="search" value="{{ request('search') }}" placeholder="Area, subject, creator..." style="width:100%;padding:10px;border:1px solid var(--hair);border-radius:8px;font-size:14px;font-family:inherit;">
 </div>

 <div>
 <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Group Session</label>
 <select name="group_session_id" style="width:100%;padding:10px;border:1px solid var(--hair);border-radius:8px;font-size:14px;font-family:inherit;cursor:pointer;background:#fff;">
 <option value="">-- All Sessions --</option>
 @foreach($groupSessions as $session)
 <option value="{{ $session->id }}" {{ request('group_session_id') == $session->id ? 'selected' : '' }}>
 {{ Str::limit($session->title, 50) }} • {{ $session->scheduled_at->format('M j, g:ia') }}
 </option>
 @endforeach
 </select>
 </div>

 <div style="display:flex;gap:8px;">
 <button type="submit" class="btn btn-v" style="flex:1;">
 <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
 Filter
 </button>
 <a href="{{ route('admin.group-sessions.suggestions') }}" class="btn btn-o" style="flex:1;text-align:center;">
 Reset
 </a>
 </div>
 </form>
 </div>

 {{-- Table --}}
 <div class="card">
 <div style="overflow-x:auto;">
 <table id="suggestions-table" style="width:100%;border-collapse:collapse;">
 <thead style="background:rgba(21,40,31,.03);">
 <tr>
 <th style="padding:14px;text-align:left;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;border-bottom:1px solid var(--hair);">Group Session</th>
 <th style="padding:14px;text-align:left;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;border-bottom:1px solid var(--hair);">Subject</th>
 <th style="padding:14px;text-align:left;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;border-bottom:1px solid var(--hair);">Area</th>
 <th style="padding:14px;text-align:center;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;border-bottom:1px solid var(--hair);">Votes</th>
 <th style="padding:14px;text-align:left;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;border-bottom:1px solid var(--hair);">Created By</th>
 <th style="padding:14px;text-align:left;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;border-bottom:1px solid var(--hair);">Created</th>
 <th style="padding:14px;text-align:center;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;border-bottom:1px solid var(--hair);">Actions</th>
 </tr>
 </thead>
 <tbody>
 @forelse($suggestions as $suggestion)
 <tr style="border-bottom:1px solid var(--hair);">
 <td style="padding:14px;">
 <div style="font-weight:600;font-size:13px;color:var(--ink);">{{ Str::limit($suggestion->groupSession?->title ?? 'N/A', 40) }}</div>
 <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $suggestion->groupSession?->scheduled_at?->format('M j, g:ia') ?? '-' }}</div>
 </td>
 <td style="padding:14px;font-size:13px;">
 @if($suggestion->subject)
 <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(10,100,188,.1);color:#0A64BC;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;">
 📚 {{ $suggestion->subject->name }}
 </span>
 @elseif($suggestion->custom_subject)
 <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(21,40,31,.06);color:var(--ink);padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;">
 ✏️ {{ $suggestion->custom_subject }}
 </span>
 @else
 <span style="color:var(--muted);">-</span>
 @endif
 </td>
 <td style="padding:14px;font-size:13px;">
 <span title="{{ $suggestion->area }}">{{ Str::limit($suggestion->area, 50) }}</span>
 </td>
 <td style="padding:14px;text-align:center;font-weight:700;color:var(--trust-blue);">
 👍 {{ $suggestion->votes_count }}
 </td>
 <td style="padding:14px;font-size:13px;">
 @if($suggestion->createdBy)
 <span style="font-weight:600;">{{ $suggestion->createdBy->name }}</span><br>
 <span style="color:var(--muted);font-size:12px;">{{ $suggestion->createdBy->email }}</span>
 @else
 <span style="color:var(--muted);font-style:italic;">Guest</span>
 @endif
 </td>
 <td style="padding:14px;font-size:13px;color:var(--muted);">
 {{ $suggestion->created_at->format('M j, Y') }}
 </td>
 <td style="padding:14px;text-align:center;">
 <form 
 method="POST" 
 action="{{ route('admin.group-sessions.suggestions.destroy', $suggestion) }}" 
 style="display:inline;"
 onsubmit="return confirm('Delete this suggestion? This action cannot be undone.');"
 >
 @csrf
 @method('DELETE')
 <button type="submit" class="btn btn-sm" style="background:rgba(239,68,68,.1);color:#dc2626;border:1px solid rgba(239,68,68,.2);padding:6px 10px;">
 <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
 Delete
 </button>
 </form>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="7" style="padding:40px;text-align:center;color:var(--muted);">
 <p style="font-size:16px;margin:0;">No suggestions found.</p>
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>

 {{-- Pagination --}}
 @if($suggestions->hasPages())
 <div style="margin-top:24px;display:flex;justify-content:center;gap:8px;flex-wrap:wrap;">
 {{ $suggestions->links() }}
 </div>
 @endif
</div>

<style>
 @media(max-width:768px) {
 table {
 font-size: 12px;
 }
 
 td, th {
 padding: 10px 8px !important;
 }
 }
</style>
@endsection
