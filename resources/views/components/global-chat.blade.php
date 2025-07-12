{{-- Enhanced Global Chat Component --}}
{{-- This component should be included in all layout files --}}

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
            @if(auth()->check())
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'director')
                    <div class="row g-2">
                        <div class="col-4">
                            <button class="btn btn-outline-primary w-100 btn-sm" onclick="selectUserType('student')">
                                <i class="bi bi-person-circle d-block"></i>
                                <small>Students</small>
                            </button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-success w-100 btn-sm" onclick="selectUserType('professor')">
                                <i class="bi bi-person-badge d-block"></i>
                                <small>Professors</small>
                            </button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-info w-100 btn-sm" onclick="selectUserType('support')">
                                <i class="bi bi-headset d-block"></i>
                                <small>Support</small>
                            </button>
                        </div>
                    </div>
                @elseif(auth()->user()->role === 'professor')
                    <div class="row g-2">
                        <div class="col-6">
                            <button class="btn btn-outline-primary w-100 btn-sm" onclick="selectUserType('student')">
                                <i class="bi bi-person-circle d-block"></i>
                                <small>Students</small>
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-info w-100 btn-sm" onclick="selectUserType('support')">
                                <i class="bi bi-headset d-block"></i>
                                <small>Support</small>
                            </button>
                        </div>
                    </div>
                @else
                    <div class="row g-2">
                        <div class="col-4">
                            <button class="btn btn-outline-success w-100 btn-sm" onclick="selectUserType('professor')">
                                <i class="bi bi-person-badge d-block"></i>
                                <small>Professors</small>
                            </button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-info w-100 btn-sm" onclick="selectUserType('support')">
                                <i class="bi bi-headset d-block"></i>
                                <small>Support</small>
                            </button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-warning w-100 btn-sm" onclick="selectUserType('faq')">
                                <i class="bi bi-question-circle d-block"></i>
                                <small>FAQ</small>
                            </button>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center">
                    <button class="btn btn-outline-warning" onclick="selectUserType('faq')">
                        <i class="bi bi-question-circle me-1"></i>FAQ Bot
                    </button>
                </div>
            @endif
        </div>
        
        <!-- Enhanced Search Section -->
        <div id="availableUsers" class="d-none">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">
                    <i class="bi bi-person-lines-fill me-2"></i>Search Users
                </h6>
                <button class="btn btn-outline-secondary btn-sm" onclick="goBackToSelection()">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </button>
            </div>
            
            <!-- Enhanced Search Input with Real-time API -->
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           id="userSearchInput" 
                           class="form-control" 
                           placeholder="Type name or email to search..."
                           onkeyup="performRealTimeSearch()"
                           autocomplete="off">
                    <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    @if(auth()->check())
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'director')
                            Search students, professors, or admins
                        @elseif(auth()->user()->role === 'professor')
                            Search students and admins
                        @else
                            Search professors and admins
                        @endif
                    @else
                        Please log in to search users
                    @endif
                </small>
            </div>
            
            <!-- Search Filters -->
            <div class="mb-3">
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="searchFilter" id="filterAll" value="all" checked>
                    <label class="btn btn-outline-primary btn-sm" for="filterAll">All</label>
                    
                    @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'director'))
                    <input type="radio" class="btn-check" name="searchFilter" id="filterStudents" value="students">
                    <label class="btn btn-outline-success btn-sm" for="filterStudents">Students</label>
                    
                    <input type="radio" class="btn-check" name="searchFilter" id="filterProfessors" value="professors">
                    <label class="btn btn-outline-info btn-sm" for="filterProfessors">Professors</label>
                    @endif
                    
                    <input type="radio" class="btn-check" name="searchFilter" id="filterAdmins" value="admins">
                    <label class="btn btn-outline-warning btn-sm" for="filterAdmins">Admins</label>
                </div>
            </div>
            
            <!-- Search Results Container -->
            <div class="search-results-container">
                <!-- Loading indicator -->
                <div id="searchLoading" class="text-center d-none py-3">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted small mt-2 mb-0">Searching users...</p>
                </div>
                
                <!-- Search Results -->
                <div id="searchResults" class="search-results" style="max-height: 250px; overflow-y: auto;">
                    <!-- Results will be populated here -->
                </div>
                
                <!-- No results message -->
                <div id="noResults" class="text-center text-muted d-none py-3">
                    <i class="bi bi-person-x mb-2" style="font-size: 2rem;"></i>
                    <p class="mb-0">No users found matching your search.</p>
                    <small class="text-muted">Try adjusting your search criteria.</small>
                </div>
                
                <!-- Search suggestions -->
                <div id="searchSuggestions" class="d-none">
                    <small class="text-muted">Popular searches:</small>
                    <div class="mt-2">
                        <button class="btn btn-outline-secondary btn-sm me-1 mb-1" onclick="searchSuggestion('professor')">Professors</button>
                        <button class="btn btn-outline-secondary btn-sm me-1 mb-1" onclick="searchSuggestion('student')">Students</button>
                        <button class="btn btn-outline-secondary btn-sm me-1 mb-1" onclick="searchSuggestion('admin')">Admins</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Group Chat Options (Admin/Director only) -->
        @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'director'))
        <div class="group-chat-options mt-3">
            <button class="btn btn-outline-secondary btn-sm w-100" onclick="createGroupChat()">
                <i class="bi bi-people me-1"></i>Create Group Chat
            </button>
        </div>
        @endif
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
        
        <!-- FAQ Quick Responses (for FAQ Bot) -->
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
                    @if(auth()->check())
                        Logged in as {{ auth()->user()->name ?? 'User' }}
                    @else
                        Please log in to chat with users
                    @endif
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

<!-- Chat CSS Styles -->
<style>
.chat-trigger {
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
}

.chat-trigger:hover {
    transform: scale(1.1);
}

.chat-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}

.chat-message {
    max-width: 100%;
}

.chat-message .avatar {
    flex-shrink: 0;
}

.chat-message.user-message .message-content {
    max-width: 80%;
}

.chat-message.system-message .message-content {
    border: 1px solid #dee2e6;
}

.chat-message.admin-message .message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

#chatMessages {
    scroll-behavior: smooth;
}

.typing-animation {
    display: inline-flex;
    gap: 2px;
}

.typing-animation span {
    width: 4px;
    height: 4px;
    background: currentColor;
    border-radius: 50%;
    animation: typing 1.4s infinite ease-in-out;
}

.typing-animation span:nth-child(1) { animation-delay: -0.32s; }
.typing-animation span:nth-child(2) { animation-delay: -0.16s; }

@keyframes typing {
    0%, 80%, 100% { transform: scale(0); opacity: 0.5; }
    40% { transform: scale(1); opacity: 1; }
}

@media (max-width: 768px) {
    .offcanvas {
        width: 100% !important;
    }
    
    .chat-message .message-content {
        max-width: 90%;
    }
}

.user-item {
    transition: background-color 0.2s ease;
}

.user-item:hover {
    background-color: #f8f9fa !important;
    border-color: #007bff !important;
}

.user-avatar .avatar {
    transition: transform 0.2s ease;
}

.user-item:hover .user-avatar .avatar {
    transform: scale(1.1);
}

#searchLoading {
    padding: 20px;
}

#noResults {
    padding: 20px;
}

#noResults i {
    font-size: 2rem;
    margin-bottom: 10px;
}

/* Enhanced Search Styles */
.search-results-container {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background: #fff;
}

.user-result-item {
    transition: all 0.2s ease;
    border: 1px solid #e9ecef !important;
}

.user-result-item:hover {
    background-color: #f8f9fa !important;
    border-color: #007bff !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.user-result-item:active {
    transform: translateY(0);
}

.search-results {
    max-height: 300px;
    overflow-y: auto;
    padding: 10px;
}

.search-results::-webkit-scrollbar {
    width: 6px;
}

.search-results::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.search-results::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.search-results::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.btn-check:checked + .btn-outline-primary {
    background-color: #007bff;
    border-color: #007bff;
    color: #fff;
}

.btn-check:checked + .btn-outline-success {
    background-color: #28a745;
    border-color: #28a745;
    color: #fff;
}

.btn-check:checked + .btn-outline-info {
    background-color: #17a2b8;
    border-color: #17a2b8;
    color: #fff;
}

.btn-check:checked + .btn-outline-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

/* Search input enhancements */
#userSearchInput {
    border-color: #007bff;
    transition: all 0.2s ease;
}

#userSearchInput:focus {
    border-color: #0056b3;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Avatar improvements */
.user-avatar .avatar img {
    object-fit: cover;
}

/* Role badges */
.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

/* Search suggestions */
#searchSuggestions {
    padding: 10px;
    background: #f8f9fa;
    border-radius: 6px;
    margin-top: 10px;
}

#searchSuggestions .btn {
    font-size: 0.8rem;
    padding: 2px 8px;
}

/* Loading animation */
#searchLoading {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 6px;
}

/* No results styling */
#noResults {
    padding: 30px 20px;
    background: #f8f9fa;
    border-radius: 6px;
    border: 2px dashed #dee2e6;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .search-results {
        max-height: 200px;
    }
    
    .user-result-item {
        padding: 8px !important;
    }
    
    .user-avatar .avatar {
        width: 32px !important;
        height: 32px !important;
    }
}
</style>

<!-- Enhanced Chat JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatTrigger = document.querySelector('.chat-trigger');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');
    const typingIndicator = document.getElementById('typingIndicator');
    const chatOffcanvas = document.getElementById('chatOffcanvas');
    const chatSelectionPanel = document.getElementById('chatSelectionPanel');
    const chatInterface = document.getElementById('chatInterface');
    const faqQuickResponses = document.getElementById('faqQuickResponses');
    
    let hasUnseenMessages = false;
    let typingTimeout;
    let isUserTyping = false;
    let currentChatType = null;
    let currentChatUser = null;
    let chatHistory = {};
    let isAuthenticated = @json(auth()->check());
    let userRole = @json(auth()->check() ? (auth()->user()->role ?? 'guest') : 'guest');
    
    // Initialize chat
    updateChatBadge();
    
    // FAQ responses database
    const faqResponses = {
        enrollment: {
            question: "How do I enroll in a course?",
            answer: "To enroll in a course:\n1. Log into your student dashboard\n2. Go to 'Available Courses'\n3. Select your desired course\n4. Click 'Enroll Now'\n5. Complete the payment process\n\nIf you need help with enrollment, please contact our support team."
        },
        payment: {
            question: "What are the payment options?",
            answer: "We accept the following payment methods:\n• Credit/Debit Cards (Visa, Mastercard)\n• PayPal\n• Bank Transfer\n• Online Banking\n• Installment Plans (available for select courses)\n\nAll payments are processed securely through our encrypted payment gateway."
        },
        schedule: {
            question: "How do I check my class schedule?",
            answer: "To view your class schedule:\n1. Login to your dashboard\n2. Go to 'My Courses'\n3. Click on 'Schedule' tab\n4. Select the course to view detailed schedule\n\nYou can also sync your schedule with your calendar app by clicking the 'Export to Calendar' button."
        },
        certificate: {
            question: "How do I get my certificate?",
            answer: "To receive your certificate:\n1. Complete all course modules\n2. Pass all required assessments\n3. Maintain minimum attendance (80%)\n4. Complete the final project/exam\n\nCertificates are automatically generated and available in your dashboard within 5-7 business days after course completion."
        },
        support: {
            question: "How do I contact support?",
            answer: "You can reach our support team through:\n• Live Chat (available 24/7)\n• Email: support@artc.edu\n• Phone: +1 (555) 123-4567\n• Support Portal: Submit a ticket\n\nOur average response time is within 2 hours during business hours."
        }
    };
    
    // Chat form submission
    if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = chatInput.value.trim();
            
            if (message && (currentChatType || currentChatUser)) {
                // Add message to UI immediately
                addMessage(message, 'user');
                chatInput.value = '';
                
                if (currentChatType === 'faq') {
                    // Handle FAQ messages
                    saveChatHistory(message, 'user');
                    setTimeout(() => {
                        generateResponse(message);
                    }, Math.random() * 2000 + 1000);
                } else if (currentChatUser) {
                    // Send message to real user via API
                    sendMessageToUser(currentChatUser.id, message);
                } else {
                    // Fallback for other chat types
                    saveChatHistory(message, 'user');
                    setTimeout(() => {
                        generateResponse(message);
                    }, Math.random() * 2000 + 1000);
                }
            }
        });
    }
    
    // Send message to user via API (updated to use enhanced system)
    function sendMessageToUser(userId, message) {
        fetch('/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                receiver_id: userId,
                content: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                console.log('Message sent successfully');
                // Message will be handled by real-time broadcasting
            } else {
                console.error('Failed to send message:', data.error);
                addMessage('Sorry, there was an error sending your message. Please try again.', 'system', 'System');
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            addMessage('Sorry, there was an error sending your message. Please try again.', 'system', 'System');
        });
    }
    
    // Clear badge when chat is opened
    if (chatOffcanvas) {
        chatOffcanvas.addEventListener('shown.bs.offcanvas', function() {
            hasUnseenMessages = false;
            updateChatBadge();
            loadChatHistory();
        });
    }
    
    // User type selection
    window.selectUserType = function(type) {
        currentChatType = type;
        
        if (type === 'faq') {
            showFAQInterface();
        } else if (!isAuthenticated && type !== 'faq') {
            alert('Please log in to chat with users');
            return;
        } else {
            showUserSelection(type);
        }
    };
    
    // Show FAQ interface
    function showFAQInterface() {
        chatSelectionPanel.classList.add('d-none');
        chatInterface.classList.remove('d-none');
        faqQuickResponses.classList.remove('d-none');
        
        document.getElementById('chatWithName').textContent = 'FAQ Bot';
        document.getElementById('chatStatus').textContent = 'Always Available';
        
        // Show welcome message
        clearChatMessages();
        addMessage("👋 Hi! I'm the ARTC FAQ Bot. I can help you with frequently asked questions. Choose from the options below or type your question directly.", 'system', 'FAQ Bot');
    }
    
    // Show user selection
    function showUserSelection(type) {
        const availableUsers = document.getElementById('availableUsers');
        const usersList = availableUsers.querySelector('.available-users-list');
        
        // Clear previous results
        usersList.innerHTML = '';
        
        // Show the search interface
        chatSelectionPanel.classList.add('d-none');
        availableUsers.classList.remove('d-none');
        
        // Store current chat type for search
        currentChatType = type;
        
        // Load initial users from API (show some users by default)
        loadUsersFromAPI(type, '');
        
        // Focus on search input
        const searchInput = document.getElementById('userSearchInput');
        if (searchInput) {
            searchInput.focus();
        }
    }
    
    // Load users from API
    function loadUsersFromAPI(type, search = '') {
        const searchLoading = document.getElementById('searchLoading');
        const noResults = document.getElementById('noResults');
        const usersList = document.querySelector('.available-users-list');
        
        showSearchLoading(true);
        noResults.classList.add('d-none');
        
        fetch(`/chat/enhanced-search?type=${type}&search=${encodeURIComponent(search)}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            showSearchLoading(false);
            
            if (data.success && data.users) {
                displayUsers(data.users, type);
                
                if (data.users.length === 0) {
                    noResults.classList.remove('d-none');
                }
            } else {
                console.error('Failed to load users:', data.error);
                noResults.classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Error loading users:', error);
            showSearchLoading(false);
            noResults.classList.remove('d-none');
        });
    }
    
    // Display users in the list
    function displayUsers(users, type) {
        const usersList = document.querySelector('.available-users-list');
        usersList.innerHTML = '';
        
        users.forEach(user => {
            const userDiv = document.createElement('div');
            userDiv.className = 'user-item p-2 mb-2 border rounded cursor-pointer';
            userDiv.style.cursor = 'pointer';
            
            // Get status badge color
            const statusColor = user.status === 'online' ? 'success' : 
                              user.status === 'away' ? 'warning' : 'secondary';
            
            userDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="user-avatar me-2">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                            ${user.avatar}
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-medium">${user.name}</div>
                        <small class="text-muted">${user.email}</small>
                        ${user.last_seen ? `<br><small class="text-muted">Last seen: ${user.last_seen}</small>` : ''}
                    </div>
                    <div class="status-indicator">
                        <span class="badge bg-${statusColor}">
                            ${user.status}
                        </span>
                    </div>
                </div>
            `;
            
            userDiv.addEventListener('click', function() {
                selectUser(user, type);
            });
            
            usersList.appendChild(userDiv);
        });
    }
    
    // Show/hide search loading
    function showSearchLoading(show) {
        const searchLoading = document.getElementById('searchLoading');
        if (show) {
            searchLoading.classList.remove('d-none');
        } else {
            searchLoading.classList.add('d-none');
        }
    }
    
    // Enhanced Real-time Search with API
    let searchTimeout;
    let lastSearchTerm = '';
    let currentSearchFilter = 'all';
    
    // Add event listeners for search filters
    document.addEventListener('DOMContentLoaded', function() {
        const searchFilters = document.querySelectorAll('input[name="searchFilter"]');
        searchFilters.forEach(filter => {
            filter.addEventListener('change', function() {
                currentSearchFilter = this.value;
                const searchInput = document.getElementById('userSearchInput');
                if (searchInput && searchInput.value.trim()) {
                    performRealTimeSearch();
                }
            });
        });
    });
    
    // Perform real-time search
    function performRealTimeSearch() {
        const searchInput = document.getElementById('userSearchInput');
        const searchTerm = searchInput.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (searchTerm === lastSearchTerm) {
            return; // No change in search term
        }
        
        lastSearchTerm = searchTerm;
        
        if (searchTerm.length < 2) {
            clearSearchResults();
            showSearchSuggestions();
            return;
        }
        
        searchTimeout = setTimeout(() => {
            searchUsersWithAPI(searchTerm, currentSearchFilter);
        }, 300);
    }
    
    // Search users with API (updated to use enhanced search)
    function searchUsersWithAPI(searchTerm, filter) {
        showSearchLoading(true);
        hideSearchSuggestions();
        
        const params = new URLSearchParams({
            query: searchTerm,
            type: filter,
            limit: 20
        });
        
        // Use the enhanced search endpoint for better results
        fetch(`/chat/search-users?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                showSearchLoading(false);
                
                if (data && Array.isArray(data)) {
                    displaySearchResults(data);
                } else {
                    console.error('Search failed:', data.error);
                    showNoResults();
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                showSearchLoading(false);
                showNoResults();
            });
    }
    
    // Display search results
    function displaySearchResults(users) {
        const resultsContainer = document.getElementById('searchResults');
        const noResults = document.getElementById('noResults');
        
        if (users.length === 0) {
            showNoResults();
            return;
        }
        
        noResults.classList.add('d-none');
        resultsContainer.innerHTML = '';
        
        users.forEach(user => {
            const userDiv = document.createElement('div');
            userDiv.className = 'user-result-item p-2 mb-2 border rounded';
            userDiv.style.cursor = 'pointer';
            
            // Get role-specific styling
            const roleConfig = getRoleConfig(user.role);
            
            userDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="user-avatar me-2">
                        <div class="avatar ${roleConfig.bgClass} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 0.9rem;">
                            ${user.avatar ? '<img src="' + user.avatar + '" class="rounded-circle" width="36" height="36">' : user.name.charAt(0).toUpperCase()}
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-medium">${user.name}</div>
                                <small class="text-muted">${user.email}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge ${roleConfig.badgeClass}">${roleConfig.label}</span>
                            </div>
                        </div>
                        <div class="mt-1">
                            <small class="text-muted">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem; color: ${user.status === 'active' ? '#28a745' : '#6c757d'};"></i>
                                ${user.status || 'Unknown'}
                            </small>
                        </div>
                    </div>
                </div>
            `;
            
            // Add click event to start chat
            userDiv.addEventListener('click', function() {
                selectUserForChat(user);
            });
            
            // Add hover effect
            userDiv.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
                this.style.borderColor = '#007bff';
            });
            
            userDiv.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
                this.style.borderColor = '';
            });
            
            resultsContainer.appendChild(userDiv);
        });
    }
    
    // Get role configuration for styling
    function getRoleConfig(role) {
        const configs = {
            'student': {
                bgClass: 'bg-primary',
                badgeClass: 'bg-primary',
                label: 'Student'
            },
            'professor': {
                bgClass: 'bg-success',
                badgeClass: 'bg-success',
                label: 'Professor'
            },
            'admin': {
                bgClass: 'bg-warning',
                badgeClass: 'bg-warning text-dark',
                label: 'Admin'
            },
            'director': {
                bgClass: 'bg-info',
                badgeClass: 'bg-info',
                label: 'Director'
            }
        };
        
        return configs[role] || configs['student'];
    }
    
    // Select user for chat
    function selectUserForChat(user) {
        currentChatUser = user;
        
        // Hide search panel and show chat interface
        document.getElementById('chatSelectionPanel').classList.add('d-none');
        document.getElementById('chatInterface').classList.remove('d-none');
        document.getElementById('faqQuickResponses').classList.add('d-none');
        
        // Update chat header
        document.getElementById('chatWithName').textContent = user.name;
        document.getElementById('chatStatus').textContent = user.status || 'Online';
        
        // Clear search
        clearSearch();
        
        // Load chat history
        loadChatHistoryForUser(user.id);
        
        // Show welcome message
        clearChatMessages();
        addMessage(`Hello! You're now chatting with ${user.name}. How can I help you today?`, 'other', user.name);
    }
    
    // Load chat history for specific user (enhanced version)
    function loadChatHistoryForUser(userId) {
        fetch(`/chat/history/${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.messages) {
                    clearChatMessages();
                    // Load existing messages
                    data.messages.forEach(message => {
                        const messageType = message.sender_id === @json(auth()->id()) ? 'user' : 'other';
                        addMessage(message.content, messageType, message.sender_name);
                    });
                } else {
                    // Show welcome message if no history
                    clearChatMessages();
                    addMessage(`Hello! You're now connected with ${currentChatUser.name}. Start the conversation!`, 'system', 'System');
                }
            })
            .catch(error => {
                console.error('Error loading chat history:', error);
                clearChatMessages();
                addMessage('Unable to load chat history. You can still send messages.', 'system', 'System');
            });
    }
    
    // Enhanced message sending with database storage
    function sendMessageToSelectedUser(message) {
        if (!currentChatUser) {
            console.error('No chat user selected');
            return;
        }
        
        // Save message to database
        fetch('/chat/save-message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                recipient_id: currentChatUser.id,
                message: message,
                chat_type: 'direct'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Message saved successfully');
                
                // Simulate response from the user
                setTimeout(() => {
                    const responses = [
                        "Thanks for your message! I'll get back to you soon.",
                        "I received your message. Let me think about this and respond shortly.",
                        "That's a good point! Let me consider this and reply.",
                        "I appreciate you reaching out. I'll respond as soon as possible.",
                        "Thanks for the message! I'll get back to you with more information."
                    ];
                    const randomResponse = responses[Math.floor(Math.random() * responses.length)];
                    addMessage(randomResponse, 'other', currentChatUser.name);
                    
                    // Save the response as well
                    saveChatHistory(randomResponse, 'other');
                }, Math.random() * 3000 + 2000);
                
            } else {
                console.error('Failed to save message:', data.error);
                addMessage('Sorry, there was an error sending your message. Please try again.', 'system', 'System');
            }
        })
        .catch(error => {
            console.error('Error saving message:', error);
            addMessage('Sorry, there was an error sending your message. Please try again.', 'system', 'System');
        });
    }
    
    // Clear search
    function clearSearch() {
        const searchInput = document.getElementById('userSearchInput');
        if (searchInput) {
            searchInput.value = '';
        }
        lastSearchTerm = '';
        clearSearchResults();
        showSearchSuggestions();
    }
    
    // Clear search results
    function clearSearchResults() {
        const resultsContainer = document.getElementById('searchResults');
        const noResults = document.getElementById('noResults');
        
        if (resultsContainer) {
            resultsContainer.innerHTML = '';
        }
        
        if (noResults) {
            noResults.classList.add('d-none');
        }
    }
    
    // Show search suggestions
    function showSearchSuggestions() {
        const suggestions = document.getElementById('searchSuggestions');
        if (suggestions) {
            suggestions.classList.remove('d-none');
        }
    }
    
    // Hide search suggestions
    function hideSearchSuggestions() {
        const suggestions = document.getElementById('searchSuggestions');
        if (suggestions) {
            suggestions.classList.add('d-none');
        }
    }
    
    // Show no results
    function showNoResults() {
        const noResults = document.getElementById('noResults');
        const resultsContainer = document.getElementById('searchResults');
        
        if (noResults) {
            noResults.classList.remove('d-none');
        }
        
        if (resultsContainer) {
            resultsContainer.innerHTML = '';
        }
    }
    
    // Search suggestion handler
    function searchSuggestion(term) {
        const searchInput = document.getElementById('userSearchInput');
        if (searchInput) {
            searchInput.value = term;
            performRealTimeSearch();
        }
    }
    
    // Enhanced search users function (replacing the old one)
    window.searchUsers = function() {
        performRealTimeSearch();
    };
    
    // Show/hide search loading
    function showSearchLoading(show) {
        const searchLoading = document.getElementById('searchLoading');
        if (searchLoading) {
            if (show) {
                searchLoading.classList.remove('d-none');
            } else {
                searchLoading.classList.add('d-none');
            }
        }
    }
    
    // Select specific user to chat with
    function selectUser(user, type) {
        currentChatUser = user;
        
        chatSelectionPanel.classList.add('d-none');
        chatInterface.classList.remove('d-none');
        faqQuickResponses.classList.add('d-none');
        
        document.getElementById('chatWithName').textContent = user.name;
        document.getElementById('chatStatus').textContent = user.status;
        
        // Load chat history for this user
        loadChatHistoryFromAPI(user.id);
    }
    
    // Load chat history from API
    function loadChatHistoryFromAPI(userId) {
        fetch(`/chat/messages?user_id=${userId}&limit=20`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data && Array.isArray(data)) {
                clearChatMessages();
                
                if (data.length > 0) {
                    // Display chat history
                    data.forEach(message => {
                        const isCurrentUser = message.sender_id === @json(auth()->id());
                        addMessage(message.content, isCurrentUser ? 'user' : 'other', 
                                 isCurrentUser ? message.sender.name : message.sender.name);
                    });
                } else {
                    // Show welcome message if no history
                    addMessage(`Hello! I'm ${currentChatUser.name}. How can I help you today?`, 'other', currentChatUser.name);
                }
            } else {
                console.error('Failed to load chat history:', data.error);
                clearChatMessages();
                addMessage(`Hello! I'm ${currentChatUser.name}. How can I help you today?`, 'other', currentChatUser.name);
            }
        })
        .catch(error => {
            console.error('Error loading chat history:', error);
            clearChatMessages();
            addMessage(`Hello! I'm ${currentChatUser.name}. How can I help you today?`, 'other', currentChatUser.name);
        });
    }
    
    // FAQ selection
    window.selectFAQ = function(faqType) {
        const faq = faqResponses[faqType];
        if (faq) {
            addMessage(faq.question, 'user');
            setTimeout(() => {
                addMessage(faq.answer, 'system', 'FAQ Bot');
            }, 500);
        }
    };
    
    // Back to selection
    window.backToSelection = function() {
        chatInterface.classList.add('d-none');
        chatSelectionPanel.classList.remove('d-none');
        faqQuickResponses.classList.add('d-none');
        
        currentChatType = null;
        currentChatUser = null;
    };
    
    // Go back to user type selection
    window.goBackToSelection = function() {
        const availableUsers = document.getElementById('availableUsers');
        const chatSelectionPanel = document.getElementById('chatSelectionPanel');
        
        // Hide search interface
        availableUsers.classList.add('d-none');
        
        // Show selection panel
        chatSelectionPanel.classList.remove('d-none');
        
        // Reset search
        const searchInput = document.getElementById('userSearchInput');
        if (searchInput) {
            searchInput.value = '';
        }
        
        // Clear search results
        const usersList = document.querySelector('.available-users-list');
        if (usersList) {
            usersList.innerHTML = '';
        }
        
        // Reset current chat type
        currentChatType = null;
    };

    // Clear chat history
    window.clearChatHistory = function() {
        if (confirm('Are you sure you want to clear the chat history?')) {
            clearChatMessages();
            if (currentChatUser) {
                delete chatHistory[currentChatUser.id];
            }
        }
    };
    
    // Create group chat (Admin/Director only)
    window.createGroupChat = function() {
        alert('Group chat functionality coming soon!');
    };
    
    // Add message to chat
    function addMessage(content, type = 'user', author = null) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${type}-message mb-3`;
        
        const timestamp = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        const currentUser = @json(auth()->user());
        let userName = 'Guest';
        let userInitial = 'G';
        
        if (currentUser) {
            userName = currentUser.name || 'User';
            userInitial = userName.charAt(0).toUpperCase();
        }
        
        let avatarClass = 'bg-primary';
        let displayName = userName;
        
        if (type === 'other') {
            avatarClass = 'bg-success';
            displayName = author || 'User';
        } else if (type === 'system') {
            avatarClass = 'bg-info';
            displayName = author || 'System';
        }
        
        messageDiv.innerHTML = `
            <div class="d-flex align-items-center mb-2">
                <div class="avatar ${avatarClass} text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                    ${type === 'system' ? '<i class="bi bi-robot"></i>' : (author ? author.charAt(0).toUpperCase() : userInitial)}
                </div>
                <small class="fw-medium">${displayName}</small>
                <small class="text-muted ms-auto">${timestamp}</small>
            </div>
            <div class="message-content ${type === 'user' ? 'bg-primary text-white' : type === 'other' ? 'bg-success text-white' : 'bg-light'} rounded-3 p-3 ms-5">
                <p class="mb-0" style="white-space: pre-line;">${content}</p>
            </div>
        `;
        
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
        
        // Mark as unseen if not from current user and chat is closed
        if (type !== 'user' && !chatOffcanvas.classList.contains('show')) {
            hasUnseenMessages = true;
            updateChatBadge();
        }
    }
    
    // Generate response based on chat type
    function generateResponse(message) {
        let response = '';
        
        if (currentChatType === 'faq') {
            response = generateFAQResponse(message);
            addMessage(response, 'system', 'FAQ Bot');
        } else if (currentChatType === 'support') {
            response = generateSupportResponse(message);
            addMessage(response, 'other', 'Support Team');
        } else if (currentChatUser) {
            response = generateUserResponse(message);
            addMessage(response, 'other', currentChatUser.name);
        }
        
        // Save response to history
        saveChatHistory(response, 'other');
    }
    
    // Generate FAQ response
    function generateFAQResponse(message) {
        const lowerMessage = message.toLowerCase();
        
        // Check for keywords and provide relevant responses
        if (lowerMessage.includes('enroll') || lowerMessage.includes('register') || lowerMessage.includes('course')) {
            return faqResponses.enrollment.answer;
        } else if (lowerMessage.includes('payment') || lowerMessage.includes('pay') || lowerMessage.includes('fee')) {
            return faqResponses.payment.answer;
        } else if (lowerMessage.includes('schedule') || lowerMessage.includes('time') || lowerMessage.includes('class')) {
            return faqResponses.schedule.answer;
        } else if (lowerMessage.includes('certificate') || lowerMessage.includes('diploma') || lowerMessage.includes('completion')) {
            return faqResponses.certificate.answer;
        } else if (lowerMessage.includes('support') || lowerMessage.includes('help') || lowerMessage.includes('contact')) {
            return faqResponses.support.answer;
        } else {
            return "I'm sorry, I don't have a specific answer for that question. Please choose from the FAQ options above or contact our support team for personalized assistance.";
        }
    }
    
    // Generate support response
    function generateSupportResponse(message) {
        const supportResponses = [
            "Thank you for contacting support! I've received your message and will help you resolve this issue.",
            "I understand your concern. Let me look into this for you right away.",
            "Thanks for reaching out! Could you provide more details about the issue you're experiencing?",
            "I'm here to help! Based on your message, I'll need to gather some additional information.",
            "Great question! Let me check our knowledge base for the most up-to-date information."
        ];
        
        return supportResponses[Math.floor(Math.random() * supportResponses.length)];
    }
    
    // Generate user response
    function generateUserResponse(message) {
        const userResponses = [
            "Thanks for your message! I'll get back to you soon.",
            "I received your message. Let me think about this and respond shortly.",
            "That's a good point! Let me consider this and reply.",
            "I appreciate you reaching out. I'll respond as soon as possible.",
            "Thanks for the message! I'll get back to you with more information."
        ];
        
        return userResponses[Math.floor(Math.random() * userResponses.length)];
    }
    
    // Save chat history
    function saveChatHistory(message, type) {
        const key = currentChatUser ? currentChatUser.id : currentChatType;
        
        if (!chatHistory[key]) {
            chatHistory[key] = [];
        }
        
        chatHistory[key].push({
            message: message,
            type: type,
            timestamp: new Date().toISOString()
        });
        
        // Save to localStorage
        localStorage.setItem('artc_chat_history', JSON.stringify(chatHistory));
    }
    
    // Load chat history
    function loadChatHistory() {
        const saved = localStorage.getItem('artc_chat_history');
        if (saved) {
            chatHistory = JSON.parse(saved);
        }
        
        const key = currentChatUser ? currentChatUser.id : currentChatType;
        const history = chatHistory[key];
        
        if (history && history.length > 0) {
            clearChatMessages();
            history.forEach(item => {
                const author = currentChatUser ? currentChatUser.name : (currentChatType === 'faq' ? 'FAQ Bot' : 'Support Team');
                addMessage(item.message, item.type, item.type === 'other' ? author : null);
            });
        }
    }
    
    // Clear chat messages
    function clearChatMessages() {
        chatMessages.innerHTML = '';
    }
    
    // Scroll to bottom
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Update chat badge
    function updateChatBadge() {
        if (chatTrigger) {
            const existingBadge = chatTrigger.querySelector('.chat-badge');
            
            if (hasUnseenMessages && !existingBadge) {
                const badge = document.createElement('span');
                badge.className = 'chat-badge';
                badge.textContent = '!';
                chatTrigger.appendChild(badge);
            } else if (!hasUnseenMessages && existingBadge) {
                existingBadge.remove();
            }
        }
    }
    
    // Handle typing indicator
    if (chatInput) {
        chatInput.addEventListener('input', function() {
            if (!isUserTyping) {
                isUserTyping = true;
                // Show typing indicator to others
            }
            
            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                isUserTyping = false;
            }, 1000);
        });
    }
});
</script>
