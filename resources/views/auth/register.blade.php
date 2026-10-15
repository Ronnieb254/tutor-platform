<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Create Account – Mwalimu</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>
:root{--ink:#15281F;--cream:#FBF5E6;--violet:#6D28D9;--coral:#E8522A;--gold:#D4A017;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.12);}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'DM Sans',sans-serif;color:var(--ink);min-height:100vh;display:flex;flex-direction:column;}
h1,h2,h3{font-family:'Fraunces',Georgia,serif;}
a{color:inherit;text-decoration:none;}
[x-cloak]{display:none!important;}

.page{display:flex;min-height:100vh;}

/* LEFT PANEL */
.left{display:none;background:var(--ink);padding:48px;flex-direction:column;justify-content:space-between;position:relative;overflow:hidden;width:440px;flex-shrink:0;}
@media(min-width:1024px){.left{display:flex;}}
.left::before{content:'';position:absolute;top:-80px;right:-80px;width:300px;height:300px;background:rgba(109,40,217,.2);border-radius:50%;}
.left::after{content:'';position:absolute;bottom:-60px;left:-40px;width:220px;height:220px;background:rgba(109,40,217,.12);border-radius:50%;}
.left-logo{display:flex;align-items:center;gap:10px;position:relative;z-index:1;}
.left-logo span{font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:#fff;}
.left-body{position:relative;z-index:1;}
.left-body h2{font-size:34px;color:#fff;line-height:1.15;margin-bottom:14px;}
.left-body h2 em{font-style:normal;color:var(--gold);}
.left-body p{color:rgba(255,255,255,.62);font-size:15px;line-height:1.75;margin-bottom:28px;}
.left-points{display:flex;flex-direction:column;gap:12px;}
.left-point{display:flex;align-items:center;gap:12px;font-size:14px;color:rgba(255,255,255,.85);}
.left-point-dot{width:24px;height:24px;background:rgba(109,40,217,.4);border-radius:999px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:rgba(255,255,255,.9);}
.left-stats{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;position:relative;z-index:1;}
.lstat{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:16px;padding:16px;text-align:center;}
.lstat-n{font-family:'Fraunces',serif;font-size:26px;font-weight:700;color:#fff;}
.lstat-l{font-size:11px;color:rgba(255,255,255,.45);margin-top:3px;}

/* RIGHT PANEL */
.right{flex:1;background:var(--cream);display:flex;flex-direction:column;}
.right-head{padding:20px 32px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--hair);}
.right-logo{display:flex;align-items:center;gap:8px;font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--ink);}
@media(min-width:1024px){.right-logo{display:none;}}
.right-body{flex:1;display:flex;align-items:center;justify-content:center;padding:32px 24px;}
.form-wrap{width:100%;max-width:480px;}
.form-wrap h1{font-size:30px;margin-bottom:5px;}
.subtitle{font-size:14px;color:var(--muted);margin-bottom:24px;}

/* Role cards */
.role-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:22px;}
.role-card{border:2px solid var(--hair);border-radius:16px;padding:14px 8px;text-align:center;cursor:pointer;transition:all .18s;}
.role-card.active{border-color:var(--violet);background:rgba(109,40,217,.06);}
.role-card:hover{border-color:rgba(109,40,217,.4);}
.role-icon{font-size:26px;margin-bottom:5px;color:var(--ink);}
.role-label{font-size:13px;font-weight:700;color:var(--ink);}
.role-desc{font-size:11px;color:var(--muted);margin-top:2px;}

/* Tutor info */
.tutor-info{background:rgba(109,40,217,.06);border:1px solid rgba(109,40,217,.18);border-radius:14px;padding:14px 16px;margin-bottom:18px;font-size:13px;color:var(--violet);line-height:1.65;}
.tutor-info strong{display:block;margin-bottom:3px;font-size:14px;}

/* Social */
.social-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:18px;}
.soc-btn{display:flex;align-items:center;justify-content:center;gap:7px;height:42px;border-radius:12px;border:1.5px solid var(--hair);background:#fff;font-size:13px;font-weight:500;color:var(--ink);cursor:pointer;transition:all .15s;}
.soc-btn:hover{border-color:rgba(109,40,217,.4);transform:translateY(-1px);}

/* Divider */
.divider{display:flex;align-items:center;gap:10px;margin:4px 0 18px;}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--hair);}
.divider span{font-size:12px;color:var(--muted);}

/* Fields */
.row2{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.field{margin-bottom:13px;}
.field label{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:flex;align-items:center;gap:5px;margin-bottom:5px;}
.field label i{color:var(--violet);font-size:11px;}
.field input,.field select{width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.2);background:#fff;font-size:14px;font-family:inherit;color:var(--ink);outline:none;transition:all .15s;}
.field input:focus,.field select:focus{border-color:var(--violet);box-shadow:0 0 0 3px rgba(109,40,217,.1);}
.field input.err{border-color:var(--coral);}
.ferr{font-size:12px;color:var(--coral);margin-top:3px;display:flex;align-items:center;gap:4px;}

/* Password strength */
.pw-bars{display:flex;gap:3px;margin-top:6px;}
.pw-bar{height:3px;flex:1;border-radius:2px;background:var(--hair);transition:background .3s;}

/* Submit */
.btn-submit{width:100%;height:50px;border-radius:999px;background:var(--violet);color:#fff;font-size:15px;font-weight:700;font-family:inherit;border:none;cursor:pointer;transition:all .18s;margin-top:4px;display:flex;align-items:center;justify-content:center;gap:8px;}
.btn-submit:hover{background:#4C1D95;transform:translateY(-1px);box-shadow:0 6px 18px rgba(109,40,217,.3);}

.terms{font-size:12px;color:var(--muted);text-align:center;margin-top:10px;line-height:1.55;}
.terms a{color:var(--violet);}

.right-foot{padding:16px 32px;text-align:center;font-size:13px;color:var(--muted);border-top:1px solid var(--hair);}
.right-foot a{color:var(--violet);font-weight:600;}
</style>
</head>
<body>
<div class="page" x-data="{ role: '{{ old('role', request('role','student')) }}' }">

    {{-- LEFT PANEL --}}
    <div class="left">
        <a href="{{ route('home') }}" class="left-logo">
            <svg width="32" height="32" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
            <span>Mwalimu</span>
        </a>

        <div class="left-body">
            <template x-if="role === 'tutor'">
                <div>
                    <h2>Start <em>earning</em> with your expertise</h2>
                    <p>Join hundreds of verified tutors across Kenya. Set your own rates, choose your availability, and get paid.</p>
                    <div class="left-points">
                        @foreach(['Upload documents once — verified for life','Set your own hourly rate in KSh','Keep up to 85% of your earnings','Unique session codes sent by email'] as $p)
                        <div class="left-point">
                            <div class="left-point-dot"><i class="fa-solid fa-check" style="font-size:10px;"></i></div>
                            {{ $p }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </template>
            <template x-if="role !== 'tutor'">
                <div>
                    <h2>Learn from <em>verified</em> experts</h2>
                    <p>Find the perfect tutor for any subject. Book sessions in flexible time blocks and pay securely.</p>
                    <div class="left-points">
                        @foreach(['500+ verified tutors across Kenya','15, 30, 45 or 60-minute sessions','Unique access code for every session','4.9★ average rating from students'] as $p)
                        <div class="left-point">
                            <div class="left-point-dot"><i class="fa-solid fa-check" style="font-size:10px;"></i></div>
                            {{ $p }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </template>
        </div>

        <div class="left-stats">
            @foreach([['500+','Verified Tutors'],['4.9★','Avg Rating'],['80+','Subjects'],['10k+','Sessions']] as [$n,$l])
            <div class="lstat">
                <div class="lstat-n">{{ $n }}</div>
                <div class="lstat-l">{{ $l }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="right">
        <div class="right-head">
            <a href="{{ route('home') }}" class="right-logo">
                <svg width="28" height="28" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#15281F"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
                Mwalimu
            </a>
            <span style="font-size:14px;color:var(--muted);">
                Already have an account?
                <a href="{{ route('login') }}" style="color:var(--violet);font-weight:600;">Sign in</a>
            </span>
        </div>

        <div class="right-body">
            <div class="form-wrap">
                <h1>Create your account</h1>
                <p class="subtitle">Free to join. No credit card required.</p>

                {{-- Role selector --}}
                <div class="role-grid">
                    <div class="role-card" :class="role==='student'?'active':''" @click="role='student'">
                        <div class="role-icon"><i class="fa-solid fa-graduation-cap" style="color:var(--violet);"></i></div>
                        <div class="role-label">Student</div>
                        <div class="role-desc">I want to learn</div>
                    </div>
                    <div class="role-card" :class="role==='parent'?'active':''" @click="role='parent'">
                        <div class="role-icon"><i class="fa-solid fa-house-user" style="color:var(--coral);"></i></div>
                        <div class="role-label">Parent</div>
                        <div class="role-desc">Book for my child</div>
                    </div>
                    <div class="role-card" :class="role==='tutor'?'active':''" @click="role='tutor'">
                        <div class="role-icon"><i class="fa-solid fa-chalkboard-user" style="color:var(--mint);"></i></div>
                        <div class="role-label">Tutor</div>
                        <div class="role-desc">I want to teach</div>
                    </div>
                </div>

                {{-- Tutor notice --}}
                <div class="tutor-info" x-show="role==='tutor'" x-cloak>
                    <strong><i class="fa-solid fa-circle-info" style="margin-right:4px;"></i> What happens after you register:</strong>
                    Complete your profile → Upload verification documents → Admin approves → Profile goes live → Start earning
                </div>

                {{-- Social login --}}
                <div class="social-grid">
                    <a href="{{ route('auth.social.redirect','google') }}" class="soc-btn">
                        <i class="fa-brands fa-google" style="color:#4285F4;font-size:15px;"></i> Google
                    </a>
                    <a href="{{ route('auth.social.redirect','facebook') }}" class="soc-btn">
                        <i class="fa-brands fa-facebook" style="color:#1877F2;font-size:15px;"></i> Facebook
                    </a>
                    <a href="{{ route('auth.social.redirect','github') }}" class="soc-btn">
                        <i class="fa-brands fa-github" style="font-size:15px;"></i> GitHub
                    </a>
                </div>

                <div class="divider"><span>or register with email</span></div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <input type="hidden" name="role" :value="role">

                    <div class="row2">
                        <div class="field">
                            <label><i class="fa-solid fa-user"></i> Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Jane Mwangi" required class="{{ $errors->has('name')?'err':'' }}">
                            @error('name')<p class="ferr"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="field">
                            <label><i class="fa-solid fa-phone"></i> Phone <span style="font-size:10px;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+254 7XX XXX XXX">
                        </div>
                    </div>

                    <div class="field">
                        <label><i class="fa-solid fa-envelope"></i> Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required class="{{ $errors->has('email')?'err':'' }}">
                        @error('email')<p class="ferr"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                    </div>

                    <div class="row2">
                        <div class="field" x-data="pwStrength()">
                            <label><i class="fa-solid fa-lock"></i> Password</label>
                            <input type="password" name="password" placeholder="Min 8 characters" required
                                   class="{{ $errors->has('password')?'err':'' }}"
                                   @input="checkStrength($event.target.value)">
                            <div class="pw-bars">
                                <div class="pw-bar" :style="score>=1?'background:var(--coral)':''"></div>
                                <div class="pw-bar" :style="score>=2?'background:var(--gold)':''"></div>
                                <div class="pw-bar" :style="score>=3?'background:var(--mint)':''"></div>
                                <div class="pw-bar" :style="score>=4?'background:var(--mint)':''"></div>
                            </div>
                            @error('password')<p class="ferr"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="field">
                            <label><i class="fa-solid fa-lock"></i> Confirm Password</label>
                            <input type="password" name="password_confirmation" placeholder="Repeat password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-user-plus"></i>
                        <span x-text="role==='tutor'?'Create Tutor Account':role==='parent'?'Create Parent Account':'Create Student Account'">Create Account</span>
                    </button>
                </form>

                <p class="terms">
                    By creating an account you agree to our
                    <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                </p>
            </div>
        </div>

        <div class="right-foot">
            Already have an account? <a href="{{ route('login') }}">Sign in to Mwalimu</a>
        </div>
    </div>
</div>

<script>
function pwStrength() {
    return {
        score: 0,
        checkStrength(v) {
            let s = 0;
            if (v.length >= 8) s++;
            if (/[A-Z]/.test(v)) s++;
            if (/[0-9]/.test(v)) s++;
            if (/[^A-Za-z0-9]/.test(v)) s++;
            this.score = s;
        }
    }
}
</script>
</body>
</html>
