<?php
/**
 * Complete Chat System Fix
 * Fixes: 1) Search functionality not working 2) Auto message popup 3) CSS styling
 */

echo "=== CHAT SYSTEM COMPLETE FIX ===\n\n";

// 1. Fix Global Chat Component - Add Missing Search Functionality
$globalChatPath = 'resources/views/components/global-chat.blade.php';
$globalChatContent = file_get_contents($globalChatPath);

// Check if search input handler is missing
if (strpos($globalChatContent, 'userSearchInput') !== false && strpos($globalChatContent, 'addEventListener') === false) {
    echo "❌ Search functionality missing - Adding search input handler\n";
    
    // Add search functionality after the existing JavaScript
    $searchJs = <<<'JS'

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
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      if (data.success && data.data) {
        displaySearchResults(data.data);
      } else {
        document.getElementById('searchResults').innerHTML = 
          '<div class="text-center text-muted py-3">No users found for "' + query + '"</div>';
      }
    })
    .catch(error => {
      console.error('Search error:', error);
      document.getElementById('searchResults').innerHTML = 
        '<div class="text-center text-danger py-3">Search failed. Please try again.</div>';
    });
  }

  function displaySearchResults(users) {
    const container = document.getElementById('searchResults');
    if (!users || users.length === 0) {
      container.innerHTML = '<div class="text-center text-muted py-3">No users found.</div>';
      return;
    }
    
    container.innerHTML = users.map(user => `
      <div class="user-item p-3 border-bottom" onclick="selectUserForChat(${user.id},'${user.name}','${user.role}')">
        <div class="d-flex align-items-center">
          <div class="avatar-circle me-3">
            <i class="bi bi-person-circle"></i>
          </div>
          <div class="flex-grow-1">
            <div class="fw-bold text-dark">${user.name}</div>
            <div class="text-muted small">${user.email}</div>
            <div class="text-muted small text-capitalize">
              <i class="bi bi-person-badge me-1"></i>${user.role}
            </div>
          </div>
          <span class="badge ${user.is_online ? 'bg-success' : 'bg-secondary'} ms-2">
            ${user.is_online ? 'Online' : 'Offline'}
          </span>
        </div>
      </div>
    `).join('');
  }

  // Function to show/hide search section
  function showSearchInterface() {
    document.getElementById('userTypeSelection').classList.add('d-none');
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

  function goBackToSelection() {
    document.getElementById('availableUsers').classList.add('d-none');
    document.getElementById('userTypeSelection').classList.remove('d-none');
    document.getElementById('chatInterface').classList.add('d-none');
    
    // Clear search
    const searchInput = document.getElementById('userSearchInput');
    if (searchInput) {
      searchInput.value = '';
    }
    document.getElementById('searchResults').innerHTML = '';
    
    // Reset current chat user
    currentChatUser = null;
  }

JS;

    // Insert the search functionality
    $insertPosition = strpos($globalChatContent, '// ─────── REAL-TIME CHAT FUNCTIONALITY ───────');
    if ($insertPosition !== false) {
        $globalChatContent = substr_replace($globalChatContent, $searchJs . "\n", $insertPosition, 0);
        file_put_contents($globalChatPath, $globalChatContent);
        echo "✅ Search functionality added successfully\n";
    } else {
        echo "❌ Could not find insertion point for search functionality\n";
    }
} else {
    echo "✅ Search functionality already present\n";
}

// 2. Fix Auto Message Popup - Update realtime-chat component
$realtimeChatPath = 'resources/views/components/realtime-chat.blade.php';
$realtimeChatContent = file_get_contents($realtimeChatPath);

if (strpos($realtimeChatContent, 'autoOpenChatForNewMessage') === false) {
    echo "❌ Auto-popup functionality missing - Adding message auto-popup\n";
    
    $autoPopupJs = <<<'JS'

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
            const chatOffcanvas = document.querySelector('[data-bs-target="#liveChatOffcanvas"]');
            if (chatOffcanvas) {
                chatOffcanvas.click();
            }
        }
    }

JS;

    // Update the showChatNotification function to include auto-popup
    $updatedNotificationFunction = <<<'JS'
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
    }
JS;

    // Replace the existing showChatNotification function
    $pattern = '/function showChatNotification\(message\) \{[^}]+\}/s';
    if (preg_match($pattern, $realtimeChatContent)) {
        $realtimeChatContent = preg_replace($pattern, $updatedNotificationFunction, $realtimeChatContent);
        
        // Add the auto-popup function before the last closing script tag
        $insertPosition = strrpos($realtimeChatContent, '</script>');
        if ($insertPosition !== false) {
            $realtimeChatContent = substr_replace($realtimeChatContent, $autoPopupJs . "\n", $insertPosition, 0);
        }
        
        file_put_contents($realtimeChatPath, $realtimeChatContent);
        echo "✅ Auto-popup functionality added successfully\n";
    } else {
        echo "❌ Could not update showChatNotification function\n";
    }
} else {
    echo "✅ Auto-popup functionality already present\n";
}

// 3. Enhanced CSS for better message styling
echo "❌ Updating CSS for better message styling\n";

$enhancedCss = <<<'CSS'
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
    font-size: 20px;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
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

CSS;

// Find the position to insert enhanced CSS in global-chat.blade.php
$cssInsertPosition = strpos($globalChatContent, '.message.bot .message-sender::before');
if ($cssInsertPosition !== false) {
    // Find the end of the existing CSS
    $endCssPosition = strpos($globalChatContent, '</style>', $cssInsertPosition);
    if ($endCssPosition !== false) {
        $globalChatContent = substr_replace($globalChatContent, "\n\n" . $enhancedCss . "\n", $endCssPosition, 0);
        file_put_contents($globalChatPath, $globalChatContent);
        echo "✅ Enhanced CSS styling added successfully\n";
    }
} else {
    echo "❌ Could not find CSS insertion point\n";
}

echo "\n=== CHAT SYSTEM FIX COMPLETED ===\n";
echo "Please test the following:\n";
echo "1. ✅ Search functionality in chat\n";
echo "2. ✅ Auto-popup when receiving messages\n"; 
echo "3. ✅ Enhanced message styling\n";
echo "4. ✅ Improved user experience\n";
echo "\nTo test: Open chat, try searching for users, and send/receive messages\n";
?>
