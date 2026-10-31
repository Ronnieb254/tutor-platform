@extends('layouts.admin')
@section('title', 'Create Group Session')

@section('content')

<div style="display:flex;align-items:center;gap:14px;margin-bottom:24px;">
    <a href="{{ route('admin.group-sessions.index') }}" class="btn btn-outline btn-sm">← Back</a>
    <div>
        <h2 style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);">Create Group Session</h2>
        <p style="font-size:13px;color:var(--muted);margin-top:2px;">Set up a new admin-hosted group learning session</p>
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

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;" class="form-grid">
    <form method="POST" action="{{ route('admin.group-sessions.store') }}">
        @csrf

        <div class="card" style="padding:28px;margin-bottom:0;">
            <h3 style="font-family:'Fraunces',serif;font-size:16px;font-weight:700;color:var(--ink);margin-bottom:20px;">Session Details</h3>

            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">Title <span style="color:#E8522A;">*</span></label>
                <input type="text" name="title" class="form-input" value="{{ old('title') }}"
                       placeholder="e.g. KCSE Chemistry PP2 Evening Revision" required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;" class="two-col">
                <div>
                    <label class="form-label">Subject <span style="color:#E8522A;">*</span></label>
                    <select name="subject_id" class="form-input form-select" required>
                        <option value="">— Select subject —</option>
                        @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ old('subject_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Assign Tutor <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                    <select name="tutor_id" class="form-input form-select">
                        <option value="">— TBD —</option>
                        @foreach($tutors as $t)
                        <option value="{{ $t->id }}" {{ old('tutor_id')==$t->id?'selected':'' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Description <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                <textarea name="description" class="form-input" rows="3"
                          style="height:auto;padding:12px 14px;resize:vertical;"
                          placeholder="What will students learn? Topics covered, exam papers, tips…">{{ old('description') }}</textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;" class="two-col">
                <div>
                    <label class="form-label">Scheduled Date & Time <span style="color:#E8522A;">*</span></label>
                    <input type="datetime-local" name="scheduled_at" class="form-input"
                           value="{{ old('scheduled_at') }}" required>
                </div>
                <div>
                    <label class="form-label">Duration (minutes) <span style="color:#E8522A;">*</span></label>
                    <input type="number" name="duration_minutes" class="form-input"
                           value="{{ old('duration_minutes', 60) }}" min="15" max="480" required>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;" class="two-col">
                <div>
                    <label class="form-label">Max Students <span style="color:var(--muted);font-weight:400;">(default 30)</span></label>
                    <input type="number" name="max_students" class="form-input"
                           value="{{ old('max_students', 30) }}" min="1" max="200">
                </div>
                <div>
                    <label class="form-label">Price (KSh) <span style="color:var(--muted);font-weight:400;">(default 500)</span></label>
                    <input type="number" name="price" class="form-input"
                           value="{{ old('price', 500) }}" min="0" step="50">
                </div>
            </div>
        </div>

        <div style="margin-top:16px;display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary" style="padding:12px 28px;">
                Create Group Session
            </button>
            <a href="{{ route('admin.group-sessions.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>

    {{-- Info panel --}}
    <div style="height:fit-content;">
        <div class="card" style="padding:20px;">
            <h4 style="font-size:13px;font-weight:700;color:var(--ink);margin-bottom:12px;display:flex;align-items:center;gap:6px;">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#0A64BC" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Auto-generated on save
            </h4>
            <div style="font-size:12px;color:var(--muted);line-height:1.7;">
                <p>• <strong>Session code</strong> — GRP-XXXXXXXX</p>
                <p>• <strong>Meeting room</strong> — Mwalimu-group-{uuid}</p>
                <p>• <strong>Status</strong> set to "Scheduled"</p>
            </div>
        </div>
        <div class="card" style="padding:20px;margin-top:12px;">
            <h4 style="font-size:13px;font-weight:700;color:var(--ink);margin-bottom:10px;">Session Lifecycle</h4>
            <div style="font-size:12px;color:var(--muted);line-height:1.8;">
                <p>📅 <strong>Scheduled</strong> → students can enroll</p>
                <p>▶️ <strong>In Progress</strong> → session live (Start it from Show page)</p>
                <p>✅ <strong>Completed</strong> → session ended</p>
                <p>❌ <strong>Cancelled</strong> → session called off</p>
            </div>
        </div>
    </div>
</div>

<style>
@media(max-width:900px){.form-grid{grid-template-columns:1fr!important;}.two-col{grid-template-columns:1fr!important;}}
</style>

@endsection
