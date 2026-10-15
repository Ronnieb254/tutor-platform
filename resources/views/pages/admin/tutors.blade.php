<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Tutor Approvals – Admin | Mwalimu</title>
<style>
html,body{height:100%;background:#F2F5F2;}
.layout{display:flex;height:100vh;overflow:hidden;}
/* Sidebar */
.sidebar{width:240px;background:#111C16;display:flex;flex-direction:column;flex-shrink:0;}
.sb-logo{padding:20px 16px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;gap:8px;}
.sb-logo-text{font-family:'Fraunces',serif;font-size:18px;font-weight:800;color:#fff;}
.sb-logo-text em{font-style:normal;color:#a78bfa;}
.sb-nav{flex:1;padding:8px;overflow-y:auto;}
.sb-item{display:flex;align-items:center;gap:9px;padding:9px 10px;border-radius:10px;font-size:13px;font-weight:500;color:rgba(255,255,255,.55);transition:all .15s;margin-bottom:1px;cursor:pointer;text-decoration:none;}
.sb-item:hover{background:rgba(255,255,255,.07);color:#fff;}
.sb-item.active{background:#6D28D9;color:#fff;}
.sb-badge{margin-left:auto;background:#E8522A;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:999px;}
.sb-footer{padding:12px;border-top:1px solid rgba(255,255,255,.06);}
.sb-user{display:flex;align-items:center;gap:8px;}
.sb-avatar{width:32px;height:32px;border-radius:9px;object-fit:cover;flex-shrink:0;}
/* Main */
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;}
.topbar{height:58px;background:#fff;border-bottom:1px solid rgba(21,40,31,.08);display:flex;align-items:center;justify-content:space-between;padding:0 24px;flex-shrink:0;}
.content{flex:1;overflow-y:auto;padding:24px;}
/* Cards / Tables */
.card{background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.08);}
.card-p{padding:20px;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:all .15s;font-family:inherit;}
.btn-v{background:#6D28D9;color:#fff;}.btn-v:hover{background:#4C1D95;}
.btn-o{background:#fff;color:#15281F;border:1.5px solid rgba(21,40,31,.15);}.btn-o:hover{border-color:#6D28D9;color:#6D28D9;}
.btn-success{background:#DCFCE7;color:#166534;}.btn-success:hover{background:#BBF7D0;}
.btn-danger{background:#FEE2E2;color:#991B1B;}.btn-danger:hover{background:#FECACA;}
.btn-amber{background:#FEF9C3;color:#854D0E;}.btn-amber:hover{background:#FDE68A;}
.btn-sm{padding:6px 12px;font-size:12px;border-radius:8px;}
.bdg{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;}
.bdg-amber{background:#FEF9C3;color:#854D0E;}
.bdg-green{background:#DCFCE7;color:#166534;}
.bdg-red{background:#FEE2E2;color:#991B1B;}
.bdg-blue{background:#DBEAFE;color:#1E40AF;}
.bdg-gray{background:#F3F4F6;color:#6B7280;}
.bdg-violet{background:rgba(109,40,217,.1);color:#6D28D9;}
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
/* Flash */
.flash{padding:12px 16px;border-radius:12px;font-size:13.5px;font-weight:500;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.flash-ok{background:#DCFCE7;color:#166534;border:1px solid #BBF7D0;}
.flash-err{background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;}
</style>
</head>
<body>
@php
use App\Models\TutorProfile;
use App\Models\User;
$pending   = TutorProfile::where('is_approved',false)->with(['user','subjects','documents'])->latest()->paginate(20);
$approved  = TutorProfile::where('is_approved',true)->count();
$totalTutors = TutorProfile::count();
$speedy    = TutorProfile::where('speedy_verification_paid',true)->where('is_approved',false)->count();
@endphp

<div class="layout">

    {{-- Sidebar --}}
    <aside class="sidebar">
        <div class="sb-logo">
            <svg width="28" height="28" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
            <span class="sb-logo-text"><em>Mw</em>alimu</span>
        </div>
        <nav class="sb-nav">
            @foreach([
                ['admin.dashboard','fa-gauge-high','Dashboard'],
                ['admin.tutors','fa-user-check','Tutor Approvals'],
                ['admin.users','fa-users','All Users'],
                ['admin.payouts','fa-credit-card','Payouts'],
                ['admin.categories','fa-layer-group','Categories'],
                ['admin.blog','fa-newspaper','Blog'],
                ['admin.settings','fa-gear','Settings'],
            ] as [$route,$icon,$label])
            <a href="{{ route($route) }}" class="sb-item {{ request()->routeIs($route)?'active':'' }}">
                <i class="fa-solid fa-{{ $icon }}" style="font-size:13px;width:14px;text-align:center;"></i>
                {{ $label }}
                @if($route==='admin.tutors' && $pending->total() > 0)<span class="sb-badge">{{ $pending->total() }}</span>@endif
            </a>
            @endforeach
            <a href="{{ route('home') }}" target="_blank" class="sb-item" style="margin-top:8px;">
                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:13px;width:14px;text-align:center;"></i> View Site
            </a>
        </nav>
        <div class="sb-footer">
            <div class="sb-user">
                <img src="{{ auth()->user()->avatar_url }}" class="sb-avatar" alt="">
                <div style="flex:1;min-width:0;">
                    <div style="font-size:12px;font-weight:600;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->name }}</div>
                    <div style="font-size:10px;color:rgba(255,255,255,.35);">Administrator</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="width:26px;height:26px;border-radius:8px;border:none;background:rgba(255,255,255,.08);color:rgba(255,255,255,.45);cursor:pointer;display:flex;align-items:center;justify-content:center;" title="Sign out">
                        <i class="fa-solid fa-right-from-bracket" style="font-size:11px;"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <div class="main">
        {{-- Topbar --}}
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:12px;">
                <h1 style="font-family:'Fraunces',serif;font-size:19px;font-weight:700;color:#15281F;">Tutor Approvals</h1>
                @if($speedy > 0)
                <span class="bdg" style="background:rgba(212,160,23,.15);color:#D4A017;border:1px solid rgba(212,160,23,.3);">
                    <i class="fa-solid fa-bolt fa-xs"></i> {{ $speedy }} priority
                </span>
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:10px;font-size:13px;color:rgba(21,40,31,.55);">
                <i class="fa-solid fa-circle" style="color:#10B981;font-size:8px;"></i>
                {{ $approved }} approved · {{ $pending->total() }} pending · {{ $totalTutors }} total
            </div>
        </header>

        {{-- Content --}}
        <div class="content">
            @if(session('success'))
            <div class="flash flash-ok"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="flash flash-err"><i class="fa-solid fa-circle-exclamation"></i>{{ session('error') }}</div>
            @endif

            {{-- Summary cards --}}
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:22px;" class="sum-g">
                @foreach([
                    ['Pending Review',$pending->total(),'#D4A017','fa-clock','rgba(212,160,23,.1)'],
                    ['Approved',$approved,'#10B981','fa-circle-check','rgba(16,185,129,.1)'],
                    ['Priority (Speedy)',$speedy,'#6D28D9','fa-bolt','rgba(109,40,217,.1)'],
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
            <style>.sum-g{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:1200px){.sum-g{grid-template-columns:repeat(4,1fr)!important;}}</style>

            @if($pending->isEmpty())
            <div class="card card-p" style="text-align:center;padding:56px;">
                <i class="fa-solid fa-party-horn" style="font-size:40px;color:rgba(21,40,31,.15);margin-bottom:12px;display:block;"></i>
                <h3 style="font-family:'Fraunces',serif;font-size:20px;color:#15281F;margin-bottom:6px;">All caught up!</h3>
                <p style="color:rgba(21,40,31,.5);font-size:14px;">No pending tutor applications right now.</p>
            </div>
            @else
            <div style="margin-bottom:12px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                <p style="font-size:14px;color:rgba(21,40,31,.55);">{{ $pending->total() }} application{{ $pending->total()!==1?'s':'' }} awaiting review</p>
                <p style="font-size:12px;color:rgba(21,40,31,.4);"><i class="fa-solid fa-bolt" style="color:#D4A017;"></i> Priority applications are marked with a gold badge</p>
            </div>

            @foreach($pending as $profile)
            @php
            $docs = $profile->documents;
            $docCount = $docs->count();
            $allPending = $docs->every(fn($d) => $d->status === 'pending');
            $hasRejected = $docs->contains(fn($d) => $d->status === 'rejected');
            @endphp
            <div class="review-panel" x-data="{ open: false }">

                {{-- Header row --}}
                <div class="review-head">
                    <div style="display:flex;align-items:center;gap:14px;flex:1;min-width:0;">
                        <div style="width:48px;height:48px;border-radius:14px;background:#{{ substr(md5($profile->user->email),0,6) }};display:flex;align-items:center;justify-content:center;font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:#fff;flex-shrink:0;">{{ strtoupper(substr($profile->user->name,0,1)) }}</div>
                        <div style="min-width:0;">
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                <span style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:#15281F;">{{ $profile->user->name }}</span>
                                @if($profile->speedy_verification_paid)
                                <span class="bdg" style="background:rgba(212,160,23,.15);color:#D4A017;border:1px solid rgba(212,160,23,.3);"><i class="fa-solid fa-bolt fa-xs"></i> Priority</span>
                                @endif
                                @if($hasRejected)
                                <span class="bdg bdg-red"><i class="fa-solid fa-circle-exclamation fa-xs"></i> Action Needed</span>
                                @elseif($allPending)
                                <span class="bdg bdg-amber"><i class="fa-solid fa-clock fa-xs"></i> Pending</span>
                                @endif
                            </div>
                            <div style="font-size:13px;color:rgba(21,40,31,.5);margin-top:2px;">
                                {{ $profile->user->email }} · {{ $profile->city ?? 'Kenya' }} · {{ $docCount }}/5 docs uploaded
                                @if($profile->speedy_verification_paid)
                                · <span style="color:#D4A017;font-weight:600;"><i class="fa-solid fa-clock fa-xs"></i> Within 24hrs</span>
                                @else
                                · <span style="color:rgba(21,40,31,.4);">Up to 3 days</span>
                                @endif
                            </div>
                            <div style="display:flex;flex-wrap:wrap;gap:5px;margin-top:6px;">
                                @foreach($profile->subjects->take(3) as $s)
                                <span class="bdg bdg-violet" style="font-size:10px;">{{ $s->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                        <span style="font-size:12px;color:rgba(21,40,31,.4);">Applied {{ $profile->created_at->diffForHumans() }}</span>
                        <button @click="open=!open" class="btn btn-o btn-sm">
                            <span x-text="open ? 'Hide Details' : 'Review Documents'"></span>
                            <i class="fa-solid fa-chevron-down fa-xs" :style="open?'transform:rotate(180deg)':''" style="transition:.2s;"></i>
                        </button>
                    </div>
                </div>

                {{-- Expanded review panel --}}
                <div class="review-detail" :class="open ? 'open' : ''" x-show="open" x-collapse>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;" class="detail-g">

                        {{-- Left: Profile info --}}
                        <div>
                            <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.4);margin-bottom:10px;">Profile Details</p>
                            <div style="background:#F9FAF9;border-radius:12px;padding:14px;font-size:14px;color:#15281F;line-height:1.75;margin-bottom:12px;">
                                {{ $profile->bio ?? 'No bio provided.' }}
                            </div>
                            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:14px;">
                                @foreach([
                                    ['KSh '.number_format($profile->hourly_rate),'Hourly Rate'],
                                    [$profile->experience_years.'yr','Experience'],
                                    [$profile->city ?? '—','City'],
                                ] as [$v,$l])
                                <div style="background:#F9FAF9;border-radius:10px;padding:10px;text-align:center;">
                                    <div style="font-size:12px;font-weight:700;color:#15281F;">{{ $v }}</div>
                                    <div style="font-size:10px;color:rgba(21,40,31,.45);margin-top:2px;">{{ $l }}</div>
                                </div>
                                @endforeach
                            </div>
                            <div>
                                <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.4);margin-bottom:8px;">Subjects</p>
                                <div style="display:flex;flex-wrap:wrap;gap:5px;">
                                    @foreach($profile->subjects as $s)
                                    <span class="bdg bdg-violet">{{ $s->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Right: Documents --}}
                        <div>
                            <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.4);margin-bottom:10px;">
                                Uploaded Documents ({{ $docCount }}/5)
                            </p>
                            @if($docs->isEmpty())
                            <div style="background:#FEF9C3;border:1px solid #FDE68A;border-radius:12px;padding:14px;font-size:13px;color:#92400E;">
                                <i class="fa-solid fa-triangle-exclamation fa-xs" style="margin-right:5px;"></i> No documents uploaded yet
                            </div>
                            @else
                            <div style="display:flex;flex-direction:column;gap:8px;">
                                @foreach($docs as $doc)
                                @php
                                $ext = pathinfo($doc->file_path, PATHINFO_EXTENSION);
                                $isPdf = strtolower($ext) === 'pdf';
                                $docTypeLabels = ['national_id'=>'National ID','degree_certificate'=>'Degree Certificate','tsc_certificate'=>'TSC Certificate','certificate_of_good_conduct'=>'Good Conduct','passport_photo'=>'Passport Photo'];
                                $docLabel = $docTypeLabels[$doc->type] ?? ucwords(str_replace('_',' ',$doc->type));
                                @endphp
                                <div class="doc-row" x-data="{ viewDoc: false }">

                                    {{-- Thumbnail or PDF icon --}}
                                    @if($isPdf)
                                    <div class="doc-thumb-placeholder" @click="viewDoc=true" title="View document">
                                        <i class="fa-solid fa-file-pdf" style="color:#E8522A;font-size:22px;"></i>
                                    </div>
                                    @else
                                    <img src="{{ asset('storage/'.$doc->file_path) }}"
                                         class="doc-thumb" @click="viewDoc=true" title="Click to view"
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                    <div class="doc-thumb-placeholder" style="display:none;" @click="viewDoc=true">
                                        <i class="fa-solid fa-image" style="color:rgba(21,40,31,.3);"></i>
                                    </div>
                                    @endif

                                    <div style="flex:1;min-width:0;">
                                        <div style="font-size:13px;font-weight:600;color:#15281F;">{{ $docLabel }}</div>
                                        <div style="font-size:11px;color:rgba(21,40,31,.45);margin-top:1px;">{{ $doc->original_name ?? 'document' }} · {{ strtoupper($ext) }}</div>
                                        @if($doc->admin_note)
                                        <div style="font-size:11px;color:#E8522A;margin-top:3px;"><i class="fa-solid fa-comment fa-xs" style="margin-right:3px;"></i>{{ $doc->admin_note }}</div>
                                        @endif
                                    </div>

                                    <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                                        @php $bc=['pending'=>'bdg-amber','approved'=>'bdg-green','rejected'=>'bdg-red'][$doc->status]; @endphp
                                        <span class="bdg {{ $bc }}">{{ ucfirst($doc->status) }}</span>

                                        {{-- Per-doc actions --}}
                                        <form method="POST" action="/admin/documents/{{ $doc->id }}/approve" style="display:inline;">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm btn-success" title="Approve document" type="submit">
                                                <i class="fa-solid fa-check fa-xs"></i>
                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-danger"
                                                onclick="rejectDoc({{ $doc->id }}, '{{ addslashes($docLabel) }}')"
                                                title="Reject document">
                                            <i class="fa-solid fa-xmark fa-xs"></i>
                                        </button>
                                        <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="btn btn-sm btn-o" title="Open in new tab">
                                            <i class="fa-solid fa-arrow-up-right-from-square fa-xs"></i>
                                        </a>
                                    </div>

                                    {{-- Document viewer modal --}}
                                    <div class="modal-overlay" x-show="viewDoc" x-cloak @click.self="viewDoc=false" @keydown.escape.window="viewDoc=false">
                                        <div class="modal-box">
                                            <div class="modal-head">
                                                <div>
                                                    <span style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:#15281F;">{{ $docLabel }}</span>
                                                    <span style="font-size:12px;color:rgba(21,40,31,.45);margin-left:8px;">{{ $profile->user->name }}</span>
                                                </div>
                                                <div style="display:flex;gap:8px;align-items:center;">
                                                    <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="btn btn-sm btn-o">
                                                        <i class="fa-solid fa-download fa-xs"></i> Download
                                                    </a>
                                                    <button @click="viewDoc=false" style="width:30px;height:30px;border-radius:8px;border:none;background:#F4F6F4;cursor:pointer;display:flex;align-items:center;justify-content:center;color:rgba(21,40,31,.5);">
                                                        <i class="fa-solid fa-xmark fa-sm"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="modal-body" style="text-align:center;">
                                                @if($isPdf)
                                                <iframe src="{{ asset('storage/'.$doc->file_path) }}" style="width:100%;height:500px;border:none;border-radius:12px;"></iframe>
                                                @else
                                                <img src="{{ asset('storage/'.$doc->file_path) }}" alt="{{ $docLabel }}" style="max-width:100%;border-radius:12px;border:1px solid rgba(21,40,31,.08);">
                                                @endif
                                            </div>
                                            <div style="padding:14px 20px;border-top:1px solid rgba(21,40,31,.08);display:flex;gap:8px;justify-content:flex-end;">
                                                <form method="POST" action="/admin/documents/{{ $doc->id }}/approve">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fa-solid fa-check fa-xs"></i> Approve Document
                                                    </button>
                                                </form>
                                                <button class="btn btn-danger" @click="rejectDocModal({{ $doc->id }},'{{ addslashes($docLabel) }}');viewDoc=false">
                                                    <i class="fa-solid fa-xmark fa-xs"></i> Reject Document
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    <style>.detail-g{grid-template-columns:1fr!important;}@media(min-width:900px){.detail-g{grid-template-columns:1fr 1fr!important;}}</style>

                    {{-- Profile-level actions --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:18px;padding-top:16px;border-top:1px solid rgba(21,40,31,.07);flex-wrap:wrap;gap:10px;">
                        <div style="font-size:13px;color:rgba(21,40,31,.5);">
                            @php
                            $approvedDocs = $docs->where('status','approved')->count();
                            @endphp
                            <i class="fa-solid fa-info-circle fa-xs" style="color:#6D28D9;"></i>
                            {{ $approvedDocs }}/{{ $docCount }} documents approved.
                            @if($approvedDocs === $docCount && $docCount === 5)
                            <span style="color:#10B981;font-weight:600;">All documents approved — ready to activate.</span>
                            @else
                            Review and approve all documents before activating this tutor.
                            @endif
                        </div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <form method="POST" action="/admin/tutors/{{ $profile->id }}/suspend"
                                  onsubmit="return confirm('Suspend {{ addslashes($profile->user->name) }}? They will be notified.')">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa-solid fa-ban fa-xs"></i> Suspend
                                </button>
                            </form>
                            <form method="POST" action="/admin/tutors/{{ $profile->id }}/approve">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-success"
                                        @if($docCount < 5) style="opacity:.5;" title="Upload all 5 documents first" @endif>
                                    <i class="fa-solid fa-circle-check fa-xs"></i> Approve & Go Live
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            @if($pending->hasPages())
            <div style="margin-top:16px;">{{ $pending->links() }}</div>
            @endif
            @endif
        </div>
    </div>
</div>

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
</body>
</html>
