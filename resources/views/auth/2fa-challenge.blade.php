<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Two-Factor Authentication – Mwalimu</title>
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
    max-width:440px;
    width:100%;
    padding:48px;
}

.header{
    text-align:center;
    margin-bottom:40px;
}

.icon-shield{
    width:64px;
    height:64px;
    background:linear-gradient(135deg,var(--trust-blue) 0%,#5B21B6 100%);
    border-radius:16px;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:0 auto 20px;
}

.icon-shield svg{
    width:32px;
    height:32px;
    stroke:#fff;
    stroke-width:2;
    fill:none;
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

.form-group{
    margin-bottom:24px;
}

.form-label{
    display:block;
    font-size:14px;
    font-weight:600;
    color:var(--ink);
    margin-bottom:8px;
    text-align:center;
}

.form-input{
    width:100%;
    padding:18px 16px;
    border:2px solid var(--hair);
    border-radius:12px;
    font-size:24px;
    font-family:'DM Sans',sans-serif;
    color:var(--ink);
    background:#fff;
    transition:all .2s;
    text-align:center;
    font-weight:700;
    letter-spacing:12px;
}

.form-input:focus{
    outline:none;
    border-color:var(--trust-blue);
    box-shadow:0 0 0 4px rgba(10,100,188,.08);
}

.error-msg{
    display:block;
    margin-top:8px;
    font-size:13px;
    color:var(--coral);
    text-align:center;
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
    margin-bottom:16px;
}

.btn-submit:hover{
    transform:translateY(-2px);
    box-shadow:0 6px 20px rgba(10,100,188,.35);
}

.btn-submit:active{
    transform:translateY(0);
}

.help-text{
    text-align:center;
    font-size:14px;
    color:var(--muted);
    margin-bottom:20px;
}

.divider{
    position:relative;
    text-align:center;
    margin:28px 0;
}

.divider::before{
    content:'';
    position:absolute;
    left:0;
    top:50%;
    width:100%;
    height:1px;
    background:var(--hair);
}

.divider span{
    position:relative;
    background:#fff;
    padding:0 16px;
    font-size:13px;
    color:var(--muted);
    font-weight:500;
}

.link-group{
    display:flex;
    flex-direction:column;
    gap:12px;
}

.link{
    text-align:center;
    font-size:14px;
    color:var(--trust-blue);
    font-weight:600;
    text-decoration:none;
    padding:12px;
    border-radius:8px;
    transition:all .2s;
}

.link:hover{
    background:rgba(10,100,188,.05);
}

@media(max-width:640px){
    .card{
        padding:32px 24px;
    }
}
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="header">
            <div class="icon-shield">
                <svg viewBox="0 0 24 24">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <h1>Verify Your Identity</h1>
            <p>Enter the 6-digit code from your authenticator app</p>
        </div>

        <form action="{{ route('2fa.challenge') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="code" class="form-label">Authentication Code</label>
                <input 
                    type="text" 
                    id="code" 
                    name="code" 
                    class="form-input" 
                    placeholder="000000"
                    maxlength="9"
                    required
                    autofocus>
                @error('code')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="help-text">
                The code refreshes every 30 seconds
            </div>

            <button type="submit" class="btn-submit">
                ✓ Verify & Continue
            </button>
        </form>

        <div class="divider">
            <span>Having trouble?</span>
        </div>

        <div class="link-group">
            <button type="button" class="link" onclick="toggleRecoveryMode()">
                🔑 Use a recovery code instead
            </button>
            <a href="{{ route('login') }}" class="link">
                ← Back to login
            </a>
        </div>
    </div>
</div>

<script>
let recoveryMode = false;

function toggleRecoveryMode() {
    recoveryMode = !recoveryMode;
    const input = document.getElementById('code');
    const label = document.querySelector('.form-label');
    
    if (recoveryMode) {
        input.placeholder = 'XXXX-XXXX';
        input.maxLength = 9;
        input.style.letterSpacing = '6px';
        input.style.fontSize = '18px';
        label.textContent = 'Recovery Code';
    } else {
        input.placeholder = '000000';
        input.maxLength = 6;
        input.style.letterSpacing = '12px';
        input.style.fontSize = '24px';
        label.textContent = 'Authentication Code';
    }
    input.value = '';
    input.focus();
}
</script>

</body>
</html>
