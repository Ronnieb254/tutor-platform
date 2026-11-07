@extends('layouts.admin')
@section('title', 'Users')

@section('content')
@php
use App\Models\User;
$search = request('search','');
$role   = request('role','');
$users  = User::when($search, fn($q) => $q->where('name','like',"%$search%")->orWhere('email','like',"%$search%"))
              ->when($role, fn($q) => $q->where('role',$role))
              ->latest()->paginate(20);
@endphp

<div class="page-header">
    <div>
        <h1>All Users</h1>
        <p>Manage students, parents, tutors and admins</p>
    </div>
</div>

{{-- Filters --}}
<div class="card card-p" style="margin-bottom:20px;">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:200px;">
            <label class="form-label">Search</label>
            <input type="text" name="search" value="{{ $search }}" class="form-input" placeholder="Name or email…">
        </div>
        <div style="width:160px;">
            <label class="form-label">Role</label>
            <select name="role" class="form-input form-select">
                <option value="">All roles</option>
                <option value="student" {{ $role==='student'?'selected':'' }}>Students</option>
                <option value="parent" {{ $role==='parent'?'selected':'' }}>Parents</option>
                <option value="tutor" {{ $role==='tutor'?'selected':'' }}>Tutors</option>
                <option value="admin" {{ $role==='admin'?'selected':'' }}>Admins</option>
                <option value="super-admin" {{ $role==='super-admin'?'selected':'' }}>Super Admins</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if($search || $role)
        <a href="{{ route('admin.users') }}" class="btn btn-outline">Clear</a>
        @endif
    </form>
</div>

{{-- Role summary --}}
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:20px;" class="role-summary-grid">
    @foreach([['Students','student','#0A64BC'],['Parents','parent','#E8522A'],['Tutors','tutor','#10B981'],['Admins','admin','#EEBE15'],['Super Admins','super-admin','#7C3AED']] as [$label,$r,$color])
    <a href="{{ route('admin.users') }}?role={{ $r }}" style="background:#fff;border:1px solid {{ $r===$role ? $color : 'var(--hairline)' }};border-radius:12px;padding:14px 16px;text-align:center;display:block;">
        <div style="font-family:'Fraunces',serif;font-size:24px;font-weight:700;color:{{ $color }};">{{ \App\Models\User::where('role',$r)->count() }}</div>
        <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $label }}</div>
    </a>
    @endforeach
</div>

<div class="card">
    <div style="padding:16px 24px;border-bottom:1px solid var(--hairline);display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:14px;color:var(--muted);">{{ $users->total() }} user{{ $users->total()!==1?'s':'' }} found</span>
    </div>
    <div class="table-wrap">
        <table id="users-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Status</th>
                    <th class="no-export">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div class="avatar" style="background:#{{ substr(md5($user->email),0,6) }};">{{ substr($user->name,0,1) }}</div>
                            <div>
                                <div style="font-weight:600;font-size:13.5px;">{{ $user->name }}</div>
                                <div style="font-size:12px;color:var(--muted);">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php $rc=['student'=>'badge-blue','parent'=>'badge-trust-blue','tutor'=>'badge-green','admin'=>'badge-amber','super-admin'=>'badge-purple'][$user->role]??'badge-gray'; @endphp
                        <span class="badge {{ $rc }}" style="{{ $user->role==='super-admin'?'background:#7C3AED;color:#fff;':'' }}">{{ $user->role==='super-admin'?'â­ Super Admin':ucfirst($user->role) }}</span>
                    </td>
                    <td data-order="{{ $user->created_at->timestamp }}" style="color:var(--muted);font-size:13px;">{{ $user->created_at->format('M j, Y') }}</td>
                    <td>
                        @if($user->email_verified_at)
                        <span class="badge badge-green">âœ- Verified</span>
                        @else
                        <span class="badge badge-amber">Unverified</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            @if($user->role === 'tutor' && $user->tutorProfile)
                            <a href="{{ route('admin.tutors') }}" class="btn btn-outline btn-sm">Profile</a>
                            @endif
                            @if($user->id !== auth()->id())
                            <form method="POST" action="/admin/users/{{ $user->id }}/toggle-active" style="display:inline;">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm {{ $user->is_active ? 'btn-danger' : 'btn-success' }}">
                                    {{ $user->is_active ? 'Suspend' : 'Activate' }}
                                </button>
                            </form>
                            {{-- Change Role "- only super-admin can do this --}}
                            @if(auth()->user()->isSuperAdmin())
                            <form method="POST" action="/admin/users/{{ $user->id }}/change-role" style="display:inline;display:flex;align-items:center;gap:4px;">
                                @csrf @method('PATCH')
                                <select name="role" onchange="this.form.submit()"
                                        style="height:30px;padding:0 8px;border-radius:8px;border:1.5px solid rgba(21,40,31,.2);font-size:12px;font-family:inherit;background:#fff;cursor:pointer;"
                                        title="Change role">
                                    @foreach(['student','parent','tutor','admin','super-admin'] as $r)
                                    <option value="{{ $r }}" {{ $user->role===$r?'selected':'' }}>{{ $r==='super-admin'?'â­ Super Admin':ucfirst($r) }}</option>
                                    @endforeach
                                </select>
                            </form>
                            @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div style="padding:16px 24px;border-top:1px solid var(--hairline);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        {{-- Result count --}}
        <div style="font-size:13px;color:var(--muted);">
            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
        </div>
        {{-- Page links --}}
        <div style="display:flex;align-items:center;gap:4px;">
            {{-- Previous --}}
            @if($users->onFirstPage())
                <span style="padding:6px 12px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--muted);cursor:not-allowed;background:#fafafa;">
                    <span style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>
                </span>
            @else
                <a href="{{ $users->previousPageUrl() }}" style="padding:6px 12px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--ink);text-decoration:none;background:#fff;transition:border-color .15s;" onmouseover="this.style.borderColor='#0A64BC'" onmouseout="this.style.borderColor='var(--hairline)'">
                    <span style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>
                </a>
            @endif

            {{-- Page numbers --}}
            @php
                $current = $users->currentPage();
                $last    = $users->lastPage();
                $window  = collect(range(1, $last))->filter(fn($p) =>
                    $p === 1 || $p === $last || abs($p - $current) <= 2
                );
            @endphp
            @php $prev = null; @endphp
            @foreach($window as $page)
                @if($prev !== null && $page - $prev > 1)
                    <span style="padding:6px 8px;font-size:13px;color:var(--muted);">…</span>
                @endif
                @if($page == $users->currentPage())
                    <span style="padding:6px 12px;border-radius:8px;border:1px solid #0A64BC;font-size:13px;font-weight:700;color:#fff;background:#0A64BC;">{{ $page }}</span>
                @else
                    <a href="{{ $users->url($page) }}" style="padding:6px 12px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--ink);text-decoration:none;background:#fff;transition:border-color .15s;" onmouseover="this.style.borderColor='#0A64BC'" onmouseout="this.style.borderColor='var(--hairline)'">{{ $page }}</a>
                @endif
                @php $prev = $page; @endphp
            @endforeach

            {{-- Next --}}
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" style="padding:6px 12px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--ink);text-decoration:none;background:#fff;transition:border-color .15s;" onmouseover="this.style.borderColor='#0A64BC'" onmouseout="this.style.borderColor='var(--hairline)'">
                    <span style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>
                </a>
            @else
                <span style="padding:6px 12px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--muted);cursor:not-allowed;background:#fafafa;">
                    <span style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
/* role summary grid: 5-col â†’ 3-col â†’ 2-col â†’ 1-col */
@media(max-width:767px){
    .role-summary-grid{ grid-template-columns:repeat(3,1fr) !important; }
}
@media(max-width:479px){
    .role-summary-grid{ grid-template-columns:repeat(2,1fr) !important; }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    initDataTable('#users-table', {
        order: [[2, 'desc']], // Joined column descending
        columnDefs: [
            { type: 'date', targets: 2 }, // Joined
            { orderable: false, targets: 4 } // Actions
        ]
    });
});
</script>
@endpush
@endsection
