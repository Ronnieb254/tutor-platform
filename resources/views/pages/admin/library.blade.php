@extends('layouts.admin')
@section('title', 'Library Management')

@section('content')
<div class="page-header">
 <div>
 <h1><i class="fa-solid fa-book-open"></i>Library Management</h1>
 <p>Manage all learning resources, approve tutor submissions, organise categories</p>
 </div>
 <button onclick="document.getElementById('add-modal').style.display='flex'" class="btn btn-primary">
 + Add Resource
 </button>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:20px;">{{ session('success') }}</div>
@endif

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;" class="stats-row">
 <div class="card card-p" style="text-align:center;">
 <div style="font-size:28px;font-family:'Fraunces',serif;font-weight:700;color:var(--primary);">{{ $stats['total'] }}</div>
 <div style="font-size:12px;color:var(--text-muted);margin-top:4px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Total Resources</div>
 </div>
 <div class="card card-p" style="text-align:center;">
 <div style="font-size:28px;font-family:'Fraunces',serif;font-weight:700;color:#10B981;">{{ $stats['published'] }}</div>
 <div style="font-size:12px;color:var(--text-muted);margin-top:4px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Published</div>
 </div>
 <div class="card card-p" style="text-align:center;">
 <div style="font-size:28px;font-family:'Fraunces',serif;font-weight:700;color:#F59E0B;">{{ $stats['pending'] }}</div>
 <div style="font-size:12px;color:var(--text-muted);margin-top:4px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Pending Approval</div>
 </div>
 <div class="card card-p" style="text-align:center;">
 <div style="font-size:28px;font-family:'Fraunces',serif;font-weight:700;color:var(--primary);">{{ number_format($stats['download_count']) }}</div>
 <div style="font-size:12px;color:var(--text-muted);margin-top:4px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Total Downloads</div>
 </div>
</div>
<style>.stats-row{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:768px){.stats-row{grid-template-columns:repeat(4,1fr)!important;}}</style>

{{-- Filters --}}
<div class="card card-p" style="margin-bottom:20px;">
 <form method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
 <div style="flex:1;min-width:200px;">
 <label class="form-label">Search</label>
 <input type="text" name="search" class="form-input" placeholder="Title or excerptâ‚¬Â¦" value="{{ request('search') }}">
 </div>
 <div>
 <label class="form-label">Type</label>
 <select name="type" class="form-input" style="min-width:130px;">
 <option value="">All Types</option>
 @foreach($resourceTypes as $t)
 <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="form-label">Category</label>
 <select name="category" class="form-input" style="min-width:140px;">
 <option value="">All Categories</option>
 @foreach($categories as $cat)
 <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="form-label">Status</label>
 <select name="status" class="form-input" style="min-width:130px;">
 <option value="">All</option>
 <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
 <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
 <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Approval</option>
 </select>
 </div>
 <button type="submit" class="btn btn-primary">Filter</button>
 @if(request()->hasAny(['search','type','category','status']))
 <a href="{{ route('admin.library') }}" class="btn btn-secondary">Clear</a>
 @endif
 </form>
</div>

{{-- Resources table --}}
<div class="card" style="margin-bottom:28px;">
 <div style="padding:16px 20px;border-bottom:1px solid var(--hairline);display:flex;justify-content:space-between;align-items:center;">
 <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;">Resources ({{ $resources->total() }})</h3>
 </div>
 <div style="overflow-x:auto;">
 <table id="library-table">
 <thead>
 <tr>
 <th>Resource</th>
 <th>Type</th>
 <th>Author</th>
 <th>Category</th>
 <th>Curriculum</th>
 <th>Grade</th>
 <th>Downloads</th>
 <th>Status</th>
 <th class="no-export">Actions</th>
 </tr>
 </thead>
 <tbody>
 @forelse($resources as $resource)
 @php
 $typeIcons = ['article'=>'<i class="fa-solid fa-book"></i>','video'=>'<i class="fa-solid fa-video"></i>','notes'=>'<i class="fa-solid fa-note-sticky"></i>','past_paper'=>'<i class="fa-solid fa-file-lines"></i>'];
 $typeClasses = ['article'=>'badge-primary','video'=>'badge-purple','notes'=>'badge-warning','past_paper'=>'badge-success'];
 @endphp
 <tr>
 <td style="max-width:280px;">
 <div style="display:flex;align-items:center;gap:10px;">
 <div style="width:36px;height:36px;border-radius:9px;background:rgba(10,100,188,.08);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
 {{ $typeIcons[$resource->resource_type] ?? '<i class="fa-solid fa-book"></i>' }}
 </div>
 <div>
 <div style="font-weight:600;font-size:13.5px;color:var(--text);">{{ Str::limit($resource->title, 50) }}</div>
 @if($resource->excerpt)
 <div style="font-size:12px;color:var(--text-muted);">{{ Str::limit($resource->excerpt, 60) }}</div>
 @endif
 </div>
 </div>
 </td>
 <td>
 <span class="badge {{ $typeClasses[$resource->resource_type] ?? '' }}" style="font-size:11px;">
 {{ ucfirst(str_replace('_',' ',$resource->resource_type)) }}
 </span>
 </td>
 <td style="font-size:13px;">{{ $resource->author->name }}</td>
 <td style="font-size:13px;color:var(--text-muted);">{{ $resource->category?->name ?? 'â‚¬"' }}</td>
 <td style="font-size:13px;">
 @if($resource->curriculum_id)
 <span style="font-size:11px;background:rgba(10,100,188,.08);color:#0A64BC;padding:2px 7px;border-radius:999px;white-space:nowrap;">{{ $resource->curriculum->name ?? 'â‚¬"' }}</span>
 @else
 <span style="font-size:11px;color:var(--text-muted);">All</span>
 @endif
 </td>
 <td style="font-size:13px;color:var(--text-muted);">{{ $resource->grade_level ?? 'â‚¬"' }}</td>
 <td data-order="{{ $resource->download_count }}" style="font-size:13px;font-weight:600;">{{ number_format($resource->download_count) }}</td>
 <td>
 @if(!$resource->is_approved)
 <span class="badge badge-warning">Pending</span>
 @elseif($resource->is_published)
 <span class="badge badge-success">Published</span>
 @else
 <span class="badge badge-secondary">Draft</span>
 @endif
 </td>
 <td>
 <div style="display:flex;gap:6px;flex-wrap:wrap;">
 <a href="{{ route('library.show', $resource->slug) }}" target="_blank" class="btn btn-secondary btn-sm">View</a>

 @if(!$resource->is_approved)
 <form method="POST" action="{{ route('admin.library.approve', $resource) }}" style="display:inline;">
 @csrf
 <button type="submit" class="btn btn-success btn-sm">Approve</button>
 </form>
 @endif

 <form method="POST" action="{{ route('admin.library.toggle', $resource) }}" style="display:inline;">
 @csrf
 <button type="submit" class="btn btn-sm" style="{{ $resource->is_published ? 'background:#FEF9C3;color:#854D0E;' : 'background:#DCFCE7;color:#166534;' }}border:none;font-weight:600;">
 {{ $resource->is_published ? 'Unpublish' : 'Publish' }}
 </button>
 </form>

 <form method="POST" action="{{ route('admin.library.destroy', $resource) }}" style="display:inline;" onsubmit="return confirm('Delete this resource?')">
 @csrf @method('DELETE')
 <button type="submit" class="btn btn-danger btn-sm">Delete</button>
 </form>
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
 No resources found. <button onclick="document.getElementById('add-modal').style.display='flex'" style="background:none;border:none;color:var(--primary);font-weight:600;cursor:pointer;">Add the first one " '</button>
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 @if($resources->hasPages())
 <div style="padding:16px 20px;border-top:1px solid var(--hairline);">
 {{ $resources->withQueryString()->links() }}
 </div>
 @endif
</div>

{{-- Categories management --}}
<div class="card card-p">
 <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
 <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;">Categories</h3>
 <button onclick="document.getElementById('cat-modal').style.display='flex'" class="btn btn-secondary btn-sm">+ Add Category</button>
 </div>
 <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
 @foreach($categories as $cat)
 <div style="background:#F8FAF8;border:1px solid var(--hairline);border-radius:12px;padding:14px;display:flex;align-items:center;justify-content:space-between;gap:10px;">
 <div>
 @if($cat->icon)<span style="font-size:18px;margin-right:6px;">{{ $cat->icon }}</span>@endif
 <span style="font-weight:600;font-size:14px;">{{ $cat->name }}</span>
 @if($cat->description)<div style="font-size:12px;color:var(--text-muted);margin-top:3px;">{{ Str::limit($cat->description, 40) }}</div>@endif
 </div>
 <form method="POST" action="{{ route('admin.library.categories.destroy', $cat) }}" onsubmit="return confirm('Delete category?')">
 @csrf @method('DELETE')
 <button type="submit" style="background:none;border:none;color:#DC2626;cursor:pointer;font-size:16px;" title="Delete">Å-•</button>
 </form>
 </div>
 @endforeach
 @if($categories->isEmpty())
 <p style="color:var(--text-muted);font-size:14px;">No categories yet.</p>
 @endif
 </div>
</div>

{{-- Add Resource Modal --}}
<div id="add-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)this.style.display='none'">
 <div style="background:#fff;border-radius:20px;padding:32px;max-width:600px;width:100%;max-height:90vh;overflow-y:auto;">
 <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
 <h2 style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;">Add Resource</h2>
 <button onclick="document.getElementById('add-modal').style.display='none'" style="background:none;border:none;font-size:22px;cursor:pointer;color:var(--text-muted);">Å-•</button>
 </div>
 <form method="POST" action="{{ route('admin.library.store') }}" enctype="multipart/form-data">
 @csrf
 <div style="display:flex;flex-direction:column;gap:16px;">
 <div>
 <label class="form-label">Title *</label>
 <input type="text" name="title" class="form-input" required placeholder="Resource title">
 </div>
 <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
 <div>
 <label class="form-label">Subject</label>
 <select name="subject_id" class="form-input">
 <option value="">None</option>
 @foreach($subjects as $s)
 <option value="{{ $s->id }}">{{ $s->name }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="form-label">Curriculum</label>
 <select name="curriculum_id" class="form-input">
 <option value="">All tracks</option>
 @foreach($curricula->groupBy('track') as $trackName => $group)
 <optgroup label="{{ ucfirst($trackName) }}">
 @foreach($group as $c)
 <option value="{{ $c->id }}">{{ $c->name }}</option>
 @endforeach
 </optgroup>
 @endforeach
 </select>
 </div>
 </div>
 <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
 <div>
 <label class="form-label">Resource Type *</label>
 <select name="resource_type" class="form-input" required>
 @foreach($resourceTypes as $t)
 <option value="{{ $t }}">{{ ucfirst(str_replace('_',' ',$t)) }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="form-label">Category</label>
 <select name="blog_category_id" class="form-input">
 <option value="">None</option>
 @foreach($categories as $cat)
 <option value="{{ $cat->id }}">{{ $cat->name }}</option>
 @endforeach
 </select>
 </div>
 </div>
 <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
 <div>
 <label class="form-label">Grade Level</label>
 <select name="grade_level" class="form-input">
 <option value="">All Levels</option>
 @foreach(['Form 1','Form 2','Form 3','Form 4','Primary','University'] as $g)
 <option value="{{ $g }}">{{ $g }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="form-label">Publish?</label>
 <select name="is_published" class="form-input">
 <option value="1">Yes â‚¬" publish now</option>
 <option value="0">No â‚¬" save as draft</option>
 </select>
 </div>
 </div>
 <div>
 <label class="form-label">Excerpt / Summary</label>
 <textarea name="excerpt" class="form-input" rows="3" placeholder="Brief descriptionâ‚¬Â¦"></textarea>
 </div>
 <div>
 <label class="form-label">Content (optional for PDFs/videos)</label>
 <textarea name="content" class="form-input" rows="5" placeholder="Full content or notesâ‚¬Â¦"></textarea>
 </div>
 <div>
 <label class="form-label">File (PDF, video, doc â‚¬" max 50MB)</label>
 <input type="file" name="file" class="form-input" accept=".pdf,.doc,.docx,.ppt,.pptx,.mp4,.avi,.mov">
 </div>
 <div>
 <label class="form-label">Thumbnail Image (optional)</label>
 <input type="file" name="thumbnail" class="form-input" accept="image/*">
 </div>
 <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;">
 <button type="button" onclick="document.getElementById('add-modal').style.display='none'" class="btn btn-secondary">Cancel</button>
 <button type="submit" class="btn btn-primary">Save Resource</button>
 </div>
 </div>
 </form>
 </div>
</div>

{{-- Add Category Modal --}}
<div id="cat-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)this.style.display='none'">
 <div style="background:#fff;border-radius:20px;padding:32px;max-width:420px;width:100%;">
 <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
 <h2 style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;">Add Category</h2>
 <button onclick="document.getElementById('cat-modal').style.display='none'" style="background:none;border:none;font-size:22px;cursor:pointer;color:var(--text-muted);">Å-•</button>
 </div>
 <form method="POST" action="{{ route('admin.library.categories.store') }}">
 @csrf
 <div style="display:flex;flex-direction:column;gap:14px;">
 <div>
 <label class="form-label">Category Name *</label>
 <input type="text" name="name" class="form-input" required placeholder="e.g. Mathematics">
 </div>
 <div>
 <label class="form-label">Description</label>
 <textarea name="description" class="form-input" rows="2" placeholder="Brief description"></textarea>
 </div>
 <div>
 <label class="form-label">Emoji Icon (optional)</label>
 <input type="text" name="icon" class="form-input" placeholder="e.g. <i class="fa-solid fa-note-sticky"></i>" maxlength="10">
 </div>
 <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;">
 <button type="button" onclick="document.getElementById('cat-modal').style.display='none'" class="btn btn-secondary">Cancel</button>
 <button type="submit" class="btn btn-primary">Create Category</button>
 </div>
 </div>
 </form>
 </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
 initDataTable('#library-table', {
 order: [[5, 'desc']], // Downloads descending
 columnDefs: [
 { type: 'num', targets: 5 }, // Downloads
 { orderable: false, targets: 7 } // Actions
 ]
 });
});
</script>
@endpush
