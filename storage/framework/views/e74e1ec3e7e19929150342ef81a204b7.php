<!DOCTYPE html>
<html lang="en">
<head>
<?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<title>Sign In – Mwalimu</title>
<style>
html,body{height:100%;margin:0;}
.page{display:flex;min-height:100vh;}

/* ── LEFT PANEL ── */
.left{display:none;background:var(--ink);padding:52px;flex-direction:column;justify-content:space-between;position:relative;overflow:hidden;width:460px;flex-shrink:0;}
@media(min-width:1024px){.left{display:flex;}}
.left-glow{position:absolute;border-radius:50%;pointer-events:none;}
.left-glow-1{top:-100px;right:-80px;width:340px;height:340px;background:rgba(109,40,217,.18);}
.left-glow-2{bottom:-80px;left:-60px;width:260px;height:260px;background:rgba(109,40,217,.1);}
.left-logo{display:flex;align-items:center;gap:10px;position:relative;z-index:1;}
.left-logo-text{font-family:'Fraunces',serif;font-size:24px;font-weight:800;color:#fff;}
.left-logo-text em{font-style:normal;color:#a78bfa;}
.left-body{position:relative;z-index:1;}
.left-body h2{font-family:'Fraunces',serif;font-size:36px;color:#fff;line-height:1.15;margin-bottom:14px;}
.left-body h2 em{font-style:normal;color:var(--gold);}
.left-body p{font-size:15px;color:rgba(255,255,255,.6);line-height:1.75;margin-bottom:28px;}
.trust-list{display:flex;flex-direction:column;gap:14px;}
.trust-item{display:flex;align-items:flex-start;gap:12px;}
.trust-dot{width:28px;height:28px;background:rgba(109,40,217,.4);border-radius:999px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.trust-item p{font-size:14px;color:rgba(255,255,255,.82);line-height:1.5;}
.left-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;position:relative;z-index:1;}
.lstat{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:14px;text-align:center;}
.lstat-n{font-family:'Fraunces',serif;font-size:24px;font-weight:700;color:#fff;}
.lstat-l{font-size:11px;color:rgba(255,255,255,.4);margin-top:2px;}

/* ── RIGHT PANEL ── */
.right{flex:1;background:var(--cream);display:flex;flex-direction:column;}
.right-head{padding:20px 32px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--hair);}
.right-logo{display:flex;align-items:center;gap:8px;font-family:'Fraunces',serif;font-size:20px;font-weight:800;color:var(--ink);}
.right-logo em{font-style:normal;color:var(--violet);}
.right-main{flex:1;display:flex;align-items:center;justify-content:center;padding:32px 24px;}
.form-box{width:100%;max-width:420px;}
.form-box h1{font-family:'Fraunces',serif;font-size:32px;color:var(--ink);margin-bottom:5px;}
.form-box .sub{font-size:14px;color:var(--muted);margin-bottom:28px;}

/* Social buttons */
.soc-row{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:18px;}
.soc-btn{display:flex;align-items:center;justify-content:center;gap:7px;height:44px;border-radius:12px;border:1.5px solid var(--hair);background:#fff;font-size:13px;font-weight:600;color:var(--ink);cursor:pointer;transition:all .18s;text-decoration:none;}
.soc-btn:hover{border-color:rgba(109,40,217,.4);transform:translateY(-1px);box-shadow:0 4px 12px rgba(21,40,31,.08);}

/* Divider */
.or-line{display:flex;align-items:center;gap:10px;margin:4px 0 20px;}
.or-line::before,.or-line::after{content:'';flex:1;height:1px;background:var(--hair);}
.or-line span{font-size:12px;color:var(--muted);}

/* Form fields */
.field{margin-bottom:14px;}
.field label{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;display:flex;align-items:center;gap:5px;margin-bottom:5px;}
.field label i{color:var(--violet);font-size:11px;}
.f-wrap{position:relative;}
.f-wrap i.lead{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px;pointer-events:none;}
.field input{width:100%;height:48px;padding:0 14px 0 38px;border-radius:13px;border:1.5px solid rgba(21,40,31,.18);background:#fff;font-size:14px;font-family:inherit;color:var(--ink);outline:none;transition:all .18s;}
.field input:focus{border-color:var(--violet);box-shadow:0 0 0 3px rgba(109,40,217,.1);}
.field input.err{border-color:var(--coral);}
.field-err{font-size:12px;color:var(--coral);margin-top:4px;display:flex;align-items:center;gap:4px;}

/* Remember row */
.remember-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;}
.check-wrap{display:flex;align-items:center;gap:8px;}
.check-wrap input{width:16px;height:16px;accent-color:var(--violet);cursor:pointer;}
.check-wrap label{font-size:13px;color:var(--muted);}
.forgot{font-size:13px;color:var(--violet);font-weight:600;transition:opacity .15s;}
.forgot:hover{opacity:.75;}

/* Submit */
.btn-submit{width:100%;height:52px;border-radius:999px;background:var(--violet);color:#fff;font-size:15px;font-weight:700;font-family:inherit;border:none;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;}
.btn-submit:hover{background:#4C1D95;transform:translateY(-1px);box-shadow:0 8px 22px rgba(109,40,217,.35);}

/* Bottom */
.form-foot{text-align:center;font-size:13px;color:var(--muted);margin-top:18px;}
.form-foot a{color:var(--violet);font-weight:700;}
.right-foot{padding:16px 32px;text-align:center;font-size:12px;color:var(--muted);border-top:1px solid var(--hair);}

/* Alert */
.alert{padding:12px 16px;border-radius:13px;font-size:13.5px;font-weight:500;margin-bottom:18px;display:flex;align-items:center;gap:9px;}
.alert-err{background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;}
</style>
</head>
<body>
<div class="page">

    
    <div class="left">
        <div class="left-glow left-glow-1"></div>
        <div class="left-glow left-glow-2"></div>

        <a href="<?php echo e(route('home')); ?>" class="left-logo">
            <svg width="34" height="34" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
            <span class="left-logo-text"><em>Mw</em>alimu</span>
        </a>

        <div class="left-body">
            <h2>Welcome <em>back</em> to your classroom.</h2>
            <p>Sign in to access your sessions, session codes, messages and dashboard. Kenya's most trusted tutoring platform.</p>
            <div class="trust-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                    ['fa-certificate','TSC-registered & background-checked tutors'],
                    ['fa-lock','Secure, private classrooms — no contact sharing'],
                    ['fa-rotate-left','100% money-back on your first lesson'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$icon, $text]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="trust-item">
                    <div class="trust-dot"><i class="fa-solid <?php echo e($icon); ?>" style="font-size:11px;color:rgba(255,255,255,.85);"></i></div>
                    <p><?php echo e($text); ?></p>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="left-stats">
            <div class="lstat">
                <div class="lstat-n">500+</div>
                <div class="lstat-l">Tutors</div>
            </div>
            <div class="lstat">
                <div class="lstat-n">4.9★</div>
                <div class="lstat-l">Rating</div>
            </div>
            <div class="lstat">
                <div class="lstat-n">10k+</div>
                <div class="lstat-l">Sessions</div>
            </div>
        </div>
    </div>

    
    <div class="right">
        <div class="right-head">
            <a href="<?php echo e(route('home')); ?>" class="right-logo">
                <svg width="28" height="28" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#15281F"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
                <em>Mw</em>alimu
            </a>
            <span style="font-size:13px;color:var(--muted);">
                No account?
                <a href="<?php echo e(route('register')); ?>" style="color:var(--violet);font-weight:700;">Create one free</a>
            </span>
        </div>

        <div class="right-main">
            <div class="form-box">
                <h1>Sign in</h1>
                <p class="sub">Access your Mwalimu dashboard and upcoming sessions.</p>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                <div class="alert alert-err">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php echo e($errors->first()); ?>

                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="soc-row">
                    <a href="<?php echo e(route('auth.social.redirect','google')); ?>" class="soc-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        Google
                    </a>
                    <a href="<?php echo e(route('auth.social.redirect','facebook')); ?>" class="soc-btn">
                        <i class="fa-brands fa-facebook" style="color:#1877F2;font-size:16px;"></i>
                        Facebook
                    </a>
                    <a href="<?php echo e(route('auth.social.redirect','github')); ?>" class="soc-btn">
                        <i class="fa-brands fa-github" style="font-size:16px;"></i>
                        GitHub
                    </a>
                </div>

                <div class="or-line"><span>or sign in with email</span></div>

                
                <form method="POST" action="<?php echo e(route('login')); ?>">
                    <?php echo csrf_field(); ?>

                    <div class="field">
                        <label><i class="fa-solid fa-envelope"></i> Email address</label>
                        <div class="f-wrap">
                            <i class="fa-solid fa-envelope lead"></i>
                            <input type="email" name="email" value="<?php echo e(old('email')); ?>"
                                   placeholder="you@example.com"
                                   class="<?php echo e($errors->has('email') ? 'err' : ''); ?>"
                                   autocomplete="email" required>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-err"><i class="fa-solid fa-triangle-exclamation fa-xs"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="field">
                        <label><i class="fa-solid fa-lock"></i> Password</label>
                        <div class="f-wrap" x-data="{ show: false }">
                            <i class="fa-solid fa-lock lead"></i>
                            <input :type="show ? 'text' : 'password'" name="password"
                                   placeholder="Your password"
                                   class="<?php echo e($errors->has('password') ? 'err' : ''); ?>"
                                   style="padding-right:42px;"
                                   autocomplete="current-password" required>
                            <button type="button" @click="show=!show"
                                    style="position:absolute;right:13px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;font-size:13px;">
                                <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                            </button>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-err"><i class="fa-solid fa-triangle-exclamation fa-xs"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="remember-row">
                        <div class="check-wrap">
                            <input type="checkbox" name="remember" id="remember" value="1">
                            <label for="remember">Keep me signed in</label>
                        </div>
                        <a href="#" class="forgot">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Sign In to Mwalimu
                    </button>
                </form>

                <p class="form-foot">
                    New to Mwalimu?
                    <a href="<?php echo e(route('register')); ?>">Create a free account</a>
                </p>

                <div style="margin-top:24px;background:rgba(109,40,217,.05);border:1px solid rgba(109,40,217,.12);border-radius:14px;padding:14px 16px;">
                    <p style="font-size:12px;font-weight:700;color:var(--violet);margin-bottom:6px;"><i class="fa-solid fa-graduation-cap fa-xs" style="margin-right:5px;"></i>Demo accounts</p>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [['Admin','admin@mwalimu.co.ke'],['Tutor','amara@mwalimu.co.ke'],['Student','alice@example.com'],['Parent','sarah@example.com']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$role,$email]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="background:#fff;border:1px solid var(--hair);border-radius:9px;padding:7px 10px;cursor:pointer;"
                             onclick="document.querySelector('[name=email]').value='<?php echo e($email); ?>';document.querySelector('[name=password]').value='password';"
                             title="Click to fill credentials">
                            <div style="font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;"><?php echo e($role); ?></div>
                            <div style="font-size:11px;color:var(--ink);margin-top:1px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo e($email); ?></div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <p style="font-size:11px;color:var(--muted);margin-top:8px;"><i class="fa-solid fa-info-circle fa-xs"></i> Password for all demo accounts: <code style="background:rgba(21,40,31,.06);padding:1px 6px;border-radius:4px;font-size:11px;">password</code></p>
                </div>
            </div>
        </div>

        <div class="right-foot">
            &copy; <?php echo e(date('Y')); ?> Mwalimu by OvalDesk Ventures &middot;
            <a href="#">Privacy</a> &middot; <a href="#">Terms</a>
        </div>
    </div>
</div>
</body>
</html>
<?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/auth/login.blade.php ENDPATH**/ ?>