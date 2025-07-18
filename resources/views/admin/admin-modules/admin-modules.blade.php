@extends('admin.admin-dashboard-layout')

@section('title', 'Modules')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="{{ asset('css/admin/admin-modules.css') }}" rel="stylesheet">
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Alert Messages -->
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please correct the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="modules-container">
    <!-- Header -->
    <div class="modules-header">
        <h1><i class="bi bi-journals"></i> Module Management</h1>
        <p>Create, organize, and manage your educational content</p>
    </div>

    <!-- Program Selector -->
    <div class="program-selector">
        <label for="programSelect" class="form-label">Select Program to View/Manage Modules:</label>
        <div class="d-flex align-items-center gap-3">
            <select id="programSelect" name="program_id" class="form-select" style="max-width: 400px;">
                <option value="">-- Select a Program --</option>
                @foreach($programs as $program)
                    <option value="{{ $program->program_id }}"
                        {{ request('program_id') == $program->program_id ? 'selected' : '' }}>
                        {{ $program->program_name }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">
                <i class="bi bi-info-circle"></i> 
                Select a program to view and manage its modules
            </small>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section" id="filterSection" style="display: none;">
        <div class="filter-row">
            <div class="filter-group">
                <label for="batchFilter">Filter by Batch:</label>
                <select id="batchFilter" class="form-select">
                    <option value="">All Batches</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="courseFilter">Filter by Course:</label>
                <select id="courseFilter" class="form-select">
                    <option value="">All Courses</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="learningModeFilter">Filter by Learning Mode:</label>
                <select id="learningModeFilter" class="form-select">
                    <option value="">All Learning Modes</option>
                    <option value="synchronous">Synchronous</option>
                    <option value="asynchronous">Asynchronous</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="contentTypeFilter">Filter by Content Type:</label>
                <select id="contentTypeFilter" class="form-select">
                    <option value="">All Content Types</option>
                    <option value="module">Module/Lesson</option>
                    <option value="assignment">Assignment</option>
                    <option value="quiz">Quiz</option>
                    <option value="test">Test</option>
                    <option value="link">External Link</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <button type="button" class="add-module-btn" id="showAddModal">
            <i class="bi bi-plus-circle"></i> Add New Content
        </button>
        <button type="button" class="add-course-btn" id="showAddCourseModal">
            <i class="bi bi-journal-plus"></i> Add New Course
        </button>
        <button type="button" class="batch-upload-btn" id="showBatchModal">
            <i class="bi bi-upload"></i> Add Course Content
        </button>
        <a href="{{ route('admin.modules.archived') }}" class="view-archived-btn">
            <i class="bi bi-archive"></i> View Archived
        </a>
        <a href="{{ route('admin.quiz-generator') }}" class="quiz-generator-btn">
            <i class="bi bi-robot"></i> AI Quiz Generator
        </a>
    </div>

    <!-- Modules Display Area -->
    <div id="modulesDisplayArea">
        @if(request('program_id') && isset($modules))
            @if($modules->count() > 0)
<style>
  /* Hierarchical Module Structure */
  .modules-hierarchy {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
  }
  
  .module-container {
    border: 2px solid #e1e5e9;
    border-radius: 15px;
    background: white;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
  }
  
  .module-container:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
  }
  
  .module-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
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
    gap: 1.5rem;
  }
  
  .module-title-section h4 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
  }
  
  .module-title-section small {
    font-size: 1rem;
    opacity: 0.9;
  }
  
  .module-toggle-icon {
    transition: transform 0.3s ease;
    font-size: 1.2rem;
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
  
  .courses-list {
    padding: 1.5rem;
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
  
  .content-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    margin-bottom: 0.75rem;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    transition: all 0.3s ease;
  }
  
  .content-item:hover {
    background: #e9ecef;
    border-color: #dee2e6;
    transform: translateX(5px);
  }
  
  .content-item-info {
    display: flex;
    align-items: center;
    gap: 1rem;
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
  
  .content-item-type.assignment {
    background: #dc3545;
  }
  
  .content-item-type.pdf {
    background: #fd7e14;
  }
  
  .content-item-type.lesson {
    background: #17a2b8;
  }
  
  .content-item-type.quiz {
    background: #6f42c1;
  }
  
  .content-item-type.test {
    background: #e83e8c;
  }
  
  .content-item-type.link {
    background: #20c997;
  }
  
  .content-item-actions {
    display: flex;
    gap: 0.5rem;
  }
  
  .content-item-actions .btn {
    padding: 0.4rem 0.8rem;
    font-size: 0.875rem;
    border-radius: 6px;
    border: none;
    font-weight: 500;
    transition: all 0.2s ease;
  }
  
  .content-item-actions .btn:hover {
    transform: translateY(-1px);
  }
  
  .module-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
  }
  
  .module-actions .btn {
    padding: 0.7rem 1.2rem;
    font-size: 0.9rem;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
  }
  
  .add-course-btn {
    background: rgba(255, 255, 255, 0.2);
    color: white !important;
    border: 2px solid rgba(255, 255, 255, 0.3) !important;
  }
  
  .add-course-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    color: white !important;
    border-color: rgba(255, 255, 255, 0.5) !important;
    transform: translateY(-2px);
  }
  
  .btn-outline-light {
    background: rgba(255, 255, 255, 0.15);
    color: white !important;
    border: 2px solid rgba(255, 255, 255, 0.3) !important;
  }
  
  .btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.25);
    color: white !important;
    border-color: rgba(255, 255, 255, 0.5) !important;
    transform: translateY(-2px);
  }
  
  /* Module action buttons */
  .module-actions .btn-outline-light {
    background: rgba(255, 255, 255, 0.2);
    color: white !important;
    border: 1px solid rgba(255, 255, 255, 0.4) !important;
    padding: 0.5rem 0.8rem;
    font-size: 0.875rem;
  }
  
  .module-actions .btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.3);
    color: white !important;
    border-color: rgba(255, 255, 255, 0.6) !important;
    transform: translateY(-1px);
  }
  
  /* Ensure edit and delete buttons are visible */
  .module-actions .btn-outline-light i {
    color: white !important;
  }
  
  .module-actions .btn-outline-light:hover i {
    color: white !important;
  }
  
  .no-courses-message {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
    font-style: italic;
  }
  
  .drag-handle {
    cursor: move;
    color: rgba(108, 117, 125, 0.7);
    margin-right: 0.5rem;
    font-size: 1.1rem;
    transition: color 0.2s ease;
  }
  
  .drag-handle:hover {
    color: rgba(108, 117, 125, 1);
  }
  
  .module-header .drag-handle {
    color: rgba(255, 255, 255, 0.7);
  }
  
  .module-header .drag-handle:hover {
    color: rgba(255, 255, 255, 1);
  }
  
  .sortable-ghost {
    opacity: 0.5;
    background: #f1f3f4;
  }
  
  .sortable-chosen {
    transform: scale(1.02);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  }
  
  /* Action Buttons */
  .action-buttons {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
  }
  
  .action-buttons .btn {
    padding: 0.8rem 1.5rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
    border: none;
  }
  
  .add-module-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
  }
  
  .add-module-btn:hover {
    background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
    transform: translateY(-2px);
    color: white;
  }
  
  .add-course-btn {
    background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
    color: white;
  }
  
  .add-course-btn:hover {
    background: linear-gradient(135deg, #0056b3 0%, #520dc2 100%);
    transform: translateY(-2px);
    color: white;
  }
  
  .batch-upload-btn {
    background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%);
    color: white;
  }
  
  .batch-upload-btn:hover {
    background: linear-gradient(135deg, #e55a00 0%, #d91a72 100%);
    transform: translateY(-2px);
    color: white;
  }
  
  .view-archived-btn {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
  }
  
  .view-archived-btn:hover {
    background: linear-gradient(135deg, #545b62 0%, #343a40 100%);
    transform: translateY(-2px);
    color: white;
  }
  
  .quiz-generator-btn {
    background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
    color: white;
  }
  
  .quiz-generator-btn:hover {
    background: linear-gradient(135deg, #5a2d91 0%, #d91a72 100%);
    transform: translateY(-2px);
    color: white;
  }
  
  /* Cross-module drag and drop styles */
  .drag-over {
    border: 2px dashed #007bff !important;
    background: rgba(0, 123, 255, 0.1) !important;
    transform: scale(1.02);
  }
  
  .sortable-drag {
    opacity: 0.8;
    transform: rotate(5deg);
  }
  
  .course-selection-item {
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  
  .course-selection-item:hover {
    background: #f8f9fa;
    border-color: #007bff;
    transform: translateX(5px);
  }
  
  .course-selection-item i {
    color: #007bff;
    margin-right: 0.5rem;
  }
  
  .course-selection-item p {
    margin: 0.5rem 0 0 0;
    color: #6c757d;
    font-size: 0.9rem;
  }
  
  /* Notification animations */
  @keyframes slideInRight {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
</style>

<div class="modules-hierarchy" id="modulesHierarchy">
  @foreach($modules as $module)
    <div class="module-container" data-module-id="{{ $module->modules_id }}">
      <div class="module-header" onclick="toggleModule({{ $module->modules_id }})">
        <div class="module-title-section">
          <i class="drag-handle bi bi-grip-vertical"></i>
          <i class="module-toggle-icon bi bi-chevron-right"></i>
          <div>
            <h4 class="mb-0">{{ $module->module_name }}</h4>
            @if($module->module_description)
              <small class="opacity-75">{{ $module->module_description }}</small>
            @endif
          </div>
        </div>
        
        <div class="module-actions" onclick="event.stopPropagation();">
          <button class="btn add-course-btn" onclick="showAddCourseModal({{ $module->modules_id }}, '{{ addslashes($module->module_name) }}')">
            <i class="bi bi-plus-circle"></i> Add Course
          </button>
          <button class="btn btn-sm btn-outline-light" onclick="editModule({{ $module->modules_id }})">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm btn-outline-light" onclick="deleteModule({{ $module->modules_id }})">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </div>
      
      <div class="module-content" id="module-content-{{ $module->modules_id }}">
        <div class="courses-list">
          <div id="courses-container-{{ $module->modules_id }}">
            <!-- Courses will be loaded dynamically -->
          </div>
        </div>
      </div>
    </div>
  @endforeach
</div>
            @else
                <div class="select-program-msg">
                    <div class="empty-state">
                        <i class="bi bi-journals" style="font-size: 4rem; color: #6c757d; margin-bottom: 1rem;"></i>
                        <h4 style="color: #6c757d; margin-bottom: 1rem;">No Modules Found</h4>
                        <p style="color: #6c757d; margin-bottom: 2rem;">No modules are available for the selected program yet.</p>
                        <button type="button" class="add-module-btn" onclick="document.getElementById('addModalBg').classList.add('show');">
                            <i class="bi bi-plus-circle"></i> Create First Module
                        </button>
                    </div>
                </div>
            @endif
        @else
            <div class="select-program-msg">
                <div class="empty-state">
                    <i class="bi bi-arrow-up-circle" style="font-size: 4rem; color: #6c757d; margin-bottom: 1rem;"></i>
                    <h4 style="color: #6c757d; margin-bottom: 1rem;">Select a Program</h4>
                    <p style="color: #6c757d;">Select a program from the dropdown above to view and manage its modules</p>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Global variables
let currentArchiveModuleId = null;
let currentOverrideModuleId = null;

// Module toggle functionality
function toggleModule(moduleId) {
    const content = document.getElementById(`module-content-${moduleId}`);
    const icon = content.previousElementSibling.querySelector('.module-toggle-icon');
    
    if (content.classList.contains('expanded')) {
        content.classList.remove('expanded');
        icon.classList.remove('expanded');
    } else {
        content.classList.add('expanded');
        icon.classList.add('expanded');
        loadModuleCourses(moduleId);
    }
}

// Course toggle functionality
function toggleCourse(moduleId, courseId) {
    const content = document.getElementById(`course-content-${moduleId}-${courseId}`);
    const icon = content.previousElementSibling.querySelector('.course-toggle-icon');
    
    if (content.classList.contains('expanded')) {
        content.classList.remove('expanded');
        icon.classList.remove('expanded');
    } else {
        content.classList.add('expanded');
        icon.classList.add('expanded');
        loadCourseContent(moduleId, courseId);
    }
}

// Load courses for a module
function loadModuleCourses(moduleId) {
    const container = document.getElementById(`courses-container-${moduleId}`);
    if (container.dataset.loaded === 'true') return;
    
    container.innerHTML = '<div class="text-center p-3"><i class="bi bi-arrow-clockwise fa-spin"></i> Loading courses...</div>';
    
    fetch(`/admin/modules/${moduleId}/courses`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.courses) {
                displayCourses(moduleId, data.courses);
                container.dataset.loaded = 'true';
            } else {
                container.innerHTML = `
                    <div class="no-courses-message">
                        <i class="bi bi-journal-x" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                        <p>No courses found for this module.</p>
                        <button class="btn btn-primary" onclick="showAddCourseModal(${moduleId})">
                            <i class="bi bi-plus-circle"></i> Add First Course
                        </button>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading courses:', error);
            container.innerHTML = '<div class="alert alert-danger">Error loading courses</div>';
        });
}

// Display courses in the module
function displayCourses(moduleId, courses) {
    const container = document.getElementById(`courses-container-${moduleId}`);
    
    if (courses.length === 0) {
        container.innerHTML = `
            <div class="no-courses-message">
                <i class="bi bi-journal-x" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>No courses found for this module.</p>
                <button class="btn btn-primary" onclick="showAddCourseModal(${moduleId})">
                    <i class="bi bi-plus-circle"></i> Add First Course
                </button>
            </div>
        `;
        return;
    }
    
    let html = '';
    courses.forEach(course => {
        html += `
            <div class="course-container" data-course-id="${course.subject_id}">
                <div class="course-header" onclick="toggleCourse(${moduleId}, ${course.subject_id})">
                    <div class="d-flex align-items-center gap-2">
                        <i class="course-toggle-icon bi bi-chevron-right"></i>
                        <div>
                            <h5 class="mb-0">${course.subject_name}</h5>
                            ${course.subject_description ? `<small class="opacity-75">${course.subject_description}</small>` : ''}
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation();">
                        <button class="btn btn-sm btn-outline-light" onclick="showAddContentModal(${moduleId}, ${course.subject_id}, '${course.subject_name}')">
                            <i class="bi bi-plus"></i> Add Content
                        </button>
                        <button class="btn btn-sm btn-outline-light" onclick="editCourse(${course.subject_id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-light" onclick="deleteCourse(${course.subject_id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="course-content" id="course-content-${moduleId}-${course.subject_id}">
                    <div id="content-container-${moduleId}-${course.subject_id}">
                        <!-- Content items will be loaded here -->
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Load content items for a course
function loadCourseContent(moduleId, courseId) {
    const container = document.getElementById(`content-container-${moduleId}-${courseId}`);
    if (container.dataset.loaded === 'true') return;
    
    container.innerHTML = '<div class="text-center p-3"><i class="bi bi-arrow-clockwise fa-spin"></i> Loading content...</div>';
    
    fetch(`/admin/courses/${courseId}/content`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.content) {
                displayCourseContent(moduleId, courseId, data.content);
                container.dataset.loaded = 'true';
            } else {
                container.innerHTML = `
                    <div class="text-center p-3">
                        <p class="text-muted">No content items found.</p>
                        <button class="btn btn-sm btn-primary" onclick="showAddContentModal(${moduleId}, ${courseId})">
                            <i class="bi bi-plus-circle"></i> Add Content
                        </button>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading content:', error);
            container.innerHTML = '<div class="alert alert-danger">Error loading content</div>';
        });
}

// Display course content items
function displayCourseContent(moduleId, courseId, contentItems) {
    const container = document.getElementById(`content-container-${moduleId}-${courseId}`);
    
    if (contentItems.length === 0) {
        container.innerHTML = `
            <div class="text-center p-3">
                <p class="text-muted">No content items found.</p>
                <button class="btn btn-sm btn-primary" onclick="showAddContentModal(${moduleId}, ${courseId})">
                    <i class="bi bi-plus-circle"></i> Add Content
                </button>
            </div>
        `;
        return;
    }
    
    let html = '';
    contentItems.forEach(item => {
        const typeClass = getContentTypeClass(item.content_type);
        const typeIcon = getContentTypeIcon(item.content_type);
        
        html += `
            <div class="content-item" data-content-id="${item.id}">
                <div class="content-item-info">
                    <i class="drag-handle bi bi-grip-vertical"></i>
                    <span class="content-item-type ${typeClass}">
                        <i class="bi ${typeIcon}"></i> ${item.content_type}
                    </span>
                    <div>
                        <strong>${item.content_title}</strong>
                        ${item.content_description ? `<br><small class="text-muted">${item.content_description}</small>` : ''}
                    </div>
                </div>
                <div class="content-item-actions">
                    ${item.content_type === 'assignment' ? 
                        `<button class="btn btn-sm btn-info" onclick="viewSubmissions(${item.id})">
                            <i class="bi bi-file-earmark-text"></i> Submissions
                        </button>` : ''
                    }
                    <button class="btn btn-sm btn-warning" onclick="editContent(${item.id})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteContent(${item.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    
    // Initialize sortable for content items
    if (typeof Sortable !== 'undefined') {
        new Sortable(container, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function(evt) {
                const contentIds = Array.from(container.children).map(el => 
                    el.getAttribute('data-content-id')
                );
                updateContentOrder(contentIds);
            }
        });
    }
}

// Helper functions for content types
function getContentTypeClass(type) {
    const classes = {
        'assignment': 'assignment',
        'pdf': 'pdf',
        'lesson': 'lesson',
        'quiz': 'quiz',
        'test': 'test',
        'link': 'link'
    };
    return classes[type] || 'lesson';
}

function getContentTypeIcon(type) {
    const icons = {
        'assignment': 'bi-file-earmark-text',
        'pdf': 'bi-file-pdf',
        'lesson': 'bi-journal-text',
        'quiz': 'bi-question-circle',
        'test': 'bi-clipboard-check',
        'link': 'bi-link-45deg'
    };
    return icons[type] || 'bi-file-text';
}

// Modal functions
function showAddCourseModal(moduleId, moduleName = '') {
    const modal = document.getElementById('addCourseModalBg');
    const form = document.getElementById('addCourseForm');
    
    if (modal && form) {
        // Pre-fill the module selection if moduleId is provided
        if (moduleId) {
            // Set the program first by finding which program this module belongs to
            fetch(`/admin/modules/by-program?module_id=${moduleId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.program_id) {
                        const programSelect = document.getElementById('courseProgramSelect');
                        const moduleSelect = document.getElementById('courseModuleSelect');
                        
                        if (programSelect) {
                            programSelect.value = data.program_id;
                            // Load modules for this program
                            loadModulesForProgram(data.program_id, 'courseModuleSelect');
                            
                            // Set the module after loading
                            setTimeout(() => {
                                if (moduleSelect) {
                                    moduleSelect.value = moduleId;
                                }
                            }, 500);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading module info:', error);
                });
        }
        
        modal.classList.add('show');
    } else {
        console.error('Add course modal or form not found');
    }
}

function showAddContentModal(moduleId, courseId, courseName = '') {
    const modal = document.getElementById('batchModalBg');
    const form = document.getElementById('courseContentForm');
    
    if (modal && form) {
        // Pre-fill the selections if provided
        if (moduleId && courseId) {
            // Get program info and pre-fill the form
            fetch(`/admin/modules/by-program?module_id=${moduleId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.program_id) {
                        const programSelect = document.getElementById('contentProgramSelect');
                        const moduleSelect = document.getElementById('contentModuleSelect');
                        const courseSelect = document.getElementById('contentCourseSelect');
                        
                        if (programSelect) {
                            programSelect.value = data.program_id;
                            // Load modules for this program
                            loadModulesForProgram(data.program_id, 'contentModuleSelect');
                            
                            // Set the module and then load courses
                            setTimeout(() => {
                                if (moduleSelect) {
                                    moduleSelect.value = moduleId;
                                    loadCoursesForModule(moduleId, 'contentCourseSelect');
                                    
                                    // Set the course after loading
                                    setTimeout(() => {
                                        if (courseSelect) {
                                            courseSelect.value = courseId;
                                        }
                                    }, 500);
                                }
                            }, 500);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading module info:', error);
                });
        }
        
        modal.classList.add('show');
    } else {
        console.error('Add content modal or form not found');
    }
}

function editModule(moduleId) {
    // Implementation for editing module
    fetch(`/admin/modules/${moduleId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success || data.module) {
                // Extract module data - handle both response formats
                const moduleData = data.module || data;
                
                // Open edit modal with pre-filled data
                const modal = document.getElementById('editModalBg');
                if (modal) {
                    // Fill form with module data
                    document.getElementById('editModuleName').value = moduleData.module_name || '';
                    document.getElementById('editModuleDescription').value = moduleData.module_description || '';
                    document.getElementById('editModalProgramSelect').value = moduleData.program_id || '';
                    
                    // Set form action
                    const form = document.getElementById('editModuleForm');
                    if (form) {
                        form.action = `/admin/modules/${moduleId}`;
                    }
                    
                    modal.classList.add('show');
                } else {
                    // Fallback: use prompt for editing
                    const newName = prompt('Enter new module name:', moduleData.module_name || '');
                    const newDescription = prompt('Enter new module description:', moduleData.module_description || '');
                    
                    if (newName && newName !== moduleData.module_name) {
                        updateModuleDetails(moduleId, newName, newDescription);
                    }
                }
            } else {
                alert('Error loading module data: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error loading module:', error);
            alert('Error loading module data: ' + error.message);
        });
}

function deleteModule(moduleId) {
    if (confirm('Are you sure you want to delete this module? This action cannot be undone.')) {
        fetch(`/admin/modules/${moduleId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to delete module'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the module');
        });
    }
}

// Helper function to update module details
function updateModuleDetails(moduleId, newName, newDescription) {
    fetch(`/admin/modules/${moduleId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            module_name: newName,
            module_description: newDescription || ''
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Module updated successfully!', 'success');
            // Reload the page to reflect changes
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('Error updating module: ' + (data.message || 'Unknown error'), 'danger');
        }
    })
    .catch(error => {
        console.error('Error updating module:', error);
        showAlert('Error updating module: ' + error.message, 'danger');
    });
}

function editCourse(courseId) {
    // Implementation for editing course
    fetch(`/admin/courses/${courseId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success || data.course) {
                // Extract course data - handle both response formats
                const courseData = data.course || data;
                
                // Open edit modal with pre-filled data
                const modal = document.getElementById('editCourseModalBg');
                if (modal) {
                    // Fill form with course data
                    document.getElementById('editCourseName').value = courseData.subject_name || '';
                    document.getElementById('editCourseDescription').value = courseData.subject_description || '';
                    document.getElementById('editCourseModuleSelect').value = courseData.module_id || '';
                    
                    // Set form action
                    const form = document.getElementById('editCourseForm');
                    if (form) {
                        form.action = `/admin/courses/${courseId}`;
                    }
                    
                    modal.classList.add('show');
                } else {
                    // Fallback to simple editing if modal is not available
                    const newName = prompt('Enter new course name:', courseData.subject_name || '');
                    if (newName && newName.trim() !== '' && newName.trim() !== courseData.subject_name) {
                        updateCourseDetails(courseId, { subject_name: newName.trim() });
                    }
                }
            } else {
                alert('Error loading course data: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error loading course:', error);
            alert('Error loading course data: ' + error.message);
        });
}

// Helper function to update course details
function updateCourseDetails(courseId, courseData) {
    fetch(`/admin/courses/${courseId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(courseData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating course: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error updating course:', error);
        alert('Error updating course');
    });
}

function deleteCourse(courseId) {
    if (confirm('Are you sure you want to delete this course? This action cannot be undone.')) {
        fetch(`/admin/courses/${courseId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to delete course'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the course');
        });
    }
}

function editContent(contentId) {
    // Fetch content data
    fetch(`/admin/content/${contentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Open edit modal with pre-filled data
                const modal = document.getElementById('editContentModalBg');
                const form = document.getElementById('editContentForm');
                
                if (modal && form) {
                    // Fill form with content data
                    document.getElementById('edit_content_id').value = contentId;
                    document.getElementById('edit_content_title').value = data.content.title || '';
                    document.getElementById('edit_content_description').value = data.content.description || '';
                    document.getElementById('edit_content_type').value = data.content.type || '';
                    document.getElementById('edit_content_order').value = data.content.sort_order || '';
                    
                    // Handle link field visibility
                    const linkSection = document.getElementById('edit_link_section');
                    const fileSection = document.getElementById('edit_file_section');
                    
                    if (data.content.type === 'link') {
                        linkSection.style.display = 'block';
                        fileSection.style.display = 'none';
                        document.getElementById('edit_content_link').value = data.content.link || '';
                    } else {
                        linkSection.style.display = 'none';
                        fileSection.style.display = 'block';
                    }
                    
                    // Set form action
                    form.action = `/admin/content/${contentId}`;
                    
                    modal.classList.add('show');
                }
            } else {
                alert('Error loading content data: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error loading content:', error);
            alert('Error loading content data');
        });
}

function deleteContent(contentId) {
    if (confirm('Are you sure you want to delete this content? This action cannot be undone.')) {
        fetch(`/admin/content/${contentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to delete content'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the content');
        });
    }
}

function viewSubmissions(assignmentId) {
    // Implementation for viewing submissions
    window.open(`/admin/assignments/${assignmentId}/submissions`, '_blank');
}

// Initialize sortable functionality for modules
document.addEventListener('DOMContentLoaded', function() {
    const modulesContainer = document.getElementById('modulesHierarchy');
    if (modulesContainer && typeof Sortable !== 'undefined') {
        // Module-level sorting
        new Sortable(modulesContainer, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function(evt) {
                const moduleIds = Array.from(modulesContainer.children).map(el => 
                    el.getAttribute('data-module-id')
                );
                updateModuleOrder(moduleIds);
            }
        });
        
        // Initialize content-level sorting for each course
        initializeContentSorting();
        
        // Initialize cross-module content drag and drop
        initializeCrossModuleDragDrop();
    }
    
    // Auto-select program if specified in URL
    const programSelect = document.getElementById('programSelect');
    if (programSelect && programSelect.value) {
        // Load filter options for the selected program
        loadFiltersForProgram(programSelect.value);
    }

    // Setup modal event listeners
    setupModalEventListeners();
});

// Initialize content sorting within courses
function initializeContentSorting() {
    // Find all content containers and make them sortable
    const contentContainers = document.querySelectorAll('[id^="content-container-"]');
    
    contentContainers.forEach(container => {
        if (typeof Sortable !== 'undefined') {
            new Sortable(container, {
                group: {
                    name: 'content-items',
                    pull: true,
                    put: true
                },
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                handle: '.drag-handle',
                onEnd: function(evt) {
                    // Get the content IDs in their new order
                    const contentIds = Array.from(evt.to.children).map(el => {
                        const contentId = el.getAttribute('data-content-id');
                        return contentId ? parseInt(contentId) : null;
                    }).filter(id => id !== null);
                    
                    // Check if it's a cross-container move
                    if (evt.from !== evt.to) {
                        handleCrossContainerContentMove(evt);
                    } else {
                        // Same container reorder
                        updateContentOrder(contentIds);
                    }
                },
                onMove: function(evt) {
                    // Allow dropping on any content container
                    return evt.related.classList.contains('content-item') || 
                           evt.related.id.startsWith('content-container-');
                }
            });
        }
    });
}

// Initialize cross-module drag and drop functionality
function initializeCrossModuleDragDrop() {
    // Make modules accept dropped content items
    const moduleContainers = document.querySelectorAll('.module-container');
    
    moduleContainers.forEach(moduleContainer => {
        const moduleId = moduleContainer.getAttribute('data-module-id');
        
        // Add drop zone styling
        moduleContainer.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });
        
        moduleContainer.addEventListener('dragleave', function(e) {
            if (!this.contains(e.relatedTarget)) {
                this.classList.remove('drag-over');
            }
        });
        
        moduleContainer.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            // Handle the drop if it's a content item being moved to a different module
            const draggedElement = document.querySelector('.sortable-chosen');
            if (draggedElement && draggedElement.classList.contains('content-item')) {
                handleContentDropOnModule(draggedElement, moduleId);
            }
        });
    });
}

// Handle content being moved between different courses/modules
function handleCrossContainerContentMove(evt) {
    const movedElement = evt.item;
    const contentId = movedElement.getAttribute('data-content-id');
    const fromContainer = evt.from;
    const toContainer = evt.to;
    
    // Extract course/module IDs from container IDs
    const fromIds = extractModuleCourseIds(fromContainer.id);
    const toIds = extractModuleCourseIds(toContainer.id);
    
    if (contentId && fromIds && toIds) {
        // Check if moving within the same module (just different courses)
        const sameModule = fromIds.moduleId === toIds.moduleId;
        
        if (sameModule) {
            // Moving within same module - simple reorder/move, no confirmation needed
            moveContentToNewLocation(contentId, toIds.courseId, toIds.moduleId)
                .then(success => {
                    if (success) {
                        // Update the new order in the destination
                        const newOrder = Array.from(toContainer.children).map((el, index) => {
                            const id = el.getAttribute('data-content-id');
                            return id ? parseInt(id) : null;
                        }).filter(id => id !== null);
                        
                        updateContentOrder(newOrder);
                        
                        // Show success message
                        showNotification('Content moved within module successfully!', 'success');
                    } else {
                        // Revert the move
                        fromContainer.appendChild(movedElement);
                        showNotification('Failed to move content. Please try again.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error moving content:', error);
                    fromContainer.appendChild(movedElement);
                    showNotification('Error moving content. Please try again.', 'error');
                });
        } else {
            // Moving between different modules - require confirmation
            const confirmMove = confirm(
                `Are you sure you want to move this content item to a different module?\n\n` +
                `From: ${fromIds.moduleName || 'Module'} → ${fromIds.courseName || 'Course'}\n` +
                `To: ${toIds.moduleName || 'Module'} → ${toIds.courseName || 'Course'}`
            );
            
            if (confirmMove) {
                // Make API call to move content
                moveContentToNewLocation(contentId, toIds.courseId, toIds.moduleId)
                    .then(success => {
                        if (success) {
                            // Update the new order in the destination
                            const newOrder = Array.from(toContainer.children).map((el, index) => {
                                const id = el.getAttribute('data-content-id');
                                return id ? parseInt(id) : null;
                            }).filter(id => id !== null);
                            
                            updateContentOrder(newOrder);
                            
                            // Show success message
                            showNotification('Content moved to different module successfully!', 'success');
                        } else {
                            // Revert the move
                            fromContainer.appendChild(movedElement);
                            showNotification('Failed to move content. Please try again.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error moving content:', error);
                        fromContainer.appendChild(movedElement);
                        showNotification('Error moving content. Please try again.', 'error');
                    });
            } else {
                // User cancelled - revert the move
                fromContainer.appendChild(movedElement);
            }
        }
    }
}

// Extract module and course IDs from container ID
function extractModuleCourseIds(containerId) {
    // Container ID format: "content-container-{moduleId}-{courseId}"
    const match = containerId.match(/content-container-(\d+)-(\d+)/);
    if (match) {
        const moduleId = parseInt(match[1]);
        const courseId = parseInt(match[2]);
        
        // Try to get names from DOM
        const moduleElement = document.querySelector(`[data-module-id="${moduleId}"]`);
        const moduleName = moduleElement ? moduleElement.querySelector('.module-title-section h4').textContent : null;
        
        const courseElement = document.querySelector(`#course-content-${moduleId}-${courseId}`);
        const courseName = courseElement ? courseElement.previousElementSibling.querySelector('h5').textContent : null;
        
        return {
            moduleId,
            courseId,
            moduleName,
            courseName
        };
    }
    return null;
}

// Move content to a new location via API
function moveContentToNewLocation(contentId, newCourseId, newModuleId) {
    return fetch('/admin/content/move', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            content_id: contentId,
            new_course_id: newCourseId,
            new_module_id: newModuleId
        })
    })
    .then(response => response.json())
    .then(data => {
        return data.success;
    })
    .catch(error => {
        console.error('Error in moveContentToNewLocation:', error);
        return false;
    });
}

// Handle content being dropped directly on a module (not in a specific course)
function handleContentDropOnModule(contentElement, targetModuleId) {
    const contentId = contentElement.getAttribute('data-content-id');
    
    if (contentId) {
        // Show modal to select which course in the module to add to
        showCourseSelectionModal(contentId, targetModuleId);
    }
}

// Show modal to select target course when dropping on module
function showCourseSelectionModal(contentId, moduleId) {
    // Create modal dynamically
    const modal = document.createElement('div');
    modal.className = 'modal-bg';
    modal.innerHTML = `
        <div class="modal">
            <div class="modal-header">
                <h3><i class="bi bi-arrow-right-circle"></i> Select Target Course</h3>
                <button type="button" class="modal-close" onclick="this.closest('.modal-bg').remove()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Select which course to move this content to:</p>
                <div id="courseSelectionList">
                    <div class="text-center">
                        <i class="bi bi-arrow-clockwise fa-spin"></i> Loading courses...
                    </div>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="cancel-btn" onclick="this.closest('.modal-bg').remove()">Cancel</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.classList.add('show');
    
    // Load courses for the module
    loadCoursesForSelection(moduleId, contentId);
}

// Load courses for selection modal
function loadCoursesForSelection(moduleId, contentId) {
    fetch(`/admin/modules/${moduleId}/courses`)
        .then(response => response.json())
        .then(data => {
            const listContainer = document.getElementById('courseSelectionList');
            
            if (data.success && data.courses && data.courses.length > 0) {
                let html = '';
                data.courses.forEach(course => {
                    html += `
                        <div class="course-selection-item" onclick="selectTargetCourse(${contentId}, ${course.subject_id}, ${moduleId}, '${course.subject_name}')">
                            <i class="bi bi-book"></i>
                            <strong>${course.subject_name}</strong>
                            <p>${course.subject_description || 'No description'}</p>
                        </div>
                    `;
                });
                listContainer.innerHTML = html;
            } else {
                listContainer.innerHTML = `
                    <div class="text-center text-muted">
                        <i class="bi bi-exclamation-circle"></i>
                        <p>No courses available in this module.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading courses:', error);
            document.getElementById('courseSelectionList').innerHTML = `
                <div class="text-center text-danger">
                    <i class="bi bi-x-circle"></i>
                    <p>Error loading courses. Please try again.</p>
                </div>
            `;
        });
}

// Select target course and move content
function selectTargetCourse(contentId, courseId, moduleId, courseName) {
    const modal = document.querySelector('.modal-bg');
    
    if (confirm(`Move content to "${courseName}"?`)) {
        moveContentToNewLocation(contentId, courseId, moduleId)
            .then(success => {
                modal.remove();
                if (success) {
                    location.reload(); // Refresh to show updated structure
                } else {
                    showNotification('Failed to move content. Please try again.', 'error');
                }
            });
    }
}

// Show notification messages
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : 'success'} position-fixed`;
    notification.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-radius: 10px;
        animation: slideInRight 0.3s ease;
    `;
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-${type === 'error' ? 'x-circle' : 'check-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 4000);
}

// Setup modal event listeners
function setupModalEventListeners() {
    // Add Module Modal
    const showAddModal = document.getElementById('showAddModal');
    const addModalBg = document.getElementById('addModalBg');
    const closeAddModal = document.getElementById('closeAddModal');
    const closeAddModalBtn = document.getElementById('closeAddModalBtn');

    if (showAddModal) {
        showAddModal.addEventListener('click', function() {
            addModalBg.classList.add('show');
        });
    }

    if (closeAddModal) {
        closeAddModal.addEventListener('click', function() {
            addModalBg.classList.remove('show');
        });
    }

    if (closeAddModalBtn) {
        closeAddModalBtn.addEventListener('click', function() {
            addModalBg.classList.remove('show');
        });
    }

    // Add Course Modal
    const showAddCourseModal = document.getElementById('showAddCourseModal');
    const addCourseModalBg = document.getElementById('addCourseModalBg');
    const closeAddCourseModal = document.getElementById('closeAddCourseModal');
    const closeAddCourseModalBtn = document.getElementById('closeAddCourseModalBtn');

    if (showAddCourseModal) {
        showAddCourseModal.addEventListener('click', function() {
            addCourseModalBg.classList.add('show');
        });
    }

    if (closeAddCourseModal) {
        closeAddCourseModal.addEventListener('click', function() {
            addCourseModalBg.classList.remove('show');
        });
    }

    if (closeAddCourseModalBtn) {
        closeAddCourseModalBtn.addEventListener('click', function() {
            addCourseModalBg.classList.remove('show');
        });
    }

    // Batch Modal
    const showBatchModal = document.getElementById('showBatchModal');
    const batchModalBg = document.getElementById('batchModalBg');
    const closeBatchModal = document.getElementById('closeBatchModal');
    const closeBatchModalBtn = document.getElementById('closeBatchModalBtn');

    if (showBatchModal) {
        showBatchModal.addEventListener('click', function() {
            batchModalBg.classList.add('show');
        });
    }

    if (closeBatchModal) {
        closeBatchModal.addEventListener('click', function() {
            batchModalBg.classList.remove('show');
        });
    }

    if (closeBatchModalBtn) {
        closeBatchModalBtn.addEventListener('click', function() {
            batchModalBg.classList.remove('show');
        });
    }

    // Edit Content Modal
    const editContentModalBg = document.getElementById('editContentModalBg');
    const closeEditContentModal = document.getElementById('closeEditContentModal');
    const closeEditContentModalBtn = document.getElementById('closeEditContentModalBtn');

    if (closeEditContentModal) {
        closeEditContentModal.addEventListener('click', function() {
            editContentModalBg.classList.remove('show');
        });
    }

    if (closeEditContentModalBtn) {
        closeEditContentModalBtn.addEventListener('click', function() {
            editContentModalBg.classList.remove('show');
        });
    }

    // Form submission handlers
    setupFormHandlers();
    setupProgramChangeHandlers();
}

// Program selector event listener
document.getElementById('programSelect').addEventListener('change', function() {
    const programId = this.value;
    if (programId) {
        window.location.href = `{{ route('admin.modules.index') }}?program_id=${programId}`;
    } else {
        document.getElementById('modulesDisplayArea').innerHTML = `
            <div class="select-program-msg">
                <div class="empty-state">
                    <i class="bi bi-arrow-up-circle" style="font-size: 4rem; color: #6c757d; margin-bottom: 1rem;"></i>
                    <h4 style="color: #6c757d; margin-bottom: 1rem;">Select a Program</h4>
                    <p style="color: #6c757d;">Select a program from the dropdown above to view and manage its modules</p>
                </div>
            </div>
        `;
    }
});

function loadFiltersForProgram(programId) {
    // Load batches for filtering
    fetch(`/admin/modules/batches/${programId}`)
        .then(response => response.json())
        .then(data => {
            const batchFilter = document.getElementById('batchFilter');
            batchFilter.innerHTML = '<option value="">All Batches</option>';
            
            if (data.success && data.batches) {
                data.batches.forEach(batch => {
                    const option = document.createElement('option');
                    option.value = batch.batch_id;
                    option.textContent = batch.batch_name;
                    batchFilter.appendChild(option);
                });
            }
        });
    
    // Show filter section
    document.getElementById('filterSection').style.display = 'block';
}

// Update module order
function updateModuleOrder(moduleIds) {
    fetch('/admin/modules/update-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ module_ids: moduleIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Module order updated successfully');
        } else {
            console.error('Failed to update module order');
        }
    })
    .catch(error => {
        console.error('Error updating module order:', error);
    });
}

// Update content order
function updateContentOrder(contentIds) {
    fetch('/admin/content/update-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ content_ids: contentIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Content order updated successfully');
        } else {
            console.error('Failed to update content order');
        }
    })
    .catch(error => {
        console.error('Error updating content order:', error);
    });
}

// Setup form submission handlers
function setupFormHandlers() {
    // Add Module Form
    const addModuleForm = document.getElementById('addModuleForm');
    if (addModuleForm) {
        addModuleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitModuleForm(this);
        });
    }

    // Add Course Form
    const addCourseForm = document.getElementById('addCourseForm');
    if (addCourseForm) {
        addCourseForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitCourseForm(this);
        });
    }

    // Course Content Form
    const courseContentForm = document.getElementById('courseContentForm');
    if (courseContentForm) {
        courseContentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitCourseContentForm(this);
        });
    }

    // Edit Content Form
    const editContentForm = document.getElementById('editContentForm');
    if (editContentForm) {
        editContentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitEditContentForm(this);
        });
    }

    // Content type change handler for edit modal
    const editContentType = document.getElementById('edit_content_type');
    if (editContentType) {
        editContentType.addEventListener('change', function() {
            const linkSection = document.getElementById('edit_link_section');
            const fileSection = document.getElementById('edit_file_section');
            
            if (this.value === 'link') {
                linkSection.style.display = 'block';
                fileSection.style.display = 'none';
            } else {
                linkSection.style.display = 'none';
                fileSection.style.display = 'block';
            }
        });
    }
}

// Setup program change handlers
function setupProgramChangeHandlers() {
    // Modal program selectors
    const modalProgramSelect = document.getElementById('modalProgramSelect');
    if (modalProgramSelect) {
        modalProgramSelect.addEventListener('change', function() {
            loadBatchesForProgram(this.value, 'batch_id');
        });
    }

    const courseProgramSelect = document.getElementById('courseProgramSelect');
    if (courseProgramSelect) {
        courseProgramSelect.addEventListener('change', function() {
            loadModulesForProgram(this.value, 'courseModuleSelect');
        });
    }

    const contentProgramSelect = document.getElementById('contentProgramSelect');
    if (contentProgramSelect) {
        contentProgramSelect.addEventListener('change', function() {
            loadModulesForProgram(this.value, 'contentModuleSelect');
        });
    }

    // Module change handlers
    const courseModuleSelect = document.getElementById('courseModuleSelect');
    if (courseModuleSelect) {
        courseModuleSelect.addEventListener('change', function() {
            // Course creation doesn't need course selection, only module
        });
    }

    const contentModuleSelect = document.getElementById('contentModuleSelect');
    if (contentModuleSelect) {
        contentModuleSelect.addEventListener('change', function() {
            loadCoursesForModule(this.value, 'contentCourseSelect');
        });
    }
}

// Load batches for a program
function loadBatchesForProgram(programId, targetSelectId) {
    const batchSelect = document.getElementById(targetSelectId);
    if (!batchSelect || !programId) return;

    batchSelect.innerHTML = '<option value="">Loading...</option>';
    batchSelect.disabled = true;

    fetch(`/admin/modules/batches/${programId}`)
        .then(response => response.json())
        .then(data => {
            batchSelect.innerHTML = '<option value="">-- Select Batch --</option>';
            
            if (data.success && data.batches && data.batches.length > 0) {
                data.batches.forEach(batch => {
                    const option = document.createElement('option');
                    option.value = batch.id;
                    option.textContent = batch.batch_name;
                    batchSelect.appendChild(option);
                });
                batchSelect.disabled = false;
            } else {
                batchSelect.innerHTML = '<option value="">No batches available</option>';
            }
        })
        .catch(error => {
            console.error('Error loading batches:', error);
            batchSelect.innerHTML = '<option value="">Error loading batches</option>';
        });
}

// Load modules for a program
function loadModulesForProgram(programId, targetSelectId) {
    const moduleSelect = document.getElementById(targetSelectId);
    if (!moduleSelect || !programId) return;

    moduleSelect.innerHTML = '<option value="">Loading...</option>';
    moduleSelect.disabled = true;

    fetch(`/admin/modules/by-program?program_id=${programId}`)
        .then(response => response.json())
        .then(data => {
            moduleSelect.innerHTML = '<option value="">-- Select Module --</option>';
            
            if (data.success && data.modules && data.modules.length > 0) {
                data.modules.forEach(module => {
                    const option = document.createElement('option');
                    option.value = module.modules_id;
                    option.textContent = module.module_name;
                    moduleSelect.appendChild(option);
                });
                moduleSelect.disabled = false;
            } else {
                moduleSelect.innerHTML = '<option value="">No modules available</option>';
            }
        })
        .catch(error => {
            console.error('Error loading modules:', error);
            moduleSelect.innerHTML = '<option value="">Error loading modules</option>';
        });
}

// Load courses for a module
function loadCoursesForModule(moduleId, targetSelectId) {
    const courseSelect = document.getElementById(targetSelectId);
    if (!courseSelect || !moduleId) return;

    courseSelect.innerHTML = '<option value="">Loading...</option>';
    courseSelect.disabled = true;

    fetch(`/admin/modules/${moduleId}/courses`)
        .then(response => response.json())
        .then(data => {
            courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
            
            if (data.success && data.courses && data.courses.length > 0) {
                data.courses.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.subject_id;
                    option.textContent = course.subject_name;
                    courseSelect.appendChild(option);
                });
                courseSelect.disabled = false;
            } else {
                courseSelect.innerHTML = '<option value="">No courses available</option>';
            }
        })
        .catch(error => {
            console.error('Error loading courses:', error);
            courseSelect.innerHTML = '<option value="">Error loading courses</option>';
        });
}

// Form submission functions
function submitModuleForm(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Creating...';

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and refresh page
            document.getElementById('addModalBg').classList.remove('show');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to create module'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
}

function submitCourseForm(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Creating...';

    fetch('/admin/courses', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and refresh page
            document.getElementById('addCourseModalBg').classList.remove('show');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to create course'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
}

function submitCourseContentForm(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Adding...';

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and refresh page
            document.getElementById('batchModalBg').classList.remove('show');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to add content'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
}

function submitEditContentForm(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Updating...';

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and refresh page
            document.getElementById('editContentModalBg').classList.remove('show');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update content'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
}

// Legacy function for compatibility
function showModuleCourses(moduleId, moduleName) {
    toggleModule(moduleId);
}

</script>
@endpush

<!-- Add Module Modal -->
<div class="modal-bg" id="addModalBg">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="bi bi-plus-circle"></i> Add New Content</h3>
            <button type="button" class="modal-close" id="closeAddModal">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <form action="{{ route('admin.modules.store') }}" method="POST" enctype="multipart/form-data" id="addModuleForm">
            <div class="modal-body">
                @csrf
                
                <div class="form-group">
                    <label for="modalProgramSelect">Program <span class="text-danger">*</span></label>
                    <select id="modalProgramSelect" name="program_id" class="form-select" required>
                        <option value="">-- Select Program --</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="batch_id">Batch <span class="text-danger">*</span></label>
                    <select id="batch_id" name="batch_id" class="form-select" required disabled>
                        <option value="">-- Select Batch --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="content_type">Content Type <span class="text-danger">*</span></label>
                    <select id="content_type" name="content_type" class="form-select" required>
                        <option value="module">Module/Lesson</option>
                        <option value="assignment">Assignment</option>
                        <option value="link">External Link</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="module_name">Title <span class="text-danger">*</span></label>
                    <input type="text" id="module_name" name="module_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="module_description">Description</label>
                    <textarea id="module_description" name="module_description" class="form-control" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="learning_mode">Learning Mode <span class="text-danger">*</span></label>
                    <select id="learning_mode" name="learning_mode" class="form-select" required>
                        <option value="Synchronous">Synchronous</option>
                        <option value="Asynchronous">Asynchronous</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="attachment">Attachment</label>
                    <input type="file" id="attachment" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.zip,.png,.jpg,.jpeg,.mp4,.webm,.ogg">
                    <small class="text-muted">Supported formats: PDF, DOC, DOCX, ZIP, Images, Videos</small>
                </div>

                <!-- Content-specific fields will be populated by JavaScript -->
                <div id="contentSpecificFields"></div>

                <div class="form-group">
                    <label for="video_url">Video URL (YouTube/Vimeo)</label>
                    <input type="url" id="video_url" name="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                    <small class="text-muted">Enter a YouTube or Vimeo URL for video content</small>
                </div>

            </div>
            
            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="closeAddModalBtn">Cancel</button>
                <button type="submit" class="add-btn">Create Content</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Course Content Modal -->
<div class="modal-bg" id="batchModalBg">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="bi bi-upload"></i> Add Course Content</h3>
            <button type="button" class="modal-close" id="closeBatchModal">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <form action="{{ route('admin.modules.course-content-store') }}" method="POST" enctype="multipart/form-data" id="courseContentForm">
            <div class="modal-body">
                @csrf
                
                <div class="form-group">
                    <label for="contentProgramSelect">Program <span class="text-danger">*</span></label>
                    <select id="contentProgramSelect" name="program_id" class="form-select" required>
                        <option value="">-- Select Program --</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="contentModuleSelect">Module <span class="text-danger">*</span></label>
                    <select id="contentModuleSelect" name="module_id" class="form-select" required disabled>
                        <option value="">-- Select Module --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="contentCourseSelect">Course <span class="text-danger">*</span></label>
                    <select id="contentCourseSelect" name="course_id" class="form-select" required disabled>
                        <option value="">-- Select Course --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="contentTypeSelect">Content Type <span class="text-danger">*</span></label>
                    <select id="contentTypeSelect" name="content_type" class="form-select" required>
                        <option value="">-- Select Content Type --</option>
                        <option value="lesson">Lesson</option>
                        <option value="assignment">Assignment</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="contentTitle">Content Title <span class="text-danger">*</span></label>
                    <input type="text" id="contentTitle" name="content_title" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="contentDescription">Content Description</label>
                    <textarea id="contentDescription" name="content_description" class="form-control" rows="4"></textarea>
                </div>

                <!-- Dynamic content fields will be added here -->
                <div id="dynamicContentFields"></div>

                <div class="form-group">
                    <label for="contentAttachment">Attachment</label>
                    <input type="file" id="contentAttachment" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.zip,.png,.jpg,.jpeg,.mp4,.webm,.ogg">
                    <small class="text-muted">Supported formats: PDF, DOC, DOCX, ZIP, Images, Videos</small>
                </div>

            </div>
            
            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="closeBatchModalBtn">Cancel</button>
                <button type="submit" class="add-btn" id="submitContentBtn">Add Content</button>
            </div>
        </form>
    </div>
</div>

<!-- Archive Confirmation Modal -->
<div class="modal-bg confirmation-modal" id="archiveConfirmationModal">
    <div class="modal">
        <div class="modal-body">
            <h3><i class="bi bi-archive"></i> Archive Module</h3>
            <p>Are you sure you want to archive this module?</p>
            <p><strong id="archiveModuleName"></strong></p>
            <div class="modal-actions">
                <button type="button" class="cancel-btn" onclick="closeArchiveModal()">Cancel</button>
                <button type="button" class="add-btn" onclick="confirmArchive()">Archive</button>
            </div>
        </div>
    </div>
</div>

<!-- Override Modal -->
<div class="modal-bg" id="overrideModal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="bi bi-unlock-fill"></i> Admin Override Settings</h3>
            <button type="button" class="modal-close" id="closeOverrideModal">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>Configure admin override settings for: <strong id="overrideModuleName"></strong></p>
            <form id="overrideForm">
                <div class="admin-override-checkboxes">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="override_completion_modal" name="admin_override[]" value="completion">
                        <label class="form-check-label" for="override_completion_modal">
                            <i class="bi bi-check-circle"></i> Override Completion Requirements
                        </label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="override_prerequisites_modal" name="admin_override[]" value="prerequisites">
                        <label class="form-check-label" for="override_prerequisites_modal">
                            <i class="bi bi-arrow-right-circle"></i> Override Prerequisites
                        </label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="override_time_limits_modal" name="admin_override[]" value="time_limits">
                        <label class="form-check-label" for="override_time_limits_modal">
                            <i class="bi bi-clock"></i> Override Time Limits
                        </label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="override_access_control_modal" name="admin_override[]" value="access_control">
                        <label class="form-check-label" for="override_access_control_modal">
                            <i class="bi bi-unlock"></i> Override Access Control
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-actions">
            <button type="button" class="cancel-btn" onclick="closeOverrideModal()">Cancel</button>
            <button type="button" class="add-btn" onclick="saveOverrideSettings()">Save Override Settings</button>
        </div>
    </div>
</div>

<!-- Edit Module Modal -->
<div class="modal-bg" id="editModalBg">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="bi bi-pencil"></i> Edit Module</h3>
            <button type="button" class="modal-close" id="closeEditModal">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <form id="editModuleForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label for="editModuleName">Title <span class="text-danger">*</span></label>
                    <input type="text" id="editModuleName" name="module_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="editModuleDescription">Description</label>
                    <textarea id="editModuleDescription" name="module_description" class="form-control" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="editModalProgramSelect">Program <span class="text-danger">*</span></label>
                    <select id="editModalProgramSelect" name="program_id" class="form-select" required>
                        <option value="">-- Select Program --</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="editAttachment">Attachment</label>
                    <input type="file" id="editAttachment" name="attachment" class="form-control">
                    <small class="text-muted">Leave empty to keep current attachment</small>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="closeEditModalBtn">Cancel</button>
                <button type="submit" class="update-btn">Update Module</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Course Modal -->
<div class="modal-bg" id="addCourseModalBg">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="bi bi-journal-plus"></i> Add New Course</h3>
            <button type="button" class="modal-close" id="closeAddCourseModal">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <form id="addCourseForm">
            <div class="modal-body">
                @csrf

                <div class="form-group">
                    <label for="courseProgramSelect">Program <span class="text-danger">*</span></label>
                    <select id="courseProgramSelect" name="program_id" class="form-select" required>
                        <option value="">-- Select Program --</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="courseModuleSelect">Module <span class="text-danger">*</span></label>
                    <select id="courseModuleSelect" name="module_id" class="form-select" required disabled>
                        <option value="">-- Select Module --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="courseName">Course Name <span class="text-danger">*</span></label>
                    <input type="text" id="courseName" name="subject_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="courseDescription">Course Description</label>
                    <textarea id="courseDescription" name="subject_description" class="form-control" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="coursePrice">Course Price <span class="text-danger">*</span></label>
                    <input type="number" id="coursePrice" name="subject_price" class="form-control" min="0" step="0.01" required>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="courseRequired" name="is_required" value="1">
                        <label class="form-check-label" for="courseRequired">
                            <i class="bi bi-exclamation-circle"></i> This course is required
                        </label>
                    </div>
                </div>

            </div>
            
            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="closeAddCourseModalBtn">Cancel</button>
                <button type="submit" class="add-btn">Create Course</button>
            </div>
        </form>
    </div>
</div>

<!-- Module Courses Modal -->
<div class="modal-bg" id="moduleCoursesModalBg">
    <div class="modal large-modal">
        <div class="modal-header">
            <h3><i class="bi bi-journals"></i> <span id="moduleCoursesTitle">Module Courses</span></h3>
            <button type="button" class="modal-close" id="closeModuleCoursesModal">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="moduleCoursesContent">
                <div class="loading-spinner">
                    <i class="bi bi-hourglass-split"></i> Loading courses...
                </div>
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" class="cancel-btn" id="closeModuleCoursesModalBtn">Close</button>
        </div>
    </div>
</div>

<!-- Course Content Modal -->
<div class="modal-bg" id="courseContentModalBg">
    <div class="modal large-modal">
        <div class="modal-header">
            <h3><i class="bi bi-book"></i> <span id="courseContentTitle">Course Content</span></h3>
            <button type="button" class="modal-close" id="closeCourseContentModal">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="courseContentContent">
                <div class="loading-spinner">
                    <i class="bi bi-hourglass-split"></i> Loading content...
                </div>
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" class="cancel-btn" id="closeCourseContentModalBtn">Close</button>
        </div>
    </div>
</div>

<!-- Edit Content Modal -->
<div class="modal-bg" id="editContentModalBg">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="bi bi-pencil"></i> Edit Content Item</h3>
            <button type="button" class="modal-close" id="closeEditContentModal">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <form id="editContentForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <input type="hidden" id="edit_content_id" name="content_id">
                
                <div class="form-group">
                    <label for="edit_content_title">Title <span class="text-danger">*</span></label>
                    <input type="text" id="edit_content_title" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_content_description">Description</label>
                    <textarea id="edit_content_description" name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_content_type">Content Type <span class="text-danger">*</span></label>
                    <select id="edit_content_type" name="type" class="form-select" required>
                        <option value="">Select type...</option>
                        <option value="lesson">Lesson</option>
                        <option value="assignment">Assignment</option>
                        <option value="quiz">Quiz</option>
                        <option value="test">Test</option>
                        <option value="pdf">PDF Document</option>
                        <option value="link">External Link</option>
                    </select>
                </div>
                
                <div class="form-group" id="edit_file_section">
                    <label for="edit_content_file">Replace File (optional)</label>
                    <input type="file" id="edit_content_file" name="file" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif">
                    <small class="text-muted">Current file will be kept if no new file is selected.</small>
                </div>
                
                <div class="form-group" id="edit_link_section" style="display: none;">
                    <label for="edit_content_link">External Link URL</label>
                    <input type="url" id="edit_content_link" name="link" class="form-control" placeholder="https://...">
                </div>
                
                <div class="form-group">
                    <label for="edit_content_order">Display Order</label>
                    <input type="number" id="edit_content_order" name="sort_order" class="form-control" min="1">
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="closeEditContentModalBtn">Cancel</button>
                <button type="submit" class="update-btn">Update Content</button>
            </div>
        </form>
    </div>
</div>

@endsection
