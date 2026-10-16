<?php $__env->startSection('title', 'Sessions'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Sessions</h1>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6 flex space-x-4 border-b border-gray-200">
        <a href="<?php echo e(route('sessions.index', ['filter' => 'upcoming'])); ?>" 
            class="px-4 py-2 border-b-2 text-sm font-medium <?php echo e($filter === 'upcoming' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
            Upcoming
        </a>
        <a href="<?php echo e(route('sessions.index', ['filter' => 'history'])); ?>" 
            class="px-4 py-2 border-b-2 text-sm font-medium <?php echo e($filter === 'history' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
            History
        </a>
        <a href="<?php echo e(route('sessions.index', ['filter' => 'all'])); ?>" 
            class="px-4 py-2 border-b-2 text-sm font-medium <?php echo e($filter === 'all' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
            All Sessions
        </a>
    </div>

    <!-- Sessions List -->
    <div class="space-y-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->isTutor()): ?>
                                <img src="<?php echo e($session->student->avatar_url); ?>" alt="<?php echo e($session->student->name); ?>" 
                                    class="h-14 w-14 rounded-full">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900"><?php echo e($session->subject->name); ?></h3>
                                    <p class="text-gray-600">Student: <strong><?php echo e($session->student->name); ?></strong></p>
                            <?php else: ?>
                                <img src="<?php echo e($session->tutor->avatar_url); ?>" alt="<?php echo e($session->tutor->name); ?>" 
                                    class="h-14 w-14 rounded-full">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900"><?php echo e($session->subject->name); ?></h3>
                                    <p class="text-gray-600">Tutor: <strong><?php echo e($session->tutor->name); ?></strong></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <p class="text-sm text-gray-500"><?php echo e($session->scheduled_at->format('l, F j, Y \a\t g:i A')); ?></p>
                                    <p class="text-sm text-gray-500">Duration: <?php echo e($session->duration_minutes); ?> minutes</p>
                                </div>
                        </div>
                        <div class="flex flex-col items-end space-y-2">
                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                <?php if($session->status === 'completed'): ?> bg-green-100 text-green-800
                                <?php elseif($session->status === 'pending'): ?> bg-yellow-100 text-yellow-800
                                <?php elseif($session->status === 'cancelled'): ?> bg-red-100 text-red-800
                                <?php else: ?> bg-blue-100 text-blue-800
                                <?php endif; ?>">
                                <?php echo e(ucfirst($session->status)); ?>

                            </span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($session->canJoin()): ?>
                                <a href="<?php echo e(route('sessions.join', $session->uuid)); ?>" 
                                    class="px-3 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700">
                                    Join Now
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
                        <div class="flex space-x-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($session->status === 'pending' && Auth::user()->isTutor()): ?>
                                <button onclick="confirmAction('<?php echo e(route('sessions.confirm', $session->uuid)); ?>', 'POST', 'Confirm Booking?', 'The student will be notified.')"
                                    class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                                    Confirm
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($session->status, ['completed', 'cancelled'])): ?>
                                <button onclick="cancelBooking('<?php echo e(route('sessions.cancel', $session->uuid)); ?>')"
                                    class="px-4 py-2 border border-red-300 text-red-600 text-sm font-medium rounded-md hover:bg-red-50">
                                    Cancel
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <a href="<?php echo e(route('sessions.show', $session->uuid)); ?>" 
                            class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No sessions found</h3>
                <p class="mt-2 text-gray-500">Get started by exploring available tutors or students.</p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sessions->hasPages()): ?>
        <div class="mt-8">
            <?php echo e($sessions->links()); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<script src="<?php echo e(asset('js/sweetalert-config.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/sessions/index.blade.php ENDPATH**/ ?>