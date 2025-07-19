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
  
  /* Module Container Styles */
  .modules-hierarchy {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }
  
  .module-container {
    border: 2px solid #e1e5e9;
    border-radius: 15px;
    background: white;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
  }
  
  .module-container:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
  }
  
  .course-container {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }
  
  .course-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 1.5rem 2rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
  }
  
  .course-header:hover {
    background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
  }
  
  .course-header h5 {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0;
  }
  
  .course-content {
    display: none;
    padding: 1.5rem;
    background: #ffffff;
  }
  
  .course-content.expanded {
    display: block;
  }
  
  .course-toggle-icon {
    transition: transform 0.3s ease;
    font-size: 1.1rem;
  }
  
  .course-toggle-icon.expanded {
    transform: rotate(90deg);
  }
  
  .content-item-type {
    background: #007bff;
    color: white;
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
  }
  
  .content-item-type.assignment { background: #dc3545; }
  .content-item-type.pdf { background: #fd7e14; }
  .content-item-type.lesson { background: #17a2b8; }
  .content-item-type.quiz { background: #6f42c1; }
  .content-item-type.test { background: #e83e8c; }
  .content-item-type.link { background: #20c997; }
  .content-item-type.module { background: #007bff; }
  
  .content-item-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
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
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
  }
  
  .content-item:hover {
    background: #f1f3f4;
    border-color: #dee2e6;
    transform: translateX(3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
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
  
  .content-type-badge.assignment { background: linear-gradient(135deg, #dc3545, #c82333); }
  .content-type-badge.pdf { background: linear-gradient(135deg, #fd7e14, #e55a00); }
  .content-type-badge.video { background: linear-gradient(135deg, #6f42c1, #5a2d91); }
  .content-type-badge.lesson { background: linear-gradient(135deg, #17a2b8, #138496); }
  .content-type-badge.quiz { background: linear-gradient(135deg, #e83e8c, #d91a72); }
  .content-type-badge.test { background: linear-gradient(135deg, #20c997, #1e7e34); }
  .content-type-badge.link { background: linear-gradient(135deg, #007bff, #0056b3); }
  
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
    padding: 0.6rem 1.2rem;
    font-size: 0.875rem;
    border-radius: 8px;
    border: none;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  .action-btn:hover {
    transform: translateY(-1px);
    text-decoration: none;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
  }
  
  .start-btn {
    background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
    color: white;
  }
  
  .start-btn:hover {
    background: linear-gradient(135deg, #0056b3 0%, #520dc2 100%);
    color: white;
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
  
  /* Loading Indicator */
  .loading-indicator {
    padding: 2rem;
    text-align: center;
    color: #6c757d;
  }

  /* Lesson Section */
  .lesson-section {
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #007bff;
  }

  .lesson-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .lesson-description {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 1rem;
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
        <div class="modules-hierarchy" id="modulesHierarchy">
            @foreach($course['modules'] as $index => $module)
                <div class="module-container" data-module-id="{{ $module['id'] ?? $index }}">
                    <div class="module-header" onclick="toggleModule('{{ $module['id'] ?? $index }}')">
                        <div class="module-title-section">
                            <i class="bi bi-grip-vertical"></i>
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
                        <i class="bi bi-chevron-right module-toggle-icon" id="module-toggle-icon-{{ $module['id'] ?? $index }}"></i>
                    </div>
                    
                    <div class="module-content" id="module-content-{{ $module['id'] ?? $index }}">
                        <div class="courses-list" id="courses-list-{{ $module['id'] ?? $index }}" style="padding: 1.5rem;">
                            <!-- Loading indicator -->
                            <div class="loading-indicator" id="loading-{{ $module['id'] ?? $index }}">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Loading course content...</p>
                                </div>
                            </div>
                            
                            <!-- Courses content will be loaded here -->
                            <div class="courses-content" id="courses-content-{{ $module['id'] ?? $index }}" style="display: none;">
                                <!-- Content will be dynamically loaded -->
                            </div>
                        </div>

                        <!-- Module Complete Section -->
                        @if(!$module['is_completed'])
                            <div class="module-complete-section" style="margin: 1.5rem;">
                                <i class="bi bi-trophy" style="font-size: 2.5rem; color: #28a745; margin-bottom: 1rem;"></i>
                                <h4 style="color: #28a745; margin-bottom: 0.5rem;">Complete This Module</h4>
                                <p style="color: #6c757d; margin-bottom: 1.5rem;">Mark this module as complete when you've finished all activities.</p>
                                <button class="complete-module-btn" onclick="markModuleComplete({{ $module['id'] ?? $index }})">
                                    <i class="bi bi-check-circle"></i> Mark Module as Complete
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
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
            <button type="button" class="video-modal-close" onclick="closeVideoModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <iframe class="video-frame" id="videoFrame" src="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('📚 Student Course Page - Initializing...');
    
    // Constants
    const programId = {{ $program->program_id ?? 0 }};
    
    // Core Toggle Functions
    window.toggleModule = function(moduleId) {
        console.log('🔄 Toggling module:', moduleId);
        const content = document.getElementById(`module-content-${moduleId}`);
        const icon = document.getElementById(`module-toggle-icon-${moduleId}`);
        
        if (content) {
            const isExpanding = !content.classList.contains('expanded');
            content.classList.toggle('expanded');
            console.log('✅ Module content toggled - Expanded:', isExpanding);
            
            if (isExpanding) {
                loadModuleCourses(moduleId);
            }
        }
        
        if (icon) {
            icon.classList.toggle('expanded');
        }
    };

    window.toggleCourse = function(courseId) {
        const content = document.getElementById(`course-content-${courseId}`);
        const icon = document.getElementById(`course-toggle-icon-${courseId}`);
        if (content) content.classList.toggle('expanded');
        if (icon) icon.classList.toggle('expanded');
    };

    // Load Module Courses Function
    function loadModuleCourses(moduleId) {
        const loadingIndicator = document.getElementById(`loading-${moduleId}`);
        const coursesContent = document.getElementById(`courses-content-${moduleId}`);
        
        if (!loadingIndicator || !coursesContent) {
            console.error('Loading elements not found for module:', moduleId);
            return;
        }

        loadingIndicator.style.display = 'block';
        coursesContent.style.display = 'none';

        fetch(`/student/module/${moduleId}/courses`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('📚 Loaded courses for module:', moduleId, data);
                
                if (data.success && data.courses) {
                    displayCourses(moduleId, data.courses);
                } else {
                    showNoCourseContent(moduleId);
                }
            })
            .catch(error => {
                console.error('❌ Error loading courses:', error);
                showErrorContent(moduleId, error.message);
            })
            .finally(() => {
                loadingIndicator.style.display = 'none';
                coursesContent.style.display = 'block';
            });
    }

    // Display Courses Function
    function displayCourses(moduleId, courses) {
        const coursesContent = document.getElementById(`courses-content-${moduleId}`);
        
        if (!courses || courses.length === 0) {
            showNoCourseContent(moduleId);
            return;
        }

        let coursesHtml = '';
        
        courses.forEach((course, index) => {
            const courseId = `${moduleId}-${course.course_id}`;
            const hasContent = (course.lessons && course.lessons.length > 0) || 
                              (course.direct_content_items && course.direct_content_items.length > 0);
            
            coursesHtml += `
                <div class="course-container">
                    <div class="course-header" onclick="toggleCourse('${courseId}')">
                        <div>
                            <h5><i class="bi bi-book"></i> ${course.course_name}</h5>
                            <small>${course.course_description || 'Course materials and activities'}</small>
                        </div>
                        <i class="bi bi-chevron-right course-toggle-icon" id="course-toggle-icon-${courseId}"></i>
                    </div>
                    
                    <div class="course-content" id="course-content-${courseId}">
                        ${hasContent ? generateCourseContentHtml(course) : '<div class="no-content-message"><p>No content available for this course yet.</p></div>'}
                    </div>
                </div>
            `;
        });
        
        coursesContent.innerHTML = coursesHtml;
    }

    // Generate Course Content HTML
    function generateCourseContentHtml(course) {
        let contentHtml = '<div class="content-list">';
        
        if (course.direct_content_items && course.direct_content_items.length > 0) {
            course.direct_content_items.forEach(item => {
                contentHtml += generateContentItemHtml(item);
            });
        }
        
        if (course.lessons && course.lessons.length > 0) {
            course.lessons.forEach(lesson => {
                contentHtml += `
                    <div class="lesson-section">
                        <h6 class="lesson-title"><i class="bi bi-play-circle"></i> ${lesson.lesson_name}</h6>
                        ${lesson.lesson_description ? `<p class="lesson-description">${lesson.lesson_description}</p>` : ''}
                `;
                
                if (lesson.content_items && lesson.content_items.length > 0) {
                    lesson.content_items.forEach(item => {
                        contentHtml += generateContentItemHtml(item);
                    });
                }
                
                contentHtml += '</div>';
            });
        }
        
        contentHtml += '</div>';
        return contentHtml;
    }

    // Generate Content Item HTML
    function generateContentItemHtml(item) {
        const contentTypeClass = item.content_type.toLowerCase();
        const actionButton = generateActionButton(item);
        
        return `
            <div class="content-item">
                <div class="content-item-info">
                    <div class="content-type-badge ${contentTypeClass}">
                        ${item.content_type.toUpperCase()}
                    </div>
                    <div class="content-details">
                        <h6>${item.content_title}</h6>
                        <p>${item.content_description || 'No description available'}</p>
                        ${item.due_date ? `<small class="text-muted"><i class="bi bi-calendar"></i> Due: ${new Date(item.due_date).toLocaleDateString()}</small>` : ''}
                    </div>
                </div>
                <div class="content-actions">
                    ${actionButton}
                    ${item.is_required ? '<span class="badge bg-warning text-dark">Required</span>' : ''}
                </div>
            </div>
        `;
    }

    // Generate Action Button
    function generateActionButton(item) {
        switch (item.content_type.toLowerCase()) {
            case 'assignment':
                return `<a href="#" class="action-btn submit-btn">
                    <i class="bi bi-upload"></i> Submit
                </a>`;
            case 'pdf':
                return `<a href="${item.attachment_path ? '/storage/' + item.attachment_path : '#'}" target="_blank" class="action-btn download-btn">
                    <i class="bi bi-download"></i> Download
                </a>`;
            case 'video':
                return `<button class="action-btn watch-btn" onclick="openVideoModal('${item.content_url || item.attachment_path}', '${item.content_title}')">
                    <i class="bi bi-play"></i> Watch
                </button>`;
            case 'lesson':
                return `<a href="#" class="action-btn start-btn">
                    <i class="bi bi-play-circle"></i> Start
                </a>`;
            case 'quiz':
            case 'test':
                return `<a href="#" class="action-btn start-btn">
                    <i class="bi bi-pencil-square"></i> Take ${item.content_type}
                </a>`;
            case 'link':
                const linkData = item.content_data ? JSON.parse(item.content_data) : {};
                const linkUrl = linkData.url || '#';
                return `<a href="${linkUrl}" target="_blank" class="action-btn start-btn">
                    <i class="bi bi-link-45deg"></i> Open Link
                </a>`;
            default:
                return `<a href="#" class="action-btn start-btn">
                    <i class="bi bi-eye"></i> View
                </a>`;
        }
    }

    // Error and No Content Functions
    function showNoCourseContent(moduleId) {
        const coursesContent = document.getElementById(`courses-content-${moduleId}`);
        coursesContent.innerHTML = `
            <div class="no-content-message">
                <i class="bi bi-book" style="font-size: 3rem; opacity: 0.3;"></i>
                <h5>No Courses Available</h5>
                <p>This module doesn't have any courses or content yet.</p>
            </div>
        `;
    }

    function showErrorContent(moduleId, errorMessage) {
        const coursesContent = document.getElementById(`courses-content-${moduleId}`);
        coursesContent.innerHTML = `
            <div class="no-content-message">
                <i class="bi bi-exclamation-triangle" style="font-size: 3rem; color: #dc3545;"></i>
                <h5>Error Loading Content</h5>
                <p>There was an error loading the course content: ${errorMessage}</p>
                <button class="btn btn-outline-primary btn-sm" onclick="loadModuleCourses(${moduleId})">
                    <i class="bi bi-arrow-clockwise"></i> Retry
                </button>
            </div>
        `;
    }

    // Video Modal Functions
    window.openVideoModal = function(videoUrl, title) {
        const modal = document.getElementById('videoModal');
        const frame = document.getElementById('videoFrame');
        const titleElement = document.getElementById('videoModalTitle');
        
        if (!modal || !frame || !titleElement) return;

        titleElement.textContent = title || 'Video Content';
        
        let embedUrl = videoUrl;
        if (videoUrl.includes('youtube.com/watch?v=')) {
            embedUrl = videoUrl.replace('watch?v=', 'embed/');
        } else if (videoUrl.includes('youtu.be/')) {
            embedUrl = videoUrl.replace('youtu.be/', 'youtube.com/embed/');
        }
        frame.src = embedUrl;
        
        modal.classList.add('show');
    };

    window.closeVideoModal = function() {
        const modal = document.getElementById('videoModal');
        const frame = document.getElementById('videoFrame');
        
        if (modal) modal.classList.remove('show');
        if (frame) frame.src = '';
    };

    // Module Complete Function
    window.markModuleComplete = function(moduleId) {
        if (!confirm('Are you sure you want to mark this module as complete?')) {
            return;
        }

        fetch(`/student/module/${moduleId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ program_id: programId })
        })
        .then(res => res.ok ? res.json() : Promise.reject('Network response was not ok.'))
        .then(data => {
            if (data.success) {
                const moduleEl = document.querySelector(`.module-container[data-module-id="${moduleId}"]`);
                if (moduleEl) {
                    const badge = moduleEl.querySelector('.module-status-badge');
                    if (badge) {
                        badge.className = 'module-status-badge completed';
                        badge.innerHTML = '<i class="bi bi-check-circle"></i> Completed';
                    }
                    
                    const completeSection = moduleEl.querySelector('.module-complete-section');
                    if (completeSection) {
                        completeSection.style.display = 'none';
                    }
                }
                alert('Module marked as complete! 🎉');
            } else {
                alert('Error: ' + (data.message || 'Could not mark module as complete.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while marking the module as complete.');
        });
    };

    // Debug: Check module data
    const moduleContainers = document.querySelectorAll('.module-container');
    console.log('🔍 Found module containers:', moduleContainers.length);
    
    moduleContainers.forEach((container, index) => {
        const moduleId = container.getAttribute('data-module-id');
        console.log(`📋 Module ${index + 1}: ID = ${moduleId}`);
    });

    console.log('✅ Student Course Page - Initialization complete!');
});
</script>
@endpush
