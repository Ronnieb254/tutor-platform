<footer style="background:#0D1F14;color:rgba(255,255,255,.65);">
    <div style="background:linear-gradient(135deg,#15281F,#111C16);padding:64px 0 0;">
        <div style="max-width:1360px;margin:0 auto;padding:0 24px;">

            
            <div style="display:grid;grid-template-columns:1fr;gap:40px;margin-bottom:48px;" class="mw-foot-grid">

                
                <div>
                    <a href="<?php echo e(route('home')); ?>" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:16px;">
                        <svg width="34" height="34" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="50" fill="#FBF5E6"/>
                            <circle cx="50" cy="50" r="34" fill="#6D28D9"/>
                            <circle cx="38" cy="38" r="6" fill="#FBF5E6"/>
                        </svg>
                        <span style="font-family:'Fraunces',serif;font-size:22px;font-weight:800;color:#fff;"><span style="color:#a78bfa;">Mw</span>alimu</span>
                    </a>
                    <p style="font-size:14px;color:rgba(255,255,255,.45);line-height:1.8;max-width:280px;margin-bottom:20px;">Instant 1-on-1 tutoring, matched in seconds. TSC-registered teachers. Secure classrooms. 100% money-back on your first lesson.</p>

                    
                    <!-- <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:20px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                            ['fa-certificate','TSC-Registered Tutors','#10B981'],
                            ['fa-shield-halved','Background Checked','#10B981'],
                            ['fa-rotate-left','100% Money-Back Guarantee','#10B981'],
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$ic,$label,$color]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.6);">
                            <i class="fa-solid <?php echo e($ic); ?>" style="color:<?php echo e($color); ?>;font-size:12px;width:14px;text-align:center;"></i>
                            <?php echo e($label); ?>

                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div> -->

                    
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                            ['fa-envelope','info@ovaldesk.co.ke','mailto:info@ovaldesk.co.ke'],
                            ['fa-phone','0724 367 814 / 0723 636 901','tel:+254724367814'],
                            ['fa-location-dot','Suite 11, Samima Suites, Nairobi West',null],
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$ic,$text,$href]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="display:flex;align-items:flex-start;gap:8px;font-size:13px;color:rgba(255,255,255,.5);">
                            <i class="fa-solid <?php echo e($ic); ?>" style="color:rgba(255,255,255,.3);font-size:11px;width:14px;text-align:center;margin-top:2px;flex-shrink:0;"></i>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($href): ?>
                            <a href="<?php echo e($href); ?>" style="color:rgba(255,255,255,.6);text-decoration:none;transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.6)'"><?php echo e($text); ?></a>
                            <?php else: ?>
                            <span><?php echo e($text); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div>
                    <h4 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.35);margin-bottom:16px;">For Learners</h4>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                        ['fa-magnifying-glass','Find a Tutor',         route('tutors.index')],
                        ['fa-star',            "Examiner's Clinic",    route('examiner-clinic')],
                        ['fa-circle-info',     'How It Works',         route('how-it-works')],
                        ['fa-play-circle',     'Courses',              route('courses.index')],
                        ['fa-newspaper',       'Blog',                 route('blog.index')],
                        ['fa-user-plus',       'Create Free Account',  route('register')],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$ic,$label,$url]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($url); ?>" style="display:flex;align-items:center;gap:8px;font-size:13.5px;color:rgba(255,255,255,.6);text-decoration:none;margin-bottom:9px;transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.6)'">
                        <i class="fa-solid <?php echo e($ic); ?>" style="font-size:11px;width:13px;text-align:center;opacity:.5;"></i>
                        <?php echo e($label); ?>

                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div>
                    <h4 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.35);margin-bottom:16px;">For Tutors</h4>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                        ['fa-chalkboard-user', 'Become a Tutor',        route('register').'?role=tutor'],
                        ['fa-id-card-clip',    'Verification Process',  route('how-it-works')],
                        ['fa-star',            'Host a Clinic',         route('examiner-clinic').'#examiners'],
                        ['fa-share-nodes',     'Referral Programme',    '#'],
                        ['fa-right-to-bracket','Tutor Login',           route('login')],
                        ['fa-gauge-high',      'Dashboard',             auth()->check() ? route('dashboard') : route('login')],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$ic,$label,$url]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($url); ?>" style="display:flex;align-items:center;gap:8px;font-size:13.5px;color:rgba(255,255,255,.6);text-decoration:none;margin-bottom:9px;transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.6)'">
                        <i class="fa-solid <?php echo e($ic); ?>" style="font-size:11px;width:13px;text-align:center;opacity:.5;"></i>
                        <?php echo e($label); ?>

                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div>
                    <h4 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.35);margin-bottom:16px;">Company</h4>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                        ['fa-building',      'OvalDesk Ventures',    'https://ovaldesk.co.ke'],
                        ['fa-envelope',      'Contact Us',           route('contact')],
                        ['fa-shield-halved', 'Privacy Policy',       '#'],
                        ['fa-file-contract', 'Terms of Service',     '#'],
                        ['fa-cookie-bite',   'Cookie Policy',        '#'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$ic,$label,$url]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($url); ?>" <?php echo e(str_starts_with($url,'http') ? 'target="_blank"' : ''); ?> style="display:flex;align-items:center;gap:8px;font-size:13.5px;color:rgba(255,255,255,.6);text-decoration:none;margin-bottom:9px;transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.6)'">
                        <i class="fa-solid <?php echo e($ic); ?>" style="font-size:11px;width:13px;text-align:center;opacity:.5;"></i>
                        <?php echo e($label); ?>

                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <!-- <div style="border-top:1px solid rgba(255,255,255,.07);padding:24px 0;margin-bottom:0;">
                <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.3);margin-bottom:12px;">Session Pricing</p>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [['⚡','Quick Help','15 min','KSh 300'],['📘','Concept Check','30 min','KSh 500'],['🎯','Guided Practice','45 min','KSh 680'],['🏆','Deep Dive','60 min','KSh 800']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$ic,$name,$dur,$price]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('tutors.index')); ?>" style="display:flex;align-items:center;gap:8px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:8px 14px;text-decoration:none;transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,.1)'" onmouseout="this.style.background='rgba(255,255,255,.06)'">
                        <span style="font-size:14px;"><?php echo e($ic); ?></span>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:#fff;"><?php echo e($name); ?></div>
                            <div style="font-size:11px;color:rgba(255,255,255,.45);"><?php echo e($dur); ?> · <span style="color:#D4A017;font-weight:700;"><?php echo e($price); ?></span></div>
                        </div>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div> -->
        </div>
    </div>

    
    <div style="background:#0A1810;padding:16px 0;">
        <div style="max-width:1360px;margin:0 auto;padding:0 24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <span style="font-size:12px;color:rgba(255,255,255,.3);">&copy; <?php echo e(date('Y')); ?> Mwalimu by <a href="https://ovaldesk.co.ke" target="_blank" style="color:rgba(255,255,255,.45);text-decoration:none;transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.45)'">OvalDesk Ventures</a>. All rights reserved.</span>
            <div style="display:flex;align-items:center;gap:14px;">
                <span style="font-size:11px;color:rgba(255,255,255,.25);display:flex;align-items:center;gap:4px;"><i class="fa-solid fa-certificate fa-xs" style="color:#10B981;"></i> TSC Verified</span>
                <span style="font-size:11px;color:rgba(255,255,255,.25);display:flex;align-items:center;gap:4px;"><i class="fa-solid fa-lock fa-xs" style="color:#10B981;"></i> Secure</span>
                <span style="font-size:11px;color:rgba(255,255,255,.25);display:flex;align-items:center;gap:4px;"><i class="fa-solid fa-shield-halved fa-xs" style="color:#10B981;"></i> Verified</span>
            </div>
        </div>
    </div>
</footer>


<div x-data="{ show: !localStorage.getItem('mw_cookie') }" x-show="show" x-cloak
     style="position:fixed;bottom:20px;left:50%;transform:translateX(-50%);z-index:9999;width:calc(100% - 32px);max-width:460px;">
    <div style="background:#FFFDF5;border:2px solid #15281F;border-radius:22px;padding:18px 20px;box-shadow:0 16px 48px rgba(21,40,31,.2);">
        <div style="display:flex;gap:12px;align-items:flex-start;">
            <div style="width:38px;height:38px;border-radius:11px;background:rgba(212,160,23,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid fa-cookie-bite" style="font-size:16px;color:#D4A017;"></i>
            </div>
            <div style="flex:1;">
                <p style="font-weight:700;font-size:14px;color:#15281F;margin-bottom:3px;">We value your privacy</p>
                <p style="font-size:12px;color:rgba(21,40,31,.6);line-height:1.55;">We use cookies to personalise your experience and keep your sessions secure on Mwalimu.</p>
            </div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:14px;">
            <button @click="localStorage.setItem('mw_cookie','min');show=false"
                    style="padding:9px 18px;border-radius:999px;font-size:13px;font-weight:600;background:#fff;color:#15281F;border:1.5px solid rgba(21,40,31,.2);cursor:pointer;font-family:inherit;transition:all .15s;"
                    onmouseover="this.style.borderColor='rgba(21,40,31,.5)'" onmouseout="this.style.borderColor='rgba(21,40,31,.2)'">
                Necessary Only
            </button>
            <button @click="localStorage.setItem('mw_cookie','all');show=false"
                    style="padding:9px 20px;border-radius:999px;font-size:13px;font-weight:700;background:#6D28D9;color:#fff;border:1.5px solid #15281F;cursor:pointer;font-family:inherit;display:flex;align-items:center;gap:6px;transition:all .18s;"
                    onmouseover="this.style.background='#4C1D95'" onmouseout="this.style.background='#6D28D9'">
                <i class="fa-solid fa-check fa-xs"></i> Accept All
            </button>
        </div>
    </div>
</div>

<style>
.mw-foot-grid{grid-template-columns:1fr!important;}
@media(min-width:640px){.mw-foot-grid{grid-template-columns:repeat(2,1fr)!important;}}
@media(min-width:1024px){.mw-foot-grid{grid-template-columns:2fr 1fr 1fr 1fr!important;}}
</style>
<?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/partials/footer.blade.php ENDPATH**/ ?>