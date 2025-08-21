


<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.x/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo e(asset('css/chat/chat.css')); ?>">
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

.user-item {
    cursor: pointer;
    transition: background-color 0.2s;
}

.user-item:hover {
    background-color: #f8f9fa;
}

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}
</style>
</head>

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
                        autocomplete="off"
                        maxlength="500"
                        required
                    >
                    <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center" style="min-width: 45px;">
                        <i class="bi bi-send"></i>
                    </button>
                </form>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        <?php
                            $role = session('user_role', 'guest');
                            $userName = session('user_name', 'Guest');
                        ?>
                        <?php if($role !== 'guest'): ?>
                            Logged in as <?php echo e($userName); ?>

                        <?php else: ?>
                            Please log in to chat with users
                        <?php endif; ?>
                    </small>
                    <small class="text-muted">
                        <span id="typingIndicator" class="d-none">
                            <i class="bi bi-three-dots text-primary"></i>
                            Someone is typing...
                        </span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Chat JavaScript -->
<script>
// Global variables
let currentChatType = null;
let currentChatUser = null;
let myId = <?php echo json_encode(session('user_id'), 15, 512) ?>;
let myName = <?php echo json_encode(session('user_name', 'Guest'), 512) ?>;
const isAuthenticated = <?php echo json_encode(session('logged_in', false), 512) ?>;
const userRole = <?php echo json_encode(session('user_role', 'guest'), 512) ?>;

// Fetch current user info if myId is null
if (!myId && isAuthenticated) {
    fetchCurrentUserInfo();
}

// ─────── UTILITY FUNCTIONS ───────
function updateChatBadge() {
    // Implementation for chat badge updates
}

function loadFilterOptions() {
    // Implementation for filter options
}

function clearSearch() {
    const searchInput = document.getElementById('userSearchInput');
    if (searchInput) {
        searchInput.value = '';
        document.getElementById('searchResults').innerHTML = '';
    }
}

function clearChatHistory() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.innerHTML = '<div class="text-center text-muted py-3">Chat history cleared.</div>';
    }
}

function createGroupChat() {
    alert('Group chat feature coming soon!');
}

// ─────── NAVIGATION FUNCTIONS ───────
function goBackToSelection() {
    document.getElementById('availableUsers').classList.add('d-none');
    document.getElementById('chatSelectionPanel').classList.remove('d-none');
}

function backToSelection() {
    document.getElementById('chatInterface').classList.add('d-none');
    document.getElementById('chatSelectionPanel').classList.remove('d-none');
}

// ─────── USER TYPE SELECTION ───────
function selectUserType(type) {
    currentChatType = type;
    document.getElementById('chatSelectionPanel').classList.add('d-none');
    document.getElementById('availableUsers').classList.remove('d-none');
    
    // Set appropriate placeholders and load users
    const searchInput = document.getElementById('userSearchInput');
    if (searchInput) {
        searchInput.placeholder = `Search ${type}s...`;
        searchInput.value = '';
    }
    
    // Show FAQ quick responses for FAQ type
    if (type === 'faq') {
        document.getElementById('availableUsers').classList.add('d-none');
        document.getElementById('chatInterface').classList.remove('d-none');
        document.getElementById('faqQuickResponses').classList.remove('d-none');
        document.getElementById('chatWithName').textContent = 'FAQ Bot';
        document.getElementById('chatStatus').textContent = 'Always Available';
    } else {
        document.getElementById('faqQuickResponses').classList.add('d-none');
        loadUsersByType(type);
    }
}

// ─────── USER MANAGEMENT ───────
function fetchCurrentUserInfo() {
    fetch('/api/me', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.data) {
            myId = data.data.id;
            myName = data.data.name;
            console.log('User info fetched:', { myId, myName });
        }
    })
    .catch(error => {
        console.error('Error fetching user info:', error);
    });
}

function loadUsersByType(type) {
    fetch(`/api/chat/session/users?type=${type}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
        if (data.data && Array.isArray(data.data)) {
            displayUsers(data.data);
        } else {
            document.getElementById('searchResults').innerHTML = '<div class="text-center text-muted py-3">No users found.</div>';
        }
    })
    .catch(error => {
        console.error('Error loading users:', error);
        document.getElementById('searchResults').innerHTML = '<div class="text-center text-danger py-3">Error loading users. Please try again.</div>';
    });
}

function displayUsers(users) {
    const container = document.getElementById('searchResults');
    if (!users || !users.length) {
        container.innerHTML = '<div class="text-center text-muted py-3">No users found.</div>';
        return;
    }

    container.innerHTML = users.map(user => `
        <div class="user-item p-2 border-bottom" onclick="selectUserForChat(${user.id},'${user.name}','${user.role}')">
            <div class="d-flex align-items-center">
                <div class="avatar-circle me-2">
                    <i class="fas fa-user"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold">${user.name}</div>
                    <div class="text-muted small">${user.email}</div>
                    <div class="text-muted small text-capitalize">${user.role}</div>
                </div>
                <div class="text-end">
                    <span class="badge ${user.is_online ? 'bg-success' : 'bg-secondary'} rounded-pill">
                        ${user.is_online ? 'Online' : 'Offline'}
                    </span>
                </div>
            </div>
        </div>
    `).join('');
}

function selectUserForChat(userId, userName, userRole) {
    currentChatUser = { id: userId, name: userName, role: userRole };
    
    // Update chat interface
    document.getElementById('chatInterface').classList.remove('d-none');
    document.getElementById('availableUsers').classList.add('d-none');
    
    // Update chat header
    document.getElementById('chatWithName').textContent = userName;
    document.getElementById('chatStatus').textContent = userRole.charAt(0).toUpperCase() + userRole.slice(1);
    
    // Load messages for this user
    loadMessages(userId);
}

// ─────── SEARCH FUNCTIONALITY ───────
function performRealTimeSearch() {
    const q = document.getElementById('userSearchInput').value.trim();
    const container = document.getElementById('searchResults');
    
    if (!q) {
        container.innerHTML = '';
        return;
    }

    if (!currentChatType) {
        container.innerHTML = `<p class="text-center text-warning">Please select a user type first.</p>`;
        return;
    }
    
    // Show loading state
    container.innerHTML = `
        <div class="search-loading">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="mt-2">Searching users...</div>
        </div>
    `;
    
    fetch(`/api/chat/session/users?` + new URLSearchParams({
        type: currentChatType,
        q: q
    }), {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(r => {
        if (!r.ok) {
            throw new Error(`HTTP error! status: ${r.status}`);
        }
        return r.json();
    })
    .then(json => {
        const users = Array.isArray(json.data) ? json.data : [];
        if (!users.length) {
            container.innerHTML = `<p class="text-center text-muted">No users found for "${q}".</p>`;
        } else {
            displayUsers(users);
        }
    })
    .catch(err => {
        console.error('Search failed:', err);
        container.innerHTML = `<p class="text-center text-danger">Error searching users. Please try again.</p>`;
    });
}

// ─────── CHAT MESSAGING ───────
function loadMessages(userId) {
    if (!userId) return;
    
    const chatMessages = document.getElementById('chatMessages');
    
    fetch(`/api/chat/session/messages?with=${userId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
        if (data.data && Array.isArray(data.data)) {
            displayChatMessages(data.data);
        } else {
            chatMessages.innerHTML = '<div class="text-center text-muted py-3">No messages yet. Start a conversation!</div>';
        }
    })
    .catch(error => {
        console.error('Error loading messages:', error);
        chatMessages.innerHTML = '<div class="text-center text-danger py-3">Error loading messages. Please try again.</div>';
    });
}

function displayChatMessages(messages) {
    const chatMessages = document.getElementById('chatMessages');
    if (!messages || !Array.isArray(messages)) {
        chatMessages.innerHTML = '<div class="text-center text-muted py-3">No messages yet. Start a conversation!</div>';
        return;
    }

    chatMessages.innerHTML = messages.map(msg => {
        const isMyMessage = msg.sender_id == myId;
        const messageClass = isMyMessage ? 'user-message' : 'other-message';
        const alignClass = isMyMessage ? 'ms-auto' : 'me-auto';
        
        const senderName = isMyMessage ? 'You' : (msg.sender?.name || 'Unknown');
        const messageTime = new Date(msg.sent_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
        return `
            <div class="message ${messageClass} ${alignClass}">
                <div class="message-content">
                    <div class="message-sender">${senderName}</div>
                    <div class="message-text">${msg.message}</div>
                    <div class="message-time">${messageTime}</div>
                </div>
            </div>
        `;
    }).join('');
    
    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function sendMessageToUser(userId, message) {
    if (!userId || !message.trim()) return;
    
    const chatMessages = document.getElementById('chatMessages');
    const chatInput = document.getElementById('chatInput');
    
    fetch('/api/chat/session/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'include',
        body: JSON.stringify({
            receiver_id: userId,
            message: message.trim()
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.data) {
            // Add message to chat immediately
            const messageElement = document.createElement('div');
            messageElement.className = 'message user-message ms-auto';
            messageElement.innerHTML = `
                <div class="message-content">
                    <div class="message-sender">You</div>
                    <div class="message-text">${message}</div>
                    <div class="message-time">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                </div>
            `;
            chatMessages.appendChild(messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
            // Clear input
            chatInput.value = '';
        } else {
            throw new Error(data.error || 'Failed to send message');
        }
    })
    .catch(error => {
        console.error('Failed to send message:', error);
        alert('Failed to send message. Please try again.');
    });
}

// ─────── FAQ FUNCTIONS ───────
function selectFAQ(faqType) {
    const faqResponses = {
        enrollment: "To enroll in a course:\n1. Log into your student dashboard\n2. Go to 'Available Courses'\n3. Select your desired course\n4. Click 'Enroll Now'\n5. Complete the payment process",
        payment: "We accept Credit/Debit Cards, PayPal, Bank Transfer, and Online Banking. Installment plans are available for select courses.",
        schedule: "To view your class schedule:\n1. Login to your dashboard\n2. Go to 'My Courses'\n3. Click on 'Schedule' tab\n4. Select the course to view detailed schedule",
        certificate: "To receive your certificate:\n1. Complete all course modules\n2. Pass all required assessments\n3. Maintain minimum attendance (80%)\n4. Complete the final project/exam",
        support: "Contact our support team through Live Chat (24/7), Email: support@artc.edu, Phone: +1 (555) 123-4567, or our Support Portal."
    };
    
    const chatMessages = document.getElementById('chatMessages');
    const response = faqResponses[faqType];
    if (response) {
        const messageElement = document.createElement('div');
        messageElement.className = 'message bot me-auto';
        messageElement.innerHTML = `
            <div class="message-content">
                <div class="message-sender">FAQ Bot</div>
                <div class="message-text">${response.replace(/\n/g, '<br>')}</div>
                <div class="message-time">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
            </div>
        `;
        chatMessages.appendChild(messageElement);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

// ─────── INITIALIZATION ───────
document.addEventListener('DOMContentLoaded', () => {
    // Initialize features
    updateChatBadge();
    loadFilterOptions();

    // Get required elements
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatOffcanvas = document.getElementById('chatOffcanvas');
    const searchInput = document.getElementById('userSearchInput');

    // Wire up user type buttons
    document.querySelectorAll('.user-type-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const type = btn.dataset.type;
            selectUserType(type);
        });
    });

    // Wire up search functionality
    if (searchInput) {
        searchInput.addEventListener('keyup', performRealTimeSearch);
    }

    // Wire up chat form submission
    if (chatForm) {
        chatForm.addEventListener('submit', e => {
            e.preventDefault();
            const msg = chatInput.value.trim();
            if (!msg || !currentChatUser) return;
            
            sendMessageToUser(currentChatUser.id, msg);
        });
    }

    // Clear badge when chat is opened
    if (chatOffcanvas) {
        chatOffcanvas.addEventListener('shown.bs.offcanvas', () => {
            updateChatBadge();
        });
    }

    // Debug information
    console.log('Chat System Initialized:', {
        myId: myId,
        myName: myName,
        isAuthenticated: isAuthenticated,
        userRole: userRole,
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    });
});
</script>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\components\global-chat-fixed.blade.php ENDPATH**/ ?>