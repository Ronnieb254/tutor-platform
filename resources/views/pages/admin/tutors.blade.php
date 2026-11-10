@extends('layouts.admin')
@section('title', 'Tutor Approvals')

@section('content')
@php
use App\Models\TutorProfile;
use App\Models\User;

// Fetch all tutors with filtering and pagination
$status = request('status', 'all'); // all|pending|approved|suspended
$search = request('search', ''); // Client-side filter
$sortBy = request('sort', 'created'); // Server-side sort: created|name|email|status
$perPage = 25;

$query = TutorProfile::with(['user', 'subjects', 'documents']);

// Status filter (server-side)
if ($status === 'new') {
    // New = tutors with NO documents uploaded yet
    $query->whereDoesntHave('documents');
} elseif ($status === 'pending') {
    // Pending = tutors with documents but not yet approved (no approved_at)
    $query->where('is_approved', false)->whereNull('approved_at')->whereHas('documents');
} elseif ($status === 'approved') {
    $query->where('is_approved', true);
} elseif ($status === 'suspended') {
    $query->where('is_approved', false)->whereNotNull('approved_at');
}

// Sorting (server-side)
if ($sortBy === 'name') {
    $query->join('users', 'users.id', '=', 'tutor_profiles.user_id')
          ->orderBy('users.name', 'asc')
          ->select('tutor_profiles.*');
} elseif ($sortBy === 'email') {
    $query->join('users', 'users.id', '=', 'tutor_profiles.user_id')
          ->orderBy('users.email', 'asc')
          ->select('tutor_profiles.*');
} elseif ($sortBy === 'status') {
    $query->orderBy('is_approved', 'desc')->orderBy('created_at', 'desc');
} else {
    // Default: sort by created_at descending
    $query->orderBy('created_at', 'desc');
}

$tutors = $query->paginate($perPage);

// Stats for header
$totalTutors = TutorProfile::count();
$newCount = TutorProfile::whereDoesntHave('documents')->count();
$approvedCount = TutorProfile::where('is_approved', true)->count();
$pendingCount = TutorProfile::where('is_approved', false)->whereNull('approved_at')->whereHas('documents')->count();
$suspendedCount = TutorProfile::where('is_approved', false)->whereNotNull('approved_at')->count();
@endphp

<style>
/* Modal styles */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1000;display:flex;align-items:center;justify-content:center;padding:20px;}
.modal-box{background:#fff;border-radius:20px;width:100%;max-width:900px;max-height:85vh;min-height:500px;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2);will-change:transform;}
.modal-head{padding:16px 22px;border-bottom:1px solid rgba(21,40,31,.08);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;}
.modal-body{flex:1;overflow-y:auto;padding:20px;overflow-x:hidden;}
.review-panel{background:#fff;border-radius:18px;border:1px solid rgba(21,40,31,.08);margin-bottom:14px;overflow:hidden;}
.review-head{padding:18px 22px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;}
.review-detail{padding:22px;border-top:1px solid rgba(21,40,31,.06);display:none;}
.review-detail.open{display:block;}
.doc-row{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:#F9FAF9;border:1px solid rgba(21,40,31,.06);margin-bottom:8px;}
.doc-thumb{width:48px;height:48px;border-radius:10px;object-fit:cover;border:1px solid rgba(21,40,31,.08);flex-shrink:0;cursor:pointer;}
.doc-thumb-placeholder{width:48px;height:48px;border-radius:10px;background:rgba(21,40,31,.06);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:20px;cursor:pointer;}
.email-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1500;display:none;align-items:center;justify-content:center;padding:20px;overflow-y:auto;}
.email-modal-overlay.open{display:flex;}
.email-modal-box{background:#fff;border-radius:20px;width:100%;max-width:520px;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2);}
.email-form-group{margin-bottom:18px;}
.email-form-group label{display:block;font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;}
.email-form-group input,.email-form-group textarea{width:100%;padding:12px 14px;border:1.5px solid rgba(21,40,31,.18);border-radius:12px;font-size:14px;font-family:inherit;outline:none;color:#15281F;line-height:1.5;}
.email-form-group input:focus,.email-form-group textarea:focus{border-color:#0A64BC;background:rgba(10,100,188,.02);}
.email-form-group textarea{resize:none;min-height:120px;}
.email-char-counter{font-size:12px;color:rgba(21,40,31,.5);margin-top:4px;}

/* Table styles */
.tutors-table-wrapper{overflow-x:auto;border-radius:14px;border:1px solid var(--hairline);}
.tutors-table{width:100%;border-collapse:collapse;background:#fff;}
.tutors-table th{background:#F9FAF9;padding:14px 16px;text-align:left;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--hairline);}
.tutors-table th a{color:var(--muted);text-decoration:none;display:flex;align-items:center;gap:6px;white-space:nowrap;}
.tutors-table th a:hover{color:var(--ink);}
.tutors-table td{padding:12px 16px;border-bottom:1px solid var(--hairline);}
.tutors-table tbody tr:hover{background:#F9FAF9;}
.tutor-name-cell{display:flex;align-items:center;gap:10px;font-weight:600;color:var(--ink);}
.tutor-avatar{width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#fff;flex-shrink:0;}
.tutor-email-link{color:#0A64BC;text-decoration:none;}
.tutor-email-link:hover{text-decoration:underline;}
.status-badge{display:inline-block;padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700;}
.status-new{background:#E0E7FF;color:#3730A3;}
.status-pending{background:#FEF9C3;color:#854D0E;}
.status-approved{background:#DCFCE7;color:#166534;}
.status-suspended{background:#FEE2E2;color:#991B1B;}
.tutor-actions{display:flex;gap:6px;flex-wrap:wrap;}
.tutor-actions button,.tutor-actions a{padding:6px 12px;font-size:12px;border-radius:8px;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:4px;transition:all .2s;}
.btn-view{background:rgba(10,100,188,.1);color:#0A64BC;}
.btn-view:hover{background:rgba(10,100,188,.2);}
.btn-email{background:rgba(16,185,129,.1);color:#10B981;}
.btn-email:hover{background:rgba(16,185,129,.2);}

/* Card Grid Styles */
.tutors-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;margin-bottom:24px;}
.tutor-card{background:#fff;border-radius:18px;border:1.5px solid rgba(21,40,31,.08);box-shadow:0 2px 10px rgba(0,0,0,.06);padding:20px;transition:all .2s;display:flex;flex-direction:column;}
.tutor-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.12);transform:translateY(-2px);}
.tutor-card-header{display:flex;align-items:flex-start;gap:12px;margin-bottom:16px;justify-content:space-between;}
.tutor-card-avatar{width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#fff;flex-shrink:0;}
.tutor-card-info{flex:1;}
.tutor-card-name{font-weight:700;color:#15281F;font-size:15px;margin-bottom:2px;}
.tutor-card-id{font-size:12px;color:rgba(21,40,31,.5);}
.tutor-card-badge{display:inline-block;}
.tutor-card-divider{height:1px;background:rgba(21,40,31,.08);margin:12px 0;}
.tutor-card-row{display:flex;align-items:center;gap:10px;font-size:13px;color:rgba(21,40,31,.65);margin-bottom:10px;}
.tutor-card-row:last-of-type{margin-bottom:0;}
.tutor-card-row i{width:18px;text-align:center;color:#0A64BC;flex-shrink:0;}
.tutor-card-email{color:#0A64BC;text-decoration:none;}
.tutor-card-email:hover{text-decoration:underline;}
.tutor-card-footer{display:flex;align-items:center;gap:10px;margin-top:16px;padding-top:12px;border-top:1px solid rgba(21,40,31,.08);}
.tutor-card-checkbox{width:18px;height:18px;cursor:pointer;flex-shrink:0;}
.tutor-card-actions{display:flex;gap:8px;flex:1;}
.tutor-card-btn{flex:1;padding:8px 12px;font-size:12px;border-radius:8px;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:4px;transition:all .2s;white-space:nowrap;}
.tutor-card-btn-email{background:rgba(16,185,129,.1);color:#10B981;}
.tutor-card-btn-email:hover{background:rgba(16,185,129,.2);}
.tutor-card-btn-view{background:rgba(10,100,188,.1);color:#0A64BC;}
.tutor-card-btn-view:hover{background:rgba(10,100,188,.2);}

/* Search and filter bar */
.tutors-controls{display:flex;gap:12px;align-items:flex-start;flex-wrap:wrap;margin-bottom:20px;}
.search-box{flex:1;min-width:200px;}
.search-box input{width:100%;padding:10px 14px;border:1.5px solid var(--hairline);border-radius:10px;font-size:14px;outline:none;}
.search-box input:focus{border-color:#0A64BC;background:rgba(10,100,188,.02);}
.filter-select{min-width:160px;}
.filter-select select{padding:10px 14px;border:1.5px solid var(--hairline);border-radius:10px;font-size:14px;outline:none;background:#fff;cursor:pointer;appearance:none;padding-right:36px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23666' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;background-size:12px;}
.filter-select select:focus{border-color:#0A64BC;background-color:rgba(10,100,188,.02);}
.bulk-actions{display:flex;gap:12px;align-items:center;}
.bulk-checkbox{width:20px;height:20px;cursor:pointer;}
.email-selected-btn{padding:10px 14px;border-radius:10px;border:none;background:#0A64BC;color:#fff;cursor:pointer;font-size:13px;font-weight:600;transition:all .2s;display:flex;align-items:center;gap:6px;}
.email-selected-btn:hover{background:#084BA6;}
.email-all-btn{padding:10px 14px;border-radius:10px;border:1.5px solid rgba(10,100,188,.3);background:rgba(10,100,188,.02);color:#0A64BC;cursor:pointer;font-size:13px;font-weight:600;transition:all .2s;display:flex;align-items:center;gap:6px;}
.email-all-btn:hover{border-color:rgba(10,100,188,.5);background:rgba(10,100,188,.08);}

/* Responsive */
@media(max-width:768px){
  .tutors-grid{grid-template-columns:repeat(auto-fill,minmax(260px,1fr)) !important;}
}

@media(max-width:480px){
  .tutors-grid{grid-template-columns:1fr !important;}
  .tutors-controls{flex-direction:column;}
  .search-box,.filter-select{width:100%;}
  .filter-select select{width:100%;}
}

@media(max-width:639px){
 .modal-content, [id*="tutor-"] .modal-box{ width:95vw !important; max-width:95vw !important; max-height:90vh !important; }
 .modal-box{ width:95vw !important; max-width:95vw !important; max-height:90vh !important; }
 .email-modal-box{ width:95vw !important; max-width:95vw !important; }
}
</style>

<div class="page-header" style="margin-bottom:32px;">
 <div style="max-width:1400px;margin:0 auto;">
 <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:24px;flex-wrap:wrap;">
 <div>
 <h1 style="font-family:'Fraunces',serif;font-size:32px;font-weight:800;color:#15281F;margin-bottom:8px;">Tutor Management</h1>
 <p style="font-size:15px;color:rgba(21,40,31,.6);line-height:1.5;">Manage all tutors on the platform. Review documents, approve profiles, and handle communications.</p>
 </div>
 </div>
 </div>
</div>

@if(session('success'))
<div style="background:#F0FDF4;border:1.5px solid #86EFAC;border-radius:14px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:flex-start;gap:12px;">
 <i class="fa-solid fa-circle-check" style="color:#10B981;font-size:20px;margin-top:2px;flex-shrink:0;"></i>
 <div>
 <div style="font-weight:700;color:#166534;font-size:14px;">Success!</div>
 <div style="font-size:13px;color:#4ADE80;margin-top:4px;">{{ session('success') }}</div>
 </div>
</div>
@endif
@if(session('error'))
<div style="background:#FEF2F2;border:1.5px solid #FECACA;border-radius:14px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:flex-start;gap:12px;">
 <i class="fa-solid fa-circle-xmark" style="color:#EF4444;font-size:20px;margin-top:2px;flex-shrink:0;"></i>
 <div>
 <div style="font-weight:700;color:#991B1B;font-size:14px;">Error</div>
 <div style="font-size:13px;color:#FCA5A5;margin-top:4px;">{{ session('error') }}</div>
 </div>
</div>
@endif
@if(session('info'))
<div style="background:#DBEAFE;border:1.5px solid #93C5FD;border-radius:14px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:flex-start;gap:12px;">
 <i class="fa-solid fa-circle-info" style="color:#0A64BC;font-size:20px;margin-top:2px;flex-shrink:0;"></i>
 <div>
 <div style="font-weight:700;color:#1D4ED8;font-size:14px;">Info</div>
 <div style="font-size:13px;color:#3B82F6;margin-top:4px;">{{ session('info') }}</div>
 </div>
</div>
@endif

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:32px;">
 @foreach([
 ['New', $newCount, '#6366F1', 'fa-star', 'rgba(99,102,241,.08)', 'new'],
 ['Pending', $pendingCount, '#EEBE15', 'fa-hourglass-end', 'rgba(212,160,23,.08)', 'pending'],
 ['Approved', $approvedCount, '#10B981', 'fa-check-circle', 'rgba(16,185,129,.08)', 'approved'],
 ['Suspended', $suspendedCount, '#EF4444', 'fa-ban', 'rgba(239,68,68,.08)', 'suspended'],
 ['Total', $totalTutors, '#3B82F6', 'fa-users', 'rgba(59,130,246,.08)', null],
 ] as [$label, $val, $col, $ic, $bg, $status_param])
 <div style="background:#fff;border-radius:18px;border:1.5px solid rgba(21,40,31,.08);padding:20px;display:flex;align-items:center;gap:14px;box-shadow:0 2px 8px rgba(0,0,0,.05);">
 <div style="width:48px;height:48px;border-radius:14px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:22px;color:{{ $col }};">
 <i class="fa-solid {{ $ic }}"></i>
 </div>
 <div style="flex:1;">
 <div style="font-size:12px;color:rgba(21,40,31,.5);font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px;">{{ $label }}</div>
 <div style="font-family:'Fraunces',serif;font-size:28px;font-weight:800;color:#15281F;line-height:1;">{{ $val }}</div>
 </div>
 </div>
 @endforeach
</div>

{{-- Search, Filter, and Bulk Actions --}}
<div style="margin-bottom:24px;">
 <div class="tutors-controls">
 <div class="search-box">
 <input type="text" id="search-input" placeholder="Search by name, email, or tutor ID…" style="">
 </div>
 <div class="filter-select">
 <select id="status-filter" onchange="updateStatusFilter(this.value)">
 <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Statuses</option>
 <option value="new" {{ $status === 'new' ? 'selected' : '' }}>New (No Docs)</option>
 <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending (Under Review)</option>
 <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
 <option value="suspended" {{ $status === 'suspended' ? 'selected' : '' }}>Suspended</option>
 </select>
 </div>
 <div class="bulk-actions">
 <input type="checkbox" id="select-all-checkbox" class="bulk-checkbox" onchange="toggleAllCheckboxes(this.checked)">
 <form id="bulk-email-form" method="POST" action="{{ route('admin.tutors.send-email-all') }}" style="display:none;">
 @csrf
 <input type="hidden" name="profile_ids" id="profile-ids-input">
 </form>
 <button type="button" id="email-button" class="email-all-btn" onclick="triggerBulkEmail()">
 <i class="fa-solid fa-envelope"></i> Email All Tutors
 </button>
 </div>
 </div>
</div>

{{-- Tutors Card Grid --}}
@if($tutors->isEmpty())
<div style="text-align:center;padding:64px 40px;background:#fff;border-radius:18px;border:1.5px solid rgba(21,40,31,.08);">
 <div style="display:flex;align-items:center;justify-content:center;width:80px;height:80px;background:rgba(10,100,188,.08);border-radius:16px;margin:0 auto 24px;">
 <i class="fa-solid fa-inbox" style="font-size:36px;color:#0A64BC;"></i>
 </div>
 <h3 style="font-family:'Fraunces',serif;font-size:22px;font-weight:800;color:#15281F;margin-bottom:8px;">No tutors found</h3>
 <p style="color:rgba(21,40,31,.55);font-size:15px;line-height:1.6;margin-bottom:24px;max-width:500px;margin-left:auto;margin-right:auto;">
 Adjust your search or filter criteria and try again.
 </p>
</div>
@else
<div id="tutors-grid" class="tutors-grid">
 @foreach($tutors as $profile)
 @php
 $docs = $profile->documents;
 $approvedDocs = $docs->where('status', 'approved')->count();
 $totalDocs = $docs->count();
 $subjectCount = $profile->subjects->count();
 
 // Determine status display
 if ($profile->documents->isEmpty()) {
 $statusLabel = 'New';
 $statusClass = 'status-new';
 } elseif ($profile->is_approved) {
 $statusLabel = 'Approved';
 $statusClass = 'status-approved';
 } elseif ($profile->approved_at) {
 $statusLabel = 'Suspended';
 $statusClass = 'status-suspended';
 } else {
 $statusLabel = 'Pending';
 $statusClass = 'status-pending';
 }
 
 // Avatar color based on email
 $avatarColor = '#' . substr(md5($profile->user->email), 0, 6);
 @endphp
 <div class="tutor-card" data-tutor-name="{{ strtolower($profile->user->name) }}" data-tutor-email="{{ strtolower($profile->user->email) }}" data-tutor-id="{{ $profile->id }}">
 <div class="tutor-card-header">
 <div style="display:flex;align-items:flex-start;gap:12px;flex:1;">
 <div class="tutor-card-avatar" style="background-color:{{ $avatarColor }};">{{ substr($profile->user->name, 0, 1) }}</div>
 <div class="tutor-card-info">
 <div class="tutor-card-name">{{ $profile->user->name }}</div>
 <div class="tutor-card-id">Tutor #{{ $profile->id }}</div>
 </div>
 </div>
 <span class="status-badge {{ $statusClass }} tutor-card-badge">{{ $statusLabel }}</span>
 </div>

 <div class="tutor-card-divider"></div>

 <div class="tutor-card-row">
 <i class="fa-solid fa-envelope"></i>
 <a href="mailto:{{ $profile->user->email }}" class="tutor-card-email">{{ $profile->user->email }}</a>
 </div>
 <div class="tutor-card-row">
 <i class="fa-solid fa-check"></i>
 <span>{{ $approvedDocs }}/{{ max(4, $totalDocs) }} docs verified</span>
 </div>
 <div class="tutor-card-row">
 <i class="fa-solid fa-book"></i>
 <span>{{ $subjectCount }} {{ $subjectCount === 1 ? 'subject' : 'subjects' }}</span>
 </div>
 <div class="tutor-card-row">
 <i class="fa-solid fa-calendar"></i>
 <span>Joined {{ $profile->created_at->diffForHumans() }}</span>
 </div>

 <div class="tutor-card-footer">
 <input type="checkbox" class="tutor-card-checkbox tutor-checkbox" value="{{ $profile->id }}" onchange="updateEmailButton()">
 <div class="tutor-card-actions">
 <button class="tutor-card-btn tutor-card-btn-email" onclick="openEmailModal('{{ $profile->id }}', '{{ addslashes($profile->user->name) }}', '{{ $profile->user->email }}')">
 <i class="fa-solid fa-envelope"></i> Email
 </button>
 <button class="tutor-card-btn tutor-card-btn-view" onclick="openTutorDetailModal('{{ $profile->id }}', '{{ addslashes($profile->user->name) }}')">
 <i class="fa-solid fa-eye"></i> View
 </button>
 </div>
 </div>
 </div>
 @endforeach
</div>

{{-- Pagination --}}
@if($tutors->hasPages())
<div style="margin-top:28px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
 <div style="font-size:13px;color:var(--muted);">
 Showing {{ $tutors->firstItem() }} to {{ $tutors->lastItem() }} of {{ $tutors->total() }} results
 </div>
 <div style="display:flex;align-items:center;gap:4px;">
 @if($tutors->onFirstPage())
 <span style="padding:8px 14px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--muted);cursor:not-allowed;background:#fafafa;"><i class="fa-solid fa-chevron-left"></i></span>
 @else
 <a href="{{ $tutors->previousPageUrl() }}" style="padding:8px 14px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--ink);text-decoration:none;background:#fff;transition:all .2s;" onmouseover="this.style.borderColor='#0A64BC'" onmouseout="this.style.borderColor='var(--hairline)'"><i class="fa-solid fa-chevron-left"></i></a>
 @endif
 @foreach($tutors->getUrlRange(1, $tutors->lastPage()) as $page => $url)
 @if($page == $tutors->currentPage())
 <span style="padding:8px 14px;border-radius:8px;border:1px solid #0A64BC;font-size:13px;font-weight:700;color:#fff;background:#0A64BC;box-shadow:0 2px 8px rgba(10,100,188,.3);">{{ $page }}</span>
 @else
 <a href="{{ $url }}" style="padding:8px 14px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--ink);text-decoration:none;background:#fff;transition:all .2s;" onmouseover="this.style.borderColor='#0A64BC'" onmouseout="this.style.borderColor='var(--hairline)'">{{ $page }}</a>
 @endif
 @endforeach
 @if($tutors->hasMorePages())
 <a href="{{ $tutors->nextPageUrl() }}" style="padding:8px 14px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--ink);text-decoration:none;background:#fff;transition:all .2s;" onmouseover="this.style.borderColor='#0A64BC'" onmouseout="this.style.borderColor='var(--hairline)'"><i class="fa-solid fa-chevron-right"></i></a>
 @else
 <span style="padding:8px 14px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--muted);cursor:not-allowed;background:#fafafa;"><i class="fa-solid fa-chevron-right"></i></span>
 @endif
 </div>
</div>
@endif
@endif

{{-- Reject doc modal --}}
<div id="reject-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:2000;align-items:center;justify-content:center;padding:20px;">
 <div style="background:#fff;border-radius:20px;padding:32px;width:100%;max-width:440px;">
 <h3 style="font-family:'Fraunces',serif;font-size:19px;color:#15281F;margin-bottom:6px;">Reject Document</h3>
 <p style="font-size:13px;color:rgba(21,40,31,.55);margin-bottom:18px;" id="reject-doc-name"></p>
 <form id="reject-form" method="POST">
 @csrf @method('PATCH')
 <div style="margin-bottom:16px;">
 <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:block;margin-bottom:6px;">
 Reason for rejection <span style="color:#E8522A;">*</span>
 </label>
 <select name="reason" required
 style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;margin-bottom:10px;appearance:none;">
 <option value="">Select reason</option>
 <option>Document is blurry or unreadable</option>
 <option>Wrong document uploaded</option>
 <option>Document appears altered or invalid</option>
 <option>Document has expired</option>
 <option>Name on document does not match profile</option>
 <option>Other (specify below)</option>
 </select>
 <textarea name="admin_note" rows="2" placeholder="Add a note for the tutor (optional)"
 style="width:100%;padding:10px 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;resize:none;line-height:1.6;"></textarea>
 </div>
 <div style="display:flex;gap:10px;">
 <button type="button" onclick="document.getElementById('reject-modal').style.display='none'" class="btn btn-o" style="flex:1;justify-content:center;">Cancel</button>
 <button type="submit" class="btn btn-danger" style="flex:1;justify-content:center;">
 <i class="fa-solid fa-circle-xmark"></i> Reject Document
 </button>
 </div>
 </form>
 </div>
</div>

{{-- Document Viewer Modal --}}
<div id="doc-viewer-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:2500;align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this && !document.getElementById('doc-viewer-modal').classList.contains('loading')) closeDocViewer()">
 <div style="background:#fff;border-radius:20px;width:100%;max-width:900px;max-height:85vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.3);">
 <div style="padding:20px 24px;border-bottom:1px solid rgba(21,40,31,.08);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
 <h3 style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:#15281F;margin:0;text-transform:capitalize;" id="doc-viewer-title">Document</h3>
 <button id="doc-viewer-close-btn" onclick="closeDocViewer()" style="width:36px;height:36px;border-radius:10px;border:none;background:rgba(0,0,0,.05);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:20px;transition:all .2s;">
 <i class="fa-solid fa-circle-xmark" style="color:rgba(21,40,31,.5);"></i>
 </button>
 </div>
 
 {{-- Loading State --}}
 <div id="doc-viewer-loading" style="flex:1;overflow:auto;display:flex;align-items:center;justify-content:center;padding:20px;background:#F9FAF9;flex-direction:column;gap:16px;">
 <div style="width:48px;height:48px;border:3px solid rgba(10,100,188,.2);border-top-color:#0A64BC;border-radius:50%;animation:spin 1s linear infinite;"></div>
 <p style="color:rgba(21,40,31,.6);font-size:14px;margin:0;">Loading document & tutor details...</p>
 </div>
 
 {{-- Content State --}}
 <div id="doc-viewer-content" style="flex:1;overflow:auto;display:none;padding:20px;background:#F9FAF9;">
 <!-- Document content will be inserted here -->
 </div>
 
 {{-- Action Buttons --}}
 <div style="padding:16px 24px;border-top:1px solid rgba(21,40,31,.08);display:none;gap:10px;justify-content:flex-end;flex-shrink:0;" id="doc-viewer-actions">
 <button onclick="closeDocViewer()" class="btn btn-outline" style="padding:8px 16px;">Close</button>
 <button onclick="rejectDocFromViewer()" class="btn btn-danger" style="padding:8px 16px;">
 <i class="fa-solid fa-circle-xmark"></i> Reject
 </button>
 <button onclick="approveDoc()" class="btn btn-primary" style="padding:8px 16px;">
 <i class="fa-solid fa-check-circle"></i> Approve
 </button>
 </div>
 </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
 pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
</script>

<style>
 @keyframes spin {
 from { transform: rotate(0deg); }
 to { transform: rotate(360deg); }
 }
</style>

{{-- Email Modals --}}
<div id="email-modal" class="email-modal-overlay" onclick="if(event.target===this) this.classList.remove('open')">
 <div class="email-modal-box" onclick="event.stopPropagation()">
 <div class="modal-head">
 <h3 style="font-family:'Fraunces',serif;font-size:18px;font-weight:700;">Email Tutor</h3>
 <button onclick="document.getElementById('email-modal').classList.remove('open')" style="width:32px;height:32px;border-radius:8px;border:none;background:rgba(0,0,0,.05);cursor:pointer;display:flex;align-items:center;justify-content:center;">
 <i class="fa-solid fa-circle-xmark"></i>
 </button>
 </div>
 <div style="padding:24px;overflow-y:auto;">
 <form id="email-form" onsubmit="submitEmail(event, this, 'email')">
 <div class="email-form-group">
 <label>To</label>
 <input type="text" id="email-to" readonly style="background:rgba(21,40,31,.05);cursor:not-allowed;">
 </div>
 <div class="email-form-group">
 <label>Subject <span style="color:#E8522A;">*</span></label>
 <input type="text" name="subject" id="email-subject" placeholder="Enter email subject..." required style="">
 </div>
 <div class="email-form-group">
 <label>Message <span style="color:#E8522A;">*</span></label>
 <textarea name="message" id="email-message" placeholder="Enter your message..." required></textarea>
 <div class="email-char-counter"><span id="email-char-count">0</span>/5000 characters</div>
 </div>
 <div style="display:flex;gap:10px;margin-top:24px;border-top:1px solid rgba(21,40,31,.1);padding-top:16px;">
 <button type="button" onclick="document.getElementById('email-modal').classList.remove('open')" class="btn btn-outline" style="flex:1;justify-content:center;">Cancel</button>
 <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;" id="email-submit-btn">
 <i class="fa-solid fa-paper-plane"></i> Send
 </button>
 </div>
 </form>
 </div>
 </div>
</div>

<div id="email-all-modal" class="email-modal-overlay" onclick="if(event.target===this) this.classList.remove('open')">
 <div class="email-modal-box" onclick="event.stopPropagation()">
 <div class="modal-head">
 <h3 style="font-family:'Fraunces',serif;font-size:18px;font-weight:700;">Email Tutors</h3>
 <button onclick="document.getElementById('email-all-modal').classList.remove('open')" style="width:32px;height:32px;border-radius:8px;border:none;background:rgba(0,0,0,.05);cursor:pointer;display:flex;align-items:center;justify-content:center;">
 <i class="fa-solid fa-circle-xmark"></i>
 </button>
 </div>
 <div style="padding:24px;overflow-y:auto;">
 <form id="email-all-form" onsubmit="submitEmail(event, this, 'email-all')">
 <p style="font-size:13px;color:rgba(21,40,31,.65);margin-bottom:20px;">This message will be sent to <strong id="email-all-count">0</strong> tutor<span id="email-all-plural">s</span>.</p>
 <div class="email-form-group">
 <label>Subject <span style="color:#E8522A;">*</span></label>
 <input type="text" name="subject" id="email-all-subject" placeholder="Enter email subject..." required>
 </div>
 <div class="email-form-group">
 <label>Message <span style="color:#E8522A;">*</span></label>
 <textarea name="message" id="email-all-message" placeholder="Enter your message..." required></textarea>
 <div class="email-char-counter"><span id="email-all-char-count">0</span>/5000 characters</div>
 </div>
 <div style="display:flex;gap:10px;margin-top:24px;border-top:1px solid rgba(21,40,31,.1);padding-top:16px;">
 <button type="button" onclick="document.getElementById('email-all-modal').classList.remove('open')" class="btn btn-outline" style="flex:1;justify-content:center;">Cancel</button>
 <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;" id="email-all-submit-btn">
 <i class="fa-solid fa-paper-plane"></i> Send
 </button>
 </div>
 </form>
 </div>
 </div>
</div>

{{-- Per-Tutor Detail Modals --}}
@php $allTutors = $tutors; @endphp
@foreach($allTutors as $profile)
 @php
 $docs = $profile->documents;
 $approvedDocs = $docs->where('status', 'approved')->count();
 $totalDocs = $docs->count();
 $subjectCount = $profile->subjects->count();
 
 // Determine status display
 if ($profile->documents->isEmpty()) {
 $statusLabel = 'New';
 $statusClass = 'status-new';
 } elseif ($profile->is_approved) {
 $statusLabel = 'Approved';
 $statusClass = 'status-approved';
 } elseif ($profile->approved_at) {
 $statusLabel = 'Suspended';
 $statusClass = 'status-suspended';
 } else {
 $statusLabel = 'Pending';
 $statusClass = 'status-pending';
 }
 
 // Avatar color based on email
 $avatarColor = '#' . substr(md5($profile->user->email), 0, 6);
 @endphp
 <div id="tutor-{{ $profile->id }}" class="modal-overlay" style="display:none;z-index:{{ 1500 + $loop->index }};" onclick="if(event.target===this) closeTutorModal('tutor-{{ $profile->id }}')">
 <div class="modal-box" onclick="event.stopPropagation()">
 <div class="modal-head">
 <div style="display:flex;align-items:center;gap:12px;">
 <div class="tutor-avatar" style="background-color:{{ $avatarColor }};width:40px;height:40px;font-size:16px;">{{ substr($profile->user->name, 0, 1) }}</div>
 <div>
 <h2 style="font-size:18px;font-weight:700;color:#15281F;margin:0;">{{ $profile->user->name }}</h2>
 <p style="font-size:12px;color:rgba(21,40,31,.5);margin:2px 0 0 0;">Tutor #{{ $profile->id }}</p>
 </div>
 </div>
 <button onclick="closeTutorModal('tutor-{{ $profile->id }}')" style="width:32px;height:32px;border-radius:8px;border:none;background:rgba(0,0,0,.05);cursor:pointer;display:flex;align-items:center;justify-content:center;">
 <i class="fa-solid fa-circle-xmark"></i>
 </button>
 </div>
 <div class="modal-body">
 {{-- Basic Info --}}
 <div class="review-panel">
 <div class="review-head">
 <h3 style="font-size:14px;font-weight:700;color:#15281F;">Contact Information</h3>
 </div>
 <div class="review-detail open">
 <p style="font-size:13px;color:rgba(21,40,31,.65);margin-bottom:6px;"><strong>Email:</strong> <a href="mailto:{{ $profile->user->email }}" style="color:#0A64BC;text-decoration:none;">{{ $profile->user->email }}</a></p>
 <p style="font-size:13px;color:rgba(21,40,31,.65);margin-bottom:6px;"><strong>Joined:</strong> {{ $profile->created_at->format('M d, Y') }}</p>
 <p style="font-size:13px;color:rgba(21,40,31,.65);"><strong>Status:</strong> <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span></p>
 </div>
 </div>

 {{-- Documents --}}
 <div class="review-panel">
 <div class="review-head">
 <h3 style="font-size:14px;font-weight:700;color:#15281F;">Documents ({{ $approvedDocs }}/{{ max(4, $totalDocs) }} Verified)</h3>
 </div>
 <div class="review-detail open" style="max-height:400px;overflow-y:auto;">
 @forelse($docs as $doc)
 <div class="doc-row">
 @if($doc->file_path && (str_ends_with($doc->file_path, '.jpg') || str_ends_with($doc->file_path, '.jpeg') || str_ends_with($doc->file_path, '.png') || str_ends_with($doc->file_path, '.gif')))
 <img src="{{ asset('storage/' . $doc->file_path) }}" alt="Document" class="doc-thumb" onclick="openDocViewer('{{ route('documents.view', $doc->id) }}', '{{ $doc->document_type }}', '{{ $doc->id }}')">
 @else
 <div class="doc-thumb-placeholder" onclick="openDocViewer('{{ route('documents.view', $doc->id) }}', '{{ $doc->document_type }}', '{{ $doc->id }}')"><i class="fa-solid fa-file"></i></div>
 @endif
 <div style="flex:1;">
 <p style="font-size:12px;font-weight:600;color:#15281F;margin:0;text-transform:capitalize;">{{ str_replace('_', ' ', $doc->document_type) }}</p>
 <p style="font-size:12px;color:rgba(21,40,31,.5);margin:2px 0 0 0;">Uploaded {{ $doc->created_at->diffForHumans() }}</p>
 </div>
 <span class="status-badge @if($doc->status === 'approved') status-approved @elseif($doc->status === 'rejected') status-suspended @else status-pending @endif" style="font-size:10px;padding:3px 8px;">{{ ucfirst($doc->status) }}</span>
 @if($doc->status !== 'approved')
 <button type="button" class="btn btn-o" style="font-size:12px;padding:4px 8px;" onclick="rejectDoc('{{ $doc->id }}', '{{ $doc->document_type }}')">
 <i class="fa-solid fa-circle-xmark"></i>
 </button>
 @endif
 </div>
 @empty
 <p style="font-size:13px;color:rgba(21,40,31,.5);text-align:center;padding:20px;">No documents uploaded yet.</p>
 @endforelse
 </div>
 </div>

 {{-- Subjects --}}
 <div class="review-panel">
 <div class="review-head">
 <h3 style="font-size:14px;font-weight:700;color:#15281F;">Subjects ({{ $subjectCount }})</h3>
 </div>
 <div class="review-detail open">
 @forelse($profile->subjects as $subject)
 <div style="padding:10px;background:rgba(10,100,188,.05);border-radius:8px;margin-bottom:8px;font-size:13px;color:#0A64BC;font-weight:600;">
 {{ $subject->name }}
 </div>
 @empty
 <p style="font-size:13px;color:rgba(21,40,31,.5);text-align:center;padding:20px;">No subjects yet.</p>
 @endforelse
 </div>
 </div>

 {{-- Actions --}}
 <div style="display:flex;gap:12px;padding-top:20px;border-top:1px solid rgba(21,40,31,.08);">
 @if($statusLabel === 'New')
 <div style="flex:1;padding:16px;background:rgba(99,102,241,.08);border-radius:12px;border:1.5px solid rgba(99,102,241,.2);text-align:center;">
 <div style="font-size:13px;color:#3730A3;font-weight:600;">
 <i class="fa-solid fa-star" style="margin-right:6px;"></i>Awaiting Documents
 </div>
 <div style="font-size:11px;color:rgba(55,48,163,.6);margin-top:4px;">This tutor has not uploaded any documents yet. Documents are required before approval.</div>
 </div>
 @elseif($statusLabel === 'Pending')
 <form method="POST" action="{{ route('admin.tutors.approve', $profile->id) }}" style="flex:1;">
 @csrf @method('PATCH')
 <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
 <i class="fa-solid fa-check"></i> Approve Profile
 </button>
 </form>
 @endif
 @if($statusLabel === 'Approved')
 <form method="POST" action="{{ route('admin.tutors.suspend', $profile->id) }}" style="flex:1;">
 @csrf @method('PATCH')
 <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;">
 <i class="fa-solid fa-ban"></i> Suspend Profile
 </button>
 </form>
 @endif
 @if($statusLabel === 'Suspended')
 <form method="POST" action="{{ route('admin.tutors.approve', $profile->id) }}" style="flex:1;">
 @csrf @method('PATCH')
 <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
 <i class="fa-solid fa-check"></i> Re-approve Profile
 </button>
 </form>
 @endif
 </div>
 </div>
 </div>
@endforeach

<script>
// Store profile data for single email
let currentProfileId = null;
let selectedTutorIds = [];

// Get CSRF token
function getCsrfToken() {
 let token = document.querySelector('meta[name="csrf-token"]');
 if (token) return token.content;
 let input = document.querySelector('input[name="_token"]');
 if (input) return input.value;
 return '';
}

// Modal management
let currentOpenModal = null;

// Open tutor detail modal
function openTutorDetailModal(profileId, profileName) {
 const modalId = 'tutor-' + profileId;
 
 // Debug: check if modal exists
 const allModals = document.querySelectorAll('.modal-overlay');
 console.log('Total modals on page:', allModals.length);
 
 // Close currently open modal first
 if (currentOpenModal && currentOpenModal !== modalId) {
 const prevModal = document.getElementById(currentOpenModal);
 if (prevModal) {
 prevModal.style.display = 'none';
 }
 }
 
 const modal = document.getElementById(modalId);
 if (modal) {
 modal.style.display = 'flex';
 currentOpenModal = modalId;
 console.log('✓ Opened modal:', modalId);
 } else {
 console.error('✗ Modal not found:', modalId);
 }
}

// Close tutor detail modal
function closeTutorModal(modalId) {
 const modal = document.getElementById(modalId);
 if (modal) {
 modal.style.display = 'none';
 if (currentOpenModal === modalId) {
 currentOpenModal = null;
 }
 }
}

// Client-side search filter
document.getElementById('search-input')?.addEventListener('input', function() {
 const searchTerm = this.value.toLowerCase();
 const cards = document.querySelectorAll('.tutor-card');
 cards.forEach(card => {
 const name = card.dataset.tutorName;
 const email = card.dataset.tutorEmail;
 const tutorId = card.dataset.tutorId;
 const matches = name.includes(searchTerm) || email.includes(searchTerm) || tutorId.includes(searchTerm);
 card.style.display = matches ? '' : 'none';
 });
});

// Status filter (server-side via URL params)
function updateStatusFilter(status) {
 const url = new URL(window.location);
 url.searchParams.set('status', status);
 window.location.href = url.toString();
}

// Toggle all checkboxes
function toggleAllCheckboxes(checked) {
 const checkboxes = document.querySelectorAll('.tutor-checkbox');
 checkboxes.forEach(cb => {
 cb.checked = checked;
 });
 updateEmailButton();
}

// Update email button based on selected checkboxes
function updateEmailButton() {
 selectedTutorIds = Array.from(document.querySelectorAll('.tutor-checkbox:checked')).map(cb => cb.value);
 const btn = document.getElementById('email-button');
 
 if (selectedTutorIds.length > 0) {
 btn.innerHTML = `<i class="fa-solid fa-envelope"></i> Email Selected (${selectedTutorIds.length})`;
 btn.classList.remove('email-all-btn');
 btn.classList.add('email-selected-btn');
 } else {
 btn.innerHTML = `<i class="fa-solid fa-envelope"></i> Email All Tutors`;
 btn.classList.remove('email-selected-btn');
 btn.classList.add('email-all-btn');
 }
}

// Trigger bulk email (selected or all)
function triggerBulkEmail() {
 selectedTutorIds = Array.from(document.querySelectorAll('.tutor-checkbox:checked')).map(cb => cb.value);
 
 if (selectedTutorIds.length > 0) {
 // Email selected
 document.getElementById('profile-ids-input').value = selectedTutorIds.join(',');
 openEmailAllModal(selectedTutorIds.length);
 } else {
 // Email all
 const totalTutors = {{ $totalTutors }};
 openEmailAllModal(totalTutors);
 }
}

// Open single tutor email modal
function openEmailModal(profileId, name, email) {
 currentProfileId = profileId;
 document.getElementById('email-to').value = name + ' (' + email + ')';
 document.getElementById('email-subject').value = '';
 document.getElementById('email-message').value = '';
 document.getElementById('email-char-count').textContent = '0';
 document.getElementById('email-modal').classList.add('open');
 setTimeout(() => document.getElementById('email-subject').focus(), 100);
}

// Open bulk email modal
function openEmailAllModal(count) {
 document.getElementById('email-all-count').textContent = count;
 document.getElementById('email-all-plural').textContent = count === 1 ? '' : 's';
 document.getElementById('email-all-subject').value = '';
 document.getElementById('email-all-message').value = '';
 document.getElementById('email-all-char-count').textContent = '0';
 document.getElementById('email-all-modal').classList.add('open');
 setTimeout(() => document.getElementById('email-all-subject').focus(), 100);
}

// Character counter
document.getElementById('email-message')?.addEventListener('input', function() {
 document.getElementById('email-char-count').textContent = this.value.length;
});
document.getElementById('email-all-message')?.addEventListener('input', function() {
 document.getElementById('email-all-char-count').textContent = this.value.length;
});

// Submit email form
function submitEmail(event, form, type) {
 event.preventDefault();
 
 const isAll = type === 'email-all';
 const subjectInput = form.querySelector('input[name="subject"]');
 const messageInput = form.querySelector('textarea[name="message"]');
 const submitBtn = form.querySelector('button[type="submit"]');
 
 const subject = subjectInput.value.trim();
 const message = messageInput.value.trim();
 
 // Validation
 if (subject.length < 5) {
 showToast('Subject must be at least 5 characters', 'error');
 return;
 }
 if (message.length < 20) {
 showToast('Message must be at least 20 characters', 'error');
 return;
 }
 
 // Show loading state
 submitBtn.disabled = true;
 submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';
 
 // Prepare data
 let url;
 let data;
 
 if (isAll) {
 selectedTutorIds = Array.from(document.querySelectorAll('.tutor-checkbox:checked')).map(cb => cb.value);
 if (selectedTutorIds.length > 0) {
 // Email selected tutors
 url = '{{ route("admin.tutors.send-email-all") }}';
 data = {
 subject: subject,
 message: message,
 profile_ids: String(selectedTutorIds.join(',')),
 _token: getCsrfToken()
 };
 } else {
 // Email all tutors
 url = '{{ route("admin.tutors.send-email-all") }}';
 data = {
 subject: subject,
 message: message,
 _token: getCsrfToken()
 };
 }
 } else {
 url = '{{ route("admin.tutors.send-email", ":id") }}'.replace(':id', currentProfileId);
 data = {
 subject: subject,
 message: message,
 _token: getCsrfToken()
 };
 }
 
 // Send request
 fetch(url, {
 method: 'POST',
 headers: {
 'Content-Type': 'application/json',
 'X-CSRF-TOKEN': getCsrfToken()
 },
 body: JSON.stringify(data)
 })
 .then(res => {
 if (!res.ok && res.status === 422) {
 return res.json().then(err => {
 throw new Error(err.message || Object.values(err.errors || {})[0]?.[0] || 'Validation error');
 });
 }
 return res.json();
 })
 .then(data => {
 submitBtn.disabled = false;
 if (isAll) {
 submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send';
 } else {
 submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send';
 }
 
 if (data.success) {
 showToast(data.message, 'success');
 setTimeout(() => {
 if (isAll) {
 document.getElementById('email-all-modal').classList.remove('open');
 } else {
 document.getElementById('email-modal').classList.remove('open');
 }
 }, 500);
 } else {
 showToast(data.message || 'Error sending email', 'error');
 }
 })
 .catch(err => {
 submitBtn.disabled = false;
 if (isAll) {
 submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send';
 } else {
 submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send';
 }
 showToast('Error: ' + err.message, 'error');
 });
}

// Toast notification
function showToast(message, type) {
 const toast = document.createElement('div');
 toast.style.cssText = `
 position: fixed;
 bottom: 24px;
 right: 24px;
 padding: 14px 20px;
 border-radius: 12px;
 font-size: 14px;
 font-weight: 600;
 z-index: 9999;
 animation: slideIn 0.3s ease-out;
 box-shadow: 0 10px 25px rgba(0,0,0,.15);
 `;
 
 if (type === 'success') {
 toast.style.background = '#10B981';
 toast.style.color = '#fff';
 } else {
 toast.style.background = '#EF4444';
 toast.style.color = '#fff';
 }
 
 toast.textContent = message;
 document.body.appendChild(toast);
 
 setTimeout(() => {
 toast.style.animation = 'slideOut 0.3s ease-in';
 setTimeout(() => toast.remove(), 300);
 }, 4000);
}

// Reject document modal
function rejectDoc(docId, docName) {
 document.getElementById('reject-doc-name').textContent = 'Document: ' + docName;
 document.getElementById('reject-form').action = '/admin/documents/' + docId + '/reject';
 document.getElementById('reject-modal').style.display = 'flex';
}

// Document viewer modal
function openDocViewer(docPath, docName, docId = null) {
 const modal = document.getElementById('doc-viewer-modal');
 const loading = document.getElementById('doc-viewer-loading');
 const content = document.getElementById('doc-viewer-content');
 const actions = document.getElementById('doc-viewer-actions');
 
 if (!modal) {
 console.error('Document viewer modal not found');
 return;
 }
 
 // Show loading state
 modal.classList.add('loading');
 modal.style.display = 'flex';
 loading.style.display = 'flex';
 content.style.display = 'none';
 actions.style.display = 'none';
 
 // Store document ID for actions
 modal.dataset.docId = docId || '';
 
 // Update title
 document.getElementById('doc-viewer-title').textContent = docName;
 
 console.log('Opening document:', docPath);
 
 // Simulate loading delay for better UX
 setTimeout(() => {
 // Clear previous content
 content.innerHTML = '';
 
 // Check if it's an image
 if (docPath.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
 const img = document.createElement('img');
 img.src = docPath;
 img.style.cssText = 'max-width:100%;max-height:100%;object-fit:contain;border-radius:12px;';
 img.onload = () => {
 // Show content and hide loading
 loading.style.display = 'none';
 content.style.display = 'flex';
 content.style.flexDirection = 'column';
 content.style.alignItems = 'center';
 content.style.justifyContent = 'center';
 if (docId) actions.style.display = 'flex';
 modal.classList.remove('loading');
 console.log('Image loaded successfully');
 };
 img.onerror = () => {
 console.error('Error loading image:', docPath);
 content.innerHTML = '<p style="color:rgba(21,40,31,.6);">Error loading image. The file may not exist or the path may be incorrect.</p>';
 loading.style.display = 'none';
 content.style.display = 'flex';
 modal.classList.remove('loading');
 };
 content.appendChild(img);
 } else if (docPath.match(/\.pdf$/i)) {
 // Handle PDF files with PDF.js
 if (typeof pdfjsLib === 'undefined') {
 console.warn('PDF.js not loaded');
 content.innerHTML = `
 <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:20px;">
 <i class="fa-solid fa-file-pdf" style="font-size:64px;color:rgba(21,40,31,.2);"></i>
 <p style="color:rgba(21,40,31,.6);font-size:14px;text-align:center;margin:0;">
 PDF preview not available
 </p>
 <a href="${docPath}" download style="padding:10px 20px;background:#0A64BC;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:13px;">
 <i class="fa-solid fa-download"></i> Download PDF
 </a>
 </div>
 `;
 loading.style.display = 'none';
 content.style.display = 'flex';
 if (docId) actions.style.display = 'flex';
 modal.classList.remove('loading');
 } else {
 renderPdfPreview(docPath, content, loading, actions, docId, modal);
 }
 } else {
 // For other files, show file icon with download option
 content.innerHTML = `
 <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:20px;">
 <i class="fa-solid fa-file" style="font-size:64px;color:rgba(21,40,31,.2);"></i>
 <p style="color:rgba(21,40,31,.6);font-size:14px;text-align:center;margin:0;">
 File type preview not available
 </p>
 <a href="${docPath}" download style="padding:10px 20px;background:#0A64BC;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:13px;">
 <i class="fa-solid fa-download"></i> Download File
 </a>
 </div>
 `;
 
 // Show content and hide loading
 loading.style.display = 'none';
 content.style.display = 'flex';
 if (docId) actions.style.display = 'flex';
 modal.classList.remove('loading');
 }
 }, 300);
}

// Render PDF preview using PDF.js
function renderPdfPreview(pdfUrl, content, loading, actions, docId, modal) {
 if (typeof pdfjsLib === 'undefined') {
 console.warn('PDF.js library not loaded');
 content.innerHTML = `
 <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:20px;">
 <i class="fa-solid fa-file-pdf" style="font-size:64px;color:rgba(21,40,31,.2);"></i>
 <p style="color:rgba(21,40,31,.6);font-size:14px;text-align:center;margin:0;">
 PDF.js library failed to load. Download to view.
 </p>
 <a href="${pdfUrl}" download style="padding:10px 20px;background:#0A64BC;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:13px;">
 <i class="fa-solid fa-download"></i> Download PDF
 </a>
 </div>
 `;
 loading.style.display = 'none';
 content.style.display = 'flex';
 if (docId) actions.style.display = 'flex';
 modal.classList.remove('loading');
 return;
 }

 try {
 pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
 // Render first page as preview
 pdf.getPage(1).then(page => {
 const canvas = document.createElement('canvas');
 const context = canvas.getContext('2d');
 const scale = 1.5;
 const viewport = page.getViewport({ scale: scale });
 
 canvas.width = viewport.width;
 canvas.height = viewport.height;
 
 page.render({ canvasContext: context, viewport: viewport }).promise.then(() => {
 content.innerHTML = `
 <div style="display:flex;flex-direction:column;align-items:center;gap:16px;width:100%;height:100%;overflow:auto;padding:16px;">
 <canvas style="max-width:100%;max-height:70%;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.1);"></canvas>
 <p style="color:rgba(21,40,31,.6);font-size:12px;text-align:center;margin:0;width:100%;">
 Page 1 of ${pdf.numPages} (${pdf.numPages > 1 ? 'showing preview only' : ''})
 </p>
 ${pdf.numPages > 1 ? '<a href="' + pdfUrl + '" download style="padding:8px 16px;background:#0A64BC;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:12px;"><i class="fa-solid fa-download"></i> Download Full PDF</a>' : ''}
 </div>
 `;
 
 // Append the canvas with rendered PDF
 const canvases = content.querySelectorAll('canvas');
 if (canvases.length > 0) {
 canvases[0].replaceWith(canvas);
 }
 
 loading.style.display = 'none';
 content.style.display = 'flex';
 if (docId) actions.style.display = 'flex';
 modal.classList.remove('loading');
 console.log('PDF rendered successfully');
 });
 });
 }).catch(error => {
 console.error('PDF loading error:', error);
 content.innerHTML = `
 <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:20px;">
 <i class="fa-solid fa-file-pdf" style="font-size:64px;color:rgba(21,40,31,.2);"></i>
 <p style="color:rgba(21,40,31,.6);font-size:14px;text-align:center;margin:0;">
 Error loading PDF. Download to view.
 </p>
 <a href="${pdfUrl}" download style="padding:10px 20px;background:#0A64BC;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:13px;">
 <i class="fa-solid fa-download"></i> Download PDF
 </a>
 </div>
 `;
 loading.style.display = 'none';
 content.style.display = 'flex';
 if (docId) actions.style.display = 'flex';
 modal.classList.remove('loading');
 });
 } catch (err) {
 console.error('PDF.js error:', err);
 content.innerHTML = `
 <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:20px;">
 <i class="fa-solid fa-file-pdf" style="font-size:64px;color:rgba(21,40,31,.2);"></i>
 <p style="color:rgba(21,40,31,.6);font-size:14px;text-align:center;margin:0;">
 Cannot preview PDF. Download to view.
 </p>
 <a href="${pdfUrl}" download style="padding:10px 20px;background:#0A64BC;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:13px;">
 <i class="fa-solid fa-download"></i> Download PDF
 </a>
 </div>
 `;
 loading.style.display = 'none';
 content.style.display = 'flex';
 if (docId) actions.style.display = 'flex';
 modal.classList.remove('loading');
 }
}

function closeDocViewer() {
 const modal = document.getElementById('doc-viewer-modal');
 if (modal.classList.contains('loading')) return; // Prevent closing while loading
 document.getElementById('doc-viewer-modal').style.display = 'none';
}

function approveDoc() {
 const docId = document.getElementById('doc-viewer-modal').dataset.docId;
 if (!docId) return;
 
 const approveBtn = event.target.closest('button');
 approveBtn.disabled = true;
 approveBtn.innerHTML = '<i class="fa-solid fa-spinner" style="animation:spin 1s linear infinite;"></i> Approving...';
 
 // Create a hidden form and submit it
 const form = document.createElement('form');
 form.method = 'POST';
 form.action = `/admin/documents/${docId}/approve`;
 form.style.display = 'none';
 
 // Add CSRF token
 const token = document.createElement('input');
 token.type = 'hidden';
 token.name = '_token';
 token.value = document.querySelector('meta[name="csrf-token"]').content;
 form.appendChild(token);
 
 // Add method override for PATCH
 const method = document.createElement('input');
 method.type = 'hidden';
 method.name = '_method';
 method.value = 'PATCH';
 form.appendChild(method);
 
 document.body.appendChild(form);
 form.submit();
}

function rejectDocFromViewer() {
 const docId = document.getElementById('doc-viewer-modal').dataset.docId;
 if (!docId) return;
 
 closeDocViewer();
 // Get the document name from the title
 const docName = document.getElementById('doc-viewer-title').textContent;
 rejectDoc(docId, docName);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
 @keyframes slideIn {
 from { transform: translateX(400px); opacity: 0; }
 to { transform: translateX(0); opacity: 1; }
 }
 @keyframes slideOut {
 from { transform: translateX(0); opacity: 1; }
 to { transform: translateX(400px); opacity: 0; }
 }
`;
document.head.appendChild(style);
</script>

@livewireScripts
@endsection

