@extends('layouts.admin')
@section('title', 'Tutor Approvals')

@section('content')
@php
use App\Models\TutorProfile;
$pending   = TutorProfile::where('is_approved', false)->with(['user','subjects','documents'])->latest()->paginate(15);
$approved  = TutorProfile::where('is_approved', true)->count();
$total     = TutorProfile::count();
@endphp

<div class="page-header">
    <div>
        <h1>Tutor Approvals</h1>
        <p>Review tutor applications, verify documents and approve profiles</p>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <div style="background:#fff;border:1px solid var(--hairline);border-radius:12px;padding:10px 20px;text-align:center;">
            <div style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--violet);">{{ $pending->total() }}</div>
            <div style="font-size:11px;color:var(--muted);">Pending</div>
        </div>
        <div style="background:#fff;border:1px solid var(--hairline);border-radius:12px;padding:10px 20px;text-align:center;">
            <div style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--mint);">{{ $approved }}</div>
            <div style="font-size:11px;color:var(--muted);">Approved</div>
        </div>
        <div style="background:#fff;border:1px solid var(--hairline);border-radius:12px;padding:10px 20px;text-align:center;">
            <div style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);">{{ $total }}</div>
            <div style="font-size:11px;color:var(--muted);">Total</div>
        </div>
    </div>
</div>

@if($pending->isEmpty())
<div class="card card-p" style="text-align:center;padding:64px;">
    <div style="font-size:48px;margin-bottom:12px;">🎉</div>
    <h3 style="font-family:'Fraunces',serif;font-size:22px;color:var(--ink);margin-bottom:8px;">All caught up!</h3>
    <p style="color:var(--muted);">No pending tutor applications at the moment.</p>
</div>
@else
<div style="display:flex;flex-direction:column;gap:16px;">
    @foreach($pending as $profile)
    <div class="card" x-data="{ expanded: false }">
        <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            {{-- Left: tutor info --}}
            <div style="display:flex;align-items:center;gap:16px;flex:1;min-width:0;">
                <div class="avatar" style="width:48px;height:48px;border-radius:14px;font-size:18px;background:#{{ ['6D28D9','10B981','E8522A','D4A017','3B82F6'][$loop->index % 5] }};">
                    {{ substr($profile->user->name, 0, 1) }}
                </div>
                <div style="min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <span style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);">{{ $profile->user->name }}</span>
                        <span class="badge badge-amber">Pending Review</span>
                    </div>
                    <div style="font-size:13px;color:var(--muted);margin-top:3px;">
                        {{ $profile->user->email }} · {{ $profile->city ?? 'Kenya' }} · KSh {{ number_format($profile->hourly_rate) }}/hr
                    </div>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:6px;">
                        @foreach($profile->subjects->take(4) as $subject)
                        <span class="badge badge-violet">{{ $subject->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right: actions --}}
            <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                <div style="text-align:center;padding:0 12px;">
                    <div style="font-size:11px;color:var(--muted);">Documents</div>
                    <div style="font-weight:700;color:var(--ink);">{{ $profile->documents->count() }}</div>
                </div>
                <div style="text-align:center;padding:0 12px;">
                    <div style="font-size:11px;color:var(--muted);">Applied</div>
                    <div style="font-weight:600;font-size:12px;color:var(--ink);">{{ $profile->created_at->diffForHumans() }}</div>
                </div>
                <button @click="expanded=!expanded" class="btn btn-outline btn-sm">
                    <span x-text="expanded ? 'Hide Details' : 'Review'"></span>
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" :style="expanded ? 'transform:rotate(180deg)' : ''" style="transition:.2s"><path d="M6 9l6 6 6-6"/></svg>
                </button>
            </div>
        </div>

        {{-- Expanded review panel --}}
        <div x-show="expanded" x-cloak style="border-top:1px solid var(--hairline);padding:24px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

                {{-- Bio & details --}}
                <div>
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:10px;">Profile Details</div>
                    <div style="background:#F4F6F4;border-radius:12px;padding:16px;font-size:13.5px;line-height:1.7;color:var(--ink);">
                        {{ $profile->bio ?? 'No bio provided.' }}
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:14px;">
                        @foreach([['Experience', $profile->experience_years . ' years'],['Rate', 'KSh ' . number_format($profile->hourly_rate)],['Languages', implode(', ', $profile->languages ?? ['English'])]] as [$k,$v])
                        <div style="background:#F4F6F4;border-radius:10px;padding:10px;text-align:center;">
                            <div style="font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;">{{ $k }}</div>
                            <div style="font-size:13px;font-weight:600;color:var(--ink);margin-top:2px;">{{ $v }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Documents --}}
                <div>
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:10px;">Uploaded Documents</div>
                    @if($profile->documents->isEmpty())
                    <div style="background:#FEF9C3;border:1px solid #FDE68A;border-radius:12px;padding:14px;font-size:13px;color:#92400E;">
                        ⚠ No documents uploaded yet
                    </div>
                    @else
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        @foreach($profile->documents as $doc)
                        <div style="display:flex;align-items:center;justify-content:space-between;background:#F4F6F4;border-radius:10px;padding:12px 14px;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:32px;height:32px;background:#fff;border-radius:8px;border:1px solid var(--hairline);display:flex;align-items:center;justify-content:center;font-size:14px;">
                                    {{ in_array(pathinfo($doc->file_path, PATHINFO_EXTENSION), ['pdf']) ? '📄' : '🖼' }}
                                </div>
                                <div>
                                    <div style="font-size:13px;font-weight:600;color:var(--ink);">{{ ucwords(str_replace('_',' ',$doc->type)) }}</div>
                                    <div style="font-size:11px;color:var(--muted);">{{ $doc->original_name ?? 'document' }}</div>
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="btn btn-outline btn-sm" style="font-size:11px;padding:4px 10px;">View</a>
                                @php $dc = ['pending'=>'badge-amber','approved'=>'badge-green','rejected'=>'badge-red'][$doc->status]; @endphp
                                <span class="badge {{ $dc }}">{{ ucfirst($doc->status) }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Action buttons --}}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:20px;padding-top:16px;border-top:1px solid var(--hairline);">
                <div style="font-size:13px;color:var(--muted);">
                    Approve to make this tutor's profile live on the platform.
                </div>
                <div style="display:flex;gap:10px;">
                    <form method="POST" action="/admin/tutors/{{ $profile->id }}/reject" onsubmit="return confirm('Reject this tutor application?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-danger">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Reject
                        </button>
                    </form>
                    <form method="POST" action="/admin/tutors/{{ $profile->id }}/approve">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-success" style="background:var(--mint);color:#fff;">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Approve & Go Live
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div style="margin-top:20px;">{{ $pending->links() }}</div>
@endif
@endsection
