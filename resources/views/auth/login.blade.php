<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login – Mwalimu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
:root{
    --ink:#15281F;
    --cream:#FBF5E6;
    --Trust Blue:#0A64BC;
    --coral:#E8522A;
    --mint:#10B981;
    --muted:rgba(21,40,31,.55);
    --hair:rgba(21,40,31,.1);
}

*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:'DM Sans',sans-serif;color:var(--ink);}

/* Layout */
.login-container{
    display:flex;
    min-height:100vh;
    background:linear-gradient(135deg,#F2F5F2 0%,#E8F0E8 100%);
}

/* Left Panel - Branding */
.brand-panel{
    flex:1;
    background:linear-gradient(135deg,var(--ink) 0%,#0A1410 100%);
    padding:60px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    position:relative;
    overflow:hidden;
}

.brand-panel::before{
    content:'';
    position:absolute;
    top:-200px;
    right:-200px;
    width:500px;
    height:500px;
    background:radial-gradient(circle,rgba(10,100,188,.15) 0%,transparent 70%);
    border-radius:50%;
}

.brand-panel::after{
    content:'';
    position:absolute;
    bottom:-200px;
    left:-200px;
    width:500px;
    height:500px;
    background:radial-gradient(circle,rgba(16,185,129,.12) 0%,transparent 70%);
    border-radius:50%;
}

.brand-logo{
    position:relative;
    z-index:2;
}

.brand-logo h1{
    font-family:'Fraunces',serif;
    font-size:38px;
    font-weight:800;
    background:linear-gradient(135deg,#fff 0%,rgba(255,255,255,.7) 100%);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    background-clip:text;
    margin-bottom:8px;
}

.brand-tagline{
    font-size:16px;
    color:rgba(255,255,255,.5);
    font-weight:500;
}

.brand-features{
    position:relative;
    z-index:2;
    display:flex;
    flex-direction:column;
    gap:32px;
}

.feature{
    display:flex;
    gap:18px;
    align-items:flex-start;
}

.feature-icon{
    width:48px;
    height:48px;
    background:rgba(10,100,188,.15);
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
}

.feature-icon svg{
    width:22px;
    height:22px;
    stroke:#fff;
    stroke-width:2;
    fill:none;
}

.feature-content h3{
    font-size:18px;
    font-weight:700;
    color:#fff;
    margin-bottom:6px;
}

.feature-content p{
    font-size:14px;
    color:rgba(255,255,255,.5);
    line-height:1.6;
}

.brand-footer{
    position:relative;
    z-index:2;
    font-size:13px;
    color:rgba(255,255,255,.35);
}

/* Right Panel - Form */
.form-panel{
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:40px;
}

.form-container{
    width:100%;
    max-width:440px;
}

.form-header{
    text-align:center;
    margin-bottom:48px;
}

.form-header h2{
    font-family:'Fraunces',serif;
    font-size:32px;
    font-weight:800;
    color:var(--ink);
    margin-bottom:10px;
}

.form-header p{
    font-size:15px;
    color:var(--muted);
}

/* Form Elements */
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
    border-color:var(--Trust Blue);
    box-shadow:0 0 0 4px rgba(10,100,188,.08);
}

.error-msg{
    display:block;
    margin-top:6px;
    font-size:13px;
    color:var(--coral);
}

/* Remember & Forgot Row */
.form-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:28px;
}

.checkbox-wrapper{
    display:flex;
    align-items:center;
    gap:8px;
    cursor:pointer;
}

.checkbox-wrapper input{
    width:18px;
    height:18px;
    cursor:pointer;
    accent-color:var(--Trust Blue);
}

.checkbox-wrapper label{
    font-size:14px;
    color:var(--ink);
    cursor:pointer;
    user-select:none;
}

.forgot-link{
    font-size:14px;
    color:var(--Trust Blue);
    font-weight:600;
    text-decoration:none;
    transition:color .2s;
}

.forgot-link:hover{
    color:#5B21B6;
}

/* Submit Button */
.btn-submit{
    width:100%;
    padding:16px;
    background:linear-gradient(135deg,var(--Trust Blue) 0%,#5B21B6 100%);
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

/* Divider */
.divider{
    position:relative;
    text-align:center;
    margin:36px 0;
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
    background:linear-gradient(135deg,#F2F5F2 0%,#E8F0E8 100%);
    padding:0 16px;
    font-size:13px;
    color:var(--muted);
    font-weight:500;
}

/* Sign Up Link */
.signup-link{
    text-align:center;
    font-size:14px;
    color:var(--muted);
}

.signup-link a{
    color:var(--Trust Blue);
    font-weight:700;
    text-decoration:none;
    transition:color .2s;
}

.signup-link a:hover{
    color:#5B21B6;
}

/* Mobile Responsive */
@media(max-width:1024px){
    .brand-panel{
        display:none;
    }
    .form-panel{
        padding:24px;
    }
}

/* SVG Icons */
svg{
    stroke:currentColor;
    stroke-width:2;
    stroke-linecap:round;
    stroke-linejoin:round;
    fill:none;
}
    </style>
</head>
<body>

<div class="login-container">
    {{-- Left Panel - Brand --}}
    <div class="brand-panel">
        <div class="brand-logo">
            <h1>Mwalimu</h1>
            <p class="brand-tagline">Connecting Students with Expert Tutors</p>
        </div>

        <div class="brand-features">
            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <div class="feature-content">
                    <h3>Verified Tutors</h3>
                    <p>TSC-registered educators with proven expertise and background verification</p>
                </div>
            </div>

            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <div class="feature-content">
                    <h3>Secure Platform</h3>
                    <p>End-to-end encrypted sessions with secure payment processing</p>
                </div>
            </div>

            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div class="feature-content">
                    <h3>Instant Booking</h3>
                    <p>Book 1-on-1 sessions or join exam clinics in just a few clicks</p>
                </div>
            </div>
        </div>

        <div class="brand-footer">
            © 2026 Mwalimu. Trusted by thousands of students across Kenya.
        </div>
    </div>

    {{-- Right Panel - Form --}}
    <div class="form-panel">
        <div class="form-container">
            <div class="form-header">
                <h2>Welcome Back</h2>
                <p>Sign in to your account to continue learning</p>
            </div>

            <form action="{{ route('login') }}" method="POST">
                @csrf

                {{-- Email Field --}}
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
                            autocomplete="email">
                    </div>
                    @error('email')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password Field --}}
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
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
                            autocomplete="current-password">
                    </div>
                    @error('password')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Remember & Forgot --}}
                <div class="form-row">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn-submit">
                    Sign In
                </button>
            </form>

            {{-- Divider --}}
            <div class="divider">
                <span>New to Mwalimu?</span>
            </div>

            {{-- Sign Up Link --}}
            <div class="signup-link">
                Don't have an account? <a href="{{ route('register') }}">Create one now</a>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
<script>
    alert('Login failed: {{ $errors->first() }}');
</script>
@endif

</body>
</html>
