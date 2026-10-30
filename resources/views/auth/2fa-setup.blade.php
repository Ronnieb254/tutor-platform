<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Enable Two-Factor Authentication – Mwalimu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
:root{
    --ink:#15281F;
    --cream:#FBF5E6;
    --trust-blue:#0A64BC;
    --coral:#E8522A;
    --mint:#10B981;
    --muted:rgba(21,40,31,.55);
    --hair:rgba(21,40,31,.1);
}

*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:'DM Sans',sans-serif;color:var(--ink);}

.container{
    min-height:100vh;
    background:linear-gradient(135deg,#F2F5F2 0%,#E8F0E8 100%);
    display:flex;
    align-items:center;
    justify-content:center;
    padding:40px 20px;
}

.card{
    background:#fff;
    border-radius:16px;
    box-shadow:0 8px 32px rgba(21,40,31,.08);
    max-width:540px;
    width:100%;
    padding:48px;
}

.header{
    text-align:center;
    margin-bottom:36px;
}

.header h1{
    font-family:'Fraunces',serif;
    font-size:28px;
    font-weight:800;
    color:var(--ink);
    margin-bottom:12px;
}

.header p{
    font-size:15px;
    color:var(--muted);
    line-height:1.6;
}

.section{
    margin-bottom:32px;
}

.section-title{
    font-size:16px;
    font-weight:700;
    color:var(--ink);
    margin-bottom:16px;
    display:flex;
    align-items:center;
    gap:8px;
}

.badge{
    background:var(--trust-blue);
    color:#fff;
    font-size:12px;
    font-weight:700;
    padding:4px 10px;
    border-radius:6px;
}

.qr-container{
    text-align:center;
    padding:24px;
    background:var(--cream);
    border-radius:12px;
    margin-bottom:16px;
}

.qr-container img{
    max-width:200px;
    width:100%;
    height:auto;
    margin:0 auto;
}

.instruction{
    font-size:14px;
    color:var(--muted);
    line-height:1.6;
    padding:16px;
    background:rgba(10,100,188,.05);
    border-radius:10px;
    border-left:4px solid var(--trust-blue);
}

.recovery-codes{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
    margin-bottom:16px;
}

.recovery-code{
    font-family:'Courier New',monospace;
    font-size:14px;
    font-weight:600;
    color:var(--ink);
    background:var(--cream);
    padding:12px;
    border-radius:8px;
    text-align:center;
}

.btn-copy{
    width:100%;
    padding:12px;
    background:#fff;
    border:2px solid var(--trust-blue);
    color:var(--trust-blue);
    font-size:14px;
    font-weight:700;
    border-radius:10px;
    cursor:pointer;
    transition:all .2s;
    margin-bottom:12px;
}

.btn-copy:hover{
    background:var(--trust-blue);
    color:#fff;
}

.warning{
    font-size:13px;
    color:var(--coral);
    padding:12px;
    background:rgba(232,82,42,.08);
    border-radius:8px;
    border-left:3px solid var(--coral);
}

.form-group{
    margin-bottom:24px;
}

.form-label{
    display:block;
    font-size:14px;
    font-weight:600;
    color:var(--ink);
    margin-bottom:8px;
}

.form-input{
    width:100%;
    padding:14px 16px;
    border:2px solid var(--hair);
    border-radius:12px;
    font-size:15px;
    font-family:'DM Sans',sans-serif;
    color:var(--ink);
    background:#fff;
    transition:all .2s;
    text-align:center;
    font-weight:600;
    letter-spacing:8px;
}

.form-input:focus{
    outline:none;
    border-color:var(--trust-blue);
    box-shadow:0 0 0 4px rgba(10,100,188,.08);
}

.error-msg{
    display:block;
    margin-top:6px;
    font-size:13px;
    color:var(--coral);
}

.btn-submit{
    width:100%;
    padding:16px;
    background:linear-gradient(135deg,var(--trust-blue) 0%,#5B21B6 100%);
    color:#fff;
    font-size:16px;
    font-weight:700;
    border:none;
    border-radius:12px;
    cursor:pointer;
    transition:all .2s;
    box-shadow:0 4px 14px rgba(10,100,188,.25);
}

.btn-submit:hover{
    transform:translateY(-2px);
    box-shadow:0 6px 20px rgba(10,100,188,.35);
}

.btn-submit:active{
    transform:translateY(0);
}

@media(max-width:640px){
    .card{
        padding:32px 24px;
    }
    .recovery-codes{
        grid-template-columns:1fr;
    }
}
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="header">
            <h1>Enable 2FA</h1>
            <p>Secure your account with two-factor authentication using an authenticator app</p>
        </div>

        <form action="{{ route('2fa.setup') }}" method="POST">
            @csrf
            <input type="hidden" name="secret" value="{{ $secret }}">
            <input type="hidden" name="recovery_codes" value="{{ json_encode($recoveryCodes) }}">

            {{-- Step 1: Scan QR Code --}}
            <div class="section">
                <div class="section-title">
                    <span class="badge">STEP 1</span>
                    <span>Scan QR Code</span>
                </div>
                <div class="qr-container">
                    <img src="{{ $qrCodeInline }}" alt="QR Code">
                </div>
                <div class="instruction">
                    Open your authenticator app (Google Authenticator, Authy, or similar) and scan this QR code to add your Mwalimu account.
                </div>
            </div>

            {{-- Step 2: Save Recovery Codes --}}
            <div class="section">
                <div class="section-title">
                    <span class="badge">STEP 2</span>
                    <span>Save Recovery Codes</span>
                </div>
                <div class="recovery-codes">
                    @foreach($recoveryCodes as $code)
                        <div class="recovery-code">{{ $code }}</div>
                    @endforeach
                </div>
                <button type="button" class="btn-copy" onclick="copyRecoveryCodes()">
                    📋 Copy All Recovery Codes
                </button>
                <div class="warning">
                    ⚠️ Save these recovery codes in a secure location. You'll need them if you lose access to your authenticator app.
                </div>
            </div>

            {{-- Step 3: Verify Code --}}
            <div class="section">
                <div class="section-title">
                    <span class="badge">STEP 3</span>
                    <span>Verify Code</span>
                </div>
                <div class="form-group">
                    <label for="code" class="form-label">Enter the 6-digit code from your app</label>
                    <input 
                        type="text" 
                        id="code" 
                        name="code" 
                        class="form-input" 
                        placeholder="000000"
                        maxlength="6"
                        pattern="[0-9]{6}"
                        required
                        autofocus>
                    @error('code')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn-submit">
                ✓ Enable Two-Factor Authentication
            </button>
        </form>
    </div>
</div>

<script>
function copyRecoveryCodes() {
    const codes = @json($recoveryCodes);
    const text = codes.join('\n');
    
    navigator.clipboard.writeText(text).then(() => {
        alert('Recovery codes copied to clipboard!');
    }).catch(() => {
        alert('Failed to copy. Please copy manually.');
    });
}
</script>

</body>
</html>
