<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat Component Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Chat Component Test</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Test Chat User Search</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">User Type</label>
                            <div class="btn-group d-block">
                                <button class="btn btn-outline-primary" onclick="selectUserType('student')">Students</button>
                                <button class="btn btn-outline-success" onclick="selectUserType('professor')">Professors</button>
                                <button class="btn btn-outline-info" onclick="selectUserType('admin')">Admins</button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="userSearchInput" class="form-label">Search Users</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="userSearchInput" 
                                   placeholder="Type to search..." 
                                   onkeyup="performRealTimeSearch()">
                        </div>
                        
                        <div id="searchResults" class="border rounded p-3" style="min-height: 200px;">
                            <div class="text-center text-muted">
                                <i class="fas fa-search fa-2x mb-2"></i>
                                <p>Select a user type and start typing to search...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Chat Interface</h5>
                    </div>
                    <div class="card-body">
                        <div id="chatInterface" class="d-none">
                            <div class="mb-3">
                                <strong>Chatting with:</strong> <span id="chatWithName">No user selected</span>
                                <br>
                                <small class="text-muted" id="chatStatus">Offline</small>
                            </div>
                            
                            <div id="chatMessages" class="border rounded p-3 mb-3" style="height: 200px; overflow-y: auto;">
                                <!-- Messages will appear here -->
                            </div>
                            
                            <div class="input-group">
                                <input type="text" class="form-control" id="chatInput" placeholder="Type your message...">
                                <button class="btn btn-primary" onclick="sendMessage()">Send</button>
                            </div>
                        </div>
                        
                        <div id="noChat" class="text-center text-muted">
                            <i class="fas fa-comments fa-2x mb-2"></i>
                            <p>Select a user to start chatting</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mock session data (in real app, this would come from Laravel)
        const myId = 1;
        const myName = 'Test User';
        const isAuthenticated = true;
        const userRole = 'director';
        
        let currentChatType = null;
        let currentChatUser = null;
        
        function selectUserType(type) {
            currentChatType = type;
            
            // Update UI
            document.querySelectorAll('.btn-group .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Update search placeholder
            const searchInput = document.getElementById('userSearchInput');
            searchInput.placeholder = `Search for ${type}s...`;
            
            // Clear previous results
            document.getElementById('searchResults').innerHTML = `
                <div class="text-center text-muted">
                    <i class="fas fa-search fa-2x mb-2"></i>
                    <p>Start typing to search for ${type}s...</p>
                </div>
            `;
            
            // Focus on search input
            searchInput.focus();
        }
        
        function performRealTimeSearch() {
            const q = document.getElementById('userSearchInput').value.trim();
            const container = document.getElementById('searchResults');
            
            if (!q) {
                container.innerHTML = `
                    <div class="text-center text-muted">
                        <i class="fas fa-search fa-2x mb-2"></i>
                        <p>Start typing to search for ${currentChatType}s...</p>
                    </div>
                `;
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
                console.log('API Response:', json);
                
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
                container.innerHTML = `<p class="text-center text-danger">Error searching users: ${err.message}</p>`;
            });
        }
        
        function selectUserForChat(userId, userName, userRole) {
            currentChatUser = { id: userId, name: userName, role: userRole };
            
            // Update chat interface
            document.getElementById('chatWithName').textContent = userName;
            document.getElementById('chatStatus').textContent = userRole === 'admin' || userRole === 'director' ? 'Admin Online' : 'Online';
            
            // Show chat interface
            document.getElementById('noChat').classList.add('d-none');
            document.getElementById('chatInterface').classList.remove('d-none');
            
            // Clear previous messages
            document.getElementById('chatMessages').innerHTML = '';
            
            // Load messages for this user
            loadMessagesForUser(userId);
            
            // Focus on chat input
            document.getElementById('chatInput').focus();
        }
        
        function loadMessagesForUser(userId) {
            fetch('/api/chat/session/messages?' + new URLSearchParams({
                with: userId
            }), {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Messages loaded:', data);
                displayChatMessages(data.data || []);
            })
            .catch(error => {
                console.error('Error loading messages:', error);
                document.getElementById('chatMessages').innerHTML = '<p class="text-muted">Error loading messages</p>';
            });
        }
        
        function displayChatMessages(messages) {
            const messagesContainer = document.getElementById('chatMessages');
            messagesContainer.innerHTML = '';
            
            messages.forEach(message => {
                const isMyMessage = message.sender_id == myId;
                const messageClass = isMyMessage ? 'text-end' : 'text-start';
                const bgClass = isMyMessage ? 'bg-primary text-white' : 'bg-light';
                
                messagesContainer.innerHTML += `
                    <div class="mb-2 ${messageClass}">
                        <div class="d-inline-block p-2 rounded ${bgClass}" style="max-width: 70%;">
                            ${message.message}
                        </div>
                        <div class="small text-muted">${new Date(message.sent_at).toLocaleTimeString()}</div>
                    </div>
                `;
            });
            
            // Scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        function sendMessage() {
            const input = document.getElementById('chatInput');
            const message = input.value.trim();
            
            if (!message || !currentChatUser) return;
            
            // Add message to chat immediately
            const messagesContainer = document.getElementById('chatMessages');
            messagesContainer.innerHTML += `
                <div class="mb-2 text-end">
                    <div class="d-inline-block p-2 rounded bg-primary text-white" style="max-width: 70%;">
                        ${message}
                    </div>
                    <div class="small text-muted">Sending...</div>
                </div>
            `;
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            // Clear input
            input.value = '';
            
            // Send to API
            fetch('/api/chat/session/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    receiver_id: currentChatUser.id,
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Message sent:', data);
                // Update the "Sending..." to actual time
                const sentTime = new Date().toLocaleTimeString();
                const timeElements = messagesContainer.querySelectorAll('.text-muted');
                const lastTimeElement = timeElements[timeElements.length - 1];
                if (lastTimeElement && lastTimeElement.textContent === 'Sending...') {
                    lastTimeElement.textContent = sentTime;
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
                // Show error in chat
                messagesContainer.innerHTML += `
                    <div class="mb-2 text-center">
                        <div class="text-danger small">Failed to send message</div>
                    </div>
                `;
            });
        }
        
        // Allow enter key to send message
        document.getElementById('chatInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    </script>
</body>
</html>
