@extends('student.student-dashboard.student-dashboard-layout')

@section('title', $program->program_name ?? 'Course')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/student/student-course.css') }}">
<style>

/* --- Enable natural page scrolling --- */
html, body {
    height: auto !important;
    min-height: 100vh;
    overflow-y: auto !important;
    overflow-x: hidden;
}

/* Remove height constraints that prevent natural scrolling */
.container-fluid, 
.container-fluid.h-100,
.row.justify-content-center, 
.row.justify-content-center.h-100,
.main-content,
.content-wrapper {
    height: auto !important;
    min-height: auto !important;
    max-height: none !important;
    overflow: visible !important;
}

/* Ensure the student container can scroll naturally */
.student-course-container {
    height: auto !important;
    min-height: calc(100vh - 60px); /* Account for any header/padding */
    max-height: 100px !important;
    overflow-y: auto !important;
    overflow-x: hidden;
    background: #fff;
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px; /* Add some bottom margin for better scrolling */
}

/* Ensure course layout allows natural height */
.course-main-layout {
    height: auto !important;
    min-height: auto !important;
    max-height: none !important;
    overflow: visible !important;
}

/* Fix any modules panel height issues */
.modules-panel {
    height: auto !important;
    max-height: none !important;
    overflow-y: auto !important;
}

/* Fix content viewer height issues */
.content-viewer-panel {
    height: auto !important;
    max-height: none !important;
    overflow-y: auto !important;
}

/* Additional fixes for specific Bootstrap components */
.col-12, .col-md-11, .col-lg-10, .col-xl-9 {
    height: auto !important;
    min-height: auto !important;
    max-height: none !important;
}

/* Ensure modules hierarchy can expand naturally */
.modules-hierarchy {
    height: auto !important;
    max-height: none !important;
    overflow-y: visible !important;
}

/* Ensure content viewer body can scroll */
.content-viewer-body {
    height: auto !important;
    max-height: none !important;
    overflow-y: auto !important;
    flex-grow: 1;
}

/* Mobile responsiveness for scrolling */
@media (max-width: 992px) {
    .student-course-container {
        min-height: auto;
        padding: 10px;
    }
    
    .course-main-layout {
        flex-direction: column;
        gap: 1rem;
    }
}


</style>

@endpush

@section('content')
<div class="student-course-container">
    <!-- Header Section -->
    <div class="course-header">
        <h1><i class="bi bi-journal-bookmark"></i> {{ $program->program_name }}</h1>
        <p>Navigate through modules and view content in the viewer panel</p>
    </div>

    <!-- Main Layout - Split View with Sliding Support -->
    <div class="course-main-layout">
        <!-- Floating Toggle Button (shown when panel is collapsed) -->
        <button class="floating-toggle-btn" id="floatingToggleBtn" onclick="toggleModulesPanel()">
            <i class="bi bi-list"></i>
        </button>

        <!-- Left Panel - Module Navigation (Admin-style hierarchy) -->
        <div class="modules-panel" id="modulesPanel">
            <div class="modules-panel-header" onclick="toggleModulesPanel()">
                <div class="modules-panel-title">
                    <i class="bi bi-list-nested"></i>
                    <span>Course Modules</span>
                </div>
                <button class="panel-toggle-btn" type="button">
                    <i class="bi bi-chevron-left"></i>
                </button>
            </div>
            
            <div class="modules-hierarchy">
                @if(isset($course['modules']) && count($course['modules']) > 0)
                    @foreach($course['modules'] as $index => $module)
                    @php
                        $isAccessible = $module['is_accessible'] ?? true;
                        $lockReason = $module['lock_reason'] ?? null;
                    @endphp
                    <div class="module-container {{ !$isAccessible ? 'locked-item' : '' }}" data-module-id="{{ $module['id'] ?? $index }}">
                        <div class="module-header" onclick="{{ $isAccessible ? "toggleModule('" . ($module['id'] ?? $index) . "')" : 'return false;' }}">
                            <div class="module-title">
                                <div class="module-number {{ isset($module['is_completed']) && $module['is_completed'] ? 'completed' : '' }}">
                                    {{ $index + 1 }}
                                    @if(!$isAccessible)
                                        <i class="bi bi-lock position-absolute" style="font-size: 0.7rem; top: -2px; right: -2px;"></i>
                                    @endif
                                </div>
                                <div class="module-info">
                                    <h3>{{ $module['name'] ?? $module['title'] ?? 'Module ' . ($index + 1) }}</h3>
                                    <p>{{ $module['description'] ?? 'Click to view courses and content' }}</p>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right module-toggle" id="module-toggle-{{ $module['id'] ?? $index }}"></i>
                        </div>
                        
                        @if(!$isAccessible && $lockReason)
                        <div class="lock-overlay">
                            <i class="bi bi-lock"></i>
                            <span>{{ $lockReason }}</span>
                        </div>
                        @endif
                        
                        <div class="module-content" id="module-content-{{ $module['id'] ?? $index }}">
                            <div class="loading-indicator" id="loading-{{ $module['id'] ?? $index }}" style="display: none;">
                                <i class="bi bi-arrow-clockwise"></i>
                                <p>Loading courses...</p>
                            </div>
                            
                            <div class="courses-container" id="courses-content-{{ $module['id'] ?? $index }}">
                                <!-- Courses will be dynamically loaded here -->
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="no-modules-message">
                        <div class="text-center p-4">
                            <i class="bi bi-journal-x" style="font-size: 3rem; color: #6c757d; margin-bottom: 1rem;"></i>
                            <h5>No Modules Available</h5>
                            <p class="text-muted">This course doesn't have any modules yet.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Panel - Content Viewer -->
        <div class="content-viewer-panel">
            <div class="content-viewer-header">
                <div>
                    <h3 id="content-title">Select Content to View</h3>
                    <small id="content-subtitle">Choose any content from the left panel</small>
                </div>
                <div class="content-navigation">
                    <button class="nav-btn" id="prev-btn" onclick="navigatePrevious()" style="display: none;">
                        <i class="bi bi-chevron-left"></i> Previous
                    </button>
                    <button class="nav-btn" id="next-btn" onclick="navigateNext()" style="display: none;">
                        Next <i class="bi bi-chevron-right"></i>
                    </button>
                    <button class="nav-btn nav-btn-primary" id="submit-btn" onclick="submitAssignment()" style="display: none;">
                        <i class="bi bi-upload"></i> Submit
                    </button>
                </div>
            </div>
            
            <div class="content-viewer-body">
                <div id="content-viewer" class="content-placeholder">
                    <i class="bi bi-play-circle"></i>
                    <h2>Welcome to Your Course</h2>
                    <p>Select any module from the left panel to start learning.<br>
                    Content will appear here including videos, PDFs, assignments, and interactive lessons.</p>
                    <div class="placeholder-features">
                        <div class="feature-item">
                            <i class="bi bi-camera-video"></i>
                            <span>Video Lessons</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-file-pdf"></i>
                            <span>PDF Materials</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-clipboard-check"></i>
                            <span>Assignments</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-trophy"></i>
                            <span>Quizzes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





<!-- Student-Style Modals for Student Interface -->
<div id="videoModal" class="content-modal" style="display: none;">
    <div class="modal-backdrop" onclick="closeVideoModal()"></div>
    <div class="modal-container">
        <div class="modal-header">
            <h5 id="videoModalTitle">Video Content</h5>
            <button class="modal-close" onclick="closeVideoModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="modal-content">
            <div class="video-player">
                <iframe id="videoFrame" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Student-Style Assignment Submission Modal -->
<div id="submissionModal" class="content-modal" style="display: none;">
    <div class="modal-backdrop" onclick="closeSubmissionModal()"></div>
    <div class="modal-container submission-modal">
        <div class="modal-header">
            <h5 id="submissionModalTitle">Submit Assignment</h5>
            <button class="modal-close" onclick="closeSubmissionModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="modal-content">
            <form id="submissionForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="submission_content_id" name="content_id" value="">
                
                <div class="submission-section">
                    <label class="section-label">
                        <i class="bi bi-cloud-upload"></i>
                        Upload Your Work
                    </label>
                    <div class="file-upload-area">
                        <input type="file" id="submission_file" name="file" required>
                        <div class="upload-placeholder">
                            <i class="bi bi-file-earmark-arrow-up"></i>
                            <span>Choose file or drag & drop</span>
                        </div>
                    </div>
                    <div class="file-info" id="submission_file_info">
                        Loading submission requirements...
                    </div>
                </div>
                
                <div class="submission-section">
                    <label class="section-label" for="submission_comments">
                        <i class="bi bi-chat-left-text"></i>
                        Comments (Optional)
                    </label>
                    <textarea id="submission_comments" name="comments" rows="4" placeholder="Add any comments about your submission..."></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeSubmissionModal()">Cancel</button>
                    <button type="submit" id="submitAssignmentBtn" class="btn-primary">
                        <i class="bi bi-upload"></i>
                        Submit Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎓 Admin-Style Student Learning Platform - Initializing...');
    
    // Global variables
    let currentContentId = null;
    let currentModuleId = null;
    let currentCourseId = null;
    let contentHistory = [];
    let currentContentIndex = 0;
    let panelCollapsed = false;
    
    // Initialize sliding panel functionality
    initializeSlidingPanel();
    
    // Sliding Panel Management with Bootstrap 5 support
    function initializeSlidingPanel() {
        const modulesPanel = document.getElementById('modulesPanel');
        const floatingToggleBtn = document.getElementById('floatingToggleBtn');
        
        // Check if elements exist
        if (!modulesPanel || !floatingToggleBtn) {
            console.warn('Sliding panel elements not found');
            return;
        }
        
        // Initialize panel state
        updatePanelState();
        
        // Add keyboard support (ESC to toggle)
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                toggleModulesPanel();
            }
        });
        
        // Add window resize handler
        window.addEventListener('resize', function() {
            const floatingToggleBtn = document.getElementById('floatingToggleBtn');
            if (window.innerWidth <= 992) {
                floatingToggleBtn.style.display = 'flex';
            } else {
                floatingToggleBtn.style.display = panelCollapsed ? 'flex' : 'none';
            }
        });
    }
    
    // Toggle modules panel function (Global scope)
    window.toggleModulesPanel = function() {
        const modulesPanel = document.getElementById('modulesPanel');
        const floatingToggleBtn = document.getElementById('floatingToggleBtn');
        const contentViewer = document.querySelector('.content-viewer-panel');
        
        if (!modulesPanel) return;
        
        panelCollapsed = !panelCollapsed;
        
        // Add smooth transition classes
        modulesPanel.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
        if (contentViewer) {
            contentViewer.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
        }
        
        if (panelCollapsed) {
            // Collapse panel
            modulesPanel.classList.add('collapsed');
            floatingToggleBtn.classList.add('show');
            
            // Update content viewer
            if (contentViewer) {
                contentViewer.classList.add('expanded');
            }
            
            // Store panel state in localStorage
            localStorage.setItem('studentPanelCollapsed', 'true');
            
            console.log('📱 Modules panel collapsed - Content viewer expanded');
        } else {
            // Expand panel
            modulesPanel.classList.remove('collapsed');
            floatingToggleBtn.classList.remove('show');
            
            // Update content viewer
            if (contentViewer) {
                contentViewer.classList.remove('expanded');
            }
            
            // Store panel state in localStorage
            localStorage.setItem('studentPanelCollapsed', 'false');
            
            console.log('📱 Modules panel expanded - Content viewer normal');
        }
        
        // Add animation complete handler
        setTimeout(() => {
            modulesPanel.style.transition = '';
            if (contentViewer) {
                contentViewer.style.transition = '';
            }
        }, 400);
        
        // Dispatch custom event for other scripts
        window.dispatchEvent(new CustomEvent('panelToggled', {
            detail: { collapsed: panelCollapsed }
        }));
    };
    
    // Update panel state function
    function updatePanelState() {
        const modulesPanel = document.getElementById('modulesPanel');
        const floatingToggleBtn = document.getElementById('floatingToggleBtn');
        
        // Restore panel state from localStorage
        const savedState = localStorage.getItem('studentPanelCollapsed');
        if (savedState === 'true' && window.innerWidth > 992) {
            panelCollapsed = true;
            modulesPanel.classList.add('collapsed');
            floatingToggleBtn.classList.add('show');
        }
        
        // Handle mobile responsiveness
        if (window.innerWidth <= 992) {
            // On mobile, ensure proper behavior
            if (panelCollapsed) {
                floatingToggleBtn.style.display = 'flex';
            }
        } else {
            // On desktop, show/hide based on state
            floatingToggleBtn.style.display = panelCollapsed ? 'flex' : 'none';
        }
    }
    
    // Add smooth scroll behavior for content viewer
    function smoothScrollToContent() {
        const contentViewer = document.getElementById('content-viewer');
        if (contentViewer) {
            contentViewer.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'nearest' 
            });
        }
    }
    
    // Module Management - Admin-style toggle
    window.toggleModule = function(moduleId) {
        console.log('📚 Toggling module:', moduleId);
        
        const moduleContent = document.getElementById(`module-content-${moduleId}`);
        const moduleToggle = document.getElementById(`module-toggle-${moduleId}`);
        const moduleHeader = document.querySelector(`[data-module-id="${moduleId}"] .module-header`);
        
        if (!moduleContent) return;
        
        const isExpanding = !moduleContent.classList.contains('expanded');
        
        // Close all other modules (admin-style behavior)
        document.querySelectorAll('.module-content.expanded').forEach(el => {
            if (el.id !== `module-content-${moduleId}`) {
                el.classList.remove('expanded');
                const otherModuleId = el.id.replace('module-content-', '');
                const otherToggle = document.getElementById(`module-toggle-${otherModuleId}`);
                const otherHeader = document.querySelector(`[data-module-id="${otherModuleId}"] .module-header`);
                if (otherToggle) otherToggle.classList.remove('expanded');
                if (otherHeader) otherHeader.classList.remove('active');
            }
        });
        
        // Toggle current module
        moduleContent.classList.toggle('expanded');
        if (moduleToggle) moduleToggle.classList.toggle('expanded');
        if (moduleHeader) moduleHeader.classList.toggle('active');
        
        if (isExpanding) {
            loadModuleCourses(moduleId);
            currentModuleId = moduleId;
        }
    };
    
    // Load module courses
    function loadModuleCourses(moduleId) {
        const loadingIndicator = document.getElementById(`loading-${moduleId}`);
        const coursesContent = document.getElementById(`courses-content-${moduleId}`);
        
        if (!loadingIndicator || !coursesContent) return;
        
        loadingIndicator.style.display = 'block';
        coursesContent.style.display = 'none';
        
        fetch(`/student/module/${moduleId}/courses`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.courses) {
                    displayCourses(moduleId, data.courses);
                } else {
                    showNoCoursesMessage(moduleId);
                }
            })
            .catch(error => {
                console.error('Error loading courses:', error);
                showErrorMessage(moduleId);
            })
            .finally(() => {
                loadingIndicator.style.display = 'none';
                coursesContent.style.display = 'block';
            });
    }
    
    // Display courses in admin-style hierarchical structure
    function displayCourses(moduleId, courses) {
        const coursesContent = document.getElementById(`courses-content-${moduleId}`);
        
        let coursesHtml = '';
        courses.forEach((course, index) => {
            const courseId = course.course_id || course.subject_id;
            const isAccessible = course.is_accessible !== false;
            const lockReason = course.lock_reason || null;
            
            // Check for content in both lessons and direct content items
            const hasLessonContent = course.lessons && course.lessons.some(lesson => lesson.content_items && lesson.content_items.length > 0);
            const hasDirectContent = course.direct_content_items && course.direct_content_items.length > 0;
            const hasContent = hasLessonContent || hasDirectContent;
            
            const lockedClass = !isAccessible ? 'locked-item' : '';
            const clickHandler = isAccessible ? `toggleCourse('${moduleId}', '${courseId}')` : 'return false;';
            
            coursesHtml += `
                <div class="course-item ${lockedClass}" data-course-id="${courseId}">
                    <div class="course-header" onclick="${clickHandler}">
                        <div class="course-info">
                            <div class="course-icon">
                                <i class="bi bi-book"></i>
                                ${!isAccessible ? '<i class="bi bi-lock position-absolute" style="font-size: 0.6rem; top: -2px; right: -2px; color: #dc3545;"></i>' : ''}
                            </div>
                            <div class="course-details">
                                <h5>${course.course_name || course.subject_name}</h5>
                                <small>${course.course_description || course.subject_description || 'Course materials and activities'}</small>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right course-toggle" id="course-toggle-${moduleId}-${courseId}"></i>
                    </div>
                    
                    ${!isAccessible && lockReason ? `
                    <div class="lock-overlay ${getLockType(lockReason)}">
                        <i class="bi bi-lock"></i>
                        <span>${lockReason}</span>
                    </div>
                    ` : ''}
                    
                    <div class="content-list" id="content-list-${moduleId}-${courseId}">
                        ${hasContent ? generateContentListHtml(course, moduleId, courseId) : '<div style="padding: 1rem; color: #6c757d; text-align: center;">No content available</div>'}
                    </div>
                </div>
            `;
        });
        
        coursesContent.innerHTML = coursesHtml;
    }

    // Helper function to determine lock type for styling
    function getLockType(lockReason) {
        if (lockReason.includes('Available on') || lockReason.includes('available on')) {
            return 'scheduled';
        } else if (lockReason.includes('Complete') || lockReason.includes('complete')) {
            return 'prerequisite';
        }
        return '';
    }
    
    // Toggle course content - admin-style
    window.toggleCourse = function(moduleId, courseId) {
        const contentList = document.getElementById(`content-list-${moduleId}-${courseId}`);
        const courseHeader = document.querySelector(`#content-list-${moduleId}-${courseId}`).previousElementSibling;
        const courseToggle = document.getElementById(`course-toggle-${moduleId}-${courseId}`);
        
        // Close other course contents in this module
        document.querySelectorAll(`[id^="content-list-${moduleId}-"]`).forEach(el => {
            if (el.id !== `content-list-${moduleId}-${courseId}`) {
                el.classList.remove('expanded');
                const otherCourseId = el.id.replace(`content-list-${moduleId}-`, '');
                const otherToggle = document.getElementById(`course-toggle-${moduleId}-${otherCourseId}`);
                const otherHeader = el.previousElementSibling;
                if (otherToggle) otherToggle.classList.remove('expanded');
                if (otherHeader) otherHeader.classList.remove('active');
            }
        });
        
        // Toggle current course
        if (contentList) contentList.classList.toggle('expanded');
        if (courseHeader) courseHeader.classList.toggle('active');
        if (courseToggle) courseToggle.classList.toggle('expanded');
        
        currentCourseId = courseId;
    };
    
    // Generate content list HTML - admin-style structure
    function generateContentListHtml(course, moduleId, courseId) {
        let html = '';
        
        // Add lessons and their content items
        if (course.lessons && course.lessons.length > 0) {
            course.lessons.forEach((lesson, lessonIndex) => {
                if (lesson.content_items && lesson.content_items.length > 0) {
                    // Add lesson header
                    html += `
                        <div class="lesson-section">
                            <div class="lesson-title">
                                <i class="bi bi-journal-text"></i>
                                ${lesson.lesson_name}
                            </div>
                            ${lesson.lesson_description ? `<div class="lesson-description">${lesson.lesson_description}</div>` : ''}
                        </div>
                    `;
                    
                    // Add lesson content items
                    lesson.content_items.forEach((item, index) => {
                        const typeIcon = getContentTypeIcon(item.content_type);
                        const statusIcon = item.completed ? '<div class="progress-ring completed"><i class="bi bi-check"></i></div>' : '<div class="progress-ring"></div>';
                        const isAccessible = item.is_accessible !== false;
                        const lockReason = item.lock_reason || null;
                        const lockedClass = !isAccessible ? 'locked-item' : '';
                        const clickHandler = isAccessible ? `loadContent('${item.id}', '${item.content_type}', '${item.content_title}')` : 'return false;';
                        
                        html += `
                            <div class="content-item ${item.completed ? 'completed' : ''} ${lockedClass}" onclick="${clickHandler}">
                                <div class="content-type-icon">
                                    <i class="bi ${typeIcon}"></i>
                                    ${!isAccessible ? '<i class="bi bi-lock position-absolute" style="font-size: 0.6rem; top: -2px; right: -2px; color: #dc3545;"></i>' : ''}
                                </div>
                                <span class="content-type-badge ${(item.content_type || 'content').toLowerCase()}">${(item.content_type || 'CONTENT').toUpperCase()}</span>
                                <span class="content-title">${item.content_title}</span>
                                <div class="content-status">${statusIcon}</div>
                                ${!isAccessible && lockReason ? `
                                <div class="lock-overlay ${getLockType(lockReason)}">
                                    <i class="bi bi-lock"></i>
                                    <span>${lockReason}</span>
                                </div>
                                ` : ''}
                            </div>
                        `;
                    });
                }
            });
        }
        
        // Add direct content items (not in lessons)
        if (course.direct_content_items && course.direct_content_items.length > 0) {
            course.direct_content_items.forEach((item, index) => {
                const typeIcon = getContentTypeIcon(item.content_type);
                const statusIcon = item.completed ? '<div class="progress-ring completed"><i class="bi bi-check"></i></div>' : '<div class="progress-ring"></div>';
                const isAccessible = item.is_accessible !== false;
                const lockReason = item.lock_reason || null;
                const lockedClass = !isAccessible ? 'locked-item' : '';
                const clickHandler = isAccessible ? `loadContent('${item.id}', '${item.content_type}', '${item.content_title}')` : 'return false;';
                
                html += `
                    <div class="content-item ${item.completed ? 'completed' : ''} ${lockedClass}" onclick="${clickHandler}">
                        <div class="content-type-icon">
                            <i class="bi ${typeIcon}"></i>
                            ${!isAccessible ? '<i class="bi bi-lock position-absolute" style="font-size: 0.6rem; top: -2px; right: -2px; color: #dc3545;"></i>' : ''}
                        </div>
                        <span class="content-type-badge ${(item.content_type || 'content').toLowerCase()}">${(item.content_type || 'CONTENT').toUpperCase()}</span>
                        <span class="content-title">${item.content_title}</span>
                        <div class="content-status">${statusIcon}</div>
                        ${!isAccessible && lockReason ? `
                        <div class="lock-overlay ${getLockType(lockReason)}">
                            <i class="bi bi-lock"></i>
                            <span>${lockReason}</span>
                        </div>
                        ` : ''}
                    </div>
                `;
            });
        }
        
        return html;
    }
    
    // Get content type icon
    function getContentTypeIcon(type) {
        const icons = {
            'video': 'bi-play-circle',
            'pdf': 'bi-file-pdf',
            'lesson': 'bi-journal-text',
            'assignment': 'bi-pencil-square',
            'quiz': 'bi-question-circle',
            'test': 'bi-clipboard-check',
            'link': 'bi-link-45deg'
        };
        return icons[type.toLowerCase()] || 'bi-file-text';
    }
    
    // Load content in main viewer
    window.loadContent = function(contentId, contentType, contentTitle) {
        console.log('📖 Loading content:', contentId, contentType, contentTitle);
        
        // Update header
        document.getElementById('content-title').textContent = contentTitle;
        document.getElementById('content-subtitle').textContent = `${contentType.toUpperCase()} • Module ${currentModuleId}`;
        
        // Show navigation buttons
        updateNavigationButtons();
        
        // Load content based on type
        const viewer = document.getElementById('content-viewer');
        
        switch(contentType.toLowerCase()) {
            case 'video':
                loadVideoContent(contentId, viewer);
                break;
            case 'pdf':
                loadPdfContent(contentId, viewer);
                break;
            case 'assignment':
                loadAssignmentContent(contentId, viewer);
                break;
            case 'lesson':
                loadLessonContent(contentId, viewer);
                break;
            case 'link':
                loadLinkContent(contentId, viewer);
                break;
            default:
                loadDefaultContent(contentId, viewer);
        }
        
        // Update current content tracking
        currentContentId = contentId;
        
        // Mark content as active
        document.querySelectorAll('.content-item.active').forEach(el => el.classList.remove('active'));
        document.querySelector(`[onclick*="${contentId}"]`).classList.add('active');
    };
    
    // Load video content
    function loadVideoContent(contentId, viewer) {
        fetch(`/student/content/${contentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const videoUrl = data.content.content_url || data.content.attachment_path;
                    viewer.innerHTML = `
                        <div class="video-container">
                            <iframe class="video-frame" src="${videoUrl}" allowfullscreen></iframe>
                        </div>
                    `;
                }
            });
    }
    
    // Load PDF content
    function loadPdfContent(contentId, viewer) {
        fetch(`/student/content/${contentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const pdfUrl = `/storage/${data.content.attachment_path}`;
                    viewer.innerHTML = `
                        <div class="pdf-viewer">
                            <iframe class="content-frame" src="${pdfUrl}"></iframe>
                        </div>
                    `;
                }
            });
    }
    
    // Load assignment content
    function loadAssignmentContent(contentId, viewer) {
        console.log('📖 Loading assignment content:', contentId);
        document.getElementById('submit-btn').style.display = 'inline-block';
        
        fetch(`/student/content/${contentId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const assignment = data.content;
                    let contentHtml = `
                        <div class="lesson-content">
                            <h1>${assignment.content_title}</h1>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> This is an assignment. Complete the requirements and submit your work.
                            </div>
                            <div class="assignment-details">
                                <p><strong>Description:</strong> ${assignment.content_description || 'No description provided'}</p>
                                ${assignment.due_date ? `<p><strong>Due Date:</strong> ${new Date(assignment.due_date).toLocaleDateString()}</p>` : ''}
                                ${assignment.submission_instructions ? `<div class="instructions"><h4>Instructions:</h4><p>${assignment.submission_instructions}</p></div>` : ''}
                    `;
                    
                    // Add file attachment if available
                    if (assignment.attachment_path) {
                        const fileName = assignment.attachment_path.split('/').pop();
                        contentHtml += `
                            <div class="attachment-section mt-3">
                                <h5><i class="bi bi-paperclip"></i> Attachment</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <a href="/storage/${assignment.attachment_path}" target="_blank" class="btn btn-outline-primary">
                                            <i class="bi bi-download"></i> Download ${fileName}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    contentHtml += `
                            </div>
                        </div>
                    `;
                    
                    viewer.innerHTML = contentHtml;
                } else {
                    viewer.innerHTML = `<div class="alert alert-danger">Error loading assignment: ${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error loading assignment:', error);
                viewer.innerHTML = `<div class="alert alert-danger">Error loading assignment: ${error.message}</div>`;
            });
    }
    
    // Load lesson content
    function loadLessonContent(contentId, viewer) {
        console.log('📖 Loading lesson content:', contentId);
        
        fetch(`/student/content/${contentId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const lesson = data.content;
                    let contentHtml = `
                        <div class="lesson-content">
                            <h1>${lesson.content_title}</h1>
                            <div class="lesson-body">
                                ${lesson.content_description ? `<p>${lesson.content_description}</p>` : ''}
                                ${lesson.content_text ? `<div>${lesson.content_text}</div>` : ''}
                            </div>
                    `;
                    
                    // Add file attachment if available
                    if (lesson.attachment_path) {
                        const fileName = lesson.attachment_path.split('/').pop();
                        const fileExt = fileName.split('.').pop().toLowerCase();
                        const fileUrl = `/storage/${lesson.attachment_path}`;
                        
                        contentHtml += `
                            <div class="attachment-section mt-3">
                                <h5><i class="bi bi-paperclip"></i> Lesson Material</h5>
                                <div class="card">
                                    <div class="card-body">
                        `;
                        
                        // Handle different file types
                        if (['pdf'].includes(fileExt)) {
                            contentHtml += `
                                <div class="pdf-viewer mb-3" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-0"><i class="bi bi-file-pdf text-danger"></i> ${fileName}</h6>
                                            <small class="text-muted">PDF Document</small>
                                        </div>
                                        <a href="${fileUrl}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </div>
                                    <div class="pdf-container" style="border: 2px solid #dee2e6; border-radius: 6px; overflow: hidden; background: white;">
                                        <iframe src="${fileUrl}" 
                                                width="100%" 
                                                height="700px" 
                                                style="border: none; display: block;"
                                                frameborder="0">
                                            <p>Your browser does not support PDF viewing. <a href="${fileUrl}" target="_blank">Download the PDF</a></p>
                                        </iframe>
                                    </div>
                                </div>
                            `;
                        } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExt)) {
                            contentHtml += `
                                <div class="image-viewer mb-3" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-0"><i class="bi bi-image text-primary"></i> ${fileName}</h6>
                                            <small class="text-muted">Image File</small>
                                        </div>
                                        <a href="${fileUrl}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </div>
                                    <div class="text-center">
                                        <img src="${fileUrl}" class="img-fluid" alt="${fileName}" 
                                             style="max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 5px; max-height: 600px;">
                                    </div>
                                </div>
                            `;
                        } else if (['mp4', 'webm', 'ogg'].includes(fileExt)) {
                            contentHtml += `
                                <div class="video-viewer mb-3" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-0"><i class="bi bi-camera-video text-success"></i> ${fileName}</h6>
                                            <small class="text-muted">Video File</small>
                                        </div>
                                        <a href="${fileUrl}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </div>
                                    <video controls style="width: 100%; max-height: 500px; border: 1px solid #ddd; border-radius: 5px;">
                                        <source src="${fileUrl}" type="video/${fileExt}">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            `;
                        } else if (['mp3', 'wav', 'ogg'].includes(fileExt)) {
                            contentHtml += `
                                <div class="audio-viewer mb-3" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-0"><i class="bi bi-music-note text-info"></i> ${fileName}</h6>
                                            <small class="text-muted">Audio File</small>
                                        </div>
                                        <a href="${fileUrl}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </div>
                                    <audio controls style="width: 100%; border: 1px solid #ddd; border-radius: 5px;">
                                        <source src="${fileUrl}" type="audio/${fileExt}">
                                        Your browser does not support the audio tag.
                                    </audio>
                                </div>
                            `;
                        } else if (['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'].includes(fileExt)) {
                            contentHtml += `
                                <div class="document-viewer mb-3" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <div class="text-center p-4 border rounded bg-white">
                                        <i class="bi bi-file-earmark-word text-primary" style="font-size: 3rem;"></i>
                                        <h6 class="mt-3 mb-2">${fileName}</h6>
                                        <p class="text-muted mb-3">Document file - Click download to view</p>
                                        <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                                            <i class="bi bi-download"></i> Download Document
                                        </a>
                                    </div>
                                </div>
                            `;
                        } else {
                            contentHtml += `
                                <div class="file-viewer mb-3" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <div class="text-center p-4 border rounded bg-white">
                                        <i class="bi bi-file-earmark text-muted" style="font-size: 3rem;"></i>
                                        <h6 class="mt-3 mb-2">${fileName}</h6>
                                        <p class="text-muted mb-3">File preview not available</p>
                                        <a href="${fileUrl}" target="_blank" class="btn btn-outline-primary">
                                            <i class="bi bi-download"></i> Download File
                                        </a>
                                    </div>
                                </div>
                            `;
                        }
                        
                        contentHtml += `
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    // Add external link if available
                    if (lesson.content_url) {
                        contentHtml += `
                            <div class="link-section mt-3">
                                <h5><i class="bi bi-link-45deg"></i> External Resource</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <a href="${lesson.content_url}" target="_blank" class="btn btn-primary">
                                            <i class="bi bi-box-arrow-up-right"></i> Open Link
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    contentHtml += `</div>`;
                    viewer.innerHTML = contentHtml;
                } else {
                    viewer.innerHTML = `<div class="alert alert-danger">Error loading lesson: ${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error loading lesson:', error);
                viewer.innerHTML = `<div class="alert alert-danger">Error loading lesson: ${error.message}</div>`;
            });
    }
    
    // Navigation functions
    function updateNavigationButtons() {
        document.getElementById('prev-btn').style.display = 'inline-block';
        document.getElementById('next-btn').style.display = 'inline-block';
        document.getElementById('submit-btn').style.display = 'none';
    }
    
    window.navigatePrevious = function() {
        // Implementation for previous content
        console.log('Navigate to previous content');
    };
    
    window.navigateNext = function() {
        // Implementation for next content  
        console.log('Navigate to next content');
    };
    
    window.submitAssignment = function() {
        if (currentContentId) {
            openSubmissionModal(currentContentId, document.getElementById('content-title').textContent);
        }
    };
    
    // Modal functions
    window.openVideoModal = function(videoUrl, title) {
        const modal = document.getElementById('videoModal');
        const frame = document.getElementById('videoFrame');
        const titleElement = document.getElementById('videoModalTitle');
        
        if (modal && frame && titleElement) {
            titleElement.textContent = title;
            frame.src = videoUrl;
            modal.style.display = 'block';
        }
    };
    
    window.closeVideoModal = function() {
        const modal = document.getElementById('videoModal');
        const frame = document.getElementById('videoFrame');
        
        if (modal) modal.style.display = 'none';
        if (frame) frame.src = '';
    };
    
    window.openSubmissionModal = function(contentId, contentTitle) {
        const modal = document.getElementById('submissionModal');
        const titleElement = document.getElementById('submissionModalTitle');
        const contentIdInput = document.getElementById('submission_content_id');
        
        if (modal && titleElement && contentIdInput) {
            titleElement.textContent = `Submit: ${contentTitle}`;
            contentIdInput.value = contentId;
            modal.style.display = 'block';
        }
    };
    
    window.closeSubmissionModal = function() {
        const modal = document.getElementById('submissionModal');
        if (modal) modal.style.display = 'none';
    };
    
    // Handle submission form
    document.getElementById('submissionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = document.getElementById('submitAssignmentBtn');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting...';
        
        fetch('/student/submit-assignment', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Assignment submitted successfully! 🎉');
                closeSubmissionModal();
            } else {
                alert('Error: ' + (data.message || 'Failed to submit assignment'));
            }
        })
        .catch(error => {
            console.error('Error submitting assignment:', error);
            alert('An error occurred while submitting. Please try again.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
    
    // Helper functions
    function showNoCoursesMessage(moduleId) {
        const coursesContent = document.getElementById(`courses-content-${moduleId}`);
        coursesContent.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">No courses available</div>';
    }
    
    function showErrorMessage(moduleId) {
        const coursesContent = document.getElementById(`courses-content-${moduleId}`);
        coursesContent.innerHTML = '<div style="padding: 1rem; color: #dc3545; text-align: center;">Error loading courses</div>';
    }
    
    console.log('✅ Student Learning Platform - Ready!');
});
</script>
@endpush
