<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>How It Works – TutorLink</title>
<meta name="description" content="Learn how TutorLink connects students with verified tutors in Kenya. Book a session in 3 simple steps.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

<style>
:root{--ink:#15281F;--cream:#FBF5E6;--violet:#6D28D9;--coral:#E8522A;--gold:#D4A017;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{background:var(--cream);}
body{font-family:'DM Sans',sans-serif;color:var(--ink);background:var(--cream);line-height:1.6;}
h1,h2,h3,h4{font-family:'Fraunces',Georgia,serif;line-height:1.1;}
a{color:inherit;text-decoration:none;}
.w{max-width:1360px;margin:0 auto;padding:0 24px;}
@media(min-width:1024px){.w{padding:0 48px;}}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 28px;border-radius:999px;font-weight:600;font-size:15px;cursor:pointer;transition:all .18s;border:none;font-family:inherit;text-decoration:none;}
.btn-v{background:var(--violet);color:#fff;}.btn-v:hover{background:#4C1D95;transform:translateY(-1px);box-shadow:0 6px 20px rgba(109,40,217,.3);}
.btn-o{background:transparent;color:var(--ink);border:2px solid var(--ink);}.btn-o:hover{background:var(--ink);color:var(--cream);}
.btn-w{background:#fff;color:var(--violet);font-weight:700;}.btn-w:hover{background:var(--cream);}
.card{background:#fff;border-radius:22px;border:1px solid var(--hair);box-shadow:0 2px 16px rgba(21,40,31,.06);}
nav{position:sticky;top:0;z-index:100;background:rgba(251,245,230,.92);backdrop-filter:blur(16px);border-bottom:1px solid var(--hair);}
.nav-inner{display:flex;align-items:center;justify-content:space-between;height:70px;gap:16px;}
.logo{display:flex;align-items:center;gap:10px;font-family:'Fraunces',serif;font-size:21px;font-weight:700;color:var(--ink);}
.desk-links{display:none;align-items:center;gap:4px;}
@media(min-width:1024px){.desk-links{display:flex;}}
.desk-links a{padding:8px 15px;border-radius:999px;font-size:14px;font-weight:500;transition:background .15s;}
.desk-links a:hover,.desk-links a.cur{background:rgba(21,40,31,.07);}
.desk-links a.cur{font-weight:700;color:var(--violet);}

/* HERO */
.hero{background:var(--ink);padding:80px 0;text-align:center;position:relative;overflow:hidden;}
.hero::before{content:'';position:absolute;top:-80px;left:50%;transform:translateX(-50%);width:600px;height:600px;background:rgba(109,40,217,.15);border-radius:50%;}
.hero-badge{display:inline-flex;align-items:center;gap:6px;background:rgba(109,40,217,.3);color:rgba(255,255,255,.9);padding:6px 16px;border-radius:999px;font-size:13px;font-weight:600;margin-bottom:20px;}
.hero h1{font-size:clamp(36px,6vw,64px);color:#fff;margin-bottom:16px;}
.hero h1 em{font-style:normal;color:var(--gold);}
.hero p{font-size:18px;color:rgba(255,255,255,.65);max-width:560px;margin:0 auto 32px;line-height:1.75;}

/* STEP CONNECTOR */
.steps-wrap{position:relative;padding:80px 0;}
.steps-line{position:absolute;left:50%;top:0;bottom:0;width:2px;background:linear-gradient(to bottom,transparent,var(--hair),var(--violet),var(--hair),transparent);transform:translateX(-50%);}

/* TABS */
.tab-bar{display:flex;gap:6px;flex-wrap:wrap;justify-content:center;margin-bottom:48px;}
.tab-btn{padding:10px 24px;border-radius:999px;border:1.5px solid var(--hair);background:#fff;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s;color:var(--muted);}
.tab-btn.active,.tab-btn:hover{background:var(--violet);color:#fff;border-color:var(--violet);}

/* FAQ */
.faq-item{background:#fff;border-radius:16px;border:1px solid var(--hair);overflow:hidden;margin-bottom:10px;}
.faq-q{display:flex;align-items:center;justify-content:space-between;padding:18px 22px;cursor:pointer;font-weight:600;font-size:15px;color:var(--ink);transition:background .15s;}
.faq-q:hover{background:#FAFCFA;}
.faq-a{padding:0 22px 18px;font-size:14px;color:var(--muted);line-height:1.8;}

/* Timeline */
.timeline{display:flex;flex-direction:column;gap:0;}
.tl-item{display:flex;gap:20px;align-items:flex-start;position:relative;padding-bottom:32px;}
.tl-item:last-child{padding-bottom:0;}
.tl-dot{width:44px;height:44px;border-radius:999px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px;font-weight:700;z-index:1;}
.tl-line{position:absolute;left:22px;top:44px;bottom:0;width:2px;transform:translateX(-50%);}
.tl-content{flex:1;padding-bottom:8px;}
.tl-title{font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:var(--ink);margin-bottom:6px;}
.tl-desc{font-size:14px;color:var(--muted);line-height:1.75;}

/* COMPARISON TABLE */
.cmp-table{width:100%;border-collapse:collapse;}
.cmp-table th{padding:14px 20px;text-align:left;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);border-bottom:2px solid var(--hair);}
.cmp-table td{padding:14px 20px;font-size:14px;border-bottom:1px solid rgba(21,40,31,.05);vertical-align:middle;}
.cmp-table tr:last-child td{border-bottom:none;}
.cmp-table tr:hover td{background:#FAFCFA;}
.chk{color:var(--mint);font-weight:700;font-size:16px;}
.cross{color:#DC2626;font-weight:700;font-size:16px;}

/* CODE DEMO */
.code-demo{background:rgba(109,40,217,.06);border:2px solid rgba(109,40,217,.2);border-radius:20px;padding:28px;text-align:center;}
.code-demo .code{font-family:monospace;font-size:36px;font-weight:800;color:var(--violet);letter-spacing:.12em;}
</style>
</head>
<body>


<nav x-data="{ open: false }">
    <div class="w">
        <div class="nav-inner">
            <a href="<?php echo e(route('home')); ?>" class="logo">
                <svg width="34" height="34" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#15281F"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
                TutorLink
            </a>
            <div class="desk-links">
                <a href="<?php echo e(route('tutors.index')); ?>">Find a Tutor</a>
                <a href="<?php echo e(route('courses.index')); ?>">Courses</a>
                <a href="<?php echo e(route('blog.index')); ?>">Blog</a>
                <a href="<?php echo e(route('how-it-works')); ?>" class="cur">How It Works</a>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-v" style="font-size:13px;padding:9px 18px;">Dashboard</a>
                <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" style="font-size:14px;font-weight:600;padding:8px 16px;border-radius:999px;transition:background .15s;display:none;" class="desk-signin" onmouseover="this.style.background='rgba(21,40,31,.07)'" onmouseout="this.style.background='transparent'">Sign In</a>
                <a href="<?php echo e(route('register')); ?>" class="btn btn-v" style="font-size:13px;padding:9px 18px;">Get Started Free</a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
    <style>.desk-signin{display:none!important;}@media(min-width:1024px){.desk-signin{display:block!important;}}</style>
</nav>


<section class="hero">
    <div class="w" style="position:relative;z-index:1;">
        <div class="hero-badge">📖 Simple · Secure · Effective</div>
        <h1>Learning made <em>simple</em></h1>
        <p>TutorLink connects Kenyan students and parents with verified expert tutors. Book a session in minutes, pay securely, and join a live class from anywhere.</p>
        <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;">
            <a href="<?php echo e(route('tutors.index')); ?>" class="btn btn-v" style="font-size:16px;padding:14px 36px;">Find a Tutor</a>
            <a href="<?php echo e(route('register')); ?>?role=tutor" class="btn btn-w" style="font-size:16px;padding:14px 36px;">Become a Tutor</a>
        </div>
        
        <div style="display:flex;flex-wrap:wrap;gap:32px;justify-content:center;margin-top:48px;padding-top:36px;border-top:1px solid rgba(255,255,255,.1);">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [['500+','Verified Tutors'],['4.9★','Average Rating'],['10,000+','Sessions Completed'],['48hr','Max Approval Time'],['80+','Subjects Covered']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$n,$l]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="text-align:center;">
                <div style="font-family:'Fraunces',serif;font-size:26px;font-weight:700;color:#fff;"><?php echo e($n); ?></div>
                <div style="font-size:12px;color:rgba(255,255,255,.45);margin-top:3px;"><?php echo e($l); ?></div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>


<section style="padding:72px 0 0;background:var(--cream);">
    <div class="w" x-data="{ role: 'student' }">
        <div style="text-align:center;margin-bottom:32px;">
            <h2 style="font-size:clamp(28px,4vw,42px);color:var(--ink);margin-bottom:10px;">How does it work?</h2>
            <p style="font-size:17px;color:var(--muted);">Choose your role to see the process</p>
        </div>

        
        <div style="display:flex;background:#fff;border-radius:999px;padding:4px;border:1px solid var(--hair);width:fit-content;margin:0 auto 52px;">
            <button @click="role='student'" :style="role==='student'?'background:var(--violet);color:#fff;':''" style="padding:10px 32px;border-radius:999px;border:none;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .2s;color:var(--muted);">👨‍🎓 I'm a Student / Parent</button>
            <button @click="role='tutor'" :style="role==='tutor'?'background:var(--violet);color:#fff;':''" style="padding:10px 32px;border-radius:999px;border:none;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .2s;color:var(--muted);">📚 I'm a Tutor</button>
        </div>

        
        <div x-show="role==='student'">
            <div style="display:grid;grid-template-columns:1fr;gap:20px;" class="flow-grid">
                <?php
                $studentSteps = [
                    ['1','🔍','Find Your Tutor','#6D28D9',
                        'Browse our vetted tutors by subject, price, and rating. Read real reviews from students and parents who\'ve already booked.',
                        ['Filter by subject (Maths, English, Physics + 80 more)','View ratings and student reviews','Check availability calendar','Compare session pricing']],
                    ['2','📅','Pick a Duration & Time','#10B981',
                        'Choose your session length — 15, 30, 45 or 60 minutes — and pick a time from your tutor\'s availability. Flexible to fit school and family routines.',
                        ['15 min – Quick Q&A or revision drill','30 min – Targeted concept work','45 min – Structured lesson','60 min – Full session, best value']],
                    ['3','💳','Pay Securely with Paystack','#E8522A',
                        'Complete your booking by paying through Paystack — Kenya\'s trusted payment gateway. Pay by M-Pesa, Visa, or Mastercard.',
                        ['M-Pesa, Visa & Mastercard accepted','Payment held securely until session confirms','Full refund if tutor cancels','Booking confirmed instantly']],
                    ['4','📧','Receive Your Access Code','#D4A017',
                        'Immediately after payment, you receive a confirmation email with your unique session access code (STU-XXXXXX) and all session details.',
                        ['Unique STU-XXXXXX code sent by email','Session date, time, and duration confirmed','Tutor contact and session info included','Reminder sent 1 hour before session']],
                    ['5','🎥','Join Your Live Session','#3B82F6',
                        'On session day, open your booking and enter your code to launch the Jitsi live classroom — no app download needed, runs in your browser.',
                        ['Enter STU-XXXXXX code to verify identity','Private Jitsi video room opens instantly','Screen sharing and whiteboard available','Session recording available (with consent)']],
                    ['6','⭐','Review & Rebook','#EC4899',
                        'After your session, leave a rating and review to help other students. Rebook your tutor directly from your dashboard with one click.',
                        ['Star rating + written review','Tutor appears in Saved Tutors for easy rebook','Session history tracked in your dashboard','Message tutor directly for follow-up']],
                ];
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $studentSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$num,$icon,$title,$color,$desc,$points]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card" style="display:grid;grid-template-columns:1fr;gap:0;" class="step-card">
                    <div style="padding:28px 28px 24px;">
                        <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;">
                            <div style="width:52px;height:52px;border-radius:14px;background:<?php echo e($color); ?>;display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;"><?php echo e($icon); ?></div>
                            <div>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:<?php echo e($color); ?>;">Step <?php echo e($num); ?></span>
                                </div>
                                <h3 style="font-size:20px;color:var(--ink);"><?php echo e($title); ?></h3>
                            </div>
                        </div>
                        <p style="font-size:14px;color:var(--muted);line-height:1.8;margin-bottom:16px;"><?php echo e($desc); ?></p>
                        <ul style="display:flex;flex-direction:column;gap:8px;list-style:none;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $points; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li style="display:flex;align-items:flex-start;gap:8px;font-size:13px;color:var(--ink);">
                                <span style="color:<?php echo e($color); ?>;font-weight:700;flex-shrink:0;margin-top:1px;">✓</span>
                                <?php echo e($point); ?>

                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($num === '4'): ?>
                    <div style="border-top:1px solid var(--hair);padding:20px 28px;background:rgba(109,40,217,.03);">
                        <div class="code-demo">
                            <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:10px;">Sample Session Email</p>
                            <div style="background:#fff;border-radius:14px;padding:20px;text-align:left;border:1px solid var(--hair);">
                                <p style="font-size:12px;color:var(--muted);">From: noreply@tutorlink.co.ke</p>
                                <p style="font-size:13px;font-weight:700;color:var(--ink);margin:6px 0;">✅ Booking Confirmed – Session with Dr. Amara K.</p>
                                <div style="background:rgba(109,40,217,.06);border:2px solid rgba(109,40,217,.2);border-radius:12px;padding:14px;margin:10px 0;text-align:center;">
                                    <p style="font-size:11px;color:var(--muted);margin-bottom:4px;">YOUR SESSION ACCESS CODE</p>
                                    <p style="font-family:monospace;font-size:28px;font-weight:800;color:var(--violet);">STU-M4N8RZ</p>
                                </div>
                                <p style="font-size:12px;color:var(--muted);">📅 Thursday, Nov 14 at 4:00 PM · 45 minutes · KSh 1,125 paid</p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div x-show="role==='tutor'" x-cloak>
            <div style="display:grid;grid-template-columns:1fr;gap:20px;" class="flow-grid">
                <?php
                $tutorSteps = [
                    ['1','📝','Sign Up as a Tutor','#6D28D9',
                        'Create your free account, select "Tutor" as your role, and complete your basic profile with your name, email and city.',
                        ['Free to join — no upfront fees','Takes under 5 minutes','Select subjects you teach','Set your hourly rate (min KSh 500)']],
                    ['2','📄','Upload Verification Documents','#10B981',
                        'Upload your National ID and optionally teaching certificates or academic qualifications. Our team reviews documents within 24–48 hours.',
                        ['National ID / Passport (required)','Teaching certificate (optional but recommended)','Academic qualifications (optional)','Accepted: JPG, PNG, PDF up to 5MB']],
                    ['3','⏳','Get Reviewed & Approved','#D4A017',
                        'Our verification team reviews your documents and profile. You\'ll receive an email once approved — usually within 24–48 hours.',
                        ['Admin reviews all submitted documents','You\'re notified by email when approved','Request re-upload if any doc is rejected','Profile goes live immediately after approval']],
                    ['4','📅','Set Your Availability','#E8522A',
                        'Once approved, set your weekly schedule — which days and hours students can book sessions with you. Update it anytime from your dashboard.',
                        ['Set available hours per day of the week','Students can only book within your schedule','Update availability anytime','Block off holidays or busy periods']],
                    ['5','🔔','Receive Booking Notifications','#3B82F6',
                        'When a student books and pays, you receive an email immediately with the student\'s name, session details, your earnings, and your unique session code.',
                        ['Email: "New Session Booked – [Student] on [Date]"','Your earnings shown (after platform fee)','Unique TUT-XXXXXX access code sent to you','Also visible in your dashboard']],
                    ['6','🎥','Host Your Live Session','#EC4899',
                        'On session day, enter your TUT-XXXXXX code to open your private Jitsi classroom as the moderator. You control recording and screen sharing.',
                        ['Enter TUT-XXXXXX code to join as moderator','Full video, audio, screen share and whiteboard','Recording with student consent','Earnings credited to wallet after session']],
                    ['7','💰','Get Paid to Your Wallet','#10B981',
                        'After each completed session, your earnings are automatically credited to your TutorLink wallet (platform takes 15% commission). Request a payout anytime.',
                        ['Earnings credited after session completes','Platform fee: 15% (you keep 85%)','Request payout to M-Pesa or bank','Processed within 2–3 business days']],
                ];
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tutorSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$num,$icon,$title,$color,$desc,$points]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card" style="padding:28px;">
                    <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;">
                        <div style="width:52px;height:52px;border-radius:14px;background:<?php echo e($color); ?>;display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;"><?php echo e($icon); ?></div>
                        <div>
                            <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:<?php echo e($color); ?>;">Step <?php echo e($num); ?></span>
                            <h3 style="font-size:20px;color:var(--ink);"><?php echo e($title); ?></h3>
                        </div>
                    </div>
                    <p style="font-size:14px;color:var(--muted);line-height:1.8;margin-bottom:14px;"><?php echo e($desc); ?></p>
                    <ul style="display:flex;flex-direction:column;gap:7px;list-style:none;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $points; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li style="display:flex;align-items:flex-start;gap:8px;font-size:13px;">
                            <span style="color:<?php echo e($color); ?>;font-weight:700;flex-shrink:0;margin-top:1px;">✓</span>
                            <span style="color:var(--ink);"><?php echo e($point); ?></span>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
    <style>.flow-grid{grid-template-columns:1fr!important;}@media(min-width:768px){.flow-grid{grid-template-columns:repeat(2,1fr)!important;}}@media(min-width:1024px){.flow-grid{grid-template-columns:repeat(3,1fr)!important;}}</style>
</section>


<section style="padding:80px 0;background:var(--ink);">
    <div class="w">
        <div style="text-align:center;margin-bottom:48px;">
            <h2 style="font-size:clamp(26px,4vw,40px);color:#fff;margin-bottom:10px;">Session Duration Blocks</h2>
            <p style="font-size:17px;color:rgba(255,255,255,.55);">Choose the block that fits your needs and budget. Prices are calculated from the tutor's hourly rate.</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:40px;" class="dur-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                ['15','Quick Focus','⚡','Perfect for a quick question, revision drill, or checking a specific topic you\'re stuck on.','Ideal for: Daily revision, quick questions, exam week check-ins','From KSh 375'],
                ['30','Half Hour','📘','Structured half-hour session for working through a problem set or clarifying a concept in depth.','Ideal for: Homework help, concept explanations, KCSE prep','From KSh 750'],
                ['45','Extended','🎯','A full lesson with time for warm-up, teaching, practice and Q&A. The most popular session length.','Ideal for: Weekly lessons, exam preparation, new topics','From KSh 1,125'],
                ['60','Full Hour','🏆','Maximum learning time. Best value per minute. Great for students with multiple topics to cover.','Ideal for: Holiday intensive prep, multiple subjects, catching up','From KSh 1,500'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$mins,$title,$icon,$desc,$ideal,$from]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:20px;padding:26px;transition:background .2s;" onmouseover="this.style.background='rgba(255,255,255,.13)'" onmouseout="this.style.background='rgba(255,255,255,.07)'">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                    <div style="width:48px;height:48px;background:rgba(109,40,217,.3);border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:22px;"><?php echo e($icon); ?></div>
                    <div>
                        <div style="font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:#fff;line-height:1;"><?php echo e($mins); ?><span style="font-size:15px;color:rgba(255,255,255,.4);"> min</span></div>
                        <div style="font-size:14px;font-weight:600;color:rgba(255,255,255,.75);"><?php echo e($title); ?></div>
                    </div>
                </div>
                <p style="font-size:13px;color:rgba(255,255,255,.55);line-height:1.7;margin-bottom:10px;"><?php echo e($desc); ?></p>
                <p style="font-size:12px;color:rgba(255,255,255,.4);font-style:italic;margin-bottom:14px;"><?php echo e($ideal); ?></p>
                <span style="font-size:14px;font-weight:700;color:var(--gold);"><?php echo e($from); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div style="text-align:center;">
            <p style="font-size:14px;color:rgba(255,255,255,.45);margin-bottom:8px;">Pricing formula: Hourly rate × duration multiplier (0.25 / 0.50 / 0.75 / 1.00)</p>
            <a href="<?php echo e(route('tutors.index')); ?>" class="btn btn-w" style="font-size:15px;padding:13px 32px;">Browse Tutors & See Prices</a>
        </div>
    </div>
    <style>.dur-grid{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:768px){.dur-grid{grid-template-columns:repeat(4,1fr)!important;}}</style>
</section>


<section style="padding:80px 0;">
    <div class="w">
        <div style="display:grid;grid-template-columns:1fr;gap:48px;align-items:center;" class="code-grid">
            <div>
                <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(109,40,217,.1);color:var(--violet);padding:6px 14px;border-radius:999px;font-size:13px;font-weight:600;margin-bottom:20px;">🔐 Secure Session Access</div>
                <h2 style="font-size:clamp(28px,4vw,42px);color:var(--ink);margin-bottom:16px;">Your unique session access code</h2>
                <p style="font-size:16px;color:var(--muted);line-height:1.8;margin-bottom:28px;">After booking, both the tutor and student receive a unique one-time access code by email. This ensures only the right people join each private session.</p>
                <div style="display:flex;flex-direction:column;gap:16px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                        ['📧','Sent instantly by email','Codes arrive in your inbox immediately after payment confirmation.'],
                        ['🔒','Unique per booking','Each session generates a fresh STU-XXXXXX (student) and TUT-XXXXXX (tutor) code.'],
                        ['🎥','Opens private Jitsi room','Enter your code on the booking page to launch a private, encrypted video classroom.'],
                        ['🛡️','Prevents unauthorised access','Only the code holder can enter. No public links. No uninvited guests.'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$icon,$title,$desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display:flex;align-items:flex-start;gap:14px;">
                        <div style="width:46px;height:46px;background:rgba(109,40,217,.1);border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;"><?php echo e($icon); ?></div>
                        <div>
                            <div style="font-weight:700;font-size:15px;color:var(--ink);"><?php echo e($title); ?></div>
                            <div style="font-size:13px;color:var(--muted);margin-top:3px;line-height:1.6;"><?php echo e($desc); ?></div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <div>
                <div class="card" style="padding:36px;text-align:center;">
                    <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:20px;">Two codes. One session.</p>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:24px;">
                        <div style="background:rgba(109,40,217,.06);border:2px solid rgba(109,40,217,.2);border-radius:16px;padding:20px;">
                            <p style="font-size:11px;color:var(--muted);font-weight:600;margin-bottom:8px;">STUDENT CODE</p>
                            <p style="font-family:monospace;font-size:22px;font-weight:800;color:var(--violet);">STU-XXXXXX</p>
                            <p style="font-size:11px;color:var(--muted);margin-top:6px;">Sent to student/parent</p>
                        </div>
                        <div style="background:rgba(16,185,129,.06);border:2px solid rgba(16,185,129,.2);border-radius:16px;padding:20px;">
                            <p style="font-size:11px;color:var(--muted);font-weight:600;margin-bottom:8px;">TUTOR CODE</p>
                            <p style="font-family:monospace;font-size:22px;font-weight:800;color:var(--mint);">TUT-XXXXXX</p>
                            <p style="font-size:11px;color:var(--muted);margin-top:6px;">Sent to tutor</p>
                        </div>
                    </div>
                    <div style="background:#F9FAF9;border-radius:14px;padding:16px;text-align:left;">
                        <p style="font-size:12px;font-weight:700;color:var(--ink);margin-bottom:8px;">How to join:</p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['1. Open your booking on TutorLink','2. Click "Join Session"','3. Enter your code (STU- or TUT-)','4. Jitsi classroom opens instantly in your browser']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p style="font-size:13px;color:var(--muted);margin-bottom:4px;"><?php echo e($step); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>.code-grid{grid-template-columns:1fr!important;}@media(min-width:1024px){.code-grid{grid-template-columns:1fr 1fr!important;}}</style>
</section>


<section style="padding:80px 0;background:rgba(21,40,31,.03);">
    <div class="w">
        <div style="text-align:center;margin-bottom:44px;">
            <h2 style="font-size:clamp(26px,4vw,40px);color:var(--ink);margin-bottom:10px;">TutorLink vs Traditional Tutoring</h2>
            <p style="font-size:16px;color:var(--muted);">See why thousands of Kenyan families choose TutorLink</p>
        </div>
        <div class="card" style="overflow:hidden;">
            <table class="cmp-table">
                <thead>
                    <tr style="background:#F9FAF9;">
                        <th style="width:40%;">Feature</th>
                        <th style="color:var(--violet);">✓ TutorLink</th>
                        <th style="color:var(--muted);">Traditional Tutoring</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                        ['Verified tutors','All tutors ID-verified','Often unverified'],
                        ['Booking process','Online, instant, 24/7','Phone calls, scheduling hassle'],
                        ['Payment security','Paystack – M-Pesa & cards','Cash, often no receipt'],
                        ['Flexible session lengths','15, 30, 45 or 60 min','Usually 1hr fixed, wasted time'],
                        ['Access codes for security','Unique code per session','No access control'],
                        ['Live session platform','Jitsi – no download','Physical location or WhatsApp'],
                        ['Session history & records','Full dashboard history','No records'],
                        ['Reviews & ratings','Verified student reviews','No accountability'],
                        ['Cancellation policy','Free up to 24hrs before','Usually non-refundable'],
                        ['Pricing transparency','Clear KSh pricing upfront','Negotiated, opaque'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$feature,$tl,$trad]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td style="font-weight:500;"><?php echo e($feature); ?></td>
                        <td><span class="chk">✓</span> <span style="font-size:13px;color:var(--ink);"><?php echo e($tl); ?></span></td>
                        <td><span class="cross">✗</span> <span style="font-size:13px;color:var(--muted);"><?php echo e($trad); ?></span></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>


<section style="padding:80px 0;">
    <div class="w" style="max-width:760px;">
        <div style="text-align:center;margin-bottom:44px;">
            <h2 style="font-size:clamp(26px,4vw,40px);color:var(--ink);margin-bottom:10px;">Frequently Asked Questions</h2>
            <p style="font-size:16px;color:var(--muted);">Everything you need to know before getting started</p>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
            ['How do I know tutors are trustworthy?','Every tutor on TutorLink goes through our verification process: they upload a National ID, and optionally teaching certificates and academic qualifications. Our team manually reviews each document before approving a tutor\'s profile. Approved tutors display a green "✓ Verified" badge.'],
            ['What if I\'m not happy with my session?','Contact our support team within 24 hours of your session. We review all disputes fairly and offer refunds where appropriate. You can also rate and review your tutor to help other students.'],
            ['Do I need to download anything to join a session?','No. Sessions run in your browser using Jitsi Meet. No app download is required. Just enter your access code on the booking page and your session opens in a new tab.'],
            ['Can parents book sessions for their children?','Yes. Select "Parent" when creating your account. You can add child profiles (name, age, grade level) and book sessions on their behalf. The session code is sent to your email.'],
            ['How are session prices calculated?','Each tutor sets their hourly rate. Session prices are automatically calculated: 15min = 25%, 30min = 50%, 45min = 75%, 60min = 100% of the hourly rate. All prices are shown in KSh before you book.'],
            ['How and when do tutors get paid?','After a session is completed, the tutor\'s earnings (85% of the session fee — TutorLink keeps 15%) are credited to their in-app wallet. Tutors can request a payout to M-Pesa or bank at any time. Payouts are processed within 2–3 business days.'],
            ['What happens if a tutor cancels?','If a tutor cancels a confirmed session, you receive a full refund automatically. We also flag repeated cancellations and may suspend tutors who cancel excessively.'],
            ['Can I message my tutor before booking?','Once a booking is confirmed, you can message your tutor through the in-app chat. Pre-booking messages aren\'t available to prevent off-platform arrangements.'],
            ['Is my payment information secure?','Yes. All payments are processed by Paystack, a PCI-DSS compliant payment gateway trusted by thousands of African businesses. We never store your card details.'],
            ['How do I become a tutor on TutorLink?','Sign up and select "Tutor" as your role, complete your profile, upload your National ID, and set your availability. Our team reviews your application within 48 hours. There\'s no joining fee.'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$q,$a]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="faq-item" x-data="{ open: false }">
            <div class="faq-q" @click="open=!open">
                <span><?php echo e($q); ?></span>
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" :style="open?'transform:rotate(180deg)':''" style="flex-shrink:0;transition:.2s"><path d="M6 9l6 6 6-6"/></svg>
            </div>
            <div class="faq-a" x-show="open" x-transition x-cloak><?php echo e($a); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>


<section style="background:var(--ink);padding:90px 0;">
    <div class="w" style="display:grid;grid-template-columns:1fr;gap:40px;align-items:center;" class="cta-grid">
        <div style="text-align:center;">
            <h2 style="font-size:clamp(28px,5vw,52px);color:#fff;margin-bottom:14px;">Ready to get started?</h2>
            <p style="font-size:18px;color:rgba(255,255,255,.6);margin-bottom:36px;max-width:480px;margin-left:auto;margin-right:auto;">Join thousands of students, parents and tutors on Kenya's leading tutoring platform.</p>
            <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;">
                <a href="<?php echo e(route('tutors.index')); ?>" class="btn btn-v" style="font-size:16px;padding:15px 40px;">Find a Tutor</a>
                <a href="<?php echo e(route('register')); ?>?role=tutor" class="btn btn-w" style="font-size:16px;padding:15px 40px;">Become a Tutor</a>
            </div>
        </div>
    </div>
    <style>.cta-grid{grid-template-columns:1fr!important;}</style>
</section>


<footer style="background:var(--ink);border-top:1px solid rgba(255,255,255,.07);padding:40px 0;">
    <div class="w" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <a href="<?php echo e(route('home')); ?>" style="display:flex;align-items:center;gap:8px;font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:#fff;">
            <svg width="26" height="26" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>TutorLink
        </a>
        <div style="display:flex;gap:20px;flex-wrap:wrap;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [['home','Home'],['tutors.index','Find Tutors'],['courses.index','Courses'],['blog.index','Blog']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$r,$l]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route($r)); ?>" style="font-size:13px;color:rgba(255,255,255,.55);transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.55)'"><?php echo e($l); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <span style="font-size:12px;color:rgba(255,255,255,.3);">&copy; <?php echo e(date('Y')); ?> TutorLink. All rights reserved.</span>
    </div>
</footer>

<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/pages/how-it-works.blade.php ENDPATH**/ ?>