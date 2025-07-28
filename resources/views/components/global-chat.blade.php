{{-- Enhanced Chat Component with Real-Time Features --}}
@php 
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
@endphp

{{-- Chat CSS --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.x/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="{{ asset('css/chat/chat.css') }}" rel="stylesheet">
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

.message.bot {
    background: #fffbe6;
    color: #856404;
    border-left: 4px solid #ffd700;
    box-shadow: 0 2px 8px rgba(255, 215, 0, 0.08);
    position: relative;
    padding-left: 2.5rem;
}
.message.bot .message-sender::before {
    content: '\f059'; /* FontAwesome info-circle or use a question icon */
    font-family: 'Font Awesome 5 Free', 'FontAwesome', Arial, sans-serif;
    font-weight: 900;
    color: #ffd700;
    margin-right: 0.5rem;
    position: absolute;
    left: 1rem;
    top: 0.7rem;
    font-size: 1.2em;
}


/* Enhanced Chat Styling */
.message {
    margin-bottom: 12px;
    padding: 10px 15px;
    border-radius: 18px;
    max-width: 85%;
    word-wrap: break-word;
    position: relative;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.message:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.message.user {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    margin-left: auto;
    text-align: right;
    border-bottom-right-radius: 6px;
}

.message.user-message {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    margin-left: auto;
    text-align: right;
    border-bottom-right-radius: 6px;
}

.message.other-message {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: #333;
    margin-right: auto;
    text-align: left;
    border-bottom-left-radius: 6px;
    border: 1px solid #dee2e6;
}

.message.bot,
.message.system {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    color: #856404;
    margin-right: auto;
    text-align: left;
    border-left: 4px solid #ffc107;
    box-shadow: 0 2px 8px rgba(255, 193, 7, 0.2);
    border-bottom-left-radius: 6px;
}

.message-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.message-sender {
    font-weight: 600;
    font-size: 0.85em;
    opacity: 0.9;
    text-transform: capitalize;
}

.message-text {
    line-height: 1.5;
    font-size: 0.95em;
    word-break: break-word;
}

.message-time {
    font-size: 0.75em;
    opacity: 0.75;
    font-weight: 500;
    margin-top: 2px;
}

.user-message .message-sender,
.user-message .message-time {
    color: rgba(255, 255, 255, 0.9);
}

.user-message .message-text {
    color: white;
}

#chatMessages {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 15px;
    min-height: 300px;
    max-height: 400px;
    overflow-y: auto;
    scroll-behavior: smooth;
}

#chatMessages::-webkit-scrollbar {
    width: 6px;
}

#chatMessages::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#chatMessages::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#chatMessages::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Search Results Styling */
.user-item {
    transition: all 0.2s ease;
    cursor: pointer;
    border-radius: 10px;
    margin-bottom: 4px;
    border: 1px solid transparent;
}

.user-item:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transform: translateX(5px);
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    border-color: #dee2e6;
}

.avatar-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.avatar-circle.bg-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
}

.avatar-circle.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
}

.avatar-circle.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
    color: #212529;
}

.avatar-circle.bg-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    box-shadow: 0 2px 8px rgba(23, 162, 184, 0.3);
}

.avatar-circle.bg-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #545b62 100%);
    box-shadow: 0 2px 8px rgba(108, 117, 125, 0.3);
}

.chat-loading {
    text-align: center;
    padding: 30px;
    color: #6c757d;
}

.search-loading {
    text-align: center;
    padding: 25px;
    color: #6c757d;
}

.search-loading .spinner-border {
    width: 1.2rem;
    height: 1.2rem;
}

/* Chat Input Styling */
#chatInput {
    border-radius: 25px;
    padding: 12px 20px;
    border: 1px solid #dee2e6;
    font-size: 0.95em;
}

#chatInput:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    border-color: #007bff;
}

#chatForm button {
    border-radius: 50%;
    width: 45px;
    height: 45px;
    border: none;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
    transition: all 0.2s ease;
}

#chatForm button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
}

/* Status Badges */
.badge.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    box-shadow: 0 1px 3px rgba(40, 167, 69, 0.3);
}

.badge.bg-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
}

/* Typing Indicator */
.typing-indicator {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    background: #f8f9fa;
    border-radius: 18px;
    margin-bottom: 10px;
    margin-right: auto;
    max-width: 80px;
}

.typing-indicator span {
    height: 6px;
    width: 6px;
    background: #999;
    border-radius: 50%;
    display: inline-block;
    margin: 0 1px;
    animation: typing 1.4s infinite;
}

.typing-indicator span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-indicator span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: scale(1);
        opacity: 0.5;
    }
    30% {
        transform: scale(1.2);
        opacity: 1;
    }
}

</style>
@endpush

{{-- Chat JavaScript --}}
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
    document.getElementById('chatSelectionPanel').classList.add('d-none');
    
    // Update chat header with user info
    document.getElementById('chatWithName').textContent = userName;
    document.getElementById('chatStatus').textContent = 'Online';
    
    // Update avatar in chat header
    const chatAvatar = document.querySelector('.chat-avatar');
    const initials = userName.split(' ').map(word => word.charAt(0)).join('').substring(0, 2).toUpperCase();
    chatAvatar.innerHTML = `
      <div class="avatar-circle">
        ${initials}
      </div>
    `;
    
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
      return c.innerHTML = '<div class="text-center text-muted py-3">No messages yet. Start the conversation!</div>';
    }
    c.innerHTML = msgs.map(m => {
      const isMe = m.sender_id === myId;
      const cls = isMe ? 'user-message' : 'other-message';
      const name = isMe ? 'You' : (m.sender?.name || 'Unknown');
      const tm = new Date(m.sent_at).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
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
      
      // Add message to UI immediately for better UX
      const tempMessage = {
        sender_id: myId,
        message: txt,
        sent_at: new Date(),
        sender: { name: 'You' }
      };
      addMessageToChat(tempMessage);
      document.getElementById('chatInput').value = '';
      
      // Send message to server
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
        if (!d.success) {
          console.error('Failed to send message:', d);
          // Optionally show error message to user
        }
      })
      .catch(console.error);
    }
  });

  // Helper function to add a single message to chat
  function addMessageToChat(message) {
    const c = document.getElementById('chatMessages');
    const isMe = message.sender_id === myId;
    const cls = isMe ? 'user-message' : 'other-message';
    const name = isMe ? 'You' : (message.sender?.name || 'Unknown');
    const tm = new Date(message.sent_at).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
    
    const messageHtml = `
      <div class="message ${cls}">
        <div class="message-content">
          <div class="message-sender">${name}</div>
          <div class="message-text">${message.message}</div>
          <div class="message-time">${tm}</div>
        </div>
      </div>
    `;
    
    c.insertAdjacentHTML('beforeend', messageHtml);
    c.scrollTop = c.scrollHeight;
  }

  // USER‐TYPE BUTTONS
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.user-type-btn');
    if (btn) {
      currentChatType = btn.dataset.type;
      document.getElementById('chatSelectionPanel').classList.add('d-none');
      if (currentChatType === 'faq') {
        document.getElementById('availableUsers').classList.add('d-none');
        document.getElementById('chatInterface').classList.remove('d-none');
        document.getElementById('faqQuickResponses').classList.remove('d-none');
        // Optionally hide chat messages area if you want only FAQ visible:
        // document.getElementById('chatMessages').classList.add('d-none');
      } else {
        document.getElementById('faqQuickResponses').classList.add('d-none');
        // Show search interface instead of loading users by type
        showSearchInterface();
      }
    }
  });

  // ─────── SEARCH FUNCTIONALITY ───────
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('userSearchInput');
    if (searchInput) {
      let searchTimeout;
      
      searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
          document.getElementById('searchResults').innerHTML = 
            '<div class="text-center text-muted py-3">Type at least 2 characters to search...</div>';
          return;
        }
        
        // Show loading state
        document.getElementById('searchResults').innerHTML = 
          '<div class="search-loading"><div class="spinner-border spinner-border-sm me-2"></div>Searching...</div>';
        
        // Debounce search
        searchTimeout = setTimeout(() => {
          performSearch(query);
        }, 500);
      });
      
      searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          const query = this.value.trim();
          if (query.length >= 2) {
            performSearch(query);
          }
        }
      });
    }
  });

  function performSearch(query) {
    fetch('/api/chat/session/search', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'include',
      body: JSON.stringify({ query: query })
    })
    .then(response => {
      if (!response.ok) {
        if (response.status === 401) {
          throw new Error('Please log in to search for users');
        }
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      if (data.success && data.data && data.data.length > 0) {
        displaySearchResults(data.data);
      } else {
        document.getElementById('searchResults').innerHTML = 
          '<div class="text-center text-muted py-3">No users found for "' + query + '"</div>';
      }
    })
    .catch(error => {
      console.error('Search error:', error);
      let errorMessage = 'Search failed. Please try again.';
      if (error.message.includes('log in')) {
        errorMessage = 'Please log in to search for users.';
      }
      document.getElementById('searchResults').innerHTML = 
        '<div class="text-center text-danger py-3">' + errorMessage + '</div>';
    });
  }

  function displaySearchResults(users) {
    const container = document.getElementById('searchResults');
    if (!users || users.length === 0) {
      container.innerHTML = '<div class="text-center text-muted py-3">No users found.</div>';
      return;
    }
    
    container.innerHTML = users.map(user => {
      const initials = user.name.split(' ').map(word => word.charAt(0)).join('').substring(0, 2).toUpperCase();
      const roleColor = {
        'student': 'primary',
        'professor': 'success', 
        'admin': 'warning',
        'director': 'info'
      }[user.role] || 'secondary';
      
      return `
        <div class="user-item p-3 border-bottom" onclick="selectUserForChat(${user.id},'${user.name}','${user.role}')">
          <div class="d-flex align-items-center">
            <div class="avatar-circle me-3 bg-${roleColor}">
              ${initials}
            </div>
            <div class="flex-grow-1">
              <div class="fw-bold text-dark">${user.name}</div>
              <div class="text-muted small">${user.email}</div>
              <div class="text-muted small d-flex align-items-center">
                <i class="bi bi-person-badge me-1"></i>
                <span class="text-capitalize">${user.role}</span>
              </div>
            </div>
            <div class="d-flex flex-column align-items-end">
              <span class="badge bg-${user.status === 'online' ? 'success' : 'secondary'} mb-1">
                ${user.status === 'online' ? 'Online' : 'Offline'}
              </span>
              ${user.last_seen ? `<small class="text-muted">${user.last_seen}</small>` : ''}
            </div>
          </div>
        </div>
      `;
    }).join('');
  }

  // Function to show/hide search section
  function showSearchInterface() {
    document.getElementById('chatSelectionPanel').classList.add('d-none');
    document.getElementById('availableUsers').classList.remove('d-none');
    document.getElementById('chatInterface').classList.add('d-none');
    
    // Focus on search input
    setTimeout(() => {
      const searchInput = document.getElementById('userSearchInput');
      if (searchInput) {
        searchInput.focus();
      }
    }, 100);
  }

  window.goBackToSelection = function() {
    document.getElementById('availableUsers').classList.add('d-none');
    document.getElementById('chatSelectionPanel').classList.remove('d-none');
    document.getElementById('chatInterface').classList.add('d-none');
    
    // Clear search
    const searchInput = document.getElementById('userSearchInput');
    if (searchInput) {
      searchInput.value = '';
    }
    document.getElementById('searchResults').innerHTML = '';
    
    // Reset current chat user
    currentChatUser = null;
  };

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

{{-- Additional Chat Functions --}}
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
    // FAQ responses mapping (should match admin FAQ management)
    const faqResponses = {
        enrollment: "To enroll in a course, go to your dashboard, select 'Available Courses', choose your desired course, and click 'Enroll Now'. Complete the payment process to finalize your enrollment.",
        payment: "We accept credit/debit cards, PayPal, bank transfers, and installment plans for select courses. All payments are processed securely.",
        schedule: "Login to your dashboard, go to 'My Courses', and click the 'Schedule' tab. You can also export your schedule to your calendar.",
        certificate: "Complete all course modules, pass assessments, maintain 80% attendance, and complete the final project. Certificates are generated automatically within 5-7 business days.",
        support: "Contact support via live chat, email (support@artc.edu), phone (+1-555-123-4567), or submit a ticket through the support portal."
    };
    const chatMessages = document.getElementById('chatMessages');
    const response = faqResponses[type];
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
</script>
