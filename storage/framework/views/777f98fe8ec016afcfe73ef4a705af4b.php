<?php $__env->startSection('title', 'Courses'); ?>

<?php $__env->startSection('content'); ?>
<div class="section-container py-12">
    <div class="mb-8">
        <h1 class="font-serif text-4xl font-bold text-ink mb-2">Browse Courses</h1>
        <p class="text-muted">Pre-recorded lessons from our verified tutors</p>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($courses->isEmpty()): ?>
    <div class="card p-16 text-center">
        <div class="text-5xl mb-4">📚</div>
        <p class="font-serif text-xl font-semibold text-ink">Coming soon</p>
        <p class="text-muted mt-2">Our tutors are preparing course content. Check back soon!</p>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('courses.show', $course->slug)); ?>" class="card-hover block overflow-hidden">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->thumbnail): ?>
            <img src="<?php echo e(asset('storage/' . $course->thumbnail)); ?>" alt="<?php echo e($course->title); ?>" class="w-full h-44 object-cover">
            <?php else: ?>
            <div class="w-full h-44 bg-violet/10 flex items-center justify-center">
                <span class="text-5xl">📖</span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="p-5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->category): ?>
                <span class="badge-violet text-[11px]"><?php echo e($course->category->name); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <h3 class="font-serif font-semibold text-ink mt-2 line-clamp-2"><?php echo e($course->title); ?></h3>
                <p class="text-xs text-muted mt-1">by <?php echo e($course->tutor->name); ?></p>
                <div class="flex items-center justify-between mt-3">
                    <span class="text-xs text-muted"><?php echo e($course->total_lessons); ?> lessons</span>
                    <span class="font-bold text-ink"><?php echo e($course->is_free ? 'Free' : 'KSh ' . number_format($course->price, 0)); ?></span>
                </div>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="mt-8"><?php echo e($courses->links()); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/pages/courses/index.blade.php ENDPATH**/ ?>