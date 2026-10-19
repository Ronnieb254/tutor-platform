<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Clinic Booking – Checkout</title>
<style>
:root{--ink:#15281F;--cream:#FBF5E6;--violet:#6D28D9;--coral:#E8522A;--gold:#D4A017;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);}
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
.total-row{font-size:18px!important;font-weight:700!important;color:var(--violet)!important;}

.clinic-info{background:rgba(109,40,217,.06);border-radius:12px;padding:16px;margin-bottom:24px;border-left:4px solid var(--violet);}
.clinic-title{font-weight:700;font-size:15px;color:var(--ink);margin-bottom:6px;}
.clinic-detail{font-size:13px;color:var(--muted);margin-bottom:3px;}
.clinic-detail i{color:var(--violet);margin-right:6px;width:14px;}

.payment-method{background:#fff;border:1.5px solid var(--hair);border-radius:12px;padding:16px;margin-bottom:20px;cursor:pointer;transition:all .2s;}
.payment-method:hover{border-color:var(--violet);background:rgba(109,40,217,.02);}
.payment-method.selected{border-color:var(--violet);background:rgba(109,40,217,.08);}
.payment-icon{font-size:24px;margin-bottom:8px;}
.payment-name{font-weight:600;font-size:14px;margin-bottom:2px;}
.payment-desc{font-size:12px;color:var(--muted);}

.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:14px 28px;border-radius:10px;font-size:14px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .15s;width:100%;}
.btn-primary{background:var(--violet);color:#fff;}
.btn-primary:hover{background:#4C1D95;transform:translateY(-1px);}
.btn-primary:disabled{background:rgba(21,40,31,.2);cursor:not-allowed;transform:none;}
.btn-secondary{background:#F4F6F4;color:var(--ink);border:1.5px solid var(--hair);}
.btn-secondary:hover{border-color:var(--violet);background:rgba(109,40,217,.02);}

.terms{font-size:12px;color:var(--muted);margin-bottom:20px;line-height:1.6;}
.terms a{color:var(--violet);font-weight:600;}

.error{background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;}
.success{background:#DCFCE7;color:#166534;border:1px solid #BBF7D0;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;}

.loading{display:none;text-align:center;padding:40px;}
.spinner{width:40px;height:40px;border:4px solid var(--hair);border-top-color:var(--violet);border-radius:50%;animation:spin .6s linear infinite;margin:0 auto;}
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
        <div class="error"><i class="fa-solid fa-circle-xmark" style="margin-right:8px;"></i> {{ session('error') }}</div>
        @endif

        {{-- Clinic Information --}}
        <div class="clinic-info">
            <div class="clinic-title">{{ $clinic->title }}</div>
            <div class="clinic-detail"><i class="fa-solid fa-calendar-check fa-xs"></i> {{ $clinic->scheduled_at->format('D, M j, Y \a\t g:ia') }}</div>
            <div class="clinic-detail"><i class="fa-solid fa-clock fa-xs"></i> {{ $clinic->duration_minutes }} minutes</div>
            <div class="clinic-detail"><i class="fa-solid fa-user fa-xs"></i> {{ $clinic->tutor->name }}</div>
            @if($clinic->subject)
            <div class="clinic-detail"><i class="fa-solid fa-book-open fa-xs"></i> {{ $clinic->subject->name }}</div>
            @endif
            @if($clinic->paper_type)
            <div class="clinic-detail"><i class="fa-solid fa-file-lines fa-xs"></i> {{ $clinic->paper_type }}</div>
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

        {{-- Payment Method Selection --}}
        <div style="margin-bottom:24px;">
            <h3 style="font-size:14px;font-weight:700;margin-bottom:12px;">Payment Method</h3>
            <div class="payment-method selected" data-method="paystack">
                <div class="payment-icon">💳</div>
                <div class="payment-name">M-Pesa / Card Payment</div>
                <div class="payment-desc">Secure payment via Paystack (M-Pesa, Visa, Mastercard)</div>
            </div>
        </div>

        {{-- Payment Form --}}
        <form id="paymentForm" method="POST" action="{{ route('clinic.pay', $clinic) }}">
            @csrf

            {{-- User Verification --}}
            <div style="background:#F9FAFB;border-radius:10px;padding:14px;margin-bottom:20px;font-size:13px;">
                <div style="color:var(--muted);margin-bottom:6px;">Logged in as:</div>
                <div style="font-weight:600;color:var(--ink);">{{ $user->name }} ({{ $user->email }})</div>
                <div style="font-size:12px;color:var(--muted);margin-top:4px;"><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();" style="color:var(--violet);">Sign out</a></div>
            </div>

            {{-- Terms & Conditions --}}
            <div class="terms">
                <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
                    <input type="checkbox" name="terms" required style="margin-top:3px;cursor:pointer;width:16px;height:16px;">
                    <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. I understand that this booking is non-refundable.</span>
                </label>
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fa-solid fa-lock fa-sm"></i> Pay KSh {{ number_format($clinic->price) }} & Confirm Booking
            </button>
            <a href="{{ route('examiner-clinic') }}" class="btn btn-secondary" style="margin-top:8px;">
                <i class="fa-solid fa-arrow-left fa-sm"></i> Back
            </a>
        </form>

        {{-- Loading State --}}
        <div class="loading" id="loadingState">
            <div class="spinner"></div>
            <p style="margin-top:16px;font-size:14px;color:var(--muted);">Processing your payment...</p>
        </div>

        {{-- Security Badge --}}
        <div style="text-align:center;margin-top:24px;font-size:12px;color:var(--muted);">
            <i class="fa-solid fa-shield-check" style="color:var(--mint);"></i> Your payment is secure and encrypted
        </div>
    </div>
</div>

<form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>

<script>
document.getElementById('paymentForm').addEventListener('submit', function() {
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('loadingState').style.display = 'block';
});

// Payment method selection (for future multi-method support)
document.querySelectorAll('.payment-method').forEach(el => {
    el.addEventListener('click', function() {
        document.querySelectorAll('.payment-method').forEach(e => e.classList.remove('selected'));
        this.classList.add('selected');
        document.querySelector('input[name="method"]').value = this.dataset.method;
    });
});
</script>

@include('partials.footer')
@livewireScripts
</body>
</html>
