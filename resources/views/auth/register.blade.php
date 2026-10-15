<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account – Mwalimu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        :root { --ink:#15281F; --cream:#FBF5E6; --violet:#6D28D9; --coral:#E8522A; --gold:#D4A017; --mint:#10B981; --muted:rgba(21,40,31,0.55); --hairline:rgba(21,40,31,0.12); }
        *,*::before,*::after { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'DM Sans',sans-serif; color:var(--ink); min-height:100vh; display:flex; flex-direction:column; }
        h1,h2,h3 { font-family:'Fraunces',Georgia,serif; }
        a { color:inherit; text-decoration:none; }
        [x-cloak] { display:none !important; }

        /* Split layout */
        .page-wrap { display:flex; min-height:100vh; }

        /* Left panel */
        .left-panel { display:none; background:var(--ink); padding:48px; flex-direction:column; justify-content:space-between; position:relative; overflow:hidden; }
        @media(min-width:1024px) { .left-panel { display:flex; width:440px; flex-shrink:0; } }
        .left-panel::before { content:''; position:absolute; top:-80px; right:-80px; width:320px; height:320px; background:rgba(109,40,217,0.25); border-radius:50%; }
        .left-panel::after { content:''; position:absolute; bottom:-60px; left:-60px; width:240px; height:240px; background:rgba(109,40,217,0.15); border-radius:50%; }
        .left-logo { display:flex; align-items:center; gap:10px; font-family:'Fraunces',serif; font-size:22px; font-weight:700; color:#fff; position:relative; z-index:1; }
        .left-content { position:relative; z-index:1; }
        .left-content h2 { font-size:38px; color:#fff; line-height:1.15; margin-bottom:16px; }
        .left-content h2 span { color:var(--gold); }
        .left-content p { color:rgba(255,255,255,0.65); font-size:16px; line-height:1.7; margin-bottom:32px; }
        .left-points { display:flex; flex-direction:column; gap:14px; }
        .left-point { display:flex; align-items:center; gap:12px; }
        .left-point-dot { width:24px; height:24px; background:rgba(109,40,217,0.4); border-radius:999px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .left-point span { color:rgba(255,255,255,0.85); font-size:14px; }
        .left-stats { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; position:relative; z-index:1; }
        .left-stat { background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); border-radius:16px; padding:16px; text-align:center; }
        .left-stat-num { font-family:'Fraunces',serif; font-size:28px; font-weight:700; color:#fff; }
        .left-stat-label { font-size:11px; color:rgba(255,255,255,0.5); margin-top:2px; }

        /* Right panel */
        .right-panel { flex:1; background:var(--cream); display:flex; flex-direction:column; }
        .right-header { padding:20px 32px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--hairline); }
        .right-header-logo { display:flex; align-items:center; gap:8px; font-family:'Fraunces',serif; font-size:20px; font-weight:700; color:var(--ink); }
        @media(min-width:1024px) { .right-header-logo { display:none; } }
        .right-header-signin { font-size:14px; color:var(--muted); }
        .right-header-signin a { color:var(--violet); font-weight:600; }
        .right-main { flex:1; display:flex; align-items:center; justify-content:center; padding:32px 24px; }
        .form-wrap { width:100%; max-width:480px; }
        .form-wrap h1 { font-size:32px; color:var(--ink); margin-bottom:6px; }
        .form-wrap .subtitle { color:var(--muted); font-size:15px; margin-bottom:28px; }

        /* Role cards */
        .role-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:24px; }
        .role-card { border:2px solid var(--hairline); border-radius:16px; padding:16px 8px; text-align:center; cursor:pointer; transition:all .15s; }
        .role-card.active { border-color:var(--violet); background:rgba(109,40,217,0.06); }
        .role-card .r-icon { font-size:28px; margin-bottom:6px; }
        .role-card .r-label { font-size:13px; font-weight:600; color:var(--ink); }
        .role-card .r-desc { font-size:11px; color:var(--muted); margin-top:2px; }

        /* Tutor info box */
        .tutor-info { background:rgba(109,40,217,0.06); border:1px solid rgba(109,40,217,0.2); border-radius:14px; padding:14px 16px; margin-bottom:20px; font-size:13px; color:var(--violet); line-height:1.6; }
        .tutor-info strong { display:block; margin-bottom:4px; font-size:14px; }

        /* Social buttons */
        .social-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:20px; }
        .social-btn { display:flex; align-items:center; justify-content:center; gap:6px; height:42px; border-radius:12px; border:1.5px solid var(--hairline); background:#fff; font-size:13px; font-weight:500; color:var(--ink); cursor:pointer; transition:border-color .15s; text-decoration:none; }
        .social-btn:hover { border-color:rgba(109,40,217,0.4); background:rgba(109,40,217,0.03); }

        /* Divider */
        .divider { display:flex; align-items:center; gap:12px; margin:4px 0 20px; }
        .divider::before, .divider::after { content:''; flex:1; height:1px; background:var(--hairline); }
        .divider span { font-size:12px; color:var(--muted); }

        /* Fields */
        .fields-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .field { margin-bottom:14px; }
        .field label { display:block; font-size:13px; font-weight:600; color:var(--ink); margin-bottom:5px; }
        .field input, .field select { width:100%; height:46px; padding:0 14px; border-radius:12px; border:1.5px solid rgba(21,40,31,0.2); background:#fff; font-size:14px; font-family:inherit; color:var(--ink); outline:none; transition:all .15s; }
        .field input:focus, .field select:focus { border-color:var(--violet); box-shadow:0 0 0 3px rgba(109,40,217,0.1); }
        .field input.error { border-color:var(--coral); }
        .field-error { font-size:12px; color:var(--coral); margin-top:4px; display:flex; align-items:center; gap:4px; }

        /* Password strength */
        .pw-strength { display:flex; gap:4px; margin-top:6px; }
        .pw-bar { height:3px; flex:1; border-radius:2px; background:var(--hairline); transition:background .3s; }

        /* Submit */
        .btn-submit { width:100%; height:50px; border-radius:999px; background:var(--violet); color:#fff; font-size:15px; font-weight:700; font-family:inherit; border:none; cursor:pointer; transition:all .15s; margin-top:4px; display:flex; align-items:center; justify-content:center; gap:8px; }
        .btn-submit:hover { background:#4C1D95; transform:translateY(-1px); box-shadow:0 4px 16px rgba(109,40,217,0.3); }

        /* Terms */
        .terms-text { font-size:12px; color:var(--muted); text-align:center; margin-top:12px; line-height:1.5; }
        .terms-text a { color:var(--violet); }

        .right-footer { padding:16px 32px; text-align:center; font-size:13px; color:var(--muted); border-top:1px solid var(--hairline); }
        .right-footer a { color:var(--violet); font-weight:600; }

        /* Step indicator for tutor */
        .tutor-steps { display:flex; gap:0; margin-bottom:20px; border-radius:12px; overflow:hidden; border:1px solid var(--hairline); }
        .tutor-step { flex:1; padding:10px 4px; text-align:center; font-size:11px; font-weight:600; background:#fff; color:var(--muted); border-right:1px solid var(--hairline); }
        .tutor-step:last-child { border-right:none; }
        .tutor-step.active { background:var(--violet); color:#fff; }
        .tutor-step.done { background:var(--mint); color:#fff; }
    </style>
</head>
<body>
<div class="page-wrap" x-data="{ role: '{{ old('role', request('role', 'student')) }}' }">

    {{-- Left decorative panel --}}
    <div class="left-panel">
        <a href="{{ route('home') }}" class="left-logo">
            <svg width="32" height="32" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
            Mwalimu
        </a>

        {{-- Dynamic content based on role --}}
        <div class="left-content">
            <template x-if="role === 'tutor'">
                <div>
                    <h2>Start <span>earning</span> with your expertise</h2>
                    <p>Join hundreds of verified tutors across Kenya. Set your own rates, choose your availability, and get paid.</p>
                    <div class="left-points">
                        @foreach(['Upload docs once — verified for life','Set your own hourly rate','Keep up to 85% of earnings','Unique session codes sent by email']) as $p)
                        <div class="left-point">
                            <div class="left-point-dot">
                                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <span>{{ $p }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </template>
            <template x-if="role !== 'tutor'">
                <div>
                    <h2>Learn from <span>verified</span> experts</h2>
                    <p>Find the perfect tutor for any subject. Book sessions in flexible time blocks and pay securely.</p>
                    <div class="left-points">
                        @foreach(['500+ verified tutors across Kenya','15, 30, 45 or 60-minute sessions','Unique access code for every session','4.9★ average rating from students']) as $p)
                        <div class="left-point">
                            <div class="left-point-dot">
                                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <span>{{ $p }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </template>
        </div>

        <div class="left-stats">
            <div class="left-stat"><div class="left-stat-num">500+</div><div class="left-stat-label">Verified Tutors</div></div>
            <div class="left-stat"><div class="left-stat-num">4.9★</div><div class="left-stat-label">Avg Rating</div></div>
            <div class="left-stat"><div class="left-stat-num">80+</div><div class="left-stat-label">Subjects</div></div>
            <div class="left-stat"><div class="left-stat-num">10k+</div><div class="left-stat-label">Sessions Done</div></div>
        </div>
    </div>

    {{-- Right form panel --}}
    <div class="right-panel">
        <div class="right-header">
            <a href="{{ route('home') }}" class="right-header-logo">
                <svg width="28" height="28" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#15281F"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
                Mwalimu
            </a>
            <span class="right-header-signin">Already have an account? <a href="{{ route('login') }}">Sign in</a></span>
        </div>

        <div class="right-main">
            <div class="form-wrap">
                <h1>Create your account</h1>
                <p class="subtitle">Free to join. No credit card required.</p>

                {{-- Role selector --}}
                <div class="role-grid">
                    <div class="role-card" :class="role === 'student' ? 'active' : ''" @click="role = 'student'">
                        <div class="r-icon">🎓</div>
                        <div class="r-label">Student</div>
                        <div class="r-desc">I want to learn</div>
                    </div>
                    <div class="role-card" :class="role === 'parent' ? 'active' : ''" @click="role = 'parent'">
                        <div class="r-icon">👨‍👩‍👧</div>
                        <div class="r-label">Parent</div>
                        <div class="r-desc">Book for my child</div>
                    </div>
                    <div class="role-card" :class="role === 'tutor' ? 'active' : ''" @click="role = 'tutor'">
                        <div class="r-icon">📚</div>
                        <div class="r-label">Tutor</div>
                        <div class="r-desc">I want to teach</div>
                    </div>
                </div>

                {{-- Tutor next-steps notice --}}
                <div class="tutor-info" x-show="role === 'tutor'" x-cloak>
                    <strong>📋 What happens after you register:</strong>
                    Complete your profile → Upload verification docs → Admin approves → Profile goes live → Start earning
                </div>

                {{-- Social login --}}
                <div class="social-grid">
                    <a href="{{ route('auth.social.redirect', 'google') }}" class="social-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        Google
                    </a>
                    <a href="{{ route('auth.social.redirect', 'facebook') }}" class="social-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="#1877F2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                        Facebook
                    </a>
                    <a href="{{ route('auth.social.redirect', 'github') }}" class="social-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                        GitHub
                    </a>
                </div>

                <div class="divider"><span>or register with email</span></div>

                {{-- Form --}}
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <input type="hidden" name="role" :value="role">

                    <div class="fields-row">
                        <div class="field">
                            <label>Full name</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Jane Mwangi" required class="{{ $errors->has('name') ? 'error' : '' }}">
                            @error('name')<div class="field-error">⚠ {{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label>Phone <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+254 7XX XXX XXX">
                        </div>
                    </div>

                    <div class="field">
                        <label>Email address</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required class="{{ $errors->has('email') ? 'error' : '' }}">
                        @error('email')<div class="field-error">⚠ {{ $message }}</div>@enderror
                    </div>

                    <div class="fields-row">
                        <div class="field">
                            <label>Password</label>
                            <input type="password" name="password" placeholder="Min 8 characters" required id="pw-input" class="{{ $errors->has('password') ? 'error' : '' }}"
                                   x-on:input="
                                        let v = $el.value;
                                        let s = 0;
                                        if(v.length >= 8) s++;
                                        if(/[A-Z]/.test(v)) s++;
                                        if(/[0-9]/.test(v)) s++;
                                        if(/[^A-Za-z0-9]/.test(v)) s++;
                                        $refs.bar1.style.background = s>=1 ? '#E8522A' : 'var(--hairline)';
                                        $refs.bar2.style.background = s>=2 ? '#D4A017' : 'var(--hairline)';
                                        $refs.bar3.style.background = s>=3 ? '#10B981' : 'var(--hairline)';
                                        $refs.bar4.style.background = s>=4 ? '#10B981' : 'var(--hairline)';
                                   ">
                            <div class="pw-strength">
                                <div class="pw-bar" x-ref="bar1"></div>
                                <div class="pw-bar" x-ref="bar2"></div>
                                <div class="pw-bar" x-ref="bar3"></div>
                                <div class="pw-bar" x-ref="bar4"></div>
                            </div>
                            @error('password')<div class="field-error">⚠ {{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label>Confirm password</label>
                            <input type="password" name="password_confirmation" placeholder="Repeat password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <span x-text="role === 'tutor' ? 'Create Tutor Account' : role === 'parent' ? 'Create Parent Account' : 'Create Student Account'">Create Account</span>
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>
                </form>

                <p class="terms-text">
                    By creating an account you agree to our
                    <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                </p>
            </div>
        </div>

        <div class="right-footer">
            Already have an account? <a href="{{ route('login') }}">Sign in to Mwalimu</a>
        </div>
    </div>
</div>
</body>
</html>
