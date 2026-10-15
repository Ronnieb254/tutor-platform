<!DOCTYPE html>
<html lang="en">
<head>
<?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<title>Courses – Mwalimu</title>
<meta name="description" content="Browse pre-recorded courses from TSC-registered tutors. Learn at your own pace across the Kenyan curriculum.">
</head>
<body>
<?php
use App\Models\Course; use App\Models\Category;
$courses    = Course::published()->with(['tutor','category'])->latest()->paginate(12);
$categories = Category::active()->withCount('subjects')->get();
$colors     = ['#6D28D9','#10B981','#E8522A','#D4A017','#3B82F6','#EC4899'];
?>
<?php echo $__env->make('partials.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<section style="background:var(--ink);padding:52px 0;position:relative;overflow:hidden;">
    <div style="position:absolute;bottom:-60px;right:-40px;width:260px;height:260px;background:rgba(109,40,217,.15);border-radius:50%;"></div>
    <div class="w" style="position:relative;z-index:1;">
        <div class="badge badge-white" style="margin-bottom:16px;"><i class="fa-solid fa-play-circle"></i> Pre-Recorded Courses</div>
        <h1 style="font-size:clamp(30px,5vw,50px);color:#fff;margin-bottom:12px;">Learn at your own pace</h1>
        <p style="font-size:16px;color:rgba(255,255,255,.6);max-width:480px;">Video courses from TSC-registered tutors and national examiners — covering the full Kenyan curriculum.</p>
    </div>
</section>

<div class="w" style="padding-top:48px;padding-bottom:64px;">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($courses->isEmpty()): ?>
    <div class="card" style="padding:80px;text-align:center;">
        <i class="fa-solid fa-photo-film" style="font-size:52px;color:var(--hair);margin-bottom:18px;display:block;"></i>
        <h2 style="font-family:'Fraunces',serif;font-size:26px;color:var(--ink);margin-bottom:10px;">Courses coming soon</h2>
        <p style="color:var(--muted);font-size:16px;margin-bottom:24px;max-width:400px;margin-left:auto;margin-right:auto;">Our tutors are preparing high-quality course content. In the meantime, book a live 1-on-1 session.</p>
        <a href="<?php echo e(route('tutors.index')); ?>" class="btn btn-v"><i class="fa-solid fa-magnifying-glass"></i> Find a Tutor Instead</a>
    </div>
    <?php else: ?>
    <div style="display:grid;grid-template-columns:1fr;gap:20px;" class="course-g">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('courses.show', $course->slug)); ?>" style="background:#fff;border-radius:20px;border:1px solid var(--hair);overflow:hidden;display:block;transition:all .2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 36px rgba(21,40,31,.1)'" onmouseout="this.style.transform='none';this.style.boxShadow='none'">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->thumbnail): ?>
            <img src="<?php echo e(asset('storage/'.$course->thumbnail)); ?>" alt="<?php echo e($course->title); ?>" style="width:100%;height:200px;object-fit:cover;">
            <?php else: ?>
            <div style="width:100%;height:180px;background:<?php echo e($colors[$loop->index%count($colors)]); ?>22;display:flex;align-items:center;justify-content:center;font-size:52px;">
                <?php echo e(['📐','🔬','📚','✏️','🎯','💡'][$loop->index%6]); ?>

            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div style="padding:20px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:8px;flex-wrap:wrap;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->category): ?><span class="badge badge-v" style="font-size:10px;"><?php echo e($course->category->name); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span style="background:rgba(21,40,31,.06);color:var(--muted);padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;"><?php echo e($course->level); ?></span>
                </div>
                <h3 style="font-family:'Fraunces',serif;font-size:18px;color:var(--ink);margin-bottom:6px;line-height:1.3;"><?php echo e($course->title); ?></h3>
                <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--muted);margin-bottom:12px;">
                    <img src="<?php echo e($course->tutor->avatar_url); ?>" style="width:20px;height:20px;border-radius:999px;object-fit:cover;">
                    <span><?php echo e($course->tutor->name); ?></span>
                    <span>·</span>
                    <span><i class="fa-solid fa-play fa-xs"></i> <?php echo e($course->total_lessons); ?> lessons</span>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:<?php echo e($course->is_free?'var(--mint)':'var(--violet)'); ?>;">
                        <?php echo e($course->is_free ? 'Free' : 'KSh '.number_format($course->price)); ?>

                    </span>
                    <span class="btn btn-v btn-sm"><i class="fa-solid fa-arrow-right"></i> View Course</span>
                </div>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($courses->hasPages()): ?>
    <div style="margin-top:28px;display:flex;justify-content:center;"><?php echo e($courses->links()); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<style>.course-g{grid-template-columns:1fr!important;}@media(min-width:640px){.course-g{grid-template-columns:repeat(2,1fr)!important;}}@media(min-width:1024px){.course-g{grid-template-columns:repeat(3,1fr)!important;}}</style>
<?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/pages/courses/index.blade.php ENDPATH**/ ?>