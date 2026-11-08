@extends('layouts.admin')
@section('title', 'Examiner Clinics')
@section('content')

<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
 <div>
 <h2 style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);">Examiner Clinics</h2>
 <p style="font-size:13px;color:var(--muted);margin-top:2px;">Manage all examiner clinic sessions</p>
 </div>
 <a href="{{ route('admin.examiner-clinics.create') }}" class="btn btn-primary">+ Create Clinic</a>
</div>

{{-- Filters --}}
<div class="card card-p" style="margin-bottom:20px;">
 <form method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
 <div style="flex:1;min-width:180px;">
 <label class="form-label">Search</label>
 <input type="text" name="search" class="form-input" placeholder="Title or description" value="{{ request('search') }}">
 </div>
 <div>
 <label class="form-label">Status</label>
 <select name="status" class="form-input" style="min-width:130px;">
 <option value="">All</option>
 <option value="scheduled" {{ request('status')==='scheduled'?'selected':'' }}>Scheduled</option>
 <option value="in_progress" {{ request('status')==='in_progress'?'selected':'' }}>In Progress</option>
 <option value="completed" {{ request('status')==='completed'?'selected':'' }}>Completed</option>
 <option value="cancelled" {{ request('status')==='cancelled'?'selected':'' }}>Cancelled</option>
 </select>
 </div>
 <div>
 <label class="form-label">Subject</label>
 <select name="subject_id" class="form-input" style="min-width:150px;">
 <option value="">All Subjects</option>
 @foreach($subjects as $s)
 <option value="{{ $s->id }}" {{ request('subject_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>
 @endforeach
 </select>
 </div>
 <button type="submit" class="btn btn-primary">Filter</button>
 @if(request()->hasAny(['search','status','subject_id']))
 <a href="{{ route('admin.examiner-clinics.index') }}" class="btn btn-secondary">Clear</a>
 @endif
 </form>
</div>

{{-- Table --}}
<div class="card" style="overflow:hidden;">
 <table style="width:100%;border-collapse:collapse;font-size:13px;">
 <thead>
 <tr style="background:rgba(21,40,31,.04);border-bottom:1px solid var(--hair);">
 <th style="padding:12px 16px;text-align:left;font-weight:700;color:var(--muted);text-transform:uppercase;font-size:11px;letter-spacing:.05em;">Clinic</th>
 <th style="padding:12px 16px;text-align:left;font-weight:700;color:var(--muted);text-transform:uppercase;font-size:11px;letter-spacing:.05em;">Subject</th>
 <th style="padding:12px 16px;text-align:left;font-weight:700;color:var(--muted);text-transform:uppercase;font-size:11px;letter-spacing:.05em;">Curriculum</th>
 <th style="padding:12px 16px;text-align:left;font-weight:700;color:var(--muted);text-transform:uppercase;font-size:11px;letter-spacing:.05em;">Scheduled</th>
 <th style="padding:12px 16px;text-align:left;font-weight:700;color:var(--muted);text-transform:uppercase;font-size:11px;letter-spacing:.05em;">Status</th>
 <th style="padding:12px 16px;text-align:right;font-weight:700;color:var(--muted);text-transform:uppercase;font-size:11px;letter-spacing:.05em;">Actions</th>
 </tr>
 </thead>
 <tbody>
 @forelse($clinics as $clinic)
 <tr style="border-bottom:1px solid var(--hair);">
 <td style="padding:12px 16px;font-weight:600;color:var(--ink);">
 {{ Str::limit($clinic->title, 45) }}
 @if($clinic->is_auto_created)
 <span style="font-size:10px;background:#EDE9FE;color:#5B21B6;padding:2px 7px;border-radius:4px;margin-left:5px;">Auto</span>
 @endif
 </td>
 <td style="padding:12px 16px;color:var(--muted);">{{ $clinic->subject?->name ?? '"' }}</td>
 <td style="padding:12px 16px;color:var(--muted);">
 @if($clinic->curriculum_id)
 <span style="font-size:11px;background:rgba(10,100,188,.08);color:#0A64BC;padding:2px 8px;border-radius:999px;">
 {{ $clinic->curriculum->name ?? '"' }}
 </span>
 @else
 <span style="font-size:11px;color:var(--muted);">All tracks</span>
 @endif
 </td>
 <td style="padding:12px 16px;color:var(--muted);white-space:nowrap;">{{ $clinic->scheduled_at->format('M j, Y g:ia') }}</td>
 <td style="padding:12px 16px;">
 @php $statusColors = ['scheduled'=>'#DBEAFE:#1E40AF','in_progress'=>'#DCFCE7:#166534','completed'=>'#F3F4F6:#6B7280','cancelled'=>'#FEE2E2:#991B1B']; $sc = explode(':',$statusColors[$clinic->status]??'#F3F4F6:#6B7280'); @endphp
 <span style="font-size:11px;font-weight:700;background:{{ $sc[0] }};color:{{ $sc[1] }};padding:3px 10px;border-radius:999px;">{{ ucwords(str_replace('_',' ',$clinic->status)) }}</span>
 </td>
 <td style="padding:12px 16px;text-align:right;">
 <div style="display:flex;gap:6px;justify-content:flex-end;">
 <a href="{{ route('admin.examiner-clinics.show', $clinic) }}" class="btn btn-secondary btn-sm">View</a>
 @if(!$clinic->is_auto_created && !in_array($clinic->status,['completed','cancelled']))
 <a href="{{ route('admin.examiner-clinics.edit', $clinic) }}" class="btn btn-outline btn-sm">Edit</a>
 @endif
 </div>
 </td>
 </tr>
 @empty
 <tr><td colspan="6" style="padding:48px;text-align:center;color:var(--muted);">No clinics found</td></tr>
 @endforelse
 </tbody>
 </table>
</div>

{{-- Pagination --}}
@if($clinics->hasPages())
<div style="margin-top:20px;display:flex;justify-content:center;">
 {{ $clinics->withQueryString()->links() }}
</div>
@endif

@endsection

