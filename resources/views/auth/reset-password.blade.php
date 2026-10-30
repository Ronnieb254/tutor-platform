<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password – Mwalimu</title>
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

.reset-container{
    display:flex;
    min-height:100vh;
    background:linear-gradient(135deg,#F2F5F2 0%,#E8F0E8 100%);
    align-items:center;
    justify-content:center;
    padding:40px;
}

.reset-card{
    width:100%;
    max-width:520px;
    background:#fff;
    border-radius:20px;
    padding:48px;
    box-shadow:0 20px 60px rgba(21,40,31,.08);
}

.reset-header{
    text-align:center;
    margin-bottom:36px;
}

.reset-icon{
    width:64px;
    height:64px;
    margin:0 auto 20px;
    background:linear-gradient(135deg,var(--trust-blue) 0%,#5B21B6 100%);
    border-radius:16px;
    display:flex;
    align-items:center;
    justify-content:center;
}

.reset-icon svg{
    width:32px;
    height:32px;
    stroke:#fff;
    stroke-width:2;
    fill:none;
}

.reset-header h2{
    font-family:'Fraunces',serif;
    font-size:28px;
    font-weight:800;
    color:var(--ink);
    margin-bottom:10px;
}

.reset-header p{
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

.password-requirements{
    background:rgba(10,100,188,.04);
    border:1px solid rgba(10,100,188,.12);
    border-radius:10px;
    padding:14px 16px;
    margin-top:16px;
}

.password-requirements h4{
    font-size:13px;
    font-weight:700;
    color:var(--ink);
    margin-bottom:8px;
}

.password-requirements ul{
    list-style:none;
    padding:0;
    margin:0;
}

.password-requirements li{
    font-size:12px;
    color:var(--muted);
    padding-left:18px;
    position:relative;
    margin-bottom:4px;
}

.password-requirements li::before{
    content:'•';
    position:absolute;
    left:6px;
    color:var(--trust-blue);
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
    margin-top:24px;
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
    .reset-card{
        padding:32px 24px;
    }
}
    </style>
</head>
<body>

<div class="reset-container">
    <div class="reset-card">
        <div class="reset-header">
            <div class="reset-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                </svg>
            </div>
            <h2>Create New Password</h2>
            <p>Your new password must be different from your last 3 passwords.</p>
        </div>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group">
                <label for="password" class="form-label">New Password</label>
                <div class="input-wrapper">
                    <div class="input-icon">
                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="••••••••"
                        required
                        autocomplete="new-password"
                        autofocus>
                </div>
                @error('password')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                <div class="input-wrapper">
                    <div class="input-icon">
                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="form-input" 
                        placeholder="••••••••"
                        required
                        autocomplete="new-password">
                </div>
            </div>

            <div class="password-requirements">
                <h4>Password Requirements:</h4>
                <ul>
                    <li>At least 8 characters long</li>
                    <li>Must not match your last 3 passwords</li>
                    <li>Cannot be a commonly used password</li>
                </ul>
            </div>

            @error('email')
                <span class="error-msg" style="display:block;margin-top:12px;">{{ $message }}</span>
            @enderror

            <button type="submit" class="btn-submit">
                Reset Password
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('login') }}">← Back to Login</a>
        </div>
    </div>
</div>

</body>
</html>
