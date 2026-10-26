@extends('layouts.admin')
@section('title', 'Tutor Approvals')

@section('content')
@php
use App\Models\TutorProfile;
use App\Models\User;
$pending   = TutorProfile::where('is_approved',false)->with(['user','subjects','documents'])->latest()->paginate(20);
$approved  = TutorProfile::where('is_approved',true)->count();
$totalTutors = TutorProfile::count();
$speedy    = TutorProfile::where('speedy_verification_paid',true)->where('is_approved',false)->count();
@endphp

<style>
/* Doc viewer modal */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1000;display:flex;align-items:center;justify-content:center;padding:20px;}
.modal-box{background:#fff;border-radius:20px;width:100%;max-width:760px;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;}
.modal-head{padding:16px 22px;border-bottom:1px solid rgba(21,40,31,.08);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;}
.modal-body{flex:1;overflow-y:auto;padding:20px;}
/* Tutor review panel */
.review-panel{background:#fff;border-radius:18px;border:1px solid rgba(21,40,31,.08);margin-bottom:14px;overflow:hidden;}
.review-head{padding:18px 22px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;}
.review-detail{padding:22px;border-top:1px solid rgba(21,40,31,.06);display:none;}
.review-detail.open{display:block;}
.doc-row{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:#F9FAF9;border:1px solid rgba(21,40,31,.06);margin-bottom:8px;}
.doc-thumb{width:48px;height:48px;border-radius:10px;object-fit:cover;border:1px solid rgba(21,40,31,.08);flex-shrink:0;cursor:pointer;}
.doc-thumb-placeholder{width:48px;height:48px;border-radius:10px;background:rgba(21,40,31,.06);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:20px;cursor:pointer;}
</style>

<div class="page-header">
    <div>
        <h1>Tutor Approvals</h1>
        <p>Review and approve tutor applications</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:22px;" class="sum-g">
                @foreach([
                    ['Pending Review',$pending->total(),'#EEBE15','fa-clock','rgba(212,160,23,.1)'],
                    ['Approved',$approved,'#10B981','fa-circle-check','rgba(16,185,129,.1)'],
                    ['Priority (Speedy)',$speedy,'#0A64BC','fa-bolt','rgba(10,100,188,.1)'],
                    ['Total Tutors',$totalTutors,'#3B82F6','fa-users','rgba(59,130,246,.1)'],
                ] as [$label,$val,$col,$ic,$bg])
                <div style="background:#fff;border-radius:14px;border:1px solid rgba(21,40,31,.07);padding:16px;display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;border-radius:11px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa-solid {{ $ic }}" style="color:{{ $col }};font-size:15px;"></i>
                    </div>
                    <div>
                        <div style="font-size:11px;color:rgba(21,40,31,.5);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">{{ $label }}</div>
                        <div style="font-family:'Fraunces',serif;font-size:24px;font-weight:700;color:#15281F;line-height:1.1;">{{ $val }}</div>
                    </div>
                </div>
                @endforeach
            </div>
{{-- Pending applications list --}}
@if($pending->isEmpty())
<div class="card card-p" style="text-align:center;padding:56px;">
    <i class="fa-solid fa-party-horn" style="font-size:40px;color:rgba(21,40,31,.15);margin-bottom:12px;display:block;"></i>
    <h3 style="font-family:'Fraunces',serif;font-size:20px;color:#15281F;margin-bottom:6px;">All caught up!</h3>
    <p style="color:rgba(21,40,31,.5);font-size:14px;">No pending tutor applications right now.</p>
</div>
@else
<div style="margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
    <p style="font-size:14px;color:rgba(21,40,31,.55);font-weight:600;">{{ $pending->total() }} application{{ $pending->total()!==1?'s':'' }} awaiting review</p>
    <p style="font-size:12px;color:rgba(21,40,31,.4);display:flex;align-items:center;gap:6px;">
        <i class="fa-solid fa-bolt" style="color:#EEBE15;"></i> Priority applications are marked with a gold badge
    </p>
</div>

{{-- Card Grid --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(380px,1fr));gap:16px;">
    @foreach($pending as $profile)
    @php
    $docs = $profile->documents;
    $docCount = $docs->count();
    $approvedDocs = $docs->where('status', 'approved')->count();
    $pendingDocs = $docs->where('status', 'pending')->count();
    $rejectedDocs = $docs->where('status', 'rejected')->count();
    $isSpeedy = $profile->speedy_verification_paid;
    @endphp
    
    <div class="card" style="overflow:hidden;transition:all .2s;position:relative;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 16px rgba(0,0,0,.08)'" onmouseout="this.style.transform='none';this.style.boxShadow='none'">
        {{-- Speedy Badge --}}
        @if($isSpeedy)
        <div style="position:absolute;top:12px;right:12px;z-index:10;">
            <span class="badge" style="background:linear-gradient(135deg,#FEF9C3,#FDE68A);color:#854D0E;border:1px solid #FBBF24;font-weight:700;font-size:11px;padding:4px 10px;box-shadow:0 2px 4px rgba(0,0,0,.1);">
                <i class="fa-solid fa-bolt"></i> SPEEDY
            </span>
        </div>
        @endif
        
        {{-- Card Header --}}
        <div style="padding:20px 24px;border-bottom:1px solid var(--hairline);">
            <div style="display:flex;align-items:flex-start;gap:14px;">
                <div class="avatar" style="width:52px;height:52px;font-size:20px;flex-shrink:0;background:#{{ substr(md5($profile->user->email),0,6) }};">
                    {{ substr($profile->user->name,0,1) }}
                </div>
                <div style="flex:1;min-width:0;">
                    <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);margin-bottom:4px;">
                        {{ $profile->user->name }}
                    </h3>
                    <div style="font-size:12px;color:var(--muted);margin-bottom:6px;">
                        <i class="fa-solid fa-envelope fa-xs"></i> {{ $profile->user->email }}
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <span class="badge badge-amber">
                            <i class="fa-solid fa-clock"></i> Pending
                        </span>
                        <span style="font-size:11px;color:var(--muted);">
                            Applied {{ $profile->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Quick Stats --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);border-bottom:1px solid var(--hairline);">
            <div style="padding:14px;text-align:center;border-right:1px solid var(--hairline);">
                <div style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:#0A64BC;">
                    {{ $profile->hourly_rate ? 'KSh '.number_format($profile->hourly_rate) : '—' }}
                </div>
                <div style="font-size:11px;color:var(--muted);margin-top:2px;">Hourly Rate</div>
            </div>
            <div style="padding:14px;text-align:center;border-right:1px solid var(--hairline);">
                <div style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:#10B981;">
                    {{ $profile->years_experience ?? 0 }}yr
                </div>
                <div style="font-size:11px;color:var(--muted);margin-top:2px;">Experience</div>
            </div>
            <div style="padding:14px;text-align:center;">
                <div style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:#EEBE15;">
                    {{ $profile->subjects->count() }}
                </div>
                <div style="font-size:11px;color:var(--muted);margin-top:2px;">Subjects</div>
            </div>
        </div>
        
        {{-- Documents Status --}}
        <div style="padding:16px 20px;border-bottom:1px solid var(--hairline);background:{{ $approvedDocs === 3 ? 'rgba(16,185,129,.05)' : ($rejectedDocs > 0 ? 'rgba(239,68,68,.05)' : 'rgba(251,191,36,.05)') }};">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                <span style="font-size:12px;font-weight:700;color:var(--ink);text-transform:uppercase;letter-spacing:.05em;">Documents</span>
                <span class="badge {{ $approvedDocs === 3 ? 'badge-green' : ($pendingDocs > 0 ? 'badge-amber' : 'badge-gray') }}" style="font-size:10px;">
                    {{ $approvedDocs }}/{{ max(3, $docCount) }} Approved
                </span>
            </div>
            
            @if($docCount === 0)
            <div style="font-size:12px;color:var(--muted);text-align:center;padding:8px;">
                <i class="fa-solid fa-inbox"></i> No documents uploaded yet
            </div>
            @else
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                @foreach($docs as $doc)
                @php
                    $docLabels = ['id_document'=>'ID','certificate'=>'Certificate','proof_of_qualification'=>'Qualification'];
                    $statusColors = ['pending'=>'badge-amber','approved'=>'badge-green','rejected'=>'badge-red'];
                @endphp
                <span class="badge {{ $statusColors[$doc->status] ?? 'badge-gray' }}" style="font-size:10px;">
                    {{ $docLabels[$doc->document_type] ?? ucfirst($doc->document_type) }}
                </span>
                @endforeach
            </div>
            @endif
        </div>
        
        {{-- Actions --}}
        <div style="padding:16px 20px;display:flex;gap:8px;">
            @if($docCount === 0)
            <div style="flex:1;text-align:center;padding:8px;font-size:12px;color:var(--muted);">
                Waiting for document upload...
            </div>
            @else
            <button onclick="document.getElementById('tutor-{{ $profile->id }}').style.display='block'" class="btn btn-outline" style="flex:1;font-size:13px;">
                <i class="fa-solid fa-eye"></i> Review
            </button>
            @if($approvedDocs === 3)
            <form method="POST" action="{{ route('admin.tutors.approve', $profile->id) }}" style="flex:1;">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-primary" style="width:100%;font-size:13px;" onclick="return confirm('Approve {{ $profile->user->name }} and make them live?')">
                    <i class="fa-solid fa-circle-check"></i> Approve
                </button>
            </form>
            @else
            <button disabled class="btn" style="flex:1;background:rgba(0,0,0,.05);color:var(--muted);cursor:not-allowed;font-size:13px;">
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
                    <i class="fa-solid fa-xmark"></i>
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
                        <div style="font-size:14px;">{{ $profile->phone ?? '—' }}</div>
                    </div>
                    <div>
                        <div style="font-size:12px;color:var(--muted);margin-bottom:4px;font-weight:600;">Hourly Rate</div>
                        <div style="font-size:14px;">{{ $profile->hourly_rate ? 'KSh '.number_format($profile->hourly_rate) : '—' }}</div>
                    </div>
                    <div>
                        <div style="font-size:12px;color:var(--muted);margin-bottom:4px;font-weight:600;">Experience</div>
                        <div style="font-size:14px;">{{ $profile->years_experience ?? 0 }} years</div>
                    </div>
                </div>
                
                <div style="margin-bottom:20px;">
                    <div style="font-size:12px;color:var(--muted);margin-bottom:8px;font-weight:600;">Subjects ({{ $profile->subjects->count() }})</div>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                        @forelse($profile->subjects as $subj)
                        <span class="badge badge-blue">{{ $subj->name }}</span>
                        @empty
                        <span style="font-size:13px;color:var(--muted);">No subjects selected</span>
                        @endforelse
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
                            $docLabel = ['id_document'=>'ID Document','certificate'=>'Certificate','proof_of_qualification'=>'Proof of Qualification'][$doc->document_type] ?? ucfirst($doc->document_type);
                        @endphp
                        
                        @if($isPdf)
                        <div class="doc-thumb-placeholder" onclick="window.open('{{ route('tutor.documents.view', $doc->id) }}', '_blank')">
                            <i class="fa-solid fa-file-pdf" style="color:#dc2626;"></i>
                        </div>
                        @else
                        <img src="{{ route('tutor.documents.view', $doc->id) }}" class="doc-thumb" onclick="window.open('{{ route('tutor.documents.view', $doc->id) }}', '_blank')" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="doc-thumb-placeholder" style="display:none;" onclick="window.open('{{ route('tutor.documents.view', $doc->id) }}', '_blank')">
                            <i class="fa-solid fa-file"></i>
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
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>
                            <button type="button" onclick="rejectDoc({{ $doc->id }}, '{{ addslashes($docLabel) }}')" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
                
                {{-- Final Actions --}}
                <div style="display:flex;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--hairline);">
                    <form method="POST" action="{{ route('admin.tutors.suspend', $profile->id) }}" style="flex:1;">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-danger" style="width:100%;" onclick="return confirm('Suspend this tutor?')">
                            <i class="fa-solid fa-ban"></i> Suspend
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.tutors.approve', $profile->id) }}" style="flex:2;">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-primary" style="width:100%;" {{ $approvedDocs < 3 ? 'disabled' : '' }} onclick="return confirm('Approve and make this tutor live?')">
                            <i class="fa-solid fa-circle-check"></i> {{ $approvedDocs === 3 ? 'Approve & Go Live' : 'Approve ('.$approvedDocs.'/3 docs)' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
            <div style="margin-bottom:12px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                <p style="font-size:14px;color:rgba(21,40,31,.55);">{{ $pending->total() }} application{{ $pending->total()!==1?'s':'' }} awaiting review</p>
                <p style="font-size:12px;color:rgba(21,40,31,.4);"><i class="fa-solid fa-bolt" style="color:#EEBE15;"></i> Priority applications are marked with a gold badge</p>
            </div>

@endif

{{-- Pagination --}}
@if($pending->hasPages())
<div style="margin-top:20px;">{{ $pending->links() }}</div>
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
                    <option value="">Select reason…</option>
                    <option>Document is blurry or unreadable</option>
                    <option>Wrong document uploaded</option>
                    <option>Document appears altered or invalid</option>
                    <option>Document has expired</option>
                    <option>Name on document does not match profile</option>
                    <option>Other (specify below)</option>
                </select>
                <textarea name="admin_note" rows="2" placeholder="Add a note for the tutor (optional)…"
                          style="width:100%;padding:10px 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;resize:none;line-height:1.6;"></textarea>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="button" onclick="document.getElementById('reject-modal').style.display='none'" class="btn btn-o" style="flex:1;justify-content:center;">Cancel</button>
                <button type="submit" class="btn btn-danger" style="flex:1;justify-content:center;">
                    <i class="fa-solid fa-xmark fa-xs"></i> Reject Document
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
@livewireScripts
@endsection
