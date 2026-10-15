<?php $__env->startSection('title', 'Find a Tutor'); ?>
<?php $__env->startSection('description', 'Browse verified tutors across Kenya. Filter by subject, price, and availability.'); ?>

<?php $__env->startSection('content'); ?>
<div class="section-container py-12">
    <div class="mb-8">
        <h1 class="font-serif text-4xl font-bold text-ink mb-2">Find your perfect tutor</h1>
        <p class="text-muted"><?php echo e(number_format(\App\Models\TutorProfile::approved()->count())); ?> verified tutors ready to teach</p>
    </div>
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('tutors.tutor-search');

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3882187559-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/pages/tutors/index.blade.php ENDPATH**/ ?>