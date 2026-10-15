@extends('layouts.admin')
@section('title', 'Payout Requests')

@section('content')
@php
use App\Models\PayoutRequest;
$status   = request('status','pending');
$payouts  = PayoutRequest::where('status',$status)->with('tutor')->latest()->paginate(20);
$pending  = PayoutRequest::where('status','pending')->sum('amount');
$paid     = PayoutRequest::where('status','paid')->sum('amount');
@endphp

<div class="page-header">
    <div>
        <h1>Payout Requests</h1>
        <p>Review and process tutor withdrawal requests</p>
    </div>
</div>

{{-- Summary cards --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    @foreach([
        ['Pending','pending',PayoutRequest::where('status','pending')->count(),'#D4A017','badge-amber'],
        ['Approved','approved',PayoutRequest::where('status','approved')->count(),'#6D28D9','badge-violet'],
        ['Paid Out','paid',PayoutRequest::where('status','paid')->count(),'#10B981','badge-green'],
        ['Rejected','rejected',PayoutRequest::where('status','rejected')->count(),'#E8522A','badge-red'],
    ] as [$label,$st,$count,$color,$bc])
    <a href="{{ route('admin.payouts') }}?status={{ $st }}"
       style="background:#fff;border:2px solid {{ $st===$status ? $color : 'var(--hairline)' }};border-radius:14px;padding:18px;text-align:center;display:block;transition:border-color .15s;">
        <div style="font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:{{ $color }};">{{ $count }}</div>
        <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $label }}</div>
    </a>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
    <div style="background:#fff;border:1px solid var(--hairline);border-radius:14px;padding:20px;display:flex;align-items:center;gap:16px;">
        <div style="width:44px;height:44px;background:rgba(212,160,23,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#D4A017" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;font-weight:600;">Awaiting Payment</div>
            <div style="font-family:'Fraunces',serif;font-size:26px;font-weight:700;color:var(--ink);">KSh {{ number_format($pending) }}</div>
        </div>
    </div>
    <div style="background:#fff;border:1px solid var(--hairline);border-radius:14px;padding:20px;display:flex;align-items:center;gap:16px;">
        <div style="width:44px;height:44px;background:rgba(16,185,129,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#10B981" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
            <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;font-weight:600;">Total Paid Out</div>
            <div style="font-family:'Fraunces',serif;font-size:26px;font-weight:700;color:var(--ink);">KSh {{ number_format($paid) }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div style="padding:16px 24px;border-bottom:1px solid var(--hairline);display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:14px;font-weight:600;color:var(--ink);">{{ ucfirst($status) }} Requests</span>
        <span style="font-size:13px;color:var(--muted);">{{ $payouts->total() }} requests</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tutor</th>
                    <th>Amount</th>
                    <th>Bank Details</th>
                    <th>Requested</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payouts as $payout)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="avatar" style="background:#6D28D9;">{{ substr($payout->tutor->name,0,1) }}</div>
                            <div>
                                <div style="font-weight:600;font-size:13.5px;">{{ $payout->tutor->name }}</div>
                                <div style="font-size:12px;color:var(--muted);">{{ $payout->tutor->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);">KSh {{ number_format($payout->amount) }}</td>
                    <td>
                        @if($payout->bank_name)
                        <div style="font-size:13px;"><strong>{{ $payout->bank_name }}</strong></div>
                        <div style="font-size:12px;color:var(--muted);">{{ $payout->account_number }} · {{ $payout->account_name }}</div>
                        @else
                        <span style="color:var(--muted);font-size:13px;">Not provided</span>
                        @endif
                    </td>
                    <td style="color:var(--muted);font-size:13px;">{{ $payout->created_at->diffForHumans() }}</td>
                    <td>
                        @php $pc=['pending'=>'badge-amber','approved'=>'badge-violet','paid'=>'badge-green','rejected'=>'badge-red'][$payout->status]??'badge-gray'; @endphp
                        <span class="badge {{ $pc }}">{{ ucfirst($payout->status) }}</span>
                    </td>
                    <td>
                        @if($payout->status === 'pending')
                        <div style="display:flex;gap:6px;">
                            <form method="POST" action="/admin/payouts/{{ $payout->id }}/approve">
                                @csrf @method('PATCH')
                                <button class="btn btn-success btn-sm">Approve</button>
                            </form>
                            <form method="POST" action="/admin/payouts/{{ $payout->id }}/reject">
                                @csrf @method('PATCH')
                                <button class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        </div>
                        @elseif($payout->status === 'approved')
                        <form method="POST" action="/admin/payouts/{{ $payout->id }}/mark-paid">
                            @csrf @method('PATCH')
                            <button class="btn btn-primary btn-sm">Mark Paid</button>
                        </form>
                        @else
                        <span style="font-size:12px;color:var(--muted);">{{ $payout->processed_at?->format('M j') ?? '—' }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:40px;">No {{ $status }} payout requests</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payouts->hasPages())
    <div style="padding:16px 24px;border-top:1px solid var(--hairline);">{{ $payouts->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
