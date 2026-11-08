<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Clinic Booking - Checkout</title>
<style>
:root{--ink:#15281F;--cream:#FBF5E6;--trust-blue:#0A64BC;--coral:#E8522A;--gold:#EEBE15;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'DM Sans',sans-serif;color:var(--ink);background:#F2F5F2;}
a{color:inherit;text-decoration:none;}

.container{max-width:560px;margin:60px auto;padding:0 20px;}
.card{background:#fff;border-radius:16px;border:1px solid var(--hair);padding:32px;box-shadow:0 4px 16px rgba(21,40,31,.08);}
.title{font-family:'Fraunces',serif;font-size:28px;font-weight:700;margin-bottom:8px;}
.subtitle{font-size:14px;color:var(--muted);margin-bottom:28px;}

.summary{background:#F9FAFB;border-radius:12px;padding:20px;margin-bottom:28px;}
.summary-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;font-size:14px;}
.summary-row:last-child{margin-bottom:0;}
.summary-label{color:var(--muted);}
.summary-value{font-weight:600;color:var(--ink);}
.divider{height:1px;background:var(--hair);margin:16px 0;}
.total-row{font-size:18px!important;font-weight:700!important;color:var(--trust-blue)!important;}

.clinic-info{background:rgba(10,100,188,.06);border-radius:12px;padding:16px;margin-bottom:24px;border-left:4px solid var(--trust-blue);}
.clinic-title{font-weight:700;font-size:15px;color:var(--ink);margin-bottom:6px;}
.clinic-detail{font-size:13px;color:var(--muted);margin-bottom:3px;}
.clinic-detail i{color:var(--trust-blue);margin-right:6px;width:14px;}

.payment-method{background:#fff;border:1.5px solid var(--hair);border-radius:12px;padding:16px;margin-bottom:20px;cursor:pointer;transition:all .2s;}
.payment-method:hover{border-color:var(--trust-blue);background:rgba(10,100,188,.02);}
.payment-method.selected{border-color:var(--trust-blue);background:rgba(10,100,188,.08);}
.payment-icon{font-size:24px;margin-bottom:8px;}
.payment-name{font-weight:600;font-size:14px;margin-bottom:2px;}
.payment-desc{font-size:12px;color:var(--muted);}

.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:14px 28px;border-radius:10px;font-size:14px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .15s;width:100%;}
.btn-primary{background:var(--trust-blue);color:#fff;}
.btn-primary:hover{background:#120A8F;transform:translateY(-1px);}
.btn-primary:disabled{background:rgba(21,40,31,.2);cursor:not-allowed;transform:none;}
.btn-secondary{background:#F4F6F4;color:var(--ink);border:1.5px solid var(--hair);}
.btn-secondary:hover{border-color:var(--trust-blue);background:rgba(10,100,188,.02);}

.terms{font-size:12px;color:var(--muted);margin-bottom:20px;line-height:1.6;}
.terms a{color:var(--trust-blue);font-weight:600;}

.error{background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;}
.success{background:#DCFCE7;color:#166534;border:1px solid #BBF7D0;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;}

.loading{display:none;text-align:center;padding:40px;}
.spinner{width:40px;height:40px;border:4px solid var(--hair);border-top-color:var(--trust-blue);border-radius:50%;animation:spin .6s linear infinite;margin:0 auto;}
@keyframes spin{to{transform:rotate(360deg)}}
</style>
</head>
<body>
@include('partials.nav')

<div class="container">
 <div class="card">
 <h1 class="title">Clinic Booking</h1>
 <p class="subtitle">Complete your registration for the examiner's clinic</p>

 @if(session('error'))
 <div class="error"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
 @endif

 {{-- Clinic Information --}}
 <div class="clinic-info">
 <div class="clinic-title">{{ $clinic->title }}</div>
 <div class="clinic-detail"><i class="fa-solid fa-circle-check"></i> {{ $clinic->scheduled_at->format('D, M j, Y \a\t g:ia') }}</div>
 <div class="clinic-detail"><i class="fa-solid fa-clock"></i> {{ $clinic->duration_minutes }} minutes</div>
 <div class="clinic-detail"><i class="fa-solid fa-user"></i> {{ $clinic->tutor->name }}</div>
 @if($clinic->subject)
 <div class="clinic-detail"><span style="font-size:inherit;"><i class="fa-solid fa-book-open"></i></span> {{ $clinic->subject->name }}</div>
 @endif
 @if($clinic->paper_type)
 <div class="clinic-detail"><i class="fa-solid fa-file-lines"></i> {{ $clinic->paper_type }}</div>
 @endif
 </div>

 {{-- Order Summary --}}
 <div class="summary">
 <div class="summary-row">
 <span class="summary-label">Clinic Session</span>
 <span class="summary-value">KSh {{ number_format($clinic->price) }}</span>
 </div>
 <div class="divider"></div>
 <div class="summary-row total-row">
 <span>Total Payment</span>
 <span>KSh {{ number_format($clinic->price) }}</span>
 </div>
 </div>

 {{-- Payment Method --}}
 <div style="margin-bottom:24px;" x-data="{ method: 'mpesa' }">
 <h3 style="font-size:14px;font-weight:700;margin-bottom:12px;">Payment Method</h3>
 <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:16px;">
 <button type="button" @click="method='mpesa'"
 :style="method==='mpesa' ? 'border:2px solid #0A64BC;background:#EFF6FF;' : 'border:2px solid #D1D5DB;background:#fff;'"
 style="padding:12px 6px;border-radius:8px;cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:4px;font-size:12px;font-weight:600;">
 <span>📱</span><span>M-Pesa</span>
 </button>
 <button type="button" @click="method='airtel'"
 :style="method==='airtel' ? 'border:2px solid #DC2626;background:#FEF2F2;' : 'border:2px solid #D1D5DB;background:#fff;'"
 style="padding:12px 6px;border-radius:8px;cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:4px;font-size:12px;font-weight:600;">
 <span>📶</span><span>Airtel</span>
 </button>
 <button type="button" @click="method='card'"
 :style="method==='card' ? 'border:2px solid #059669;background:#ECFDF5;' : 'border:2px solid #D1D5DB;background:#fff;'"
 style="padding:12px 6px;border-radius:8px;cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:4px;font-size:12px;font-weight:600;">
 <span>💳</span><span>Card</span>
 </button>
 </div>

 {{-- Payment Form --}}
 <form id="paymentForm" method="POST" action="{{ route('clinic.pay', $clinic) }}">
 @csrf
 <input type="hidden" name="payment_method" :value="method" x-bind:value="method">

 {{-- M-Pesa / Airtel Phone --}}
 <div style="margin-bottom:18px;" x-show="method === 'mpesa' || method === 'airtel'">
 <label style="font-size:13px;font-weight:700;color:var(--ink);display:block;margin-bottom:6px;">
 <span style="font-size:inherit;"><i class="fa-solid fa-mobile-screen"></i></span>
 Phone Number
 </label>
 <input type="tel" name="phone"
 placeholder="e.g. 0712 345 678"
 :required="method === 'mpesa' || method === 'airtel'"
 style="width:100%;height:46px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);padding:0 14px;font-size:14px;font-family:inherit;outline:none;box-sizing:border-box;"
 onfocus="this.style.borderColor='var(--trust-blue)'"
 onblur="this.style.borderColor='rgba(21,40,31,.18)'">
 @error('phone')
 <p style="font-size:12px;color:var(--coral);margin-top:5px;">{{ $message }}</p>
 @enderror
 <p style="font-size:11px;color:var(--muted);margin-top:5px;">
 You'll receive an STK Push on this number. Enter your PIN to complete payment.
 </p>
 </div>

 {{-- Card notice --}}
 <div x-show="method === 'card'" style="padding:12px;background:#F0FDF4;border-radius:8px;border:1px solid #BBF7D0;font-size:13px;color:#065F46;margin-bottom:18px;">
 <p style="margin:0;">💳 You will be redirected to a secure card payment page after confirming.</p>
 </div>

 {{-- User Verification --}}
 <div style="background:#F9FAFB;border-radius:10px;padding:14px;margin-bottom:20px;font-size:13px;">
 <div style="color:var(--muted);margin-bottom:6px;">Logged in as:</div>
 <div style="font-weight:600;color:var(--ink);">{{ $user->name }} ({{ $user->email }})</div>
 <div style="font-size:12px;color:var(--muted);margin-top:4px;"><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();" style="color:var(--trust-blue);">Sign out</a></div>
 </div>

 {{-- Terms & Conditions --}}
 <div class="terms">
 <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
 <input type="checkbox" name="terms" required style="margin-top:3px;cursor:pointer;width:16px;height:16px;">
 <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. I understand this booking is subject to the Mwalimu cancellation policy.</span>
 </label>
 </div>

 {{-- Submit Button --}}
 <button type="submit" class="btn btn-primary" id="submitBtn">
 <i class="fa-solid fa-lock"></i> Pay KSh {{ number_format($clinic->price) }}
 </button>
 <a href="{{ route('examiner-clinic') }}" class="btn btn-secondary" style="margin-top:8px;">
 <span style="font-size:inherit;">←</span> Back
 </a>
 </form>

 {{-- Loading State --}}
 <div class="loading" id="loadingState">
 <div class="spinner"></div>
 <p style="margin-top:16px;font-size:14px;color:var(--muted);">Processing your payment...</p>
 </div>
 </div>

 {{-- Security Badge --}}
 <div style="text-align:center;margin-top:24px;font-size:12px;color:var(--muted);">
 <i class="fa-solid fa-shield-halved"></i> Your payment is secure and encrypted
 </div>
 </div>
</div>

<form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>

<script>
document.getElementById('paymentForm').addEventListener('submit', function() {
 document.getElementById('submitBtn').disabled = true;
 document.getElementById('loadingState').style.display = 'block';
});
</script>

@include('partials.footer')
@livewireScripts
</body>
</html>
