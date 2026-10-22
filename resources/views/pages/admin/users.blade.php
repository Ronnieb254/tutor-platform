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
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if($search || $role)
        <a href="{{ route('admin.users') }}" class="btn btn-outline">Clear</a>
        @endif
    </form>
</div>

{{-- Role summary --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
    @foreach([['Students','student','#0A64BC'],['Parents','parent','#E8522A'],['Tutors','tutor','#10B981'],['Admins','admin','#EEBE15']] as [$label,$r,$color])
    <a href="{{ route('admin.users') }}?role={{ $r }}" style="background:#fff;border:1px solid {{ $r===$role ? $color : 'var(--hairline)' }};border-radius:12px;padding:14px 16px;text-align:center;display:block;">
        <div style="font-family:'Fraunces',serif;font-size:24px;font-weight:700;color:{{ $color }};">{{ User::where('role',$r)->count() }}</div>
        <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $label }}</div>
    </a>
    @endforeach
</div>

<div class="card">
    <div style="padding:16px 24px;border-bottom:1px solid var(--hairline);display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:14px;color:var(--muted);">{{ $users->total() }} user{{ $users->total()!==1?'s':'' }} found</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Status</th>
                    <th>Actions</th>
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
                        @php $rc=['student'=>'badge-blue','parent'=>'badge-trust-blue','tutor'=>'badge-green','admin'=>'badge-amber'][$user->role]??'badge-gray'; @endphp
                        <span class="badge {{ $rc }}">{{ ucfirst($user->role) }}</span>
                    </td>
                    <td style="color:var(--muted);font-size:13px;">{{ $user->created_at->format('M j, Y') }}</td>
                    <td>
                        @if($user->email_verified_at)
                        <span class="badge badge-green">✓ Verified</span>
                        @else
                        <span class="badge badge-amber">Unverified</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
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
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:40px;">No users found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div style="padding:16px 24px;border-top:1px solid var(--hairline);">{{ $users->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
