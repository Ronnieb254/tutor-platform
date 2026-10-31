@extends('layouts.admin')
@section('title', 'Edit Group Session')

@section('content')

<div style="display:flex;align-items:center;gap:14px;margin-bottom:24px;">
    <a href="{{ route('admin.group-sessions.show', $groupSession) }}" class="btn btn-outline btn-sm">← Back</a>
    <div>
        <h2 style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);">Edit Group Session</h2>
        <p style="font-size:13px;color:var(--muted);margin-top:2px;">{{ $groupSession->title }}</p>
    </div>
</div>

@if($errors->any())
<div style="background:#FEE2E2;border:1px solid #FECACA;border-radius:12px;padding:14px 18px;margin-bottom:20px;">
    <p style="font-weight:700;color:#991B1B;margin-bottom:6px;">Please fix the following:</p>
    <ul style="margin:0;padding-left:18px;color:#991B1B;font-size:13px;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 280px;gap:20px;" class="form-grid">
    <form method="POST" action="{{ route('admin.group-sessions.update', $groupSession) }}">
        @csrf @method('PATCH')

        <div class="card" style="padding:28px;">
            <h3 style="font-family:'Fraunces',serif;font-size:16px;font-weight:700;color:var(--ink);margin-bottom:20px;">Session Details</h3>

            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">Title <span style="color:#E8522A;">*</span></label>
                <input type="text" name="title" class="form-input"
                       value="{{ old('title', $groupSession->title) }}" required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;" class="two-col">
                <div>
                    <label class="form-label">Subject <span style="color:#E8522A;">*</span></label>
                    <select name="subject_id" class="form-input form-select" required>
                        <option value="">— Select subject —</option>
                        @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ old('subject_id', $groupSession->subject_id)==$s->id?'selected':'' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Assign Tutor</label>
                    <select name="tutor_id" class="form-input form-select">
                        <option value="">— TBD —</option>
                        @foreach($tutors as $t)
                        <option value="{{ $t->id }}" {{ old('tutor_id', $groupSession->tutor_id)==$t->id?'selected':'' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-input" rows="3"
                          style="height:auto;padding:12px 14px;resize:vertical;">{{ old('description', $groupSession->description) }}</textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;" class="two-col">
                <div>
                    <label class="form-label">Scheduled Date & Time <span style="color:#E8522A;">*</span></label>
                    <input type="datetime-local" name="scheduled_at" class="form-input"
                           value="{{ old('scheduled_at', $groupSession->scheduled_at->format('Y-m-d\TH:i')) }}" required>
                </div>
                <div>
                    <label class="form-label">Duration (minutes) <span style="color:#E8522A;">*</span></label>
                    <input type="number" name="duration_minutes" class="form-input"
                           value="{{ old('duration_minutes', $groupSession->duration_minutes) }}" min="15" max="480" required>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;" class="two-col">
                <div>
                    <label class="form-label">Max Students</label>
                    <input type="number" name="max_students" class="form-input"
                           value="{{ old('max_students', $groupSession->max_students) }}" min="1" max="200">
                    @php $enrolled = $groupSession->enrollments()->count(); @endphp
                    <p style="font-size:11px;color:var(--muted);margin-top:4px;">Currently {{ $enrolled }} enrolled. Cannot reduce below this.</p>
                </div>
                <div>
                    <label class="form-label">Price (KSh)</label>
                    <input type="number" name="price" class="form-input"
                           value="{{ old('price', $groupSession->price) }}" min="0" step="50">
                </div>
            </div>

            {{-- Read-only session code --}}
            <div style="background:var(--cream);border-radius:10px;padding:12px 16px;font-size:13px;">
                <span style="color:var(--muted);">Session Code (cannot be changed):</span>
                <strong style="font-family:monospace;color:#0A64BC;margin-left:8px;">{{ $groupSession->session_code }}</strong>
            </div>
        </div>

        <div style="margin-top:16px;display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary" style="padding:12px 28px;">Save Changes</button>
            <a href="{{ route('admin.group-sessions.show', $groupSession) }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>

    {{-- Info panel --}}
    <div style="height:fit-content;" class="card" style="padding:20px;">
        <div class="card" style="padding:18px;">
            <h4 style="font-size:13px;font-weight:700;color:var(--ink);margin-bottom:10px;">Current Status</h4>
            <span class="badge {{ ['scheduled'=>'badge-blue','in_progress'=>'badge-green','completed'=>'badge-amber','cancelled'=>'badge-red'][$groupSession->status]??'badge-gray' }}">
                {{ ucwords(str_replace('_',' ',$groupSession->status)) }}
            </span>
            <p style="font-size:12px;color:var(--muted);margin-top:8px;line-height:1.6;">
                Enrollments: <strong>{{ $enrolled }}</strong> / {{ $groupSession->max_students ?? 30 }}<br>
                Created: {{ $groupSession->created_at->format('M j, Y') }}
            </p>
        </div>
    </div>
</div>

<style>
@media(max-width:900px){.form-grid{grid-template-columns:1fr!important;}.two-col{grid-template-columns:1fr!important;}}
</style>

@endsection
