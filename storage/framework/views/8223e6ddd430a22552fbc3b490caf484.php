<?php $__env->startSection('title', 'Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<?php
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\TutorProfile;
use App\Models\PayoutRequest;

$totalUsers      = User::count();
$activeTutors    = TutorProfile::where('is_approved',true)->count();
$pendingTutors   = TutorProfile::where('is_approved',false)->count();
$todayBookings   = Booking::whereDate('created_at', today())->count();
$todayRevenue    = Payment::where('status','success')->whereDate('paid_at', today())->sum('amount') ?? 0;
$monthRevenue    = Payment::where('status','success')->whereMonth('paid_at', now()->month)->sum('amount') ?? 0;
$totalRevenue    = Payment::where('status','success')->sum('amount') ?? 0;
$pendingPayouts  = PayoutRequest::where('status','pending')->count() ?? 0;
$recentBookings  = Booking::with(['student','tutor','subject'])->latest()->take(8)->get();
$pendingApprovals= TutorProfile::where('is_approved',false)->with(['user','documents'])->latest()->take(5)->get();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="mt-2 text-gray-600">Manage users, bookings, and platform revenue</p>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-gray-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                        <dd class="text-2xl font-semibold text-gray-900"><?php echo e(number_format($totalUsers)); ?></dd>
                        <dd class="text-xs text-gray-400 mt-1"><?php echo e($activeTutors); ?> active tutors</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-gray-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Today's Bookings</dt>
                        <dd class="text-2xl font-semibold text-gray-900"><?php echo e($todayBookings); ?></dd>
                        <dd class="text-xs text-gray-400 mt-1"><?php echo e(Booking::whereDate('created_at', today()->subDay())->count()); ?> yesterday</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-gray-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Today's Revenue</dt>
                        <dd class="text-2xl font-semibold text-gray-900">KSh <?php echo e(number_format($todayRevenue)); ?></dd>
                        <dd class="text-xs text-gray-400 mt-1">Month: KSh <?php echo e(number_format($monthRevenue)); ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-gray-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Actions</dt>
                        <dd class="text-2xl font-semibold text-gray-900"><?php echo e($pendingTutors + $pendingPayouts); ?></dd>
                        <dd class="text-xs text-gray-400 mt-1"><?php echo e($pendingTutors); ?> tutors · <?php echo e($pendingPayouts); ?> payouts</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">KSh <?php echo e(number_format($totalRevenue)); ?></p>
                </div>
                <div class="flex-shrink-0 bg-gray-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Completed Sessions</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e(number_format(Booking::where('status','completed')->count())); ?></p>
                </div>
                <div class="flex-shrink-0 bg-gray-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Students</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e(number_format(User::whereIn('role',['student','parent'])->count())); ?></p>
                </div>
                <div class="flex-shrink-0 bg-gray-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Recent Bookings</h3>
                        <p class="text-sm text-gray-500 mt-1">Latest session bookings across the platform</p>
                    </div>
                    <a href="#" class="px-4 py-2 text-sm font-medium text-black hover:text-gray-700">View All →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="<?php echo e($booking->student->avatar_url); ?>" alt="<?php echo e($booking->student->name); ?>" 
                                            class="h-8 w-8 rounded-full mr-2 object-cover">
                                        <span class="text-sm font-medium text-gray-900"><?php echo e($booking->student->name); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($booking->tutor->name); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($booking->subject?->name ?? '—'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($booking->scheduled_at->format('M j, g:ia')); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">KSh <?php echo e(number_format($booking->total_amount ?? 0)); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($booking->status === 'pending'): ?>
                                        <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">Pending</span>
                                    <?php elseif($booking->status === 'confirmed'): ?>
                                        <span class="px-3 py-1 bg-black text-white text-xs font-semibold rounded-full">Confirmed</span>
                                    <?php elseif($booking->status === 'completed'): ?>
                                        <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">Completed</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Cancelled</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No bookings yet</td>
                            </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Pending Approvals</h3>
                    <p class="text-sm text-gray-500 mt-1">Tutors awaiting verification</p>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingTutors > 0): ?>
                <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full"><?php echo e($pendingTutors); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pendingApprovals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="px-6 py-4 hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <img src="<?php echo e($profile->user->avatar_url); ?>" alt="<?php echo e($profile->user->name); ?>" 
                                    class="h-10 w-10 rounded-full object-cover">
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900"><?php echo e($profile->user->name); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($profile->city ?? 'N/A'); ?> · <?php echo e($profile->documents->count()); ?> doc(s)</p>
                            </div>
                        </div>
                        <a href="#" class="px-3 py-1.5 text-xs font-medium text-black hover:text-gray-700 bg-gray-100 rounded">Review</a>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-6 py-8 text-center">
                    <p class="text-gray-500 text-sm">✨ All tutors reviewed!</p>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingTutors > 5): ?>
                <div class="px-6 py-4 border-t border-gray-200 text-center">
                    <a href="#" class="text-sm font-semibold text-black hover:text-gray-700">
                        View all <?php echo e($pendingTutors); ?> pending →
                    </a>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/dashboards/admin.blade.php ENDPATH**/ ?>