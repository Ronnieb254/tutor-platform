<?php $__env->startSection('title', 'Categories & Subjects'); ?>

<?php $__env->startSection('content'); ?>
<?php
use App\Models\Category;
use App\Models\Subject;
$categories = Category::withCount('subjects')->orderBy('sort_order')->get();
?>

<div class="page-header">
    <div>
        <h1>Categories & Subjects</h1>
        <p>Manage subject categories that tutors teach</p>
    </div>
    <button onclick="document.getElementById('add-cat-modal').style.display='flex'" class="btn btn-primary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Category
    </button>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card" x-data="{ expanded: false }">
        <div style="padding:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:44px;height:44px;background:rgba(109,40,217,0.08);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;"><?php echo e($category->icon ?? '📚'); ?></div>
                    <div>
                        <div style="font-weight:700;font-size:15px;color:var(--ink);"><?php echo e($category->name); ?></div>
                        <div style="font-size:12px;color:var(--muted);"><?php echo e($category->subjects_count); ?> subjects</div>
                    </div>
                </div>
                <div style="display:flex;gap:6px;align-items:center;">
                    <span class="badge <?php echo e($category->is_active ? 'badge-green' : 'badge-gray'); ?>"><?php echo e($category->is_active ? 'Active' : 'Hidden'); ?></span>
                    <button @click="expanded=!expanded" class="btn btn-outline btn-sm" style="padding:5px 8px;">
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" :style="expanded?'transform:rotate(180deg)':''"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                </div>
            </div>

            <div x-show="expanded" x-cloak style="border-top:1px solid var(--hairline);padding-top:12px;margin-top:4px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:8px;">Subjects</div>
                <?php $subjects = Subject::where('category_id',$category->id)->get(); ?>
                <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display:flex;align-items:center;gap:4px;background:#F4F6F4;border-radius:8px;padding:4px 10px;">
                        <span style="font-size:12px;color:var(--ink);font-weight:500;"><?php echo e($subject->name); ?></span>
                        <form method="POST" action="/admin/subjects/<?php echo e($subject->id); ?>/delete" style="display:inline;" onsubmit="return confirm('Delete <?php echo e($subject->name); ?>?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" style="background:none;border:none;color:rgba(21,40,31,0.3);cursor:pointer;font-size:14px;line-height:1;padding:0 2px;" title="Delete">&times;</button>
                        </form>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <form method="POST" action="/admin/subjects" style="display:flex;gap:6px;">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="category_id" value="<?php echo e($category->id); ?>">
                    <input type="text" name="name" class="form-input" placeholder="New subject name…" style="height:36px;font-size:13px;" required>
                    <button type="submit" class="btn btn-primary btn-sm">Add</button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<div id="add-cat-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:20px;padding:32px;width:100%;max-width:440px;margin:16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <h3 style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--ink);">Add Category</h3>
            <button onclick="document.getElementById('add-cat-modal').style.display='none'" style="background:none;border:none;font-size:22px;color:var(--muted);cursor:pointer;">&times;</button>
        </div>
        <form method="POST" action="/admin/categories">
            <?php echo csrf_field(); ?>
            <div style="margin-bottom:14px;">
                <label class="form-label">Category Name</label>
                <input type="text" name="name" class="form-input" placeholder="e.g. Sciences" required>
            </div>
            <div style="margin-bottom:14px;">
                <label class="form-label">Icon (emoji)</label>
                <input type="text" name="icon" class="form-input" placeholder="e.g. 🔬" maxlength="4">
            </div>
            <div style="margin-bottom:20px;">
                <label class="form-label">Description</label>
                <input type="text" name="description" class="form-input" placeholder="Optional description">
            </div>
            <div style="display:flex;gap:10px;">
                <button type="button" onclick="document.getElementById('add-cat-modal').style.display='none'" class="btn btn-outline" style="flex:1;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex:1;">Create Category</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/pages/admin/categories.blade.php ENDPATH**/ ?>