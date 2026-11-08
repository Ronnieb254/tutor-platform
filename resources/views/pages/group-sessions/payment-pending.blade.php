<!DOCTYPE html>
<html lang="en">
<head>
 @include('partials.head')
 <title>Waiting for Payment &ndash; Mwalimu</title>
 <style>
 .pending-card {
 max-width: 480px;
 margin: 80px auto;
 background: #fff;
 border-radius: 24px;
 padding: 48px 36px;
 box-shadow: 0 8px 40px rgba(21,40,31,.10);
 text-align: center;
 }
 .spinner {
 width: 64px; height: 64px;
 border: 5px solid rgba(10,100,188,.15);
 border-top-color: var(--trust-blue);
 border-radius: 50%;
 animation: spin 1s linear infinite;
 margin: 0 auto 24px;
 }
 @keyframes spin { to { transform: rotate(360deg); } }
 .status-icon { font-size: 56px; margin-bottom: 16px; }
 .btn-primary {
 display: inline-flex; align-items: center; justify-content: center; gap: 8px;
 padding: 14px 32px; border-radius: 999px;
 background: var(--trust-blue); color: #fff;
 font-weight: 700; font-size: 15px; text-decoration: none;
 border: none; cursor: pointer; transition: background .18s;
 }
 .btn-primary:hover { background: #120A8F; }
 </style>
</head>
<body style="background: var(--cream);">
@include('partials.nav')

<div class="w">
 <div class="pending-card" id="pendingCard">

 {{-- Waiting state --}}
 <div id="stateWaiting">
 <div class="spinner"></div>
 <h2 style="font-family:'Fraunces',serif;font-size:26px;color:var(--ink);margin-bottom:12px;">
 Waiting for M-Pesa&hellip;
 </h2>
 <p style="color:var(--muted);line-height:1.7;margin-bottom:24px;">
 An STK Push has been sent to your phone.
 Enter your M-Pesa PIN to complete the payment.
 </p>
 <div style="background:rgba(10,100,188,.06);border:1px solid rgba(10,100,188,.2);border-radius:14px;padding:16px;margin-bottom:24px;">
 <p style="font-size:12px;color:var(--muted);margin-bottom:4px;">Payment reference</p>
 <code style="font-size:14px;font-weight:700;color:var(--ink);">{{ $reference }}</code>
 </div>
 <p style="font-size:13px;color:var(--muted);">
 This page will update automatically. Do not refresh or close it.
 </p>
 </div>

 {{-- Success state (shown by JS) --}}
 <div id="stateSuccess" style="display:none;">
 <div class="status-icon"><i class="fa-solid fa-circle-check"></i></div>
 <h2 style="font-family:'Fraunces',serif;font-size:26px;color:var(--ink);margin-bottom:12px;">
 Payment Confirmed!
 </h2>
 <p style="color:var(--muted);margin-bottom:28px;">
 Your group session spot is confirmed.
 </p>
 <a href="{{ route('group-sessions.my-sessions') }}" class="btn-primary">
 <i class="fa-solid fa-circle-check"></i> View My Sessions
 </a>
 </div>

 {{-- Failed state (shown by JS) --}}
 <div id="stateFailed" style="display:none;">
 <div class="status-icon"><i class="fa-solid fa-circle-xmark"></i></div>
 <h2 style="font-family:'Fraunces',serif;font-size:26px;color:var(--ink);margin-bottom:12px;">
 Payment Not Completed
 </h2>
 <p id="failedMessage" style="color:var(--muted);margin-bottom:28px;">
 Your M-Pesa payment was not completed. Please try again.
 </p>
 <a href="{{ route('group-sessions.index') }}" class="btn-primary">
 Try Again
 </a>
 </div>

 </div>
</div>

<script>
(function () {
 const reference = @json($reference);
 const statusUrl = '/api/daraja/status?reference=' + encodeURIComponent(reference);
 const maxPolls = 60; // 5 minutes at 5-second intervals
 let polls = 0;

 function show(id) {
 ['stateWaiting','stateSuccess','stateFailed'].forEach(function(s) {
 document.getElementById(s).style.display = s === id ? '' : 'none';
 });
 }

 function poll() {
 if (polls >= maxPolls) {
 document.getElementById('failedMessage').textContent =
 'Payment confirmation timed out. If you were charged, please contact support with reference: ' + reference;
 show('stateFailed');
 return;
 }
 polls++;

 fetch(statusUrl, {
 headers: { 'X-Requested-With': 'XMLHttpRequest' }
 })
 .then(function(r) { return r.json(); })
 .then(function(data) {
 if (data.status === 'success') {
 show('stateSuccess');
 // Redirect after a short delay if redirect_url or booking_url is provided
 var target = data.redirect_url || data.booking_url || null;
 if (target) { setTimeout(function() { window.location.href = target; }, 2500); }
 } else if (data.status === 'failed') {
 document.getElementById('failedMessage').textContent =
 data.message || 'Your M-Pesa payment was not completed. Please try again.';
 show('stateFailed');
 } else {
 // Still pending - keep polling
 setTimeout(poll, 5000);
 }
 })
 .catch(function() {
 // Network error - retry
 setTimeout(poll, 5000);
 });
 }

 // Start polling after a 3-second grace period
 setTimeout(poll, 3000);
})();
</script>

@include('partials.footer')
</body>
</html>
