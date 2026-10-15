<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Examiner's Clinic – Mwalimu</title>
<meta name="description" content="Learn directly from Kenya's national examiners and markers. Group sessions on exam strategy, marking schemes, and KNEC examiner tips. KSh 1,000 per session.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

<style>
:root{--ink:#15281F;--cream:#FBF5E6;--violet:#6D28D9;--coral:#E8522A;--gold:#D4A017;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{background:var(--cream);}
body{font-family:'DM Sans',sans-serif;color:var(--ink);background:var(--cream);line-height:1.6;}
h1,h2,h3,h4{font-family:'Fraunces',Georgia,serif;line-height:1.1;}
a{color:inherit;text-decoration:none;}[x-cloak]{display:none!important;}
.w{max-width:1360px;margin:0 auto;padding:0 24px;}
@media(min-width:1024px){.w{padding:0 48px;}}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 28px;border-radius:999px;font-weight:600;font-size:15px;cursor:pointer;transition:all .18s;border:none;font-family:inherit;text-decoration:none;}
.btn-v{background:var(--violet);color:#fff;}.btn-v:hover{background:#4C1D95;transform:translateY(-1px);}
.btn-gold{background:var(--gold);color:var(--ink);}.btn-gold:hover{opacity:.9;transform:translateY(-1px);}
.btn-o{background:transparent;color:var(--ink);border:2px solid var(--ink);}.btn-o:hover{background:var(--ink);color:var(--cream);}
.card{background:#fff;border-radius:22px;border:1px solid var(--hair);box-shadow:0 2px 16px rgba(21,40,31,.06);}
nav{position:sticky;top:0;z-index:100;background:rgba(251,245,230,.92);backdrop-filter:blur(16px);border-bottom:1px solid var(--hair);}
.nav-inner{display:flex;align-items:center;justify-content:space-between;height:70px;gap:16px;}
.logo{display:flex;align-items:center;gap:10px;font-family:'Fraunces',serif;font-size:22px;font-weight:800;color:var(--ink);}
.logo em{font-style:normal;color:var(--violet);}
</style>
</head>
<body>
<?php
use App\Models\ExaminerClinic;
use App\Models\Category;
$clinics = ExaminerClinic::upcoming()->with(['tutor','subject'])->orderBy('scheduled_at')->get();
$subjects = \App\Models\Subject::active()->with('category')->get();
?>


<nav>
    <div class="w">
        <div class="nav-inner">
            <a href="<?php echo e(route('home')); ?>" class="logo">
                <svg width="34" height="34" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#15281F"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
                <em>Mwalimu</em>
            </a>
            <div style="display:flex;align-items:center;gap:10px;">
                <a href="<?php echo e(route('tutors.index')); ?>" class="btn btn-o" style="font-size:13px;padding:8px 18px;"><i class="fa-solid fa-magnifying-glass"></i> Find Tutor</a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-v" style="font-size:13px;padding:8px 18px;"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
                <?php else: ?>
                <a href="<?php echo e(route('register')); ?>" class="btn btn-v" style="font-size:13px;padding:8px 18px;"><i class="fa-solid fa-user-plus"></i> Get Started</a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</nav>


<section style="background:var(--ink);padding:72px 0;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-60px;right:-60px;width:300px;height:300px;background:rgba(212,160,23,.1);border-radius:50%;"></div>
    <div style="position:absolute;bottom:-40px;left:-40px;width:200px;height:200px;background:rgba(109,40,217,.15);border-radius:50%;"></div>
    <div class="w" style="position:relative;z-index:1;">
        <div style="display:grid;grid-template-columns:1fr;gap:48px;align-items:center;" class="ec-hg">
            <div>
                <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(212,160,23,.2);color:var(--gold);padding:6px 16px;border-radius:999px;font-size:13px;font-weight:600;margin-bottom:22px;">
                    <i class="fa-solid fa-star"></i> The Examiner's Clinic
                </div>
                <h1 style="font-size:clamp(34px,5vw,58px);color:#fff;margin-bottom:16px;">
                    Learn from the experts who <span style="color:var(--gold);">set the exams.</span>
                </h1>
                <p style="font-size:17px;color:rgba(255,255,255,.65);max-width:560px;margin-bottom:28px;line-height:1.8;">
                    Most revision resources focus on <em>what</em> to study. The Examiner's Clinic focuses on <strong style="color:#fff;">how to answer questions</strong> — delivered by national examiners and markers from top-performing schools, once every week per subject.
                </p>
                <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:32px;">
                    <a href="#clinics" class="btn btn-gold"><i class="fa-solid fa-calendar-check"></i> Book a Clinic</a>
                    <a href="<?php echo e(route('register')); ?>" class="btn" style="background:rgba(255,255,255,.12);color:#fff;border:1.5px solid rgba(255,255,255,.3);">
                        <i class="fa-solid fa-user-plus"></i> Create Free Account
                    </a>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:20px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [['fa-sterling-sign','KSh 1,000','Per 60-min session'],['fa-users','Group format','Up to 50 learners'],['fa-calendar-week','Weekly','Every subject'],['fa-shield-check','Secure','Strict safeguards']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$ic,$val,$lbl]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <i class="fa-solid <?php echo e($ic); ?>" style="color:var(--gold);font-size:16px;width:20px;text-align:center;"></i>
                        <div>
                            <div style="font-size:14px;font-weight:700;color:#fff;"><?php echo e($val); ?></div>
                            <div style="font-size:11px;color:rgba(255,255,255,.45);"><?php echo e($lbl); ?></div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <div>
                <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:24px;padding:28px;">
                    <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.4);margin-bottom:16px;">What each clinic covers</p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Exam strategy & question time management','High-frequency exam questions by topic','Examiner traps — mistakes that lose marks','Marking scheme analysis — how marks are awarded','Common student errors and how to avoid them','How KNEC examiners grade papers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:12px;">
                        <i class="fa-solid fa-circle-check" style="color:var(--mint);margin-top:2px;flex-shrink:0;font-size:14px;"></i>
                        <span style="font-size:14px;color:rgba(255,255,255,.8);"><?php echo e($item); ?></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div style="margin-top:20px;padding-top:16px;border-top:1px solid rgba(255,255,255,.12);text-align:center;">
                        <span style="font-family:'Fraunces',serif;font-size:32px;font-weight:800;color:var(--gold);">KSh 1,000</span>
                        <span style="font-size:14px;color:rgba(255,255,255,.5);margin-left:6px;">/ 60-minute session</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>.ec-hg{grid-template-columns:1fr!important;}@media(min-width:1024px){.ec-hg{grid-template-columns:1fr 420px!important;}}</style>
</section>


<section style="padding:60px 0;background:rgba(21,40,31,.02);">
    <div class="w" style="max-width:900px;">
        <div style="text-align:center;margin-bottom:36px;">
            <h2 style="font-size:clamp(24px,4vw,36px);color:var(--ink);margin-bottom:8px;">Why the Examiner's Clinic?</h2>
            <p style="color:var(--muted);font-size:16px;">It's a fraction of the cost of long-term tuition, delivered in a focused, high-impact group environment.</p>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;" class="vs-g">
            <div class="card" style="padding:24px;border-left:4px solid var(--hair);">
                <h3 style="font-size:16px;color:var(--muted);margin-bottom:16px;"><i class="fa-solid fa-book fa-sm" style="margin-right:6px;"></i> Regular Revision</h3>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Focuses on what to study','Content-heavy','You re-read notes hoping something sticks','No insight into examiner expectations','Expensive private tuition required']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;font-size:13px;color:var(--muted);">
                    <i class="fa-solid fa-xmark" style="color:#DC2626;margin-top:2px;flex-shrink:0;"></i><?php echo e($item); ?>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="card" style="padding:24px;border-left:4px solid var(--gold);">
                <h3 style="font-size:16px;color:var(--ink);margin-bottom:16px;"><i class="fa-solid fa-star fa-sm" style="color:var(--gold);margin-right:6px;"></i> Examiner's Clinic</h3>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Focuses on HOW to answer','Strategic, marks-focused','Taught by the person who sets your exam','Direct insight into KNEC marking criteria','KSh 1,000 for a 60-minute group session']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;font-size:13px;color:var(--ink);">
                    <i class="fa-solid fa-circle-check" style="color:var(--mint);margin-top:2px;flex-shrink:0;"></i><?php echo e($item); ?>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <style>.vs-g{grid-template-columns:1fr!important;}@media(min-width:640px){.vs-g{grid-template-columns:1fr 1fr!important;}}</style>
    </div>
</section>


<section id="clinics" style="padding:72px 0;">
    <div class="w">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:32px;flex-wrap:wrap;gap:16px;">
            <div>
                <h2 style="font-size:clamp(24px,4vw,38px);color:var(--ink);margin-bottom:6px;">Upcoming Clinics</h2>
                <p style="color:var(--muted);">Scheduled once every week per subject. Conducted by highest-rated National Examiners.</p>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clinics->isEmpty()): ?>
        
        <?php
        $demoClinics = [
            ['Advanced Mathematics – Paper 2 Strategy','Dr. Amara Kimani','Mathematics','KCSE','Thu, Nov 14 at 4:00 PM','12 spots left','#6D28D9'],
            ['KCSE English – Comprehension & Essay Writing','Ms. Sarah Njoroge','English','KCSE','Sat, Nov 16 at 10:00 AM','23 spots left','#10B981'],
            ['Physics Paper 1 – Examiner Traps & Common Errors','Prof. James Mwangi','Physics','KCSE','Mon, Nov 18 at 5:00 PM','8 spots left','#E8522A'],
            ['Chemistry – Organic Chemistry Marking Scheme Analysis','Mr. David Otieno','Chemistry','KCSE','Wed, Nov 20 at 4:30 PM','31 spots left','#D4A017'],
        ];
        ?>
        <div style="display:grid;grid-template-columns:1fr;gap:16px;" class="clinic-g">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $demoClinics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$title,$tutor,$subject,$level,$date,$spots,$color]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card" style="padding:24px;display:flex;align-items:flex-start;gap:20px;flex-wrap:wrap;">
                <div style="width:56px;height:56px;background:<?php echo e($color); ?>22;border-radius:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-solid fa-chalkboard-user" style="color:<?php echo e($color); ?>;font-size:22px;"></i>
                </div>
                <div style="flex:1;min-width:200px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:6px;">
                        <h3 style="font-size:17px;color:var(--ink);font-family:'Fraunces',serif;max-width:500px;"><?php echo e($title); ?></h3>
                        <span style="font-family:'Fraunces',serif;font-size:20px;font-weight:800;color:var(--gold);flex-shrink:0;">KSh 1,000</span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px;">
                        <span style="background:rgba(109,40,217,.1);color:var(--violet);padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;"><i class="fa-solid fa-user fa-xs"></i> <?php echo e($tutor); ?></span>
                        <span style="background:rgba(21,40,31,.06);color:var(--ink);padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;"><i class="fa-solid fa-book-open fa-xs"></i> <?php echo e($subject); ?></span>
                        <span style="background:rgba(212,160,23,.1);color:#8B6914;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;"><i class="fa-solid fa-graduation-cap fa-xs"></i> <?php echo e($level); ?></span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:16px;font-size:13px;color:var(--muted);">
                        <span><i class="fa-regular fa-calendar fa-xs" style="color:var(--violet);margin-right:4px;"></i><?php echo e($date); ?></span>
                        <span><i class="fa-solid fa-clock fa-xs" style="color:var(--violet);margin-right:4px;"></i>60 minutes</span>
                        <span><i class="fa-solid fa-users fa-xs" style="color:var(--mint);margin-right:4px;"></i><?php echo e($spots); ?></span>
                    </div>
                </div>
                <div style="flex-shrink:0;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-gold" style="padding:11px 24px;font-size:14px;"><i class="fa-solid fa-ticket"></i> Book Spot</a>
                    <?php else: ?>
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-gold" style="padding:11px 24px;font-size:14px;"><i class="fa-solid fa-ticket"></i> Book Spot</a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php else: ?>
        <div style="display:grid;grid-template-columns:1fr;gap:16px;" class="clinic-g">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $clinics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clinic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card" style="padding:24px;display:flex;align-items:flex-start;gap:20px;flex-wrap:wrap;">
                <div style="width:56px;height:56px;background:rgba(212,160,23,.15);border-radius:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-solid fa-chalkboard-user" style="color:var(--gold);font-size:22px;"></i>
                </div>
                <div style="flex:1;min-width:200px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:6px;">
                        <h3 style="font-size:17px;color:var(--ink);font-family:'Fraunces',serif;"><?php echo e($clinic->title); ?></h3>
                        <span style="font-family:'Fraunces',serif;font-size:20px;font-weight:800;color:var(--gold);">KSh <?php echo e(number_format($clinic->price)); ?></span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px;">
                        <span style="background:rgba(109,40,217,.1);color:var(--violet);padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;"><i class="fa-solid fa-user fa-xs"></i> <?php echo e($clinic->tutor->name); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clinic->subject): ?><span style="background:rgba(21,40,31,.06);color:var(--ink);padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;"><i class="fa-solid fa-book-open fa-xs"></i> <?php echo e($clinic->subject->name); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:16px;font-size:13px;color:var(--muted);">
                        <span><i class="fa-regular fa-calendar fa-xs" style="color:var(--violet);margin-right:4px;"></i><?php echo e($clinic->scheduled_at->format('D, M j \a\t g:ia')); ?></span>
                        <span><i class="fa-solid fa-clock fa-xs" style="color:var(--violet);margin-right:4px;"></i><?php echo e($clinic->duration_minutes); ?> minutes</span>
                        <span><i class="fa-solid fa-users fa-xs" style="color:var(--mint);margin-right:4px;"></i><?php echo e($clinic->spotsLeft()); ?> spots left</span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clinic->description): ?><p style="font-size:13px;color:var(--muted);margin-top:8px;line-height:1.6;"><?php echo e($clinic->description); ?></p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div style="flex-shrink:0;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clinic->isEnrolledBy(auth()->user())): ?>
                    <span style="background:rgba(16,185,129,.1);color:var(--mint);padding:10px 20px;border-radius:999px;font-size:14px;font-weight:600;"><i class="fa-solid fa-check"></i> Enrolled</span>
                    <?php else: ?>
                    <form method="POST" action="/clinic/<?php echo e($clinic->id); ?>/enroll">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-gold" style="padding:11px 24px;font-size:14px;"><i class="fa-solid fa-ticket"></i> Book Spot</button>
                    </form>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-gold" style="padding:11px 24px;font-size:14px;"><i class="fa-solid fa-ticket"></i> Book Spot</a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <style>.clinic-g{grid-template-columns:1fr!important;}</style>
    </div>
</section>


<section style="padding:72px 0;background:rgba(21,40,31,.03);">
    <div class="w" style="max-width:900px;">
        <div style="text-align:center;margin-bottom:40px;">
            <h2 style="font-size:clamp(24px,4vw,38px);color:var(--ink);margin-bottom:8px;">For Tutors & Examiners</h2>
            <p style="color:var(--muted);font-size:16px;">Monetize your marking experience with zero administrative friction and instant payouts.</p>
        </div>
        <div style="display:grid;grid-template-columns:1fr;gap:14px;" class="ex-g">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                ['fa-bolt','Instant & Guaranteed Payouts via M-Pesa','No delays or follow-ups. Payments are processed immediately upon completing your clinic session directly to your M-Pesa account.'],
                ['fa-arrow-trend-up','Build Personal Authority & Platform Reputation','Hosting featured clinics puts your professional profile directly in front of hundreds of parents and learners on Mwalimu, positioning you as a top-tier tutor for 1-on-1 bookings.'],
                ['fa-headset','Zero Administrative Overhead','You bring the expertise; Mwalimu handles student acquisition, M-Pesa ticketing, automated reminders, and classroom setup.'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$icon,$title,$desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card" style="padding:22px;display:flex;gap:16px;align-items:flex-start;">
                <div style="width:46px;height:46px;background:rgba(109,40,217,.1);border-radius:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-solid <?php echo e($icon); ?>" style="color:var(--violet);font-size:18px;"></i>
                </div>
                <div>
                    <h3 style="font-size:16px;color:var(--ink);margin-bottom:6px;"><?php echo e($title); ?></h3>
                    <p style="font-size:13px;color:var(--muted);line-height:1.7;"><?php echo e($desc); ?></p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div style="text-align:center;margin-top:28px;">
            <a href="<?php echo e(route('register')); ?>?role=tutor" class="btn btn-v"><i class="fa-solid fa-chalkboard-user"></i> Register as an Examiner</a>
        </div>
        <style>.ex-g{grid-template-columns:1fr!important;}@media(min-width:768px){.ex-g{grid-template-columns:repeat(3,1fr)!important;}}</style>
    </div>
</section>


<footer style="background:var(--ink);padding:36px 0;">
    <div class="w" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <a href="<?php echo e(route('home')); ?>" style="display:flex;align-items:center;gap:8px;font-family:'Fraunces',serif;font-size:20px;font-weight:800;color:#fff;">
            <svg width="28" height="28" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
            Mwalimu
        </a>
        <div style="display:flex;gap:20px;flex-wrap:wrap;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [['home','Home'],['tutors.index','Find Tutors'],['how-it-works','How It Works']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$r,$l]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route($r)); ?>" style="font-size:13px;color:rgba(255,255,255,.5);" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.5)'"><?php echo e($l); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <span style="font-size:12px;color:rgba(255,255,255,.3);">&copy; <?php echo e(date('Y')); ?> Mwalimu by OvalDesk Ventures</span>
    </div>
</footer>
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/pages/examiner-clinic.blade.php ENDPATH**/ ?>