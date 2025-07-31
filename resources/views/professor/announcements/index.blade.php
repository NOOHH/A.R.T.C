@extends('professor.professor-layouts.professor-layout')

@section('title', 'My Announcements')

@push('styles')
<style>
.announcement-card {
    transition: all 0.3s ease;
    border: 1px solid #e3e6f0;
}

.announcement-card:hover {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    transform: translateY(-2px);
}

.announcement-type-badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.announcement-type-general { background-color: #3498db; }
.announcement-type-urgent { background-color: #e74c3c; }
.announcement-type-event { background-color: #f39c12; }
.announcement-type-system { background-color: #9b59b6; }

.status-toggle {
    cursor: pointer;
}

.target-info {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 0.85rem;
}

.announcement-meta {
    font-size: 0.875rem;
    color: #6c757d;
}

.table-responsive {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.creator-avatar {
    width: 32px;
    height: 32px;
    object-fit: cover;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-megaphone me-2"></i>My Announcements
            </h1>
            <p class="text-muted">Create and manage your announcements for students</p>
        </div>
        <a href="{{ route('professor.announcements.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create Announcement
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Announcements
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $announcements->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-megaphone fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Published
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $announcements->where('is_published', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Urgent
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $announcements->where('type', 'urgent')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $announcements->where('is_active', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-eye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-list-ul me-2"></i>My Announcements
            </h6>
        </div>
        <div class="card-body">
            @if($announcements->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">Creator</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Target Audience</th>
                                <th>Status</th>
                                <th>Published</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($announcements as $announcement)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $currentUserId = session('user_id');
                                                $creator = $announcement->getCreator();
                                                $creatorName = $announcement->getCreatorName();
                                                $creatorAvatar = $announcement->getCreatorAvatar();
                                                $isCurrentUser = false;
                                                
                                                if ($announcement->professor_id && $announcement->professor_id == $currentUserId) {
                                                    $isCurrentUser = true;
                                                }
                                            @endphp
                                            
                                            <div class="me-2">
                                                @if($creatorAvatar && file_exists(public_path($creatorAvatar)))
                                                    <img src="{{ asset($creatorAvatar) }}" alt="Profile" class="creator-avatar rounded-circle">
                                                @else
                                                    <img src="{{ asset('images/default-avatar.svg') }}" alt="Default Profile" class="creator-avatar rounded-circle">
                                                @endif
                                            </div>
                                            <div>
                                                <small class="fw-bold">{{ $isCurrentUser ? 'YOU' : $creatorName }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong>{{ $announcement->title }}</strong>
                                            @if($announcement->description)
                                                <small class="text-muted">{{ Str::limit($announcement->description, 60) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge announcement-type-badge announcement-type-{{ $announcement->type }}">
                                            {{ ucfirst($announcement->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="target-info">
                                            @if($announcement->target_scope === 'all')
                                                <span class="badge bg-secondary">All Students</span>
                                            @else
                                                @php
                                                    // Handle both array (new format) and JSON string (old format)
                                                    $targetUsers = [];
                                                    if (is_array($announcement->target_users)) {
                                                        $targetUsers = $announcement->target_users;
                                                    } elseif (is_string($announcement->target_users)) {
                                                        $targetUsers = json_decode($announcement->target_users, true) ?: [];
                                                    }
                                                    
                                                    $targetPrograms = [];
                                                    if (is_array($announcement->target_programs)) {
                                                        $targetPrograms = $announcement->target_programs;
                                                    } elseif (is_string($announcement->target_programs)) {
                                                        $targetPrograms = json_decode($announcement->target_programs, true) ?: [];
                                                    }
                                                @endphp
                                                @if($targetUsers)
                                                    @foreach($targetUsers as $user)
                                                        <span class="badge bg-info me-1">{{ ucfirst($user) }}</span>
                                                    @endforeach
                                                @endif
                                                @if($targetPrograms)
                                                    <br><small class="text-muted">{{ count($targetPrograms) }} program(s)</small>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" type="checkbox" 
                                                   data-id="{{ $announcement->announcement_id }}" 
                                                   data-type="status"
                                                   {{ $announcement->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" type="checkbox" 
                                                   data-id="{{ $announcement->announcement_id }}" 
                                                   data-type="published"
                                                   {{ $announcement->is_published ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="announcement-meta">
                                            {{ $announcement->created_at->format('M d, Y') }}<br>
                                            <small>{{ $announcement->created_at->format('g:i A') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('professor.announcements.show', $announcement->announcement_id) }}" 
                                               class="btn btn-outline-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('professor.announcements.edit', $announcement->announcement_id) }}" 
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button class="btn btn-outline-danger delete-btn" 
                                                    data-id="{{ $announcement->announcement_id }}"
                                                    data-title="{{ $announcement->title }}"
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $announcements->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-megaphone text-muted" style="font-size: 3rem;"></i>
                    <h4 class="text-muted mt-3">No announcements found</h4>
                    <p class="text-muted">Create your first announcement to get started.</p>
                    <a href="{{ route('professor.announcements.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Announcement
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the announcement "<strong id="deleteAnnouncementTitle"></strong>"?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle status toggles
    document.querySelectorAll('.status-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const id = this.dataset.id;
            const type = this.dataset.type;
            const url = type === 'status' 
                ? `/professor/announcements/${id}/toggle-status`
                : `/professor/announcements/${id}/toggle-published`;
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Optionally show a success message
                    console.log(data.message);
                } else {
                    // Revert toggle on error
                    this.checked = !this.checked;
                    alert('Error updating status');
                }
            })
            .catch(error => {
                // Revert toggle on error
                this.checked = !this.checked;
                console.error('Error:', error);
                alert('Error updating status');
            });
        });
    });

    // Handle delete buttons
    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const title = this.dataset.title;
            
            document.getElementById('deleteAnnouncementTitle').textContent = title;
            document.getElementById('deleteForm').action = `/professor/announcements/${id}`;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
});
</script>
@endpush
