<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Your Account – Mwalimu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
:root{
    --ink:#15281F;
    --trust-blue:#0A64BC;
    --mint:#10B981;
    --coral:#E8522A;
    --gold:#EEBE15;
    --muted:rgba(21,40,31,.55);
    --hair:rgba(21,40,31,.1);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:'DM Sans',sans-serif;color:var(--ink);background:linear-gradient(135deg,#F2F5F2 0%,#E8F0E8 100%);}

.page{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;}

.card{background:#fff;border-radius:24px;border:1px solid var(--hair);padding:44px 40px;width:100%;max-width:480px;box-shadow:0 8px 40px rgba(21,40,31,.08);}

.logo{text-align:center;margin-bottom:32px;}
.logo-text{font-family:'Fraunces',serif;font-size:28px;font-weight:800;color:var(--ink);}
.logo-sub{font-size:13px;color:var(--muted);margin-top:2px;}

.icon-wrap{width:72px;height:72px;border-radius:20px;background:rgba(10,100,188,.08);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;}
.icon-wrap svg{width:34px;height:34px;stroke:var(--trust-blue);stroke-width:1.8;fill:none;}

h1{font-family:'Fraunces',serif;font-size:26px;font-weight:800;color:var(--ink);text-align:center;margin-bottom:8px;}
.subtitle{font-size:14px;color:var(--muted);text-align:center;line-height:1.65;margin-bottom:32px;}
.subtitle strong{color:var(--ink);}

/* Destination badge */
.dest-badge{display:inline-flex;align-items:center;gap:6px;background:rgba(10,100,188,.08);color:var(--trust-blue);border:1px solid rgba(10,100,188,.18);padding:5px 14px;border-radius:999px;font-size:13px;font-weight:600;margin-bottom:28px;}

/* Section label */
.section-label{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:10px;}

/* OTP input group */
.otp-group{display:flex;gap:10px;justify-content:center;margin-bottom:6px;}
.otp-input{
    width:52px;height:60px;
    border:2px solid var(--hair);border-radius:14px;
    text-align:center;font-size:24px;font-weight:700;color:var(--ink);
    font-family:'DM Sans',sans-serif;
    background:#fff;outline:none;
    transition:all .18s;
    -moz-appearance:textfield;
}
.otp-input::-webkit-outer-spin-button,
.otp-input::-webkit-inner-spin-button{-webkit-appearance:none;margin:0;}
.otp-input:focus{border-color:var(--trust-blue);box-shadow:0 0 0 4px rgba(10,100,188,.1);}
.otp-input.filled{border-color:var(--trust-blue);background:rgba(10,100,188,.04);}
.otp-input.error{border-color:var(--coral);background:rgba(232,82,42,.04);}
.otp-input.success{border-color:var(--mint);background:rgba(16,185,129,.04);}

.error-msg{font-size:13px;color:var(--coral);text-align:center;display:block;min-height:20px;margin-bottom:16px;}

/* Resend */
.resend-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;}
.resend-timer{font-size:13px;color:var(--muted);}
.resend-btn{font-size:13px;font-weight:600;color:var(--trust-blue);background:none;border:none;cursor:pointer;font-family:inherit;padding:0;transition:opacity .15s;}
.resend-btn:disabled{opacity:.4;cursor:not-allowed;}

/* Divider */
.divider{height:1px;background:var(--hair);margin:28px 0;}

/* Submit button */
.btn-submit{
    width:100%;height:52px;
    background:var(--trust-blue);color:#fff;
    font-size:15px;font-weight:700;
    border:none;border-radius:12px;cursor:pointer;
    font-family:inherit;transition:all .2s;
    display:flex;align-items:center;justify-content:center;gap:8px;
}
.btn-submit:hover{background:#0853a0;transform:translateY(-1px);}
.btn-submit:disabled{background:rgba(21,40,31,.2);cursor:not-allowed;transform:none;}

.back-link{display:block;text-align:center;margin-top:20px;font-size:13px;color:var(--muted);}
.back-link a{color:var(--trust-blue);font-weight:600;text-decoration:none;}

/* Flash */
.flash{padding:12px 16px;border-radius:12px;font-size:13.5px;font-weight:500;margin-bottom:20px;display:flex;align-items:flex-start;gap:8px;line-height:1.5;}
.flash-info{background:rgba(10,100,188,.08);color:var(--trust-blue);border:1px solid rgba(10,100,188,.18);}
.flash-ok{background:#DCFCE7;color:#166534;border:1px solid #BBF7D0;}
.flash-err{background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;}

/* Spinner */
@keyframes spin{to{transform:rotate(360deg)}}
.spinner{width:18px;height:18px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;display:none;}
.btn-submit.loading .spinner{display:block;}
.btn-submit.loading .btn-text{display:none;}
    </style>
</head>
<body>
<div class="page">
    <div class="card">

        {{-- Logo --}}
        <div class="logo">
            <div class="logo-text">Mwalimu</div>
            <div class="logo-sub">Account Verification</div>
        </div>

        {{-- Icon --}}
        <div class="icon-wrap">
            <svg viewBox="0 0 24 24">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
            </svg>
        </div>

        <h1>Verify your account</h1>
        <p class="subtitle">
            We sent a 6-digit code to<br>
            <strong>{{ $email }}</strong>
        </p>

        {{-- Flash messages --}}
        @if(session('info'))
        <div class="flash flash-info">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('info') }}
        </div>
        @endif
        @if(session('error'))
        <div class="flash flash-err">{{ session('error') }}</div>
        @endif

        {{-- Fallback: email delivery failed, show OTP on screen --}}
        @if(session('otp_fallback'))
        <div style="background:rgba(238,190,21,.1);border:2px dashed #EEBE15;border-radius:16px;padding:20px;margin-bottom:24px;text-align:center;">
            <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(21,40,31,.5);margin-bottom:8px;">
                ⚠️ Email delivery failed — use this code
            </div>
            <div style="font-family:monospace;font-size:38px;font-weight:800;color:#15281F;letter-spacing:10px;">
                {{ session('otp_fallback') }}
            </div>
            <div style="font-size:12px;color:rgba(21,40,31,.5);margin-top:8px;">
                Copy this code and enter it below. Valid for 10 minutes.
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('otp.verify') }}" id="otp-form">
            @csrf

            {{-- ── EMAIL OTP ── --}}
            <div style="margin-bottom:8px;">
                <div class="section-label">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    Email Code
                </div>
                <div class="otp-group" id="email-otp-group">
                    @for($i = 0; $i < 6; $i++)
                    <input type="number" maxlength="1" class="otp-input" data-group="email" data-index="{{ $i }}" min="0" max="9" autocomplete="off" inputmode="numeric" style="caret-color:transparent;">
                    @endfor
                </div>
                <input type="hidden" name="email_otp" id="email_otp_value">
                @error('email_otp')
                <span class="error-msg">{{ $message }}</span>
                @else
                <span class="error-msg" id="email-otp-error"></span>
                @enderror
            </div>

            {{-- Resend email code --}}
            <div class="resend-row">
                <span class="resend-timer" id="email-timer">Resend in <strong id="email-countdown">60</strong>s</span>
                <button type="button" class="resend-btn" id="resend-email-btn" disabled
                        onclick="resendOtp('email')">Resend email code</button>
            </div>

            @if($phone)
            {{-- ── PHONE OTP ── --}}
            <div class="divider"></div>
            <div style="margin-bottom:8px;">
                <div class="section-label">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 15.1 19.79 19.79 0 0 1 1.62 6.47 2 2 0 0 1 3.6 4h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 11.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    Phone Code (Optional) — {{ $phone }}
                </div>
                <div class="otp-group" id="phone-otp-group">
                    @for($i = 0; $i < 6; $i++)
                    <input type="number" maxlength="1" class="otp-input" data-group="phone" data-index="{{ $i }}" min="0" max="9" autocomplete="off" inputmode="numeric" style="caret-color:transparent;">
                    @endfor
                </div>
                <input type="hidden" name="phone_otp" id="phone_otp_value">
                @error('phone_otp')
                <span class="error-msg">{{ $message }}</span>
                @else
                <span class="error-msg" id="phone-otp-error"></span>
                @enderror
            </div>

            <div class="resend-row">
                <span class="resend-timer" id="phone-timer">Resend in <strong id="phone-countdown">60</strong>s</span>
                <button type="button" class="resend-btn" id="resend-phone-btn" disabled
                        onclick="resendOtp('phone')">Resend phone code</button>
            </div>
            @endif

            <div class="divider"></div>

            <button type="submit" class="btn-submit" id="submit-btn" disabled>
                <span class="btn-text">✓ Verify &amp; Create Account</span>
                <div class="spinner"></div>
            </button>
        </form>

        <p class="back-link">
            Wrong email? <a href="{{ route('register') }}">Start over</a>
        </p>
    </div>
</div>

<script>
// ── OTP input logic ──────────────────────────────────────────────────────────

function setupOtpInputs(group) {
    const inputs = document.querySelectorAll(`.otp-input[data-group="${group}"]`);
    const hiddenInput = document.getElementById(`${group}_otp_value`);

    inputs.forEach((input, idx) => {
        input.addEventListener('input', function () {
            // Allow only single digit
            this.value = this.value.replace(/[^0-9]/g, '').slice(-1);
            this.classList.toggle('filled', this.value !== '');

            // Collect all values into hidden input
            const val = Array.from(inputs).map(i => i.value).join('');
            hiddenInput.value = val;

            // Auto-advance
            if (this.value && idx < inputs.length - 1) {
                inputs[idx + 1].focus();
            }

            checkSubmitReady();
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace') {
                if (!this.value && idx > 0) {
                    inputs[idx - 1].value = '';
                    inputs[idx - 1].classList.remove('filled');
                    inputs[idx - 1].focus();
                    const val = Array.from(inputs).map(i => i.value).join('');
                    hiddenInput.value = val;
                    checkSubmitReady();
                }
            }
            // Prevent non-digit keys except control keys
            if (!/^[0-9]$/.test(e.key) && !['Backspace','Tab','ArrowLeft','ArrowRight','Delete'].includes(e.key)) {
                e.preventDefault();
            }
        });

        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
            pasted.split('').forEach((char, i) => {
                if (inputs[i]) {
                    inputs[i].value = char;
                    inputs[i].classList.add('filled');
                }
            });
            hiddenInput.value = pasted;
            const nextEmpty = Array.from(inputs).findIndex(i => !i.value);
            if (nextEmpty !== -1) inputs[nextEmpty].focus();
            else inputs[inputs.length - 1].focus();
            checkSubmitReady();
        });

        input.addEventListener('focus', () => input.select());
    });
}

function checkSubmitReady() {
    const emailVal = document.getElementById('email_otp_value').value;
    const emailReady = emailVal.length === 6;
    // Email code is sufficient — phone code is optional
    document.getElementById('submit-btn').disabled = !emailReady;
}

setupOtpInputs('email');
@if($phone)
setupOtpInputs('phone');
@endif

// ── Countdown timers ──────────────────────────────────────────────────────────

function startCountdown(countdownId, btnId, timerId, seconds) {
    let remaining = seconds;
    const countdownEl = document.getElementById(countdownId);
    const btn = document.getElementById(btnId);
    const timer = document.getElementById(timerId);

    btn.disabled = true;
    timer.style.display = '';

    const interval = setInterval(() => {
        remaining--;
        if (countdownEl) countdownEl.textContent = remaining;
        if (remaining <= 0) {
            clearInterval(interval);
            btn.disabled = false;
            timer.style.display = 'none';
        }
    }, 1000);
}

startCountdown('email-countdown', 'resend-email-btn', 'email-timer', 60);
@if($phone)
startCountdown('phone-countdown', 'resend-phone-btn', 'phone-timer', 60);
@endif

// ── Resend OTP ────────────────────────────────────────────────────────────────

function resendOtp(type) {
    const btn = document.getElementById(`resend-${type}-btn`);
    btn.disabled = true;
    btn.textContent = 'Sending…';

    fetch('{{ route("otp.resend") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ type }),
    })
    .then(r => r.json())
    .then(data => {
        btn.textContent = type === 'email' ? 'Resend email code' : 'Resend phone code';
        if (data.success) {
            // Clear the inputs
            document.querySelectorAll(`.otp-input[data-group="${type}"]`).forEach(i => {
                i.value = '';
                i.classList.remove('filled', 'error');
            });
            document.getElementById(`${type}_otp_value`).value = '';
            checkSubmitReady();
            startCountdown(`${type}-countdown`, `resend-${type}-btn`, `${type}-timer`, 60);

            // Show a brief success hint
            const errEl = document.getElementById(`${type}-otp-error`);
            if (errEl) {
                errEl.style.color = '#10B981';
                errEl.textContent = data.message;
                setTimeout(() => { errEl.textContent = ''; errEl.style.color = ''; }, 4000);
            }
        } else {
            alert(data.message || 'Failed to resend. Please try again.');
        }
    })
    .catch(() => {
        btn.textContent = type === 'email' ? 'Resend email code' : 'Resend phone code';
        btn.disabled = false;
        alert('Network error. Please try again.');
    });
}

// ── Form submit ───────────────────────────────────────────────────────────────

document.getElementById('otp-form').addEventListener('submit', function () {
    const btn = document.getElementById('submit-btn');
    btn.classList.add('loading');
    btn.disabled = true;
});
</script>
</body>
</html>
