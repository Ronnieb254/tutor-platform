<div x-data="{ open: false }" class="relative" wire:poll.30s="loadCount">
    <button @click="open = !open" @click.outside="open = false"
            class="relative w-10 h-10 rounded-full border border-hairline hover:border-violet/40 flex items-center justify-center transition">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full bg-coral text-white text-[9px] font-bold flex items-center justify-center">
            <?php echo e($unreadCount > 9 ? '9+' : $unreadCount); ?>

        </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </button>

    <div x-show="open" x-transition x-cloak
         class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl border border-hairline shadow-card-hover z-50 overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-hairline">
            <span class="font-semibold text-sm text-ink">Notifications</span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
            <button wire:click="markAllRead" class="text-xs text-violet hover:underline">Mark all read</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="max-h-72 overflow-y-auto divide-y divide-hairline">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="px-4 py-3 hover:bg-cream/50 transition <?php echo e(is_null($notification->read_at) ? 'bg-violet/5' : ''); ?>">
                <p class="text-sm text-ink font-medium"><?php echo e($notification->data['message'] ?? 'New notification'); ?></p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($notification->data['code'])): ?>
                <p class="text-xs font-mono text-violet mt-0.5">Code: <?php echo e($notification->data['code']); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <p class="text-xs text-muted mt-1"><?php echo e($notification->created_at->diffForHumans()); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-4 py-8 text-center text-muted text-sm">No notifications yet</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/livewire/shared/notification-bell.blade.php ENDPATH**/ ?>