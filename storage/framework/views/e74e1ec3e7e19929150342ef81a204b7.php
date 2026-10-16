<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Login - Mwalimu</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-black min-h-screen">
    <div class="flex min-h-screen">
        <!-- Left Panel - Branding -->
        <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-12 text-white relative overflow-hidden">
            <!-- Decorative gradient background -->
            <div class="absolute inset-0 bg-gradient-to-br from-purple-600/20 to-indigo-600/20 opacity-50"></div>
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>

            <!-- Content -->
            <div class="relative z-10">
                <div class="mb-4 flex items-center space-x-2">
                    <span class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-indigo-400 bg-clip-text text-transparent">
                        Mwalimu
                    </span>
                </div>
                <p class="text-lg text-gray-300 mt-2">Connecting Students with Expert Tutors</p>
            </div>

            <div class="relative z-10 space-y-8">
                <div class="space-y-4">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-purple-400 text-xl mt-1"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Learn from Experts</h3>
                            <p class="text-gray-400 text-sm mt-1">TSC-registered tutors with proven expertise</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-purple-400 text-xl mt-1"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Secure & Safe</h3>
                            <p class="text-gray-400 text-sm mt-1">Background-checked tutors in a secure platform</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-purple-400 text-xl mt-1"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Instant Booking</h3>
                            <p class="text-gray-400 text-sm mt-1">Book sessions and start learning in minutes</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative z-10">
                <p class="text-gray-400 text-sm">
                    © 2026 Mwalimu. Trusted by thousands of learners.
                </p>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="flex-1 flex items-center justify-center px-6 sm:px-12 lg:px-8">
            <div class="w-full max-w-sm">
                <!-- Header -->
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-white mb-2">Welcome Back</h2>
                    <p class="text-gray-400">Sign in to your account to continue</p>
                </div>

                <!-- Form -->
                <form id="loginForm" class="space-y-6" action="<?php echo e(route('login')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-500"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="w-full pl-12 pr-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                placeholder="you@example.com" value="<?php echo e(old('email')); ?>">
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-400 text-sm mt-1 block"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-500"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="w-full pl-12 pr-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                placeholder="••••••••">
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-400 text-sm mt-1 block"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input id="remember" name="remember" type="checkbox"
                                class="w-4 h-4 bg-gray-800 border border-gray-700 rounded accent-purple-500 focus:ring-2 focus:ring-purple-500">
                            <span class="text-sm text-gray-400">Remember me</span>
                        </label>
                        <a href="#" class="text-sm text-purple-400 hover:text-purple-300 transition">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full py-3 px-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition duration-200 transform hover:scale-105">
                        Sign In
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-700"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-3 bg-gray-900 text-gray-400">New to Mwalimu?</span>
                    </div>
                </div>

                <!-- Sign Up Link -->
                <div class="text-center">
                    <p class="text-gray-400 text-sm">
                        Don't have an account?
                        <a href="<?php echo e(route('register')); ?>" class="text-purple-400 hover:text-purple-300 font-semibold transition">
                            Create one now
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo e(asset('js/sweetalert-config.js')); ?>"></script>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
    <script>
        window.addEventListener('load', function() {
            const errors = <?php echo json_encode($errors->all()); ?>;
            const message = errors.length > 0 ? errors[0] : 'Login failed';
            showError('Login Failed', message);
        });
    </script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</body>
</html>
<?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/auth/login.blade.php ENDPATH**/ ?>