@extends('layouts.admin')
@section('title', 'Edit Examiner Clinic')
@section('content')

<div style="display:flex;align-items:center;gap:14px;margin-bottom:24px;">
    <a href="{{ route('admin.examiner-clinics.show', $examinerClinic) }}" class="btn btn-outline btn-sm">Ã¢â€ Â Back</a>
    <div>
        <h2 style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);">Edit Examiner Clinic</h2>
        <p style="font-size:13px;color:var(--muted);margin-top:2px;">{{ $examinerClinic->title }}</p>
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

<div style="max-width:760px;">
    <div class="card" style="padding:28px;">
        <form method="POST" action="{{ route('admin.examiner-clinics.update', $examinerClinic) }}">
            @csrf @method('PATCH')

            {{-- Subject + Curriculum --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;" class="form-row">
                <div>
                    <label class="form-label">Subject <span style="color:#E8522A;">*</span></label>
                    <select name="subject_id" required class="form-input form-select">
                        <option value="">Ã¢â‚¬â€ Select subject Ã¢â‚¬â€</option>
                        @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ old('subject_id', $examinerClinic->subject_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Curriculum <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                    <select name="curriculum_id" class="form-input form-select">
                        <option value="">Ã¢â‚¬â€ All tracks Ã¢â‚¬â€</option>
                        @foreach($curricula->groupBy('track') as $track => $group)
                        <optgroup label="{{ ucfirst($track) }}">
                            @foreach($group as $c)
                            <option value="{{ $c->id }}" {{ old('curriculum_id', $examinerClinic->curriculum_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>
            </div>
            <style>.form-row{grid-template-columns:1fr!important;}@media(min-width:600px){.form-row{grid-template-columns:1fr 1fr!important;}}</style>

            {{-- Title --}}
            <div style="margin-bottom:18px;">
                <label class="form-label">Clinic Title <span style="color:#E8522A;">*</span></label>
                <input type="text" name="title" class="form-input"
                       value="{{ old('title', $examinerClinic->title) }}" required>
            </div>

            {{-- Description --}}
            <div style="margin-bottom:18px;">
                <label class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-input" style="height:auto;padding:12px 14px;resize:vertical;">{{ old('description', $examinerClinic->description) }}</textarea>
            </div>

            {{-- Scheduled date + Duration --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;" class="form-row">
                <div>
                    <label class="form-label">Scheduled Date & Time <span style="color:#E8522A;">*</span></label>
                    <input type="datetime-local" name="scheduled_at" class="form-input"
                           value="{{ old('scheduled_at', $examinerClinic->scheduled_at->format('Y-m-d\TH:i')) }}" required>
                </div>
                <div>
                    <label class="form-label">Duration (minutes) <span style="color:#E8522A;">*</span></label>
                    <input type="number" name="duration_minutes" class="form-input"
                           value="{{ old('duration_minutes', $examinerClinic->duration_minutes) }}" min="15" max="480" required>
                </div>
            </div>

            {{-- Max students --}}
            <div style="margin-bottom:22px;">
                <label class="form-label">Max Students</label>
                <input type="number" name="max_students" class="form-input"
                       value="{{ old('max_students', $examinerClinic->max_students) }}" min="1" max="100" required>
                @php $enrolled = $examinerClinic->enrollments()->count(); @endphp
                <p style="font-size:11px;color:var(--muted);margin-top:4px;">Currently {{ $enrolled }} enrolled. Cannot reduce below this.</p>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary" style="padding:12px 28px;">Save Changes</button>
                <a href="{{ route('admin.examiner-clinics.show', $examinerClinic) }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
