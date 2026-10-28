<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Create Examiner Clinic – Mwalimu</title>
</head>
<body>
@include('partials.nav')

<section style="padding:56px 0 80px;background:#F2F5F2;min-height:calc(100vh - 64px);">
<div style="max-width:720px;margin:0 auto;padding:0 20px;">

    <div style="margin-bottom:24px;">
        <a href="{{ route('examiner.dashboard') }}" style="font-size:13px;color:rgba(21,40,31,.5);text-decoration:none;display:inline-flex;align-items:center;gap:5px;margin-bottom:12px;">
            <i class="fa-solid fa-arrow-left fa-xs"></i> Back to Dashboard
        </a>
        <h1 style="font-family:'Fraunces',serif;font-size:32px;font-weight:700;color:#15281F;margin-bottom:6px;">Create Examiner Clinic</h1>
        <p style="font-size:15px;color:rgba(21,40,31,.55);">Sessions are capped at <strong>30 students</strong>. When full, an overflow batch is auto-created starting 30 minutes after this session ends.</p>
    </div>

    @if($errors->any())
    <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);border-radius:14px;padding:14px 18px;margin-bottom:20px;">
        <div style="font-weight:700;color:#dc2626;margin-bottom:6px;"><i class="fa-solid fa-triangle-exclamation fa-sm"></i> Please fix the following</div>
        <ul style="margin:0;padding-left:20px;font-size:13px;color:#dc2626;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);border-radius:14px;padding:14px 18px;margin-bottom:20px;color:#059669;font-weight:600;">
        <i class="fa-solid fa-circle-check fa-sm"></i> {{ session('success') }}
    </div>
    @endif

    <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:30px;box-shadow:0 2px 16px rgba(21,40,31,.05);">
        <form method="POST" action="{{ route('examiner.clinics.store') }}">
            @csrf

            {{-- Subject + Paper type --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;" class="form-r">
                <div>
                    <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.5);margin-bottom:6px;display:block;">
                        Subject <span style="color:#dc2626;">*</span>
                    </label>
                    <select name="subject_id" required style="width:100%;height:44px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:14px;font-family:inherit;color:#15281F;background:#fff;appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' fill='none'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2315281F' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 12px center;padding-right:32px;">
                        <option value="">— Select subject —</option>
                        @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ old('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.5);margin-bottom:6px;display:block;">
                        Paper Type <span style="color:#dc2626;">*</span>
                    </label>
                    <div style="display:flex;gap:8px;">
                        @foreach(['PP1','PP2','PP3'] as $pt)
                        <label style="flex:1;display:flex;align-items:center;justify-content:center;height:44px;border-radius:10px;border:1.5px solid {{ old('paper_type')===$pt ? '#0A64BC' : 'rgba(21,40,31,.1)' }};background:{{ old('paper_type')===$pt ? 'rgba(10,100,188,.08)' : '#fff' }};color:{{ old('paper_type')===$pt ? '#0A64BC' : '#15281F' }};font-size:14px;font-weight:700;cursor:pointer;transition:all .15s;" class="pt-label">
                            <input type="radio" name="paper_type" value="{{ $pt }}" {{ old('paper_type')===$pt ? 'checked' : '' }} required style="position:absolute;opacity:0;pointer-events:none;">
                            {{ $pt }}
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <style>.form-r{grid-template-columns:1fr!important;}@media(min-width:560px){.form-r{grid-template-columns:1fr 1fr!important;}}</style>

            {{-- Title --}}
            <div style="margin-bottom:18px;">
                <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.5);margin-bottom:6px;display:block;">
                    Clinic Title <span style="color:#dc2626;">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    placeholder="e.g. Mathematics PP1 – Algebra &amp; Calculus Mastery"
                    style="width:100%;height:44px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:14px;font-family:inherit;color:#15281F;outline:none;transition:border-color .15s;"
                    onfocus="this.style.borderColor='#0A64BC'" onblur="this.style.borderColor='rgba(21,40,31,.1)'">
            </div>

            {{-- Description --}}
            <div style="margin-bottom:18px;">
                <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.5);margin-bottom:6px;display:block;">Description</label>
                <textarea name="description" rows="3"
                    placeholder="What will students gain from this session? Exam strategies, key topics, marking scheme insights…"
                    style="width:100%;padding:11px 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:14px;font-family:inherit;color:#15281F;outline:none;resize:vertical;transition:border-color .15s;"
                    onfocus="this.style.borderColor='#0A64BC'" onblur="this.style.borderColor='rgba(21,40,31,.1)'">{{ old('description') }}</textarea>
            </div>

            {{-- Price + Duration --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;" class="form-r">
                <div>
                    <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.5);margin-bottom:6px;display:block;">
                        Price (KSh) <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="number" name="price" value="{{ old('price', 500) }}" min="0" step="1" required
                        style="width:100%;height:44px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:14px;font-family:inherit;color:#15281F;outline:none;"
                        onfocus="this.style.borderColor='#0A64BC'" onblur="this.style.borderColor='rgba(21,40,31,.1)'">
                </div>
                <div>
                    <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.5);margin-bottom:6px;display:block;">
                        Duration (minutes) <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="30" max="180" required
                        style="width:100%;height:44px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:14px;font-family:inherit;color:#15281F;outline:none;"
                        onfocus="this.style.borderColor='#0A64BC'" onblur="this.style.borderColor='rgba(21,40,31,.1)'">
                </div>
            </div>

            {{-- Scheduled at --}}
            <div style="margin-bottom:22px;">
                <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.5);margin-bottom:6px;display:block;">
                    Scheduled Date &amp; Time <span style="color:#dc2626;">*</span>
                </label>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required
                    style="width:100%;height:44px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:14px;font-family:inherit;color:#15281F;outline:none;transition:border-color .15s;"
                    onfocus="this.style.borderColor='#0A64BC'" onblur="this.style.borderColor='rgba(21,40,31,.1)'">
                <p style="font-size:11px;color:rgba(21,40,31,.45);margin-top:5px;">Session starts in Jitsi automatically at this time. Students can join 15 minutes early.</p>
            </div>

            {{-- Info box --}}
            <div style="background:rgba(10,100,188,.06);border:1px solid rgba(10,100,188,.15);border-radius:12px;padding:14px 16px;margin-bottom:24px;display:flex;gap:10px;align-items:flex-start;">
                <i class="fa-solid fa-circle-info" style="color:#0A64BC;flex-shrink:0;margin-top:1px;"></i>
                <div style="font-size:13px;color:#0A64BC;line-height:1.65;">
                    <strong>30 slots · Auto-overflow.</strong> When all 30 spots are taken the system creates an identical session starting 30 minutes after this one ends. The session goes live in Jitsi at the scheduled time — no action needed from you.
                </div>
            </div>

            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <button type="submit" style="flex:1;min-width:160px;height:48px;border-radius:12px;background:#EEBE15;color:#15281F;font-size:15px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:opacity .15s;" onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                    <i class="fa-solid fa-calendar-plus fa-sm"></i> Create Clinic
                </button>
                <a href="{{ route('examiner.dashboard') }}" style="flex:1;min-width:120px;height:48px;border-radius:12px;background:#F4F6F4;color:#15281F;font-size:15px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
</section>

<script>
// Paper type radio visual toggle
document.querySelectorAll('.pt-label').forEach(function(label) {
    label.addEventListener('click', function() {
        document.querySelectorAll('.pt-label').forEach(function(l) {
            l.style.borderColor = 'rgba(21,40,31,.1)';
            l.style.background  = '#fff';
            l.style.color       = '#15281F';
        });
        this.style.borderColor = '#0A64BC';
        this.style.background  = 'rgba(10,100,188,.08)';
        this.style.color       = '#0A64BC';
        this.querySelector('input[type=radio]').checked = true;
    });
});
</script>

@include('partials.footer')
@livewireScripts
</body>
</html>
