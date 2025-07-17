@extends('student.student-dashboard.student-dashboard-layout')

@section('title', $program->program_name ?? 'Course')

@push('styles')
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<!-- Custom Course Styles -->
<style>
    .course-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    /* Course Header */
    .course-header {
        background: linear-gradient(135deg, #8e44ad 0%, #3498db 100%);
        color: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
    }
    
    .course-info {
        flex: 1;
        min-width: 300px;
    }
    
    .course-info h1 {
        margin: 0 0 10px 0;
        font-size: 2rem;
        font-weight: 700;
    }
    
    .course-description {
        margin-bottom: 20px;
        opacity: 0.9;
        font-size: 1.1rem;
    }
    
    .course-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 15px;
    }
    
    .stat-item {
        background: rgba(255,255,255,0.2);
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
    }
    
    .course-actions {
        padding: 20px 0;
    }
    
    .continue-btn {
        background: rgba(255,255,255,0.9);
        color: #8e44ad;
        border: none;
        padding: 12px 25px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .continue-btn:hover {
        background: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    /* Course Progress */
    .course-progress {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .progress-bar-container {
        height: 10px;
        background: #e9ecef;
        border-radius: 5px;
        margin: 15px 0;
        overflow: hidden;
    }
    
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #3498db, #9b59b6);
        border-radius: 5px;
        transition: width 1s ease;
    }
    
    .progress-stats {
        display: flex;
        justify-content: space-between;
        color: #7f8c8d;
        font-size: 0.9rem;
    }
    
    /* Module Navigation Tabs */
    .module-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .module-tab {
        padding: 10px 20px;
        background: #f1f2f6;
        border: none;
        border-radius: 30px;
        color: #555;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .module-tab.active {
        background: #3498db;
        color: white;
    }
    
    /* Modules List */
    .modules-list {
        margin-top: 30px;
    }
    
    .module-card {
        background: white;
        border-radius: 15px;
        margin-bottom: 20px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: all 0.3s;
        display: flex;
        flex-direction: column;
    }
    
    .module-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .module-header {
        padding: 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .module-title {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .module-icon {
        font-size: 1.4rem;
        color: #3498db;
    }
    
    .module-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .status-locked {
        background: #f1f2f6;
        color: #7f8c8d;
    }
    
    .status-available {
        background: #e7f3ff;
        color: #3498db;
    }
    
    .status-completed {
        background: #e8f5e9;
        color: #4caf50;
    }
    
    .module-body {
        padding: 20px;
    }
    
    .module-description {
        margin-bottom: 20px;
        color: #555;
        line-height: 1.6;
    }
    
    .module-actions {
        text-align: right;
    }
    
    .start-module-btn {
        background: #3498db;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }
    
    .start-module-btn:hover {
        background: #2980b9;
    }
    
    .locked-module-btn {
        background: #bdc3c7;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: 600;
        cursor: not-allowed;
        display: inline-block;
    }
    
    .completed-module-btn {
        background: #2ecc71;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }
    
    .completed-module-btn:hover {
        background: #27ae60;
    }
    
    /* Empty state */
    .no-modules {
        background: #f8f9fa;
        padding: 60px 20px;
        text-align: center;
        border-radius: 15px;
        color: #7f8c8d;
        border: 2px dashed #e9ecef;
    }
    
    .no-modules::before {
        content: '📚';
        display: block;
        font-size: 3rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }
    
    /* Content type badge styles */
    .content-type {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 10px;
        text-transform: uppercase;
    }
    
    .content-type.module { background: #e7f3ff; color: #0066cc; }
    .content-type.assignment { background: #fff3e0; color: #ff9800; }
    .content-type.quiz { background: #f3e5f5; color: #9c27b0; }
    .content-type.test { background: #ffebee; color: #f44336; }
    .content-type.link { background: #e8f5e8; color: #4caf50; }
</style>
@endpush

@section('content')
<div class="course-container">
    <!-- Course Header -->
    <div class="course-header">
        <div class="course-info">
            <h1>{{ $program->program_name }}</h1>
            <p class="course-description">{{ $program->program_description ?? 'No description available.' }}</p>
            <div class="course-stats">
                <div class="stat-item">
                    <strong>Progress:</strong> {{ $progress }}% Complete
                </div>
                <div class="stat-item">
                    <strong>Modules:</strong> {{ $completedModules }} of {{ $totalModules }}
                </div>
                <div class="stat-item">
                    <strong>Assignments:</strong> {{ count($modulesByType['assignment'] ?? []) }}
                </div>
            </div>
        </div>
        <div class="course-actions">
            @if($totalModules > 0)
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

    <!-- Course Progress -->
    <div class="course-progress">
        <h3>Your Progress</h3>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" style="width: {{ $progress }}%;"></div>
        </div>
        <div class="progress-stats">
            <span>{{ $completedModules }} of {{ $totalModules }} modules completed</span>
            <span>{{ $progress }}% complete</span>
        </div>
    </div>

    <!-- Course Content -->
    <div class="course-content">
        <!-- Module Navigation -->
        <div class="module-tabs">
            <button class="module-tab active" data-tab="all">All Content</button>
            @if(count($modulesByType['module'] ?? []) > 0)
                <button class="module-tab" data-tab="module">Modules</button>
            @endif
            @if(count($modulesByType['assignment'] ?? []) > 0)
                <button class="module-tab" data-tab="assignment">Assignments</button>
            @endif
            @if(count($modulesByType['quiz'] ?? []) > 0)
                <button class="module-tab" data-tab="quiz">Quizzes</button>
            @endif
            @if(count($modulesByType['test'] ?? []) > 0)
                <button class="module-tab" data-tab="test">Tests</button>
            @endif
        </div>
        
        <!-- Course Modules -->
        <div class="modules-list" id="course-modules">
            <h2>Course Content</h2>
            
            @if($totalModules > 0)
                @foreach($course['modules'] as $index => $module)
                    <div class="module-card" data-type="{{ $module['type'] }}" data-module-id="{{ $module['id'] ?? $index }}">>
                        <div class="module-header">
                            <h3 class="module-title">
                                @switch($module['type'])
                                    @case('module')
                                        <i class="bi bi-book module-icon"></i>
                                        @break
                                    @case('assignment')
                                        <i class="bi bi-pencil-square module-icon"></i>
                                        @break
                                    @case('quiz')
                                        <i class="bi bi-question-circle module-icon"></i>
                                        @break
                                    @case('test')
                                        <i class="bi bi-clipboard-check module-icon"></i>
                                        @break
                                    @case('link')
                                        <i class="bi bi-link module-icon"></i>
                                        @break
                                    @default
                                        <i class="bi bi-file-text module-icon"></i>
                                @endswitch
                                
                                {{ $module['title'] }}
                                <span class="content-type {{ $module['type'] }}">{{ ucfirst($module['type']) }}</span>
                            </h3>
                            
                            <span class="module-status {{ $module['is_locked'] ? 'status-locked' : ($module['is_completed'] ? 'status-completed' : 'status-available') }}">
                                {{ $module['is_locked'] ? 'Locked' : ($module['is_completed'] ? 'Completed' : 'Available') }}
                            </span>
                        </div>
                        
                        <div class="module-body">
                            <div class="module-description">
                                {{ $module['description'] ?: 'No description available.' }}
                            </div>
                            
                            @if($module['type'] == 'assignment' && isset($module['content_data']['assignment_title']))
                                <div class="assignment-details">
                                    <p><strong>Assignment:</strong> {{ $module['content_data']['assignment_title'] }}</p>
                                    @if(isset($module['content_data']['due_date']))
                                        <p><strong>Due:</strong> {{ \Carbon\Carbon::parse($module['content_data']['due_date'])->format('M d, Y g:i A') }}</p>
                                    @endif
                                </div>
                            @endif
                            
                            @if($module['type'] == 'quiz' && isset($module['content_data']['quiz_title']))
                                <div class="quiz-details">
                                    <p><strong>Quiz:</strong> {{ $module['content_data']['quiz_title'] }}</p>
                                    @if(isset($module['content_data']['time_limit']))
                                        <p><strong>Time Limit:</strong> {{ $module['content_data']['time_limit'] }} minutes</p>
                                    @endif
                                </div>
                            @endif
                            
                            @if($module['attachment'])
                                <div class="attachment">
                                    <p><i class="bi bi-paperclip"></i> Attachment available</p>
                                </div>
                            @endif
                            
                            <div class="module-actions">
                                @if($module['is_locked'])
                                    <span class="locked-module-btn">
                                        <i class="bi bi-lock"></i> Locked
                                    </span>
                                @elseif($module['is_completed'])
                                    <a href="{{ route('student.module', ['moduleId' => $module['id']]) }}" class="completed-module-btn">
                                        <i class="bi bi-check-circle"></i> Review Again
                                    </a>
                                @else
                                    <a href="{{ route('student.module', ['moduleId' => $module['id']]) }}" class="start-module-btn">
                                        @switch($module['type'])
                                            @case('assignment')
                                                <i class="bi bi-pencil"></i> Start Assignment
                                                @break
                                            @case('quiz')
                                                <i class="bi bi-question-circle"></i> Take Quiz
                                                @break
                                            @case('test')
                                                <i class="bi bi-clipboard-check"></i> Take Test
                                                @break
                                            @case('link')
                                                <i class="bi bi-box-arrow-up-right"></i> Open Link
                                                @break
                                            @default
                                                <i class="bi bi-play-fill"></i> Start Module
                                        @endswitch
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-modules">
                    <h3>No content available yet</h3>
                    <p>Check back later for course materials.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize course progress tracking
        updateCourseProgress();
        
        // Scroll to modules function
        window.scrollToModules = function() {
            document.getElementById('course-modules').scrollIntoView({ behavior: 'smooth' });
        };
        
        // Tab switching functionality
        const tabs = document.querySelectorAll('.module-tab');
        if (tabs.length) {
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    const filter = this.getAttribute('data-tab');
                    const modules = document.querySelectorAll('.module-card');
                    
                    modules.forEach(card => {
                        if (filter === 'all' || card.getAttribute('data-type') === filter) {
                            card.style.display = '';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
        }
    });

    // Update course progress based on completed modules
    function updateCourseProgress() {
        const programId = {{ $program->program_id }};
        const modules = @json($course['modules']);
        let completedModules = 0;
        let totalModules = modules.length;
        
        // Check localStorage for completed modules
        modules.forEach(module => {
            const isCompleted = localStorage.getItem(`module_${module.id}_completed`) === 'true';
            if (isCompleted || module.is_completed) {
                completedModules++;
                
                // Update module status display
                const moduleCard = document.querySelector(`[data-module-id="${module.id}"]`);
                if (moduleCard) {
                    const statusElement = moduleCard.querySelector('.module-status');
                    if (statusElement) {
                        statusElement.textContent = 'Completed';
                        statusElement.className = 'module-status status-completed';
                    }
                    
                    const actionButton = moduleCard.querySelector('.start-module-btn, .locked-module-btn');
                    if (actionButton) {
                        actionButton.textContent = '✓ Completed';
                        actionButton.className = 'completed-module-btn';
                    }
                }
            }
        });
        
        // Calculate and update progress
        const progressPercentage = totalModules > 0 ? Math.round((completedModules / totalModules) * 100) : 0;
        
        // Update progress bar
        const progressBar = document.querySelector('.progress-bar-fill');
        if (progressBar) {
            progressBar.style.width = progressPercentage + '%';
        }
        
        // Update progress text
        const progressStats = document.querySelector('.progress-stats');
        if (progressStats) {
            progressStats.innerHTML = `
                <span>${completedModules}/${totalModules} modules completed</span>
                <span>${progressPercentage}% complete</span>
            `;
        }
        
        console.log(`Course Progress: ${completedModules}/${totalModules} (${progressPercentage}%)`);
    }

    // Listen for storage events (when modules are completed in other tabs)
    window.addEventListener('storage', function(e) {
        if (e.key && e.key.includes('_completed')) {
            updateCourseProgress();
        }
    });
</script>
@endpush

@if(isset($showAccessModal) && $showAccessModal)
<!-- Special Access Notification Modal -->
<div class="modal fade" id="specialAccessModal" tabindex="-1" aria-labelledby="specialAccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="specialAccessModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Special Access Granted
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <h6 class="alert-heading">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Important Notice
                    </h6>
                    <p class="mb-2">You have been granted special access to this program as part of your batch enrollment.</p>
                    <hr class="my-2">
                    <p class="mb-1">
                        <strong>Current Status:</strong>
                        <span class="badge bg-{{ $enrollmentStatus === 'approved' ? 'success' : 'warning' }} ms-2">
                            {{ ucfirst($enrollmentStatus) }}
                        </span>
                    </p>
                    <p class="mb-0">
                        <strong>Payment Status:</strong>
                        <span class="badge bg-{{ $paymentStatus === 'paid' ? 'success' : 'warning' }} ms-2">
                            {{ ucfirst($paymentStatus) }}
                        </span>
                    </p>
                </div>
                
                <div class="d-flex align-items-start mb-3">
                    <i class="bi bi-check-circle-fill text-success me-3 fs-4"></i>
                    <div>
                        <h6 class="mb-1">What you can do:</h6>
                        <ul class="mb-0 text-muted">
                            <li>Access all course materials and modules</li>
                            <li>Participate in live sessions and discussions</li>
                            <li>Submit assignments and take quizzes</li>
                            <li>Track your learning progress</li>
                        </ul>
                    </div>
                </div>
                
                @if($enrollmentStatus !== 'approved' || $paymentStatus !== 'paid')
                <div class="d-flex align-items-start">
                    <i class="bi bi-info-circle-fill text-primary me-3 fs-4"></i>
                    <div>
                        <h6 class="mb-1">Action Required:</h6>
                        <p class="mb-0 text-muted">
                            @if($enrollmentStatus !== 'approved')
                                Your registration is still being reviewed by the administration.
                            @endif
                            @if($paymentStatus !== 'paid')
                                @if($enrollmentStatus !== 'approved') Additionally, @endif
                                Please complete your payment to finalize your enrollment.
                            @endif
                            Contact the admin office if you need assistance.
                        </p>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg me-2"></i>I Understand
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show the modal automatically when page loads
    const specialAccessModal = new bootstrap.Modal(document.getElementById('specialAccessModal'));
    specialAccessModal.show();
});
</script>
@endif
