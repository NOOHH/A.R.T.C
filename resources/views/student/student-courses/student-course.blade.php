@extends('student.student-dashboard.student-dashboard-layout')

@section('title', $program->program_name ?? 'Course')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
  /* Student Course Interface - Focused on Learning */
  .student-course-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1.5rem;
  }
  
  .course-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
  }
  
  .course-header h1 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
  }
  
  .course-description {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 1.5rem;
  }
  
  .course-stats {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
  }
  
  .stat-item {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.8rem 1.2rem;
    border-radius: 20px;
    font-weight: 600;
  }
  
  /* Progress Section */
  .progress-section {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  }
  
  .progress-bar-container {
    height: 12px;
    background: #e9ecef;
    border-radius: 6px;
    overflow: hidden;
    margin: 1rem 0;
  }
  
  .progress-bar-fill {
    height: 100%;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    transition: width 0.6s ease;
  }
  
  /* Learning Module Structure */
  .learning-modules {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }
  
  .learning-module {
    border: 2px solid #e1e5e9;
    border-radius: 15px;
    background: white;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
  }
  
  .learning-module:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
  }
  
  .module-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem 2rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
  }
  
  .module-header:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6b4190 100%);
  }
  
  .module-title-section {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .module-title-section h4 {
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0;
  }
  
  .module-status-badge {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.4rem 0.8rem;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 500;
  }
  
  .module-status-badge.completed {
    background: rgba(40, 167, 69, 0.9);
  }
  
  .module-status-badge.locked {
    background: rgba(108, 117, 125, 0.9);
  }
  
  .module-toggle-icon {
    transition: transform 0.3s ease;
    font-size: 1.1rem;
  }
  
  .module-toggle-icon.expanded {
    transform: rotate(90deg);
  }
  
  .module-content {
    display: none;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
  }
  
  .module-content.expanded {
    display: block;
  }
  
  .content-list {
    padding: 1.5rem;
  }
  
  .content-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    margin-bottom: 0.75rem;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    transition: all 0.3s ease;
  }
  
  .content-item:hover {
    background: #f1f3f4;
    border-color: #dee2e6;
    transform: translateX(3px);
  }
  
  .content-item-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
  }
  
  .content-type-badge {
    background: #007bff;
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
  }
  
  .content-type-badge.assignment { background: #dc3545; }
  .content-type-badge.pdf { background: #fd7e14; }
  .content-type-badge.video { background: #6f42c1; }
  .content-type-badge.quiz { background: #e83e8c; }
  .content-type-badge.test { background: #20c997; }
  .content-type-badge.link { background: #17a2b8; }
  
  .content-details h6 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
  }
  
  .content-details p {
    margin: 0;
    font-size: 0.875rem;
    color: #6c757d;
  }
  
  .content-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
  }
  
  .action-btn {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    border-radius: 6px;
    border: none;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
  }
  
  .action-btn:hover {
    transform: translateY(-1px);
    text-decoration: none;
  }
  
  .start-btn {
    background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
    color: white;
  }
  
  .start-btn:hover {
    background: linear-gradient(135deg, #0056b3 0%, #520dc2 100%);
    color: white;
  }
  
  .completed-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
  }
  
  .completed-btn:hover {
    background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
    color: white;
  }
  
  .locked-btn {
    background: #6c757d;
    color: white;
    cursor: not-allowed;
    opacity: 0.7;
  }
  
  .download-btn {
    background: #17a2b8;
    color: white;
  }
  
  .download-btn:hover {
    background: #138496;
    color: white;
  }
  
  .watch-btn {
    background: #6f42c1;
    color: white;
  }
  
  .watch-btn:hover {
    background: #5a2d91;
    color: white;
  }
  
  .submit-btn {
    background: #fd7e14;
    color: white;
  }
  
  .submit-btn:hover {
    background: #e55a00;
    color: white;
  }
  
  .completion-checkbox {
    margin-left: 1rem;
    transform: scale(1.2);
  }
  
  .completion-status {
    font-size: 0.8rem;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-weight: 600;
    margin-left: 1rem;
  }
  
  .completion-status.completed {
    background: #d4edda;
    color: #155724;
  }
  
  .completion-status.in-progress {
    background: #fff3cd;
    color: #856404;
  }
  
  .completion-status.locked {
    background: #f8d7da;
    color: #721c24;
  }
  
  .no-content-message {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
    font-style: italic;
  }
  
  /* Module Complete Section */
  .module-complete-section {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-top: 2rem;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    border: 2px dashed #28a745;
  }
  
  .complete-module-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 30px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .complete-module-btn:hover {
    background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
  }
  
  .complete-module-btn:disabled {
    background: #6c757d;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
  }
  
  /* Video Modal */
  .video-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    z-index: 9999;
    display: none;
    justify-content: center;
    align-items: center;
  }
  
  .video-modal.show {
    display: flex;
  }
  
  .video-modal-content {
    width: 90%;
    max-width: 800px;
    background: white;
    border-radius: 15px;
    overflow: hidden;
    position: relative;
  }
  
  .video-modal-header {
    background: #f8f9fa;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .video-modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #6c757d;
  }
  
  .video-modal-close:hover {
    color: #343a40;
  }
  
  .video-frame {
    width: 100%;
    height: 450px;
    border: none;
  }
</style>
@endpush

@section('content')
<div class="student-course-container">
    <!-- Course Header -->
    <div class="course-header">
        <div class="course-info">
            <h1>{{ $program->program_name }}</h1>
            <p class="course-description">{{ $program->program_description ?? 'Explore the comprehensive curriculum designed to enhance your learning experience.' }}</p>
            <div class="course-stats">
                <div class="stat-item">
                    <i class="bi bi-graph-up"></i> Progress: {{ $progress }}%
                </div>
                <div class="stat-item">
                    <i class="bi bi-book"></i> Modules: {{ $completedModules }}/{{ $totalModules }}
                </div>
                <div class="stat-item">
                    <i class="bi bi-award"></i> Assignments: {{ count($modulesByType['assignment'] ?? []) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Section -->
    <div class="progress-section">
        <h3><i class="bi bi-trophy"></i> Your Learning Progress</h3>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" style="width: {{ $progress }}%;"></div>
        </div>
        <div class="d-flex justify-content-between">
            <span>{{ $completedModules }} of {{ $totalModules }} modules completed</span>
            <span><strong>{{ $progress }}% complete</strong></span>
        </div>
    </div>

    <!-- Learning Modules -->
    @if($totalModules > 0)
        <div class="learning-modules" id="learningModules">
            @foreach($course['modules'] as $index => $module)
                <div class="learning-module" data-module-id="{{ $module['id'] ?? $index }}">
                    <div class="module-header" onclick="toggleModule({{ $module['id'] ?? $index }})">
                        <div class="module-title-section">
                            <i class="bi bi-book"></i>
                            <div>
                                <h4>{{ $module['title'] ?? $module['name'] }}</h4>
                                @if($module['description'])
                                    <small>{{ Str::limit($module['description'], 80) }}</small>
                                @endif
                            </div>
                            <div class="module-status-badge {{ $module['is_completed'] ? 'completed' : ($module['is_locked'] ? 'locked' : '') }}">
                                @if($module['is_completed'])
                                    <i class="bi bi-check-circle"></i> Completed
                                @elseif($module['is_locked'])
                                    <i class="bi bi-lock"></i> Locked
                                @else
                                    <i class="bi bi-play-circle"></i> Available
                                @endif
                            </div>
                        </div>
                        <i class="bi bi-chevron-right module-toggle-icon"></i>
                    </div>
                    
                    <div class="module-content" id="module-content-{{ $module['id'] ?? $index }}">
                        <div class="content-list">
                            <!-- Single content item -->
                            <div class="content-item">
                                <div class="content-item-info">
                                    <span class="content-type-badge {{ $module['type'] }}">
                                        {{ strtoupper($module['type']) }}
                                    </span>
                                    <div class="content-details">
                                        <h6>{{ $module['title'] ?? $module['name'] }}</h6>
                                        @if($module['description'])
                                            <p>{{ $module['description'] }}</p>
                                        @endif
                                    </div>
                                    @if($module['is_completed'])
                                        <span class="completion-status completed">
                                            <i class="bi bi-check-circle"></i> Completed
                                        </span>
                                    @elseif($module['is_locked'])
                                        <span class="completion-status locked">
                                            <i class="bi bi-lock"></i> Locked
                                        </span>
                                    @else
                                        <span class="completion-status in-progress">
                                            <i class="bi bi-clock"></i> Available
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="content-actions">
                                    @if($module['is_locked'])
                                        <button class="action-btn locked-btn" disabled>
                                            <i class="bi bi-lock"></i> Locked
                                        </button>
                                    @else
                                        <!-- Start/Continue Learning Button -->
                                        <a href="{{ route('student.module', ['moduleId' => $module['id']]) }}" 
                                           class="action-btn {{ $module['is_completed'] ? 'completed-btn' : 'start-btn' }}">
                                            @if($module['is_completed'])
                                                <i class="bi bi-eye"></i> Review
                                            @else
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
                                                    @case('video')
                                                        <i class="bi bi-play"></i> Watch Video
                                                        @break
                                                    @default
                                                        <i class="bi bi-play-fill"></i> Start Learning
                                                @endswitch
                                            @endif
                                        </a>
                                        
                                        <!-- Download Button for files -->
                                        @if($module['attachment_url'])
                                            <a href="{{ $module['attachment_url'] }}" download 
                                               class="action-btn download-btn">
                                                <i class="bi bi-download"></i> Download
                                            </a>
                                        @endif
                                        
                                        <!-- Watch Video Button -->
                                        @if($module['video_path'] || isset($module['content_data']['video_url']))
                                            <button class="action-btn watch-btn" 
                                                    onclick="openVideoModal('{{ $module['video_path'] ?? $module['content_data']['video_url'] ?? '' }}', '{{ $module['title'] ?? $module['name'] }}')">
                                                <i class="bi bi-play"></i> Watch
                                            </button>
                                        @endif
                                        
                                        <!-- Submit Assignment Button -->
                                        @if($module['type'] == 'assignment' && !$module['is_completed'])
                                            <a href="{{ route('student.assignment.submit', ['assignmentId' => $module['id']]) }}" 
                                               class="action-btn submit-btn">
                                                <i class="bi bi-upload"></i> Submit
                                            </a>
                                        @endif
                                    @endif
                                    
                                    <!-- Mark as Complete Checkbox -->
                                    @if(!$module['is_locked'] && !$module['is_completed'])
                                        <input type="checkbox" class="completion-checkbox" 
                                               data-module-id="{{ $module['id'] }}" 
                                               onchange="markItemComplete(this, {{ $module['id'] }})"
                                               title="Mark as complete">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Module Complete Section -->
        @if($progress < 100)
            <div class="module-complete-section">
                <h4><i class="bi bi-trophy"></i> Complete Your Learning Journey</h4>
                <p>Mark the entire course as complete when you've finished all activities.</p>
                <button class="complete-module-btn" onclick="markCourseComplete()" {{ $progress < 100 ? '' : 'disabled' }}>
                    <i class="bi bi-check-circle"></i> Mark Course as Complete
                </button>
            </div>
        @endif
    @else
        <div class="no-content-message">
            <i class="bi bi-book" style="font-size: 4rem; opacity: 0.3;"></i>
            <h3>No Content Available Yet</h3>
            <p>Check back later for course materials and assignments.</p>
        </div>
    @endif
</div>

<!-- Video Modal -->
<div class="video-modal" id="videoModal">
    <div class="video-modal-content">
        <div class="video-modal-header">
            <h5 id="videoModalTitle">Video Content</h5>
            <button class="video-modal-close" onclick="closeVideoModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <iframe class="video-frame" id="videoFrame" src="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Global variables for student interface
let currentProgress = {{ $progress }};
let totalModules = {{ $totalModules }};
let completedModules = {{ $completedModules }};

// Toggle module content
function toggleModule(moduleId) {
    const content = document.getElementById(`module-content-${moduleId}`);
    const icon = content.previousElementSibling.querySelector('.module-toggle-icon');
    
    if (content.classList.contains('expanded')) {
        content.classList.remove('expanded');
        icon.classList.remove('expanded');
    } else {
        content.classList.add('expanded');
        icon.classList.add('expanded');
    }
}

// Mark individual item as complete
function markItemComplete(checkbox, moduleId) {
    if (checkbox.checked) {
        // Send API request to mark as complete
        fetch('/student/modules/mark-item-complete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                module_id: moduleId,
                program_id: {{ $program->program_id ?? 0 }}
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update visual indicators
                const moduleElement = checkbox.closest('.learning-module');
                const statusBadge = moduleElement.querySelector('.module-status-badge');
                statusBadge.className = 'module-status-badge completed';
                statusBadge.innerHTML = '<i class="bi bi-check-circle"></i> Completed';
                
                const completionStatus = checkbox.closest('.content-item').querySelector('.completion-status');
                completionStatus.className = 'completion-status completed';
                completionStatus.innerHTML = '<i class="bi bi-check-circle"></i> Completed';
                
                // Hide checkbox and update action buttons
                checkbox.style.display = 'none';
                const startBtn = checkbox.closest('.content-actions').querySelector('.start-btn');
                if (startBtn) {
                    startBtn.className = 'action-btn completed-btn';
                    startBtn.innerHTML = '<i class="bi bi-eye"></i> Review';
                }
                
                updateProgressDisplay();
                showSuccessMessage('Item marked as complete! 🎉');
            } else {
                checkbox.checked = false;
                alert('Error marking item as complete: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            checkbox.checked = false;
            alert('An error occurred while marking the item as complete');
        });
    }
}

// Mark entire course as complete
function markCourseComplete() {
    if (confirm('Are you sure you want to mark this entire course as complete? This action cannot be undone.')) {
        fetch('/student/courses/mark-complete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                program_id: {{ $program->program_id ?? 0 }}
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateProgressDisplay();
                showSuccessMessage('Course marked as complete! 🎉');
                
                // Disable the button
                const btn = document.querySelector('.complete-module-btn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="bi bi-check-circle"></i> Course Completed';
                }
                
                // Update all module status badges
                document.querySelectorAll('.module-status-badge').forEach(badge => {
                    badge.className = 'module-status-badge completed';
                    badge.innerHTML = '<i class="bi bi-check-circle"></i> Completed';
                });
            } else {
                alert('Error marking course as complete: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while marking the course as complete');
        });
    }
}

// Update progress display
function updateProgressDisplay() {
    // Update progress bar
    const progressBar = document.querySelector('.progress-bar-fill');
    if (progressBar) {
        currentProgress = Math.min(currentProgress + (100 / totalModules), 100);
        progressBar.style.width = currentProgress + '%';
    }
    
    // Update progress text
    const progressTexts = document.querySelectorAll('.d-flex.justify-content-between span');
    if (progressTexts.length >= 2) {
        completedModules = Math.min(completedModules + 1, totalModules);
        progressTexts[0].textContent = `${completedModules} of ${totalModules} modules completed`;
        progressTexts[1].innerHTML = `<strong>${Math.round(currentProgress)}% complete</strong>`;
    }
}

// Video modal functions
function openVideoModal(videoUrl, title) {
    const modal = document.getElementById('videoModal');
    const frame = document.getElementById('videoFrame');
    const titleElement = document.getElementById('videoModalTitle');
    
    titleElement.textContent = title || 'Video Content';
    
    // Support for various video formats
    if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
        // Convert YouTube URLs to embed format
        let embedUrl = videoUrl;
        if (videoUrl.includes('watch?v=')) {
            embedUrl = videoUrl.replace('watch?v=', 'embed/');
        } else if (videoUrl.includes('youtu.be/')) {
            embedUrl = videoUrl.replace('youtu.be/', 'youtube.com/embed/');
        }
        frame.src = embedUrl;
    } else {
        frame.src = videoUrl;
    }
    
    modal.classList.add('show');
}

function closeVideoModal() {
    const modal = document.getElementById('videoModal');
    const frame = document.getElementById('videoFrame');
    
    modal.classList.remove('show');
    frame.src = '';
}

// Show success message
function showSuccessMessage(message) {
    const toast = document.createElement('div');
    toast.className = 'alert alert-success position-fixed';
    toast.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-radius: 10px;
        animation: slideIn 0.3s ease;
    `;
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2" style="font-size: 1.2rem;"></i>
            ${message}
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// Initialize page functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add CSS animation for toast notifications
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Initialize progress tracking
    console.log('Student course interface loaded');
    console.log(`Progress: ${currentProgress}%, Modules: ${completedModules}/${totalModules}`);
    
    // Auto-expand first available module
    const firstModule = document.querySelector('.learning-module .module-header');
    if (firstModule) {
        setTimeout(() => {
            const moduleId = firstModule.closest('.learning-module').getAttribute('data-module-id');
            if (moduleId) {
                toggleModule(moduleId);
            }
        }, 500);
    }
    
    // Close video modal when clicking outside
    document.getElementById('videoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeVideoModal();
        }
    });
});
</script>
@endpush
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
<div class="student-course-container">
    <!-- Course Header -->
    <div class="course-header">
        <div class="course-info">
            <h1>{{ $program->program_name }}</h1>
            <p class="course-description">{{ $program->program_description ?? 'Explore the comprehensive curriculum designed to enhance your learning experience.' }}</p>
            <div class="course-stats">
                <div class="stat-item">
                    <i class="bi bi-graph-up"></i> Progress: {{ $progress }}%
                </div>
                <div class="stat-item">
                    <i class="bi bi-book"></i> Modules: {{ $completedModules }}/{{ $totalModules }}
                </div>
                <div class="stat-item">
                    <i class="bi bi-award"></i> Assignments: {{ count($modulesByType['assignment'] ?? []) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Section -->
    <div class="progress-section">
        <h3><i class="bi bi-trophy"></i> Your Learning Progress</h3>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" style="width: {{ $progress }}%;"></div>
        </div>
        <div class="d-flex justify-content-between">
            <span>{{ $completedModules }} of {{ $totalModules }} modules completed</span>
            <span><strong>{{ $progress }}% complete</strong></span>
        </div>
    </div>

    <!-- Hierarchical Module Structure -->
    @if($totalModules > 0)
        <div class="modules-hierarchy" id="modulesHierarchy">
            @php
                $groupedModules = collect($course['modules'] ?? [])->groupBy(function($module) {
                    return $module['module_name'] ?? 'Module ' . ($loop->iteration ?? 1);
                });
            @endphp

            @foreach($groupedModules as $moduleName => $moduleItems)
                @php
                    $firstItem = $moduleItems->first();
                    $moduleId = $firstItem['module_id'] ?? $loop->iteration;
                    $moduleCompleted = $moduleItems->every(function($item) {
                        return $item['is_completed'] ?? false;
                    });
                    $moduleLocked = $moduleItems->every(function($item) {
                        return $item['is_locked'] ?? false;
                    });
                @endphp
                
                <div class="module-container" data-module-id="{{ $moduleId }}">
                    <div class="module-header" onclick="toggleStudentModule({{ $moduleId }})">
                        <div class="module-title-section">
                            <i class="bi bi-grip-vertical"></i>
                            <div>
                                <h4>{{ $moduleName }}</h4>
                                @if($firstItem['module_description'] ?? false)
                                    <small>{{ $firstItem['module_description'] }}</small>
                                @endif
                            </div>
                            <div class="module-status-badge {{ $moduleCompleted ? 'completed' : ($moduleLocked ? 'locked' : '') }}">
                                @if($moduleCompleted)
                                    <i class="bi bi-check-circle"></i> Completed
                                @elseif($moduleLocked)
                                    <i class="bi bi-lock"></i> Locked
                                @else
                                    <i class="bi bi-play-circle"></i> Available
                                @endif
                            </div>
                        </div>
                        <i class="bi bi-chevron-right module-toggle-icon"></i>
                    </div>
                    
                    <div class="module-content" id="student-module-content-{{ $moduleId }}">
                        <div class="courses-list">
                            @if($moduleItems->count() > 1)
                                <!-- If multiple items, group them as course content -->
                                <div class="course-container">
                                    <div class="course-header-inner" onclick="toggleStudentCourse({{ $moduleId }}, 1)">
                                        <div>
                                            <h5><i class="bi bi-book"></i> Course Content</h5>
                                            <small>{{ $moduleItems->count() }} items</small>
                                        </div>
                                        <i class="bi bi-chevron-right course-toggle-icon"></i>
                                    </div>
                                    
                                    <div class="course-content" id="student-course-content-{{ $moduleId }}-1">
                                        @foreach($moduleItems as $item)
                                            <div class="content-item">
                                                <div class="content-item-info">
                                                    <span class="content-item-type {{ $item['type'] }}">
                                                        {{ strtoupper($item['type']) }}
                                                    </span>
                                                    <div>
                                                        <strong>{{ $item['title'] }}</strong>
                                                        @if($item['description'])
                                                            <div class="text-muted">{{ $item['description'] }}</div>
                                                        @endif
                                                    </div>
                                                    @if($item['is_completed'])
                                                        <span class="completion-status completed">
                                                            <i class="bi bi-check-circle"></i> Completed
                                                        </span>
                                                    @elseif($item['is_locked'])
                                                        <span class="completion-status locked">
                                                            <i class="bi bi-lock"></i> Locked
                                                        </span>
                                                    @else
                                                        <span class="completion-status in-progress">
                                                            <i class="bi bi-clock"></i> Available
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                <div class="content-item-actions">
                                                    @if($item['is_locked'])
                                                        <button class="btn locked-btn" disabled>
                                                            <i class="bi bi-lock"></i> Locked
                                                        </button>
                                                    @elseif($item['is_completed'])
                                                        <a href="{{ route('student.module', ['moduleId' => $item['id']]) }}" class="btn completed-btn">
                                                            <i class="bi bi-eye"></i> Review
                                                        </a>
                                                    @else
                                                        <a href="{{ route('student.module', ['moduleId' => $item['id']]) }}" class="btn start-btn">
                                                            @switch($item['type'])
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
                                                                    <i class="bi bi-play-fill"></i> Start
                                                            @endswitch
                                                        </a>
                                                    @endif
                                                    
                                                    @if($item['type'] == 'assignment')
                                                        <a href="{{ route('student.assignment.submissions', ['assignmentId' => $item['id']]) }}" class="btn btn-outline-primary btn-sm">
                                                            <i class="bi bi-file-text"></i> Submissions
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <!-- Single item - display directly -->
                                @php $item = $moduleItems->first(); @endphp
                                <div class="content-item">
                                    <div class="content-item-info">
                                        <span class="content-item-type {{ $item['type'] }}">
                                            {{ strtoupper($item['type']) }}
                                        </span>
                                        <div>
                                            <strong>{{ $item['title'] }}</strong>
                                            @if($item['description'])
                                                <div class="text-muted">{{ $item['description'] }}</div>
                                            @endif
                                        </div>
                                        @if($item['is_completed'])
                                            <span class="completion-status completed">
                                                <i class="bi bi-check-circle"></i> Completed
                                            </span>
                                        @elseif($item['is_locked'])
                                            <span class="completion-status locked">
                                                <i class="bi bi-lock"></i> Locked
                                            </span>
                                        @else
                                            <span class="completion-status in-progress">
                                                <i class="bi bi-clock"></i> Available
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="content-item-actions">
                                        @if($item['is_locked'])
                                            <button class="btn locked-btn" disabled>
                                                <i class="bi bi-lock"></i> Locked
                                            </button>
                                        @elseif($item['is_completed'])
                                            <a href="{{ route('student.module', ['moduleId' => $item['id']]) }}" class="btn completed-btn">
                                                <i class="bi bi-eye"></i> Review
                                            </a>
                                        @else
                                            <a href="{{ route('student.module', ['moduleId' => $item['id']]) }}" class="btn start-btn">
                                                @switch($item['type'])
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
                                                        <i class="bi bi-play-fill"></i> Start
                                                @endswitch
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Mark Module Complete Section -->
        @if($progress < 100)
            <div class="mark-complete-section">
                <h4><i class="bi bi-trophy"></i> Complete Your Learning Journey</h4>
                <p>Mark the entire module as complete when you've finished all activities.</p>
                <button class="mark-complete-btn" onclick="markModuleComplete()" {{ $progress < 100 ? '' : 'disabled' }}>
                    <i class="bi bi-check-circle"></i> Mark Module as Complete
                </button>
            </div>
        @endif
    @else
        <div class="no-content-message">
            <i class="bi bi-book" style="font-size: 4rem; opacity: 0.3;"></i>
            <h3>No Content Available Yet</h3>
            <p>Check back later for course materials and assignments.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Global variables for student interface
let currentProgress = {{ $progress }};
let totalModules = {{ $totalModules }};
let completedModules = {{ $completedModules }};

// Student module toggle functionality
function toggleStudentModule(moduleId) {
    const content = document.getElementById(`student-module-content-${moduleId}`);
    const icon = content.previousElementSibling.querySelector('.module-toggle-icon');
    
    if (content.classList.contains('expanded')) {
        content.classList.remove('expanded');
        icon.classList.remove('expanded');
    } else {
        content.classList.add('expanded');
        icon.classList.add('expanded');
    }
}

// Student course toggle functionality
function toggleStudentCourse(moduleId, courseId) {
    const content = document.getElementById(`student-course-content-${moduleId}-${courseId}`);
    const icon = content.previousElementSibling.querySelector('.course-toggle-icon');
    
    if (content.classList.contains('expanded')) {
        content.classList.remove('expanded');
        icon.classList.remove('expanded');
    } else {
        content.classList.add('expanded');
        icon.classList.add('expanded');
    }
}

// Mark module as complete
function markModuleComplete() {
    if (confirm('Are you sure you want to mark this module as complete? This action cannot be undone.')) {
        // Here you would make an API call to mark the module as complete
        fetch('/student/modules/mark-complete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                module_id: currentModuleId,
                program_id: {{ $program->program_id ?? 0 }}
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI to reflect completion
                updateProgressDisplay();
                showSuccessMessage('Module marked as complete! 🎉');
                
                // Disable the button
                const btn = document.querySelector('.mark-complete-btn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="bi bi-check-circle"></i> Module Completed';
                }
            } else {
                alert('Error marking module as complete: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while marking the module as complete');
        });
    }
}

// Update progress display
function updateProgressDisplay() {
    // Update progress bar
    const progressBar = document.querySelector('.progress-bar-fill');
    if (progressBar) {
        currentProgress = Math.min(currentProgress + (100 / totalModules), 100);
        progressBar.style.width = currentProgress + '%';
    }
    
    // Update progress text
    const progressTexts = document.querySelectorAll('.progress-stats span');
    if (progressTexts.length >= 2) {
        completedModules = Math.min(completedModules + 1, totalModules);
        progressTexts[0].textContent = `${completedModules} of ${totalModules} modules completed`;
        progressTexts[1].innerHTML = `<strong>${Math.round(currentProgress)}% complete</strong>`;
    }
}

// Show success message
function showSuccessMessage(message) {
    // Create success toast notification
    const toast = document.createElement('div');
    toast.className = 'alert alert-success position-fixed';
    toast.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-radius: 10px;
        animation: slideIn 0.3s ease;
    `;
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2" style="font-size: 1.2rem;"></i>
            ${message}
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// Initialize page functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add CSS animation for toast notifications
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Initialize progress tracking
    console.log('Student course interface loaded');
    console.log(`Progress: ${currentProgress}%, Modules: ${completedModules}/${totalModules}`);
    
    // Auto-expand first available module
    const firstModule = document.querySelector('.module-container .module-header');
    if (firstModule) {
        // Auto-expand first module after a short delay
        setTimeout(() => {
            const moduleId = firstModule.closest('.module-container').getAttribute('data-module-id');
            if (moduleId) {
                toggleStudentModule(moduleId);
                
                // Also expand first course if it exists
                setTimeout(() => {
                    const firstCourse = document.querySelector(`#student-module-content-${moduleId} .course-header-inner`);
                    if (firstCourse) {
                        toggleStudentCourse(moduleId, 1);
                    }
                }, 300);
            }
        }, 500);
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
