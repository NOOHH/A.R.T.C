@extends('admin.admin-dashboard-layout')

@section('title', 'Chat Logs - Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-primary">
                        <i class="bi bi-chat-dots me-2"></i>Chat Logs
                    </h1>
                    <p class="text-muted mb-0">Monitor and manage all chat conversations</p>
                </div>
                <div class="d-flex gap-2">
                    <!-- FAQ Management Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-warning dropdown-toggle" type="button" id="faqDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-question-circle me-2"></i>FAQ Management
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.faq.index') }}">
                                <i class="bi bi-list-ul me-2"></i>View All FAQs
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.faq.index') }}#addFaq">
                                <i class="bi bi-plus-circle me-2"></i>Add New FAQ
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('admin.faq.index') }}#categories">
                                <i class="bi bi-tags me-2"></i>Manage Categories
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.faq.index') }}#statistics">
                                <i class="bi bi-bar-chart me-2"></i>FAQ Statistics
                            </a></li>
                        </ul>
                    </div>
                    
                    <button class="btn btn-outline-primary" onclick="refreshChatData()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#chatSettingsModal">
                        <i class="bi bi-gear me-1"></i>Settings
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $stats['total_conversations'] }}</h5>
                            <small class="opacity-75">Total Conversations</small>
                        </div>
                        <i class="bi bi-chat-square-text fs-3 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $stats['active_users'] }}</h5>
                            <small class="opacity-75">Active Users</small>
                        </div>
                        <i class="bi bi-people fs-3 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $stats['unread_messages'] }}</h5>
                            <small class="opacity-75">Unread Messages</small>
                        </div>
                        <i class="bi bi-envelope-exclamation fs-3 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $stats['response_time_avg'] }}</h5>
                            <small class="opacity-75">Avg Response Time</small>
                        </div>
                        <i class="bi bi-clock fs-3 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Rooms and Recent Messages -->
    <div class="row">
        <!-- Chat Rooms -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-left-dots me-2"></i>Chat Rooms
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary active" data-filter="all">All</button>
                        <button class="btn btn-outline-secondary" data-filter="support">Support</button>
                        <button class="btn btn-outline-secondary" data-filter="technical">Technical</button>
                        <button class="btn btn-outline-secondary" data-filter="courses">Courses</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Room Name</th>
                                    <th>Type</th>
                                    <th>Participants</th>
                                    <th>Last Message</th>
                                    <th>Last Activity</th>
                                    <th>Unread</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($chatRooms as $room)
                                <tr class="chat-room-row" data-room-type="{{ $room['type'] }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="chat-room-icon me-2">
                                                @if($room['type'] === 'support')
                                                    <i class="bi bi-headset text-primary"></i>
                                                @elseif($room['type'] === 'technical')
                                                    <i class="bi bi-tools text-warning"></i>
                                                @elseif($room['type'] === 'courses')
                                                    <i class="bi bi-book text-success"></i>
                                                @else
                                                    <i class="bi bi-chat-square text-info"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <strong>{{ $room['name'] }}</strong>
                                                <br>
                                                <small class="text-muted">Room #{{ $room['id'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($room['type']) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $room['participants'] }}</span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;">
                                            {{ $room['last_message'] }}
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $room['last_activity']->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($room['unread_count'] > 0)
                                            <span class="badge bg-danger">{{ $room['unread_count'] }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.chat.room', $room['id']) }}" 
                                               class="btn btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button class="btn btn-outline-secondary" 
                                                    onclick="joinChatRoom({{ $room['id'] }})">
                                                <i class="bi bi-box-arrow-in-right"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" 
                                                    onclick="archiveChatRoom({{ $room['id'] }})">
                                                <i class="bi bi-archive"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Messages -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>Recent Messages
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($recentMessages as $message)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="me-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="user-avatar me-2">
                                            <span class="badge bg-primary rounded-circle">
                                                {{ substr($message['user_name'], 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong class="fs-6">{{ $message['user_name'] }}</strong>
                                            <small class="text-muted ms-1">
                                                ({{ ucfirst($message['user_type']) }})
                                            </small>
                                        </div>
                                    </div>
                                    <p class="mb-1 text-muted small">{{ $message['message'] }}</p>
                                    <small class="text-muted">
                                        in {{ $message['room'] }} • 
                                        {{ $message['timestamp']->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="status-indicator">
                                    @if($message['status'] === 'unread')
                                        <span class="badge bg-danger">New</span>
                                    @elseif($message['status'] === 'responded')
                                        <span class="badge bg-success">Replied</span>
                                    @else
                                        <span class="badge bg-secondary">Read</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="#" class="text-decoration-none">View all messages</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chat Settings Modal -->
<div class="modal fade" id="chatSettingsModal" tabindex="-1" aria-labelledby="chatSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chatSettingsModalLabel">Chat Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Auto-refresh interval</label>
                    <select class="form-select" id="refreshInterval">
                        <option value="5">5 seconds</option>
                        <option value="10" selected>10 seconds</option>
                        <option value="30">30 seconds</option>
                        <option value="60">1 minute</option>
                    </select>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notificationSound" checked>
                        <label class="form-check-label" for="notificationSound">
                            Enable notification sounds
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="desktopNotifications" checked>
                        <label class="form-check-label" for="desktopNotifications">
                            Enable desktop notifications
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveChatSettings()">Save Settings</button>
            </div>
        </div>
    </div>
</div>

<style>
.chat-room-icon {
    font-size: 1.2rem;
}

.user-avatar {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.status-indicator {
    min-width: 60px;
    text-align: center;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.table-responsive {
    max-height: 500px;
    overflow-y: auto;
}

.chat-room-row:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }
    
    .table-responsive {
        max-height: 400px;
    }
}
</style>

<script>
function refreshChatData() {
    location.reload();
}

function joinChatRoom(roomId) {
    window.location.href = `/admin/chat/room/${roomId}`;
}

function archiveChatRoom(roomId) {
    if (confirm('Are you sure you want to archive this chat room?')) {
        // In a real application, this would make an AJAX request
        alert('Chat room archived successfully!');
    }
}

function saveChatSettings() {
    const refreshInterval = document.getElementById('refreshInterval').value;
    const notificationSound = document.getElementById('notificationSound').checked;
    const desktopNotifications = document.getElementById('desktopNotifications').checked;
    
    // In a real application, this would save the settings
    alert('Settings saved successfully!');
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('chatSettingsModal'));
    modal.hide();
}

// Filter chat rooms
document.querySelectorAll('[data-filter]').forEach(button => {
    button.addEventListener('click', function() {
        // Update active button
        document.querySelectorAll('[data-filter]').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        
        const filter = this.getAttribute('data-filter');
        const rows = document.querySelectorAll('.chat-room-row');
        
        rows.forEach(row => {
            if (filter === 'all' || row.getAttribute('data-room-type') === filter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});

// Auto-refresh functionality
let refreshInterval = 10000; // 10 seconds default
let refreshTimer;

function startAutoRefresh() {
    refreshTimer = setInterval(refreshChatData, refreshInterval);
}

function stopAutoRefresh() {
    if (refreshTimer) {
        clearInterval(refreshTimer);
    }
}

// Start auto-refresh when page loads
document.addEventListener('DOMContentLoaded', function() {
    startAutoRefresh();
});

// Stop auto-refresh when user leaves the page
window.addEventListener('beforeunload', function() {
    stopAutoRefresh();
});
</script>
@endsection
