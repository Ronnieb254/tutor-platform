<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password – Mwalimu</title>
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

.forgot-container{
    display:flex;
    min-height:100vh;
    background:linear-gradient(135deg,#F2F5F2 0%,#E8F0E8 100%);
    align-items:center;
    justify-content:center;
    padding:40px;
}

.forgot-card{
    width:100%;
    max-width:520px;
    background:#fff;
    border-radius:20px;
    padding:48px;
    box-shadow:0 20px 60px rgba(21,40,31,.08);
}

.forgot-header{
    text-align:center;
    margin-bottom:36px;
}

.forgot-icon{
    width:64px;
    height:64px;
    margin:0 auto 20px;
    background:linear-gradient(135deg,var(--trust-blue) 0%,#5B21B6 100%);
    border-radius:16px;
    display:flex;
    align-items:center;
    justify-content:center;
}

.forgot-icon svg{
    width:32px;
    height:32px;
    stroke:#fff;
    stroke-width:2;
    fill:none;
}

.forgot-header h2{
    font-family:'Fraunces',serif;
    font-size:28px;
    font-weight:800;
    color:var(--ink);
    margin-bottom:10px;
}

.forgot-header p{
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
}

.input-wrapper{
    position:relative;
}

.input-icon{
    position:absolute;
    left:16px;
    top:50%;
    transform:translateY(-50%);
    color:var(--muted);
    pointer-events:none;
}

.input-icon svg{
    width:18px;
    height:18px;
    stroke:currentColor;
    stroke-width:2;
    fill:none;
}

.form-input{
    width:100%;
    padding:14px 16px 14px 48px;
    border:2px solid var(--hair);
    border-radius:12px;
    font-size:15px;
    font-family:'DM Sans',sans-serif;
    color:var(--ink);
    background:#fff;
    transition:all .2s;
}

.form-input::placeholder{
    color:rgba(21,40,31,.3);
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

.success-msg{
    display:block;
    padding:14px 16px;
    background:rgba(16,185,129,.08);
    border:1px solid rgba(16,185,129,.2);
    border-radius:10px;
    color:var(--mint);
    font-size:14px;
    margin-bottom:24px;
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

.back-link{
    text-align:center;
    margin-top:28px;
}

.back-link a{
    font-size:14px;
    color:var(--trust-blue);
    font-weight:600;
    text-decoration:none;
    transition:color .2s;
}

.back-link a:hover{
    color:#5B21B6;
}

@media(max-width:768px){
    .forgot-card{
        padding:32px 24px;
    }
}
    </style>
</head>
<body>

<div class="forgot-container">
    <div class="forgot-card">
        <div class="forgot-header">
            <div class="forgot-icon">
                <svg viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <h2>Reset Your Password</h2>
            <p>Enter your email address and we'll send you a link to reset your password.</p>
        </div>

        @if(session('status'))
            <div class="success-msg">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-wrapper">
                    <div class="input-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="you@example.com"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        autofocus>
                </div>
                @error('email')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-submit">
                Send Reset Link
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('login') }}">← Back to Login</a>
        </div>
    </div>
</div>

</body>
</html>
