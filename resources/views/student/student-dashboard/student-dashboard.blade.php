@extends('student.student-dashboard.student-dashboard-layout')

@section('title', 'Student Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/student/student-dashboard.css') }}">
<style>
    /* Enhanced course cards */
    .courses-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #8e44ad 0%, #3498db 100%);
        color: white;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .card-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .completion-badge {
        background: rgba(255,255,255,0.2);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
    }
    
    .courses-list {
        padding: 20px;
    }
    
    .course-item {
        display: flex;
        background: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .course-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .course-thumbnail {
        width: 120px;
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .course-placeholder {
        font-size: 2.5rem;
        opacity: 0.5;
    }
    
    .course-details {
        padding: 15px;
        flex: 1;
    }
    
    .course-details h3 {
        margin: 0 0 10px 0;
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .course-details p {
        margin: 0 0 15px 0;
        color: #7f8c8d;
        font-size: 0.95rem;
        line-height: 1.5;
    }
    
    .progress-bar {
        height: 10px;
        background: #e9ecef;
        border-radius: 5px;
        overflow: hidden;
        position: relative;
    }
    
    .progress-bar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: var(--progress, 0%);
        background: linear-gradient(90deg, #3498db, #9b59b6);
        border-radius: 5px;
        transition: width 1s ease;
    }
    
    .progress-text {
        display: block;
        margin-top: 5px;
        font-size: 0.85rem;
        color: #7f8c8d;
        text-align: right;
    }
    
    .resume-btn {
        background: #3498db;
        color: white;
        border: none;
        padding: 10px 20px;
        margin: 15px;
        align-self: center;
        border-radius: 5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
        white-space: nowrap;
    }
    
    .resume-btn:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }
    
    /* Button states */
    .resume-btn.pending {
        background: #f39c12;
        cursor: not-allowed;
    }
    
    .resume-btn.payment-required {
        background: #e74c3c;
        animation: pulse 2s infinite;
    }
    
    .resume-btn.rejected {
        background: #95a5a6;
        cursor: not-allowed;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    /* Course enrollment info badges */
    .course-enrollment-info {
        margin: 10px 0;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    
    .enrollment-badge, .plan-badge, .type-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-weight: 500;
        line-height: 1;
    }
    
    .enrollment-badge {
        background: linear-gradient(135deg, #8e44ad 0%, #3498db 100%);
        color: white;
    }
    
    .plan-badge {
        background: rgba(39, 174, 96, 0.1);
        color: #27ae60;
        border: 1px solid rgba(39, 174, 96, 0.3);
    }
    
    .type-badge {
        background: rgba(230, 126, 34, 0.1);
        color: #e67e22;
        border: 1px solid rgba(230, 126, 34, 0.3);
    }
    
    .no-courses {
        padding: 40px 20px;
        text-align: center;
        color: #7f8c8d;
    }
    
    .no-courses::before {
        content: '📚';
        display: block;
        font-size: 3rem;
        margin-bottom: 10px;
        opacity: 0.5;
    }
    
    /* Module icons */
    .course-thumbnail::before {
        content: '📚';
        font-size: 2.5rem;
    }
</style>
@endpush

@section('content')
<div class="dashboard-grid">
    <!-- My Programs Section -->
    <div class="dashboard-card courses-card">
        <div class="card-header">
            <h2>My Programs</h2>
            <span class="completion-badge">{{ count($courses) > 0 ? floor(array_sum(array_column($courses, 'progress')) / count($courses)) : '0' }}% overall progress</span>
        </div>
        <div class="courses-list">
            @forelse($courses as $course)
                <div class="course-item">
                    <div class="course-thumbnail">
                        <div class="course-placeholder">📚</div>
                    </div>
                    <div class="course-details">
                        <h3>{{ $course['name'] }}</h3>
                        <p>{{ $course['description'] }}</p>
                        
                        <!-- Program Details -->
                        <div class="course-enrollment-info">
                            <span class="enrollment-badge">{{ $course['package_name'] }}</span>
                            @if(isset($course['plan_name']))
                                <span class="plan-badge">{{ $course['plan_name'] }}</span>
                            @endif
                            @if(isset($course['enrollment_type']))
                                <span class="type-badge">{{ $course['enrollment_type'] }}</span>
                            @endif
                        </div>
                        
                        <!-- Batch Information -->
                        @if(isset($course['batch_name']) && $course['batch_name'])
                        <div class="batch-info" style="margin-top: 10px; padding: 8px 12px; background: #e8f5e8; border-radius: 6px; font-size: 0.9rem;">
                            <div style="font-weight: 600; color: #27ae60; margin-bottom: 4px;">
                                <i class="fas fa-users"></i> {{ $course['batch_name'] }}
                            </div>
                            @if(isset($course['batch_dates']) && $course['batch_dates'])
                            <div style="color: #2c3e50; font-size: 0.85rem;">
                                <i class="fas fa-calendar-alt"></i> 
                                Start: {{ $course['batch_dates']['start'] }}
                                @if($course['batch_dates']['end'] !== 'TBA')
                                    | End: {{ $course['batch_dates']['end'] }}
                                @endif
                            </div>
                            @endif
                        </div>
                        @endif
                        
                        <div class="progress-bar" style="--progress: {{ $course['progress'] }}%">
                            <span class="progress-text">{{ $course['progress'] }}% complete</span>
                        </div>
                        <div class="course-meta" style="margin-top: 10px; font-size: 0.9rem; color: #7f8c8d;">
                            <span>{{ $course['completed_modules'] ?? 0 }} / {{ $course['total_modules'] ?? 0 }} modules complete</span>
                        </div>
                    </div>
                    @if($course['button_action'] === '#')
                        <button class="{{ $course['button_class'] }}" onclick="showStatusModal('{{ $course['enrollment_status'] }}', '{{ $course['name'] }}')" disabled>
                            {{ $course['button_text'] }}
                        </button>
                    @else
                        <a href="{{ $course['button_action'] }}" class="{{ $course['button_class'] }}">
                            {{ $course['button_text'] }}
                        </a>
                    @endif
                </div>
            @empty
                <div class="no-courses">
                    <p>You are not enrolled in any programs yet.</p>
                    <p>Please contact your administrator to get enrolled in courses.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Deadlines Section -->
    <div class="dashboard-card deadlines-card">
        <div class="card-header">
            <h2>Deadlines</h2>
        </div>
        <div class="deadlines-content">
            @forelse($deadlines as $deadline)
                <div class="deadline-item" style="padding: 10px 20px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 600; color: #2c3e50;">{{ $deadline->title }}</div>
                        <div style="font-size: 0.85rem; color: #7f8c8d;">{{ $deadline->description }}</div>
                        <div style="font-size: 0.8rem; color: #e74c3c;">
                            <i class="bi bi-clock"></i> Due: {{ \Carbon\Carbon::parse($deadline->due_date)->format('M d, Y g:i A') }}
                        </div>
                    </div>
                    <span class="badge" style="background: 
                        @if($deadline->status === 'completed') #28a745
                        @elseif($deadline->status === 'overdue') #dc3545  
                        @else #ffc107
                        @endif; color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.75rem;">
                        {{ ucfirst($deadline->status) }}
                    </span>
                </div>
            @empty
                <p style="padding: 30px 20px; text-align: center; color: #7f8c8d;">No upcoming deadlines at this time.</p>
            @endforelse
        </div>
    </div>

    <!-- My Progress Section -->
    <div class="dashboard-card progress-card">
        <div class="card-header">
            <h2>My Progress</h2>
        </div>
        <div class="progress-content" style="padding: 30px 20px; text-align: center;">
            <p style="color: #7f8c8d;">Progress chart will be displayed here</p>
            <p style="font-size: 0.9rem; color: #95a5a6;">Welcome back, {{ explode(' ', session('user_name'))[0] ?? 'student' }}!</p>
        </div>
    </div>

    <!-- Announcements Section -->
    <div class="dashboard-card announcement-card">
        <div class="card-header">
            <h2>Announcements</h2>
        </div>
        <div class="announcement-content">
            @forelse($announcements as $announcement)
                <div class="announcement-item" style="padding: 15px 20px; border-bottom: 1px solid #f0f0f0;">
                    <div style="display: flex; justify-content: between; align-items: flex-start; margin-bottom: 8px;">
                        <div style="font-weight: 600; color: #2c3e50; flex: 1;">{{ $announcement->title }}</div>
                        @if($announcement->announcement_type === 'video')
                            <span class="badge" style="background: #17a2b8; color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.7rem;">
                                📹 Video
                            </span>
                        @endif
                    </div>
                    <div style="color: #555; line-height: 1.5; margin-bottom: 8px;">{{ $announcement->content }}</div>
                    <div style="font-size: 0.8rem; color: #7f8c8d;">
                        <i class="bi bi-clock"></i> {{ $announcement->created_at->diffForHumans() }}
                    </div>
                </div>
            @empty
                <div style="padding: 20px;">
                    <p style="margin-bottom: 0;">Welcome to your student dashboard! Here you can access your courses, track your progress, and stay updated with important announcements.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalTitle">Enrollment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="statusModalBody">
                <!-- Content will be filled by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showStatusModal(status, courseName) {
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    const title = document.getElementById('statusModalTitle');
    const body = document.getElementById('statusModalBody');
    
    let modalContent = '';
    
    switch(status) {
        case 'pending':
            title.textContent = 'Pending Verification';
            modalContent = `
                <div class="text-center">
                    <i class="bi bi-hourglass-split text-warning" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Enrollment Under Review</h5>
                    <p>Your enrollment for <strong>${courseName}</strong> is currently being reviewed by our administrators.</p>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Please wait for admin approval. You will be notified once your registration is verified.
                    </div>
                </div>
            `;
            break;
        case 'rejected':
            title.textContent = 'Enrollment Rejected';
            modalContent = `
                <div class="text-center">
                    <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Enrollment Rejected</h5>
                    <p>Unfortunately, your enrollment for <strong>${courseName}</strong> has been rejected.</p>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Please contact our support team for more information.
                    </div>
                </div>
            `;
            break;
    }
    
    body.innerHTML = modalContent;
    modal.show();
}
</script>
@endsection
