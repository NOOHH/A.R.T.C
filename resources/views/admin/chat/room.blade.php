@extends('admin.admin-dashboard-layout')

@section('title', 'Chat Room - Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.chat.index') }}" class="text-decoration-none">
                                    <i class="bi bi-chat-dots me-1"></i>Chat Logs
                                </a>
                            </li>
                            <li class="breadcrumb-item active">{{ $roomInfo['name'] }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0 text-primary mt-2">
                        {{ $roomInfo['name'] }}
                    </h1>
                    <p class="text-muted mb-0">
                        {{ $roomInfo['participants'] }} participants • 
                        {{ ucfirst($roomInfo['type']) }} room
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="refreshMessages()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                    <button class="btn btn-outline-secondary" onclick="exportChat()">
                        <i class="bi bi-download me-1"></i>Export
                    </button>
                    <button class="btn btn-primary" onclick="joinConversation()">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Join
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Interface -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Chat Messages -->
            <div class="card chat-container">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-left-text me-2"></i>Conversation
                        </h5>
                        <div class="chat-controls">
                            <button class="btn btn-sm btn-outline-light" onclick="scrollToTop()">
                                <i class="bi bi-arrow-up"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-light" onclick="scrollToBottom()">
                                <i class="bi bi-arrow-down"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="chat-messages" id="chatMessages">
                        @foreach($messages as $message)
                        <div class="message-item {{ $message['user_type'] === 'admin' ? 'admin-message' : 'user-message' }}">
                            <div class="message-content">
                                <div class="message-header">
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <span class="avatar-text">{{ $message['avatar'] }}</span>
                                        </div>
                                        <div class="user-details">
                                            <strong class="user-name">{{ $message['user_name'] }}</strong>
                                            <span class="user-type badge bg-{{ $message['user_type'] === 'admin' ? 'primary' : 'secondary' }}">
                                                {{ ucfirst($message['user_type']) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="message-timestamp">
                                        <small class="text-muted">{{ $message['timestamp']->format('M j, Y g:i A') }}</small>
                                    </div>
                                </div>
                                <div class="message-body">
                                    <p class="mb-0">{{ $message['message'] }}</p>
                                </div>
                                <div class="message-actions">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="replyToMessage({{ $message['id'] }})">
                                        <i class="bi bi-reply"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="flagMessage({{ $message['id'] }})">
                                        <i class="bi bi-flag"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="deleteMessage({{ $message['id'] }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer">
                    <div class="admin-message-input">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Type your message as admin..." id="adminMessage">
                            <button class="btn btn-primary" onclick="sendAdminMessage()">
                                <i class="bi bi-send"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Info Panel -->
        <div class="col-lg-4">
            <!-- Room Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Room Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="stat-item">
                                <div class="stat-value">{{ $roomInfo['participants'] }}</div>
                                <div class="stat-label">Participants</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <div class="stat-value">{{ count($messages) }}</div>
                                <div class="stat-label">Messages</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Room Type:</span>
                        <span class="badge bg-primary">{{ ucfirst($roomInfo['type']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span>Status:</span>
                        <span class="badge bg-success">Active</span>
                    </div>
                </div>
            </div>

            <!-- Participants -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-people me-2"></i>Participants
                    </h5>
                </div>
                <div class="card-body">
                    <div class="participant-list">
                        <div class="participant-item">
                            <div class="participant-avatar">
                                <span class="avatar-text">AS</span>
                            </div>
                            <div class="participant-info">
                                <strong>Admin Support</strong>
                                <small class="text-muted d-block">Administrator</small>
                            </div>
                            <div class="participant-status">
                                <span class="status-indicator online"></span>
                            </div>
                        </div>
                        <div class="participant-item">
                            <div class="participant-avatar">
                                <span class="avatar-text">JD</span>
                            </div>
                            <div class="participant-info">
                                <strong>John Doe</strong>
                                <small class="text-muted d-block">Student</small>
                            </div>
                            <div class="participant-status">
                                <span class="status-indicator online"></span>
                            </div>
                        </div>
                        <div class="participant-item">
                            <div class="participant-avatar">
                                <span class="avatar-text">JS</span>
                            </div>
                            <div class="participant-info">
                                <strong>Jane Smith</strong>
                                <small class="text-muted d-block">Professor</small>
                            </div>
                            <div class="participant-status">
                                <span class="status-indicator away"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="inviteParticipant()">
                            <i class="bi bi-person-plus me-1"></i>Invite Participant
                        </button>
                        <button class="btn btn-outline-warning" onclick="muteRoom()">
                            <i class="bi bi-volume-mute me-1"></i>Mute Room
                        </button>
                        <button class="btn btn-outline-secondary" onclick="archiveRoom()">
                            <i class="bi bi-archive me-1"></i>Archive Room
                        </button>
                        <button class="btn btn-outline-danger" onclick="clearHistory()">
                            <i class="bi bi-trash me-1"></i>Clear History
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.chat-container {
    height: 70vh;
    display: flex;
    flex-direction: column;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
    background-color: #f8f9fa;
    max-height: 500px;
}

.message-item {
    margin-bottom: 1.5rem;
    padding: 1rem;
    border-radius: 0.5rem;
    background-color: white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.message-item.admin-message {
    border-left: 4px solid #0d6efd;
}

.message-item.user-message {
    border-left: 4px solid #6c757d;
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.user-info {
    display: flex;
    align-items: center;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #0d6efd;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
}

.avatar-text {
    color: white;
    font-weight: bold;
    font-size: 0.875rem;
}

.user-details {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.user-type {
    font-size: 0.75rem;
}

.message-body {
    margin: 0.75rem 0;
    padding-left: 55px;
}

.message-actions {
    display: flex;
    gap: 0.5rem;
    padding-left: 55px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.message-item:hover .message-actions {
    opacity: 1;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #0d6efd;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
}

.participant-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}

.participant-item:last-child {
    border-bottom: none;
}

.participant-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background-color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
}

.participant-info {
    flex: 1;
}

.participant-status {
    margin-left: auto;
}

.status-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}

.status-indicator.online {
    background-color: #28a745;
}

.status-indicator.away {
    background-color: #ffc107;
}

.status-indicator.offline {
    background-color: #dc3545;
}

.admin-message-input {
    padding: 0.5rem;
}

@media (max-width: 768px) {
    .chat-container {
        height: 50vh;
    }
    
    .message-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .message-timestamp {
        margin-top: 0.5rem;
    }
    
    .message-actions {
        padding-left: 0;
        margin-top: 0.5rem;
    }
}
</style>

<script>
function refreshMessages() {
    location.reload();
}

function exportChat() {
    // In a real application, this would export the chat history
    alert('Chat exported successfully!');
}

function joinConversation() {
    // In a real application, this would add admin to the conversation
    alert('You have joined the conversation!');
}

function scrollToTop() {
    document.getElementById('chatMessages').scrollTop = 0;
}

function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function sendAdminMessage() {
    const messageInput = document.getElementById('adminMessage');
    const message = messageInput.value.trim();
    
    if (message) {
        // In a real application, this would send the message via AJAX
        alert('Message sent: ' + message);
        messageInput.value = '';
    }
}

function replyToMessage(messageId) {
    const message = prompt('Enter your reply:');
    if (message) {
        alert('Reply sent: ' + message);
    }
}

function flagMessage(messageId) {
    if (confirm('Flag this message as inappropriate?')) {
        alert('Message flagged successfully!');
    }
}

function deleteMessage(messageId) {
    if (confirm('Are you sure you want to delete this message?')) {
        alert('Message deleted successfully!');
    }
}

function inviteParticipant() {
    const email = prompt('Enter participant email:');
    if (email) {
        alert('Invitation sent to: ' + email);
    }
}

function muteRoom() {
    if (confirm('Mute this room for 1 hour?')) {
        alert('Room muted successfully!');
    }
}

function archiveRoom() {
    if (confirm('Archive this room? This action cannot be undone.')) {
        alert('Room archived successfully!');
        window.location.href = '{{ route("admin.chat.index") }}';
    }
}

function clearHistory() {
    if (confirm('Clear all chat history? This action cannot be undone.')) {
        alert('Chat history cleared successfully!');
    }
}

// Auto-scroll to bottom when page loads
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
});

// Enter key to send message
document.getElementById('adminMessage').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendAdminMessage();
    }
});
</script>
@endsection
