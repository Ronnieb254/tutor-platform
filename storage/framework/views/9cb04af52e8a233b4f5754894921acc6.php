<?php $__env->startSection('title', 'Tutor Approvals'); ?>

<?php $__env->startSection('content'); ?>
<?php
use App\Models\TutorProfile;
$pending   = TutorProfile::where('is_approved', false)->with(['user','subjects','documents'])->latest()->paginate(15);
$approved  = TutorProfile::where('is_approved', true)->count();
$total     = TutorProfile::count();
?>

<div class="page-header">
    <div>
        <h1>Tutor Approvals</h1>
        <p>Review tutor applications, verify documents and approve profiles</p>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <div style="background:#fff;border:1px solid var(--hairline);border-radius:12px;padding:10px 20px;text-align:center;">
            <div style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--violet);"><?php echo e($pending->total()); ?></div>
            <div style="font-size:11px;color:var(--muted);">Pending</div>
        </div>
        <div style="background:#fff;border:1px solid var(--hairline);border-radius:12px;padding:10px 20px;text-align:center;">
            <div style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--mint);"><?php echo e($approved); ?></div>
            <div style="font-size:11px;color:var(--muted);">Approved</div>
        </div>
        <div style="background:#fff;border:1px solid var(--hairline);border-radius:12px;padding:10px 20px;text-align:center;">
            <div style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);"><?php echo e($total); ?></div>
            <div style="font-size:11px;color:var(--muted);">Total</div>
        </div>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pending->isEmpty()): ?>
<div class="card card-p" style="text-align:center;padding:64px;">
    <div style="font-size:48px;margin-bottom:12px;">🎉</div>
    <h3 style="font-family:'Fraunces',serif;font-size:22px;color:var(--ink);margin-bottom:8px;">All caught up!</h3>
    <p style="color:var(--muted);">No pending tutor applications at the moment.</p>
</div>
<?php else: ?>
<div style="display:flex;flex-direction:column;gap:16px;">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card" x-data="{ expanded: false }">
        <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            
            <div style="display:flex;align-items:center;gap:16px;flex:1;min-width:0;">
                <div class="avatar" style="width:48px;height:48px;border-radius:14px;font-size:18px;background:#<?php echo e(['6D28D9','10B981','E8522A','D4A017','3B82F6'][$loop->index % 5]); ?>;">
                    <?php echo e(substr($profile->user->name, 0, 1)); ?>

                </div>
                <div style="min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <span style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);"><?php echo e($profile->user->name); ?></span>
                        <span class="badge badge-amber">Pending Review</span>
                    </div>
                    <div style="font-size:13px;color:var(--muted);margin-top:3px;">
                        <?php echo e($profile->user->email); ?> · <?php echo e($profile->city ?? 'Kenya'); ?> · KSh <?php echo e(number_format($profile->hourly_rate)); ?>/hr
                    </div>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:6px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $profile->subjects->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="badge badge-violet"><?php echo e($subject->name); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                <div style="text-align:center;padding:0 12px;">
                    <div style="font-size:11px;color:var(--muted);">Documents</div>
                    <div style="font-weight:700;color:var(--ink);"><?php echo e($profile->documents->count()); ?></div>
                </div>
                <div style="text-align:center;padding:0 12px;">
                    <div style="font-size:11px;color:var(--muted);">Applied</div>
                    <div style="font-weight:600;font-size:12px;color:var(--ink);"><?php echo e($profile->created_at->diffForHumans()); ?></div>
                </div>
                <button @click="expanded=!expanded" class="btn btn-outline btn-sm">
                    <span x-text="expanded ? 'Hide Details' : 'Review'"></span>
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" :style="expanded ? 'transform:rotate(180deg)' : ''" style="transition:.2s"><path d="M6 9l6 6 6-6"/></svg>
                </button>
            </div>
        </div>

        
        <div x-show="expanded" x-cloak style="border-top:1px solid var(--hairline);padding:24px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

                
                <div>
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:10px;">Profile Details</div>
                    <div style="background:#F4F6F4;border-radius:12px;padding:16px;font-size:13.5px;line-height:1.7;color:var(--ink);">
                        <?php echo e($profile->bio ?? 'No bio provided.'); ?>

                    </div>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:14px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [['Experience', $profile->experience_years . ' years'],['Rate', 'KSh ' . number_format($profile->hourly_rate)],['Languages', implode(', ', $profile->languages ?? ['English'])]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$k,$v]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="background:#F4F6F4;border-radius:10px;padding:10px;text-align:center;">
                            <div style="font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;"><?php echo e($k); ?></div>
                            <div style="font-size:13px;font-weight:600;color:var(--ink);margin-top:2px;"><?php echo e($v); ?></div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div>
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:10px;">Uploaded Documents</div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profile->documents->isEmpty()): ?>
                    <div style="background:#FEF9C3;border:1px solid #FDE68A;border-radius:12px;padding:14px;font-size:13px;color:#92400E;">
                        ⚠ No documents uploaded yet
                    </div>
                    <?php else: ?>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $profile->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="display:flex;align-items:center;justify-content:space-between;background:#F4F6F4;border-radius:10px;padding:12px 14px;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:32px;height:32px;background:#fff;border-radius:8px;border:1px solid var(--hairline);display:flex;align-items:center;justify-content:center;font-size:14px;">
                                    <?php echo e(in_array(pathinfo($doc->file_path, PATHINFO_EXTENSION), ['pdf']) ? '📄' : '🖼'); ?>

                                </div>
                                <div>
                                    <div style="font-size:13px;font-weight:600;color:var(--ink);"><?php echo e(ucwords(str_replace('_',' ',$doc->type))); ?></div>
                                    <div style="font-size:11px;color:var(--muted);"><?php echo e($doc->original_name ?? 'document'); ?></div>
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <a href="<?php echo e(asset('storage/'.$doc->file_path)); ?>" target="_blank" class="btn btn-outline btn-sm" style="font-size:11px;padding:4px 10px;">View</a>
                                <?php $dc = ['pending'=>'badge-amber','approved'=>'badge-green','rejected'=>'badge-red'][$doc->status]; ?>
                                <span class="badge <?php echo e($dc); ?>"><?php echo e(ucfirst($doc->status)); ?></span>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:20px;padding-top:16px;border-top:1px solid var(--hairline);">
                <div style="font-size:13px;color:var(--muted);">
                    Approve to make this tutor's profile live on the platform.
                </div>
                <div style="display:flex;gap:10px;">
                    <form method="POST" action="/admin/tutors/<?php echo e($profile->id); ?>/reject" onsubmit="return confirm('Reject this tutor application?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <button type="submit" class="btn btn-danger">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Reject
                        </button>
                    </form>
                    <form method="POST" action="/admin/tutors/<?php echo e($profile->id); ?>/approve">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <button type="submit" class="btn btn-success" style="background:var(--mint);color:#fff;">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Approve & Go Live
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<div style="margin-top:20px;"><?php echo e($pending->links()); ?></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/pages/admin/tutors.blade.php ENDPATH**/ ?>