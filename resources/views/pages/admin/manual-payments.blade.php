@extends('layouts.admin')
@section('title', 'Manual Payment Verification')

@section('content')

<style>
.review-panel{background:#fff;border-radius:18px;border:1px solid rgba(21,40,31,.08);margin-bottom:14px;overflow:hidden;}
.review-head{padding:18px 22px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;}
.review-detail{padding:22px;border-top:1px solid rgba(21,40,31,.06);}
.badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;}
.badge-warning{background:rgba(238,190,21,.15);color:#92400E;}
.badge-success{background:rgba(16,185,129,.15);color:#065F46;}
.badge-danger{background:rgba(239,68,68,.15);color:#991B1B;}
.badge-info{background:rgba(10,100,188,.12);color:#1E40AF;}
.action-form{display:none;margin-top:16px;padding:16px;background:#F9FAFB;border-radius:12px;border:1px solid rgba(21,40,31,.08);}
.action-form.open{display:block;}
</style>

<div class="page-header">
    <div>
        <h1>Manual Payment Verification</h1>
        <p>Verify M-Pesa Paybill payments — Paybill <strong>542542</strong> · Account <strong>313126</strong></p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if(session('info'))
<div class="alert alert-info">{{ session('info') }}</div>
@endif

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:22px;">
    @foreach([
        ['Pending Verification', $payments->total(), '#EEBE15', 'fa-hourglass-half', 'rgba(238,190,21,.12)'],
        ['Verified Last 24h', $recentlyVerified->where('status','success')->count(), '#10B981', 'fa-circle-check', 'rgba(16,185,129,.1)'],
        ['Rejected Last 24h', $recentlyVerified->where('status','failed')->count(), '#EF4444', 'fa-xmark-circle', 'rgba(239,68,68,.1)'],
        ['Total Recent Activity', $recentlyVerified->count(), '#0A64BC', 'fa-list-check', 'rgba(10,100,188,.1)'],
    ] as [$label, $val, $col, $ic, $bg])
    <div style="background:#fff;border-radius:14px;border:1px solid rgba(21,40,31,.07);padding:16px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:11px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid {{ $ic }}" style="color:{{ $col }};font-size:17px;"></i>
        </div>
        <div>
            <div style="font-size:22px;font-weight:800;color:var(--ink);">{{ $val }}</div>
            <div style="font-size:12px;color:var(--muted);">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Pending Verification Queue --}}
<h2 style="font-size:17px;font-weight:700;color:var(--ink);margin-bottom:14px;">
    <i class="fa-solid fa-hourglass-half" style="color:#EEBE15;margin-right:6px;"></i>
    Pending Verification ({{ $payments->total() }})
</h2>

@forelse($payments as $payment)
<div class="review-panel" x-data="{ verifyOpen: false, rejectOpen: false }">
    <div class="review-head">
        <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:0;">
            <div style="width:42px;height:42px;border-radius:50%;background:rgba(238,190,21,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px;font-weight:800;color:#92400E;">
                {{ strtoupper(substr($payment->user->name ?? 'U', 0, 1)) }}
            </div>
            <div style="min-width:0;">
                <div style="font-weight:700;color:var(--ink);font-size:14px;">{{ $payment->user->name ?? 'Unknown' }}</div>
                <div style="font-size:12px;color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $payment->user->email ?? '' }}</div>
            </div>
        </div>
        <div style="text-align:right;flex-shrink:0;">
            <div style="font-family:monospace;font-size:16px;font-weight:800;color:#0A64BC;letter-spacing:.05em;">
                {{ $payment->mpesa_code ?? '—' }}
            </div>
            <div style="font-size:12px;color:var(--muted);">KSh {{ number_format($payment->amount, 2) }}</div>
        </div>
        <div style="flex-shrink:0;">
            @if($payment->booking_id)
                <span class="badge badge-info"><i class="fa-solid fa-graduation-cap"></i> Booking</span>
            @elseif($payment->clinic_id)
                <span class="badge badge-info"><i class="fa-solid fa-stethoscope"></i> Clinic</span>
            @else
                <span class="badge badge-warning">Unknown</span>
            @endif
        </div>
        <div style="font-size:11px;color:var(--muted);flex-shrink:0;">
            {{ $payment->created_at->diffForHumans() }}
        </div>
        <div style="display:flex;gap:8px;flex-shrink:0;">
            <button @click="verifyOpen = !verifyOpen; rejectOpen = false"
                style="padding:7px 14px;border-radius:999px;background:#10B981;color:#fff;border:none;cursor:pointer;font-size:12px;font-weight:700;font-family:inherit;">
                <i class="fa-solid fa-check"></i> Verify
            </button>
            <button @click="rejectOpen = !rejectOpen; verifyOpen = false"
                style="padding:7px 14px;border-radius:999px;background:#EF4444;color:#fff;border:none;cursor:pointer;font-size:12px;font-weight:700;font-family:inherit;">
                <i class="fa-solid fa-xmark"></i> Reject
            </button>
        </div>
    </div>

    {{-- Booking details --}}
    @if($payment->booking)
    <div style="padding:0 22px 14px;font-size:12px;color:var(--muted);">
        <strong>Session:</strong> {{ $payment->booking->subject->name ?? 'N/A' }} ·
        {{ $payment->booking->scheduled_at?->format('d M Y, g:ia') ?? 'N/A' }} ·
        Tutor: {{ $payment->booking->tutor->name ?? 'N/A' }}
    </div>
    @endif

    {{-- Verify form --}}
    <div class="action-form" :class="{ open: verifyOpen }">
        <form method="POST" action="{{ route('admin.manual-payments.verify', $payment) }}">
            @csrf
            <label style="font-size:13px;font-weight:700;color:var(--ink);display:block;margin-bottom:6px;">
                Optional note (visible to student if rejected — leave blank to approve silently)
            </label>
            <textarea name="note" rows="2" placeholder="e.g. Verified via M-Pesa logs"
                style="width:100%;border-radius:10px;border:1.5px solid rgba(21,40,31,.15);padding:10px;font-family:inherit;font-size:13px;resize:vertical;box-sizing:border-box;"></textarea>
            <div style="display:flex;gap:8px;margin-top:10px;">
                <button type="submit" style="padding:9px 20px;border-radius:999px;background:#10B981;color:#fff;border:none;cursor:pointer;font-size:13px;font-weight:700;font-family:inherit;">
                    <i class="fa-solid fa-check"></i> Confirm Verification
                </button>
                <button type="button" @click="verifyOpen = false" style="padding:9px 16px;border-radius:999px;background:rgba(21,40,31,.08);color:var(--ink);border:none;cursor:pointer;font-size:13px;font-family:inherit;">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    {{-- Reject form --}}
    <div class="action-form" :class="{ open: rejectOpen }">
        <form method="POST" action="{{ route('admin.manual-payments.reject', $payment) }}">
            @csrf
            <label style="font-size:13px;font-weight:700;color:#991B1B;display:block;margin-bottom:6px;">
                Rejection reason <span style="color:#EF4444;">*</span> (sent to student by email)
            </label>
            <textarea name="reason" rows="3" required placeholder="e.g. Code not found in M-Pesa records. Please check and resubmit."
                style="width:100%;border-radius:10px;border:1.5px solid rgba(239,68,68,.3);padding:10px;font-family:inherit;font-size:13px;resize:vertical;box-sizing:border-box;"></textarea>
            <div style="display:flex;gap:8px;margin-top:10px;">
                <button type="submit" style="padding:9px 20px;border-radius:999px;background:#EF4444;color:#fff;border:none;cursor:pointer;font-size:13px;font-weight:700;font-family:inherit;">
                    <i class="fa-solid fa-xmark"></i> Confirm Rejection
                </button>
                <button type="button" @click="rejectOpen = false" style="padding:9px 16px;border-radius:999px;background:rgba(21,40,31,.08);color:var(--ink);border:none;cursor:pointer;font-size:13px;font-family:inherit;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@empty
<div style="background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.08);padding:40px;text-align:center;">
    <i class="fa-solid fa-circle-check" style="font-size:36px;color:#10B981;margin-bottom:12px;"></i>
    <p style="font-size:15px;color:var(--muted);">No pending payments — all clear!</p>
</div>
@endforelse

{{ $payments->links() }}

{{-- Recently Verified --}}
@if($recentlyVerified->count())
<h2 style="font-size:17px;font-weight:700;color:var(--ink);margin-top:32px;margin-bottom:14px;">
    <i class="fa-solid fa-clock-rotate-left" style="color:var(--muted);margin-right:6px;"></i>
    Recent Activity (Last 24 Hours)
</h2>
<div style="background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.08);overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#F9FAFB;border-bottom:1px solid rgba(21,40,31,.06);">
                    <th style="padding:12px 16px;text-align:left;font-weight:700;color:var(--muted);text-transform:uppercase;font-size:11px;letter-spacing:.05em;">Verified At</th>
                    <th style="padding:12px 16px;text-align:left;font-weight:700;color:var(--muted);text-transform:uppercase;font-size:11px;letter-spacing:.05em;">Student</th>
                    <th style="padding:12px 16px;text-align:left;font-weight:700;color:var(--muted);text-transform:uppercase;font-size:11px;letter-spacing:.05em;">Amount</th>
                    <th style="padding:12px 16px;text-align:left;font-weight:700;color:var(--muted);text-transform:uppercase;font-size:11px;letter-spacing:.05em;">Code</th>
                    <th style="padding:12px 16px;text-align:left;font-weight:700;color:var(--muted);text-transform:uppercase;font-size:11px;letter-spacing:.05em;">Status</th>
                    <th style="padding:12px 16px;text-align:left;font-weight:700;color:var(--muted);text-transform:uppercase;font-size:11px;letter-spacing:.05em;">Verified By</th>
                    <th style="padding:12px 16px;text-align:left;font-weight:700;color:var(--muted);text-transform:uppercase;font-size:11px;letter-spacing:.05em;">Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentlyVerified as $pmt)
                <tr style="border-bottom:1px solid rgba(21,40,31,.04);">
                    <td style="padding:12px 16px;color:var(--muted);">{{ $pmt->verified_at?->format('d M, g:ia') }}</td>
                    <td style="padding:12px 16px;font-weight:600;color:var(--ink);">{{ $pmt->user->name ?? '—' }}</td>
                    <td style="padding:12px 16px;">KSh {{ number_format($pmt->amount, 2) }}</td>
                    <td style="padding:12px 16px;font-family:monospace;font-weight:700;color:#0A64BC;">{{ $pmt->mpesa_code ?? '—' }}</td>
                    <td style="padding:12px 16px;">
                        @if($pmt->status === 'success')
                            <span class="badge badge-success"><i class="fa-solid fa-check"></i> Verified</span>
                        @else
                            <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> Rejected</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;color:var(--muted);">{{ $pmt->verifiedBy->name ?? '—' }}</td>
                    <td style="padding:12px 16px;color:var(--muted);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $pmt->admin_note ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
