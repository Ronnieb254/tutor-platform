<?php $currentRoute = request()->route()?->getName() ?? ''; ?>
<div x-data="{ show: true }" x-show="show" x-cloak class="mw-ann">
    <i class="fa-solid fa-shield-check"></i>
    <span>All tutors are TSC-registered and background-checked &mdash; <strong>100% money-back</strong> on your first lesson</span>
    <button @click="show=false" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
</div>
<nav class="mw-nav" x-data="{ open: false }">
    <div class="w">
        <div class="mw-nav-inner">
            <a href="<?php echo e(route('home')); ?>" class="mw-logo">
                <svg width="36" height="36" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#15281F"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
                <em>Mwalimu</em>
            </a>
            <div class="mw-desk-links">
                <a href="<?php echo e(route('tutors.index')); ?>" class="<?php echo e(Str::startsWith($currentRoute,'tutors') ? 'active' : ''); ?>">
                    <i class="fa-solid fa-magnifying-glass fa-xs"></i> Find a Tutor
                </a>
                <a href="<?php echo e(route('how-it-works')); ?>" class="<?php echo e($currentRoute==='how-it-works' ? 'active' : ''); ?>">
                    <i class="fa-solid fa-circle-info fa-xs"></i> How It Works
                </a>
                <a href="<?php echo e(route('examiner-clinic')); ?>" class="<?php echo e($currentRoute==='examiner-clinic' ? 'active' : ''); ?>">
                    <i class="fa-solid fa-star fa-xs"></i> Examiner's Clinic
                </a>
                <a href="<?php echo e(route('blog.index')); ?>" class="<?php echo e(Str::startsWith($currentRoute,'blog') ? 'active' : ''); ?>">
                    <i class="fa-solid fa-newspaper fa-xs"></i> Blog
                </a>
                <a href="<?php echo e(route('contact')); ?>" class="<?php echo e($currentRoute==='contact' ? 'active' : ''); ?>">
                    <i class="fa-solid fa-envelope fa-xs"></i> Contact
                </a>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <?php $u = auth()->user(); ?>
                    <div x-data="{ open: false }" style="position:relative;">
                        <button @click="open=!open" @click.outside="open=false"
                                style="display:flex;align-items:center;gap:8px;padding:5px 12px 5px 5px;border-radius:999px;border:1.5px solid var(--hair);background:#fff;cursor:pointer;transition:border-color .15s;"
                                :style="open ? 'border-color:var(--violet)' : ''">
                            <img src="<?php echo e($u->avatar_url); ?>" style="width:28px;height:28px;border-radius:999px;object-fit:cover;flex-shrink:0;">
                            <span style="font-size:13px;font-weight:600;color:var(--ink);display:none;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" class="mw-desk-signin"><?php echo e(Str::limit($u->name,16)); ?></span>
                            <i class="fa-solid fa-chevron-down fa-xs" style="color:var(--muted);" :style="open ? 'transform:rotate(180deg)' : ''" style="transition:.2s"></i>
                        </button>
                        <div x-show="open" x-transition x-cloak
                             style="position:absolute;right:0;top:calc(100% + 8px);width:200px;background:#fff;border:1px solid var(--hair);border-radius:16px;box-shadow:0 8px 32px rgba(21,40,31,.12);padding:6px;z-index:200;">
                            <a href="<?php echo e(route('dashboard')); ?>" style="display:flex;align-items:center;gap:9px;padding:10px 12px;border-radius:10px;font-size:13.5px;color:var(--ink);" onmouseover="this.style.background='var(--cream)'" onmouseout="this.style.background='transparent'">
                                <i class="fa-solid fa-gauge-high fa-sm" style="color:var(--violet);width:14px;text-align:center;"></i> Dashboard
                            </a>
                            <div style="height:1px;background:var(--hair);margin:4px 0;"></div>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" style="width:100%;display:flex;align-items:center;gap:9px;padding:10px 12px;border-radius:10px;font-size:13.5px;color:var(--coral);background:none;border:none;cursor:pointer;font-family:inherit;text-align:left;" onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='transparent'">
                                    <i class="fa-solid fa-right-from-bracket fa-sm" style="width:14px;text-align:center;"></i> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="mw-desk-signin" style="font-size:14px;font-weight:600;padding:8px 18px;border-radius:999px;transition:background .15s;color:var(--ink);" onmouseover="this.style.background='rgba(21,40,31,.07)'" onmouseout="this.style.background='transparent'">Sign In</a>
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-v btn-sm"><i class="fa-solid fa-user-plus"></i> Get Started</a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button class="mw-hamburger" @click="open=!open" aria-label="Menu">
                    <i x-show="!open" class="fa-solid fa-bars"></i>
                    <i x-show="open" x-cloak class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
        <div class="mw-mobile-menu" x-show="open" x-cloak x-transition>
            <a href="<?php echo e(route('tutors.index')); ?>"><i class="fa-solid fa-magnifying-glass"></i> Find a Tutor</a>
            <a href="<?php echo e(route('how-it-works')); ?>"><i class="fa-solid fa-circle-info"></i> How It Works</a>
            <a href="<?php echo e(route('examiner-clinic')); ?>"><i class="fa-solid fa-star" style="color:var(--gold);"></i> Examiner's Clinic</a>
            <a href="<?php echo e(route('blog.index')); ?>"><i class="fa-solid fa-newspaper"></i> Blog</a>
            <a href="<?php echo e(route('contact')); ?>"><i class="fa-solid fa-envelope"></i> Contact</a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
            <a href="<?php echo e(route('login')); ?>" style="color:var(--violet);font-weight:700;"><i class="fa-solid fa-right-to-bracket"></i> Sign In</a>
            <a href="<?php echo e(route('register')); ?>" style="margin-top:8px;" class="btn btn-v" style="justify-content:center;"><i class="fa-solid fa-user-plus"></i> Get Started Free</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</nav>
<?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/partials/nav.blade.php ENDPATH**/ ?>