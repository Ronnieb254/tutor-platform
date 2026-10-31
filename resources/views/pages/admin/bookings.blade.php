@extends('layouts.admin')
@section('title', 'All Bookings')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <h2 style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);">All Bookings</h2>
        <p style="font-size:13px;color:var(--muted);margin-top:2px;">View, manage and confirm session bookings</p>
    </div>
    <div style="display:flex;align-items:center;gap:8px;">
        {{-- Pending count badge --}}
        @php $pendingCount = \App\Models\Booking::where('status','pending')->count(); @endphp
        @if($pendingCount > 0)
        <a href="?status=pending" style="display:inline-flex;align-items:center;gap:6px;background:rgba(238,190,21,.12);border:1px solid rgba(238,190,21,.3);color:#92700a;padding:7px 14px;border-radius:999px;font-size:13px;font-weight:700;text-decoration:none;">
            <span style="width:8px;height:8px;background:#EEBE15;border-radius:50%;"></span>
            {{ $pendingCount }} pending
        </a>
        @endif
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.bookings') }}" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px;align-items:flex-end;">
    <div>
        <label class="form-label">Status</label>
        <select name="status" class="form-input form-select" onchange="this.form.submit()">
            <option value="">All statuses</option>
            @foreach(['pending','confirmed','completed','cancelled'] as $s)
            <option value="{{ $s }}" {{ $status===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div style="flex:1;min-width:200px;">
        <label class="form-label">Search</label>
        <input type="text" name="search" value="{{ $search }}" placeholder="Student or tutor name…" class="form-input" style="height:38px;">
    </div>
    <button type="submit" class="btn btn-primary">Search</button>
    @if($search || $status)
    <a href="{{ route('admin.bookings') }}" class="btn btn-outline">Clear</a>
    @endif
</form>

{{-- Stats bar --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
    @foreach([
        ['pending','#EEBE15','Pending'],
        ['confirmed','#10B981','Confirmed'],
        ['completed','#0A64BC','Completed'],
        ['cancelled','#E8522A','Cancelled'],
    ] as [$s,$col,$lbl])
    @php $cnt = \App\Models\Booking::where('status',$s)->count(); @endphp
    <a href="?status={{ $s }}" style="background:#fff;border:1.5px solid {{ $s===$status ? $col : 'var(--hairline)' }};border-radius:12px;padding:14px 16px;text-align:center;display:block;text-decoration:none;">
        <div style="font-family:'Fraunces',serif;font-size:24px;font-weight:700;color:{{ $col }};">{{ $cnt }}</div>
        <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $lbl }}</div>
    </a>
    @endforeach
</div>

{{-- Bookings table --}}
<div class="card">
    <div style="padding:16px 24px;border-bottom:1px solid var(--hairline);display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:14px;color:var(--muted);">{{ $bookings->total() }} booking{{ $bookings->total()!==1?'s':'' }} found</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Tutor</th>
                    <th>Subject</th>
                    <th>Scheduled</th>
                    <th>Duration</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr id="booking-row-{{ $booking->uuid }}">
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="avatar" style="width:30px;height:30px;border-radius:8px;font-size:12px;background:#0A64BC;">{{ substr($booking->student->name,0,1) }}</div>
                            <div>
                                <div style="font-weight:600;font-size:13px;">{{ $booking->student->name }}</div>
                                <div style="font-size:11px;color:var(--muted);">{{ $booking->student->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:500;font-size:13px;">{{ $booking->tutor->name }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $booking->tutor->email }}</div>
                    </td>
                    <td style="color:var(--muted);">{{ $booking->subject?->name ?? '—' }}</td>
                    <td style="white-space:nowrap;font-size:13px;">
                        <div>{{ $booking->scheduled_at->format('M j, Y') }}</div>
                        <div style="color:var(--muted);font-size:11px;">{{ $booking->scheduled_at->format('g:i a') }} EAT</div>
                    </td>
                    <td style="font-size:13px;">{{ $booking->duration_minutes }} min</td>
                    <td style="font-weight:600;font-size:13px;">KSh {{ number_format($booking->total_amount) }}</td>
                    <td>
                        @php $sc = ['pending'=>'badge-amber','confirmed'=>'badge-green','completed'=>'badge-blue','cancelled'=>'badge-red'][$booking->status] ?? 'badge-gray'; @endphp
                        <span class="badge {{ $sc }}" id="status-{{ $booking->uuid }}">{{ ucfirst($booking->status) }}</span>
                        @if($booking->session_code_student)
                        <div style="font-family:monospace;font-size:10px;color:var(--muted);margin-top:3px;">{{ $booking->session_code_student }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                            <a href="{{ route('bookings.show', $booking->uuid) }}"
                               class="btn btn-outline btn-sm" style="font-size:11px;padding:5px 10px;">
                                View
                            </a>
                            @if($booking->status === 'pending')
                            <button onclick="confirmBooking('{{ $booking->uuid }}', this)"
                                    class="btn btn-sm confirm-btn-{{ $booking->uuid }}"
                                    style="background:#10B981;color:#fff;border:none;cursor:pointer;font-size:11px;padding:5px 10px;border-radius:8px;font-family:inherit;font-weight:700;white-space:nowrap;">
                                ✓ Confirm & Send Codes
                            </button>
                            @elseif($booking->status === 'confirmed')
                            <button onclick="resendCodes('{{ $booking->uuid }}', this)"
                                    class="btn btn-sm"
                                    style="background:rgba(10,100,188,.1);color:#0A64BC;border:1px solid rgba(10,100,188,.2);cursor:pointer;font-size:11px;padding:5px 10px;border-radius:8px;font-family:inherit;font-weight:700;white-space:nowrap;">
                                Resend Codes
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:40px;">No bookings found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())
    <div style="padding:16px 24px;border-top:1px solid var(--hairline);">{{ $bookings->withQueryString()->links() }}</div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function confirmBooking(uuid, btn) {
    if (!confirm('Confirm this booking?\n\nSession codes will be emailed and sent as in-app notifications to both the tutor and student.')) return;

    const originalHtml = btn.innerHTML;
    btn.innerHTML = '⏳ Confirming…';
    btn.disabled = true;
    btn.style.opacity = '0.7';

    fetch(`/bookings/${uuid}/force-confirm`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'ok') {
            btn.innerHTML = '✓ Confirmed!';
            btn.style.background = '#059669';
            btn.style.opacity = '1';

            // Update status badge
            const badge = document.getElementById(`status-${uuid}`);
            if (badge) {
                badge.className = 'badge badge-green';
                badge.textContent = 'Confirmed';
            }

            // Remove button after 2 seconds
            setTimeout(() => btn.remove(), 2000);

            // Show success toast
            showToast('✓ Booking confirmed! Codes emailed to tutor and student.', 'success');
        } else {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            btn.style.opacity = '1';
            showToast('Error: ' + (res.message || 'Failed to confirm booking.'), 'error');
        }
    })
    .catch(() => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        btn.style.opacity = '1';
        showToast('Network error. Please try again.', 'error');
    });
}

function resendCodes(uuid, btn) {
    if (!confirm('Resend session codes to the tutor and student?')) return;

    const originalHtml = btn.innerHTML;
    btn.innerHTML = '⏳ Sending…';
    btn.disabled = true;

    fetch(`/bookings/${uuid}/force-confirm`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
    .then(r => r.json())
    .then(res => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        if (res.status === 'ok') {
            showToast('✓ Session codes resent to tutor and student.', 'success');
        } else {
            showToast('Error: ' + (res.message || 'Failed to resend codes.'), 'error');
        }
    })
    .catch(() => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        showToast('Network error. Please try again.', 'error');
    });
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position:fixed;bottom:24px;right:24px;z-index:9999;
        background:${type==='success'?'#10B981':'#E8522A'};color:#fff;
        padding:12px 20px;border-radius:12px;font-size:14px;font-weight:600;
        box-shadow:0 4px 20px rgba(0,0,0,.15);max-width:380px;
        animation:slideIn .3s ease;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity .3s';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}
</script>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
@keyframes slideIn { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>
@endpush
