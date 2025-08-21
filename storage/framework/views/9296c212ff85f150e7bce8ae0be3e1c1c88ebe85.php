
<?php 
    $user = Auth::user();
    
    // If Laravel Auth user is not available, fallback to session data
    if (!$user) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Create a fake user object from session data for consistency
        $sessionUser = (object) [
            'id' => $_SESSION['user_id'] ?? session('user_id'),
            'name' => $_SESSION['user_name'] ?? session('user_name') ?? 'Guest',
            'role' => $_SESSION['user_type'] ?? session('user_role') ?? 'guest'
        ];
        
        // Only use session user if we have valid session data
        if ($sessionUser->id) {
            $user = $sessionUser;
        }
    }
?>


<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.x/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo e(asset('css/chat/chat.css')); ?>" rel="stylesheet">
<style>
.user-item {
    transition: all 0.3s ease;
    cursor: pointer;
    border-radius: 8px;
    margin-bottom: 8px;
}

.user-item:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.user-item .fw-bold {
    color: #333;
    font-size: 14px;
}

.user-item .text-muted {
    font-size: 12px;
}

#searchResults {
    max-height: 300px;
    overflow-y: auto;
}

.search-loading {
    text-align: center;
    padding: 20px;
    color: #6c757d;
}

.search-loading .spinner-border {
    width: 1rem;
    height: 1rem;
}

.message {
    margin-bottom: 10px;
    padding: 8px 12px;
    border-radius: 12px;
    max-width: 80%;
    word-wrap: break-word;
}

.message.user {
    background-color: #007bff;
    color: white;
    margin-left: auto;
    text-align: right;
}

.message.bot,
.message.system {
    background-color: #f8f9fa;
    color: #333;
    margin-right: auto;
    text-align: left;
}

.message.other-message {
    background-color: #e9ecef;
    color: #333;
    margin-right: auto;
    text-align: left;
}

.message-content {
    display: flex;
    flex-direction: column;
}

.message-sender {
    font-weight: bold;
    font-size: 0.9em;
    margin-bottom: 4px;
    color: #666;
}

.message-text {
    margin-bottom: 4px;
    line-height: 1.4;
}

.message-time {
    font-size: 0.8em;
    opacity: 0.7;
}

.user-message .message-sender {
    color: #cce7ff;
}

.user-message .message-time {
    color: #cce7ff;
}

#chatMessages {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.chat-loading {
    text-align: center;
    padding: 20px;
    color: #666;
}
</style>
<?php $__env->stopPush(); ?>


<script>
(function() {

  // UTILS
  function clearSearch() {
    document.getElementById('userSearchInput').value = '';
    document.getElementById('searchResults').innerHTML = '';
  }
  function clearChatHistory() {
    document.getElementById('chatMessages').innerHTML =
      '<div class="text-center text-muted py-3">Chat history cleared.</div>';
  }

  // FETCH current user if needed
  function fetchCurrentUserInfo() {
    if (!myId && isAuthenticated) {
      fetch('/api/me', {
        headers: { 'X-CSRF-TOKEN': csrfToken },
        credentials: 'include'
      })
      .then(r => r.json())
      .then(d => console.log('User info:', d.data))
      .catch(console.error);
    }
  }

  // LOAD USERS
  function loadUsersByType(type) {
    fetch(`/api/chat/session/users?type=${type}`, {
      headers: { 'X-CSRF-TOKEN': csrfToken },
      credentials: 'include'
    })
    .then(r => r.json())
    .then(json => displayUsers(json.data || []))
    .catch(console.error);
  }
  function displayUsers(users) {
    const c = document.getElementById('searchResults');
    if (!users.length) {
      return c.innerHTML = '<div class="text-center text-muted py-3">No users found.</div>';
    }
    c.innerHTML = users.map(u => `
      <div class="user-item p-2 border-bottom" onclick="selectUserForChat(${u.id},'${u.name}','${u.role}')">
        <div class="d-flex align-items-center">
          <div class="avatar-circle me-2"><i class="bi bi-person-circle"></i></div>
          <div class="flex-grow-1">
            <div class="fw-bold">${u.name}</div>
            <div class="text-muted small">${u.email}</div>
            <div class="text-muted small text-capitalize">${u.role}</div>
          </div>
          <span class="badge ${u.is_online?'bg-success':'bg-secondary'} ms-2">
            ${u.is_online?'Online':'Offline'}
          </span>
        </div>
      </div>
    `).join('');
  }

  // SELECT USER & LOAD MESSAGES
  window.selectUserForChat = function(userId, userName, userRole) {
    currentChatUser = { id:userId, name:userName, role:userRole };
    document.getElementById('chatInterface').classList.remove('d-none');
    document.getElementById('availableUsers').classList.add('d-none');
    document.getElementById('chatWithName').textContent = userName;
    loadMessages(userId);
  };

  function loadMessages(userId) {
    fetch(`/api/chat/session/messages?with=${userId}`, {
      headers: { 'X-CSRF-TOKEN': csrfToken },
      credentials: 'include'
    })
    .then(r => r.json())
    .then(json => displayChatMessages(json.data || []))
    .catch(console.error);
  }
  function displayChatMessages(msgs) {
    const c = document.getElementById('chatMessages');
    if (!msgs.length) {
      return c.innerHTML = '<div class="text-center text-muted py-3">No messages yet.</div>';
    }
    c.innerHTML = msgs.map(m => {
      const isMe = m.sender_id === myId;
      const cls  = isMe ? 'user-message ms-auto' : 'other-message me-auto';
      const name = isMe ? 'You' : (m.sender?.name || 'Unknown');
      const tm   = new Date(m.sent_at).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
      return `
        <div class="message ${cls}">
          <div class="message-content">
            <div class="message-sender">${name}</div>
            <div class="message-text">${m.message}</div>
            <div class="message-time">${tm}</div>
          </div>
        </div>
      `;
    }).join('');
    c.scrollTop = c.scrollHeight;
  }

  // SEND MESSAGE
  document.addEventListener('submit', function(e) {
    if (e.target.id === 'chatForm') {
      e.preventDefault();
      const txt = document.getElementById('chatInput').value.trim();
      if (!txt || !currentChatUser) return;
      fetch('/api/chat/session/send', {
        method:'POST',
        headers:{
          'Content-Type':'application/json',
          'X-CSRF-TOKEN':csrfToken
        },
        credentials:'include',
        body: JSON.stringify({
          receiver_id: currentChatUser.id,
          message: txt
        })
      })
      .then(r=>r.json())
      .then(d=>{
        if (d.success) {
          displayChatMessages([{ sender_id:myId, message:txt, sent_at:new Date() }]);
          document.getElementById('chatInput').value = '';
        }
      })
      .catch(console.error);
    }
  });

  // USER‐TYPE BUTTONS
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.user-type-btn');
    if (btn) {
      currentChatType = btn.dataset.type;
      document.getElementById('chatSelectionPanel').classList.add('d-none');
      document.getElementById('availableUsers').classList.remove('d-none');
      loadUsersByType(currentChatType);
    }
  });

  // INITIALIZE
  document.addEventListener('DOMContentLoaded', fetchCurrentUserInfo);
})();
</script>

<!-- Chat Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="chatOffcanvas" aria-labelledby="chatOffcanvasLabel" style="width: 400px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold text-primary" id="chatOffcanvasLabel">
            <i class="bi bi-chat-dots me-2"></i>Live Chat
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    
    <!-- Chat Selection Panel -->
    <div id="chatSelectionPanel" class="p-3 border-bottom">
        <h6 class="mb-3">Choose who to chat with:</h6>
        
        <!-- User Type Selection -->
        <div class="user-type-selection mb-3">
            <?php
                $role     = session('user_role', 'guest');
                $loggedIn = session('logged_in', false);
                $userName = session('user_name', 'Guest');
            ?>

            <?php if($loggedIn): ?>
                <?php if($role === 'admin' || $role === 'director'): ?>
                    <div class="row g-2">
                        <div class="col-4">
                            <button class="btn btn-outline-primary w-100 btn-sm user-type-btn" data-type="student">
                                <i class="bi bi-person-circle d-block"></i>
                                <small>Students</small>
                            </button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-success w-100 btn-sm user-type-btn" data-type="professor">
                                <i class="bi bi-person-badge d-block"></i>
                                <small>Professors</small>
                            </button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-info w-100 btn-sm user-type-btn" data-type="support">
                                <i class="bi bi-headset d-block"></i>
                                <small>Support</small>
                            </button>
                        </div>
                    </div>
                <?php elseif($role === 'professor'): ?>
                    <div class="row g-2">
                        <div class="col-6">
                            <button class="btn btn-outline-primary w-100 btn-sm user-type-btn" data-type="student">
                                <i class="bi bi-person-circle d-block"></i>
                                <small>Students</small>
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-info w-100 btn-sm user-type-btn" data-type="support">
                                <i class="bi bi-headset d-block"></i>
                                <small>Support</small>
                            </button>
                        </div>
                    </div>
                <?php elseif($role === 'student'): ?>
                    <div class="row g-2">
                        <div class="col-4">
                            <button type="button" class="btn btn-outline-success w-100 btn-sm user-type-btn" data-type="professor">
                                <i class="bi bi-person-badge d-block"></i>
                                <small>Professors</small>
                            </button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-info w-100 btn-sm user-type-btn" data-type="admin">
                                <i class="bi bi-shield-check d-block"></i>
                                <small>Admins</small>
                            </button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-warning w-100 btn-sm user-type-btn" data-type="faq">
                                <i class="bi bi-question-circle d-block"></i>
                                <small>FAQ</small>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center">
                    <button class="btn btn-outline-warning user-type-btn" data-type="faq">
                        <i class="bi bi-question-circle me-1"></i>FAQ Bot
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Group Chat Options (Admin/Director only) -->
        <?php if($loggedIn && ($role === 'admin' || $role === 'director')): ?>
        <div class="group-chat-options mt-3">
            <button class="btn btn-outline-secondary btn-sm w-100" onclick="createGroupChat()">
                <i class="bi bi-people me-1"></i>Create Group Chat
            </button>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Enhanced Search Section -->
    <div id="availableUsers" class="d-none p-3 border-bottom">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-person-lines-fill me-2"></i>Search Users
            </h6>
            <button class="btn btn-outline-secondary btn-sm" onclick="goBackToSelection()">
                <i class="bi bi-arrow-left me-1"></i>Back
            </button>
        </div>
        
        <!-- Enhanced Search Input -->
        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
                <input
                    type="text"
                    id="userSearchInput"
                    class="form-control"
                    placeholder="Type name or email to search..."
                    autocomplete="off"
                >
                <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
        </div>
        
        <!-- Search Results Container -->
        <div class="search-results-container">
            <div id="searchResults" class="search-results" style="max-height: 250px; overflow-y: auto;">
                <!-- Results will be populated here -->
            </div>
        </div>
    </div>
    
    <!-- Chat Interface -->
    <div id="chatInterface" class="d-none flex-grow-1 d-flex flex-column">
        <!-- Chat Header -->
        <div class="chat-header p-3 border-bottom bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="chat-avatar me-2">
                        <i class="bi bi-person-circle text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <div class="fw-medium" id="chatWithName">Support Team</div>
                        <small class="text-muted" id="chatStatus">Online</small>
                    </div>
                </div>
                <div class="chat-actions">
                    <button class="btn btn-sm btn-outline-secondary" onclick="backToSelection()">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="clearChatHistory()">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="flex-grow-1 d-flex flex-column">
            <!-- Chat Messages Area -->
            <div id="chatMessages" class="flex-grow-1 overflow-auto p-3" style="max-height: calc(100vh - 300px);">
                <!-- Messages will be populated here -->
            </div>
            
            <!-- FAQ Quick Responses -->
            <div id="faqQuickResponses" class="d-none p-3 border-top">
                <h6 class="mb-2">Frequently Asked Questions:</h6>
                <div class="faq-buttons">
                    <button class="btn btn-sm btn-outline-primary mb-2 w-100" onclick="selectFAQ('enrollment')">
                        How do I enroll in a course?
                    </button>
                    <button class="btn btn-sm btn-outline-primary mb-2 w-100" onclick="selectFAQ('payment')">
                        What are the payment options?
                    </button>
                    <button class="btn btn-sm btn-outline-primary mb-2 w-100" onclick="selectFAQ('schedule')">
                        How do I check my class schedule?
                    </button>
                    <button class="btn btn-sm btn-outline-primary mb-2 w-100" onclick="selectFAQ('certificate')">
                        How do I get my certificate?
                    </button>
                    <button class="btn btn-sm btn-outline-primary mb-2 w-100" onclick="selectFAQ('support')">
                        How do I contact support?
                    </button>
                </div>
            </div>
            
            <!-- Chat Input Form -->
            <div class="border-top p-3 bg-light">
                <form id="chatForm" class="d-flex gap-2">
                    <input 
                        type="text" 
                        id="chatInput" 
                        class="form-control" 
                        placeholder="Type your message..."
                        required
                    >
                    <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center" style="min-width: 45px;">
                        <i class="bi bi-send"></i>
                    </button>
                </form>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">
                        Press Enter to send
                    </small>
                    <small class="text-muted">
                        Real-time chat enabled
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
// Global variables already defined in layout files

// Fetch current user info if myId is null
if (typeof myId !== 'undefined' && !myId && isAuthenticated) {
    fetchCurrentUserInfo();
}

// ─────── REAL-TIME CHAT FUNCTIONALITY ───────
if (typeof window.chatPollingInterval === 'undefined') {
    window.chatPollingInterval = null;
}
if (typeof window.lastMessageId === 'undefined') {
    window.lastMessageId = 0;
}

function startRealTimeChat() {
    if (currentChatUser && currentChatUser.id) {
        window.chatPollingInterval = setInterval(() => {
            checkForNewMessages(currentChatUser.id);
        }, 3000); // Check every 3 seconds
    }
}

function stopRealTimeChat() {
    if (window.chatPollingInterval) {
        clearInterval(window.chatPollingInterval);
        window.chatPollingInterval = null;
    }
}

function checkForNewMessages(userId) {
    if (!userId) return;
    
    fetch(`/api/chat/session/messages?with=${userId}&after=${window.lastMessageId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'include'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.data && Array.isArray(data.data) && data.data.length > 0) {
            // Add new messages to chat
            const chatMessages = document.getElementById('chatMessages');
            data.data.forEach(msg => {
                // Append new message logic here
            });
            
            // Scroll to bottom
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    })
    .catch(error => {
        console.error('Error checking for new messages:', error);
    });
}

// ─────── NAVIGATION FUNCTIONS ───────
function goBackToSelection() {
    document.getElementById('availableUsers').classList.add('d-none');
    document.getElementById('chatSelectionPanel').classList.remove('d-none');
}

function backToSelection() {
    // Stop real-time polling
    stopRealTimeChat();
    
    document.getElementById('chatInterface').classList.add('d-none');
    document.getElementById('chatSelectionPanel').classList.remove('d-none');
    
    // Clear current chat user
    currentChatUser = null;
}

function createGroupChat() {
    alert('Group chat feature coming soon!');
}

function selectFAQ(type) {
    // FAQ functionality
    alert('FAQ: ' + type + ' - Feature coming soon!');
}
</script>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\components\global-chat-clean.blade.php ENDPATH**/ ?>