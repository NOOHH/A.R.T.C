@extends('professor.layout')

@section('title', 'Module Management')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="{{ asset('css/admin/admin-modules.css') }}" rel="stylesheet">
<style>
  /* Reset and ensure proper positioning */
  .page-content {
    position: relative;
    z-index: 1;
    background: #f8f9fa;
    min-height: calc(100vh - 68px);
  }
  
  .content-wrapper {
    position: relative;
    z-index: 1;
    background: #f8f9fa;
    padding: 1rem;
    min-height: calc(100vh - 68px);
  }

  /* Ensure module management content is properly positioned */
  .modules-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.07);
    position: relative;
    z-index: 1;
  }
  
  /* Override any conflicting admin dashboard styles */
  .main-header .modules-container,
  .main-header .container-fluid,
  .main-header .page-content,
  .main-header .content-wrapper {
    display: none !important;
  }
  
  /* Force proper layout for professor module management */
  .page-content .content-wrapper {
    padding: 1rem;
    overflow-y: auto;
    min-height: 0;
    margin-left: 0;
  }

  /* Ensure header stays at top */
  .main-header {
    position: relative;
    z-index: 1000;
    background: white;
    border-bottom: 1px solid #dee2e6;
  }

  /* Ensure main wrapper doesn't overlap header */
  .main-wrapper {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 68px);
    overflow: hidden;
  }

  /* Override any admin dashboard CSS that might interfere */
  .admin-container {
    display: flex;
    flex-direction: column;
    height: 100vh;
    overflow: hidden;
  }

  /* Ensure content is not rendered inside header */
  .main-header .content-wrapper,
  .main-header .page-content,
  .main-header .modules-container {
    display: none !important;
  }

  /* Force proper content positioning */
  .content-wrapper > * {
    position: relative !important;
    z-index: 1 !important;
  }

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
  
  .drag-handle,
  .module-drag-handle,
  .course-drag-handle,
  .content-drag-handle {
    cursor: move;
    color: rgba(108, 117, 125, 0.7);
    margin-right: 0.5rem;
    font-size: 1.1rem;
    transition: color 0.2s ease;
  }
  
  .drag-handle:hover,
  .module-drag-handle:hover,
  .course-drag-handle:hover,
  .content-drag-handle:hover {
    color: rgba(108, 117, 125, 1);
  }
  
  .module-header .module-drag-handle {
    color: rgba(255, 255, 255, 0.7);
  }
  
  .module-header .module-drag-handle:hover {
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

  /* Split Layout Styles */
  .admin-split-container {
    display: flex;
    height: calc(100vh - 200px);
    gap: 1.5rem;
    margin-top: 2rem;
  }

  .admin-modules-panel {
    flex: 0 0 60%;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    overflow-y: auto;
    padding: 1.5rem;
  }

  .admin-content-panel {
    flex: 1;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .content-viewer-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 15px 15px 0 0;
  }

  .content-viewer-header h3 {
    margin: 0;
    font-size: 1.4rem;
    font-weight: 600;
  }

  .content-viewer-header small {
    opacity: 0.9;
    font-size: 0.9rem;
  }

  .content-viewer-body {
    flex: 1;
    padding: 2rem;
    overflow-y: auto;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .content-placeholder {
    text-align: center;
    color: #6c757d;
  }

  .content-display {
    width: 100%;
    height: 100%;
  }

  .content-display iframe {
    width: 100%;
    height: 100%;
    border: none;
    border-radius: 10px;
  }

  .content-display .content-text {
    line-height: 1.6;
    font-size: 1.1rem;
  }

  .courses-preview, .content-items-preview {
    max-height: 400px;
    overflow-y: auto;
  }

  .course-preview-item, .content-item-preview {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    border-left: 4px solid #667eea;
  }

  .course-preview-item h6, .content-item-preview h6 {
    margin: 0 0 0.5rem 0;
    color: #495057;
    font-weight: 600;
  }

  .course-preview-item p, .content-item-preview p {
    margin: 0;
    font-size: 0.9rem;
  }

  .content-item-preview .badge {
    margin-top: 0.5rem;
  }

  .content-item.active {
    background: #e3f2fd !important;
    border-color: #2196f3 !important;
    box-shadow: 0 4px 12px rgba(33, 150, 243, 0.2) !important;
  }

  .content-frame {
    width: 100%;
    height: 500px;
    border: none;
    border-radius: 10px;
  }

  .link-preview, .assignment-preview {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 2rem;
    text-align: center;
  }

  .video-container, .pdf-viewer {
    background: #000;
    border-radius: 10px;
    overflow: hidden;
  }

  .content-details {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.5rem;
    border-left: 4px solid #667eea;
  }

  @media (max-width: 1200px) {
    .admin-split-container {
      flex-direction: column;
      height: auto;
    }
    
    .admin-modules-panel {
      flex: none;
      max-height: 500px;
    }
    
    .admin-content-panel {
      flex: none;
      height: 400px;
    }
  }
</style>
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
                    <option value="lesson">📚 Lesson</option>
                    <option value="video">🎥 Video</option>
                    <option value="assignment">📝 Assignment</option>
                    <option value="quiz">❓ Quiz</option>
                    <option value="test">📋 Test</option>
                    <option value="link">🔗 External Link</option>
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
        <a href="{{ route('professor.modules.archived') }}" class="view-archived-btn">
            <i class="bi bi-archive"></i> View Archived
        </a>
        <a href="{{ route('professor.quiz-generator') }}" class="quiz-generator-btn">
            <i class="bi bi-robot"></i> AI Quiz Generator
        </a>
    </div>

    <!-- Split Layout Container -->
    <div class="admin-split-container">
        <!-- Left Panel - Modules List -->
        <div class="admin-modules-panel">
            <div id="modulesDisplayArea">
                @if(request('program_id') && isset($modules))
                    @if($modules->count() > 0)
                        <div class="modules-hierarchy" id="modulesHierarchy">
                            @foreach($modules as $module)
                                @php $escapedModuleName = addslashes($module->module_name); @endphp
                                <div class="module-container" data-module-id="{{ $module->modules_id }}">
                                    <div class="module-header" onclick="toggleModule({{ $module->modules_id }})">
                                        <div class="module-title-section">
                                            <i class="module-drag-handle bi bi-grip-vertical"></i>
                                            <i class="module-toggle-icon bi bi-chevron-right"></i>
                                            <div>
                                                <h4 class="mb-0">{{ $module->module_name }}</h4>
                                                @if($module->module_description)
                                                    <small class="opacity-75">{{ $module->module_description }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="module-actions" onclick="event.stopPropagation();">
                                            <div class="action-btn-group">
                                                <button class="action-btn-green" onclick="showAddCourseModal({{ $module->modules_id }}, '{{ $escapedModuleName }}')"><i class="bi bi-plus-circle"></i> Add Course</button>
                                                <button class="action-btn-green" onclick="editModule({{ $module->modules_id }})"><i class="bi bi-pencil"></i></button>
                                                <button class="action-btn-green" onclick="deleteModule({{ $module->modules_id }})"><i class="bi bi-trash"></i></button>
                                                <button class="action-btn-green" onclick="openOverrideModal('module', {{ $module->modules_id }}, '{{ $escapedModuleName }}')"><i class="bi bi-sliders"></i> Override Settings</button>
                                            </div>
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
        
        <!-- Right Panel - Content Viewer -->
        <div class="admin-content-panel">
            <div class="content-viewer-header">
                <h3 id="content-title">Content Viewer</h3>
                <small id="content-subtitle">Select a module or course to view content</small>
            </div>
            
            <div class="content-viewer-body" id="contentViewer">
                <div class="content-placeholder">
                    <i class="bi bi-file-earmark-text" style="font-size: 4rem; color: #6c757d; margin-bottom: 1rem;"></i>
                    <h4 style="color: #6c757d;">No Content Selected</h4>
                    <p style="color: #6c757d;">Click on a module or course to view its content here</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Global variables
let currentArchiveModuleId = null;
let currentOverrideModuleId = null;

// Module toggle functionality with content viewer
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
    
    // Load module content in viewer
    loadModuleContentInViewer(moduleId);
}

// Course toggle functionality with content viewer
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
    
    // Load course content in viewer
    loadCourseContentInViewer(moduleId, courseId);
}

// Load module content in the content viewer
function loadModuleContentInViewer(moduleId) {
    const titleElement = document.getElementById('content-title');
    const subtitleElement = document.getElementById('content-subtitle');
    const viewerBody = document.getElementById('contentViewer');
    
    // Show loading state
    titleElement.textContent = 'Loading Module...';
    subtitleElement.textContent = 'Fetching module details';
    viewerBody.innerHTML = '<div class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</div>';
    
    // Fetch module content
    fetch(`/professor/modules/${moduleId}/content`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                titleElement.textContent = data.module.module_name;
                subtitleElement.textContent = `Module • ${data.courses?.length || 0} courses`;
                
                let contentHtml = `
                    <div class="content-display">
                        <h4>Module Overview</h4>
                        <p><strong>Description:</strong> ${data.module.module_description || 'No description available'}</p>
                        <p><strong>Type:</strong> ${data.module.type || 'Standard'}</p>
                        <p><strong>Order:</strong> ${data.module.module_order || 'Not set'}</p>
                        
                        <h5 class="mt-4">Courses (${data.courses?.length || 0})</h5>
                        <div class="courses-preview">
                `;
                
                if (data.courses && data.courses.length > 0) {
                    data.courses.forEach(course => {
                        contentHtml += `
                            <div class="course-preview-item">
                                <h6>${course.course_name}</h6>
                                <p class="text-muted">${course.course_description || 'No description'}</p>
                            </div>
                        `;
                    });
                } else {
                    contentHtml += '<p class="text-muted">No courses available</p>';
                }
                
                contentHtml += `
                        </div>
                    </div>
                `;
                
                viewerBody.innerHTML = contentHtml;
            } else {
                viewerBody.innerHTML = '<div class="alert alert-danger">Failed to load module content</div>';
            }
        })
        .catch(error => {
            console.error('Error loading module content:', error);
            viewerBody.innerHTML = '<div class="alert alert-danger">Error loading content</div>';
        });
}

// Load course content in the content viewer
function loadCourseContentInViewer(moduleId, courseId) {
    const titleElement = document.getElementById('content-title');
    const subtitleElement = document.getElementById('content-subtitle');
    const viewerBody = document.getElementById('contentViewer');
    
    // Show loading state
    titleElement.textContent = 'Loading Course...';
    subtitleElement.textContent = 'Fetching course content';
    viewerBody.innerHTML = '<div class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</div>';
    
    // Fetch course content
    fetch(`/professor/modules/${moduleId}/courses/${courseId}/content`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                titleElement.textContent = data.course.course_name;
                subtitleElement.textContent = `Course • ${data.content_items?.length || 0} items`;
                
                let contentHtml = `
                    <div class="content-display">
                        <h4>Course Details</h4>
                        <p><strong>Description:</strong> ${data.course.course_description || 'No description available'}</p>
                        <p><strong>Type:</strong> ${data.course.course_type || 'Standard'}</p>
                        
                        <h5 class="mt-4">Content Items (${data.content_items?.length || 0})</h5>
                        <div class="content-items-preview">
                `;
                
                if (data.content_items && data.content_items.length > 0) {
                    data.content_items.forEach(item => {
                        contentHtml += `
                            <div class="content-item-preview">
                                <h6><i class="bi bi-${getContentIcon(item.content_type)}"></i> ${item.content_title}</h6>
                                <p class="text-muted">${item.content_description || 'No description'}</p>
                                <small class="badge bg-secondary">${item.content_type}</small>
                            </div>
                        `;
                    });
                } else {
                    contentHtml += '<p class="text-muted">No content items available</p>';
                }
                
                contentHtml += `
                        </div>
                    </div>
                `;
                
                viewerBody.innerHTML = contentHtml;
            } else {
                viewerBody.innerHTML = '<div class="alert alert-danger">Failed to load course content</div>';
            }
        })
        .catch(error => {
            console.error('Error loading course content:', error);
            viewerBody.innerHTML = '<div class="alert alert-danger">Error loading content</div>';
        });
}

// Helper function to get content type icon
function getContentIcon(contentType) {
    const icons = {
        'video': 'play-circle',
        'quiz': 'question-circle',
        'assignment': 'pencil-square',
        'link': 'link-45deg',
        'test': 'clipboard-check'
    };
    return icons[contentType] || 'file-earmark';
}

// Load individual content item in the content viewer
function loadContentInViewer(contentId, contentType, contentTitle, moduleId, courseId) {
    const titleElement = document.getElementById('content-title');
    const subtitleElement = document.getElementById('content-subtitle');
    const viewerBody = document.getElementById('contentViewer');
    
    // Show loading state
    titleElement.textContent = 'Loading Content...';
    subtitleElement.textContent = 'Fetching content details';
    viewerBody.innerHTML = '<div class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</div>';
    
    // Fetch content details
    fetch(`/professor/content/${contentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const content = data.content;
                titleElement.textContent = content.content_title || 'Content';
                subtitleElement.textContent = `${(content.content_type || 'CONTENT').toUpperCase()} • Course ID: ${courseId}`;
                
                let contentHtml = '';
                const contentType = (content.content_type || 'lesson').toLowerCase();
                
                switch(contentType) {
                    case 'video':
                        if (content.content_url || content.attachment_path) {
                            const videoUrl = content.content_url || `/storage/${content.attachment_path}`;
                            let videoPlayer = '';
                            
                            // Check if it's a YouTube or Vimeo URL
                            if (content.content_url && (content.content_url.includes('youtube.com') || content.content_url.includes('youtu.be') || content.content_url.includes('vimeo.com'))) {
                                let embedUrl = content.content_url;
                                
                                // Convert YouTube URLs to embed format
                                if (content.content_url.includes('youtube.com/watch?v=')) {
                                    const videoId = content.content_url.split('v=')[1].split('&')[0];
                                    embedUrl = `https://www.youtube.com/embed/${videoId}`;
                                } else if (content.content_url.includes('youtu.be/')) {
                                    const videoId = content.content_url.split('youtu.be/')[1].split('?')[0];
                                    embedUrl = `https://www.youtube.com/embed/${videoId}`;
                                } else if (content.content_url.includes('vimeo.com/')) {
                                    const videoId = content.content_url.split('vimeo.com/')[1].split('/')[0];
                                    embedUrl = `https://player.vimeo.com/video/${videoId}`;
                                }
                                
                                videoPlayer = `
                                    <div class="video-container">
                                        <iframe class="content-frame" src="${embedUrl}" style="width: 100%; height: 450px; border: 1px solid #ddd;" allowfullscreen></iframe>
                                    </div>
                                `;
                            } else {
                                // Local video file
                                videoPlayer = `
                                    <div class="video-container">
                                        <video controls style="width: 100%; max-height: 450px;" class="border">
                                            <source src="${videoUrl}" type="video/mp4">
                                            <source src="${videoUrl}" type="video/webm">
                                            <source src="${videoUrl}" type="video/ogg">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                `;
                            }
                            
                            contentHtml = `
                                <div class="content-display">
                                    ${videoPlayer}
                                    <div class="content-details mt-3">
                                        <h5>Video Details</h5>
                                        <p><strong>Description:</strong> ${content.content_description || 'No description'}</p>
                                        <p><strong>Source:</strong> <a href="${videoUrl}" target="_blank">${content.content_url ? 'External Link' : 'Uploaded File'}</a></p>
                                    </div>
                                </div>
                            `;
                        } else {
                            contentHtml = '<div class="alert alert-warning">Video URL not available</div>';
                        }
                        break;
                    
                    case 'lesson':
                        // Display lesson video if available, otherwise show description
                        let lessonHtml = '';
                        if (content.content_url || content.attachment_path) {
                            const videoUrl = content.content_url || `/storage/${content.attachment_path}`;
                            
                            // Check if it's a YouTube or Vimeo URL
                            if (content.content_url && (content.content_url.includes('youtube.com') || content.content_url.includes('youtu.be') || content.content_url.includes('vimeo.com'))) {
                                let embedUrl = content.content_url;
                                
                                // Convert YouTube URLs to embed format
                                if (content.content_url.includes('youtube.com/watch?v=')) {
                                    const videoId = content.content_url.split('v=')[1].split('&')[0];
                                    embedUrl = `https://www.youtube.com/embed/${videoId}`;
                                } else if (content.content_url.includes('youtu.be/')) {
                                    const videoId = content.content_url.split('youtu.be/')[1].split('?')[0];
                                    embedUrl = `https://www.youtube.com/embed/${videoId}`;
                                } else if (content.content_url.includes('vimeo.com/')) {
                                    const videoId = content.content_url.split('vimeo.com/')[1].split('/')[0];
                                    embedUrl = `https://player.vimeo.com/video/${videoId}`;
                                }
                                
                                lessonHtml = `
                                    <div class="video-container">
                                        <iframe class="content-frame" src="${embedUrl}" style="width: 100%; height: 450px; border: 1px solid #ddd;" allowfullscreen></iframe>
                                    </div>
                                `;
                            } else {
                                // Local video file
                                lessonHtml = `
                                    <div class="video-container">
                                        <video controls style="width: 100%; max-height: 450px;" class="border">
                                            <source src="${videoUrl}" type="video/mp4">
                                            <source src="${videoUrl}" type="video/webm">
                                            <source src="${videoUrl}" type="video/ogg">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                `;
                            }
                        }
                        
                        contentHtml = `
                            <div class="content-display">
                                ${lessonHtml}
                                <div class="content-details mt-3">
                                    <h5>Lesson Details</h5>
                                    <p><strong>Description:</strong> ${content.content_description || 'No description'}</p>
                                    ${content.content_url ? `<p><strong>Video URL:</strong> <a href="${content.content_url}" target="_blank">${content.content_url}</a></p>` : ''}
                                </div>
                            </div>
                        `;
                        break;
                    
                    case 'pdf':
                        if (content.attachment_path) {
                            const fileUrl = `/storage/${content.attachment_path}`;
                            const fileName = content.attachment_path.split('/').pop();
                            
                            contentHtml = `
                                <div class="content-display">
                                    <div class="pdf-viewer">
                                        <div class="pdf-controls mb-2">
                                            <div class="btn-group">
                                                <a href="${fileUrl}" target="_blank" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-fullscreen"></i> Full Screen
                                                </a>
                                                <a href="${fileUrl}" download class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                        <iframe class="content-frame" src="${fileUrl}#toolbar=1&navpanes=1&scrollbar=1" 
                                                style="width: 100%; height: 700px; border: 1px solid #ddd; border-radius: 5px;"
                                                allowfullscreen>
                                            <p>Your browser does not support PDFs. 
                                               <a href="${fileUrl}" target="_blank">Download the PDF</a>
                                            </p>
                                        </iframe>
                                    </div>
                                    <div class="content-details mt-3">
                                        <h5>Document Details</h5>
                                        <p><strong>Description:</strong> ${content.content_description || 'No description'}</p>
                                        <p><strong>File:</strong> ${fileName}</p>
                                    </div>
                                </div>
                            `;
                        } else {
                            contentHtml = '<div class="alert alert-warning">Document file not available</div>';
                        }
                        break;
                    
                    case 'link':
                        if (content.content_url) {
                            contentHtml = `
                                <div class="content-display">
                                    <div class="link-preview">
                                        <h5><i class="bi bi-link-45deg"></i> External Link</h5>
                                        <p><strong>Description:</strong> ${content.content_description || 'No description'}</p>
                                        <p><strong>URL:</strong> <a href="${content.content_url}" target="_blank">${content.content_url}</a></p>
                                        <div class="mt-3">
                                            <a href="${content.content_url}" target="_blank" class="btn btn-primary">
                                                <i class="bi bi-box-arrow-up-right"></i> Open Link
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `;
                        } else {
                            contentHtml = '<div class="alert alert-warning">External URL not available</div>';
                        }
                        break;
                    
                    case 'assignment':
                        contentHtml = `
                            <div class="content-display">
                                <div class="assignment-preview">
                                    <h5><i class="bi bi-pencil-square"></i> Assignment Details</h5>
                                    <p><strong>Description:</strong> ${content.content_description || 'No description'}</p>
                                    ${content.due_date ? `<p><strong>Due Date:</strong> ${new Date(content.due_date).toLocaleDateString()}</p>` : ''}
                                    ${content.submission_instructions ? `<div class="mt-3"><h6>Instructions:</h6><p>${content.submission_instructions}</p></div>` : ''}
                                    <div class="mt-3">
                                        <button class="btn btn-info" onclick="viewSubmissions(${contentId})">
                                            <i class="bi bi-file-earmark-text"></i> View Submissions
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        break;
                    
                    default:
                        contentHtml = `
                            <div class="content-display">
                                <h5>${(content.content_type || 'Content').charAt(0).toUpperCase() + (content.content_type || 'Content').slice(1)} Details</h5>
                                <p><strong>Description:</strong> ${content.content_description || 'No description'}</p>
                                ${content.content_url ? `<p><strong>URL:</strong> <a href="${content.content_url}" target="_blank">${content.content_url}</a></p>` : ''}
                            </div>
                        `;
                }
                
                viewerBody.innerHTML = contentHtml;
                
                // Mark content item as active
                document.querySelectorAll('.content-item').forEach(el => el.classList.remove('active'));
                document.querySelector(`[data-content-id="${contentId}"]`).classList.add('active');
                
            } else {
                viewerBody.innerHTML = '<div class="alert alert-danger">Failed to load content</div>';
            }
        })
        .catch(error => {
            console.error('Error loading content:', error);
            viewerBody.innerHTML = '<div class="alert alert-danger">Error loading content</div>';
        });
}

// Load courses for a module
function loadModuleCourses(moduleId) {
    const container = document.getElementById(`courses-container-${moduleId}`);
    if (container.dataset.loaded === 'true') return;
    
    container.innerHTML = '<div class="text-center p-3"><i class="bi bi-arrow-clockwise fa-spin"></i> Loading courses...</div>';
    
    fetch(`/professor/modules/${moduleId}/courses`)
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
                        <i class="course-drag-handle bi bi-grip-vertical" onclick="event.stopPropagation();" style="cursor: move; color: rgba(255,255,255,0.7);" title="Drag to move course"></i>
                        <i class="course-toggle-icon bi bi-chevron-right"></i>
                        <div>
                            <h5 class="mb-0">${course.subject_name}</h5>
                            ${course.subject_description ? `<small class="opacity-75">${course.subject_description}</small>` : ''}
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation();">
                        <div class="action-btn-group">
                            <button class="action-btn-green" onclick="showAddContentModal(${moduleId}, ${course.subject_id}, '${course.subject_name}')"><i class="bi bi-plus"></i> Add Content</button>
                            <button class="action-btn-green" onclick="editCourse(${course.subject_id})"><i class="bi bi-pencil"></i></button>
                            <button class="action-btn-green" onclick="deleteCourse(${course.subject_id})"><i class="bi bi-trash"></i></button>
                            <button class="action-btn-green" onclick="openOverrideModal('course', ${course.subject_id}, '${course.subject_name}')"><i class="bi bi-sliders"></i> Override Settings</button>
                        </div>
                    </div>
                </div>
                <div class="course-content" id="course-content-${moduleId}-${course.subject_id}">
                    <div id="content-container-${moduleId}-${course.subject_id}">
                        <!-- Content items will be loaded here -->
                    </div>
                </div>
            </div>`;
    });
    
    container.innerHTML = html;
    
    // Initialize course-level sorting for this module
    initializeCourseSorting(moduleId);
}

// Load content items for a course
function loadCourseContent(moduleId, courseId) {
    const container = document.getElementById(`content-container-${moduleId}-${courseId}`);
    if (container.dataset.loaded === 'true') return;
    
    container.innerHTML = '<div class="text-center p-3"><i class="bi bi-arrow-clockwise fa-spin"></i> Loading content...</div>';
    
    fetch(`/professor/courses/${courseId}/content`)
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
            <div class="content-item" data-content-id="${item.id}" onclick="loadContentInViewer(${item.id}, '${item.content_type}', '${item.content_title}', ${moduleId}, ${courseId})" style="cursor: pointer;">
                <div class="content-item-info">
                    <i class="content-drag-handle bi bi-grip-vertical" onclick="event.stopPropagation();" style="cursor: move;"></i>
                    <span class="content-item-type ${typeClass}">
                        <i class="bi ${typeIcon}"></i> ${item.content_type}
                    </span>
                    <div>
                        <strong>${item.content_title}</strong>
                        ${item.content_description ? `<br><small class="text-muted">${item.content_description}</small>` : ''}
                    </div>
                </div>
                <div class="content-item-actions" onclick="event.stopPropagation();">
                    <div class="action-btn-group">
                        <button class="action-btn-green" onclick="loadContentInViewer(${item.id}, '${item.content_type}', '${item.content_title}', ${moduleId}, ${courseId})" title="View Content"><i class="bi bi-eye"></i></button>
                        <button class="action-btn-green" onclick="editContent(${item.id})"><i class="bi bi-pencil"></i></button>
                        <button class="action-btn-green" onclick="deleteContent(${item.id})"><i class="bi bi-trash"></i></button>
                        <button class="action-btn-green" onclick="openOverrideModal('content', ${item.id}, '${item.content_title}')"><i class="bi bi-sliders"></i> Override Settings</button>
                    </div>
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
    // Implementation for showing add course modal
    console.log('Show add course modal for module:', moduleId);
}

function showAddContentModal(moduleId, courseId, courseName = '') {
    // Implementation for showing add content modal
    console.log('Show add content modal for course:', courseId);
}

function editModule(moduleId) {
    // Implementation for editing module
    console.log('Edit module:', moduleId);
}

function deleteModule(moduleId) {
    if (confirm('Are you sure you want to delete this module? This action cannot be undone.')) {
        fetch(`/professor/modules/${moduleId}`, {
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

function editCourse(courseId) {
    // Implementation for editing course
    console.log('Edit course:', courseId);
}

function deleteCourse(courseId) {
    if (confirm('Are you sure you want to delete this course? This action cannot be undone.')) {
        fetch(`/professor/courses/${courseId}`, {
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
    // Implementation for editing content
    console.log('Edit content:', contentId);
}

function deleteContent(contentId) {
    if (confirm('Are you sure you want to delete this content? This action cannot be undone.')) {
        fetch(`/professor/content/${contentId}`, {
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
    console.log('View submissions for assignment:', assignmentId);
}

function openOverrideModal(type, id, name) {
    // Implementation for override modal
    console.log('Open override modal for:', type, id, name);
}

// Initialize sortable functionality for modules
document.addEventListener('DOMContentLoaded', function() {
    console.log('Professor modules JavaScript loaded');
    
    const modulesContainer = document.getElementById('modulesHierarchy');
    if (modulesContainer && typeof Sortable !== 'undefined') {
        // Module-level sorting
        new Sortable(modulesContainer, {
            handle: '.module-drag-handle',
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
    }
    
    // Program selector event listener
    const programSelect = document.getElementById('programSelect');
    if (programSelect) {
        programSelect.addEventListener('change', function() {
            const programId = this.value;
            if (programId) {
                window.location.href = `/professor/modules?program_id=${programId}`;
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
    }
});

// Initialize course sorting within modules
function initializeCourseSorting(moduleId) {
    const coursesContainer = document.getElementById(`courses-container-${moduleId}`);
    if (coursesContainer && typeof Sortable !== 'undefined') {
        new Sortable(coursesContainer, {
            handle: '.course-drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function(evt) {
                const courseIds = Array.from(coursesContainer.children).map(el => 
                    el.getAttribute('data-course-id')
                ).filter(id => id !== null);
                
                updateCourseOrder(moduleId, courseIds);
            }
        });
    }
}

// Update module order
function updateModuleOrder(moduleIds) {
    fetch('/professor/modules/update-order', {
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

// Update course order within a module
function updateCourseOrder(moduleId, courseIds) {
    fetch('/professor/courses/update-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            module_id: moduleId,
            course_ids: courseIds 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Course order updated successfully');
        } else {
            console.error('Failed to update course order');
        }
    })
    .catch(error => {
        console.error('Error updating course order:', error);
    });
}

// Update content order
function updateContentOrder(contentIds) {
    fetch('/professor/content/update-order', {
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
</script>
@endpush
