<?php $__env->startSection('title', 'Users'); ?>

<?php $__env->startSection('content'); ?>
<?php
use App\Models\User;
$search = request('search','');
$role   = request('role','');
$users  = User::when($search, fn($q) => $q->where('name','like',"%$search%")->orWhere('email','like',"%$search%"))
              ->when($role, fn($q) => $q->where('role',$role))
              ->latest()->paginate(20);
?>

<div class="page-header">
    <div>
        <h1>All Users</h1>
        <p>Manage students, parents, tutors and admins</p>
    </div>
</div>


<div class="card card-p" style="margin-bottom:20px;">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:200px;">
            <label class="form-label">Search</label>
            <input type="text" name="search" value="<?php echo e($search); ?>" class="form-input" placeholder="Name or email…">
        </div>
        <div style="width:160px;">
            <label class="form-label">Role</label>
            <select name="role" class="form-input form-select">
                <option value="">All roles</option>
                <option value="student" <?php echo e($role==='student'?'selected':''); ?>>Students</option>
                <option value="parent" <?php echo e($role==='parent'?'selected':''); ?>>Parents</option>
                <option value="tutor" <?php echo e($role==='tutor'?'selected':''); ?>>Tutors</option>
                <option value="admin" <?php echo e($role==='admin'?'selected':''); ?>>Admins</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search || $role): ?>
        <a href="<?php echo e(route('admin.users')); ?>" class="btn btn-outline">Clear</a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </form>
</div>


<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [['Students','student','#6D28D9'],['Parents','parent','#E8522A'],['Tutors','tutor','#10B981'],['Admins','admin','#D4A017']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label,$r,$color]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route('admin.users')); ?>?role=<?php echo e($r); ?>" style="background:#fff;border:1px solid <?php echo e($r===$role ? $color : 'var(--hairline)'); ?>;border-radius:12px;padding:14px 16px;text-align:center;display:block;">
        <div style="font-family:'Fraunces',serif;font-size:24px;font-weight:700;color:<?php echo e($color); ?>;"><?php echo e(User::where('role',$r)->count()); ?></div>
        <div style="font-size:12px;color:var(--muted);margin-top:2px;"><?php echo e($label); ?></div>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<div class="card">
    <div style="padding:16px 24px;border-bottom:1px solid var(--hairline);display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:14px;color:var(--muted);"><?php echo e($users->total()); ?> user<?php echo e($users->total()!==1?'s':''); ?> found</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div class="avatar" style="background:#<?php echo e(substr(md5($user->email),0,6)); ?>;"><?php echo e(substr($user->name,0,1)); ?></div>
                            <div>
                                <div style="font-weight:600;font-size:13.5px;"><?php echo e($user->name); ?></div>
                                <div style="font-size:12px;color:var(--muted);"><?php echo e($user->email); ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php $rc=['student'=>'badge-blue','parent'=>'badge-violet','tutor'=>'badge-green','admin'=>'badge-amber'][$user->role]??'badge-gray'; ?>
                        <span class="badge <?php echo e($rc); ?>"><?php echo e(ucfirst($user->role)); ?></span>
                    </td>
                    <td style="color:var(--muted);font-size:13px;"><?php echo e($user->created_at->format('M j, Y')); ?></td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->email_verified_at): ?>
                        <span class="badge badge-green">✓ Verified</span>
                        <?php else: ?>
                        <span class="badge badge-amber">Unverified</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->role === 'tutor' && $user->tutorProfile): ?>
                            <a href="<?php echo e(route('admin.tutors')); ?>" class="btn btn-outline btn-sm">Profile</a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->id !== auth()->id()): ?>
                            <form method="POST" action="/admin/users/<?php echo e($user->id); ?>/toggle-active" style="display:inline;">
                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                <button class="btn btn-sm <?php echo e($user->is_active ? 'btn-danger' : 'btn-success'); ?>">
                                    <?php echo e($user->is_active ? 'Suspend' : 'Activate'); ?>

                                </button>
                            </form>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:40px;">No users found</td></tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($users->hasPages()): ?>
    <div style="padding:16px 24px;border-top:1px solid var(--hairline);"><?php echo e($users->withQueryString()->links()); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/pages/admin/users.blade.php ENDPATH**/ ?>