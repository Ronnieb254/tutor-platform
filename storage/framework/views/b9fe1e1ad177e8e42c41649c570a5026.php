<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Tutor Platform'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Sticky Navigation -->
    <nav class="sticky top-0 z-50 bg-white shadow-md border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center">
                        <span class="text-2xl font-bold bg-gradient-to-r from-black to-gray-800 bg-clip-text text-transparent">
                            Mwalimu
                        </span>
                    </a>
                </div>

                <!-- Main Navigation (Desktop) -->
                <div class="hidden md:flex items-center space-x-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('dashboard')); ?>" 
                            class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 <?php echo e(request()->routeIs('dashboard') ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-100'); ?>">
                            Dashboard
                        </a>
                        <a href="<?php echo e(route('sessions.index')); ?>" 
                            class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 <?php echo e(request()->routeIs('sessions.*') ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-100'); ?>">
                            Sessions
                        </a>
                        <a href="<?php echo e(route('inbox.index')); ?>" 
                            class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 <?php echo e(request()->routeIs('inbox.*') ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-100'); ?>">
                            Messages
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <!-- Right Side Navigation -->
                <div class="flex items-center space-x-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                        <!-- Notifications -->
                        <div class="relative group">
                            <button id="notificationBell" class="relative text-gray-700 hover:text-black transition-colors duration-200 p-2 hover:bg-gray-100 rounded-lg">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </button>

                            <!-- Notification Dropdown -->
                            <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-2xl z-50 border border-gray-200">
                                <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                                    <h3 class="font-semibold text-gray-900 flex items-center">
                                        <i class="fas fa-bell text-black mr-2"></i>Notifications
                                    </h3>
                                </div>
                                <div id="notificationsList" class="max-h-96 overflow-y-auto">
                                    <p class="p-4 text-center text-gray-500 text-sm">Loading notifications...</p>
                                </div>
                                <div class="p-4 border-t border-gray-200 text-center bg-gray-50">
                                    <a href="<?php echo e(route('notifications.index')); ?>" class="text-black hover:text-gray-700 text-sm font-medium hover:underline">
                                        View All Notifications
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div class="relative group">
                            <button class="flex items-center text-gray-700 hover:text-black transition-colors duration-200 p-2 hover:bg-gray-100 rounded-lg">
                                <img src="<?php echo e(Auth::user()->avatar_url); ?>" alt="<?php echo e(Auth::user()->name); ?>" 
                                    class="h-8 w-8 rounded-full mr-2 object-cover">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                </svg>
                            </button>

                            <!-- User Dropdown -->
                            <div class="hidden group-hover:block absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-2xl z-50 border border-gray-200">
                                <div class="px-4 py-3 border-b border-gray-200">
                                    <p class="text-sm font-semibold text-gray-900"><?php echo e(Auth::user()->name); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo e(Auth::user()->email); ?></p>
                                </div>
                                <a href="<?php echo e(route('dashboard')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <a href="<?php echo e(route('notifications.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                    <i class="fas fa-bell mr-2"></i>Notifications
                                </a>
                                <hr class="my-1">
                                <form action="<?php echo e(route('logout')); ?>" method="POST" class="inline-block w-full">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="px-4 py-2 text-black hover:text-gray-700 font-medium transition-colors duration-200">
                            Login
                        </a>
                        <a href="<?php echo e(route('register')); ?>" class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 font-medium transition-colors duration-200">
                            Register
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button class="text-gray-700 hover:text-purple-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-b from-gray-900 to-black text-gray-400 mt-20 border-t border-gray-800 w-full">
        <!-- Main Footer Content -->
        <div class="w-full px-4 sm:px-6 lg:px-8 py-12">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 text-center lg:text-left">
                    <!-- Brand Section -->
                    <div class="space-y-4 flex flex-col items-center lg:items-start">
                        <h3 class="text-white font-bold text-xl">Mwalimu</h3>
                        <p class="text-sm text-gray-500">
                            Connecting students with qualified tutors for personalized learning experiences.
                        </p>
                        <div class="flex space-x-4 pt-4 justify-center lg:justify-start">
                            <a href="#" class="text-gray-500 hover:text-black transition-colors duration-200">
                                <i class="fab fa-facebook-f text-lg"></i>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-black transition-colors duration-200">
                                <i class="fab fa-twitter text-lg"></i>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-black transition-colors duration-200">
                                <i class="fab fa-linkedin-in text-lg"></i>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-black transition-colors duration-200">
                                <i class="fab fa-instagram text-lg"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="flex flex-col items-center lg:items-start">
                        <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                        <ul class="space-y-2 text-sm">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                                <li>
                                    <a href="<?php echo e(route('dashboard')); ?>" class="text-gray-500 hover:text-black transition-colors duration-200">
                                        Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('sessions.index')); ?>" class="text-gray-500 hover:text-black transition-colors duration-200">
                                        Sessions
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('inbox.index')); ?>" class="text-gray-500 hover:text-black transition-colors duration-200">
                                        Messages
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('notifications.index')); ?>" class="text-gray-500 hover:text-black transition-colors duration-200">
                                        Notifications
                                    </a>
                                </li>
                            <?php else: ?>
                                <li>
                                    <a href="<?php echo e(route('login')); ?>" class="text-gray-500 hover:text-black transition-colors duration-200">
                                        Login
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('register')); ?>" class="text-gray-500 hover:text-black transition-colors duration-200">
                                        Register
                                    </a>
                                </li>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div class="flex flex-col items-center lg:items-start">
                        <h4 class="text-white font-semibold mb-4">Support</h4>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <a href="#" class="text-gray-500 hover:text-black transition-colors duration-200">
                                    Contact Us
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-500 hover:text-black transition-colors duration-200">
                                    FAQ
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-500 hover:text-black transition-colors duration-200">
                                    Help Center
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-500 hover:text-black transition-colors duration-200">
                                    Blog
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Legal -->
                    <div class="flex flex-col items-center lg:items-start">
                        <h4 class="text-white font-semibold mb-4">Legal</h4>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <a href="#" class="text-gray-500 hover:text-black transition-colors duration-200">
                                    Privacy Policy
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-500 hover:text-black transition-colors duration-200">
                                    Terms of Service
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-500 hover:text-black transition-colors duration-200">
                                    Cookie Policy
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-500 hover:text-black transition-colors duration-200">
                                    Disclaimer
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Footer -->
        <div class="w-full border-t border-gray-800 py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-center md:justify-between items-center text-center md:text-left">
                <p class="text-gray-500 text-sm">
                    &copy; 2026 Mwalimu. All rights reserved.
                </p>
                <p class="text-gray-500 text-sm mt-4 md:mt-0">
                    Made with <i class="fas fa-heart text-red-500"></i> for educators and learners
                </p>
            </div>
        </div>
    </footer>

    <script src="<?php echo e(asset('js/sweetalert-config.js')); ?>"></script>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
    <script>
        // Load notifications on page load
        function loadNotifications() {
            fetch('<?php echo e(route('notifications.unread')); ?>', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const count = data.unread_count;
                    const notificationsList = document.getElementById('notificationsList');
                    
                    // Build notifications list
                    let html = '';
                    if (data.notifications.length > 0) {
                        data.notifications.forEach(notif => {
                            const bookingUrl = notif.data.booking_uuid ? `/sessions/${notif.data.booking_uuid}` : '#';
                            const timestamp = new Date(notif.created_at);
                            const timeAgo = getTimeAgo(timestamp);
                            
                            html += `
                                <a href="${bookingUrl}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition-colors duration-150">
                                    <p class="text-sm font-medium text-gray-900">${notif.data.message}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-clock mr-1"></i>${timeAgo}
                                    </p>
                                </a>
                            `;
                        });
                    } else {
                        html = '<p class="p-4 text-center text-gray-500 text-sm"><i class="fas fa-inbox mr-2"></i>No new notifications</p>';
                    }
                    notificationsList.innerHTML = html;
                }
            })
            .catch(error => console.error('Error loading notifications:', error));
        }

        // Helper function to format time ago
        function getTimeAgo(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            let interval = seconds / 31536000;

            if (interval > 1) return Math.floor(interval) + ' years ago';
            interval = seconds / 2592000;
            if (interval > 1) return Math.floor(interval) + ' months ago';
            interval = seconds / 86400;
            if (interval > 1) return Math.floor(interval) + ' days ago';
            interval = seconds / 3600;
            if (interval > 1) return Math.floor(interval) + ' hours ago';
            interval = seconds / 60;
            if (interval > 1) return Math.floor(interval) + ' minutes ago';
            return Math.floor(seconds) + ' seconds ago';
        }

        // Toggle notification dropdown
        document.getElementById('notificationBell').addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('hidden');
            if (!dropdown.classList.contains('hidden')) {
                loadNotifications();
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('notificationDropdown');
            if (!e.target.closest('#notificationBell') && !e.target.closest('#notificationDropdown')) {
                dropdown.classList.add('hidden');
            }
        });

        // Load notifications on page load and every 30 seconds
        loadNotifications();
        setInterval(loadNotifications, 30000);
    </script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</body>
</html>
<?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/layouts/app.blade.php ENDPATH**/ ?>