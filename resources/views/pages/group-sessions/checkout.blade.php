<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Secure Your Seat - {{ $groupSession->title }}</title>
</head>
<body>
@include('partials.nav')

<div style="max-width:900px;margin:0 auto;padding:40px 24px;">
 {{-- Header --}}
 <div style="margin-bottom:40px;">
 <a href="{{ route('group-sessions.show', $groupSession) }}" style="display:inline-flex;align-items:center;gap:6px;color:#0A64BC;text-decoration:none;font-size:14px;font-weight:600;margin-bottom:16px;">
 <i class="fa-solid fa-arrow-left"></i> Back to Session
 </a>
 <h1 style="font-family:'Fraunces',serif;font-size:32px;font-weight:700;color:#15281F;margin:0;">Secure Your Seat</h1>
 <p style="color:rgba(21,40,31,.55);margin-top:8px;">Complete payment to reserve your spot in this group session</p>
 </div>

 {{-- Main Grid --}}
 <div style="display:grid;grid-template-columns:1fr 380px;gap:32px;" class="checkout-grid">
 
 {{-- Left: Session Details & Payment Form --}}
 <div>
 {{-- Session Card --}}
 <div style="background:#fff;border-radius:18px;border:1px solid rgba(21,40,31,.08);padding:24px;margin-bottom:24px;">
 <h2 style="font-family:'Fraunces',serif;font-size:20px;color:#15281F;margin:0 0 16px;">Session Details</h2>
 
 <div style="display:flex;gap:16px;padding-bottom:20px;border-bottom:1px solid rgba(21,40,31,.08);">
 <div style="width:80px;height:80px;background:linear-gradient(135deg,#0A64BC,#084BA6);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:32px;flex-shrink:0;">
 <i class="fa-solid fa-users"></i>
 </div>
 <div style="flex:1;">
 <h3 style="font-size:16px;font-weight:700;color:#15281F;margin:0 0 6px;">{{ $groupSession->title }}</h3>
 @if($groupSession->subject)
 <p style="font-size:13px;color:rgba(21,40,31,.55);margin:0;"><i class="fa-solid fa-book-open"></i> {{ $groupSession->subject->name }}</p>
 @endif
 @if($groupSession->tutor)
 <p style="font-size:13px;color:rgba(21,40,31,.55);margin:6px 0 0;"><i class="fa-solid fa-user"></i> {{ $groupSession->tutor->name }}</p>
 @endif
 </div>
 </div>

 <div style="padding-top:20px;">
 @foreach([
 ['Date & Time', $groupSession->scheduled_at->setTimezone('Africa/Nairobi')->format('D, M j, Y \a\t g:ia').' EAT'],
 ['Duration', $groupSession->duration_minutes . ' minutes'],
 ['Class Size', 'Max ' . $groupSession->max_students . ' students'],
 ['Enrollment', $groupSession->enrolledCount() . '/' . $groupSession->max_students . ' enrolled'],
 ] as [$label, $value])
 <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid rgba(21,40,31,.06);">
 <span style="font-size:13px;color:rgba(21,40,31,.55);">{{ $label }}</span>
 <strong style="font-size:13px;color:#15281F;">{{ $value }}</strong>
 </div>
 @endforeach
 </div>
 </div>

 {{-- Pay with Paystack --}}
 <div style="background:rgba(10,100,188,.02);border:1px solid rgba(10,100,188,.1);border-radius:18px;padding:24px;">
 <h2 style="font-family:'Fraunces',serif;font-size:18px;color:#15281F;margin:0 0 18px;display:flex;align-items:center;gap:10px;">
 <i class="fa-solid fa-credit-card" style="color:#0A64BC;"></i> Secure Payment
 </h2>

 {{--
 ══════════════════════════════════════════════════════════════════
 MANUAL M-PESA PAYMENT COMMENTED OUT — Paystack integration active
 ══════════════════════════════════════════════════════════════════

 <form method="POST" action="{{ route('group-sessions.enroll', $groupSession) }}" id="enrollForm" style="display:flex;flex-direction:column;gap:16px;">
 @csrf
 <div>
 <label>Your M-Pesa Phone Number</label>
 <input type="tel" name="phone" placeholder="254712345678" value="{{ old('phone') }}" required />
 @error('phone')<span>{{ $message }}</span>@enderror
 </div>
 <div>
 <label>M-Pesa Transaction Code</label>
 <input type="text" name="mpesa_code" placeholder="e.g., ABC123DEF456" value="{{ old('mpesa_code') }}" required />
 @error('mpesa_code')<span>{{ $message }}</span>@enderror
 </div>
 <div>How to pay: M-Pesa → Lipa Na M-Pesa Online → Business 542542 → Account 313126 → Amount → PIN</div>
 <button type="submit">Submit Payment Code</button>
 </form>
 --}}

 {{-- Paystack redirect form --}}
 <div style="background:#fff;border-radius:14px;padding:20px;border:1px solid rgba(10,100,188,.12);text-align:center;margin-bottom:16px;">
 <i class="fa-solid fa-shield-halved" style="font-size:32px;color:#0A64BC;margin-bottom:12px;display:block;"></i>
 <p style="font-size:14px;font-weight:700;color:#15281F;margin:0 0 6px;">Pay securely with Paystack</p>
 <p style="font-size:12px;color:rgba(21,40,31,.55);margin:0 0 20px;line-height:1.5;">
 You'll be redirected to Paystack's secure checkout to pay <strong>KSh {{ number_format($groupSession->price) }}</strong>.<br>
 Accepts Visa, Mastercard, and M-Pesa.
 </p>
 @auth
 <form method="POST" action="{{ route('group-sessions.enroll', $groupSession) }}" id="enrollForm">
 @csrf
 <button type="submit" id="paystackBtn"
 style="padding:14px 28px;border-radius:10px;background:#0A64BC;color:#fff;font-weight:700;font-size:15px;border:none;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:10px;min-width:220px;justify-content:center;"
 onmouseover="this.style.background='#084BA6'"
 onmouseout="this.style.background='#0A64BC'">
 <i class="fa-solid fa-lock"></i> Pay KSh {{ number_format($groupSession->price) }}
 </button>
 </form>
 @else
 <a href="{{ route('login') }}" style="display:inline-flex;align-items:center;gap:8px;padding:14px 28px;border-radius:10px;background:#0A64BC;color:#fff;font-weight:700;font-size:15px;text-decoration:none;">
 <i class="fa-solid fa-right-to-bracket"></i> Sign in to Pay
 </a>
 @endauth
 </div>

 <div style="background:rgba(16,185,129,.05);border:1px solid rgba(16,185,129,.2);border-radius:12px;padding:12px;display:flex;align-items:flex-start;gap:10px;">
 <i class="fa-solid fa-circle-check" style="color:#10B981;flex-shrink:0;margin-top:2px;"></i>
 <span style="font-size:12px;color:#047857;"><strong>Instant confirmation:</strong> Your session code is sent immediately after payment.</span>
 </div>
 </div>
 </div>

 {{-- Right: Summary --}}
 <div>
 <div style="background:#fff;border-radius:18px;border:1px solid rgba(21,40,31,.08);padding:24px;position:sticky;top:20px;">
 <h3 style="font-size:15px;font-weight:700;color:#15281F;margin:0 0 20px;">Order Summary</h3>

 <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid rgba(21,40,31,.08);font-size:14px;">
 <span style="color:rgba(21,40,31,.55);">Group Session (1x)</span>
 <strong style="color:#15281F;">KSh {{ number_format($groupSession->price) }}</strong>
 </div>

 <div style="display:flex;justify-content:space-between;padding:20px 0;border-top:1px solid rgba(21,40,31,.08);font-size:16px;">
 <span style="font-weight:700;color:#15281F;">Total Due</span>
 <strong style="font-family:'Fraunces',serif;font-size:24px;color:#EEBE15;">KSh {{ number_format($groupSession->price) }}</strong>
 </div>

 @auth
 <div style="background:rgba(10,100,188,.05);border-radius:12px;padding:12px;margin-top:20px;text-align:center;font-size:12px;color:#0A64BC;">
 <i class="fa-solid fa-user"></i> {{ auth()->user()->name }}
 </div>

 <div style="background:rgba(212,160,23,.05);border:1px solid rgba(212,160,23,.2);border-radius:12px;padding:12px;margin-top:12px;text-align:center;font-size:11px;color:#B45309;">
 <i class="fa-solid fa-bolt"></i> Instant confirmation via Paystack
 </div>
 @else
 <a href="{{ route('register') }}" style="display:block;width:100%;padding:12px;border-radius:10px;background:#0A64BC;color:#fff;text-decoration:none;text-align:center;font-weight:700;font-size:14px;margin-top:20px;transition:all .2s;" onmouseover="this.style.background='#084BA6'" onmouseout="this.style.background='#0A64BC'">
 Sign Up to Continue
 </a>
 @endauth
 </div>
 </div>
 </div>
</div>

<style>
@media(max-width:768px) {
 .checkout-grid {
 grid-template-columns: 1fr !important;
 gap: 20px !important;
 }
 
 .checkout-grid > div:last-child {
 position: static !important;
 }
}
</style>

@include('partials.footer')
@livewireScripts
</body>
</html>

<style>
@media(max-width:768px) {
 .checkout-grid {
 grid-template-columns: 1fr !important;
 gap: 20px !important;
 }
 
 .checkout-grid > div:last-child {
 position: static !important;
 }
}
</style>

@include('partials.footer')
@livewireScripts
</body>
</html>
