{{-- Enhanced Global Chat Component --}}
{{-- This component should be included in all layout files --}}

<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.x/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/chat/chat.css') }}">
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

search-loading {
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
            @php
                $role     = session('user_role', 'guest');
                $loggedIn = session('logged_in', false);
                $userName = session('user_name', 'Guest');
            @endphp

            @if($loggedIn)
                @if($role === 'admin' || $role === 'director')
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
                @elseif($role === 'professor')
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
                @elseif($role === 'student')
                    <div class="row g-2">
                        <div class="col-4">
                                <button type="button"
                                        class="btn btn-outline-success w-100 btn-sm user-type-btn"
                                        data-type="professor">
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
                @endif
            @else
                <div class="text-center">
                    <button class="btn btn-outline-warning user-type-btn" data-type="faq">
                        <i class="bi bi-question-circle me-1"></i>FAQ Bot
                    </button>
                </div>
            @endif
        </div>

        <!-- Group Chat Options (Admin/Director only) -->
        @if($loggedIn && ($role === 'admin' || $role === 'director'))
        <div class="group-chat-options mt-3">
            <button class="btn btn-outline-secondary btn-sm w-100" onclick="createGroupChat()">
                <i class="bi bi-people me-1"></i>Create Group Chat
            </button>
        </div>
        @endif
    </div>
    
    <!-- Enhanced Search Section (moved outside chatSelectionPanel) -->
    <div id="availableUsers" class="d-none p-3 border-bottom">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-person-lines-fill me-2"></i>Search Users
            </h6>
            <button class="btn btn-outline-secondary btn-sm" onclick="goBackToSelection()">
                <i class="bi bi-arrow-left me-1"></i>Back
            </button>
        </div>
        
        <!-- Role-specific Filter Options -->
        @php
            $role = session('user_role', 'guest');
        @endphp
        
        @if($role === 'student')
            <!-- Student can search professors with batch/program filters -->
            <div class="mb-3">
                <label class="form-label">Search in your enrolled programs:</label>
                <div class="row g-2">
                    <div class="col-md-6">
                        <select class="form-select form-select-sm" id="studentProgramFilter">
                            <option value="">All Programs</option>
                            <!-- Will be populated by JavaScript -->
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select class="form-select form-select-sm" id="studentBatchFilter">
                            <option value="">All Batches</option>
                            <!-- Will be populated by JavaScript -->
                        </select>
                    </div>
                </div>
            </div>
        @elseif($role === 'professor')
            <!-- Professor can search students with batch/program/learning mode filters -->
            <div class="mb-3">
                <label class="form-label">Search in your assigned programs:</label>
                <div class="row g-2">
                    <div class="col-md-4">
                        <select class="form-select form-select-sm" id="professorProgramFilter">
                            <option value="">All Programs</option>
                            <!-- Will be populated by JavaScript -->
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select form-select-sm" id="professorBatchFilter">
                            <option value="">All Batches</option>
                            <!-- Will be populated by JavaScript -->
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select form-select-sm" id="professorLearningModeFilter">
                            <option value="">All Modes</option>
                            <option value="synchronous">Synchronous</option>
                            <option value="asynchronous">Asynchronous</option>
                        </select>
                    </div>
                </div>
            </div>
        @elseif($role === 'admin' || $role === 'director')
            <!-- Admin can search students with program/learning mode filters -->
            <div class="mb-3">
                <label class="form-label">Filter by program and learning mode:</label>
                <div class="row g-2">
                    <div class="col-md-4">
                        <select class="form-select form-select-sm" id="adminProgramFilter">
                            <option value="">All Programs</option>
                            <!-- Will be populated by JavaScript -->
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select form-select-sm" id="adminLearningModeFilter">
                            <option value="">All Modes</option>
                            <option value="synchronous">Synchronous</option>
                            <option value="asynchronous">Asynchronous</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select form-select-sm" id="adminBatchFilter" style="display: none;">
                            <option value="">All Batches</option>
                            <!-- Will be populated by JavaScript when synchronous is selected -->
                        </select>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Enhanced Search Input with Real-Time API -->
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
                    onkeyup="performRealTimeSearch()"
                    autocomplete="off"
                >
                <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                @if($role !== 'guest')
                    @if($role === 'admin' || $role === 'director')
                        Search students, professors, or admins
                    @elseif($role === 'professor')
                        Search students and admins
                    @elseif($role === 'student')
                        Search professors and admins
                    @endif
                @else
                    Please log in to search users
                @endif
            </small>
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
            
            <!-- Available Users List (for JS population) -->
            <div class="available-users-list"></div>
            
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
                    @php
                        $role = session('role', 'guest');
                        $userName = session('user_name', 'Guest');
                    @endphp
                    @if($role !== 'guest')
                        Logged in as {{ $userName }}
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


<!-- Enhanced Chat JavaScript -->
<script>


function updateChatBadge() {
    // no-op for now (implements unread‐count badge later)
  }

  function loadFilterOptions() {
    // no-op for now (implements program/batch filter later)
  }

  // ─────── NAVIGATION HELPERS ───────
        function goBackToSelection() {
        document.getElementById('availableUsers').classList.add('d-none');
        document.getElementById('chatSelectionPanel').classList.remove('d-none');
        }
        function backToSelection() {
        document.getElementById('chatInterface').classList.add('d-none');
        document.getElementById('chatSelectionPanel').classList.remove('d-none');
        }


  // ─────── REAL-TIME SEARCH ───────
  let currentChatType = null;

function performRealTimeSearch() {
  const q = document.getElementById('userSearchInput').value.trim();
  const container = document.getElementById('searchResults');
  
  if (!q) {
    container.innerHTML = '';
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
  
  // Search all types by default (professors, admins, directors)
  fetch(`/api/chat/session/users?` + new URLSearchParams({
    type: 'all',
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
    console.log('Search response:', json); // Debug log
    // Ensure data exists and is an array
    const users = Array.isArray(json.data) ? json.data : [];
    if (!users.length) {
      container.innerHTML = `<p class="text-center text-muted">No users found for "${q}".</p>`;
    } else {
      container.innerHTML = users.map(u => `
        <div class="user-item p-2 border-bottom" onclick="selectUserForChat(${u.id},'${u.name}','${u.role}')">
          <div class="d-flex align-items-center">
            <div class="avatar-circle me-2">
              <i class="fas fa-user"></i>
            </div>
            <div>
              <div class="fw-bold">${u.name}</div>
              <div class="text-muted small">${u.email}</div>
              <div class="text-muted small text-capitalize">${u.role}</div>
            </div>
          </div>
        </div>
      `).join('');
    }
  })
  .catch(err => {
    console.error('Search failed:', err);
    container.innerHTML = `<p class="text-center text-danger">Error searching users. Please try again.</p>`;
  });
}


  // ─────── BOOTSTRAP ON LOAD ───────
document.addEventListener('DOMContentLoaded', () => {
  // … stubs …
  updateChatBadge();
  loadFilterOptions();

  // wire up your .user-type-btn to call selectUserType
  document.querySelectorAll('.user-type-btn').forEach(btn =>
    btn.addEventListener('click', () => {
      currentChatType = btn.dataset.type;
      selectUserType(currentChatType);
    })
  );

  // wire up the search box
  const searchInput = document.getElementById('userSearchInput');
  if (searchInput) searchInput.addEventListener('keyup', performRealTimeSearch);

  // wire up “Back” buttons
  document.querySelectorAll('[onclick="goBackToSelection()"], [onclick="backToSelection()"]')
    .forEach(b => b.addEventListener('click', e => e.preventDefault()));

  // **then** all of your existing chat-form, FAQ, sendMessage, loadMessages, etc.
});



document.addEventListener('DOMContentLoaded', function() {
    // Session-based variables
    const myId            = @json(session('user_id'));
    const myName          = @json(session('user_name','Guest'));
    const isAuthenticated = @json(session('logged_in', false));
    const userRole        = @json(session('user_role','guest'));

    const chatTrigger = document.querySelector('.chat-trigger');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');
    const typingIndicator = document.getElementById('typingIndicator');
    const chatOffcanvas = document.getElementById('chatOffcanvas');
    const chatSelectionPanel = document.getElementById('chatSelectionPanel');
    const availableUsers = document.getElementById('availableUsers');
    const chatInterface = document.getElementById('chatInterface');
    const faqQuickResponses = document.getElementById('faqQuickResponses');
    
    let hasUnseenMessages = false;
    let typingTimeout;
    let isUserTyping = false;
    let currentChatType = null;
    let currentChatUser = null;
    let chatHistory = {};
    let currentFilters = {
        program: '',
        batch: '',
        learningMode: ''
    };
    
    // Initialize chat
    updateChatBadge();
    loadFilterOptions();
    
    // Debug information
    console.log('Chat Debug Info:', {
        myId: myId,
        myName: myName,
        isAuthenticated: isAuthenticated,
        userRole: userRole,
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    });
    
    // Define selectUserType function
    window.selectUserType = function(type) {
        currentChatType = type;
        chatSelectionPanel.classList.add('d-none');
        availableUsers.classList.remove('d-none');
        
        // Set appropriate placeholders and load users
        const searchInput = document.getElementById('userSearchInput');
        if (searchInput) {
            searchInput.placeholder = `Search ${type}s...`;
            searchInput.value = '';
        }
        
        // Load users based on type
        loadUsersByType(type);
        
        // Show/hide role-specific filters
        updateFilterVisibility(type);
    };
    
    // Add event listeners for user type buttons
    document.querySelectorAll('.user-type-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            window.selectUserType(btn.dataset.type);
        });
    });
    
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
        chatForm.addEventListener('submit', e => {
            e.preventDefault();
            const msg = chatInput.value.trim();
            if (!msg || (!currentChatType && !currentChatUser)) return;

            addMessage(msg, 'user');        // show immediately
            chatInput.value = '';

            // Enhanced error handling with more detailed logging
            if (currentChatType === 'faq') {
                saveChatHistory(msg, 'user');
                setTimeout(() => generateResponse(msg), randomDelay());
            } else if (currentChatUser) {
                console.log('Sending message to user:', currentChatUser);
                sendMessageToUser(currentChatUser.id, msg);
            } else {
                console.log('No chat user selected, treating as general message');
                saveChatHistory(msg, 'user');
                setTimeout(() => generateResponse(msg), randomDelay());
            }
        });
    }
    
    // Generate random delay for more natural responses
    function randomDelay() {
        return Math.random() * 2000 + 1000; // 1-3 seconds
    }
    
    // Send message to user via API (updated to use enhanced system)
    function sendMessageToUser(userId, message) {
        fetch('/api/chat/session/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                receiver_id: userId,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.id) {
                console.log('Message sent successfully');
                saveChatHistory(message, 'user');
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
    document.querySelectorAll('.user-type-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    selectUserType(btn.dataset.type);
  });
});
    
    // Show FAQ interface
    function showFAQInterface() {
        chatSelectionPanel.classList.add('d-none');
        availableUsers.classList.add('d-none');
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
        const usersList = availableUsers.querySelector('.available-users-list');
        
        // Clear previous results
        if (usersList) {
            usersList.innerHTML = '';
        }
        
        // Hide selection panel and show search interface
        chatSelectionPanel.classList.add('d-none');
        availableUsers.classList.remove('d-none');
        chatInterface.classList.add('d-none');
        
        // Store current chat type for search
        currentChatType = type;
        
        // Update the search placeholder based on type
        const searchInput = document.getElementById('userSearchInput');
        if (searchInput) {
            searchInput.placeholder = `Search for ${type}s...`;
            searchInput.focus();
        }
        
        // Show instructions
        const searchResults = document.getElementById('searchResults');
        if (searchResults) {
            searchResults.innerHTML = `
                <div class="text-center text-muted p-3">
                    <i class="fas fa-search fa-2x mb-2"></i>
                    <p>Start typing to search for ${type}s...</p>
                </div>
            `;
        }
    }
    

    
    // Enhanced filter functionality with API integration
    function loadFilterOptions() {
        // Load programs based on user role
        if (userRole === 'student') {
            loadEnrolledPrograms();
        } else if (userRole === 'professor') {
            loadAssignedPrograms();
        } else if (userRole === 'admin' || userRole === 'director') {
            loadAllPrograms();
        }
    }

    // Load enrolled programs for students
    function loadEnrolledPrograms() {
        fetch('/api/student/enrolled-programs', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            populateProgramSelect('studentProgramFilter', data.programs);
        })
        .catch(error => console.error('Error loading enrolled programs:', error));
    }

    // Load assigned programs for professors
    function loadAssignedPrograms() {
        fetch('/api/professor/assigned-programs', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.programs && Array.isArray(data.programs)) {
                populateProgramSelect('professorProgramFilter', data.programs);
            } else {
                console.error('Invalid programs response:', data);
                populateProgramSelect('professorProgramFilter', []);
            }
        })
        .catch(error => {
            console.error('Error loading assigned programs:', error);
            populateProgramSelect('professorProgramFilter', []);
        });
    }

    // Load all programs for admin/director
    function loadAllPrograms() {
        fetch('/api/chat/session/programs', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            populateProgramSelect('adminProgramFilter', data.data);
        })
        .catch(error => console.error('Error loading all programs:', error));
    }

    // Populate program select dropdown
    function populateProgramSelect(selectId, programs) {
        const selectElement = document.getElementById(selectId);
        if (!selectElement) return;

        selectElement.innerHTML = '<option value="">All Programs</option>';
        programs.forEach(program => {
            const option = document.createElement('option');
            option.value = program.id;
            option.textContent = program.program_name;
            selectElement.appendChild(option);
        });
    }

    // Handle program filter change
    function handleProgramFilterChange(selectElement) {
        const programId = selectElement.value;
        const userType = getCurrentUserType();
        
        // Update batch filter if learning mode is synchronous
        if (userType === 'admin' || userType === 'director') {
            const learningModeSelect = document.getElementById('adminLearningModeFilter');
            if (learningModeSelect && learningModeSelect.value === 'synchronous') {
                loadBatchesForProgram(programId);
            }
        } else {
            // For students and professors, always load batches
            loadBatchesForProgram(programId);
        }
        
        // Refresh user list
        refreshUserList();
    }

    // Load batches for a specific program
    function loadBatchesForProgram(programId) {
        if (!programId) {
            clearBatchFilters();
            return;
        }

        fetch('/api/chat/session/batches?' + new URLSearchParams({
            program: programId
        }), {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            populateBatchSelects(data.data);
        })
        .catch(error => {
            console.error('Error loading batches:', error);
            clearBatchFilters();
        });
    }

    // Populate batch select dropdowns
    function populateBatchSelects(batches) {
        const batchSelects = [
            'studentBatchFilter',
            'professorBatchFilter',
            'adminBatchFilter'
        ];

        batchSelects.forEach(selectId => {
            const selectElement = document.getElementById(selectId);
            if (selectElement) {
                selectElement.innerHTML = '<option value="">All Batches</option>';
                batches.forEach(batch => {
                    const option = document.createElement('option');
                    option.value = batch.id;
                    option.textContent = batch.batch_name;
                    selectElement.appendChild(option);
                });
            }
        });
    }

    // Clear batch filters
    function clearBatchFilters() {
        const batchSelects = [
            'studentBatchFilter',
            'professorBatchFilter',
            'adminBatchFilter'
        ];

        batchSelects.forEach(selectId => {
            const selectElement = document.getElementById(selectId);
            if (selectElement) {
                selectElement.innerHTML = '<option value="">All Batches</option>';
            }
        });
    }

    // Handle learning mode filter change
    function handleLearningModeChange(selectElement) {
        const learningMode = selectElement.value;
        const batchFilter = document.getElementById('adminBatchFilter');
        
        if (learningMode === 'synchronous') {
            // Show batch filter and load batches
            if (batchFilter) {
                batchFilter.style.display = 'block';
                batchFilter.closest('.col-md-4').style.display = 'block';
                
                // Load batches for current program
                const programSelect = document.getElementById('adminProgramFilter');
                if (programSelect && programSelect.value) {
                    loadBatchesForProgram(programSelect.value);
                }
            }
        } else {
            // Hide batch filter
            if (batchFilter) {
                batchFilter.style.display = 'none';
                batchFilter.closest('.col-md-4').style.display = 'none';
                batchFilter.value = '';
            }
        }
        
        // Refresh user list
        refreshUserList();
    }

    // Get current user type from chat context
    function getCurrentUserType() {
        return currentChatType || 'student';
    }

    // Refresh user list based on current filters
    function refreshUserList() {
        const userType = getCurrentUserType();
        const filters = getCurrentFilters();
        
        fetch('/api/chat/session/users?' + new URLSearchParams({
            type: userType,
            program: filters.program,
            mode: filters.mode,
            batch: filters.batch
        }), {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            displayFilteredUsers(data.data);
        })
        .catch(error => {
            console.error('Error loading filtered users:', error);
        });
    }

    // Get current filter values
    function getCurrentFilters() {
        const userType = getCurrentUserType();
        let programSelect, batchSelect, modeSelect;
        
        if (userType === 'student') {
            programSelect = document.getElementById('studentProgramFilter');
            batchSelect = document.getElementById('studentBatchFilter');
        } else if (userType === 'professor') {
            programSelect = document.getElementById('professorProgramFilter');
            batchSelect = document.getElementById('professorBatchFilter');
            modeSelect = document.getElementById('professorLearningModeFilter');
        } else if (userType === 'admin' || userType === 'director') {
            programSelect = document.getElementById('adminProgramFilter');
            batchSelect = document.getElementById('adminBatchFilter');
            modeSelect = document.getElementById('adminLearningModeFilter');
        }
        
        return {
            program: programSelect ? programSelect.value : '',
            batch: batchSelect ? batchSelect.value : '',
            mode: modeSelect ? modeSelect.value : ''
        };
    }

    // Display filtered users
    function displayFilteredUsers(users) {
        const resultsContainer = document.getElementById('searchResults');
        if (!resultsContainer) return;

        if (users.length === 0) {
            resultsContainer.innerHTML = `
                <div class="text-center py-4">
                    <i class="bi bi-person-x text-muted fs-3"></i>
                    <p class="text-muted mt-2">No users found with current filters</p>
                </div>
            `;
            return;
        }

        const usersHtml = users.map(user => `
            <div class="user-item d-flex align-items-center p-3 border-bottom cursor-pointer" 
                 onclick="selectUserForChat(${user.id}, '${user.name}', '${user.role}')">
                <div class="user-avatar me-3">
                    <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                         style="width: 40px; height: 40px; border-radius: 50%;">
                        ${user.name.charAt(0).toUpperCase()}
                    </div>
                </div>
                <div class="user-info flex-grow-1">
                    <div class="user-name fw-semibold">${user.name}</div>
                    <div class="user-details small text-muted">
                        ${user.email} • ${user.role}
                        ${user.is_online ? '<span class="badge bg-success ms-2">Online</span>' : ''}
                    </div>
                </div>
                <div class="user-actions">
                    <i class="bi bi-chat-dots text-primary"></i>
                </div>
            </div>
        `).join('');

        resultsContainer.innerHTML = usersHtml;
    }

    // Load messages for a specific user
    function loadMessagesForUser(userId) {
        fetch('/api/chat/session/messages?' + new URLSearchParams({
            with: userId
        }), {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && Array.isArray(data.data)) {
                displayChatMessages(data.data);
            } else {
                console.error('Invalid response format:', data);
                displayChatMessages([]);
            }
        })
        .catch(error => {
            console.error('Error loading messages:', error);
            addMessage('Error loading messages. Please try again.', 'system', 'System');
        });
    }

    // Display chat messages
    function displayChatMessages(messages) {
        const messagesContainer = document.getElementById('chatMessages');
        if (!messagesContainer) return;

        messagesContainer.innerHTML = '';
        
        // Ensure messages is an array
        if (Array.isArray(messages)) {
            messages.forEach(message => {
                const isMyMessage = message.sender_id == myId;
                const messageClass = isMyMessage ? 'user' : 'bot';
                const senderName = isMyMessage ? 'You' : (message.sender ? message.sender.name : 'Unknown');
                
                addMessage(message.content || message.message, messageClass, senderName);
            });
        } else {
            console.error('Messages is not an array:', messages);
        }
        
        // Scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Add message to chat
    function addMessage(message, type, sender = null) {
        const messagesContainer = document.getElementById('chatMessages');
        if (!messagesContainer) return;

        const messageElement = document.createElement('div');
        messageElement.className = `message ${type}`;
        
        const timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        let messageHTML = '';
        
        if (type === 'user') {
            messageHTML = `
                <div class="message-content user-message">
                    <div class="message-text">${message}</div>
                    <div class="message-time">${timestamp}</div>
                </div>
            `;
        } else if (type === 'bot' || type === 'system') {
            const senderName = sender || 'System';
            messageHTML = `
                <div class="message-content bot-message">
                    <div class="message-sender">${senderName}</div>
                    <div class="message-text">${message}</div>
                    <div class="message-time">${timestamp}</div>
                </div>
            `;
        } else {
            // Other message types (like received messages)
            const senderName = sender || 'Unknown';
            messageHTML = `
                <div class="message-content other-message">
                    <div class="message-sender">${senderName}</div>
                    <div class="message-text">${message}</div>
                    <div class="message-time">${timestamp}</div>
                </div>
            `;
        }
        
        messageElement.innerHTML = messageHTML;
        messagesContainer.appendChild(messageElement);
        
        // Auto-scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    // Enhanced selectUserForChat function
    window.selectUserForChat = function(userId, userName, userRole) {
        currentChatUser = { id: userId, name: userName, role: userRole };
        
        // Update chat header
        document.getElementById('chatWithName').textContent = userName;
        document.getElementById('chatStatus').textContent = userRole === 'admin' || userRole === 'director' ? 'Admin Online' : 'Online';
        
        // Clear previous messages
        clearChatMessages();
        
        // Load chat history with the selected user
        loadMessagesForUser(userId);
        
        // Show chat interface
        chatSelectionPanel.classList.add('d-none');
        availableUsers.classList.add('d-none');
        chatInterface.classList.remove('d-none');
        
        // Focus on chat input
        chatInput.focus();
    };
    
    // Clear chat messages
    function clearChatMessages() {
        const messagesContainer = document.getElementById('chatMessages');
        if (messagesContainer) {
            messagesContainer.innerHTML = '';
        }
    }

    // Clear chat history (for admin/director)
    window.clearChatHistory = function() {
        if (confirm('Are you sure you want to clear the chat history with this user?')) {
            fetch('/api/chat/session/clear-history', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    with: currentChatUser.id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    clearChatMessages();
                    addMessage('Chat history cleared.', 'system', 'System');
                } else {
                    addMessage('Failed to clear chat history. Please try again.', 'system', 'System');
                }
            })
            .catch(error => {
                console.error('Error clearing chat history:', error);
                addMessage('Error clearing chat history. Please try again.', 'system', 'System');
            });
        }
    };

    // Load initial chat history (if any)
    function loadChatHistory() {
        if (currentChatUser) {
            loadMessagesForUser(currentChatUser.id);
        }
    }

    // Save chat history to local storage
    function saveChatHistory(message, type) {
        const historyKey = currentChatUser ? `chat_${currentChatUser.id}` : 'chat_faq';
        let history = JSON.parse(localStorage.getItem(historyKey) || '[]');
        
        history.push({
            message: message,
            type: type,
            timestamp: new Date().toISOString(),
            sender: type === 'user' ? myName : (currentChatUser ? currentChatUser.name : 'FAQ Bot')
        });
        
        // Keep only last 50 messages
        if (history.length > 50) {
            history = history.slice(-50);
        }
        
        localStorage.setItem(historyKey, JSON.stringify(history));
    }
    
    // Generate response for FAQ or general chat
    function generateResponse(message) {
        if (currentChatType === 'faq') {
            generateFAQResponse(message);
        } else {
            // For other types, just acknowledge
            addMessage("Message received. This is a demo response.", 'system', 'System');
        }
    }
    
    // Generate FAQ response
    function generateFAQResponse(message) {
        const lowerMessage = message.toLowerCase();
        let response = null;
        
        // Check for keywords in the message
        if (lowerMessage.includes('enroll') || lowerMessage.includes('registration') || lowerMessage.includes('sign up')) {
            response = faqResponses.enrollment;
        } else if (lowerMessage.includes('payment') || lowerMessage.includes('pay') || lowerMessage.includes('cost') || lowerMessage.includes('price')) {
            response = faqResponses.payment;
        } else if (lowerMessage.includes('schedule') || lowerMessage.includes('time') || lowerMessage.includes('calendar')) {
            response = faqResponses.schedule;
        } else if (lowerMessage.includes('certificate') || lowerMessage.includes('certification') || lowerMessage.includes('diploma')) {
            response = faqResponses.certificate;
        } else if (lowerMessage.includes('support') || lowerMessage.includes('help') || lowerMessage.includes('contact')) {
            response = faqResponses.support;
        } else {
            // Default response
            response = {
                question: "General Information",
                answer: "I understand you're asking about: \"" + message + "\"\n\nFor specific questions, please try:\n• Enrollment questions\n• Payment information\n• Schedule inquiries\n• Certificate requirements\n• Support contact\n\nOr contact our support team directly at support@artc.edu"
            };
        }
        
        addMessage(response.answer, 'system', 'FAQ Bot');
        saveChatHistory(response.answer, 'bot');
    }
    
    // Select FAQ from quick responses
    window.selectFAQ = function(type) {
        if (faqResponses[type]) {
            const faq = faqResponses[type];
            addMessage(faq.question, 'user', 'You');
            saveChatHistory(faq.question, 'user');
            
            setTimeout(() => {
                addMessage(faq.answer, 'system', 'FAQ Bot');
                saveChatHistory(faq.answer, 'bot');
            }, 500);
        }
    };
    
    // Load chat history
    function loadChatHistory() {
        const historyKey = currentChatUser ? `chat_${currentChatUser.id}` : 'chat_faq';
        const history = JSON.parse(localStorage.getItem(historyKey) || '[]');
        
        // Clear current messages
        const messagesContainer = document.getElementById('chatMessages');
        if (messagesContainer) {
            messagesContainer.innerHTML = '';
        }
        
        // Load history
        history.forEach(item => {
            const messageClass = item.type === 'user' ? 'user' : (item.type === 'bot' ? 'bot' : 'system');
            addMessage(item.message, messageClass, item.sender);
        });
    }
    
    // Clear search input and results
    window.clearSearch = function() {
        const searchInput = document.getElementById('userSearchInput');
        if (searchInput) {
            searchInput.value = '';
            performRealTimeSearch();
        }
    };

    // Search suggestion handler
    window.searchSuggestion = function(type) {
        const searchInput = document.getElementById('userSearchInput');
        if (searchInput) {
            searchInput.value = type;
            performRealTimeSearch();
        }
    };

    // Load users by type
    function loadUsersByType(type) {
        // Clear previous results
        const resultsContainer = document.getElementById('searchResults');
        if (resultsContainer) {
            resultsContainer.innerHTML = '';
        }
        
        // Perform search based on type
        performRealTimeSearch();
    }
    
    // Update filter visibility based on user type
    function updateFilterVisibility(type) {
        // Hide all filter sections first
        const studentFilters = document.querySelectorAll('[id*="student"]');
        const professorFilters = document.querySelectorAll('[id*="professor"]');
        const adminFilters = document.querySelectorAll('[id*="admin"]');
        
        studentFilters.forEach(filter => {
            if (filter.closest('.mb-3')) {
                filter.closest('.mb-3').style.display = 'none';
            }
        });
        
        professorFilters.forEach(filter => {
            if (filter.closest('.mb-3')) {
                filter.closest('.mb-3').style.display = 'none';
            }
        });
        
        adminFilters.forEach(filter => {
            if (filter.closest('.mb-3')) {
                filter.closest('.mb-3').style.display = 'none';
            }
        });
        
        // Show relevant filters based on current user role and search type
        if (userRole === 'student') {
            const studentFilterContainer = document.querySelector('[id*="studentProgramFilter"]');
            if (studentFilterContainer && studentFilterContainer.closest('.mb-3')) {
                studentFilterContainer.closest('.mb-3').style.display = 'block';
            }
        } else if (userRole === 'professor') {
            const professorFilterContainer = document.querySelector('[id*="professorProgramFilter"]');
            if (professorFilterContainer && professorFilterContainer.closest('.mb-3')) {
                professorFilterContainer.closest('.mb-3').style.display = 'block';
            }
        } else if (userRole === 'admin' || userRole === 'director') {
            const adminFilterContainer = document.querySelector('[id*="adminProgramFilter"]');
            if (adminFilterContainer && adminFilterContainer.closest('.mb-3')) {
                adminFilterContainer.closest('.mb-3').style.display = 'block';
            }
        }
    }
    
    // Add event listeners for user type buttons
    document.querySelectorAll('.user-type-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            window.selectUserType(btn.dataset.type);
        });
    });
    
    // Enhanced search users function (make it globally accessible)
    window.performRealTimeSearch = performRealTimeSearch;
    window.searchUsers = performRealTimeSearch;
    window.clearSearch = clearSearch;
    window.searchSuggestion = searchSuggestion;
}); 
</script>