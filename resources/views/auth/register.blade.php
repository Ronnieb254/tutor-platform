<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register – Mwalimu</title>
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

/* Layout */
.register-container{
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
    background:radial-gradient(circle,rgba(16,185,129,.15) 0%,transparent 70%);
    border-radius:50%;
}

.brand-panel::after{
    content:'';
    position:absolute;
    bottom:-200px;
    left:-200px;
    width:500px;
    height:500px;
    background:radial-gradient(circle,rgba(10,100,188,.12) 0%,transparent 70%);
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
    background:rgba(16,185,129,.15);
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
    overflow-y:auto;
}

.form-container{
    width:100%;
    max-width:440px;
    padding:20px 0;
}

.form-header{
    text-align:center;
    margin-bottom:36px;
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
    margin-bottom:20px;
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

.form-input,.form-select{
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

.form-select{
    appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2315281F' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 16px center;
    padding-right:48px;
}

.form-input::placeholder{
    color:rgba(21,40,31,.3);
}

.form-input:focus,.form-select:focus{
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

/* Terms Checkbox */
.terms-wrapper{
    display:flex;
    gap:10px;
    align-items:flex-start;
    margin-bottom:24px;
}

.terms-wrapper input{
    width:18px;
    height:18px;
    cursor:pointer;
    accent-color:var(--trust-blue);
    flex-shrink:0;
    margin-top:2px;
}

.terms-wrapper label{
    font-size:13px;
    color:var(--muted);
    line-height:1.6;
    cursor:pointer;
}

.terms-wrapper label a{
    color:var(--trust-blue);
    font-weight:600;
    text-decoration:none;
}

.terms-wrapper label a:hover{
    text-decoration:underline;
}

/* Submit Button */
.btn-submit{
    width:100%;
    padding:16px;
    background:linear-gradient(135deg,var(--mint) 0%,#059669 100%);
    color:#fff;
    font-size:16px;
    font-weight:700;
    border:none;
    border-radius:12px;
    cursor:pointer;
    transition:all .2s;
    box-shadow:0 4px 14px rgba(16,185,129,.25);
}

.btn-submit:hover{
    transform:translateY(-2px);
    box-shadow:0 6px 20px rgba(16,185,129,.35);
}

.btn-submit:active{
    transform:translateY(0);
}

/* Divider */
.divider{
    position:relative;
    text-align:center;
    margin:32px 0;
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

/* Login Link */
.login-link{
    text-align:center;
    font-size:14px;
    color:var(--muted);
}

.login-link a{
    color:var(--trust-blue);
    font-weight:700;
    text-decoration:none;
    transition:color .2s;
}

.login-link a:hover{
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

<div class="register-container">
    {{-- Left Panel - Brand --}}
    <div class="brand-panel">
        <div class="brand-logo">
            <h1>Mwalimu</h1>
            <p class="brand-tagline">Join Kenya's Premier Tutoring Platform</p>
        </div>

        <div class="brand-features">
            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </div>
                <div class="feature-content">
                    <h3>Learn from the Best</h3>
                    <p>TSC-registered tutors with proven track records in education</p>
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
                    <h3>Flexible Scheduling</h3>
                    <p>Book sessions at your convenience with instant confirmation</p>
                </div>
            </div>

            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <div class="feature-content">
                    <h3>Exam Clinics</h3>
                    <p>Join group revision sessions for focused exam preparation</p>
                </div>
            </div>
        </div>

        <div class="brand-footer">
            © 2026 Mwalimu. Empowering learners nationwide.
        </div>
    </div>

    {{-- Right Panel - Form --}}
    <div class="form-panel">
        <div class="form-container">
            <div class="form-header">
                <h2>Create Account</h2>
                <p>Start your learning journey today</p>
            </div>

            <form action="{{ route('register') }}" method="POST">
                @csrf

                {{-- Name Field --}}
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <div class="input-wrapper">
                        <div class="input-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-input" 
                            placeholder="John Doe"
                            value="{{ old('name') }}"
                            required
                            autocomplete="name">
                    </div>
                    @error('name')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

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

                {{-- Phone Field --}}
                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <div class="input-wrapper">
                        <div class="input-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </div>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            class="form-input" 
                            placeholder="+254 700 000 000"
                            value="{{ old('phone') }}"
                            autocomplete="tel">
                    </div>
                    @error('phone')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Role Field --}}
                <div class="form-group">
                    <label for="role" class="form-label">I am a...</label>
                    <div class="input-wrapper">
                        <div class="input-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <select id="role" name="role" class="form-select" required>
                            <option value="">Select your role</option>
                            <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                            <option value="parent" {{ old('role') === 'parent' ? 'selected' : '' }}>Parent/Guardian</option>
                            <option value="tutor" {{ old('role') === 'tutor' ? 'selected' : '' }}>Tutor</option>
                        </select>
                    </div>
                    @error('role')
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
                            autocomplete="new-password">
                    </div>
                    @error('password')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                    
                    {{-- Password Strength Indicator --}}
                    @include('components.password-strength')
                </div>

                {{-- Confirm Password Field --}}
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
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

                {{-- Terms Checkbox --}}
                <div class="terms-wrapper">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">
                        I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                    </label>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn-submit">
                    Create Account
                </button>
            </form>

            {{-- Divider --}}
            <div class="divider">
                <span>Already have an account?</span>
            </div>

            {{-- Login Link --}}
            <div class="login-link">
                <a href="{{ route('login') }}">Sign in instead</a>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
<script>
    alert('Registration failed: {{ $errors->first() }}');
</script>
@endif

</body>
</html>
