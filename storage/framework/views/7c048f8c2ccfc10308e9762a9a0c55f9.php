<?php $__env->startSection('title', 'Blog Posts'); ?>

<?php $__env->startSection('content'); ?>
<?php
use App\Models\BlogPost;
use App\Models\BlogCategory;
$posts = BlogPost::with(['author','category'])->latest()->paginate(15);
$categories = BlogCategory::all();
?>

<div class="page-header">
    <div>
        <h1>Blog Posts</h1>
        <p>Manage articles, study guides and platform updates</p>
    </div>
    <button onclick="document.getElementById('new-post-modal').style.display='flex'" class="btn btn-primary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Post
    </button>
</div>

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px;">
    <div style="background:#fff;border:1px solid var(--hairline);border-radius:12px;padding:16px;text-align:center;">
        <div style="font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:var(--ink);"><?php echo e(BlogPost::count()); ?></div>
        <div style="font-size:12px;color:var(--muted);">Total Posts</div>
    </div>
    <div style="background:#fff;border:1px solid var(--hairline);border-radius:12px;padding:16px;text-align:center;">
        <div style="font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:var(--mint);"><?php echo e(BlogPost::where('is_published',true)->count()); ?></div>
        <div style="font-size:12px;color:var(--muted);">Published</div>
    </div>
    <div style="background:#fff;border:1px solid var(--hairline);border-radius:12px;padding:16px;text-align:center;">
        <div style="font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:var(--gold);"><?php echo e(BlogPost::where('is_published',false)->count()); ?></div>
        <div style="font-size:12px;color:var(--muted);">Drafts</div>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:13.5px;max-width:300px;"><?php echo e(Str::limit($post->title,60)); ?></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->excerpt): ?>
                        <div style="font-size:12px;color:var(--muted);margin-top:2px;"><?php echo e(Str::limit($post->excerpt,80)); ?></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->category): ?>
                        <span class="badge badge-violet"><?php echo e($post->category->name); ?></span>
                        <?php else: ?>
                        <span style="color:var(--muted);font-size:13px;">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="color:var(--muted);font-size:13px;"><?php echo e($post->author->name); ?></td>
                    <td style="color:var(--muted);font-size:13px;white-space:nowrap;"><?php echo e($post->created_at->format('M j, Y')); ?></td>
                    <td>
                        <span class="badge <?php echo e($post->is_published ? 'badge-green' : 'badge-amber'); ?>">
                            <?php echo e($post->is_published ? 'Published' : 'Draft'); ?>

                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo e(route('blog.show', $post->slug)); ?>" target="_blank" class="btn btn-outline btn-sm">View</a>
                            <form method="POST" action="/admin/blog/<?php echo e($post->id); ?>/toggle">
                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                <button class="btn btn-sm <?php echo e($post->is_published ? 'btn-danger' : 'btn-success'); ?>">
                                    <?php echo e($post->is_published ? 'Unpublish' : 'Publish'); ?>

                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:48px;color:var(--muted);">
                        <div style="font-size:36px;margin-bottom:8px;">✍️</div>
                        No blog posts yet. Create your first post!
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($posts->hasPages()): ?>
    <div style="padding:16px 24px;border-top:1px solid var(--hairline);"><?php echo e($posts->links()); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<div id="new-post-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:20px;padding:32px;width:100%;max-width:560px;max-height:90vh;overflow-y:auto;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <h3 style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--ink);">New Blog Post</h3>
            <button onclick="document.getElementById('new-post-modal').style.display='none'" style="background:none;border:none;font-size:22px;color:var(--muted);cursor:pointer;">&times;</button>
        </div>
        <form method="POST" action="/admin/blog" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div style="margin-bottom:14px;">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-input" placeholder="Post title…" required>
            </div>
            <div style="margin-bottom:14px;">
                <label class="form-label">Category</label>
                <select name="blog_category_id" class="form-input form-select">
                    <option value="">Select category</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div style="margin-bottom:14px;">
                <label class="form-label">Excerpt</label>
                <input type="text" name="excerpt" class="form-input" placeholder="Short description…">
            </div>
            <div style="margin-bottom:14px;">
                <label class="form-label">Content</label>
                <textarea name="content" rows="6" class="form-input" style="height:auto;padding:10px 14px;resize:vertical;" placeholder="Write your post content here…" required></textarea>
            </div>
            <div style="margin-bottom:14px;">
                <label class="form-label">Thumbnail</label>
                <input type="file" name="thumbnail" accept="image/*" style="font-size:13px;color:var(--muted);">
            </div>
            <div style="margin-bottom:20px;display:flex;align-items:center;gap:8px;">
                <input type="checkbox" name="is_published" id="publish_now" value="1" style="width:16px;height:16px;accent-color:var(--violet);">
                <label for="publish_now" style="font-size:14px;font-weight:500;">Publish immediately</label>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="button" onclick="document.getElementById('new-post-modal').style.display='none'" class="btn btn-outline" style="flex:1;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex:1;">Create Post</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/pages/admin/blog.blade.php ENDPATH**/ ?>