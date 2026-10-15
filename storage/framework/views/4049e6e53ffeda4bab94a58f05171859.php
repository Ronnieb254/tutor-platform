<?php $__env->startSection('title', 'Blog'); ?>

<?php $__env->startSection('content'); ?>
<div class="section-container py-12">
    <div class="mb-10">
        <h1 class="font-serif text-4xl font-bold text-ink mb-2">Learning Resources</h1>
        <p class="text-muted">Tips, guides, and insights from our tutors</p>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($posts->isEmpty()): ?>
    <div class="card p-16 text-center">
        <div class="text-5xl mb-4">✍️</div>
        <p class="font-serif text-xl font-semibold text-ink">No posts yet</p>
        <p class="text-muted mt-2">Check back soon for articles and study tips.</p>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('blog.show', $post->slug)); ?>" class="card-hover block overflow-hidden group">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->thumbnail): ?>
            <img src="<?php echo e(asset('storage/' . $post->thumbnail)); ?>" alt="<?php echo e($post->title); ?>" class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-300">
            <?php else: ?>
            <div class="w-full h-44 bg-gradient-to-br from-violet/10 to-coral/10 flex items-center justify-center">
                <span class="text-4xl">📝</span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="p-5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->category): ?>
                <span class="badge-coral text-[11px]"><?php echo e($post->category->name); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <h3 class="font-serif font-semibold text-ink mt-2 line-clamp-2 group-hover:text-violet transition"><?php echo e($post->title); ?></h3>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->excerpt): ?>
                <p class="text-xs text-muted mt-2 line-clamp-2"><?php echo e($post->excerpt); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="flex items-center gap-2 mt-3 text-xs text-muted">
                    <img src="<?php echo e($post->author->avatar_url); ?>" class="w-5 h-5 rounded-full object-cover">
                    <span><?php echo e($post->author->name); ?></span>
                    <span>·</span>
                    <span><?php echo e($post->published_at->format('M j, Y')); ?></span>
                </div>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="mt-8"><?php echo e($posts->links()); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/pages/blog/index.blade.php ENDPATH**/ ?>