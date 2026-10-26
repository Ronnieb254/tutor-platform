@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Manage Subjects - ' . $tutorProfile->user->name)

@section('content')
<style>
.subject-card{background:#fff;border-radius:14px;border:1.5px solid rgba(21,40,31,.08);padding:12px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px;transition:all .15s;cursor:pointer;}
.subject-card:hover{border-color:rgba(10,100,188,.3);background:rgba(10,100,188,.02);}
.subject-card.selected{background:rgba(10,100,188,.08);border-color:#0A64BC;}
.subject-card.disabled{opacity:.4;cursor:not-allowed;}
.subject-card.disabled:hover{border-color:rgba(21,40,31,.08);background:#fff;}
.category-section{margin-bottom:24px;}
.save-btn{position:fixed;bottom:24px;right:24px;padding:14px 32px;border-radius:999px;background:#10B981;color:#fff;border:none;font-size:15px;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(16,185,129,.3);transition:all .2s;z-index:100;}
.save-btn:hover{background:#059669;transform:translateY(-2px);box-shadow:0 8px 16px rgba(16,185,129,.4);}
.save-btn:disabled{background:rgba(0,0,0,.2);cursor:not-allowed;transform:none;}
</style>

<div class="page-header">
    <div>
        <h1>Manage Subjects</h1>
        <p>
            @if(auth()->user()->isAdmin())
                Configure subjects for <strong>{{ $tutorProfile->user->name }}</strong>
            @else
                Select the subjects you teach
            @endif
        </p>
    </div>
    <div>
        <a href="{{ auth()->user()->isAdmin() ? route('admin.tutors') : route('dashboard') }}" class="btn btn-outline">
            <i class="fa-solid fa-arrow-left fa-xs"></i> Back
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div style="max-width:1200px;">
    <div class="card" style="margin-bottom:24px;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--hairline);">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div>
                    <h3 style="font-family:'Fraunces',serif;font-size:18px;color:var(--ink);margin-bottom:4px;">
                        Currently Teaching <span id="subject-count" style="color:#0A64BC;">{{ $tutorProfile->subjects->count() }}</span> Subject{{ $tutorProfile->subjects->count() !== 1 ? 's' : '' }}
                    </h3>
                    <p style="font-size:13px;color:var(--muted);">Click to add or remove subjects from your profile</p>
                </div>
                <div style="display:flex;gap:8px;">
                    <input type="text" id="search-subjects" placeholder="Search subjects..."
                           style="width:240px;height:42px;padding:0 14px;border-radius:10px;border:1.5px solid var(--hairline);font-size:14px;outline:none;"
                           onfocus="this.style.borderColor='#0A64BC'" onblur="this.style.borderColor='var(--hairline)'">
                </div>
            </div>
        </div>

        {{-- Selected Subjects Preview --}}
        <div style="padding:20px 24px;background:#F9FAF9;border-bottom:1px solid var(--hairline);">
            <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:10px;">Your Selected Subjects</p>
            <div id="selected-preview" style="display:flex;flex-wrap:wrap;gap:6px;min-height:32px;">
                @forelse($tutorProfile->subjects as $subject)
                <span class="badge badge-blue" data-subject-id="{{ $subject->id }}">
                    {{ $subject->name }}
                    <i class="fa-solid fa-xmark fa-xs" style="margin-left:4px;cursor:pointer;" onclick="deselectSubject({{ $subject->id }})"></i>
                </span>
                @empty
                <span style="font-size:13px;color:var(--muted);">No subjects selected yet. Choose from the categories below.</span>
                @endforelse
            </div>
        </div>
    </div>

    <form id="subjects-form" method="POST" action="{{ auth()->user()->isAdmin() ? route('admin.tutors.subjects.store', $tutorProfile) : route('tutor.subjects.store') }}">
        @csrf
        <div id="subjects-container">
            @foreach($categories as $category)
            <div class="category-section" data-category="{{ strtolower($category->name) }}">
                <h4 style="font-family:'Fraunces',serif;font-size:16px;color:var(--ink);margin-bottom:12px;display:flex;align-items:center;gap:8px;">
                    <i class="fa-solid fa-folder" style="color:#EEBE15;font-size:14px;"></i>
                    {{ $category->name }}
                    <span style="font-size:12px;color:var(--muted);font-weight:400;">({{ $category->subjects->count() }})</span>
                </h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:10px;">
                    @foreach($category->subjects as $subject)
                    <div class="subject-card {{ $tutorProfile->subjects->contains($subject->id) ? 'selected' : '' }}" 
                         data-subject-id="{{ $subject->id }}"
                         data-subject-name="{{ strtolower($subject->name) }}"
                         onclick="toggleSubject({{ $subject->id }}, '{{ $subject->name }}')">
                        <div style="display:flex;align-items:center;gap:10px;flex:1;">
                            <div style="width:36px;height:36px;border-radius:10px;background:rgba(10,100,188,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fa-solid fa-book-open" style="color:#0A64BC;font-size:14px;"></i>
                            </div>
                            <div>
                                <div style="font-size:14px;font-weight:600;color:var(--ink);">{{ $subject->name }}</div>
                                <div style="font-size:11px;color:var(--muted);">{{ $category->name }}</div>
                            </div>
                        </div>
                        <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}" 
                               {{ $tutorProfile->subjects->contains($subject->id) ? 'checked' : '' }}
                               style="width:20px;height:20px;cursor:pointer;accent-color:#0A64BC;">
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <button type="submit" class="save-btn" id="save-btn">
            <i class="fa-solid fa-check fa-sm"></i> Save Changes
        </button>
    </form>
</div>

<script>
const selectedSubjects = new Set({{ json_encode($tutorProfile->subjects->pluck('id')->toArray()) }});
const subjectNames = {};

function toggleSubject(id, name) {
    const card = document.querySelector(`.subject-card[data-subject-id="${id}"]`);
    const checkbox = card.querySelector('input[type="checkbox"]');
    
    if (selectedSubjects.has(id)) {
        selectedSubjects.delete(id);
        card.classList.remove('selected');
        checkbox.checked = false;
        delete subjectNames[id];
    } else {
        selectedSubjects.add(id);
        card.classList.add('selected');
        checkbox.checked = true;
        subjectNames[id] = name;
    }
    
    updatePreview();
    updateCount();
}

function deselectSubject(id) {
    selectedSubjects.delete(id);
    const card = document.querySelector(`.subject-card[data-subject-id="${id}"]`);
    if (card) {
        card.classList.remove('selected');
        card.querySelector('input[type="checkbox"]').checked = false;
    }
    delete subjectNames[id];
    updatePreview();
    updateCount();
}

function updatePreview() {
    const preview = document.getElementById('selected-preview');
    
    if (selectedSubjects.size === 0) {
        preview.innerHTML = '<span style="font-size:13px;color:var(--muted);">No subjects selected yet. Choose from the categories below.</span>';
        return;
    }
    
    preview.innerHTML = Array.from(selectedSubjects).map(id => {
        const name = subjectNames[id] || document.querySelector(`.subject-card[data-subject-id="${id}"]`)?.dataset.subjectName || 'Subject';
        return `<span class="badge badge-blue" data-subject-id="${id}">
            ${name}
            <i class="fa-solid fa-xmark fa-xs" style="margin-left:4px;cursor:pointer;" onclick="deselectSubject(${id})"></i>
        </span>`;
    }).join('');
}

function updateCount() {
    document.getElementById('subject-count').textContent = selectedSubjects.size;
}

// Search functionality
document.getElementById('search-subjects').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    
    document.querySelectorAll('.category-section').forEach(cat => {
        let hasVisible = false;
        cat.querySelectorAll('.subject-card').forEach(card => {
            const name = card.dataset.subjectName;
            if (name.includes(query)) {
                card.style.display = 'flex';
                hasVisible = true;
            } else {
                card.style.display = 'none';
            }
        });
        cat.style.display = hasVisible ? 'block' : 'none';
    });
});

// Initialize subject names
document.querySelectorAll('.subject-card').forEach(card => {
    const id = parseInt(card.dataset.subjectId);
    const name = card.querySelector('div > div:first-child').textContent.trim();
    subjectNames[id] = name;
});

// Form submission confirmation
document.getElementById('subjects-form').addEventListener('submit', function(e) {
    if (selectedSubjects.size === 0) {
        e.preventDefault();
        alert('Please select at least one subject');
        return false;
    }
});
</script>
@endsection
