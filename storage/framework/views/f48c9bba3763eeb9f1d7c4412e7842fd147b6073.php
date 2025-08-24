
<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if Echo is available
    if (typeof window.Echo === 'undefined') {
        console.warn('Laravel Echo is not loaded. Real-time features will be disabled.');
        return;
    }

    // Get user information from session/global variables
    const currentUserId = <?php echo json_encode(auth()->check() ? auth()->user()->user_id : (session('professor_id') ?: session('director_id')), 15, 512) ?>;
    const userRole = <?php echo json_encode(auth()->check() ? auth()->user()->role : (session('professor_id') ? 'professor' : 'director'), 15, 512) ?>;
    
    if (!currentUserId) {
        console.warn('User not authenticated. Chat features disabled.');
        return;
    }

    // Listen for private messages
    window.Echo.private(`chat.${currentUserId}`)
        .listen('MessageSent', (e) => {
            console.log('New message received:', e);
            
            // Update the chat UI if the chat is currently open
            if (window.currentChatUser && window.currentChatUser.id === e.message.sender_id) {
                appendMessageToChat({
                    id: e.message.id,
                    message: e.message.message,
                    sender_id: e.message.sender_id,
                    sent_at: e.message.sent_at,
                    sender: e.message.sender
                });
            }

            // Update chat badge
            updateChatBadge();
            
            // Show notification
            showChatNotification(e.message);
        });

    // Listen for user online status changes
    window.Echo.join('presence-chat')
        .here((users) => {
            console.log('Users currently online:', users);
            updateOnlineUsers(users);
        })
        .joining((user) => {
            console.log('User joined:', user);
            updateUserOnlineStatus(user.id, true);
        })
        .leaving((user) => {
            console.log('User left:', user);
            updateUserOnlineStatus(user.id, false);
        });

    // Functions to handle chat UI updates
    function appendMessageToChat(message) {
        const chatMessages = document.getElementById('chatMessages');
        if (!chatMessages) return;

        const messageElement = createMessageElement(message);
        chatMessages.appendChild(messageElement);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function createMessageElement(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${message.sender_id === currentUserId ? 'user' : 'other-message'}`;
        
        messageDiv.innerHTML = `
            <div class="message-content">
                ${message.sender_id !== currentUserId ? `<div class="message-sender">${message.sender.name}</div>` : ''}
                <div class="message-text">${escapeHtml(message.message)}</div>
                <div class="message-time">${formatTime(message.sent_at)}</div>
            </div>
        `;
        
        return messageDiv;
    }

    function updateChatBadge() {
        // Update chat notification badge
        const badge = document.querySelector('.chat-badge');
        if (badge) {
            fetch('/api/chat/unread-count', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.count > 0) {
                    badge.textContent = data.count;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }
            })
            .catch(error => console.error('Error updating chat badge:', error));
        }
    }

        function showChatNotification(message) {
        // Browser notification
        if (Notification.permission === 'granted') {
            const notification = new Notification(`New message from ${message.sender.name}`, {
                body: message.message.substring(0, 100),
                icon: '/favicon.ico'
            });
            
            notification.onclick = function() {
                window.focus();
                autoOpenChatForNewMessage(message);
            };
        }
        
        // Auto-popup for immediate attention
        autoOpenChatForNewMessage(message);
    }`, {
                body: message.message.substring(0, 100),
                icon: '/favicon.ico'
            });
            
            notification.onclick = function() {
                window.focus();
                // Open chat with sender
                if (typeof window.openChatWith === 'function') {
                    window.openChatWith(message.sender);
                }
            };
        }
    }

    function updateOnlineUsers(users) {
        // Update online status indicators in user lists
        users.forEach(user => {
            updateUserOnlineStatus(user.id, true);
        });
    }

    function updateUserOnlineStatus(userId, isOnline) {
        const userElements = document.querySelectorAll(`[data-user-id="${userId}"]`);
        userElements.forEach(element => {
            const statusIndicator = element.querySelector('.online-status');
            if (statusIndicator) {
                statusIndicator.classList.toggle('online', isOnline);
                statusIndicator.classList.toggle('offline', !isOnline);
            }
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    // Enhanced message sending function
    window.sendRealtimeMessage = function(receiverId, message) {
        if (!message.trim()) return Promise.reject('Message cannot be empty');

        return fetch('/api/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include',
            body: JSON.stringify({
                receiver_id: receiverId,
                message: message
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Add message to local chat immediately
                appendMessageToChat({
                    id: data.data.id,
                    message: message,
                    sender_id: currentUserId,
                    sent_at: data.data.sent_at,
                    sender: {
                        name: 'You'
                    }
                });
                return data;
            } else {
                throw new Error(data.message || 'Failed to send message');
            }
        });
    };

    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }

    console.log('Real-time chat initialized for user:', currentUserId, 'role:', userRole);
});

    // Auto-open chat when new message received
    function autoOpenChatForNewMessage(message) {
        if (!message || !message.sender) return;
        
        // Don't auto-open if chat is already open with this user
        if (window.currentChatUser && window.currentChatUser.id === message.sender_id) {
            return;
        }
        
        // Show notification and auto-open chat
        const shouldAutoOpen = confirm(`New message from ${message.sender.name}:\n"${message.message.substring(0, 100)}..."\n\nOpen chat?`);
        
        if (shouldAutoOpen) {
            // Trigger chat opening
            if (typeof window.selectUserForChat === 'function') {
                window.selectUserForChat(message.sender_id, message.sender.name, message.sender.role);
            }
            
            // Open chat interface if it exists
            const chatOffcanvas = document.querySelector('[data-bs-target="#chatOffcanvas"]');
            if (chatOffcanvas) {
                chatOffcanvas.click();
            }
        }
    }

</script>
<?php $__env->stopPush(); ?>

<style>
.online-status {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-left: 5px;
}

.online-status.online {
    background-color: #28a745;
}

.online-status.offline {
    background-color: #6c757d;
}

.chat-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/components/realtime-chat.blade.php ENDPATH**/ ?>