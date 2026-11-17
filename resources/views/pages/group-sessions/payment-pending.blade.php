<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Payment Processing - {{ $groupSession->title }}</title>
</head>
<body>
@include('partials.nav')

<div style="max-width:600px;margin:0 auto;padding:60px 24px;">
    {{-- Header --}}
    <div style="text-align:center;margin-bottom:40px;">
        <div style="width:80px;height:80px;background:rgba(10,100,188,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:40px;color:#0A64BC;animation:pulse 2s infinite;">
            <i class="fa-solid fa-clock"></i>
        </div>
        <h1 style="font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:#15281F;margin:0 0 8px;">Payment Processing</h1>
        <p style="font-size:14px;color:rgba(21,40,31,.55);margin:0;">Your Paystack payment is being confirmed</p>
    </div>

    {{-- Main Content Card --}}
    <div style="background:#fff;border-radius:18px;border:1px solid rgba(21,40,31,.08);padding:32px;margin-bottom:24px;">

        {{-- Session Info --}}
        <div style="display:flex;align-items:center;gap:16px;padding-bottom:24px;border-bottom:1px solid rgba(21,40,31,.08);">
            <div style="width:60px;height:60px;background:linear-gradient(135deg,#0A64BC,#084BA6);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;flex-shrink:0;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <h3 style="font-size:15px;font-weight:700;color:#15281F;margin:0;">{{ $groupSession->title }}</h3>
                @if($groupSession->tutor)
                <p style="font-size:13px;color:rgba(21,40,31,.55);margin:6px 0 0;"><i class="fa-solid fa-user"></i> with {{ $groupSession->tutor->name }}</p>
                @endif
            </div>
        </div>

        {{-- Session Details --}}
        <div style="padding:24px 0;border-bottom:1px solid rgba(21,40,31,.08);">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;">
                <span style="font-size:13px;color:rgba(21,40,31,.55);"><i class="fa-solid fa-calendar"></i> Date & Time</span>
                <strong style="font-size:13px;color:#15281F;">{{ $groupSession->scheduled_at->setTimezone('Africa/Nairobi')->format('D, M j, Y g:ia') }} EAT</strong>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;">
                <span style="font-size:13px;color:rgba(21,40,31,.55);"><i class="fa-solid fa-clock"></i> Duration</span>
                <strong style="font-size:13px;color:#15281F;">{{ $groupSession->duration_minutes }} minutes</strong>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;">
                <span style="font-size:13px;color:rgba(21,40,31,.55);"><i class="fa-solid fa-ticket"></i> Amount</span>
                <strong style="font-size:13px;color:#15281F;">KSh {{ number_format($groupSession->price) }}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;">
                <span style="font-size:13px;color:rgba(21,40,31,.55);"><i class="fa-solid fa-key"></i> Reference</span>
                <strong style="font-size:12px;color:#0A64BC;font-family:monospace;">{{ $reference }}</strong>
            </div>
        </div>

        {{-- Status --}}
        <div style="padding:24px 0;">
            <div style="display:flex;gap:10px;align-items:flex-start;padding:12px;background:rgba(10,100,188,.05);border-radius:10px;margin-bottom:12px;">
                <i class="fa-solid fa-spinner fa-spin" style="color:#0A64BC;flex-shrink:0;margin-top:2px;font-size:16px;"></i>
                <div style="font-size:13px;color:rgba(21,40,31,.7);">
                    <strong>Verifying with Paystack:</strong> Your payment is being confirmed automatically. This usually takes a few seconds.
                </div>
            </div>
            <div style="display:flex;gap:10px;align-items:flex-start;padding:12px;background:rgba(16,185,129,.05);border-radius:10px;">
                <i class="fa-solid fa-bolt" style="color:#10B981;flex-shrink:0;margin-top:2px;font-size:16px;"></i>
                <div style="font-size:13px;color:rgba(21,40,31,.7);">
                    <strong>Instant session code:</strong> Once confirmed, your code is sent to your email immediately — no manual review needed.
                </div>
            </div>
        </div>
    </div>

    {{-- What Happens Next --}}
    <div style="background:rgba(10,100,188,.02);border:1px solid rgba(10,100,188,.1);border-radius:12px;padding:20px;margin-bottom:24px;">
        <h4 style="font-size:13px;font-weight:700;color:#15281F;margin:0 0 16px;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-arrow-right" style="color:#0A64BC;"></i> What Happens Next
        </h4>

        <div style="display:flex;gap:16px;margin-bottom:12px;">
            <div style="display:flex;flex-direction:column;align-items:center;flex-shrink:0;">
                <div style="width:32px;height:32px;background:#0A64BC;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:700;">1</div>
                <div style="width:2px;height:24px;background:rgba(10,100,188,.2);margin:4px 0;"></div>
            </div>
            <div style="padding-top:2px;">
                <p style="font-size:12px;font-weight:700;color:#15281F;margin:0;"><i class="fa-solid fa-shield-halved"></i> Paystack Auto-Confirms</p>
                <p style="font-size:11px;color:rgba(21,40,31,.55);margin:4px 0 0;">Payment verified instantly via Paystack's secure system</p>
            </div>
        </div>

        <div style="display:flex;gap:16px;margin-bottom:12px;">
            <div style="display:flex;flex-direction:column;align-items:center;flex-shrink:0;">
                <div style="width:32px;height:32px;background:#0A64BC;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:700;">2</div>
                <div style="width:2px;height:24px;background:rgba(10,100,188,.2);margin:4px 0;"></div>
            </div>
            <div style="padding-top:2px;">
                <p style="font-size:12px;font-weight:700;color:#15281F;margin:0;"><i class="fa-solid fa-bell"></i> Tutor Notified</p>
                <p style="font-size:11px;color:rgba(21,40,31,.55);margin:4px 0 0;">Instructor receives your enrollment confirmation</p>
            </div>
        </div>

        <div style="display:flex;gap:16px;">
            <div style="flex-shrink:0;">
                <div style="width:32px;height:32px;background:#10B981;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:700;">3</div>
            </div>
            <div style="padding-top:2px;">
                <p style="font-size:12px;font-weight:700;color:#15281F;margin:0;"><i class="fa-solid fa-envelope"></i> You Get Session Code</p>
                <p style="font-size:11px;color:rgba(21,40,31,.55);margin:4px 0 0;">Email with session code sent immediately</p>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:24px;">
        <a href="{{ route('group-sessions.my-sessions') }}"
           style="display:block;width:100%;padding:14px;border-radius:10px;background:#0A64BC;color:#fff;text-decoration:none;text-align:center;font-weight:700;font-size:14px;transition:all .2s;"
           onmouseover="this.style.background='#084BA6'" onmouseout="this.style.background='#0A64BC'">
            <i class="fa-solid fa-list"></i> Check My Sessions
        </a>
        <a href="{{ route('group-sessions.index') }}"
           style="display:block;width:100%;padding:12px;border-radius:10px;background:transparent;color:#0A64BC;text-decoration:none;text-align:center;font-weight:600;font-size:13px;border:1.5px solid rgba(10,100,188,.2);transition:all .2s;"
           onmouseover="this.style.borderColor='#0A64BC';this.style.background='rgba(10,100,188,.05)'"
           onmouseout="this.style.borderColor='rgba(10,100,188,.2)';this.style.background='transparent'">
            <i class="fa-solid fa-search"></i> Browse More Sessions
        </a>
    </div>

    {{-- Support --}}
    <div style="background:#F9FAFB;border-radius:10px;padding:16px;text-align:center;border:1px solid rgba(21,40,31,.06);">
        <p style="font-size:11px;color:rgba(21,40,31,.55);margin:0;line-height:1.6;">
            <strong>Payment not reflected?</strong> Contact us at <strong style="color:#0A64BC;">support@mwalimu.co.ke</strong><br>
            Reference: <span style="font-family:monospace;font-size:10px;">{{ $reference }}</span>
        </p>
    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.05); opacity: 0.8; }
}
</style>

@include('partials.footer')
@livewireScripts
</body>
</html>
