@extends('admin.admin-dashboard.admin-dashboard-layout')

@section('title', 'Modules')

@push('styles')
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
            <i class="bi bi-plus-circle"></i> Add New Module
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

    <!-- Split Layout Container -->
    <div class="admin-split-container">
        <!-- Left Panel - Modules List -->
        <div class="admin-modules-panel">
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
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    padding: 2rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
  }
  
  .module-header:hover {
    background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
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
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
    padding: 1.5rem 2rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
  }
  
  /* Mobile Course Header */
  @media (max-width: 768px) {
    .course-header {
      padding: 1.2rem 1.5rem;
      flex-direction: column;
      align-items: flex-start;
      gap: 0.5rem;
    }
    
    .course-header h5 {
      font-size: 1.1rem;
      line-height: 1.3;
    }
  }
  
  @media (max-width: 480px) {
    .course-header {
      padding: 1rem 1.2rem;
    }
    
    .course-header h5 {
      font-size: 1rem;
    }
  }
  
  .course-header:hover {
    background: linear-gradient(135deg, #2980b9 0%, #1f618d 100%);
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
  
  /* Mobile Content Items */
  @media (max-width: 768px) {
    .content-item {
      flex-direction: column;
      align-items: flex-start;
      gap: 1rem;
      padding: 1.2rem 1rem;
    }
    
    .content-item-info {
      width: 100%;
      flex-direction: column;
      align-items: flex-start;
      gap: 0.8rem;
    }
    
    .content-item:hover {
      transform: none;
    }
  }
  
  @media (max-width: 480px) {
    .content-item {
      padding: 1rem 0.8rem;
      margin-bottom: 0.5rem;
      border-radius: 8px;
    }
    
    .content-item-info {
      gap: 0.6rem;
    }
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
    display: flex; /* Show the module actions */
    gap: 1rem;
    align-items: center;
    background: transparent; /* Remove any background */
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
    background: #2c3e50;
    color: white !important;
    border: 2px solid rgba(255, 255, 255, 0.3) !important;
  }
  
  .add-course-btn:hover {
    background: #34495e;
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
    background: #2c3e50;
    color: white;
  }
  
  .add-course-btn:hover {
    background: #34495e;
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
  
  /* Professional Action Button Styles */
  .action-btn-green {
    background: #2c3e50;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 0.35rem 0.65rem;
    font-size: 0.8rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(44, 62, 80, 0.1);
    min-width: 28px;
    min-height: 28px;
    text-decoration: none;
    cursor: pointer;
  }
  
  .action-btn-green:hover {
    background: #34495e;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(44, 62, 80, 0.2);
  }
  
  .action-btn-green i {
    font-size: 0.85rem;
    line-height: 1;
  }
  
  /* Specific button types */
  .action-btn-green.add-btn {
    background: #3498db;
    padding: 0.4rem 0.8rem;
    font-size: 0.85rem;
  }
  
  .action-btn-green.add-btn:hover {
    background: #2980b9;
  }
  
  .action-btn-green.edit-btn {
    background: #f39c12;
  }
  
  .action-btn-green.edit-btn:hover {
    background: #e67e22;
  }
  
  .action-btn-green.archive-btn {
    background: #95a5a6;
  }
  
  .action-btn-green.archive-btn:hover {
    background: #7f8c8d;
  }
  
  .action-btn-green.view-btn {
    background: #3498db;
  }
  
  .action-btn-green.view-btn:hover {
    background: #2980b9;
  }
  
  .action-btn-green.override-btn {
    background: #8e44ad;
    padding: 0.35rem 0.6rem;
    font-size: 0.75rem;
    white-space: nowrap;
  }
  
  .action-btn-green.override-btn:hover {
    background: #7d3c98;
  }
  
  /* Action button groups */
  .action-btn-group {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    flex-wrap: wrap;
    background: transparent; /* Remove any background */
    padding: 0; /* Remove any padding */
    margin: 0; /* Remove any margin */
  }
  
  /* Course header actions */
  .course-header .action-btn-group {
    gap: 0.3rem;
  }
  
  /* Content item actions - prevent overflow */
  .content-item-actions {
    display: flex;
    gap: 0.3rem;
    flex-shrink: 0;
    align-items: center;
    min-width: 0;
  }
  
  .content-item-actions .action-btn-green {
    padding: 0.25rem 0.4rem;
    font-size: 0.75rem;
    min-width: 24px;
    min-height: 24px;
    flex-shrink: 0;
  }
  
  .content-item-actions .action-btn-green.override-btn {
    padding: 0.25rem 0.45rem;
    font-size: 0.7rem;
    max-width: 90px;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  
  /* Content item layout improvements */
  .content-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.8rem 1rem;
    margin-bottom: 0.75rem;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    transition: all 0.3s ease;
    gap: 1rem;
  }
  
  .content-item-info {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    flex: 1;
    min-width: 0;
  }
  
  .content-item-info h6 {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
  }
  
  /* Mobile responsiveness for buttons */
  @media (max-width: 768px) {
    .action-btn-group {
      flex-wrap: wrap;
      gap: 0.3rem;
    }
    
    .action-btn-green {
      font-size: 0.75rem;
      padding: 0.3rem 0.5rem;
    }
    
    .action-btn-green.override-btn {
      font-size: 0.7rem;
      padding: 0.3rem 0.4rem;
    }
    
    .content-item {
      flex-direction: column;
      align-items: flex-start;
      gap: 0.8rem;
      padding: 1rem;
    }
    
    .content-item-actions {
      width: 100%;
      justify-content: flex-end;
    }
    
    .content-item-info h6 {
      max-width: none;
      white-space: normal;
    }
  }
</style>

<div class="modules-hierarchy" id="modulesHierarchy">
  @foreach($modules as $module)
    @php $escapedModuleName = addslashes($module->module_name); @endphp
    <div class="module-container" data-module-id="{{ $module->modules_id }}">
      <div class="module-header" onclick="toggleModule({{ $module->modules_id }})">
        <div class="module-title-section">
          <i class="module-drag-handle bi bi-grip-vertical" onclick="event.stopPropagation();"></i>
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
            <button class="action-btn-green add-btn" onclick="showAddCourseModal({{ $module->modules_id }}, '{{ $escapedModuleName }}')"><i class="bi bi-plus-circle"></i> Add Course</button>
            <button class="action-btn-green edit-btn" onclick="editModule({{ $module->modules_id }})" title="Edit Module"><i class="bi bi-pencil"></i></button>
            <button class="action-btn-green archive-btn" onclick="archiveModule({{ $module->modules_id }})" title="Archive Module"><i class="bi bi-archive"></i></button>
            <button class="action-btn-green override-btn" onclick="openOverrideModal('module', {{ $module->modules_id }}, '{{ $escapedModuleName }}')"><i class="bi bi-sliders"></i> Override</button>
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

<style>
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
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
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
    border-left: 4px solid #2c3e50;
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
    border-left: 4px solid #2c3e50;
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
window.toggleCourse = toggleCourse;

// Load module content in the content viewer
function loadModuleContentInViewer(moduleId) {
    const titleElement = document.getElementById('content-title');
    const subtitleElement = document.getElementById('content-subtitle');
    const viewerBody = document.getElementById('contentViewer');
    
    // Show loading state
    titleElement.textContent = 'Loading Module...';
    subtitleElement.textContent = 'Fetching module details';
    viewerBody.innerHTML = '<div class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</div>';
    
    // Fetch module content with proper headers
    fetch(`/admin/modules/${moduleId}/content`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            // Check if response is actually JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned HTML instead of JSON - authentication may have expired');
            }
            
            return response.json();
        })
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
    
    // Fetch course content with proper headers
    fetch(`/admin/modules/${moduleId}/courses/${courseId}/content`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            // Check if response is actually JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned HTML instead of JSON - authentication may have expired');
            }
            
            return response.json();
        })
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
    
    // Handle quiz content specially
    if (contentType === 'quiz') {
        titleElement.textContent = contentTitle || 'Quiz';
        subtitleElement.textContent = `QUIZ • Course ID: ${courseId}`;
        viewerBody.innerHTML = `
            <div class="content-display">
                <div class="quiz-preview">
                    <h5><i class="bi bi-question-circle"></i> Quiz Content</h5>
                    <p><strong>Quiz:</strong> ${contentTitle}</p>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Quiz content cannot be previewed here. 
                        Use the Quiz Generator or Quiz Management tools to view and edit quiz content.
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.quiz-generator') }}" class="btn btn-primary" target="_blank">
                            <i class="bi bi-pencil-square"></i> Manage Quizzes
                        </a>
                    </div>
                </div>
            </div>
        `;
        return;
    }
    
    // Show loading state
    titleElement.textContent = 'Loading Content...';
    subtitleElement.textContent = 'Fetching content details';
    viewerBody.innerHTML = '<div class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</div>';
    
    // Fetch content details
    fetch(`/admin/content/${contentId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
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
                            // Handle content URL if available
                            if (content.content_url) {
                                const videoUrl = content.content_url;
                                let videoPlayer = '';
                                
                                // Check if it's a YouTube or Vimeo URL
                                if (content.content_url.includes('youtube.com') || content.content_url.includes('youtu.be') || content.content_url.includes('vimeo.com')) {
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
                                    // External video URL
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
                                        <div class="content-details mb-3 p-3 border rounded bg-light">
                                            <h5><i class="bi bi-play-circle"></i> Video Details</h5>
                                            <p><strong>Description:</strong> ${content.content_description || 'No description'}</p>
                                            <p><strong>Source:</strong> <a href="${videoUrl}" target="_blank">External Link</a></p>
                                        </div>
                                        ${videoPlayer}
                                    </div>
                                `;
                            } 
                            // Handle attachment path
                            else if (content.attachment_path) {
                                // Check if attachment_path is a JSON array
                                let attachmentPaths = [];
                                try {
                                    const parsedPath = JSON.parse(content.attachment_path);
                                    if (Array.isArray(parsedPath)) {
                                        attachmentPaths = parsedPath;
                                    } else {
                                        attachmentPaths = [content.attachment_path];
                                    }
                                } catch (e) {
                                    // Not JSON, treat as a single file
                                    attachmentPaths = [content.attachment_path];
                                }
                                
                                // Process each file and detect file type
                                let videoPlayers = '';
                                
                                for (let i = 0; i < attachmentPaths.length; i++) {
                                    const path = attachmentPaths[i];
                                    const fileUrl = `/storage/${path}`;
                                    const fileName = path.split('/').pop();
                                    const fileExtension = fileName.split('.').pop().toLowerCase();
                                    
                                    // Only create video player for video files
                                    if (['mp4', 'webm', 'ogg', 'mov'].includes(fileExtension)) {
                                        videoPlayers += `
                                            <div class="video-container mb-3">
                                                <h6>${fileName}</h6>
                                                <video controls style="width: 100%; max-height: 450px;" class="border">
                                                    <source src="${fileUrl}" type="video/${fileExtension}">
                                                    Your browser does not support the video tag.
                                                </video>
                                                <div class="mt-2">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="${fileUrl}" target="_blank" class="btn btn-outline-secondary" title="View in New Tab">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="${fileUrl}" download class="btn btn-secondary" title="Download">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    }
                                    // For non-video files, show appropriate preview based on file type
                                    else if (fileExtension === 'pdf') {
                                        videoPlayers += `
                                            <div class="pdf-container mb-3">
                                                <h6>${fileName}</h6>
                                                <iframe src="${fileUrl}" style="width: 100%; height: 400px; border: 1px solid #ddd;"></iframe>
                                                <div class="mt-2">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="${fileUrl}" target="_blank" class="btn btn-outline-secondary" title="View in New Tab">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="${fileUrl}" download class="btn btn-secondary" title="Download">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    }
                                    else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExtension)) {
                                        videoPlayers += `
                                            <div class="image-container mb-3">
                                                <h6>${fileName}</h6>
                                                <img src="${fileUrl}" class="img-fluid" alt="${fileName}" style="max-width: 100%; height: auto; border: 1px solid #ddd;">
                                                <div class="mt-2">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="${fileUrl}" target="_blank" class="btn btn-outline-secondary" title="View in New Tab">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="${fileUrl}" download class="btn btn-secondary" title="Download">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    }
                                    else {
                                        videoPlayers += `
                                            <div class="file-container mb-3">
                                                <h6>${fileName}</h6>
                                                <div class="p-4 border text-center">
                                                    <i class="bi bi-file-earmark text-muted" style="font-size: 3rem;"></i>
                                                    <p>File preview not available for this type</p>
                                                </div>
                                                <div class="mt-2">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="${fileUrl}" target="_blank" class="btn btn-outline-secondary" title="View in New Tab">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="${fileUrl}" download class="btn btn-secondary" title="Download">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    }
                                }
                                
                                contentHtml = `
                                    <div class="content-display">
                                        <div class="content-details mb-3 p-3 border rounded bg-light">
                                            <h5><i class="bi bi-play-circle"></i> Content Details</h5>
                                            <p><strong>Description:</strong> ${content.content_description || 'No description'}</p>
                                            <p><strong>Files:</strong> ${attachmentPaths.length} file(s) attached</p>
                                        </div>
                                        ${videoPlayers}
                                    </div>
                                `;
                            }
                            // Note: No additional contentHtml assignment here, each branch handles its own assignment
                        } else {
                            contentHtml = '<div class="alert alert-warning">Video URL not available</div>';
                        }
                        break;
                    
                    
                    case 'pdf':
                    case 'lesson': // Handle lesson type too since it's frequently used for PDFs
                        if (content.attachment_path) {
                            // Check if attachment_path is a JSON array
                            let attachmentPaths = [];
                            try {
                                const parsedPath = JSON.parse(content.attachment_path);
                                if (Array.isArray(parsedPath)) {
                                    attachmentPaths = parsedPath;
                                } else {
                                    attachmentPaths = [content.attachment_path];
                                }
                            } catch (e) {
                                // Not JSON, treat as a single file
                                attachmentPaths = [content.attachment_path];
                            }
                            
                            // Get file names if available
                            let fileNames = [];
                            if (content.file_name) {
                                try {
                                    const parsedNames = JSON.parse(content.file_name);
                                    if (Array.isArray(parsedNames)) {
                                        fileNames = parsedNames;
                                    } else {
                                        fileNames = [content.file_name];
                                    }
                                } catch (e) {
                                    // Not JSON, treat as a single file name
                                    fileNames = [content.file_name];
                                }
                            } else {
                                // Extract file names from paths
                                fileNames = attachmentPaths.map(path => path.split('/').pop());
                            }
                            
                            let fileViewer = '';
                            
                            // Process each attachment
                                for (let i = 0; i < attachmentPaths.length; i++) {
                                    const path = attachmentPaths[i];
                                    const fileUrl = `/storage/${path}`;
                                    const fileName = fileNames[i] || path.split('/').pop();
                                    const fileExtension = fileName.split('.').pop().toLowerCase();
                                    // Add header for each file
                                    fileViewer += `<h5 class="mt-3 mb-2">${fileName}</h5>`;
                                    // Handle different file types
                                    if (fileExtension === 'pdf') {
                                        fileViewer += `
                                            <div class="pdf-viewer">
                                                <iframe class="content-frame" src="${fileUrl}#toolbar=1&navpanes=1&scrollbar=1" 
                                                        style="width: 100%; height: 700px; border: 1px solid #ddd; border-radius: 5px;"
                                                        allowfullscreen>
                                                    <p>Your browser does not support PDFs. 
                                                       <a href="${fileUrl}" target="_blank">Download the PDF</a>
                                                    </p>
                                                </iframe>
                                            </div>
                                        `;
                                } else if (['doc', 'docx'].includes(fileExtension)) {
                                    fileViewer += `
                                    <div class="document-viewer">
                                        <div class="document-controls mb-2">
                                            <div class="btn-group">
                                                <a href="${fileUrl}" target="_blank" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                                <button onclick="window.open('https://view.officeapps.live.com/op/view.aspx?src=${encodeURIComponent(window.location.origin + fileUrl)}', '_blank')" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye"></i> View Online
                                                </button>
                                            </div>
                                        </div>
                                        <iframe class="content-frame" src="https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(window.location.origin + fileUrl)}" 
                                                style="width: 100%; height: 700px; border: 1px solid #ddd; border-radius: 5px;"
                                                allowfullscreen>
                                            <p>Document preview not available. <a href="${fileUrl}" target="_blank">Download the document</a></p>
                                        </iframe>
                                        <div class="fallback-message mt-2 text-center">
                                            <small class="text-muted">If the document doesn't load above, you can <a href="${fileUrl}" target="_blank">download it here</a></small>
                                        </div>
                                    </div>
                                `;
                            } else if (['ppt', 'pptx'].includes(fileExtension)) {
                                fileViewer += `
                                    <div class="presentation-viewer">
                                        <div class="presentation-controls mb-2">
                                            <div class="btn-group">
                                                <a href="${fileUrl}" target="_blank" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                                <button onclick="window.open('https://view.officeapps.live.com/op/view.aspx?src=${encodeURIComponent(window.location.origin + fileUrl)}', '_blank')" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye"></i> View Online
                                                </button>
                                            </div>
                                        </div>
                                        <iframe class="content-frame" src="https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(window.location.origin + fileUrl)}" 
                                                style="width: 100%; height: 700px; border: 1px solid #ddd; border-radius: 5px;"
                                                allowfullscreen>
                                            <p>Presentation preview not available. <a href="${fileUrl}" target="_blank">Download the presentation</a></p>
                                        </iframe>
                                        <div class="fallback-message mt-2 text-center">
                                            <small class="text-muted">If the presentation doesn't load above, you can <a href="${fileUrl}" target="_blank">download it here</a></small>
                                        </div>
                                    </div>
                                `;
                            } else if (['xls', 'xlsx'].includes(fileExtension)) {
                                fileViewer += `
                                    <div class="spreadsheet-viewer">
                                        <div class="spreadsheet-controls mb-2">
                                            <div class="btn-group">
                                                <a href="${fileUrl}" target="_blank" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                                <button onclick="window.open('https://view.officeapps.live.com/op/view.aspx?src=${encodeURIComponent(window.location.origin + fileUrl)}', '_blank')" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye"></i> View Online
                                                </button>
                                            </div>
                                        </div>
                                        <iframe class="content-frame" src="https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(window.location.origin + fileUrl)}" 
                                                style="width: 100%; height: 700px; border: 1px solid #ddd; border-radius: 5px;"
                                                allowfullscreen>
                                            <p>Spreadsheet preview not available. <a href="${fileUrl}" target="_blank">Download the spreadsheet</a></p>
                                        </iframe>
                                        <div class="fallback-message mt-2 text-center">
                                            <small class="text-muted">If the spreadsheet doesn't load above, you can <a href="${fileUrl}" target="_blank">download it here</a></small>
                                        </div>
                                    </div>
                                `;
                            } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExtension)) {
                                fileViewer += `
                                    <div class="image-viewer text-center">
                                        <img src="${fileUrl}" class="img-fluid" alt="${fileName}" 
                                             style="max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 5px;
                                             box-shadow: 0 2px 10px rgba(0,0,0,0.1); cursor: pointer;"
                                             onclick="window.open('${fileUrl}', '_blank')">
                                    </div>
                                `;
                            } else if (['mp4', 'webm', 'ogg'].includes(fileExtension)) {
                                fileViewer += `
                                    <div class="video-viewer">
                                        <video controls style="width: 100%; max-height: 600px; border-radius: 5px;" class="border">
                                            <source src="${fileUrl}" type="video/${fileExtension}">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                `;
                            } else if (['mp3', 'wav', 'ogg'].includes(fileExtension)) {
                                fileViewer += `
                                    <div class="audio-viewer">
                                        <audio controls style="width: 100%; border-radius: 5px;" class="border">
                                            <source src="${fileUrl}" type="audio/${fileExtension}">
                                            Your browser does not support the audio tag.
                                        </audio>
                                    </div>
                                `;
                            } else {
                                fileViewer += `
                                    <div class="file-preview text-center p-4 border">
                                        <i class="bi bi-file-earmark text-muted" style="font-size: 3rem;"></i>
                                        <p class="mt-2">File preview not available for this format</p>
                                        <p class="text-muted small">${fileName}</p>
                                    </div>
                                `;
                            }
                            } // End of for loop
                            
                            contentHtml = `
                                <div class="content-display">
                                    <div class="content-details mb-3 p-3 border rounded bg-light">
                                        <h5><i class="bi bi-file-earmark-text"></i> Document Details</h5>
                                        <p><strong>Description:</strong> ${content.content_description || 'No description'}</p>
                                        <p><strong>Files:</strong> ${attachmentPaths.length} file(s) attached</p>
                                    </div>
                                    <div class="document-controls mb-3">
                                        <div class="btn-group btn-group-sm">
                                            <a href="/storage/${attachmentPaths[0]}" target="_blank" class="btn btn-outline-secondary" title="View in New Tab">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="/storage/${attachmentPaths[0]}" download class="btn btn-secondary" title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                    ${fileViewer}
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
                                    <div class="content-details mb-3 p-3 border rounded bg-light">
                                        <h5><i class="bi bi-link-45deg"></i> External Link</h5>
                                        <p><strong>Description:</strong> ${content.content_description || 'No description'}</p>
                                        <p><strong>URL:</strong> <a href="${content.content_url}" target="_blank">${content.content_url}</a></p>
                                    </div>
                                    <div class="link-preview">
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
                                <div class="content-details mb-3 p-3 border rounded bg-light">
                                    <h5><i class="bi bi-pencil-square"></i> Assignment Details</h5>
                                    <p><strong>Description:</strong> ${content.content_description || 'No description'}</p>
                                    ${content.due_date ? `<p><strong>Due Date:</strong> ${new Date(content.due_date).toLocaleDateString()}</p>` : ''}
                                    ${content.submission_instructions ? `<div class="mt-2"><strong>Instructions:</strong><br><p>${content.submission_instructions}</p></div>` : ''}
                                </div>
                                <div class="assignment-preview">
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
                        let defaultContentHtml = `
                            <div class="content-display">
                                <div class="content-details mb-3 p-3 border rounded bg-light">
                                    <h5><i class="bi bi-file-earmark"></i> ${(content.content_type || 'Content').charAt(0).toUpperCase() + (content.content_type || 'Content').slice(1)} Details</h5>
                                    <p><strong>Description:</strong> ${content.content_description || 'No description'}</p>
                                </div>
                        `;
                        
                        // Handle attachments for any content type
                        if (content.attachment_path) {
                            // Check if attachment_path is a JSON array
                            let attachmentPaths = [];
                            try {
                                const parsedPath = JSON.parse(content.attachment_path);
                                if (Array.isArray(parsedPath)) {
                                    attachmentPaths = parsedPath;
                                } else {
                                    attachmentPaths = [content.attachment_path];
                                }
                            } catch (e) {
                                // Not JSON, treat as a single file
                                attachmentPaths = [content.attachment_path];
                            }
                            
                            // Get file names if available
                            let fileNames = [];
                            if (content.file_name) {
                                try {
                                    const parsedNames = JSON.parse(content.file_name);
                                    if (Array.isArray(parsedNames)) {
                                        fileNames = parsedNames;
                                    } else {
                                        fileNames = [content.file_name];
                                    }
                                } catch (e) {
                                    // Not JSON, treat as a single file name
                                    fileNames = [content.file_name];
                                }
                            } else {
                                // Extract file names from paths
                                fileNames = attachmentPaths.map(path => path.split('/').pop());
                            }
                            
                            // Create a file preview for each attachment
                            let filePreview = '';
                            
                            for (let i = 0; i < attachmentPaths.length; i++) {
                                const path = attachmentPaths[i];
                                const fileUrl = `/storage/${path}`;
                                const fileName = fileNames[i] || path.split('/').pop();
                                const fileExtension = fileName.split('.').pop().toLowerCase();
                                
                                filePreview += `<h6 class="mt-3 mb-2">File ${i+1}: ${fileName}</h6>`;
                                
                                if (fileExtension === 'pdf') {
                                    filePreview += `
                                        <div class="file-preview mt-2 mb-3">
                                            <iframe src="${fileUrl}" style="width: 100%; height: 400px; border: 1px solid #ddd;"></iframe>
                                        </div>
                                    `;
                                } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExtension)) {
                                    filePreview += `
                                        <div class="file-preview mt-2 mb-3 text-center">
                                            <img src="${fileUrl}" class="img-fluid" alt="${fileName}" style="max-width: 100%; height: auto; border: 1px solid #ddd;">
                                        </div>
                                    `;
                                } else if (['mp4', 'webm', 'ogg'].includes(fileExtension)) {
                                    filePreview += `
                                        <div class="file-preview mt-2 mb-3">
                                            <video controls style="width: 100%; max-height: 400px;" class="border">
                                                <source src="${fileUrl}" type="video/${fileExtension}">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    `;
                                } else if (['mp3', 'wav', 'ogg'].includes(fileExtension)) {
                                    filePreview += `
                                        <div class="file-preview mt-2 mb-3">
                                            <audio controls style="width: 100%;" class="border">
                                                <source src="${fileUrl}" type="audio/${fileExtension}">
                                                Your browser does not support the audio tag.
                                            </audio>
                                        </div>
                                    `;
                                } else {
                                    filePreview += `
                                        <div class="file-preview mt-2 mb-3 text-center p-4 border bg-light">
                                            <i class="bi bi-file-earmark text-muted" style="font-size: 3rem;"></i>
                                            <p class="mt-2 mb-0">Preview not available for this file type</p>
                                            <small class="text-muted">${fileName}</small>
                                        </div>
                                    `;
                                }
                                
                                filePreview += `
                                    <div class="mb-3">
                                        <div class="btn-group btn-group-sm">
                                            <a href="${fileUrl}" target="_blank" class="btn btn-outline-secondary" title="View in New Tab">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="${fileUrl}" download class="btn btn-secondary" title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                `;
                            }
                            
                            defaultContentHtml += `
                                ${filePreview}
                            `;
                        }
                        
                        if (content.content_url) {
                            defaultContentHtml += `
                                <div class="mt-3 p-3 border rounded">
                                    <p><strong>URL:</strong> <a href="${content.content_url}" target="_blank">${content.content_url}</a></p>
                                    <div class="mt-2">
                                        <a href="${content.content_url}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-box-arrow-up-right"></i> Open Link
                                        </a>
                                    </div>
                                </div>
                            `;
                        }
                        
                        defaultContentHtml += '</div>';
                        contentHtml = defaultContentHtml;
                }
                
                // Add debugging info if needed
                if (window.location.search.includes('debug=true')) {
                    contentHtml += `
                        <div class="mt-4 p-3 border border-info rounded bg-light">
                            <h6 class="text-primary"><i class="bi bi-info-circle"></i> Debug Information</h6>
                            <p><strong>Content Type:</strong> ${content.content_type}</p>
                            <p><strong>Attachment Path:</strong> ${content.attachment_path || 'None'}</p>
                            <p><strong>Has Multiple Files:</strong> ${content.has_multiple_files ? 'Yes' : 'No'}</p>
                            <p><strong>File Name(s):</strong> ${content.file_names ? JSON.stringify(content.file_names) : 'None'}</p>
                            <p><strong>File MIME:</strong> ${content.file_mime || 'None'}</p>
                            <p><strong>Content URL:</strong> ${content.content_url || 'None'}</p>
                            
                            <div class="mt-2">
                                <button class="btn btn-sm btn-info" onclick="console.log('Content data:', ${JSON.stringify(content)})">
                                    Log Content Data to Console
                                </button>
                            </div>
                        </div>
                    `;
                }
                
                viewerBody.innerHTML = contentHtml;
                
                // Mark content item as active
                document.querySelectorAll('.content-item').forEach(el => el.classList.remove('active'));
                const activeItem = document.querySelector(`[data-content-id="${contentId}"]`);
                if (activeItem) {
                    activeItem.classList.add('active');
                }
                
            } else {
                viewerBody.innerHTML = '<div class="alert alert-danger">Failed to load content</div>';
            }
        })
        .catch(error => {
            console.error('Error loading content:', error);
            titleElement.textContent = 'Error Loading Content';
            subtitleElement.textContent = `Content ID: ${contentId}`;
            
            let errorMessage = 'Error loading content';
            if (error.message.includes('404')) {
                errorMessage = 'Content not found (404). This content may have been deleted or moved.';
            } else if (error.message.includes('500')) {
                errorMessage = 'Server error (500). Please try again later or contact support.';
            } else if (error.message.includes('403')) {
                errorMessage = 'Access denied (403). You may not have permission to view this content.';
            }
            
            viewerBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> ${errorMessage}
                    <br><small class="text-muted">Error details: ${error.message}</small>
                </div>
            `;
        });
}
window.loadContentInViewer = loadContentInViewer;

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
                        <i class="course-drag-handle bi bi-grip-vertical" onclick="event.stopPropagation();" style="cursor: move; color: rgba(255,255,255,0.7);" title="Drag to move course"></i>
                        <i class="course-toggle-icon bi bi-chevron-right"></i>
                        <div>
                            <h5 class="mb-0">${course.subject_name}</h5>
                            ${course.subject_description ? `<small class="opacity-75">${course.subject_description}</small>` : ''}
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation();">
                        <div class="action-btn-group">
                            <button class="action-btn-green add-btn" onclick="showAddContentModal(${moduleId}, ${course.subject_id}, '${course.subject_name}')" title="Add Content"><i class="bi bi-plus"></i></button>
                            <button class="action-btn-green edit-btn" onclick="editCourse(${course.subject_id})" title="Edit Course"><i class="bi bi-pencil"></i></button>
                            <button class="action-btn-green archive-btn" onclick="archiveCourse(${course.subject_id})" title="Archive Course"><i class="bi bi-archive"></i></button>
                            <button class="action-btn-green override-btn" onclick="openOverrideModal('course', ${course.subject_id}, '${course.subject_name.replace(/'/g, "\\'")}')"><i class="bi bi-sliders"></i> Override</button>
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
    
    // Use the correct API endpoint with proper headers
    fetch(`/admin/modules/${moduleId}/courses/${courseId}/content`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            // Check if response is actually JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned HTML instead of JSON - authentication may have expired');
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success && data.content_items) {
                displayCourseContent(moduleId, courseId, data.content_items);
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
            
            // Enhanced error handling for mobile
            let errorMessage = 'Error loading content';
            if (error.message.includes('authentication')) {
                errorMessage = 'Session expired. Please refresh the page.';
            } else if (error.message.includes('HTTP 404')) {
                errorMessage = 'Content not found.';
            } else if (error.message.includes('HTTP 403')) {
                errorMessage = 'Access denied.';
            }
            
            container.innerHTML = `<div class="alert alert-danger">${errorMessage}</div>`;
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
                        <button class="action-btn-green view-btn" onclick="loadContentInViewer(${item.id}, '${item.content_type}', '${item.content_title}', ${moduleId}, ${courseId})" title="View Content"><i class="bi bi-eye"></i></button>
                        <button class="action-btn-green edit-btn" onclick="editContent(${item.id})" title="Edit Content"><i class="bi bi-pencil"></i></button>
                        <button class="action-btn-green archive-btn" onclick="archiveContent(${item.id})" title="Archive Content"><i class="bi bi-archive"></i></button>
                        @php $escapedContentTitle = isset($item) ? addslashes($item->content_title) : ''; @endphp
                        <button class="action-btn-green override-btn" onclick="openOverrideModal('content', ${item.id}, '${item.content_title}')" title="Override Settings"><i class="bi bi-sliders"></i></button>
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
    // Find the program_id for this module
    let programId = null;
    const moduleElem = document.querySelector(`[data-module-id='${moduleId}']`);
    if (moduleElem && moduleElem.dataset.programId) {
        programId = moduleElem.dataset.programId;
    } else if (typeof window.currentProgramId !== 'undefined') {
        programId = window.currentProgramId;
    } else if (document.getElementById('programSelect')) {
        programId = document.getElementById('programSelect').value;
    }
    // Build the URL with parameters
    const urlParams = new URLSearchParams();
    if (programId) urlParams.append('program_id', programId);
    if (moduleId) urlParams.append('module_id', moduleId);
    if (courseId) urlParams.append('course_id', courseId);
    const baseUrl = '/admin/modules/course-content-upload';
    const fullUrl = urlParams.toString() ? `${baseUrl}?${urlParams.toString()}` : baseUrl;
    window.location.href = fullUrl;
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

// Global variables to store the module ID and name for archiving
let moduleToArchiveId = null;
let moduleToArchiveName = null;

// Open the archive confirmation modal
function openArchiveModal(moduleId, moduleName) {
    moduleToArchiveId = moduleId;
    moduleToArchiveName = moduleName;
    
    // Set the module name in the modal
    document.getElementById('archiveModuleName').textContent = moduleName;
    
    // Show the modal
    document.getElementById('archiveConfirmationModal').classList.add('show');
}

// Close the archive confirmation modal
function closeArchiveModal() {
    document.getElementById('archiveConfirmationModal').classList.remove('show');
    moduleToArchiveId = null;
    moduleToArchiveName = null;
}

// Confirm and execute the archive action
function confirmArchive() {
    if (!moduleToArchiveId) {
        showNotification('No module selected for archiving', 'error');
        closeArchiveModal();
        return;
    }
    
    // Show loading state
    const archiveBtn = document.querySelector('#archiveConfirmationModal .add-btn');
    const originalText = archiveBtn.textContent;
    archiveBtn.disabled = true;
    archiveBtn.textContent = 'Archiving...';
    
    fetch(`/admin/modules/${moduleToArchiveId}/archive`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        closeArchiveModal();
        
        if (data.success) {
            showNotification('Module archived successfully!', 'success');
            // Reload the page to reflect changes
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Failed to archive module', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        closeArchiveModal();
        showNotification(`An error occurred: ${error.message}`, 'error');
    })
    .finally(() => {
        // Reset button state
        archiveBtn.disabled = false;
        archiveBtn.textContent = originalText;
    });
}

// Replace the existing archiveModule function with this one that uses the modal
function archiveModule(moduleId, moduleName = '') {
    // If module name wasn't provided, try to get it from the DOM
    if (!moduleName) {
        const moduleElement = document.querySelector(`[data-module-id="${moduleId}"]`);
        if (moduleElement) {
            const titleElement = moduleElement.querySelector('.module-title-section h4');
            if (titleElement) {
                moduleName = titleElement.textContent.trim();
            }
        }
        
        // Default name if we couldn't find it
        if (!moduleName) {
            moduleName = `Module #${moduleId}`;
        }
    }
    
    // Open the confirmation modal
    openArchiveModal(moduleId, moduleName);
}

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
window.toggleModule = toggleModule;

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
    // Check if courseId is valid
    if (!courseId || courseId === 'undefined') {
        alert('Error: Invalid course ID');
        console.error('editCourse called with invalid courseId:', courseId);
        return;
    }
    
    // Implementation for editing course
    fetch(`/admin/courses/${courseId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
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

function archiveCourse(courseId) {
    // Check if courseId is valid
    if (!courseId || courseId === 'undefined') {
        alert('Error: Invalid course ID');
        console.error('archiveCourse called with invalid courseId:', courseId);
        return;
    }
    
    if (confirm('Are you sure you want to archive this course? Archived courses can be restored later.')) {
        fetch(`/admin/courses/${courseId}/archive`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showAlert('Course archived successfully!', 'success');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to archive course'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while archiving the course: ' + error.message);
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
                    
                    // Handle submission settings
                    const enableSubmission = document.getElementById('edit_enable_submission');
                    const submissionOptions = document.getElementById('edit_submission_options');
                    if (enableSubmission) {
                        enableSubmission.checked = data.content.enable_submission || false;
                        submissionOptions.style.display = enableSubmission.checked ? 'block' : 'none';
                    }
                    
                    // Fill submission options
                    if (data.content.allowed_file_types) {
                        document.getElementById('edit_allowed_file_types').value = data.content.allowed_file_types;
                    }
                    if (data.content.submission_instructions) {
                        document.getElementById('edit_submission_instructions').value = data.content.submission_instructions;
                    }
                    
                    if (data.content.type === 'link') {
                        linkSection.style.display = 'block';
                        fileSection.style.display = 'none';
                        document.getElementById('edit_content_link').value = data.content.link || '';
                    } else {
                        linkSection.style.display = 'none';
                        fileSection.style.display = 'block';
                        
                        // Show existing file info
                        let fileHtml = `
                            <label for="edit_content_file">Replace File (optional)</label>
                            <input type="file" id="edit_content_file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif">
                        `;
                        
                        if (data.content.file_path) {
                            const fileName = data.content.file_path.split('/').pop();
                            fileHtml += `
                                <div class="current-file-info mt-2 p-2 bg-light rounded">
                                    <small class="text-muted"><strong>Current file:</strong> 
                                        <a href="/storage/${data.content.file_path}" target="_blank" class="text-primary">
                                            <i class="bi bi-file-earmark"></i> ${fileName}
                                        </a>
                                    </small>
                                </div>
                            `;
                            fileHtml += `<small class="text-muted">Select a new file to replace the current one, or leave empty to keep existing file.</small>`;
                        } else {
                            fileHtml += `<small class="text-muted">No file currently attached.</small>`;
                        }
                        
                        fileSection.innerHTML = fileHtml;
                    }
                    document.getElementById('edit_module_id').value = data.content.module_id;
                    document.getElementById('edit_course_id').value = data.content.course_id;
                    document.getElementById('edit_lesson_id').value = data.content.lesson_id;
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

function archiveContent(contentId) {
    if (confirm('Are you sure you want to archive this content? Archived content can be restored later.')) {
        fetch(`/admin/content/${contentId}/archive`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showAlert('Content archived successfully!', 'success');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to archive content'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while archiving the content: ' + error.message);
        });
    }
}

function viewSubmissions(assignmentId) {
    // Implementation for viewing submissions
    window.open('/admin/submissions', '_blank');
}

// Initialize sortable functionality for modules
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin modules JavaScript loaded');
    
    const modulesContainer = document.getElementById('modulesHierarchy');
    if (modulesContainer && typeof Sortable !== 'undefined') {
        // Module-level sorting
        new Sortable(modulesContainer, {
            handle: '.module-drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            fallbackOnBody: true, // << add this
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
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                handle: '.content-drag-handle',
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

// Move content directly to module level (without specific course)
function moveContentToModule(contentId, moduleId) {
    return fetch('/admin/content/move-to-module', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            content_id: contentId,
            module_id: moduleId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Content moved successfully!', 'success');
            location.reload(); // Refresh to show updated structure
        } else {
            showNotification(data.message || 'Failed to move content. Please try again.', 'error');
        }
        return data.success;
    })
    .catch(error => {
        console.error('Error in moveContentToModule:', error);
        showNotification('Error moving content. Please try again.', 'error');
        return false;
    });
}

// Handle content being dropped directly on a module (not in a specific course)
function handleContentDropOnModule(contentElement, targetModuleId) {
    const contentId = contentElement.getAttribute('data-content-id');
    
    if (contentId) {
        // Move content directly to module level (no specific course)
        moveContentToModule(contentId, targetModuleId);
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

// Show alert messages (alias for showNotification)
function showAlert(message, type = 'info') {
    showNotification(message, type);
}

// Setup modal event listeners
function setupModalEventListeners() {
    console.log('Setting up modal event listeners');
    
    // Add Module Modal
    const showAddModal = document.getElementById('showAddModal');
    const addModalBg = document.getElementById('addModalBg');
    const closeAddModal = document.getElementById('closeAddModal');
    const closeAddModalBtn = document.getElementById('closeAddModalBtn');

    console.log('showAddModal button:', showAddModal);
    console.log('addModalBg:', addModalBg);

    if (showAddModal) {
        showAddModal.addEventListener('click', function() {
            console.log('Add module button clicked');
            addModalBg.classList.add('show');
        });
    } else {
        console.error('showAddModal button not found');
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
    
    // Program selector event listener
    const programSelect = document.getElementById('programSelect');
    if (programSelect) {
        programSelect.addEventListener('change', function() {
            const programId = this.value;
            if (programId) {
                window.location.href = `/admin/modules?program_id=${programId}`;
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
}

// Removed duplicate program selector event listener

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

// Update course order within a module
function updateCourseOrder(moduleId, courseIds) {
    fetch('/admin/courses/update-order', {
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

// Update course order within a module
function updateCourseOrder(moduleId, courseIds) {
    fetch('/admin/courses/update-order', {
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

// Move course to different module
function moveCourseToModule(courseId, newModuleId) {
    fetch('/admin/courses/move', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            course_id: courseId,
            module_id: newModuleId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Course moved successfully');
            showNotification('Course moved to new module successfully!', 'success');
        } else {
            console.error('Failed to move course');
            showNotification('Failed to move course. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Error moving course:', error);
        showNotification('Error moving course. Please try again.', 'error');
    });
}

// Move content to course
function moveContentToCourse(contentId, courseId, moduleId) {
    fetch('/admin/content/move', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            content_id: contentId,
            course_id: courseId,
            module_id: moduleId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Content moved to course successfully');
            showNotification('Content moved successfully!', 'success');
        } else {
            console.error('Failed to move content to course');
            showNotification('Failed to move content. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Error moving content:', error);
        showNotification('Error moving content. Please try again.', 'error');
    });
}

// Setup form submission handlers
function setupFormHandlers() {
    // Add Module Form
    const addModuleForm = document.getElementById('addModuleForm');

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
            const submissionSection = document.getElementById('edit_submission_section');
            
            if (this.value === 'link') {
                linkSection.style.display = 'block';
                fileSection.style.display = 'none';
                submissionSection.style.display = 'none';
            } else {
                linkSection.style.display = 'none';
                fileSection.style.display = 'block';
                submissionSection.style.display = this.value === 'assignment' ? 'block' : 'none';
            }
        });
    }

    // Submission checkbox handler
    const editEnableSubmission = document.getElementById('edit_enable_submission');
    if (editEnableSubmission) {
        editEnableSubmission.addEventListener('change', function() {
            const submissionOptions = document.getElementById('edit_submission_options');
            submissionOptions.style.display = this.checked ? 'block' : 'none';
        });
    }

    // File types handler
    const editAllowedFileTypes = document.getElementById('edit_allowed_file_types');
    const editCustomFileTypes = document.getElementById('edit_custom_file_types');
    if (editAllowedFileTypes && editCustomFileTypes) {
        editAllowedFileTypes.addEventListener('change', function() {
            editCustomFileTypes.style.display = this.value === 'custom' ? 'block' : 'none';
        });
    }

    // Add form submission checkbox handler
    const enableSubmission = document.getElementById('enable_submission');
    if (enableSubmission) {
        enableSubmission.addEventListener('change', function() {
            const submissionOptions = document.getElementById('submission_options');
            submissionOptions.style.display = this.checked ? 'block' : 'none';
        });
    }

    // Add form file types handler
    const allowedFileTypes = document.getElementById('allowed_file_types');
    const customFileTypes = document.getElementById('custom_file_types');
    if (allowedFileTypes && customFileTypes) {
        allowedFileTypes.addEventListener('change', function() {
            customFileTypes.style.display = this.value === 'custom' ? 'block' : 'none';
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
  submitBtn.textContent = 'Adding…';

  console.log('Submitting course content form…', form.action);
  
  // Debug form data
  console.log('Form data entries:');
  for (let [key, value] of formData.entries()) {
    if (key === 'attachment' && value instanceof File) {
      console.log(`${key}:`, {
        name: value.name,
        size: value.size,
        type: value.type,
        lastModified: value.lastModified
      });
    } else {
      console.log(`${key}:`, value);
    }
  }

  fetch(form.action, {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    }
  })
  .then(async response => {
    const text = await response.text();
    console.log('Raw server response:', text);

    if (!response.ok) {
      // server returned 500/422/etc with HTML or JSON
      let errorMsg = text;
      try {
        // maybe it's JSON with a message?
        const json = JSON.parse(text);
        errorMsg = json.message || JSON.stringify(json.errors || json);
      } catch {}
      throw new Error(`Server error (${response.status}): ${errorMsg}`);
    }

    // at this point it's a 200–299, hopefully JSON
    try {
      return JSON.parse(text);
    } catch (err) {
      throw new Error('Invalid JSON from server: ' + text);
    }
  })
  .then(data => {
    console.log('Parsed JSON:', data);
    if (data.success) {
      alert('Content added successfully!');
      form.closest('.modal-bg').classList.remove('show');
      location.reload();
    } else {
      alert('Error: ' + (data.message || 'Unknown error'));
      console.error('Server-side errors:', data.errors || data);
    }
  })
  .catch(err => {
    console.error('Submit error:', err);
    alert(err.message);
  })
  .finally(() => {
    submitBtn.disabled = false;
    submitBtn.textContent = originalText;
  });
}


function submitEditContentForm(form) {
  const formData = new FormData(form);
  
  // Laravel method‑spoofing
  if (!formData.has('_method')) {
    formData.append('_method', 'PUT');
  }

  // Check if attachment field has a real file
  const attachmentInput = form.querySelector('input[name="attachment"]');
  const attachmentFile = attachmentInput ? attachmentInput.files[0] : null;
  
  // If there's no file selected, remove empty attachment field
  if (!attachmentFile || attachmentFile.size === 0) {
    formData.delete('attachment');
    console.log('No file selected, removing empty attachment field');
  } else {
    console.log('File selected:', {
      name: attachmentFile.name,
      size: attachmentFile.size,
      type: attachmentFile.type
    });
  }

  // Debug form data
  console.log('Edit Content Form data entries:');
  for (let [key, value] of formData.entries()) {
    if (key === 'attachment' && value instanceof File) {
      console.log(`${key}:`, {
        name: value.name,
        size: value.size,
        type: value.type,
        lastModified: value.lastModified
      });
    } else {
      console.log(`${key}:`, value);
    }
  }

  // Additional debugging
  console.log('Form encoding type:', form.enctype);
  console.log('Form method:', form.method);
  console.log('Form action:', form.action);

  const submitBtn = form.querySelector('button[type="submit"]');
  const originalText = submitBtn.textContent;
  submitBtn.disabled = true;
  submitBtn.textContent = 'Updating…';

  fetch(form.action, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    },
    body: formData
  })
  .then(async response => {
    const data = await response.json();
    console.log('Edit content server response:', data);
    console.log('Response status:', response.status);
    console.log('Response headers:', [...response.headers.entries()]);

    if (response.status === 422) {
      // validation failed
      console.error('Edit content validation failed:', data);
      let messages = [];
      if (data.errors && typeof data.errors === 'object') {
        messages = Object.values(data.errors)
          .flat()
          .map(msg => `• ${msg}`);
      } else if (data.message) {
        messages = [`• ${data.message}`];
      }
      alert('Please fix the following errors:\n' + messages.join('\n'));
    }
    else if (response.ok) {
      // success!
      console.log('Edit content successful:', data);
      document.getElementById('editContentModalBg').classList.remove('show');
      location.reload();
    }
    else {
      // some other server error
      console.error('Server error:', data);
      alert('An unexpected error occurred. Check the console for details.');
    }
  })
  .catch(err => {
    console.error('Fetch error:', err);
    alert('Network error. Please try again.');
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

function openOverrideModal(type, id, name) {
  const modal = new bootstrap.Modal(document.getElementById('overrideModal'));
  document.getElementById('overrideType').value = type;
  document.getElementById('overrideId').value = id;
  document.getElementById('overrideName').value = name;
  document.getElementById('overrideOption').value = 'none';
  document.getElementById('archiveOverride').checked = false;
  modal.show();
}

document.getElementById('overrideForm').onsubmit = function(e) {
  e.preventDefault();
  // TODO: Implement AJAX save for override settings
  const modal = bootstrap.Modal.getInstance(document.getElementById('overrideModal'));
  modal.hide();
  showNotification('Override settings saved!', 'success');
};

// --- Add New Content Modal Logic ---
const planSelect = document.getElementById('planSelect');
const batchGroup = document.getElementById('batchGroup');
const batchSelect = document.getElementById('batchSelect');
const programSelect = document.getElementById('modalProgramSelect');

function updateBatchVisibility() {
    const plan = planSelect.value;
    if (plan === 'full') {
        batchGroup.style.display = '';
        batchSelect.disabled = false;
        batchSelect.setAttribute('name', 'batch_id');
        loadBatchesForPrograms();
    } else {
        batchGroup.style.display = 'none';
        batchSelect.disabled = true;
        batchSelect.removeAttribute('name');
        batchSelect.innerHTML = '<option value="">-- Select Batch --</option>';
    }
}
planSelect.addEventListener('change', updateBatchVisibility);
programSelect.addEventListener('change', function() {
    const plan = planSelect.value;
    if (plan === 'full') {
        loadBatchesForPrograms();
    }
});
// Ensure correct initial state when modal opens
function setInitialBatchVisibility() {
    updateBatchVisibility();
}
// Attach to modal open event
const showAddModalBtn = document.getElementById('showAddModal');
if (showAddModalBtn) {
    showAddModalBtn.addEventListener('click', function() {
        setTimeout(setInitialBatchVisibility, 100); // Wait for modal to render
    });
}
// Also set on DOMContentLoaded in case modal is open by default
setInitialBatchVisibility();

// Before submitting the addModuleForm, remove the batch_id field if plan is not 'full'
document.getElementById('addModuleForm').addEventListener('submit', function(e) {
    const plan = document.getElementById('planSelect').value;
    const batchSelect = document.getElementById('batchSelect');
    if (plan !== 'full') {
        batchSelect.disabled = true;
        batchSelect.removeAttribute('name');
    } else {
        batchSelect.disabled = false;
        batchSelect.setAttribute('name', 'batch_id');
    }
});

// --- Multi-select enhancements (optional, for better UX) ---
// You can use a library like Choices.js or implement custom styling/UX for multi-selects if desired.

// --- Override Settings Button Fix ---
window.openOverrideModal = function(type, id, name) {
    const modal = document.getElementById('overrideModal');
    document.getElementById('overrideModuleName').textContent = name || '';
    // Optionally set hidden fields for type/id if needed
    modal.classList.add('show');
};
window.closeOverrideModal = function() {
    document.getElementById('overrideModal').classList.remove('show');
};
window.saveOverrideSettings = function() {
    // Implement AJAX save logic here if needed
    closeOverrideModal();
    // Optionally show a notification
    if (typeof showNotification === 'function') showNotification('Override settings saved!', 'success');
};


planSelect.addEventListener('change', updateBatchVisibility);
programSelect.addEventListener('change', function() {
    const plan = planSelect.value;
    if (plan === 'full') {
        loadBatchesForPrograms();
    }
});

function loadBatchesForPrograms() {
    const programId = programSelect.value;
    batchSelect.innerHTML = '<option value="">Loading...</option>';
    batchSelect.disabled = true;
    if (!programId) {
        batchSelect.innerHTML = '<option value="">-- Select Batch --</option>';
        batchSelect.disabled = false;
        return;
    }
    fetch(`/admin/modules/batches/${programId}`)
        .then(res => res.json())
        .then(data => {
            batchSelect.innerHTML = '';
            if (data.success && data.batches && data.batches.length > 0) {
                data.batches.forEach(batch => {
                    const option = document.createElement('option');
                    option.value = batch.id;
                    option.textContent = batch.batch_name;
                    batchSelect.appendChild(option);
                });
            } else {
                batchSelect.innerHTML = '<option value="">No batches available</option>';
            }
            batchSelect.disabled = false;
        })
        .catch(() => {
            batchSelect.innerHTML = '<option value="">Error loading batches</option>';
            batchSelect.disabled = false;
        });
}

// --- Multi-select enhancements (optional, for better UX) ---
// You can use a library like Choices.js or implement custom styling/UX for multi-selects if desired.

// --- Override Settings Button Fix ---
window.openOverrideModal = function(type, id, name) {
    const modal = document.getElementById('overrideModal');
    document.getElementById('overrideModuleName').textContent = name || '';
    // Optionally set hidden fields for type/id if needed
    modal.classList.add('show');
};
window.closeOverrideModal = function() {
    document.getElementById('overrideModal').classList.remove('show');
};
window.saveOverrideSettings = function() {
    // Implement AJAX save logic here if needed
    closeOverrideModal();
    // Optionally show a notification
    if (typeof showNotification === 'function') showNotification('Override settings saved!', 'success');
};

// REMOVED: Conflicting Archive Button Fix that was overwriting the working archiveModule function
// The proper archiveModule function is defined earlier in the file around line 1910

// Before submitting the addModuleForm, remove the batch_id field if plan is not 'full'
document.getElementById('addModuleForm').addEventListener('submit', function(e) {
    const plan = document.getElementById('planSelect').value;
    const batchGroup = document.getElementById('batchGroup');
    const batchSelect = document.getElementById('batchSelect');
    if (plan !== 'full') {
        // Remove batch_id field so backend doesn't validate it
        batchSelect.disabled = true;
        batchSelect.removeAttribute('name');
    } else {
        batchSelect.disabled = false;
        batchSelect.setAttribute('name', 'batch_id');
    }
});

// Add this JS after the DOM is ready:
document.addEventListener('DOMContentLoaded', function() {
    const showBatchModalBtn = document.getElementById('showBatchModal');
    if (showBatchModalBtn) {
        showBatchModalBtn.addEventListener('click', function() {
            // Get the current program_id from the selector
            let programId = null;
            const programSelect = document.getElementById('programSelect');
            if (programSelect) {
                programId = programSelect.value;
            }
            let url = '/admin/modules/course-content-upload';
            if (programId) {
                url += `?program_id=${programId}`;
            }
            window.location.href = url;
        });
    }
});
</script>
@endpush

<!-- Add Module Modal -->
<div class="modal-bg" id="addModalBg">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="bi bi-plus-circle"></i> Add New Modules</h3>
            <button type="button" class="modal-close" id="closeAddModal">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <form action="{{ route('admin.modules.store') }}" method="POST" enctype="multipart/form-data" id="addModuleForm">
            <div class="modal-body">
                @csrf
                <!-- 1. Program -->
                <div class="form-group">
                    <label for="modalProgramSelect">Program <span class="text-danger">*</span></label>
                    <select id="modalProgramSelect" name="program_id" class="form-select" required style="min-height: 38px;">
                        <option value="">-- Select Program --</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- 2. Plan -->
                <div class="form-group">
                    <label for="planSelect">Plan <span class="text-danger">*</span></label>
                    <select id="planSelect" name="plan" class="form-select" required>
                        <option value="">-- Select Plan --</option>
                        <option value="full">Full Plan</option>
                        <option value="modular">Modular Plan</option>
                        <option value="both">Both</option>
                    </select>
                </div>
                <!-- Batch (only if Full Plan) -->
                <div class="form-group" id="batchGroup" style="display:none;">
                    <label for="batchSelect">Batch <span class="text-danger">*</span></label>
                    <select id="batchSelect" class="form-select" style="min-height: 38px;">
                        <option value="">-- Select Batch --</option>
                        <!-- Options will be loaded dynamically based on selected program -->
                    </select>
                </div>
                <!-- 3. Learning Mode -->
                <div class="form-group">
                    <label for="learningModeSelect">Learning Mode <span class="text-danger">*</span></label>
                    <select id="learningModeSelect" name="learning_mode" class="form-select" required>
                        <option value="">-- Select Learning Mode --</option>
                        <option value="Synchronous">Synchronous</option>
                        <option value="Asynchronous">Asynchronous</option>
                    </select>
                </div>
                <!-- 4. Content Type -->
                <div class="form-group">
                    <label for="content_type">Content Type <span class="text-danger">*</span></label>
                    <select id="content_type" name="content_type" class="form-select" required>
                        <option value="lesson">📚 File</option>
                        <option value="video">🎥 Video</option>
                    </select>
                </div>
                <!-- 5. Title -->
                <div class="form-group">
                    <label for="module_name">Title <span class="text-danger">*</span></label>
                    <input type="text" id="module_name" name="module_name" class="form-control" required>
                </div>
                <!-- 6. Description -->
                <div class="form-group">
                    <label for="module_description">Description</label>
                    <textarea id="module_description" name="module_description" class="form-control" rows="4"></textarea>
                </div>
                <!-- 7. Attachment -->
                <div class="form-group">
                    <label for="attachment">Attachment</label>
                    <input type="file" id="attachment" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.zip,.png,.jpg,.jpeg,.mp4,.webm,.ogg">
                    <small class="text-muted">Supported formats: PDF, DOC, DOCX, ZIP, Images, Videos</small>
                </div>
                <!-- 8. URL -->
                <div class="form-group">
                    <label for="any_url">URL</label>
                    <input type="url" id="any_url" name="any_url" class="form-control" placeholder="https://...">
                    <small class="text-muted">Enter any external link (including video URLs)</small>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="closeAddModalBtn">Cancel</button>
                <button type="submit" class="add-btn">Create Content</button>
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
                <input type="hidden" id="edit_module_id" name="module_id">
                <input type="hidden" id="edit_course_id" name="course_id">
                <input type="hidden" id="edit_lesson_id" name="lesson_id">
                
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
                        <option value="lesson">📚 Lesson</option>
                        <option value="video">🎥 Video</option>
                        <option value="assignment">📝 Assignment</option>
                        <option value="quiz">❓ Quiz</option>
                        <option value="test">📋 Test</option>
                        <option value="link">🔗 External Link</option>
                    </select>
                </div>
                
                <!-- Submission Settings -->
                <div class="form-group" id="edit_submission_section">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="edit_enable_submission" name="enable_submission" value="1">
                        <label class="form-check-label" for="edit_enable_submission">
                            <i class="bi bi-upload"></i> Enable Student Submissions
                        </label>
                    </div>
                </div>
                
                <div class="row" id="edit_submission_options" style="display: none;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_allowed_file_types">Allowed File Types</label>
                            <select id="edit_allowed_file_types" name="allowed_file_types" class="form-select">
                                <option value="">All file types</option>
                                <option value="image">Images (jpg, png, gif)</option>
                                <option value="document">Documents (pdf, doc, docx)</option>
                                <option value="pdf">PDF only</option>
                            </select>
                            <input type="text" id="edit_custom_file_types" name="custom_file_types" class="form-control mt-2" 
                                   placeholder="e.g., pdf,docx,jpg,png" style="display: none;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_max_file_size">Max File Size (MB)</label>
                            <input type="number" id="edit_max_file_size" name="max_file_size" class="form-control" 
                                   min="1" max="100" value="10">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="edit_submission_instructions">Submission Instructions</label>
                            <textarea id="edit_submission_instructions" name="submission_instructions" 
                                    class="form-control" rows="2" 
                                    placeholder="Instructions for students on what to submit..."></textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="edit_allow_multiple_submissions" 
                                   name="allow_multiple_submissions" value="1">
                            <label class="form-check-label" for="edit_allow_multiple_submissions">
                                Allow multiple submissions (students can resubmit)
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group" id="edit_file_section">
                    <label for="edit_content_file">Replace File (optional)</label>
                    <input type="file" id="edit_content_file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif">
                    <small class="text-muted">Current file will be kept if no new file is selected.</small>
                </div>
                
                <div class="form-group" id="edit_link_section" style="display: none;">
                    <label for="edit_content_link">External Link URL</label>
                    <input type="url" id="edit_content_link" name="content_url" class="form-control" placeholder="https://...">
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

<style>
/* Hide scrollbars on modals */
.modal {
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}

.modal::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

.modal-body {
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}

.modal-body::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

/* Hide drag handle from navbar and other unintended locations */
.navbar .module-drag-handle,
.nav .module-drag-handle,
.header .module-drag-handle,
.admin-header .module-drag-handle,
.navbar-nav .module-drag-handle {
    display: none !important;
}
</style>

@endsection
