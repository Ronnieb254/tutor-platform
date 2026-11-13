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
 ['Date & Time', $groupSession->scheduled_at->format('D, M j, Y \a\t g:ia')],
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

 {{-- M-Pesa Payment Form --}}
 <div style="background:rgba(10,100,188,.02);border:1px solid rgba(10,100,188,.1);border-radius:18px;padding:24px;">
 <h2 style="font-family:'Fraunces',serif;font-size:18px;color:#15281F;margin:0 0 18px;display:flex;align-items:center;gap:10px;">
 <i class="fa-solid fa-mobile" style="color:#0A64BC;"></i> M-Pesa Payment
 </h2>

 <form method="POST" action="{{ route('group-sessions.enroll', $groupSession) }}" id="enrollForm" style="display:flex;flex-direction:column;gap:16px;">
 @csrf

 <div>
 <label style="display:block;font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;">Your M-Pesa Phone Number</label>
 <input 
  type="tel" 
  name="phone" 
  placeholder="254712345678" 
  value="{{ old('phone') }}"
  style="width:100%;padding:12px 14px;border:1.5px solid rgba(21,40,31,.1);border-radius:10px;font-size:14px;font-family:monospace;transition:all .2s;"
  onfocus="this.style.borderColor='#0A64BC';this.style.boxShadow='0 0 0 3px rgba(10,100,188,.1)'"
  onblur="this.style.borderColor='rgba(21,40,31,.1)';this.style.boxShadow='none'"
  required
 />
 @error('phone')
  <span style="font-size:12px;color:#DC2626;margin-top:6px;display:block;">{{ $message }}</span>
 @enderror
 <span style="font-size:11px;color:rgba(21,40,31,.55);margin-top:6px;display:block;">
  <i class="fa-solid fa-info-circle"></i> Format: 254712345678 (Kenya number)
 </span>
 </div>

 <div>
 <label style="display:block;font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;">M-Pesa Transaction Code</label>
 <input 
  type="text" 
  name="mpesa_code" 
  placeholder="e.g., ABC123DEF456" 
  value="{{ old('mpesa_code') }}"
  style="width:100%;padding:12px 14px;border:1.5px solid rgba(21,40,31,.1);border-radius:10px;font-size:14px;font-family:monospace;transition:all .2s;"
  onfocus="this.style.borderColor='#0A64BC';this.style.boxShadow='0 0 0 3px rgba(10,100,188,.1)'"
  onblur="this.style.borderColor='rgba(21,40,31,.1)';this.style.boxShadow='none'"
  required
 />
 @error('mpesa_code')
  <span style="font-size:12px;color:#DC2626;margin-top:6px;display:block;">{{ $message }}</span>
 @enderror
 <span style="font-size:11px;color:rgba(21,40,31,.55);margin-top:6px;display:block;">
  <i class="fa-solid fa-info-circle"></i> Enter the 10-digit code sent to your phone after payment
 </span>
 </div>

 <div style="background:#fff;border-radius:12px;padding:14px;border:1px solid rgba(10,100,188,.1);">
 <div style="font-size:12px;color:rgba(21,40,31,.55);line-height:1.6;">
 <p style="margin:0 0 10px;"><strong style="color:#15281F;">How to pay:</strong></p>
 <ol style="margin:0;padding-left:18px;line-height:1.8;">
  <li>Go to your M-Pesa menu</li>
  <li>Select <strong>Lipa Na M-Pesa Online</strong></li>
  <li>Enter Business: <strong>542542</strong></li>
  <li>Enter Account: <strong>313126</strong></li>
  <li>Enter Amount: <strong>KSh {{ number_format($groupSession->price) }}</strong></li>
  <li>Enter your M-Pesa PIN</li>
  <li>You'll receive a confirmation code - paste it above</li>
 </ol>
 </div>
 </div>

 <button 
  type="submit" 
  id="submitBtn"
  style="padding:14px;border-radius:10px;background:#0A64BC;color:#fff;font-weight:700;font-size:15px;border:none;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;"
  onmouseover="this.style.background='#084BA6'"
  onmouseout="this.style.background='#0A64BC'"
 >
  <i class="fa-solid fa-check-circle"></i> <span id="btnText">Submit Payment Code</span>
 </button>

 <a href="{{ route('group-sessions.show', $groupSession) }}" style="display:block;width:100%;padding:12px;border-radius:10px;background:transparent;color:#0A64BC;text-decoration:none;text-align:center;font-weight:600;font-size:13px;border:1.5px solid rgba(10,100,188,.2);cursor:pointer;transition:all .2s;" onmouseover="this.style.borderColor='#0A64BC';this.style.background='rgba(10,100,188,.05)'" onmouseout="this.style.borderColor='rgba(10,100,188,.2)';this.style.background='transparent'">
 Cancel
 </a>
 </form>

 <div style="background:rgba(16,185,129,.05);border:1px solid rgba(16,185,129,.2);border-radius:12px;padding:12px;margin-top:16px;display:flex;align-items:flex-start;gap:10px;">
 <i class="fa-solid fa-circle-check" style="color:#10B981;flex-shrink:0;margin-top:2px;"></i>
 <span style="font-size:12px;color:#047857;"><strong>Secure process:</strong> Our team will verify your payment quickly and send you a session code within 2 hours.</span>
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
 <i class="fa-solid fa-clock"></i> Verified within 2 hours
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
