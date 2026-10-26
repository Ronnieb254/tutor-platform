@extends('layouts.admin')
@section('title', 'Categories Management')
@section('content')
<div style="max-width:1400px;margin:0 auto;padding:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:var(--ink);margin-bottom:6px;">Categories Management</h1>
            <p style="color:var(--muted);font-size:14px;">Manage curriculum categories and learning pathways</p>
        </div>
        <button onclick="document.getElementById('add-modal').style.display='flex'" class="btn btn-v">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Category
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
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Subjects</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td><span style="font-size:24px;">{{ $category->icon ?? '📚' }}</span></td>
                        <td>
                            <div style="font-weight:600;font-size:14px;">{{ $category->name }}</div>
                            @if($category->description)
                            <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ Str::limit($category->description, 60) }}</div>
                            @endif
                        </td>
                        <td><code style="background:#F4F6F4;padding:3px 8px;border-radius:6px;font-size:11px;">{{ $category->slug }}</code></td>
                        <td><span class="bdg bdg-trust-blue">{{ $category->subjects_count }} subjects</span></td>
                        <td>{{ $category->sort_order }}</td>
                        <td>
                            <label class="toggle-switch">
                                <input type="checkbox" {{ $category->is_active ? 'checked' : '' }} onchange="toggleCategory({{ $category->id }}, this)">
                                <span class="slider"></span>
                            </label>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;align-items:center;">
                                <button onclick="editCategory({{ $category->id }})" class="btn btn-o btn-sm" title="Edit" style="padding:6px 10px;">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                <button onclick="deleteCategory({{ $category->id }}, '{{ addslashes($category->name) }}', {{ $category->subjects_count }})" class="btn btn-sm" style="background:rgba(239,68,68,.1);color:#dc2626;border:1px solid rgba(239,68,68,.2);padding:6px 10px;" title="Delete">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;padding:48px;color:var(--muted);">No categories found. Add your first category to get started.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
                <h3 style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--ink);margin-bottom:4px;">Delete Category</h3>
                <p style="font-size:14px;color:var(--muted);margin:0;">This action cannot be undone</p>
            </div>
        </div>
        <div id="delete-warning" style="background:rgba(239,68,68,.05);border:1px solid rgba(239,68,68,.15);border-radius:12px;padding:14px 16px;margin-bottom:20px;">
            <p style="font-size:14px;color:#7f1d1d;margin:0;">Are you sure you want to delete <strong id="delete-category-name"></strong>?</p>
            <p id="delete-subjects-warning" style="font-size:13px;color:#991b1b;margin:8px 0 0 0;display:none;">⚠️ This category has <strong id="delete-subjects-count"></strong> subjects. Delete them first.</p>
        </div>
        <form id="delete-form" method="POST">
            @csrf @method('DELETE')
            <div style="display:flex;gap:10px;">
                <button type="button" onclick="document.getElementById('delete-modal').style.display='none'" class="btn btn-o" style="flex:1;">Cancel</button>
                <button type="submit" id="delete-confirm-btn" class="btn" style="flex:1;background:#dc2626;color:#fff;border:1px solid #dc2626;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="margin-right:6px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    Yes, Delete
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Add Category Modal --}}
<div id="add-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:20px;padding:32px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <h3 style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;">Add New Category</h3>
            <button onclick="document.getElementById('add-modal').style.display='none'" style="background:none;border:none;font-size:26px;color:var(--muted);cursor:pointer;">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Category Name *</label>
                <input type="text" name="name" required placeholder="e.g. 8-4-4 System" style="width:100%;height:46px;padding:0 16px;border-radius:12px;border:1.5px solid var(--hair);font-size:15px;font-family:inherit;outline:none;">
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Icon Emoji</label>
                <input type="text" name="icon" placeholder="📚" maxlength="10" style="width:100%;height:46px;padding:0 16px;border-radius:12px;border:1.5px solid var(--hair);font-size:15px;font-family:inherit;outline:none;">
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">Use a single emoji (e.g. 📚 🎓 🔬 🌍 🎨)</div>
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Description</label>
                <textarea name="description" rows="3" placeholder="Brief description of this category..." style="width:100%;padding:12px 16px;border-radius:12px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;resize:vertical;"></textarea>
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Sort Order</label>
                <input type="number" name="sort_order" value="0" min="0" style="width:100%;height:46px;padding:0 16px;border-radius:12px;border:1.5px solid var(--hair);font-size:15px;font-family:inherit;outline:none;">
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                    <input type="checkbox" name="is_active" checked style="width:18px;height:18px;cursor:pointer;">
                    <span style="font-size:14px;font-weight:600;">Active (visible to users)</span>
                </label>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="button" onclick="document.getElementById('add-modal').style.display='none'" class="btn btn-o" style="flex:1;">Cancel</button>
                <button type="submit" class="btn btn-v" style="flex:1;">Create Category</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Category Modal --}}
<div id="edit-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:20px;padding:32px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <h3 style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;">Edit Category</h3>
            <button onclick="document.getElementById('edit-modal').style.display='none'" style="background:none;border:none;font-size:26px;color:var(--muted);cursor:pointer;">&times;</button>
        </div>
        <form id="edit-form" method="POST">
            @csrf @method('PATCH')
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Category Name *</label>
                <input type="text" name="name" id="edit-name" required style="width:100%;height:46px;padding:0 16px;border-radius:12px;border:1.5px solid var(--hair);font-size:15px;font-family:inherit;outline:none;">
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Icon Emoji</label>
                <input type="text" name="icon" id="edit-icon" maxlength="10" style="width:100%;height:46px;padding:0 16px;border-radius:12px;border:1.5px solid var(--hair);font-size:15px;font-family:inherit;outline:none;">
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Description</label>
                <textarea name="description" id="edit-description" rows="3" style="width:100%;padding:12px 16px;border-radius:12px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;outline:none;resize:vertical;"></textarea>
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Sort Order</label>
                <input type="number" name="sort_order" id="edit-sort-order" min="0" style="width:100%;height:46px;padding:0 16px;border-radius:12px;border:1.5px solid var(--hair);font-size:15px;font-family:inherit;outline:none;">
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
const categories = @json($categories);

function deleteCategory(id, name, subjectsCount) {
    document.getElementById('delete-category-name').textContent = name;
    document.getElementById('delete-form').action = `/admin/categories/${id}`;
    
    const warningEl = document.getElementById('delete-subjects-warning');
    const confirmBtn = document.getElementById('delete-confirm-btn');
    
    if (subjectsCount > 0) {
        // Show warning and disable delete
        document.getElementById('delete-subjects-count').textContent = subjectsCount;
        warningEl.style.display = 'block';
        confirmBtn.disabled = true;
        confirmBtn.style.opacity = '0.5';
        confirmBtn.style.cursor = 'not-allowed';
    } else {
        // Hide warning and enable delete
        warningEl.style.display = 'none';
        confirmBtn.disabled = false;
        confirmBtn.style.opacity = '1';
        confirmBtn.style.cursor = 'pointer';
    }
    
    document.getElementById('delete-modal').style.display = 'flex';
}

function editCategory(id) {
    const category = categories.find(c => c.id === id);
    if (!category) return;
    
    document.getElementById('edit-form').action = `/admin/categories/${id}`;
    document.getElementById('edit-name').value = category.name;
    document.getElementById('edit-icon').value = category.icon || '';
    document.getElementById('edit-description').value = category.description || '';
    document.getElementById('edit-sort-order').value = category.sort_order;
    document.getElementById('edit-active').checked = category.is_active;
    
    document.getElementById('edit-modal').style.display = 'flex';
}

async function toggleCategory(id, checkbox) {
    try {
        const response = await fetch(`/admin/categories/${id}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        if (data.status !== 'ok') {
            checkbox.checked = !checkbox.checked;
            alert('Failed to toggle category status');
        }
    } catch (error) {
        checkbox.checked = !checkbox.checked;
        alert('Error toggling category status');
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
