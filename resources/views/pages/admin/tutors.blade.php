@extends('layouts.admin')
@section('title', 'Tutor Approvals')

@section('content')
@php
use App\Models\TutorProfile;
use App\Models\User;

$showApproved = request('view') === 'approved';

if ($showApproved) {
 $pending = TutorProfile::where('is_approved', true)->with(['user','subjects','documents'])->orderByDesc('approved_at')->paginate(20);
} else {
 $pending = TutorProfile::where('is_approved',false)->with(['user','subjects','documents'])->latest()->paginate(20);
}

$approved = TutorProfile::where('is_approved',true)->count();
$totalTutors = TutorProfile::count();
$speedy = TutorProfile::where('speedy_verification_paid',true)->where('is_approved',false)->count();
@endphp

<style>
/* Doc viewer modal */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1000;display:flex;align-items:center;justify-content:center;padding:20px;}
.modal-box{background:#fff;border-radius:20px;width:100%;max-width:900px;max-height:85vh;min-height:500px;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2);will-change:transform;}
.modal-head{padding:16px 22px;border-bottom:1px solid rgba(21,40,31,.08);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;}
.modal-body{flex:1;overflow-y:auto;padding:20px;overflow-x:hidden;}
/* Tutor review panel */
.review-panel{background:#fff;border-radius:18px;border:1px solid rgba(21,40,31,.08);margin-bottom:14px;overflow:hidden;}
.review-head{padding:18px 22px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;}
.review-detail{padding:22px;border-top:1px solid rgba(21,40,31,.06);display:none;}
.review-detail.open{display:block;}
.doc-row{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:#F9FAF9;border:1px solid rgba(21,40,31,.06);margin-bottom:8px;}
.doc-thumb{width:48px;height:48px;border-radius:10px;object-fit:cover;border:1px solid rgba(21,40,31,.08);flex-shrink:0;cursor:pointer;}
.doc-thumb-placeholder{width:48px;height:48px;border-radius:10px;background:rgba(21,40,31,.06);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:20px;cursor:pointer;}
/* Email modal styles */
.email-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1500;display:none;align-items:center;justify-content:center;padding:20px;overflow-y:auto;}
.email-modal-overlay.open{display:flex;}
.email-modal-box{background:#fff;border-radius:20px;width:100%;max-width:520px;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2);}
.email-form-group{margin-bottom:18px;}
.email-form-group label{display:block;font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;}
.email-form-group input,.email-form-group textarea{width:100%;padding:12px 14px;border:1.5px solid rgba(21,40,31,.18);border-radius:12px;font-size:14px;font-family:inherit;outline:none;color:#15281F;line-height:1.5;}
.email-form-group input:focus,.email-form-group textarea:focus{border-color:#0A64BC;background:rgba(10,100,188,.02);}
.email-form-group textarea{resize:none;min-height:120px;}
.email-char-counter{font-size:12px;color:rgba(21,40,31,.5);margin-top:4px;}
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
 <h1 style="font-family:'Fraunces',serif;font-size:32px;font-weight:800;color:#15281F;margin-bottom:8px;">Tutor Approvals</h1>
 <p style="font-size:15px;color:rgba(21,40,31,.6);line-height:1.5;">Review and approve tutor applications. Manage verification documents and bring qualified tutors live on the platform.</p>
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

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;margin-bottom:32px;" class="sum-g">
 @foreach([
 ['Pending Review',$pending->total(),'#EEBE15','fa-hourglass-end','rgba(212,160,23,.08)',null],
 ['Approved',$approved,'#10B981','fa-check-circle','rgba(16,185,129,.08)','approved'],
 ['Priority (Speedy)',$speedy,'#0A64BC','fa-bolt','rgba(10,100,188,.08)',null],
 ['Total Tutors',$totalTutors,'#3B82F6','fa-users','rgba(59,130,246,.08)',null],
 ] as [$label,$val,$col,$ic,$bg,$view])
 @if($view)
 <a href="{{ route('admin.tutors') }}?view={{ $view }}" style="background:#fff;border-radius:18px;border:1.5px solid rgba(21,40,31,.08);padding:24px;display:flex;align-items:center;gap:16px;text-decoration:none;cursor:pointer;transition:all .3s cubic-bezier(.4,0,.2,1);box-shadow:0 2px 8px rgba(0,0,0,.05);position:relative;overflow:hidden;" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 12px 28px rgba(0,0,0,.12)';this.style.borderColor='{{ $col }}'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 8px rgba(0,0,0,.05)';this.style.borderColor='rgba(21,40,31,.08)'">
 <div style="width:56px;height:56px;border-radius:14px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:24px;color:{{ $col }};transition:all .3s ease;">
 <i class="fa-solid {{ $ic }}"></i>
 </div>
 <div style="flex:1;">
 <div style="font-size:12px;color:rgba(21,40,31,.5);font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">{{ $label }}</div>
 <div style="font-family:'Fraunces',serif;font-size:32px;font-weight:800;color:#15281F;line-height:1;">{{ $val }}</div>
 </div>
 <div style="flex-shrink:0;color:{{ $col }};font-size:18px;transition:all .3s ease;opacity:.6;">
 <i class="fa-solid fa-arrow-right"></i>
 </div>
 </a>
 @else
 <div style="background:#fff;border-radius:18px;border:1.5px solid rgba(21,40,31,.08);padding:24px;display:flex;align-items:center;gap:16px;box-shadow:0 2px 8px rgba(0,0,0,.05);">
 <div style="width:56px;height:56px;border-radius:14px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:24px;color:{{ $col }};">
 <i class="fa-solid {{ $ic }}"></i>
 </div>
 <div style="flex:1;">
 <div style="font-size:12px;color:rgba(21,40,31,.5);font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">{{ $label }}</div>
 <div style="font-family:'Fraunces',serif;font-size:32px;font-weight:800;color:#15281F;line-height:1;">{{ $val }}</div>
 </div>
 </div>
 @endif
 @endforeach
 </div>

{{-- Pending applications list --}}
@if($pending->isEmpty())
<div style="text-align:center;padding:64px 40px;background:#fff;border-radius:18px;border:1.5px solid rgba(21,40,31,.08);">
 <div style="display:flex;align-items:center;justify-content:center;width:80px;height:80px;background:rgba(10,100,188,.08);border-radius:16px;margin:0 auto 24px;">
 <i class="fa-solid fa-inbox" style="font-size:36px;color:#0A64BC;"></i>
 </div>
 <h3 style="font-family:'Fraunces',serif;font-size:22px;font-weight:800;color:#15281F;margin-bottom:8px;">
 @if($showApproved)
 No approved tutors yet.
 @else
 All caught up!
 @endif
 </h3>
 <p style="color:rgba(21,40,31,.55);font-size:15px;line-height:1.6;margin-bottom:24px;max-width:500px;margin-left:auto;margin-right:auto;">
 @if($showApproved)
 No tutors have been approved yet. Review pending applications to approve qualified tutors.
 @else
 No pending tutor applications right now. You're all set!
 @endif
 </p>
 @if($showApproved)
 <a href="{{ route('admin.tutors') }}" style="display:inline-flex;align-items:center;gap:8px;color:#0A64BC;text-decoration:none;font-weight:700;font-size:14px;padding:10px 20px;border:2px solid rgba(10,100,188,.2);border-radius:10px;transition:all .2s;">
 <i class="fa-solid fa-arrow-left"></i> Back to Pending
 </a>
 @endif
</div>
@else
<div style="margin-bottom:28px;">
 <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:20px;">
 <div>
 <h2 style="font-family:'Fraunces',serif;font-size:20px;font-weight:800;color:#15281F;margin-bottom:6px;">
 @if($showApproved)
 Approved Tutors
 @else
 Pending Applications
 @endif
 </h2>
 <p style="font-size:13px;color:rgba(21,40,31,.55);font-weight:500;">
 @if($showApproved)
 {{ $pending->total() }} approved tutor{{ $pending->total() !== 1 ? 's' : '' }} on the platform
 @else
 {{ $pending->total() }} application{{ $pending->total()!==1?'s':'' }} awaiting review
 @endif
 </p>
 </div>
 <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
 @if(!$showApproved)
 <button onclick="openEmailAllModal()" class="btn btn-outline" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;padding:10px 16px;border-radius:9px;color:#0A64BC;border-color:rgba(10,100,188,.3);">
 <i class="fa-solid fa-envelope"></i> Email All Tutors
 </button>
 @endif
 @if($showApproved)
 <a href="{{ route('admin.tutors') }}" style="display:inline-flex;align-items:center;gap:6px;color:#0A64BC;text-decoration:none;font-weight:700;font-size:13px;padding:10px 16px;border:1.5px solid rgba(10,100,188,.3);border-radius:9px;transition:all .2s;background:rgba(10,100,188,.02);" onmouseover="this.style.background='rgba(10,100,188,.08)';this.style.borderColor='rgba(10,100,188,.5)'" onmouseout="this.style.background='rgba(10,100,188,.02)';this.style.borderColor='rgba(10,100,188,.3)'">
 <i class="fa-solid fa-arrow-left"></i> Back to Pending
 </a>
 @else
 <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(232,82,42,.1);color:#E8522A;padding:8px 14px;border-radius:8px;font-size:12px;font-weight:700;">
 <i class="fa-solid fa-bolt"></i> Priority apps marked with badge
 </span>
 @endif
 </div>
 </div>

{{-- Card Grid --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(400px,1fr));gap:20px;">
 @foreach($pending as $profile)
 @php
 $docs = $profile->documents;
 $docCount = $docs->count();
 $approvedDocs = $docs->where('status', 'approved')->count();
 $pendingDocs = $docs->where('status', 'pending')->count();
 $rejectedDocs = $docs->where('status', 'rejected')->count();
 $isSpeedy = $profile->speedy_verification_paid;

 // Approval gate: 4 required docs approved + 1-2 subjects
 $requiredDocTypes = ['national_id', 'tsc_certificate', 'degree_certificate', 'passport_photo'];
 $approvedDocTypes = $docs->where('status', 'approved')->pluck('type')->toArray();
 $allRequiredApproved = count(array_diff($requiredDocTypes, $approvedDocTypes)) === 0;
 $subjectCnt = $profile->subjects->count();
 $canApprove = $allRequiredApproved && $subjectCnt >= 1 && $subjectCnt <= 2 && !$profile->is_approved;

 // Tooltip explaining why the button is locked
 $approveBlockReason = '';
 if (!$allRequiredApproved) {
 $missing = array_diff($requiredDocTypes, $approvedDocTypes);
 $approveBlockReason = 'Missing approved docs: ' . implode(', ', array_map(fn($t) => ucwords(str_replace('_', ' ', $t)), $missing));
 } elseif ($subjectCnt === 0) {
 $approveBlockReason = 'Tutor has not selected any subjects yet';
 } elseif ($subjectCnt > 2) {
 $approveBlockReason = 'Tutor has ' . $subjectCnt . ' subjects (max 2)';
 }
 @endphp
 
 <div class="card" style="overflow:hidden;transition:all .2s;position:relative;box-shadow:0 2px 8px rgba(0,0,0,.04);border-radius:18px;" onmouseover="this.style.transform='translateY(-8px)';this.style.boxShadow='0 16px 40px rgba(0,0,0,.12)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 8px rgba(0,0,0,.04)'">
 {{-- Speedy Badge --}}
 @if($isSpeedy)
 <div style="position:absolute;top:12px;right:12px;z-index:10;">
 <span class="badge" style="background:linear-gradient(135deg,#FEF9C3,#FDE68A);color:#854D0E;border:1px solid #FBBF24;font-weight:700;font-size:12px;padding:5px 12px;box-shadow:0 2px 4px rgba(0,0,0,.1);">
 <i class="fa-solid fa-bolt"></i> SPEEDY
 </span>
 </div>
 @endif
 
 {{-- Card Header --}}
 <div style="padding:22px 24px;border-bottom:1px solid var(--hairline);">
 <div style="display:flex;align-items:flex-start;gap:14px;">
 <div class="avatar" style="width:52px;height:52px;font-size:20px;flex-shrink:0;background:#{{ substr(md5($profile->user->email),0,6) }};">
 {{ substr($profile->user->name,0,1) }}
 </div>
 <div style="flex:1;min-width:0;">
 <h3 style="font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:var(--ink);margin-bottom:4px;">
 {{ $profile->user->name }}
 </h3>
 <div style="font-size:12px;color:var(--muted);margin-bottom:6px;">
 <i class="fa-solid fa-envelope"></i> {{ $profile->user->email }}
 </div>
 <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
 <span class="badge badge-amber">
 <i class="fa-solid fa-clock"></i> Pending
 </span>
 <span style="font-size:12px;color:var(--muted);">
 Applied {{ $profile->created_at->diffForHumans() }}
 </span>
 </div>
 </div>
 </div>
 </div>
 
 {{-- Quick Stats --}}
 <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:var(--hairline);border-bottom:1px solid var(--hairline);">
 <div style="padding:14px;text-align:center;background:#fff;">
 <div style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:#EEBE15;">{{$profile->subjects->count()}}</div>
 <div style="font-size:11px;color:var(--muted);margin-top:2px;">Subjects</div>
 </div>
 <div style="padding:14px;text-align:center;background:#fff;">
 <div style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:#{{$allRequiredApproved ? '10B981' : 'EEBE15'}};">{{$approvedDocs}}/4</div>
 <div style="font-size:11px;color:var(--muted);margin-top:2px;">Docs OK</div>
 </div>
 <div style="padding:14px;text-align:center;background:{{$rejectedDocs > 0 ? 'rgba(239,68,68,.06)' : '#fff'}};">
 <div style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:{{$rejectedDocs > 0 ? '#EF4444' : 'var(--muted)'}};">{{$rejectedDocs}}</div>
 <div style="font-size:11px;color:var(--muted);margin-top:2px;">Rejected</div>
 </div>
 </div>

 {{-- Documents Status --}}
 <div style="padding:16px 20px;border-bottom:1px solid var(--hairline);background:{{$allRequiredApproved ? 'rgba(16,185,129,.05)' : ($rejectedDocs > 0 ? 'rgba(239,68,68,.05)' : 'rgba(251,191,36,.05)')}};">
 <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
 <span style="font-size:12px;font-weight:700;color:var(--ink);text-transform:uppercase;letter-spacing:.05em;">Documents</span>
 <span class="badge {{$allRequiredApproved ? 'badge-green' : ($pendingDocs > 0 ? 'badge-amber' : 'badge-gray')}}" style="font-size:10px;">{{$approvedDocs}}/{{max(4, $docCount)}} Approved</span>
 </div>
 @if($docCount === 0)
 <div style="font-size:12px;color:var(--muted);text-align:center;padding:8px 0;"><i class="fa-solid fa-folder-open"></i> No documents uploaded yet</div>
 @else
 <div style="display:flex;gap:6px;flex-wrap:wrap;">
 @foreach($docs as $doc)
 @php
 $docLabels = ['national_id'=>'National ID','tsc_certificate'=>'TSC Cert','certificate_of_good_conduct'=>'Good Conduct','degree_certificate'=>'Academic Cert','passport_photo'=>'Passport Photo','other'=>'Other'];
 $statusColors = ['pending'=>'badge-amber','approved'=>'badge-green','rejected'=>'badge-red'];
 @endphp
 <span class="badge {{$statusColors[$doc->status] ?? 'badge-gray'}}" style="font-size:10px;">{{$docLabels[$doc->type] ?? ucfirst($doc->type)}}</span>
 @endforeach
 </div>
 @endif
 </div>

 {{-- Actions --}}
 <div style="padding:16px 20px;display:flex;gap:8px;flex-wrap:wrap;">
 @if($docCount === 0)
 <div style="flex:1;text-align:center;padding:10px;font-size:12px;color:var(--muted);">Waiting for document upload...</div>
 @else
 <a href="{{route('admin.tutors.subjects', $profile)}}" class="btn btn-outline" style="flex:1;font-size:12px;justify-content:center;padding:10px 14px;border-radius:9px;text-decoration:none;">
 <i class="fa-solid fa-book-open"></i> Subjects ({{$profile->subjects->count()}})
 </a>
 <button onclick="document.getElementById('tutor-{{$profile->id}}').style.display='flex'" class="btn btn-outline" style="flex:1;font-size:12px;padding:10px 14px;border-radius:9px;">
 <i class="fa-solid fa-eye"></i> Review
 </button>
 <button onclick="openEmailModal('{{$profile->id}}', '{{addslashes($profile->user->name)}}', '{{$profile->user->email}}')" class="btn btn-outline" style="flex:1;font-size:12px;padding:10px 14px;border-radius:9px;color:#0A64BC;">
 <i class="fa-solid fa-envelope"></i> Email
 </button>
 @if($canApprove)
 <form method="POST" action="{{route('admin.tutors.approve', $profile->id)}}" style="flex:1;" onsubmit="event.preventDefault(); confirmApproval{{$profile->id}}(this, '{{$profile->user->name}}')">
 @csrf @method('PATCH')
 <button type="submit" class="btn btn-primary" style="width:100%;font-size:12px;padding:10px 14px;border-radius:9px;">
 <i class="fa-solid fa-circle-check"></i> Approve
 </button>
 </form>
 <script>
 function confirmApproval{{$profile->id}}(form, name) {
 showConfirm('Approve Tutor?', 'Approve ' + name + ' and make them live?', 'Yes, Approve', 'Cancel').then((result) => {
 if (result.isConfirmed) form.submit();
 });
 }
 </script>
 @else
 <button disabled class="btn" style="flex:1;background:rgba(0,0,0,.05);color:var(--muted);cursor:not-allowed;font-size:12px;padding:10px 14px;border-radius:9px;" title="{{$approveBlockReason}}">
 <i class="fa-solid fa-lock"></i> Approve
 </button>
 @endif
 @endif
 </div>
 </div>
 
 {{-- Full Review Modal (Keep existing modal) --}}
 <div id="tutor-{{ $profile->id }}" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1000;align-items:center;justify-content:center;padding:20px;overflow-y:auto;" onclick="if(event.target===this) this.style.display='none'">
 <div class="modal-box" onclick="event.stopPropagation()" style="max-width:900px;">
 <div class="modal-head">
 <h3 style="font-family:'Fraunces',serif;font-size:18px;font-weight:700;">Review: {{ $profile->user->name }}</h3>
 <button onclick="document.getElementById('tutor-{{ $profile->id }}').style.display='none'" style="width:32px;height:32px;border-radius:8px;border:none;background:rgba(0,0,0,.05);cursor:pointer;display:flex;align-items:center;justify-content:center;">
 <i class="fa-solid fa-circle-xmark"></i>
 </button>
 </div>
 <div class="modal-body">
 {{-- Keep existing detailed review content here --}}
 <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:20px;">
 <div>
 <div style="font-size:12px;color:var(--muted);margin-bottom:4px;font-weight:600;">Email</div>
 <div style="font-size:14px;">{{ $profile->user->email }}</div>
 </div>
 <div>
 <div style="font-size:12px;color:var(--muted);margin-bottom:4px;font-weight:600;">Phone</div>
 <div style="font-size:14px;">{{ $profile->phone ?? '-' }}</div>
 </div>
 <div>
 </div>
 
 <div style="margin-bottom:20px;">
 <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
 <div style="font-size:12px;color:var(--muted);font-weight:600;">Subjects ({{ $profile->subjects->count() }})</div>
 <a href="{{ route('admin.tutors.subjects', $profile) }}" class="btn btn-outline btn-sm" style="text-decoration:none;">
 <i class="fa-solid fa-pencil"></i> Manage
 </a>
 </div>
 <div style="display:flex;gap:6px;flex-wrap:wrap;">
 @forelse($profile->subjects->take(10) as $subj)
 <span class="badge badge-blue">{{ $subj->name }}</span>
 @empty
 <div style="background:rgba(239,68,68,.05);border:1px solid rgba(239,68,68,.15);border-radius:10px;padding:12px;width:100%;">
 <p style="font-size:13px;color:#991B1B;margin:0;">
 <i class="fa-solid fa-triangle-exclamation"></i> No subjects selected. Click "Manage" to add subjects.
 </p>
 </div>
 @endforelse
 @if($profile->subjects->count() > 10)
 <span class="badge badge-gray">+{{ $profile->subjects->count() - 10 }} more</span>
 @endif
 </div>
 </div>
 
 <div style="margin-bottom:20px;">
 <div style="font-size:12px;color:var(--muted);margin-bottom:8px;font-weight:600;">Bio</div>
 <div style="font-size:14px;line-height:1.6;">{{ $profile->bio ?? 'No bio provided' }}</div>
 </div>
 
 {{-- Documents for approval --}}
 @if($docs->isNotEmpty())
 <div style="border-top:1px solid var(--hairline);padding-top:20px;">
 <h4 style="font-size:14px;font-weight:700;margin-bottom:12px;">Uploaded Documents</h4>
 @foreach($docs as $doc)
 <div class="doc-row">
 @php
 $isPdf = str_ends_with(strtolower($doc->file_path), '.pdf');
 $docLabel = [
 'national_id' => 'National ID / Passport',
 'tsc_certificate' => 'TSC Certificate',
 'certificate_of_good_conduct' => 'Certificate of Good Conduct',
 'degree_certificate' => 'Academic Certificate',
 'passport_photo' => 'Passport Photo',
 'other' => 'Other',
 ][$doc->type] ?? ucfirst($doc->type);
 @endphp
 
 @if($isPdf)
 <div class="doc-thumb-placeholder" style="cursor:pointer;" onclick="viewDocModal('{{ route('tutor.documents.view', $doc->id) }}', true, '{{ $docLabel }}')">
 <i class="fa-solid fa-file-lines"></i>
 </div>
 @else
 <img src="{{ route('tutor.documents.view', $doc->id) }}" class="doc-thumb" style="cursor:pointer;"
 onclick="viewDocModal('{{ route('tutor.documents.view', $doc->id) }}', false, '{{ $docLabel }}')"
 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
 <div class="doc-thumb-placeholder" style="display:none;cursor:pointer;" onclick="viewDocModal('{{ route('tutor.documents.view', $doc->id) }}', false, '{{ $docLabel }}')">
 <i class="fa-solid fa-file-lines"></i>
 </div>
 @endif
 
 <div style="flex:1;min-width:0;">
 <div style="font-weight:600;font-size:13px;margin-bottom:2px;">{{ $docLabel }}</div>
 <div style="font-size:11px;color:var(--muted);">{{ $doc->original_name }}</div>
 </div>
 
 @php $sc = ['pending'=>'badge-amber','approved'=>'badge-green','rejected'=>'badge-red'][$doc->status]??'badge-gray'; @endphp
 <span class="badge {{ $sc }}">{{ ucfirst($doc->status) }}</span>
 
 @if($doc->status === 'pending')
 <div style="display:flex;gap:6px;">
 <form method="POST" action="{{ route('admin.documents.approve', $doc->id) }}" style="display:inline;">
 @csrf @method('PATCH')
 <button type="submit" class="btn btn-success btn-sm">
 <i class="fa-solid fa-circle-check"></i>
 </button>
 </form>
 <button type="button" onclick="rejectDoc({{ $doc->id }}, '{{ addslashes($docLabel) }}')" class="btn btn-danger btn-sm">
 <i class="fa-solid fa-circle-xmark"></i>
 </button>
 </div>
 @endif
 </div>
 @endforeach
 </div>
 @endif
 
 {{-- Final Actions --}}
 <div style="display:flex;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--hairline);flex-wrap:wrap;">
 <a href="{{ route('admin.tutors.subjects', $profile) }}" class="btn btn-outline" style="flex:1;justify-content:center;text-decoration:none;">
 <i class="fa-solid fa-book-open"></i> Manage Subjects ({{ $profile->subjects->count() }})
 </a>
 <form method="POST" action="{{ route('admin.tutors.suspend', $profile->id) }}" style="flex:1;" onsubmit="event.preventDefault(); confirmSuspend{{ $profile->id }}(this)">
 @csrf @method('PATCH')
 <button type="submit" class="btn btn-danger" style="width:100%;">
 <i class="fa-solid fa-ban"></i> Suspend
 </button>
 </form>
 <script>
 function confirmSuspend{{ $profile->id }}(form) {
 showConfirm('Suspend Tutor?', 'Are you sure you want to suspend this tutor?', 'Yes, Suspend', 'Cancel').then((result) => {
 if (result.isConfirmed) form.submit();
 });
 }
 </script>
 <form method="POST" action="{{ route('admin.tutors.approve', $profile->id) }}" style="flex:1;" onsubmit="event.preventDefault(); confirmFinalApproval{{ $profile->id }}(this)">
 @csrf @method('PATCH')
 <button type="submit" class="btn btn-primary" style="width:100%;" {{ !$canApprove ? 'disabled title="'.$approveBlockReason.'"' : '' }}>
 <i class="fa-solid fa-circle-check"></i> {{ $canApprove ? 'Approve & Go Live' : 'Approve ('.$approvedDocs.'/4 required docs  '.$subjectCnt.' subjects)' }}
 </button>
 </form>
 <script>
 function confirmFinalApproval{{ $profile->id }}(form) {
 showConfirm('Approve Tutor?', 'Approve and make this tutor live?', 'Yes, Approve', 'Cancel').then((result) => {
 if (result.isConfirmed) form.submit();
 });
 }
 </script>
 </div>
 </div>
 </div>
 </div>
 @endforeach
</div>
</div>

@endif

{{-- Pagination --}}
@if($pending->hasPages())
<div style="margin-top:28px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
 <div style="font-size:13px;color:var(--muted);">
 Showing {{ $pending->firstItem() }} to {{ $pending->lastItem() }} of {{ $pending->total() }} results
 </div>
 <div style="display:flex;align-items:center;gap:4px;">
 @if($pending->onFirstPage())
 <span style="padding:8px 14px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--muted);cursor:not-allowed;background:#fafafa;"><i class="fa-solid fa-chevron-left"></i></span>
 @else
 <a href="{{ $pending->previousPageUrl() }}" style="padding:8px 14px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--ink);text-decoration:none;background:#fff;transition:all .2s;" onmouseover="this.style.borderColor='#0A64BC'" onmouseout="this.style.borderColor='var(--hairline)'"><i class="fa-solid fa-chevron-left"></i></a>
 @endif
 @foreach($pending->getUrlRange(1, $pending->lastPage()) as $page => $url)
 @if($page == $pending->currentPage())
 <span style="padding:8px 14px;border-radius:8px;border:1px solid #0A64BC;font-size:13px;font-weight:700;color:#fff;background:#0A64BC;box-shadow:0 2px 8px rgba(10,100,188,.3);">{{ $page }}</span>
 @else
 <a href="{{ $url }}" style="padding:8px 14px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--ink);text-decoration:none;background:#fff;transition:all .2s;" onmouseover="this.style.borderColor='#0A64BC'" onmouseout="this.style.borderColor='var(--hairline)'">{{ $page }}</a>
 @endif
 @endforeach
 @if($pending->hasMorePages())
 <a href="{{ $pending->nextPageUrl() }}" style="padding:8px 14px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--ink);text-decoration:none;background:#fff;transition:all .2s;" onmouseover="this.style.borderColor='#0A64BC'" onmouseout="this.style.borderColor='var(--hairline)'"><i class="fa-solid fa-chevron-right"></i></a>
 @else
 <span style="padding:8px 14px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--muted);cursor:not-allowed;background:#fafafa;"><i class="fa-solid fa-chevron-right"></i></span>
 @endif
 </div>
</div>
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

<script>
function rejectDoc(docId, docName) {
 document.getElementById('reject-doc-name').textContent = 'Document: ' + docName;
 document.getElementById('reject-form').action = '/admin/documents/' + docId + '/reject';
 document.getElementById('reject-modal').style.display = 'flex';
}
function rejectDocModal(docId, docName) {
 rejectDoc(docId, docName);
}
</script>

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
 <h3 style="font-family:'Fraunces',serif;font-size:18px;font-weight:700;">Email All Tutors</h3>
 <button onclick="document.getElementById('email-all-modal').classList.remove('open')" style="width:32px;height:32px;border-radius:8px;border:none;background:rgba(0,0,0,.05);cursor:pointer;display:flex;align-items:center;justify-content:center;">
 <i class="fa-solid fa-circle-xmark"></i>
 </button>
 </div>
 <div style="padding:24px;overflow-y:auto;">
 <form id="email-all-form" onsubmit="submitEmail(event, this, 'email-all')">
 <p style="font-size:13px;color:rgba(21,40,31,.65);margin-bottom:20px;">This message will be sent to <strong id="email-all-count">0</strong> tutors.</p>
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

<script>
// Store profile data for single email
let currentProfileId = null;

// Get CSRF token
function getCsrfToken() {
 let token = document.querySelector('meta[name="csrf-token"]');
 if (token) return token.content;
 let input = document.querySelector('input[name="_token"]');
 if (input) return input.value;
 return '';
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

// Open all tutors email modal
function openEmailAllModal() {
 @php
 $totalTutors = \App\Models\TutorProfile::count();
 @endphp
 document.getElementById('email-all-count').textContent = '{{ $totalTutors }}';
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
 const url = isAll 
 ? '{{ route("admin.tutors.send-email-all") }}'
 : '{{ route("admin.tutors.send-email", ":id") }}'.replace(':id', currentProfileId);
 
 const data = {
 subject: subject,
 message: message,
 _token: getCsrfToken()
 };
 
 // Send request
 fetch(url, {
 method: 'POST',
 headers: {
 'Content-Type': 'application/json',
 'X-CSRF-TOKEN': getCsrfToken()
 },
 body: JSON.stringify(data)
 })
 .then(res => res.json())
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

