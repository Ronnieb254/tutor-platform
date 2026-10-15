<div class="flex flex-col lg:flex-row gap-8">
    
    <aside class="w-full lg:w-64 flex-shrink-0">
        <div class="card p-5 space-y-5 sticky top-24">
            <h2 class="font-serif text-lg font-semibold text-ink">Filter Tutors</h2>

            <div>
                <label class="form-label">Search</label>
                <input type="text" wire:model.live.debounce.400ms="search"
                       class="form-input" placeholder="Name or subject…">
            </div>

            <div>
                <label class="form-label">Category</label>
                <select wire:model.live="categoryId" class="form-input">
                    <option value="">All categories</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subjects->isNotEmpty()): ?>
            <div>
                <label class="form-label">Subject</label>
                <select wire:model.live="subjectId" class="form-input">
                    <option value="">All subjects</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($subject->id); ?>"><?php echo e($subject->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div>
                <label class="form-label">Price range (KSh/hr)</label>
                <div class="flex items-center gap-2">
                    <input type="number" wire:model.live.debounce.500ms="minPrice"
                           class="form-input" placeholder="Min" min="0">
                    <span class="text-muted text-sm">–</span>
                    <input type="number" wire:model.live.debounce.500ms="maxPrice"
                           class="form-input" placeholder="Max" min="0">
                </div>
            </div>

            <div>
                <label class="form-label">Minimum rating</label>
                <select wire:model.live="minRating" class="form-input">
                    <option value="">Any rating</option>
                    <option value="4.5">4.5+ ★</option>
                    <option value="4.0">4.0+ ★</option>
                    <option value="3.5">3.5+ ★</option>
                </select>
            </div>

            <div>
                <label class="form-label">Sort by</label>
                <select wire:model.live="sortBy" class="form-input">
                    <option value="rating_avg">Highest rated</option>
                    <option value="hourly_rate">Lowest price</option>
                    <option value="total_sessions">Most sessions</option>
                </select>
            </div>
        </div>
    </aside>

    
    <div class="flex-1">
        <div class="flex items-center justify-between mb-5">
            <p class="text-sm text-muted"><?php echo e($tutors->total()); ?> tutor<?php echo e($tutors->total() !== 1 ? 's' : ''); ?> found</p>
            <div wire:loading class="text-xs text-violet flex items-center gap-1.5">
                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="3" stroke-linecap="round" class="opacity-75"/></svg>
                Searching…
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tutors->isEmpty()): ?>
        <div class="card p-10 text-center">
            <div class="text-4xl mb-3">🔍</div>
            <p class="font-serif text-xl font-semibold text-ink mb-2">No tutors found</p>
            <p class="text-muted text-sm">Try adjusting your filters or search term.</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tutors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tutor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card-hover p-5">
                <div class="flex items-start gap-4">
                    <img src="<?php echo e($tutor->user->avatar_url); ?>" alt="<?php echo e($tutor->user->name); ?>"
                         class="w-16 h-16 rounded-2xl object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-serif font-semibold text-ink"><?php echo e($tutor->user->name); ?></p>
                                <div class="flex items-center gap-1 mt-0.5">
                                    <span class="text-mint text-xs font-semibold">✓ Verified</span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tutor->experience_years > 0): ?>
                                    <span class="text-muted text-xs">· <?php echo e($tutor->experience_years); ?>y exp</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="font-bold text-ink text-sm">KSh <?php echo e(number_format($tutor->hourly_rate, 0)); ?></p>
                                <p class="text-xs text-muted">per hour</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-1 mt-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tutor->subjects->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="badge-violet text-[10px]"><?php echo e($subject->name); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tutor->subjects->count() > 3): ?>
                            <span class="badge bg-ink/5 text-muted text-[10px]">+<?php echo e($tutor->subjects->count() - 3); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="flex items-center gap-2 mt-2 text-xs text-muted">
                            <span class="text-gold font-bold">★ <?php echo e(number_format($tutor->rating_avg, 1)); ?></span>
                            <span>(<?php echo e($tutor->total_reviews); ?> reviews)</span>
                            <span>·</span>
                            <span><?php echo e($tutor->total_sessions); ?> sessions</span>
                        </div>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tutor->bio): ?>
                <p class="text-xs text-muted mt-3 line-clamp-2"><?php echo e($tutor->bio); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="grid grid-cols-4 gap-1.5 mt-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [15, 30, 45, 60]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mins): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="text-center rounded-xl border border-hairline py-1.5 hover:border-violet hover:bg-violet/5 cursor-pointer transition">
                        <p class="text-[11px] font-bold text-ink"><?php echo e($mins); ?>m</p>
                        <p class="text-[10px] text-muted">KSh <?php echo e(number_format($tutor->getPriceForDuration($mins), 0)); ?></p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <a href="<?php echo e(route('tutors.show', \Illuminate\Support\Str::slug($tutor->user->name))); ?>"
                   class="mt-3 w-full pill-btn-primary text-sm py-2.5 block text-center">
                    View Profile & Book
                </a>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="mt-8">
            <?php echo e($tutors->links()); ?>

        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/livewire/tutors/tutor-search.blade.php ENDPATH**/ ?>