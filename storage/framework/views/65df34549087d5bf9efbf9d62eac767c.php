<?php $__env->startSection('title', 'Messages'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Messages</h1>
        <p class="mt-2 text-gray-600">Chat with students and parents</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Conversations List -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow h-96 overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Conversations</h2>
                </div>
                
                <div id="conversationsList" class="flex-1 overflow-y-auto">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <button onclick="openConversation(<?php echo e($conv['user']->id); ?>, '<?php echo e($conv['user']->name); ?>', '<?php echo e($conv['user']->avatar_url); ?>')"
                            class="w-full px-6 py-4 border-b border-gray-100 hover:bg-purple-50 text-left transition conversation-btn
                                <?php echo e($loop->first ? 'bg-purple-50' : ''); ?>"
                            data-user-id="<?php echo e($conv['user']->id); ?>">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3 flex-1">
                                    <img src="<?php echo e($conv['user']->avatar_url); ?>" alt="<?php echo e($conv['user']->name); ?>" 
                                        class="h-10 w-10 rounded-full">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate"><?php echo e($conv['user']->name); ?></p>
                                        <p class="text-xs text-gray-500 truncate"><?php echo e($conv['user']->role); ?></p>
                                    </div>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($conv['unread'] > 0): ?>
                                    <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                        <?php echo e($conv['unread']); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-6 text-center text-gray-500">
                            <p>No conversations yet</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="lg:col-span-2">
            <div id="chatArea" class="bg-white rounded-lg shadow h-96 overflow-hidden flex flex-col hidden">
                <!-- Chat Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-600 to-purple-700 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <img id="chatUserAvatar" src="" alt="" class="h-10 w-10 rounded-full">
                            <div>
                                <h3 id="chatUserName" class="font-semibold"></h3>
                                <p id="chatUserRole" class="text-xs text-purple-100"></p>
                            </div>
                        </div>
                        <button onclick="closeChat()" class="text-white hover:text-purple-100">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Messages Container -->
                <div id="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50">
                    <p class="text-center text-gray-500">Loading messages...</p>
                </div>

                <!-- Message Input -->
                <div class="px-6 py-4 border-t border-gray-200 bg-white">
                    <form id="messageForm" class="flex space-x-2">
                        <input type="hidden" id="recipientId" value="">
                        <input type="text" id="messageInput" placeholder="Type your message..." 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                            required>
                        <button type="submit" class="px-6 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition">
                            Send
                        </button>
                    </form>
                </div>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="bg-white rounded-lg shadow h-96 flex items-center justify-center">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No conversation selected</h3>
                    <p class="mt-2 text-gray-500">Select a conversation to start messaging</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo e(asset('js/sweetalert-config.js')); ?>"></script>
<script>
let currentUserId = null;
let currentUserName = null;
let currentUserAvatar = null;

function openConversation(userId, userName, userAvatar) {
    currentUserId = userId;
    currentUserName = userName;
    currentUserAvatar = userAvatar;
    
    document.getElementById('recipientId').value = userId;
    document.getElementById('chatUserName').textContent = userName;
    document.getElementById('chatUserAvatar').src = userAvatar;
    document.getElementById('chatUserAvatar').alt = userName;
    document.getElementById('chatUserRole').textContent = '<?php echo e(Auth::user()->role === 'tutor' ? 'Student' : 'Tutor'); ?>';
    
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('chatArea').classList.remove('hidden');
    
    // Highlight selected conversation
    document.querySelectorAll('.conversation-btn').forEach(btn => {
        btn.classList.remove('bg-purple-50');
    });
    document.querySelector(`[data-user-id="${userId}"]`).classList.add('bg-purple-50');
    
    loadMessages(userId);
}

function closeChat() {
    currentUserId = null;
    document.getElementById('chatArea').classList.add('hidden');
    document.getElementById('emptyState').classList.remove('hidden');
    document.querySelectorAll('.conversation-btn').forEach(btn => {
        btn.classList.remove('bg-purple-50');
    });
}

function loadMessages(userId) {
    fetch(`/api/messages/${userId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayMessages(data.messages);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Error', 'Failed to load messages');
    });
}

function displayMessages(messages) {
    const container = document.getElementById('messagesContainer');
    
    if (messages.length === 0) {
        container.innerHTML = '<p class="text-center text-gray-500 py-8">No messages yet. Start a conversation!</p>';
        return;
    }
    
    const userId = <?php echo e(Auth::id()); ?>;
    container.innerHTML = messages.map(msg => `
        <div class="flex ${msg.sender_id === userId ? 'justify-end' : 'justify-start'}">
            <div class="max-w-xs ${msg.sender_id === userId ? 'bg-purple-600 text-white' : 'bg-white border border-gray-200'} rounded-lg p-4">
                <p class="text-sm">${msg.body}</p>
                <p class="text-xs ${msg.sender_id === userId ? 'text-purple-100' : 'text-gray-500'} mt-1">
                    ${new Date(msg.created_at).toLocaleTimeString()}
                </p>
            </div>
        </div>
    `).join('');
    
    // Scroll to bottom
    container.scrollTop = container.scrollHeight;
}

document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (!message || !currentUserId) return;
    
    fetch(`/api/messages/${currentUserId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ body: message })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            loadMessages(currentUserId);
        } else {
            showError('Error', data.message || 'Failed to send message');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Error', 'An unexpected error occurred');
    });
});

// Auto-load first conversation
<?php if($conversations->isNotEmpty()): ?>
window.addEventListener('load', function() {
    const firstConv = document.querySelector('.conversation-btn');
    if (firstConv) {
        firstConv.click();
    }
});
<?php endif; ?>
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/messages/index.blade.php ENDPATH**/ ?>