@extends('student.student-dasboard.student-dashboard-layout')

@section('title', $course['name'] ?? 'Course')

@push('styles')
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<!-- Custom Course Styles -->
<link href="{{ asset('css/student/student-course.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="course-container">
    <!-- Course Header -->
    <div class="course-header">
        <div class="course-info">
            <h1>{{ $course['name'] }}</h1>
            <p class="course-description">{{ $course['description'] }}</p>
            <div class="course-stats">
                <div class="stat-item">
                    <strong>Progress:</strong> {{ $course['progress'] }}% Complete
                </div>
                <div class="stat-item">
                    <strong>Lessons:</strong> {{ $course['completed_modules'] }} of {{ $course['total_modules'] }}
                </div>
                <div class="stat-item">
                    <strong>Assignments:</strong> 3 of 8
                </div>
            </div>
        </div>
        <div class="course-actions">
            @if($course['total_modules'] > 0)
                <button class="continue-btn" onclick="scrollToModules()">
                    Continue Learning
                </button>
            @else
                <button class="continue-btn" disabled>
                    No Modules Available
                </button>
            @endif
        </div>
    </div>

    <!-- Course Content -->
    <div class="course-content">
        <!-- Course Modules -->
        <div class="course-modules">
            <h2 id="course-modules">Course Modules</h2>
            
            @if($course['total_modules'] > 0)
                <div class="module-list">
                    @foreach($course['modules'] as $module)
                        <div class="module-item {{ $module['status'] === 'completed' ? 'completed' : ($module['status'] === 'available' ? 'in-progress' : 'locked') }}">
                            <div class="module-icon">
                                @if($module['status'] === 'completed')
                                    ✓
                                @elseif($module['status'] === 'available')
                                    📖
                                @else
                                    🔒
                                @endif
                            </div>
                            
                            <div class="module-details">
                                <h3>Module {{ $module['order'] }}: {{ $module['name'] }}</h3>
                                <p>{{ $module['description'] }}</p>
                                
                                <div class="module-progress">
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: {{ $module['progress'] }}%"></div>
                                    </div>
                                    <span class="progress-text">{{ $module['progress'] }}% Complete</span>
                                </div>
                            </div>
                            
                            <div class="module-actions">
                                @if($module['status'] === 'available')
                                    <button class="module-btn">Continue</button>
                                @elseif($module['status'] === 'completed')
                                    <button class="module-btn">Review</button>
                                @else
                                    <button class="module-btn" disabled>Locked</button>
                                @endif
                                
                                @if($module['attachment'])
                                    <a href="{{ $module['attachment_url'] }}" 
                                       class="resource-item"
                                       target="_blank"
                                       download>
                                        <span class="resource-icon">📄</span>
                                        Download Module
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="module-item">
                    <div style="text-align: center; padding: 2rem;">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">📚</div>
                        <h3>No Modules Available Yet</h3>
                        <p>Your instructors haven't uploaded any modules for this course yet.</p>
                        <p><strong>{{ $course['name'] }}</strong> modules will automatically appear here once they're added by the admin.</p>
                        <small style="color: #666;">Please check back later or contact your instructor for more information.</small>
                    </div>
                </div>
            @endif
        </div>

        <!-- Course Sidebar -->
        <div class="course-sidebar">
            <!-- Assignments Card -->
            <div class="assignments-card">
                <h3>Recent Assignments</h3>
                <div class="assignment-list">
                    <div class="assignment-item">
                        <div class="assignment-info">
                            <h4>Limit Problems Set 1</h4>
                            <p>Due: Jan 15, 2025</p>
                        </div>
                        <span class="assignment-status completed">COMPLETED</span>
                    </div>
                    <div class="assignment-item">
                        <div class="assignment-info">
                            <h4>Derivative Practice</h4>
                            <p>Due: Jan 22, 2025</p>
                        </div>
                        <span class="assignment-status pending">PENDING</span>
                    </div>
                    <div class="assignment-item">
                        <div class="assignment-info">
                            <h4>Integration Quiz</h4>
                            <p>Due: Jan 29, 2025</p>
                        </div>
                        <span class="assignment-status upcoming">UPCOMING</span>
                    </div>
                </div>
            </div>

            <!-- Resources Card -->
            <div class="resources-card">
                <h3>Course Resources</h3>
                <div class="resource-list">
                    <a href="#" class="resource-item">
                        <span class="resource-icon">📄</span>
                        <span class="resource-name">Course Syllabus</span>
                    </a>
                    <a href="#" class="resource-item">
                        <span class="resource-icon">📚</span>
                        <span class="resource-name">Textbook (PDF)</span>
                    </a>
                    <a href="#" class="resource-item">
                        <span class="resource-icon">🎥</span>
                        <span class="resource-name">Video Lectures</span>
                    </a>
                    <a href="#" class="resource-item">
                        <span class="resource-icon">🧮</span>
                        <span class="resource-name">Calculator Tools</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function scrollToModules() {
    document.getElementById('course-modules').scrollIntoView({ 
        behavior: 'smooth' 
    });
}

// Auto-refresh modules every 30 seconds to sync with admin uploads
setInterval(function() {
    console.log('Checking for module updates...');
    // You can implement live sync here by making an AJAX request
    // to check for new modules and update the page content
}, 30000);

// Show notification when new modules are detected
function showModuleUpdateNotification() {
    // Create custom notification
    const notificationHtml = `
        <div class="notification-toast" 
             style="position: fixed; top: 20px; right: 20px; 
                    background: #28a745; color: white; padding: 15px 20px; 
                    border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                    z-index: 9999; animation: slideIn 0.3s ease;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div>✓</div>
                <div>
                    <strong>New module available!</strong><br>
                    <small>Refresh the page to see the latest content.</small>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" 
                        style="background: none; border: none; color: white; 
                               font-size: 18px; cursor: pointer; margin-left: 10px;">×</button>
            </div>
        </div>
        <style>
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        </style>
    `;
    
    document.body.insertAdjacentHTML('beforeend', notificationHtml);
    
    // Auto-remove after 10 seconds
    setTimeout(() => {
        const notification = document.querySelector('.notification-toast');
        if (notification) {
            notification.remove();
        }
    }, 10000);
    
    // Add click handler to refresh page
    document.querySelector('.notification-toast').addEventListener('click', () => {
        window.location.reload();
    });
}
</script>
@endsection
