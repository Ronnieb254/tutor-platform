<!DOCTYPE html>
<html lang="en">
<head>
<?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<title>Examiner's Clinic – Mwalimu</title>
<meta name="description" content="Learn directly from Kenya's national examiners and markers. Group sessions per paper (PP1, PP2, PP3). KSh 300 per session. Max 30 students per session.">
</head>
<body>
<?php echo $__env->make('partials.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<section style="background:linear-gradient(135deg,#0D1F14,#15281F);padding:72px 0;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-80px;right:-80px;width:340px;height:340px;background:rgba(212,160,23,.08);border-radius:50%;pointer-events:none;"></div>
    <div style="position:absolute;bottom:-60px;left:-40px;width:240px;height:240px;background:rgba(109,40,217,.1);border-radius:50%;pointer-events:none;"></div>
    <div style="max-width:1360px;margin:0 auto;padding:0 24px;position:relative;z-index:1;">
        <div style="display:grid;grid-template-columns:1fr;gap:48px;align-items:center;" class="hero-g">
            <div>
                <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(212,160,23,.2);color:#FCD34D;border:1px solid rgba(212,160,23,.3);padding:6px 18px;border-radius:999px;font-size:13px;font-weight:700;margin-bottom:22px;">
                    <i class="fa-solid fa-star fa-xs"></i> The Examiner's Clinic
                </div>
                <h1 style="font-family:'Fraunces',serif;font-size:clamp(34px,5vw,58px);color:#fff;margin-bottom:16px;line-height:1.1;">
                    Learn from the experts who <span style="color:#D4A017;">set the exams.</span>
                </h1>
                <p style="font-size:17px;color:rgba(255,255,255,.65);max-width:560px;margin-bottom:28px;line-height:1.85;">
                    Most revision resources focus on <em style="font-style:italic;">what</em> to study. The Examiner's Clinic focuses on <strong style="color:#fff;">how to answer questions</strong> — delivered by national examiners and markers, structured per paper (PP1, PP2, PP3).
                </p>
                <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:32px;">
                    <a href="#clinics" style="display:inline-flex;align-items:center;gap:8px;padding:13px 32px;border-radius:999px;background:#D4A017;color:#15281F;font-size:15px;font-weight:700;text-decoration:none;transition:all .18s;" onmouseover="this.style.opacity='.9';this.style.transform='translateY(-1px)'" onmouseout="this.style.opacity='1';this.style.transform='translateY(0)'">
                        <i class="fa-solid fa-calendar-check fa-sm"></i> Book a Clinic Spot
                    </a>
                    <a href="<?php echo e(route('register')); ?>?role=tutor" style="display:inline-flex;align-items:center;gap:8px;padding:13px 28px;border-radius:999px;background:rgba(255,255,255,.1);color:#fff;border:1.5px solid rgba(255,255,255,.25);font-size:15px;font-weight:600;text-decoration:none;transition:all .18s;" onmouseover="this.style.background='rgba(255,255,255,.18)'" onmouseout="this.style.background='rgba(255,255,255,.1)'">
                        <i class="fa-solid fa-chalkboard-user fa-sm"></i> Host a Clinic
                    </a>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:20px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [['fa-sterling-sign','KSh 300','Per 60-min session'],['fa-users','Max 30','Learners per session'],['fa-calendar-week','Weekly','Every subject'],['fa-shield-check','Secure','All sessions']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$ic,$val,$lbl]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:34px;height:34px;background:rgba(212,160,23,.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid <?php echo e($ic); ?>" style="color:#D4A017;font-size:13px;"></i>
                        </div>
                        <div>
                            <div style="font-size:14px;font-weight:700;color:#fff;"><?php echo e($val); ?></div>
                            <div style="font-size:11px;color:rgba(255,255,255,.45);"><?php echo e($lbl); ?></div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:24px;padding:28px;">
                <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.4);margin-bottom:18px;">What each clinic covers</p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Exam strategy & question time management','High-frequency exam questions by topic','Examiner traps — mistakes that lose marks','Marking scheme analysis — how marks are awarded','Common student errors and how to avoid them','How KNEC examiners grade papers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:12px;">
                    <i class="fa-solid fa-circle-check" style="color:#10B981;margin-top:2px;flex-shrink:0;font-size:13px;"></i>
                    <span style="font-size:14px;color:rgba(255,255,255,.82);"><?php echo e($item); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div style="margin-top:20px;padding-top:18px;border-top:1px solid rgba(255,255,255,.1);text-align:center;">
                    <span style="font-family:'Fraunces',serif;font-size:36px;font-weight:800;color:#D4A017;">KSh 300</span>
                    <span style="font-size:14px;color:rgba(255,255,255,.45);margin-left:8px;">/ 60-minute session</span>
                </div>
            </div>
        </div>
    </div>
    <style>.hero-g{grid-template-columns:1fr!important;}@media(min-width:1024px){.hero-g{grid-template-columns:1fr 440px!important;}}</style>
</section>


<section style="padding:72px 0;background:rgba(21,40,31,.02);">
    <div style="max-width:860px;margin:0 auto;padding:0 24px;">
        <div style="text-align:center;margin-bottom:40px;">
            <h2 style="font-family:'Fraunces',serif;font-size:clamp(26px,4vw,38px);color:#15281F;margin-bottom:10px;">Why the Examiner's Clinic?</h2>
            <p style="font-size:16px;color:rgba(21,40,31,.55);">A fraction of the cost of private tuition, in a focused, high-impact group environment.</p>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;" class="vs-g">
            <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:24px;border-left:4px solid rgba(21,40,31,.12);">
                <h3 style="font-size:15px;font-weight:700;color:rgba(21,40,31,.5);margin-bottom:16px;display:flex;align-items:center;gap:7px;"><i class="fa-solid fa-book fa-sm"></i> Regular Revision</h3>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Focuses on what to study','Content-heavy, little strategy','No insight into examiner expectations','Expensive private tuition per session']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;font-size:13px;color:rgba(21,40,31,.6);">
                    <i class="fa-solid fa-xmark" style="color:#DC2626;margin-top:2px;flex-shrink:0;"></i><?php echo e($item); ?>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:24px;border-left:4px solid #D4A017;">
                <h3 style="font-size:15px;font-weight:700;color:#15281F;margin-bottom:16px;display:flex;align-items:center;gap:7px;"><i class="fa-solid fa-star fa-sm" style="color:#D4A017;"></i> Examiner's Clinic</h3>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Focuses on HOW to answer','Strategic, marks-focused teaching','Direct insight into KNEC marking criteria','KSh 300 for a 60-minute group session']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;font-size:13px;color:#15281F;">
                    <i class="fa-solid fa-circle-check" style="color:#10B981;margin-top:2px;flex-shrink:0;"></i><?php echo e($item); ?>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <style>.vs-g{grid-template-columns:1fr!important;}@media(min-width:640px){.vs-g{grid-template-columns:1fr 1fr!important;}}</style>
    </div>
</section>


<section id="clinics" style="padding:72px 0;">
    <div style="max-width:1360px;margin:0 auto;padding:0 24px;">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:16px;">
            <div>
                <h2 style="font-family:'Fraunces',serif;font-size:clamp(26px,4vw,38px);color:#15281F;margin-bottom:6px;">Upcoming Clinics</h2>
                <p style="color:rgba(21,40,31,.55);">Structured per paper (PP1, PP2, PP3). Max 30 students per session — new sessions open automatically when one fills up.</p>
            </div>
        </div>

        
        <form method="GET" action="<?php echo e(route('examiner-clinic')); ?>" style="background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.08);padding:16px 20px;margin-bottom:24px;display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;box-shadow:0 2px 12px rgba(21,40,31,.04);">
            <div style="flex:2;min-width:180px;">
                <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.55);margin-bottom:5px;display:block;"><i class="fa-solid fa-magnifying-glass fa-xs" style="color:#6D28D9;"></i> Search</label>
                <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Subject or title…" style="width:100%;height:42px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:13.5px;font-family:inherit;color:#15281F;outline:none;transition:border-color .15s;" onfocus="this.style.borderColor='#6D28D9'" onblur="this.style.borderColor='rgba(21,40,31,.1)'">
            </div>
            <div style="min-width:160px;">
                <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.55);margin-bottom:5px;display:block;"><i class="fa-solid fa-book-open fa-xs" style="color:#6D28D9;"></i> Subject</label>
                <select name="subject" onchange="this.form.submit()" style="width:100%;height:42px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:13.5px;font-family:inherit;color:#15281F;outline:none;background:#fff;appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' fill='none'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2315281F' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 12px center;padding-right:32px;">
                    <option value="">All subjects</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s->id); ?>" <?php echo e((string)$subjectId === (string)$s->id ? 'selected' : ''); ?>><?php echo e($s->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div style="min-width:140px;">
                <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.55);margin-bottom:5px;display:block;"><i class="fa-solid fa-file-lines fa-xs" style="color:#6D28D9;"></i> Paper</label>
                <select name="paper" onchange="this.form.submit()" style="width:100%;height:42px;padding:0 13px;border-radius:10px;border:1.5px solid rgba(21,40,31,.1);font-size:13.5px;font-family:inherit;color:#15281F;outline:none;background:#fff;appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' fill='none'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2315281F' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 12px center;padding-right:32px;">
                    <option value="">All papers</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $paperTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($code); ?>" <?php echo e($paperType === $code ? 'selected' : ''); ?>><?php echo e($label); ?> (<?php echo e($code); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div style="display:flex;gap:6px;">
                <button type="submit" style="height:42px;padding:0 18px;border-radius:10px;background:#6D28D9;color:#fff;font-size:13px;font-weight:600;border:none;cursor:pointer;font-family:inherit;"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search || $subjectId || $paperType): ?>
                <a href="<?php echo e(route('examiner-clinic')); ?>" style="height:42px;padding:0 16px;border-radius:10px;background:#F4F6F4;color:#15281F;font-size:13px;font-weight:500;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-xmark fa-xs"></i> Clear</a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </form>

        
        <p style="font-size:14px;color:rgba(21,40,31,.55);margin-bottom:16px;"><strong style="color:#15281F;"><?php echo e($clinics->total()); ?></strong> clinic<?php echo e($clinics->total() != 1 ? 's' : ''); ?> found</p>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clinics->isEmpty()): ?>
        <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:64px;text-align:center;">
            <div style="font-size:48px;margin-bottom:14px;"><i class="fa-solid fa-calendar-xmark" style="color:rgba(21,40,31,.12);"></i></div>
            <h3 style="font-family:'Fraunces',serif;font-size:22px;color:#15281F;margin-bottom:8px;">No clinics scheduled</h3>
            <p style="color:rgba(21,40,31,.55);margin-bottom:20px;">No clinics match your filters. Try adjusting your search or check back soon — new clinics are added weekly.</p>
            <a href="<?php echo e(route('examiner-clinic')); ?>" style="display:inline-flex;align-items:center;gap:7px;padding:11px 24px;border-radius:999px;background:#6D28D9;color:#fff;font-size:14px;font-weight:600;text-decoration:none;"><i class="fa-solid fa-arrow-left fa-sm"></i> Clear Filters</a>
        </div>
        <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:14px;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $clinics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clinic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $isFull = $clinic->isFull();
                $enrolledCount = $clinic->enrolledCount();
                $maxStudents = $clinic->max_students;
                $fillPct = $maxStudents > 0 ? round(($enrolledCount / $maxStudents) * 100) : 0;
            ?>
            <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:24px;display:flex;align-items:flex-start;gap:18px;flex-wrap:wrap;<?php echo e($isFull ? 'opacity:.75;' : ''); ?>box-shadow:0 2px 12px rgba(21,40,31,.04);">
                <div style="width:54px;height:54px;border-radius:16px;background:rgba(212,160,23,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-solid fa-chalkboard-user" style="color:#D4A017;font-size:22px;"></i>
                </div>
                <div style="flex:1;min-width:200px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:8px;">
                        <div>
                            <h3 style="font-family:'Fraunces',serif;font-size:18px;color:#15281F;"><?php echo e($clinic->title); ?></h3>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clinic->batch_number > 1): ?>
                            <span style="font-size:11px;color:rgba(21,40,31,.4);font-weight:600;">Session <?php echo e($clinic->batch_number); ?> (auto-opened)</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <span style="font-family:'Fraunces',serif;font-size:22px;font-weight:800;color:#D4A017;flex-shrink:0;">KSh <?php echo e(number_format($clinic->price)); ?></span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:10px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clinic->tutor): ?>
                        <span style="background:rgba(109,40,217,.08);color:#6D28D9;padding:3px 11px;border-radius:999px;font-size:11px;font-weight:600;display:flex;align-items:center;gap:5px;"><i class="fa-solid fa-user fa-xs"></i><?php echo e($clinic->tutor->name); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clinic->subject): ?>
                        <span style="background:rgba(21,40,31,.06);color:#15281F;padding:3px 11px;border-radius:999px;font-size:11px;font-weight:600;display:flex;align-items:center;gap:5px;"><i class="fa-solid fa-book-open fa-xs"></i><?php echo e($clinic->subject->name); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clinic->paper_type): ?>
                        <span style="background:rgba(212,160,23,.1);color:#D4A017;padding:3px 11px;border-radius:999px;font-size:11px;font-weight:700;display:flex;align-items:center;gap:5px;"><i class="fa-solid fa-file-lines fa-xs"></i><?php echo e($clinic->paper_type); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isFull): ?>
                        <span style="background:#FEE2E2;color:#991B1B;padding:3px 11px;border-radius:999px;font-size:11px;font-weight:700;display:flex;align-items:center;gap:4px;"><i class="fa-solid fa-lock fa-xs"></i> FULL</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:16px;font-size:13px;color:rgba(21,40,31,.55);align-items:center;">
                        <span><i class="fa-regular fa-calendar fa-xs" style="color:#6D28D9;margin-right:4px;"></i><?php echo e($clinic->scheduled_at->format('D, M j \a\t g:ia')); ?></span>
                        <span><i class="fa-solid fa-clock fa-xs" style="color:#6D28D9;margin-right:4px;"></i><?php echo e($clinic->duration_minutes); ?> min</span>
                        <span style="color:<?php echo e($isFull ? '#991B1B' : '#10B981'); ?>;font-weight:600;">
                            <i class="fa-solid fa-users fa-xs" style="margin-right:4px;"></i><?php echo e($enrolledCount); ?>/<?php echo e($maxStudents); ?> enrolled
                        </span>
                    </div>
                    
                    <div style="margin-top:10px;width:100%;max-width:300px;height:6px;background:rgba(21,40,31,.06);border-radius:999px;overflow:hidden;">
                        <div style="width:<?php echo e($fillPct); ?>%;height:100%;background:<?php echo e($isFull ? '#E8522A' : ($fillPct > 70 ? '#D4A017' : '#10B981')); ?>;border-radius:999px;transition:width .3s;"></div>
                    </div>
                </div>
                <div style="flex-shrink:0;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clinic->isEnrolledBy(auth()->user())): ?>
                    <span style="background:rgba(16,185,129,.1);color:#10B981;border:1px solid rgba(16,185,129,.3);padding:10px 20px;border-radius:999px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;">
                        <i class="fa-solid fa-check fa-xs"></i> Enrolled
                    </span>
                    <?php elseif($isFull): ?>
                    <span style="background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;padding:10px 20px;border-radius:999px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;">
                        <i class="fa-solid fa-lock fa-xs"></i> Session Full
                    </span>
                    <?php else: ?>
                    <form method="POST" action="<?php echo e(route('clinic.enroll', $clinic)); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" style="display:inline-flex;align-items:center;gap:7px;padding:11px 24px;border-radius:999px;background:#D4A017;color:#15281F;font-size:14px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .15s;" onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                            <i class="fa-solid fa-ticket fa-sm"></i> Book Spot — KSh <?php echo e(number_format($clinic->price)); ?>

                        </button>
                    </form>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                    <a href="<?php echo e(route('register')); ?>" style="display:inline-flex;align-items:center;gap:7px;padding:11px 24px;border-radius:999px;background:#D4A017;color:#15281F;font-size:14px;font-weight:700;text-decoration:none;">
                        <i class="fa-solid fa-ticket fa-sm"></i> Book Spot
                    </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clinics->hasPages()): ?>
        <div style="margin-top:32px;display:flex;justify-content:center;gap:5px;flex-wrap:wrap;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clinics->onFirstPage()): ?>
            <span style="padding:8px 16px;border-radius:10px;background:#F4F6F4;color:rgba(21,40,31,.35);font-size:13px;font-weight:500;border:none;"><i class="fa-solid fa-chevron-left fa-xs"></i> Prev</span>
            <?php else: ?>
            <a href="<?php echo e($clinics->previousPageUrl()); ?>" style="padding:8px 16px;border-radius:10px;background:#fff;color:#15281F;font-size:13px;font-weight:500;border:1.5px solid rgba(21,40,31,.1);text-decoration:none;"><i class="fa-solid fa-chevron-left fa-xs"></i> Prev</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $clinics->getUrlRange(max(1, $clinics->currentPage()-2), min($clinics->lastPage(), $clinics->currentPage()+2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($url); ?>" style="padding:8px 16px;border-radius:10px;background:<?php echo e($page == $clinics->currentPage() ? '#6D28D9' : '#fff'); ?>;color:<?php echo e($page == $clinics->currentPage() ? '#fff' : '#15281F'); ?>;font-size:13px;font-weight:600;border:1.5px solid <?php echo e($page == $clinics->currentPage() ? '#6D28D9' : 'rgba(21,40,31,.1)'); ?>;text-decoration:none;"><?php echo e($page); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clinics->hasMorePages()): ?>
            <a href="<?php echo e($clinics->nextPageUrl()); ?>" style="padding:8px 16px;border-radius:10px;background:#fff;color:#15281F;font-size:13px;font-weight:500;border:1.5px solid rgba(21,40,31,.1);text-decoration:none;">Next <i class="fa-solid fa-chevron-right fa-xs"></i></a>
            <?php else: ?>
            <span style="padding:8px 16px;border-radius:10px;background:#F4F6F4;color:rgba(21,40,31,.35);font-size:13px;font-weight:500;border:none;">Next <i class="fa-solid fa-chevron-right fa-xs"></i></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>


<section id="examiners" style="padding:72px 0;background:rgba(21,40,31,.03);">
    <div style="max-width:960px;margin:0 auto;padding:0 24px;">
        <div style="text-align:center;margin-bottom:40px;">
            <h2 style="font-family:'Fraunces',serif;font-size:clamp(26px,4vw,38px);color:#15281F;margin-bottom:10px;">For Tutors & Examiners</h2>
            <p style="font-size:16px;color:rgba(21,40,31,.55);">Monetize your marking experience with zero administrative friction and instant payouts.</p>
        </div>
        <div style="display:grid;grid-template-columns:1fr;gap:14px;" class="ex-g">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                ['fa-bolt','Instant M-Pesa Payouts','Payments are processed immediately upon completing your clinic session directly to your M-Pesa account. No delays, no follow-ups.'],
                ['fa-arrow-trend-up','Build Authority & Reputation','Hosting featured clinics puts your professional profile in front of hundreds of parents and learners, positioning you as a top-tier tutor for 1-on-1 bookings.'],
                ['fa-headset','Zero Administrative Overhead','You bring the expertise. Mwalimu handles student acquisition, M-Pesa ticketing, automated reminders, and classroom setup.'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$icon,$title,$desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:24px;display:flex;gap:16px;align-items:flex-start;">
                <div style="width:48px;height:48px;background:rgba(109,40,217,.08);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-solid <?php echo e($icon); ?>" style="color:#6D28D9;font-size:18px;"></i>
                </div>
                <div>
                    <h3 style="font-family:'Fraunces',serif;font-size:17px;color:#15281F;margin-bottom:6px;"><?php echo e($title); ?></h3>
                    <p style="font-size:14px;color:rgba(21,40,31,.6);line-height:1.75;"><?php echo e($desc); ?></p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div style="text-align:center;margin-top:28px;">
            <a href="<?php echo e(route('register')); ?>?role=tutor" style="display:inline-flex;align-items:center;gap:8px;padding:14px 36px;border-radius:999px;background:#6D28D9;color:#fff;font-size:15px;font-weight:700;text-decoration:none;transition:all .18s;" onmouseover="this.style.background='#4C1D95';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#6D28D9';this.style.transform='translateY(0)'">
                <i class="fa-solid fa-chalkboard-user fa-sm"></i> Register as an Examiner
            </a>
        </div>
        <style>.ex-g{grid-template-columns:1fr!important;}@media(min-width:768px){.ex-g{grid-template-columns:repeat(3,1fr)!important;}}</style>
    </div>
</section>

<?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/pages/examiner-clinic.blade.php ENDPATH**/ ?>