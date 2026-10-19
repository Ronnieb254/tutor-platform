<!DOCTYPE html>
<html lang="en">
<head>
<?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<title><?php echo e($tutor->user->name); ?> – Verified Tutor | Mwalimu</title>
<meta name="description" content="<?php echo e(Str::limit($tutor->bio ?? 'Book a session with '.$tutor->user->name.' on Mwalimu.', 160)); ?>">
<style>
/* ── Page-level styles ── */
.profile-hero{background:linear-gradient(135deg,#111C16 0%,#15281F 60%,#1a2e22 100%);padding:48px 0 0;position:relative;overflow:hidden;}
.profile-hero::before{content:'';position:absolute;top:-120px;right:-80px;width:400px;height:400px;background:rgba(109,40,217,.12);border-radius:50%;pointer-events:none;}
.profile-hero::after{content:'';position:absolute;bottom:-60px;left:20%;width:200px;height:200px;background:rgba(212,160,23,.06);border-radius:50%;pointer-events:none;}

/* Tabs */
.ptab{padding:14px 20px;font-size:14px;font-weight:600;background:none;border:none;cursor:pointer;color:rgba(255,255,255,.45);border-bottom:3px solid transparent;transition:all .18s;font-family:inherit;white-space:nowrap;}
.ptab.on,.ptab:hover{color:#fff;border-bottom-color:#6D28D9;}

/* Session tier cards */
.tier-card{border:2px solid rgba(21,40,31,.12);border-radius:16px;padding:16px;text-align:left;cursor:pointer;transition:all .2s;background:#fff;display:block;width:100%;}
.tier-card:hover,.tier-card.selected{border-color:#6D28D9;background:rgba(109,40,217,.04);}
.tier-card.selected{background:rgba(109,40,217,.06);}

/* Review rating bars */
.rbar-track{height:7px;background:#F4F6F4;border-radius:999px;overflow:hidden;flex:1;}
.rbar-fill{height:100%;background:#D4A017;border-radius:999px;transition:width .5s ease;}

/* Booking steps */
.bstep{display:flex;flex-direction:column;gap:4px;align-items:center;}
.bstep-dot{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;transition:all .2s;}

/* Payment form */
.pay-field{width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);background:#fff;font-size:14px;font-family:inherit;color:#15281F;outline:none;transition:all .15s;}
.pay-field:focus{border-color:#6D28D9;box-shadow:0 0 0 3px rgba(109,40,217,.1);}
</style>
</head>
<body>
<?php
$prices    = [15=>300,30=>500,45=>680,60=>800];
$sessNames = [15=>'Quick Help',30=>'Concept Check',45=>'Guided Practice',60=>'Deep Dive'];
$sessDesc  = [15=>'Rapid revision or a quick question',30=>'Targeted practice or concept clarity',45=>'Full structured lesson with Q&A',60=>'Maximum learning, best value'];
// Free first 15-min session check
$isFreeFirstSession = auth()->check()
    ? \App\Models\TutorProfile::isFreeFirstSession(auth()->id(), $tutor->user_id)
    : true; // Show free badge to guests too to encourage signup
$colors    = ['#6D28D9','#10B981','#E8522A','#D4A017','#3B82F6'];
$avColor   = $colors[$tutor->id % count($colors)];
$reviews   = $tutor->reviews()->where('is_visible',true)->with('student')->latest()->paginate(8);
?>

<?php echo $__env->make('partials.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<div class="profile-hero">
    <div style="max-width:1360px;margin:0 auto;padding:0 24px @media(min-width:1024px){padding:0 48px}">

        
        <div style="margin-bottom:20px;">
            <a href="<?php echo e(route('tutors.index')); ?>" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:rgba(255,255,255,.5);text-decoration:none;transition:color .15s;" onmouseover="this.style.color='rgba(255,255,255,.85)'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
                <i class="fa-solid fa-arrow-left fa-xs"></i> All Tutors
            </a>
        </div>

        
        <div style="display:flex;align-items:flex-start;gap:22px;flex-wrap:wrap;">
            
            <div style="width:96px;height:96px;border-radius:22px;background:<?php echo e($avColor); ?>;display:flex;align-items:center;justify-content:center;font-family:'Fraunces',serif;font-size:38px;font-weight:700;color:#fff;flex-shrink:0;border:3px solid rgba(255,255,255,.15);">
                <?php echo e(strtoupper(substr($tutor->user->name,0,1))); ?>

            </div>

            
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:8px;">
                    <h1 style="font-family:'Fraunces',serif;font-size:clamp(24px,4vw,38px);font-weight:800;color:#fff;line-height:1.1;"><?php echo e($tutor->user->name); ?></h1>
                    <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(16,185,129,.2);color:#4ADE80;border:1px solid rgba(16,185,129,.3);padding:4px 12px;border-radius:999px;font-size:12px;font-weight:700;">
                        <i class="fa-solid fa-certificate fa-xs"></i> TSC Verified
                    </span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tutor->is_tsc_registered ?? false): ?>
                    <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(212,160,23,.2);color:#FCD34D;border:1px solid rgba(212,160,23,.25);padding:4px 12px;border-radius:999px;font-size:12px;font-weight:700;">
                        <i class="fa-solid fa-star fa-xs"></i> National Examiner
                    </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tutor->subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.85);padding:4px 13px;border-radius:999px;font-size:12px;font-weight:500;border:1px solid rgba(255,255,255,.12);"><?php echo e($s->name); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div style="display:flex;flex-wrap:wrap;gap:20px;font-size:14px;color:rgba(255,255,255,.65);">
                    <span style="display:flex;align-items:center;gap:5px;">
                        <i class="fa-solid fa-star" style="color:#D4A017;font-size:13px;"></i>
                        <strong style="color:#fff;"><?php echo e(number_format($tutor->rating_avg,1)); ?></strong>
                        <span>(<?php echo e($tutor->total_reviews); ?> reviews)</span>
                    </span>
                    <span style="display:flex;align-items:center;gap:5px;">
                        <i class="fa-solid fa-chalkboard fa-xs" style="opacity:.6;"></i>
                        <strong style="color:#fff;"><?php echo e(number_format($tutor->total_sessions)); ?></strong> sessions
                    </span>
                    <span style="display:flex;align-items:center;gap:5px;">
                        <i class="fa-solid fa-clock fa-xs" style="opacity:.6;"></i>
                        <strong style="color:#fff;"><?php echo e($tutor->experience_years); ?></strong> years exp.
                    </span>
                    <!-- <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tutor->city): ?>
                    <span style="display:flex;align-items:center;gap:5px;">
                        <i class="fa-solid fa-location-dot fa-xs" style="opacity:.6;"></i>
                        <?php echo e($tutor->city); ?>, Kenya
                    </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> -->
                    <span style="display:flex;align-items:center;gap:5px;">
                        <i class="fa-solid fa-reply fa-xs" style="opacity:.6;"></i>
                        Responds within 2hr
                    </span>
                </div>
            </div>

            
            <div style="display:none;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0;" class="desk-price">
                <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:14px;padding:12px 18px;text-align:center;">
                    <div style="font-size:11px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px;">From</div>
                    <div style="font-family:'Fraunces',serif;font-size:28px;font-weight:800;color:#D4A017;">KSh 300</div>
                    <div style="font-size:11px;color:rgba(255,255,255,.4);">per session</div>
                </div>
            </div>
        </div>

        
        <div style="display:flex;gap:0;margin-top:28px;overflow-x:auto;" x-data="">
            <button class="ptab on" onclick="showTab('about',this)">
                <i class="fa-solid fa-user fa-xs" style="margin-right:5px;"></i>About
            </button>
            <button class="ptab" onclick="showTab('availability',this)">
                <i class="fa-solid fa-calendar-week fa-xs" style="margin-right:5px;"></i>Availability
            </button>
            <button class="ptab" onclick="showTab('reviews',this)">
                <i class="fa-solid fa-star fa-xs" style="margin-right:5px;"></i>Reviews (<?php echo e($tutor->total_reviews); ?>)
            </button>
            <button class="ptab" onclick="showTab('book',this)" id="book-tab-btn" style="color:#D4A017;border-bottom-color:#D4A017;">
                <i class="fa-solid fa-calendar-check fa-xs" style="margin-right:5px;"></i>Book Session
            </button>
        </div>
    </div>
</div>


<div style="max-width:1360px;margin:0 auto;padding:32px 24px 64px;" class="w">
    <div style="display:grid;grid-template-columns:1fr;gap:24px;" class="show-grid">

        
        <div>

            
            <div id="tab-about">
                
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:20px;" class="stat-mini-g">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                        ['fa-chalkboard','#6D28D9',number_format($tutor->total_sessions),'Sessions Completed'],
                        ['fa-star','#D4A017',$tutor->total_reviews,'Total Reviews'],
                        ['fa-clock','#10B981',$tutor->experience_years.'yr','Experience'],
                        ['fa-reply','#E8522A','<2hr','Response Time'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$ic,$col,$val,$lbl]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.08);padding:18px;display:flex;align-items:center;gap:14px;">
                        <div style="width:42px;height:42px;border-radius:12px;background:<?php echo e($col); ?>18;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid <?php echo e($ic); ?>" style="color:<?php echo e($col); ?>;font-size:16px;"></i>
                        </div>
                        <div>
                            <div style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:#15281F;"><?php echo $val; ?></div>
                            <div style="font-size:12px;color:rgba(21,40,31,.55);"><?php echo e($lbl); ?></div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <style>.stat-mini-g{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:640px){.stat-mini-g{grid-template-columns:repeat(4,1fr)!important;}}</style>

                
                <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:28px;margin-bottom:20px;">
                    <h2 style="font-family:'Fraunces',serif;font-size:20px;color:#15281F;margin-bottom:14px;display:flex;align-items:center;gap:8px;">
                        <i class="fa-solid fa-user" style="color:#6D28D9;font-size:16px;"></i>
                        About <?php echo e(explode(' ',$tutor->user->name)[0]); ?>

                    </h2>
                    <p style="font-size:15px;color:#15281F;line-height:1.9;"><?php echo e($tutor->bio ?? 'This tutor hasn\'t added a bio yet.'); ?></p>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tutor->languages): ?>
                    <div style="margin-top:20px;padding-top:18px;border-top:1px solid rgba(21,40,31,.08);">
                        <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(21,40,31,.45);margin-bottom:10px;">Languages</p>
                        <div style="display:flex;flex-wrap:wrap;gap:6px;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tutor->languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span style="background:#F4F6F4;color:#15281F;padding:5px 14px;border-radius:999px;font-size:13px;font-weight:500;"><?php echo e($lang); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:28px;">
                    <h3 style="font-family:'Fraunces',serif;font-size:18px;color:#15281F;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                        <i class="fa-solid fa-book-open" style="color:#6D28D9;font-size:15px;"></i> Subjects Taught
                    </h3>
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;" class="subj-g">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tutor->subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#F9FAF9;border-radius:12px;border:1px solid rgba(21,40,31,.07);">
                            <span style="width:8px;height:8px;border-radius:50%;background:#6D28D9;flex-shrink:0;"></span>
                            <span style="font-size:14px;font-weight:500;color:#15281F;"><?php echo e($s->name); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s->category): ?><span style="font-size:11px;color:rgba(21,40,31,.4);margin-left:auto;"><?php echo e($s->category->name); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <style>.subj-g{grid-template-columns:1fr!important;}@media(min-width:640px){.subj-g{grid-template-columns:repeat(2,1fr)!important;}}</style>
                </div>
            </div>

            
            <div id="tab-availability" style="display:none;">
                <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:28px;">
                    <h2 style="font-family:'Fraunces',serif;font-size:20px;color:#15281F;margin-bottom:6px;display:flex;align-items:center;gap:8px;">
                        <i class="fa-solid fa-calendar-week" style="color:#6D28D9;font-size:16px;"></i> Weekly Schedule
                    </h2>
                    <p style="font-size:13px;color:rgba(21,40,31,.5);margin-bottom:22px;">East Africa Time (EAT, UTC+3)</p>
                    <?php
                    $avails = $tutor->availabilities()->where('is_active',true)->orderBy('day_of_week')->get();
                    $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($avails->isEmpty()): ?>
                    <div style="text-align:center;padding:40px;color:rgba(21,40,31,.45);">
                        <i class="fa-regular fa-calendar-xmark" style="font-size:36px;margin-bottom:12px;display:block;color:rgba(21,40,31,.15);"></i>
                        <p style="font-size:14px;">Availability not set. Book anyway — the tutor will confirm a time that works.</p>
                    </div>
                    <?php else: ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $av = $avails->firstWhere('day_of_week',$i); ?>
                    <div style="display:flex;align-items:center;gap:16px;padding:14px 16px;border-radius:13px;margin-bottom:7px;background:<?php echo e($av ? 'rgba(109,40,217,.04)' : '#FAFCFA'); ?>;border:1px solid <?php echo e($av ? 'rgba(109,40,217,.15)' : 'rgba(21,40,31,.07)'); ?>;">
                        <span style="font-weight:600;font-size:14px;color:#15281F;width:96px;flex-shrink:0;"><?php echo e($day); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($av): ?>
                        <span style="font-size:14px;font-weight:500;color:#6D28D9;display:flex;align-items:center;gap:6px;">
                            <i class="fa-regular fa-clock fa-xs"></i>
                            <?php echo e(substr($av->start_time,0,5)); ?> – <?php echo e(substr($av->end_time,0,5)); ?>

                        </span>
                        <span style="margin-left:auto;background:rgba(16,185,129,.1);color:#10B981;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;display:flex;align-items:center;gap:4px;">
                            <i class="fa-solid fa-check fa-xs"></i> Available
                        </span>
                        <?php else: ?>
                        <span style="font-size:13px;color:rgba(21,40,31,.4);">Not available</span>
                        <span style="margin-left:auto;background:#F3F4F6;color:#9CA3AF;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;">Off</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div id="tab-reviews" style="display:none;">
                
                <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:28px;margin-bottom:16px;">
                    <div style="display:flex;align-items:center;gap:32px;flex-wrap:wrap;">
                        <div style="text-align:center;flex-shrink:0;">
                            <div style="font-family:'Fraunces',serif;font-size:64px;font-weight:800;color:#15281F;line-height:1;"><?php echo e(number_format($tutor->rating_avg,1)); ?></div>
                            <div style="display:flex;gap:3px;justify-content:center;margin:8px 0;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i=1;$i<=5;$i++): ?>
                                <i class="fa-solid fa-star" style="color:<?php echo e($i<=$tutor->rating_avg ? '#D4A017' : '#E5E7EB'); ?>;font-size:18px;"></i>
                                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div style="font-size:13px;color:rgba(21,40,31,.5);"><?php echo e($tutor->total_reviews); ?> reviews</div>
                        </div>
                        <div style="flex:1;min-width:200px;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [5,4,3,2,1]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $star): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $cnt=$tutor->reviews()->where('rating',$star)->count(); $pct=$tutor->total_reviews>0?round($cnt/$tutor->total_reviews*100):0; ?>
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                                <span style="font-size:13px;color:rgba(21,40,31,.5);width:12px;text-align:right;"><?php echo e($star); ?></span>
                                <i class="fa-solid fa-star" style="color:#D4A017;font-size:11px;"></i>
                                <div class="rbar-track"><div class="rbar-fill" style="width:<?php echo e($pct); ?>%;"></div></div>
                                <span style="font-size:12px;color:rgba(21,40,31,.4);width:20px;"><?php echo e($cnt); ?></span>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div style="background:#fff;border-radius:18px;border:1px solid rgba(21,40,31,.08);padding:22px;margin-bottom:12px;">
                    <div style="display:flex;align-items:flex-start;gap:14px;">
                        <div style="width:40px;height:40px;border-radius:12px;background:#<?php echo e(substr(md5($review->student->email),0,6)); ?>;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#fff;flex-shrink:0;"><?php echo e(strtoupper(substr($review->student->name,0,1))); ?></div>
                        <div style="flex:1;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:6px;">
                                <div>
                                    <span style="font-weight:700;font-size:14px;color:#15281F;"><?php echo e($review->student->name); ?></span>
                                    <span style="font-size:12px;color:rgba(21,40,31,.4);margin-left:8px;"><?php echo e($review->created_at->format('M Y')); ?></span>
                                </div>
                                <div style="display:flex;gap:2px;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i=1;$i<=5;$i++): ?><i class="fa-solid fa-star" style="color:<?php echo e($i<=$review->rating ? '#D4A017' : '#E5E7EB'); ?>;font-size:13px;"></i><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($review->comment): ?>
                            <p style="font-size:14px;color:#15281F;line-height:1.8;"><?php echo e($review->comment); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:48px;text-align:center;">
                    <i class="fa-regular fa-star" style="font-size:36px;color:rgba(21,40,31,.12);display:block;margin-bottom:12px;"></i>
                    <p style="color:rgba(21,40,31,.5);font-size:14px;">No reviews yet. Book a session and be the first!</p>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reviews->hasPages()): ?>
                <div style="margin-top:16px;"><?php echo e($reviews->links()); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div id="tab-book" style="display:none;" x-data="{
                step: 1,
                dur: 60,
                date: '',
                time: '',
                subject: '',
                notes: '',
                processing: false,
                confirmed: false,
                studentCode: '',
                bookingRef: '',
                get price() {
                    const base = {15:300,30:500,45:680,60:800}[this.dur] || 800;
                    // Free first 15-min session
                    if (this.dur === 15 && <?php echo e($isFreeFirstSession ? 'true' : 'false'); ?>) return 0;
                    return base;
                },
                get isFree() { return this.price === 0; },
                get name()  { return {15:'Quick Help',30:'Concept Check',45:'Guided Practice',60:'Deep Dive'}[this.dur] || 'Deep Dive'; }
            }">

                
                <!-- <div style="display:flex;align-items:center;justify-content:center;gap:4;margin-bottom:28px;"> -->
                    <div style="display:flex;align-items:center;justify-content:center;gap:16px;margin-bottom:28px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Choose Session','Review & Pay','Confirmed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display:flex;align-items:center;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i > 0): ?><div :style="step > <?php echo e($i); ?> ? 'background:#6D28D9' : 'background:rgba(21,40,31,.1)'" style="height:2px;width:48px;transition:background .3s;"></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div style="display:flex;flex-direction:column;align-items:center;gap:5px;">
                            <div :style="step > <?php echo e($i+1); ?> ? 'background:#10B981;color:#fff' : step === <?php echo e($i+1); ?> ? 'background:#6D28D9;color:#fff' : 'background:rgba(21,40,31,.08);color:rgba(21,40,31,.4)'"
                                 style="width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;transition:all .3s;">
                                <template x-if="step > <?php echo e($i+1); ?>"><i class="fa-solid fa-check fa-xs"></i></template>
                                <template x-if="step <= <?php echo e($i+1); ?>"><span><?php echo e($i+1); ?></span></template>
                            </div>
                            <span style="font-size:11px;font-weight:600;" :style="step === <?php echo e($i+1); ?> ? 'color:#6D28D9' : 'color:rgba(21,40,31,.4)'"><?php echo e($label); ?></span>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div x-show="step === 1">
                    <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:28px;margin-bottom:16px;">
                        <h3 style="font-family:'Fraunces',serif;font-size:19px;color:#15281F;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
                            <i class="fa-solid fa-clock" style="color:#6D28D9;font-size:16px;"></i> 1. Choose Session Type
                        </h3>
                        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [15=>['⚡','Quick Help',300,'Rapid revision or a quick question'],30=>['📘','Concept Check',500,'Targeted practice or concept clarity'],45=>['🎯','Guided Practice',680,'Full structured lesson with Q&A'],60=>['🏆','Deep Dive',800,'Maximum learning, best value']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mins => [$icon,$tname,$tprice,$tdesc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $actualPrice = ($mins===15 && $isFreeFirstSession) ? 0 : $tprice; ?>
                            <button @click="dur=<?php echo e($mins); ?>"
                                    :style="dur===<?php echo e($mins); ?> ? 'border-color:#6D28D9;background:rgba(109,40,217,.05);' : ''"
                                    style="border:2px solid rgba(21,40,31,.12);border-radius:16px;padding:16px;text-align:left;cursor:pointer;font-family:inherit;background:#fff;transition:all .18s;position:relative;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mins===15 && $isFreeFirstSession): ?>
                                <span style="position:absolute;top:-10px;right:10px;background:#10B981;color:#fff;font-size:10px;font-weight:800;padding:3px 10px;border-radius:999px;letter-spacing:.05em;">FREE FIRST SESSION</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <span style="font-size:20px;"><?php echo e($icon); ?></span>
                                        <div>
                                            <div style="font-size:14px;font-weight:700;color:#15281F;"><?php echo e($tname); ?></div>
                                            <div style="font-size:11px;color:rgba(21,40,31,.5);"><?php echo e($mins); ?> min</div>
                                        </div>
                                    </div>
                                    <div :style="dur===<?php echo e($mins); ?> ? 'color:#6D28D9;' : 'color:#15281F;'" style="font-family:'Fraunces',serif;font-weight:800;text-align:right;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($actualPrice === 0): ?>
                                        <span style="font-size:20px;color:#10B981;">FREE</span>
                                        <div style="font-size:11px;text-decoration:line-through;color:rgba(21,40,31,.35);">KSh <?php echo e($tprice); ?></div>
                                        <?php else: ?>
                                        <span style="font-size:18px;">KSh <?php echo e($actualPrice); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div style="font-size:12px;color:rgba(21,40,31,.5);"><?php echo e($tdesc); ?></div>
                            </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:28px;margin-bottom:16px;">
                        <h3 style="font-family:'Fraunces',serif;font-size:19px;color:#15281F;margin-bottom:18px;display:flex;align-items:center;gap:8px;">
                            <i class="fa-solid fa-calendar" style="color:#6D28D9;font-size:16px;"></i> 2. Pick Date & Time
                        </h3>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;" class="dt-g">
                            <div>
                                <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:block;margin-bottom:6px;">Date</label>
                                <input type="date" x-model="date" :min="new Date(Date.now()+86400000).toISOString().split('T')[0]"
                                       style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;background:#fff;"
                                       onfocus="this.style.borderColor='#6D28D9'" onblur="this.style.borderColor='rgba(21,40,31,.18)'">
                            </div>
                            <div>
                                <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:block;margin-bottom:6px;">Time (EAT)</label>
                                <input type="time" x-model="time"
                                       style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;background:#fff;"
                                       onfocus="this.style.borderColor='#6D28D9'" onblur="this.style.borderColor='rgba(21,40,31,.18)'">
                            </div>
                        </div>
                        <style>.dt-g{grid-template-columns:1fr 1fr!important;}@media(max-width:480px){.dt-g{grid-template-columns:1fr!important;}}</style>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tutor->subjects->isNotEmpty()): ?>
                        <div style="margin-bottom:14px;">
                            <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:block;margin-bottom:6px;">Subject (optional)</label>
                            <select x-model="subject"
                                    style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;background:#fff;appearance:none;"
                                    onfocus="this.style.borderColor='#6D28D9'" onblur="this.style.borderColor='rgba(21,40,31,.18)'">
                                <option value="">Select a subject…</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tutor->subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div>
                            <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:block;margin-bottom:6px;">Notes for tutor (optional)</label>
                            <textarea x-model="notes" rows="2" placeholder="Topics to cover, past exam areas, specific questions…"
                                      style="width:100%;padding:12px 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;resize:none;line-height:1.6;background:#fff;"
                                      onfocus="this.style.borderColor='#6D28D9'" onblur="this.style.borderColor='rgba(21,40,31,.18)'"></textarea>
                        </div>
                    </div>

                    <button @click="step=2" :disabled="!date || !time"
                            style="width:100%;height:52px;border-radius:999px;background:#6D28D9;color:#fff;font-size:15px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;"
                            :style="(!date||!time)?'opacity:.5;cursor:not-allowed;':''"
                            onmouseover="if(this.date&&this.time)this.style.background='#4C1D95'" onmouseout="this.style.background='#6D28D9'">
                        Continue to Review
                        <i class="fa-solid fa-arrow-right fa-sm"></i>
                    </button>
                    <p style="text-align:center;font-size:12px;color:rgba(21,40,31,.4);margin-top:10px;">
                        <i class="fa-solid fa-rotate-left fa-xs" style="color:#10B981;"></i> 100% money-back on your first lesson
                    </p>
                </div>

                
                <div x-show="step === 2" x-cloak>
                    <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:28px;margin-bottom:16px;">
                        <h3 style="font-family:'Fraunces',serif;font-size:19px;color:#15281F;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
                            <i class="fa-solid fa-receipt" style="color:#6D28D9;font-size:16px;"></i> Session Summary
                        </h3>

                        
                        <div style="background:#F9FAF9;border-radius:14px;padding:18px;margin-bottom:18px;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                                ['fa-user','Tutor',$tutor->user->name],
                                ['fa-clock','Session','<span x-text="dur+\'min — \'+name"></span>'],
                                ['fa-calendar','Date','<span x-text="date ? new Date(date).toDateString() : \'—\'"></span>'],
                                ['fa-clock','Time','<span x-text="time || \'—\'"></span> EAT'],
                            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$ic,$label,$val]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid rgba(21,40,31,.07);">
                                <span style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(21,40,31,.55);">
                                    <i class="fa-solid <?php echo e($ic); ?> fa-xs" style="width:12px;text-align:center;"></i><?php echo e($label); ?>

                                </span>
                                <span style="font-size:13px;font-weight:600;color:#15281F;"><?php echo $val; ?></span>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 0 0;">
                                <span style="font-size:15px;font-weight:700;color:#15281F;">Total</span>
                                <template x-if="isFree">
                                    <div style="text-align:right;">
                                        <span style="font-family:'Fraunces',serif;font-size:26px;font-weight:800;color:#10B981;">FREE</span>
                                        <div style="font-size:12px;text-decoration:line-through;color:rgba(21,40,31,.35);">KSh 300</div>
                                    </div>
                                </template>
                                <template x-if="!isFree">
                                    <span style="font-family:'Fraunces',serif;font-size:26px;font-weight:800;color:#6D28D9;">KSh <span x-text="price"></span></span>
                                </template>
                            </div>
                        </div>

                        
                        <div style="background:rgba(109,40,217,.05);border:1px solid rgba(109,40,217,.15);border-radius:14px;padding:14px 16px;margin-bottom:18px;">
                            <p style="font-size:13px;font-weight:700;color:#6D28D9;margin-bottom:4px;display:flex;align-items:center;gap:6px;">
                                <i class="fa-solid fa-key fa-xs"></i> After payment
                            </p>
                            <p style="font-size:12px;color:rgba(21,40,31,.6);line-height:1.65;">Both you and <?php echo e(explode(' ',$tutor->user->name)[0]); ?> will receive a unique session access code by email. You'll use your <strong style="color:#15281F;">STU-XXXXXX</strong> code to join the live class.</p>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                        <div style="background:#F9FAF9;border-radius:14px;padding:18px;">
                            <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.45);margin-bottom:12px;">
                                <i class="fa-solid fa-credit-card fa-xs" style="color:#6D28D9;"></i> Pay Securely via Paystack
                            </p>
                            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['M-Pesa','Visa','Mastercard']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span style="background:#fff;border:1px solid rgba(21,40,31,.12);border-radius:8px;padding:5px 12px;font-size:12px;font-weight:600;color:#15281F;display:flex;align-items:center;gap:5px;">
                                    <i class="fa-solid <?php echo e($pm==='M-Pesa'?'fa-mobile-screen-button':($pm==='Visa'?'fa-credit-card':'fa-credit-card')); ?>" style="color:#6D28D9;font-size:11px;"></i>
                                    <?php echo e($pm); ?>

                                </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <button @click="processPayment($data)"
                                    :disabled="processing"
                                    :style="isFree ? 'background:#10B981;' : 'background:#6D28D9;'"
                                    style="width:100%;height:52px;border-radius:999px;color:#fff;font-size:15px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;">
                                <template x-if="!processing">
                                    <span style="display:flex;align-items:center;gap:8px;">
                                        <i :class="isFree ? 'fa-solid fa-gift fa-xs' : 'fa-solid fa-lock fa-xs'"></i>
                                        <span x-text="isFree ? 'Book Free Session' : 'Pay KSh ' + price"></span>
                                    </span>
                                </template>
                                <template x-if="processing">
                                    <span style="display:flex;align-items:center;gap:8px;">
                                        <i class="fa-solid fa-spinner fa-spin fa-xs"></i> Processing…
                                    </span>
                                </template>
                            </button>
                        </div>
                        <?php else: ?>
                        <div style="text-align:center;padding:20px;">
                            <p style="font-size:14px;color:rgba(21,40,31,.6);margin-bottom:14px;">Sign in to complete your booking</p>
                            <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
                                <a href="<?php echo e(route('login')); ?>" style="display:inline-flex;align-items:center;gap:7px;padding:12px 24px;border-radius:999px;background:#6D28D9;color:#fff;font-size:14px;font-weight:600;text-decoration:none;">
                                    <i class="fa-solid fa-right-to-bracket fa-xs"></i> Sign In
                                </a>
                                <a href="<?php echo e(route('register')); ?>" style="display:inline-flex;align-items:center;gap:7px;padding:12px 24px;border-radius:999px;border:2px solid rgba(21,40,31,.2);color:#15281F;font-size:14px;font-weight:600;text-decoration:none;">
                                    <i class="fa-solid fa-user-plus fa-xs"></i> Create Account
                                </a>
                            </div>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div style="display:flex;justify-content:flex-start;margin-top:12px;">
                            <button @click="step=1" style="display:flex;align-items:center;gap:6px;background:none;border:none;color:rgba(21,40,31,.5);font-size:13px;cursor:pointer;font-family:inherit;padding:0;transition:color .15s;" onmouseover="this.style.color='#15281F'" onmouseout="this.style.color='rgba(21,40,31,.5)'">
                                <i class="fa-solid fa-arrow-left fa-xs"></i> Back
                            </button>
                        </div>
                    </div>
                </div>

                
                <div x-show="step === 3" x-cloak>
                    <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:40px;text-align:center;">
                        <div style="width:68px;height:68px;border-radius:50%;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
                            <i class="fa-solid fa-circle-check" style="font-size:30px;color:#10B981;"></i>
                        </div>
                        <h2 style="font-family:'Fraunces',serif;font-size:26px;color:#15281F;margin-bottom:8px;">Booking Confirmed!</h2>
                        <p style="font-size:14px;color:rgba(21,40,31,.6);margin-bottom:28px;">Payment received. Your session codes have been emailed to you. Redirecting to your session in 3 seconds…</p>

                        
                        <div style="background:rgba(109,40,217,.06);border:2px solid rgba(109,40,217,.25);border-radius:20px;padding:24px;margin-bottom:24px;">
                            <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(21,40,31,.45);margin-bottom:10px;display:flex;align-items:center;justify-content:center;gap:6px;">
                                <i class="fa-solid fa-key" style="color:#6D28D9;"></i> Your Session Access Code
                            </p>
                            <p style="font-family:monospace;font-size:36px;font-weight:800;color:#6D28D9;letter-spacing:.12em;line-height:1;" x-text="studentCode">STU-XXXXXX</p>
                            <p style="font-size:12px;color:rgba(21,40,31,.5);margin-top:10px;">Enter this code on session day to join your private live class</p>
                        </div>

                        
                        <div style="background:#F9FAF9;border-radius:14px;padding:16px;text-align:left;margin-bottom:24px;">
                            <p style="font-size:12px;font-weight:700;color:#15281F;margin-bottom:10px;"><i class="fa-solid fa-list-check" style="color:#6D28D9;margin-right:5px;"></i> What happens next</p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Your tutor has been notified and received their own TUT-XXXXXX code','A confirmation email with all details has been sent to you','On session day, go to your dashboard → click Join Session → enter your code','Your private Jitsi classroom opens instantly — no download required']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div style="display:flex;align-items:flex-start;gap:8px;font-size:13px;color:rgba(21,40,31,.7);margin-bottom:7px;">
                                <i class="fa-solid fa-circle-check" style="color:#10B981;margin-top:2px;flex-shrink:0;font-size:11px;"></i><?php echo e($step); ?>

                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                            <a :href="bookingRef ? '/bookings/' + bookingRef : '<?php echo e(route('student.dashboard')); ?>'"
                               style="display:inline-flex;align-items:center;gap:7px;padding:13px 28px;border-radius:999px;background:#10B981;color:#fff;font-size:15px;font-weight:700;text-decoration:none;">
                                <i class="fa-solid fa-video fa-xs"></i> Go to My Session
                            </a>
                            <a href="<?php echo e(route('student.dashboard')); ?>" style="display:inline-flex;align-items:center;gap:7px;padding:13px 24px;border-radius:999px;border:1.5px solid rgba(21,40,31,.18);color:#15281F;font-size:14px;font-weight:600;text-decoration:none;">
                                <i class="fa-solid fa-gauge-high fa-xs"></i> Dashboard
                            </a>
                            <a href="<?php echo e(route('tutors.index')); ?>" style="display:inline-flex;align-items:center;gap:7px;padding:13px 24px;border-radius:999px;border:1.5px solid rgba(21,40,31,.18);color:#15281F;font-size:14px;font-weight:600;text-decoration:none;">
                                <i class="fa-solid fa-magnifying-glass fa-xs"></i> Browse More Tutors
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div style="height:fit-content;position:sticky;top:88px;" class="book-sidebar">
            <div style="background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.1);padding:26px;box-shadow:0 4px 28px rgba(21,40,31,.08);">
                <div style="text-align:center;margin-bottom:20px;padding-bottom:18px;border-bottom:1px solid rgba(21,40,31,.08);">
                    <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.45);margin-bottom:8px;">Session Rates</div>
                    <div style="font-family:'Fraunces',serif;font-size:36px;font-weight:800;color:#6D28D9;">KSh 300</div>
                    <div style="font-size:13px;color:rgba(21,40,31,.5);">starting from per session</div>
                </div>

                
                <!-- <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:20px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [15=>['⚡','Quick Help',300],30=>['📘','Concept Check',500],45=>['🎯','Guided Practice',680],60=>['🏆','Deep Dive',800]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mins=>[$ic,$n,$p]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $sidePrice = ($mins===15 && $isFreeFirstSession) ? 0 : $p; ?>
                    <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:12px;background:<?php echo e($mins===15&&$isFreeFirstSession?'rgba(16,185,129,.06)':'#F9FAF9'); ?>;border:1px solid <?php echo e($mins===15&&$isFreeFirstSession?'rgba(16,185,129,.2)':'rgba(21,40,31,.07)'); ?>;position:relative;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mins===15 && $isFreeFirstSession): ?>
                        <span style="position:absolute;top:-8px;right:8px;background:#10B981;color:#fff;font-size:9px;font-weight:800;padding:2px 8px;border-radius:999px;letter-spacing:.04em;">FREE</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <span style="font-size:16px;"><?php echo e($ic); ?></span>
                        <div style="flex:1;">
                            <div style="font-size:13px;font-weight:600;color:#15281F;"><?php echo e($n); ?></div>
                            <div style="font-size:11px;color:rgba(21,40,31,.45);"><?php echo e($mins); ?> min</div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sidePrice === 0): ?>
                        <div style="text-align:right;">
                            <div style="font-family:'Fraunces',serif;font-size:16px;font-weight:800;color:#10B981;">FREE</div>
                            <div style="font-size:10px;text-decoration:line-through;color:rgba(21,40,31,.3);">KSh <?php echo e($p); ?></div>
                        </div>
                        <?php else: ?>
                        <div style="font-family:'Fraunces',serif;font-size:16px;font-weight:800;color:#6D28D9;">KSh <?php echo e($p); ?></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div> -->

                <button onclick="document.getElementById('book-tab-btn').click();document.getElementById('tab-book').scrollIntoView({behavior:'smooth'})"
                        style="width:100%;height:52px;border-radius:999px;background:#6D28D9;color:#fff;font-size:15px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:12px;"
                        onmouseover="this.style.background='#4C1D95';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#6D28D9';this.style.transform='translateY(0)'">
                    <i class="fa-solid fa-calendar-check fa-sm"></i> Book a Session
                </button>

                <div style="display:flex;flex-direction:column;gap:7px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['fa-certificate green TSC-Registered & Verified','fa-lock green Secure, private classroom','fa-rotate-left green 100% money-back first lesson','fa-key green Session code sent by email']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php [$ic,,$text] = explode(' ',$item,3); ?>
                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:rgba(21,40,31,.6);">
                        <i class="fa-solid fa-<?php echo e($ic); ?>" style="color:#10B981;font-size:11px;width:13px;text-align:center;"></i><?php echo e($text); ?>

                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.show-grid{grid-template-columns:1fr!important;}
@media(min-width:1024px){.show-grid{grid-template-columns:1fr 360px!important;}}
.book-sidebar{display:none!important;}
@media(min-width:1024px){.book-sidebar{display:block!important;}}
.desk-price{display:none!important;}
@media(min-width:768px){.desk-price{display:flex!important;}}
</style>

<?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
function showTab(name, btn) {
    ['about','availability','reviews','book'].forEach(t => {
        const el = document.getElementById('tab-'+t);
        if (el) el.style.display = t === name ? 'block' : 'none';
    });
    document.querySelectorAll('.ptab').forEach(b => {
        b.classList.remove('on');
        b.style.color = 'rgba(255,255,255,.45)';
        b.style.borderBottomColor = 'transparent';
    });
    if (btn) {
        btn.classList.add('on');
        btn.style.color = name === 'book' ? '#D4A017' : '#fff';
        btn.style.borderBottomColor = name === 'book' ? '#D4A017' : '#6D28D9';
    }
}

function processPayment(data) {
    if (!data || !data.date || !data.time) {
        alert('Please select a date and time.');
        return;
    }

    data.processing = true;

    fetch('/bookings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            tutor_profile_id: <?php echo e($tutor->id); ?>,
            duration_minutes: data.dur,
            scheduled_at: data.date + ' ' + data.time,
            subject_id: data.subject || null,
            notes: data.notes,
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'ok' && res.free) {
            data.processing = false;
            data.studentCode = res.student_code || '';
            data.bookingRef  = res.booking_uuid || '';
            data.step = 3;
            if (res.booking_url) {
                setTimeout(() => { window.location.href = res.booking_url; }, 3000);
            }
            return;
        }
        if (res.status === 'ok') {
            const handler = PaystackPop.setup({
                key: '<?php echo e(config('paystack.public_key')); ?>',
                email: '<?php echo e(auth()->user()?->email); ?>',
                amount: data.price * 100,
                currency: 'KES',
                ref: res.reference,
                metadata: { booking_id: res.booking_id },
                onSuccess: function(transaction) {
                    // Confirm booking after payment
                    fetch('/bookings/confirm', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ reference: transaction.reference })
                    })
                    .then(r => r.json())
                    .then(confirmRes => {
                        data.processing = false;
                        if (confirmRes.status === 'ok') {
                            data.studentCode = confirmRes.student_code || '';
                            data.bookingRef  = confirmRes.booking_uuid || '';
                            data.step = 3;
                            // Redirect to session page after 3 seconds
                            if (confirmRes.booking_url) {
                                setTimeout(() => {
                                    window.location.href = confirmRes.booking_url;
                                }, 3000);
                            }
                        }
                    })
                    .catch(() => {
                        data.processing = false;
                        data.step = 3;
                    });
                },
                onCancel: function() {
                    data.processing = false;
                }
            });
            handler.openIframe();
        } else {
            data.processing = false;
            alert(res.message || 'Could not process booking. Please try again.');
        }
    })
    .catch(err => {
        data.processing = false;
        console.error(err);
        alert('Network error. Please try again.');
    });
}
</script>
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/pages/tutors/show.blade.php ENDPATH**/ ?>