@extends('layouts.admin')
@section('title', 'Subjects Management')
@section('content')
<div style="max-width:1400px;margin:0 auto;padding:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:var(--ink);margin-bottom:6px;">Subjects Management</h1>
            <p style="color:var(--muted);font-size:14px;">Manage subjects across all curriculum categories</p>
        </div>
        <button onclick="document.getElementById('add-modal').style.display='flex'" class="btn btn-v">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Subject
        </button>
    </div>

    @if(session('success'))
    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:12px;">
        <span style="font-size:20px;">✅</span>
        <span style="color:#059669;font-weight:600;">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:12px;">
        <span style="font-size:20px;">❌</span>
        <span style="color:#dc2626;font-weight:600;">{{ session('error') }}</span>
    </div>
    @endif

    <div class="card">
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Subject Name</th>
                        <th>Category</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                    <tr>
                        <td><span style="font-size:22px;">{{ $subject->icon ?? '📖' }}</span></td>
                        <td><span style="font-weight:600;font-size:14px;">{{ $subject->name }}</span></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span style="font-size:18px;">{{ $subject->category->icon ?? '📚' }}</span>
                                <span style="font-size:13px;color:var(--muted);">{{ $subject->category->name }}</span>
                            </div>
                        </td>
                        <td><code style="background:#F4F6F4;padding:3px 8px;border-radius:6px;font-size:11px;">{{ $subject->slug }}</code></td>
                        <td>
                            <label class="toggle-switch">
                                <input type="checkbox" {{ $subject->is_active ? 'checked' : '' }} onchange="toggleSubject({{ $subject->id }}, this)">
                                <span class="slider"></span>
                            </label>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;align-items:center;">
                                <button onclick="editSubject({{ $subject->id }})" class="btn btn-o btn-sm" title="Edit" style="padding:6px 10px;">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                <button onclick="deleteSubject({{ $subject->id }}, '{{ addslashes($subject->name) }}')" class="btn btn-sm" style="background:rgba(239,68,68,.1);color:#dc2626;border:1px solid rgba(239,68,68,.2);padding:6px 10px;" title="Delete">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:48px;color:var(--muted);">No subjects found. Add your first subject to get started.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subjects->hasPages())
        <div style="padding:16px 20px;border-top:1px solid var(--hair);">
            {{ $subjects->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="delete-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:20px;padding:32px;width:100%;max-width:440px;box-shadow:0 20px 25px -5px rgba(0,0,0,.1),0 10px 10px -5px rgba(0,0,0,.04);">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
            <div style="width:48px;height:48px;border-radius:12px;background:rgba(239,68,68,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#dc2626" stroke-width="2">
                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div style="flex:1;">
                <h3 style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--ink);margin-bottom:4px;">Delete Subject</h3>
                <p style="font-size:14px;color:var(--muted);margin:0;">This action cannot be undone</p>
            </div>
        </div>
        <div style="background:rgba(239,68,68,.05);border:1px solid rgba(239,68,68,.15);border-radius:12px;padding:14px 16px;margin-bottom:20px;">
            <p style="font-size:14px;color:#7f1d1d;margin:0;">Are you sure you want to delete <strong id="delete-subject-name"></strong>?</p>
        </div>
        <form id="delete-form" method="POST">
            @csrf @method('DELETE')
            <div style="display:flex;gap:10px;">
                <button type="button" onclick="document.getElementById('delete-modal').style.display='none'" class="btn btn-o" style="flex:1;">Cancel</button>
                <button type="submit" class="btn" style="flex:1;background:#dc2626;color:#fff;border:1px solid #dc2626;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="margin-right:6px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    Yes, Delete
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Add Subject Modal --}}
<div id="add-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:20px;padding:32px;width:100%;max-width:480px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <h3 style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;">Add New Subject</h3>
            <button onclick="document.getElementById('add-modal').style.display='none'" style="background:none;border:none;font-size:26px;color:var(--muted);cursor:pointer;">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.subjects.store') }}">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Category *</label>
                <select name="category_id" required style="width:100%;height:46px;padding:0 16px;border-radius:12px;border:1.5px solid var(--hair);font-size:15px;font-family:inherit;outline:none;background:#fff;">
                    <option value="">Select a category</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Subject Name *</label>
                <input type="text" name="name" required placeholder="e.g. Mathematics" style="width:100%;height:46px;padding:0 16px;border-radius:12px;border:1.5px solid var(--hair);font-size:15px;font-family:inherit;outline:none;">
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Icon Emoji</label>
                <input type="text" name="icon" placeholder="📖" maxlength="10" style="width:100%;height:46px;padding:0 16px;border-radius:12px;border:1.5px solid var(--hair);font-size:15px;font-family:inherit;outline:none;">
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">Use a single emoji (e.g. 📖 🔢 🧬 🗺️)</div>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                    <input type="checkbox" name="is_active" checked style="width:18px;height:18px;cursor:pointer;">
                    <span style="font-size:14px;font-weight:600;">Active (visible to users)</span>
                </label>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="button" onclick="document.getElementById('add-modal').style.display='none'" class="btn btn-o" style="flex:1;">Cancel</button>
                <button type="submit" class="btn btn-v" style="flex:1;">Create Subject</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Subject Modal --}}
<div id="edit-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:20px;padding:32px;width:100%;max-width:480px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <h3 style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;">Edit Subject</h3>
            <button onclick="document.getElementById('edit-modal').style.display='none'" style="background:none;border:none;font-size:26px;color:var(--muted);cursor:pointer;">&times;</button>
        </div>
        <form id="edit-form" method="POST">
            @csrf @method('PATCH')
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Category *</label>
                <select name="category_id" id="edit-category" required style="width:100%;height:46px;padding:0 16px;border-radius:12px;border:1.5px solid var(--hair);font-size:15px;font-family:inherit;outline:none;background:#fff;">
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Subject Name *</label>
                <input type="text" name="name" id="edit-name" required style="width:100%;height:46px;padding:0 16px;border-radius:12px;border:1.5px solid var(--hair);font-size:15px;font-family:inherit;outline:none;">
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Icon Emoji</label>
                <input type="text" name="icon" id="edit-icon" maxlength="10" style="width:100%;height:46px;padding:0 16px;border-radius:12px;border:1.5px solid var(--hair);font-size:15px;font-family:inherit;outline:none;">
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                    <input type="checkbox" name="is_active" id="edit-active" style="width:18px;height:18px;cursor:pointer;">
                    <span style="font-size:14px;font-weight:600;">Active (visible to users)</span>
                </label>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="button" onclick="document.getElementById('edit-modal').style.display='none'" class="btn btn-o" style="flex:1;">Cancel</button>
                <button type="submit" class="btn btn-v" style="flex:1;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<style>
.toggle-switch{position:relative;display:inline-block;width:44px;height:24px;}
.toggle-switch input{opacity:0;width:0;height:0;}
.slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background-color:#ccc;border-radius:24px;transition:.3s;}
.slider:before{position:absolute;content:"";height:18px;width:18px;left:3px;bottom:3px;background-color:white;border-radius:50%;transition:.3s;}
input:checked + .slider{background-color:#10B981;}
input:checked + .slider:before{transform:translateX(20px);}
</style>

<script>
const subjects = @json($subjects->items());

function deleteSubject(id, name) {
    document.getElementById('delete-subject-name').textContent = name;
    document.getElementById('delete-form').action = `/admin/subjects/${id}`;
    document.getElementById('delete-modal').style.display = 'flex';
}

function editSubject(id) {
    const subject = subjects.find(s => s.id === id);
    if (!subject) return;
    
    document.getElementById('edit-form').action = `/admin/subjects/${id}`;
    document.getElementById('edit-category').value = subject.category_id;
    document.getElementById('edit-name').value = subject.name;
    document.getElementById('edit-icon').value = subject.icon || '';
    document.getElementById('edit-active').checked = subject.is_active;
    
    document.getElementById('edit-modal').style.display = 'flex';
}

async function toggleSubject(id, checkbox) {
    try {
        const response = await fetch(`/admin/subjects/${id}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        if (data.status !== 'ok') {
            checkbox.checked = !checkbox.checked;
            alert('Failed to toggle subject status');
        }
    } catch (error) {
        checkbox.checked = !checkbox.checked;
        alert('Error toggling subject status');
    }
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('add-modal').style.display = 'none';
        document.getElementById('edit-modal').style.display = 'none';
        document.getElementById('delete-modal').style.display = 'none';
    }
});
</script>
@endsection
