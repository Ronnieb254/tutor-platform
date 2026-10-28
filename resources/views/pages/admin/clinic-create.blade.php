@extends('layouts.admin')
@section('title', 'Create Clinic')
@section('content')
<div style="max-width:760px;margin:0 auto;padding:24px;">

    <div style="margin-bottom:22px;">
        <a href="{{ route('admin.clinics') }}" style="font-size:13px;color:var(--muted);text-decoration:none;display:inline-flex;align-items:center;gap:5px;margin-bottom:10px;">
            <i class="fa-solid fa-arrow-left fa-xs"></i> Back to Clinics
        </a>
        <h1 style="font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:var(--ink);margin-bottom:4px;">Create Examiner Clinic</h1>
        <p style="color:var(--muted);font-size:14px;">Clinics are fixed at <strong>30 student slots</strong>. When full, an overflow session is auto-created with a 30-minute break after the previous session ends.</p>
    </div>

    @if($errors->any())
    <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);border-radius:12px;padding:14px 18px;margin-bottom:20px;">
        <div style="font-weight:600;color:#dc2626;margin-bottom:6px;"><i class="fa-solid fa-circle-xmark fa-sm"></i> Please fix the errors below</div>
        <ul style="margin:0;padding-left:20px;font-size:13px;color:#dc2626;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div style="background:#fff;border-radius:18px;border:1px solid rgba(21,40,31,.08);padding:28px;">
        <form method="POST" action="{{ route('admin.clinics.store') }}">
            @csrf

            {{-- Row 1: Subject + Paper --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;" class="form-row">
                <div>
                    <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:6px;display:block;">
                        Subject <span style="color:#dc2626;">*</span>
                    </label>
                    <select name="subject_id" required style="width:100%;height:42px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:14px;font-family:inherit;background:#fff;color:var(--ink);">
                        <option value="">— Select subject —</option>
                        @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ old('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:6px;display:block;">
                        Paper Type <span style="color:#dc2626;">*</span>
                    </label>
                    <div style="display:flex;gap:10px;height:42px;align-items:center;">
                        @foreach(['PP1','PP2','PP3'] as $pt)
                        <label style="display:flex;align-items:center;gap:5px;cursor:pointer;font-size:14px;font-weight:600;padding:8px 14px;border-radius:10px;border:1.5px solid {{ old('paper_type')===$pt ? '#0A64BC' : 'rgba(21,40,31,.1)' }};background:{{ old('paper_type')===$pt ? 'rgba(10,100,188,.08)' : '#fff' }};color:{{ old('paper_type')===$pt ? '#0A64BC' : 'var(--ink)' }};transition:all .15s;" class="paper-label">
                            <input type="radio" name="paper_type" value="{{ $pt }}" {{ old('paper_type')===$pt ? 'checked' : '' }} required style="display:none;">
                            {{ $pt }}
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <style>.form-row{grid-template-columns:1fr!important;}@media(min-width:600px){.form-row{grid-template-columns:1fr 1fr!important;}}</style>

            {{-- Title --}}
            <div style="margin-bottom:18px;">
                <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:6px;display:block;">
                    Clinic Title <span style="color:#dc2626;">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. Mathematics PP1 – Algebra &amp; Calculus Mastery"
                    style="width:100%;height:42px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:14px;font-family:inherit;color:var(--ink);">
            </div>

            {{-- Description --}}
            <div style="margin-bottom:18px;">
                <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:6px;display:block;">Description</label>
                <textarea name="description" rows="3" placeholder="Topics covered, exam strategies, what students will gain…"
                    style="width:100%;padding:10px 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:14px;font-family:inherit;color:var(--ink);resize:vertical;">{{ old('description') }}</textarea>
            </div>

            {{-- Tutor --}}
            <div style="margin-bottom:18px;">
                <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:6px;display:block;">
                    Assign Tutor / Examiner <span style="color:#dc2626;">*</span>
                </label>
                <select name="tutor_id" required style="width:100%;height:42px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:14px;font-family:inherit;background:#fff;color:var(--ink);">
                    <option value="">— Select tutor —</option>
                    @foreach($tutors as $t)
                    <option value="{{ $t->id }}" {{ old('tutor_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
                <p style="font-size:11px;color:var(--muted);margin-top:5px;">Only approved tutors are listed.</p>
            </div>

            {{-- Row: Price + Duration --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;" class="form-row">
                <div>
                    <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:6px;display:block;">
                        Price (KSh) <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="number" name="price" value="{{ old('price', 500) }}" min="0" step="1" required
                        style="width:100%;height:42px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:14px;font-family:inherit;color:var(--ink);">
                </div>
                <div>
                    <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:6px;display:block;">
                        Duration (minutes) <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="30" max="180" required
                        style="width:100%;height:42px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:14px;font-family:inherit;color:var(--ink);">
                </div>
            </div>

            {{-- Scheduled date + time --}}
            <div style="margin-bottom:22px;">
                <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:6px;display:block;">
                    Scheduled Date &amp; Time <span style="color:#dc2626;">*</span>
                </label>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required
                    style="width:100%;height:42px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:14px;font-family:inherit;color:var(--ink);">
                <p style="font-size:11px;color:var(--muted);margin-top:5px;">Clinics can be scheduled for any future date. When full (30 students), an overflow session is automatically created 30 minutes after this one ends.</p>
            </div>

            {{-- Slots info box --}}
            <div style="background:rgba(10,100,188,.06);border:1px solid rgba(10,100,188,.15);border-radius:12px;padding:14px 16px;margin-bottom:22px;display:flex;gap:10px;">
                <i class="fa-solid fa-circle-info" style="color:#0A64BC;margin-top:2px;flex-shrink:0;"></i>
                <div style="font-size:13px;color:#0A64BC;">
                    <strong>Slots are fixed at 30.</strong> Once all 30 spots are booked, the system automatically creates a second session scheduled 30 minutes after this one ends — using the same title, tutor, and description.
                </div>
            </div>

            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <button type="submit" style="flex:1;min-width:160px;height:46px;border-radius:12px;background:#0A64BC;color:#fff;font-size:15px;font-weight:700;border:none;cursor:pointer;font-family:inherit;">
                    <i class="fa-solid fa-calendar-plus fa-sm"></i> Create Clinic
                </button>
                <a href="{{ route('admin.clinics') }}" style="flex:1;min-width:120px;height:46px;border-radius:12px;background:#F4F6F4;color:var(--ink);font-size:15px;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Paper type radio visual toggle
document.querySelectorAll('.paper-label').forEach(function(label){
    label.addEventListener('click', function(){
        document.querySelectorAll('.paper-label').forEach(function(l){
            l.style.borderColor = 'rgba(21,40,31,.1)';
            l.style.background  = '#fff';
            l.style.color       = 'var(--ink)';
        });
        this.style.borderColor = '#0A64BC';
        this.style.background  = 'rgba(10,100,188,.08)';
        this.style.color       = '#0A64BC';
    });
});
</script>
@endsection
