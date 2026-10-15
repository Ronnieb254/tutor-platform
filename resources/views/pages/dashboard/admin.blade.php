@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
@php
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\TutorProfile;
use App\Models\PayoutRequest;

$totalUsers      = User::count();
$activeTutors    = TutorProfile::where('is_approved',true)->count();
$pendingTutors   = TutorProfile::where('is_approved',false)->count();
$todayBookings   = Booking::whereDate('created_at', today())->count();
$todayRevenue    = Payment::where('status','success')->whereDate('paid_at', today())->sum('amount');
$monthRevenue    = Payment::where('status','success')->whereMonth('paid_at', now()->month)->sum('amount');
$totalRevenue    = Payment::where('status','success')->sum('amount');
$pendingPayouts  = PayoutRequest::where('status','pending')->count();
$recentBookings  = Booking::with(['student','tutor','subject'])->latest()->take(8)->get();
$pendingApprovals= TutorProfile::where('is_approved',false)->with(['user','documents'])->latest()->take(5)->get();
@endphp

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(109,40,217,0.1);">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#6D28D9" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-label">Total Users</div>
        <div class="stat-value">{{ number_format($totalUsers) }}</div>
        <div class="stat-trend trend-up">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M18 15l-6-6-6 6"/></svg>
            {{ $activeTutors }} active tutors
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(16,185,129,0.1);">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#10B981" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="stat-label">Today's Bookings</div>
        <div class="stat-value">{{ $todayBookings }}</div>
        <div class="stat-trend trend-up">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M18 15l-6-6-6 6"/></svg>
            {{ Booking::whereDate('created_at', today()->subDay())->count() }} yesterday
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(232,82,42,0.1);">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#E8522A" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
        </div>
        <div class="stat-label">Today's Revenue</div>
        <div class="stat-value" style="font-size:24px;">KSh {{ number_format($todayRevenue) }}</div>
        <div class="stat-trend" style="color:var(--muted);">
            Month: KSh {{ number_format($monthRevenue) }}
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(212,160,23,0.1);">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#D4A017" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-label">Pending Actions</div>
        <div class="stat-value">{{ $pendingTutors + $pendingPayouts }}</div>
        <div class="stat-trend" style="color:var(--muted);">
            {{ $pendingTutors }} tutors · {{ $pendingPayouts }} payouts
        </div>
    </div>
</div>

{{-- Second row stats --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
    <div style="background:#fff;border-radius:14px;border:1px solid var(--hairline);padding:16px 20px;display:flex;align-items:center;gap:16px;">
        <div style="width:40px;height:40px;background:rgba(109,40,217,0.08);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#6D28D9" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
        <div>
            <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Total Revenue</div>
            <div style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);">KSh {{ number_format($totalRevenue) }}</div>
        </div>
    </div>
    <div style="background:#fff;border-radius:14px;border:1px solid var(--hairline);padding:16px 20px;display:flex;align-items:center;gap:16px;">
        <div style="width:40px;height:40px;background:rgba(16,185,129,0.08);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#10B981" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        </div>
        <div>
            <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Completed Sessions</div>
            <div style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);">{{ number_format(Booking::where('status','completed')->count()) }}</div>
        </div>
    </div>
    <div style="background:#fff;border-radius:14px;border:1px solid var(--hairline);padding:16px 20px;display:flex;align-items:center;gap:16px;">
        <div style="width:40px;height:40px;background:rgba(232,82,42,0.08);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E8522A" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div>
            <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Students</div>
            <div style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);">{{ number_format(User::whereIn('role',['student','parent'])->count()) }}</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 380px;gap:20px;">

    {{-- Recent Bookings --}}
    <div class="card">
        <div style="padding:20px 24px;border-bottom:1px solid var(--hairline);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);">Recent Bookings</h3>
                <p style="font-size:12px;color:var(--muted);margin-top:1px;">Latest session bookings across the platform</p>
            </div>
            <a href="#" class="btn btn-outline btn-sm">View All</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Tutor</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBookings as $booking)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div class="avatar" style="width:30px;height:30px;border-radius:8px;font-size:12px;background:#6D28D9;">{{ substr($booking->student->name,0,1) }}</div>
                                <span style="font-weight:500;">{{ $booking->student->name }}</span>
                            </div>
                        </td>
                        <td style="color:var(--muted);">{{ $booking->tutor->name }}</td>
                        <td>{{ $booking->subject?->name ?? '—' }}</td>
                        <td style="color:var(--muted);white-space:nowrap;">{{ $booking->scheduled_at->format('M j, g:ia') }}</td>
                        <td style="font-weight:600;">KSh {{ number_format($booking->total_amount) }}</td>
                        <td>
                            @php $sc = ['pending'=>'badge-amber','confirmed'=>'badge-green','completed'=>'badge-blue','cancelled'=>'badge-red'][$booking->status] ?? 'badge-gray'; @endphp
                            <span class="badge {{ $sc }}">{{ ucfirst($booking->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:32px;">No bookings yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pending Approvals --}}
    <div class="card" style="height:fit-content;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--hairline);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);">Pending Approvals</h3>
                <p style="font-size:12px;color:var(--muted);margin-top:1px;">Tutors awaiting verification</p>
            </div>
            @if($pendingTutors > 0)
            <span class="badge badge-amber">{{ $pendingTutors }}</span>
            @endif
        </div>
        <div style="padding:8px;">
            @forelse($pendingApprovals as $profile)
            <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:10px;" onmouseover="this.style.background='#F4F6F4'" onmouseout="this.style.background='transparent'">
                <div class="avatar" style="background:#{{ ['6D28D9','10B981','E8522A','D4A017'][$loop->index % 4] }};">{{ substr($profile->user->name,0,1) }}</div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13.5px;font-weight:600;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $profile->user->name }}</div>
                    <div style="font-size:11px;color:var(--muted);">{{ $profile->city }} · {{ $profile->documents->count() }} doc(s)</div>
                </div>
                <a href="{{ route('admin.tutors') }}" class="btn btn-outline btn-sm" style="font-size:11px;padding:5px 10px;">Review</a>
            </div>
            @empty
            <div style="text-align:center;padding:28px 16px;color:var(--muted);font-size:14px;">
                <div style="font-size:28px;margin-bottom:8px;">🎉</div>
                All tutors reviewed!
            </div>
            @endforelse
        </div>
        @if($pendingTutors > 5)
        <div style="padding:12px 20px;border-top:1px solid var(--hairline);text-align:center;">
            <a href="{{ route('admin.tutors') }}" style="font-size:13px;color:var(--violet);font-weight:600;">View all {{ $pendingTutors }} pending →</a>
        </div>
        @endif
    </div>
</div>
@endsection
