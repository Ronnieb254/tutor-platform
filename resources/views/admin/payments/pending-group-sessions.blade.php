@extends('layouts.app')

@section('content')
<div style="max-width:1200px;margin:0 auto;padding:24px;">
 <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;flex-wrap:wrap;gap:16px;">
 <div>
 <h1 style="font-family:'Fraunces',serif;font-size:32px;font-weight:700;color:#15281F;margin:0 0 6px;">Group Session Payments</h1>
 <p style="color:rgba(21,40,31,.55);margin:0;">Pending verification</p>
 </div>
 <span style="background:#FEE2E2;color:#991B1B;padding:8px 16px;border-radius:999px;font-weight:600;font-size:14px;">
 {{ $payments->total() }} pending
 </span>
 </div>

 @if($payments->isEmpty())
 <div style="background:#fff;border-radius:12px;border:1px solid rgba(21,40,31,.08);padding:48px;text-align:center;">
 <div style="font-size:48px;color:#10B981;margin-bottom:16px;"><i class="fa-solid fa-check-circle"></i></div>
 <h3 style="font-size:18px;font-weight:700;color:#15281F;margin:0 0 8px;">All caught up!</h3>
 <p style="color:rgba(21,40,31,.55);margin:0;">No pending group session payments to verify.</p>
 </div>
 @else
 <div style="background:#fff;border-radius:12px;border:1px solid rgba(21,40,31,.08);overflow:hidden;">
 <div style="overflow-x:auto;">
 <table style="width:100%;border-collapse:collapse;">
 <thead>
 <tr style="background:#F9FAFB;border-bottom:1px solid rgba(21,40,31,.08);">
 <th style="padding:16px;text-align:left;font-size:12px;font-weight:700;color:rgba(21,40,31,.55);text-transform:uppercase;letter-spacing:.07em;">Student</th>
 <th style="padding:16px;text-align:left;font-size:12px;font-weight:700;color:rgba(21,40,31,.55);text-transform:uppercase;letter-spacing:.07em;">Session</th>
 <th style="padding:16px;text-align:left;font-size:12px;font-weight:700;color:rgba(21,40,31,.55);text-transform:uppercase;letter-spacing:.07em;">Amount</th>
 <th style="padding:16px;text-align:left;font-size:12px;font-weight:700;color:rgba(21,40,31,.55);text-transform:uppercase;letter-spacing:.07em;">M-Pesa Code</th>
 <th style="padding:16px;text-align:left;font-size:12px;font-weight:700;color:rgba(21,40,31,.55);text-transform:uppercase;letter-spacing:.07em;">Reference</th>
 <th style="padding:16px;text-align:left;font-size:12px;font-weight:700;color:rgba(21,40,31,.55);text-transform:uppercase;letter-spacing:.07em;">Submitted</th>
 <th style="padding:16px;text-align:center;font-size:12px;font-weight:700;color:rgba(21,40,31,.55);text-transform:uppercase;letter-spacing:.07em;">Action</th>
 </tr>
 </thead>
 <tbody>
 @foreach($payments as $payment)
 <tr style="border-bottom:1px solid rgba(21,40,31,.08);hover:background:#F9FAFB;">
 <td style="padding:16px;">
 <div style="font-weight:700;color:#15281F;font-size:13px;">{{ $payment->user->name }}</div>
 <div style="font-size:12px;color:rgba(21,40,31,.55);margin-top:4px;">{{ $payment->user->email }}</div>
 </td>
 <td style="padding:16px;">
 <div style="font-weight:700;color:#15281F;font-size:13px;">{{ $payment->clinic->title }}</div>
 <div style="font-size:12px;color:rgba(21,40,31,.55);margin-top:4px;">{{ $payment->clinic->scheduled_at->format('D, M j g:ia') }}</div>
 </td>
 <td style="padding:16px;">
 <span style="font-weight:700;color:#15281F;font-size:13px;">KSh {{ number_format($payment->amount) }}</span>
 </td>
 <td style="padding:16px;">
 <span style="background:#F3F4F6;color:#15281F;padding:6px 12px;border-radius:6px;font-family:monospace;font-size:12px;font-weight:600;">{{ $payment->mpesa_code }}</span>
 </td>
 <td style="padding:16px;">
 <span style="font-family:monospace;font-size:11px;color:rgba(21,40,31,.55);">{{ $payment->mpesa_reference }}</span>
 </td>
 <td style="padding:16px;">
 <span style="font-size:12px;color:rgba(21,40,31,.55);">{{ $payment->created_at->format('M j, H:i') }}</span>
 </td>
 <td style="padding:16px;text-align:center;">
 <button onclick="openVerifyModal({{ $payment->id }}, '{{ $payment->mpesa_code }}', '{{ $payment->user->name }}')" style="padding:8px 16px;border-radius:6px;background:#10B981;color:#fff;border:none;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10B981'">
 <i class="fa-solid fa-check"></i> Verify
 </button>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>

 {{-- Pagination --}}
 @if($payments->hasPages())
 <div style="padding:20px;display:flex;justify-content:center;gap:8px;border-top:1px solid rgba(21,40,31,.08);">
 @if($payments->onFirstPage())
 <span style="padding:8px 16px;border-radius:6px;background:#F3F4F6;color:rgba(21,40,31,.35);font-size:13px;border:none;"><i class="fa-solid fa-chevron-left"></i> Prev</span>
 @else
 <a href="{{ $payments->previousPageUrl() }}" style="padding:8px 16px;border-radius:6px;background:#fff;color:#15281F;font-size:13px;border:1px solid rgba(21,40,31,.1);text-decoration:none;"><i class="fa-solid fa-chevron-left"></i> Prev</a>
 @endif
 
 @foreach($payments->getUrlRange(max(1, $payments->currentPage()-2), min($payments->lastPage(), $payments->currentPage()+2)) as $page => $url)
 <a href="{{ $url }}" style="padding:8px 16px;border-radius:6px;background:{{ $page == $payments->currentPage() ? '#0A64BC' : '#fff' }};color:{{ $page == $payments->currentPage() ? '#fff' : '#15281F' }};font-size:13px;font-weight:600;border:1px solid {{ $page == $payments->currentPage() ? '#0A64BC' : 'rgba(21,40,31,.1)' }};text-decoration:none;">{{ $page }}</a>
 @endforeach
 
 @if($payments->hasMorePages())
 <a href="{{ $payments->nextPageUrl() }}" style="padding:8px 16px;border-radius:6px;background:#fff;color:#15281F;font-size:13px;border:1px solid rgba(21,40,31,.1);text-decoration:none;">Next <i class="fa-solid fa-chevron-right"></i></a>
 @else
 <span style="padding:8px 16px;border-radius:6px;background:#F3F4F6;color:rgba(21,40,31,.35);font-size:13px;border:none;">Next <i class="fa-solid fa-chevron-right"></i></span>
 @endif
 </div>
 @endif
 </div>
 @endif
</div>

{{-- Verify Modal --}}
<div id="verifyModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1000;align-items:center;justify-content:center;padding:20px;overflow-y:auto;">
 <div style="background:#fff;border-radius:16px;width:100%;max-width:500px;padding:32px;box-shadow:0 25px 80px rgba(0,0,0,.25);">
 <h2 style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:#15281F;margin:0 0 24px;">Verify Payment</h2>

 <form method="POST" id="verifyForm" style="display:flex;flex-direction:column;gap:18px;">
 @csrf

 <div style="background:#F9FAFB;border-radius:10px;padding:16px;border:1px solid rgba(21,40,31,.08);">
 <p style="font-size:12px;color:rgba(21,40,31,.55);margin:0 0 8px;"><strong>Student:</strong></p>
 <p id="studentName" style="font-size:13px;font-weight:600;color:#15281F;margin:0;"></p>
 
 <p style="font-size:12px;color:rgba(21,40,31,.55);margin:12px 0 8px;"><strong>M-Pesa Code:</strong></p>
 <p id="mpesaCode" style="font-size:13px;font-weight:600;color:#0A64BC;font-family:monospace;margin:0;"></p>
 </div>

 <div>
 <label style="display:block;font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;">Action</label>
 <select name="action" style="width:100%;padding:12px 14px;border:1.5px solid rgba(21,40,31,.12);border-radius:8px;font-size:14px;outline:none;" onchange="updateActionText()" required>
 <option value="">-- Select Action --</option>
 <option value="approve">Approve Payment</option>
 <option value="reject">Reject Payment</option>
 </select>
 </div>

 <div id="notesDiv" style="display:none;">
 <label style="display:block;font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;">Notes (optional)</label>
 <textarea name="notes" style="width:100%;padding:12px 14px;border:1.5px solid rgba(21,40,31,.12);border-radius:8px;font-size:14px;outline:none;resize:none;min-height:80px;" placeholder="Reason for rejection..."></textarea>
 </div>

 <div style="background:rgba(10,100,188,.05);border:1px solid rgba(10,100,188,.1);border-radius:10px;padding:12px;display:none;id:infoBox;">
 <p id="infoText" style="font-size:12px;color:rgba(21,40,31,.7);margin:0;line-height:1.6;"></p>
 </div>

 <div style="display:flex;gap:12px;margin-top:12px;">
 <button type="button" onclick="closeVerifyModal()" style="flex:1;padding:12px 20px;border-radius:8px;background:#f3f4f6;color:#15281F;border:none;font-size:13px;font-weight:700;cursor:pointer;transition:all .2s;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
 Cancel
 </button>
 <button type="submit" style="flex:1;padding:12px 20px;border-radius:8px;background:#0A64BC;color:#fff;border:none;font-size:13px;font-weight:700;cursor:pointer;transition:all .2s;" onmouseover="this.style.background='#084BA6'" onmouseout="this.style.background='#0A64BC'">
 <i class="fa-solid fa-check"></i> Confirm
 </button>
 </div>
 </form>
 </div>
</div>

<script>
function openVerifyModal(paymentId, mpesaCode, studentName) {
 document.getElementById('verifyForm').action = '/admin/group-session-payments/' + paymentId + '/verify';
 document.getElementById('mpesaCode').textContent = mpesaCode;
 document.getElementById('studentName').textContent = studentName;
 document.getElementById('verifyModal').style.display = 'flex';
 document.body.style.overflow = 'hidden';
}

function closeVerifyModal() {
 document.getElementById('verifyModal').style.display = 'none';
 document.body.style.overflow = 'auto';
 document.getElementById('verifyForm').reset();
}

function updateActionText() {
 const action = document.querySelector('select[name="action"]').value;
 const notesDiv = document.getElementById('notesDiv');
 const infoBox = document.getElementById('infoBox').parentElement;
 
 if (action === 'reject') {
 notesDiv.style.display = 'block';
 } else {
 notesDiv.style.display = 'none';
 }
}

// Close modal on background click
document.getElementById('verifyModal')?.addEventListener('click', function(e) {
 if (e.target === this) closeVerifyModal();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
 if (e.key === 'Escape') closeVerifyModal();
});
</script>

@endsection
