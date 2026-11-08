<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Booking Received — Mwalimu</title>
<style>
body { background: #F2F5F2; }
.pending-wrap {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
}
.pending-card {
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 8px 40px rgba(21,40,31,.10);
    max-width: 520px;
    width: 100%;
    padding: 48px 40px;
    text-align: center;
}
@media(max-width:480px){ .pending-card { padding: 36px 24px; } }

.pulse-ring {
    width: 88px; height: 88px;
    border-radius: 50%;
    background: rgba(238,190,21,.12);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 28px;
    position: relative;
}
.pulse-ring::before {
    content: '';
    position: absolute;
    inset: -8px;
    border-radius: 50%;
    border: 2px solid rgba(238,190,21,.25);
    animation: pulse 2s ease-in-out infinite;
}
@keyframes pulse {
    0%   { transform: scale(1);   opacity: 1;   }
    50%  { transform: scale(1.1); opacity: .4;  }
    100% { transform: scale(1);   opacity: 1;   }
}

.step-list {
    list-style: none;
    padding: 0; margin: 0;
    text-align: left;
}
.step-list li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid rgba(21,40,31,.06);
    font-size: 13px;
    color: rgba(21,40,31,.65);
    line-height: 1.5;
}
.step-list li:last-child { border-bottom: none; }
.step-num {
    width: 22px; height: 22px;
    border-radius: 50%;
    background: rgba(10,100,188,.1);
    color: #0A64BC;
    font-size: 11px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; margin-top: 1px;
}
</style>
</head>
<body>
@include('partials.nav')

<div class="pending-wrap">
    <div class="pending-card">

        {{-- Pulsing icon --}}
        <div class="pulse-ring">
            <i class="fa-solid fa-hourglass-half" style="font-size:36px;color:#EEBE15;"></i>
        </div>

        {{-- Heading --}}
        <h1 style="font-family:'Fraunces',serif;font-size:28px;font-weight:800;color:#15281F;margin-bottom:10px;">
            Booking Received!
        </h1>
        <p style="font-size:15px;color:rgba(21,40,31,.55);margin-bottom:28px;line-height:1.7;">
            Your booking is pending approval.<br>
            Admin will verify your payment within <strong style="color:#15281F;">5 minutes</strong> and send your session codes by email.
        </p>

        {{-- Booking summary --}}
        @if(isset($booking))
        <div style="background:#F9FAF9;border-radius:14px;padding:18px;margin-bottom:24px;text-align:left;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.4);margin-bottom:12px;">Booking Summary</div>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <div style="display:flex;justify-content:space-between;font-size:13px;">
                    <span style="color:rgba(21,40,31,.55);">Tutor</span>
                    <span style="font-weight:600;color:#15281F;">{{ $booking->tutor->name ?? '—' }}</span>
                </div>
                @if($booking->subject)
                <div style="display:flex;justify-content:space-between;font-size:13px;">
                    <span style="color:rgba(21,40,31,.55);">Subject</span>
                    <span style="font-weight:600;color:#15281F;">{{ $booking->subject->name }}</span>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;font-size:13px;">
                    <span style="color:rgba(21,40,31,.55);">Session</span>
                    <span style="font-weight:600;color:#15281F;">{{ $booking->duration_minutes }}min · {{ $booking->scheduled_at->format('D, M j, Y') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;">
                    <span style="color:rgba(21,40,31,.55);">Time</span>
                    <span style="font-weight:600;color:#15281F;">{{ $booking->scheduled_at->setTimezone('Africa/Nairobi')->format('g:i a') }} EAT</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding-top:8px;border-top:1px solid rgba(21,40,31,.07);">
                    <span style="color:rgba(21,40,31,.55);">Amount Paid</span>
                    <span style="font-weight:800;color:#0A64BC;font-size:15px;">KSh {{ number_format($booking->total_amount) }}</span>
                </div>
                @if($payment)
                <div style="display:flex;justify-content:space-between;font-size:13px;">
                    <span style="color:rgba(21,40,31,.55);">Transaction Code</span>
                    <span style="font-family:monospace;font-weight:700;color:#15281F;letter-spacing:.05em;">{{ $payment->mpesa_code }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- What happens next --}}
        <div style="background:rgba(10,100,188,.04);border-radius:14px;padding:18px;margin-bottom:28px;text-align:left;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.4);margin-bottom:12px;">What happens next</div>
            <ul class="step-list">
                <li>
                    <span class="step-num">1</span>
                    Admin receives your booking and verifies the M-Pesa code against their statement.
                </li>
                <li>
                    <span class="step-num">2</span>
                    Once verified, you'll receive a <strong>confirmation email</strong> with your unique <strong>STU-XXXXXX</strong> session code.
                </li>
                <li>
                    <span class="step-num">3</span>
                    Your tutor also receives their <strong>TUT-XXXXXX</strong> code by email.
                </li>
                <li>
                    <span class="step-num">4</span>
                    On session day, go to your dashboard and enter your code to join the live class.
                </li>
            </ul>
        </div>

        {{-- Keep SMS notice --}}
        <div style="background:#FEF3C7;border-radius:12px;padding:14px 16px;font-size:13px;color:#92400E;margin-bottom:28px;text-align:left;">
            <i class="fa-solid fa-triangle-exclamation" style="margin-right:6px;"></i>
            <strong>Keep your M-Pesa SMS</strong> — your transaction code may be requested during verification.
        </div>

        {{-- Buttons --}}
        <div style="display:flex;flex-direction:column;gap:10px;">
            <a href="{{ route('dashboard') }}" style="display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:14px 28px;background:#0A64BC;color:#fff;border-radius:999px;font-weight:700;font-size:14px;text-decoration:none;transition:background .15s;" onmouseover="this.style.background='#0850a0'" onmouseout="this.style.background='#0A64BC'">
                <i class="fa-solid fa-gauge"></i> Go to My Dashboard
            </a>
            @if(isset($booking))
            <a href="{{ route('bookings.show', $booking->uuid) }}" style="display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 28px;background:#fff;color:#15281F;border:1.5px solid rgba(21,40,31,.15);border-radius:999px;font-weight:600;font-size:13px;text-decoration:none;">
                <i class="fa-solid fa-eye"></i> View Booking Details
            </a>
            @endif
        </div>

        {{-- Auto-refresh notice --}}
        <p style="font-size:11px;color:rgba(21,40,31,.35);margin-top:20px;">
            <i class="fa-solid fa-rotate-right"></i> This page will check for updates every 30 seconds.
        </p>

    </div>
</div>

@include('partials.footer')

<script>
// Poll for booking confirmation every 30 seconds
@if(isset($payment))
(function() {
    var paymentId = {{ $payment->id }};
    var pollCount = 0;
    var maxPolls  = 20; // 10 minutes

    function checkStatus() {
        if (pollCount >= maxPolls) return;
        pollCount++;

        fetch('/bookings/payment-status?payment_id=' + paymentId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.status === 'success' && res.booking_url) {
                window.location.href = res.booking_url;
            } else if (res.status === 'failed') {
                document.querySelector('.pulse-ring').innerHTML = '<i class="fa-solid fa-circle-xmark" style="font-size:36px;color:#EF4444;"></i>';
                document.querySelector('h1').textContent = 'Payment Not Verified';
                document.querySelector('h1').nextElementSibling.innerHTML =
                    'Your transaction code could not be verified. ' +
                    (res.admin_note ? '<br><strong>Reason:</strong> ' + res.admin_note + '<br>' : '') +
                    'Please contact support or try again.';
            } else {
                setTimeout(checkStatus, 30000);
            }
        })
        .catch(function() {
            setTimeout(checkStatus, 30000);
        });
    }

    setTimeout(checkStatus, 30000);
})();
@endif
</script>
</body>
</html>
