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
<div class="modules-grid sortable-modules" id="sortableModules">
  @foreach($modules as $module)
    <div class="module-card"
         data-module-id="{{ $module->modules_id }}"
         data-batch-id="{{ $module->batch_id }}"
         data-course-id="{{ $module->course_id ?? '' }}"
         data-learning-mode="{{ strtolower($module->learning_mode) }}"
         data-content-type="{{ $module->content_type }}"
         onclick="showModuleCourses({{ $module->modules_id }}, '{{ addslashes($module->module_name) }}')"
         style="cursor: pointer;">

      <div class="module-drag-handle">
        <i class="bi bi-grip-vertical"></i>
      </div>
                            
      {{-- HEADER: badges + title + batch all in one flex row --}}
      <div class="module-header">
        <div class="d-flex align-items-center gap-3">
          @if($module->content_type === 'ai_quiz')
            <span class="module-type-badge"
                  style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
              <i class="bi bi-robot"></i> AI Quiz
            </span>
          @else
            <span class="module-type-badge">
              @switch($module->content_type)
                @case('assignment')<i class="bi bi-file-earmark-text"></i> Assignment @break
                @case('quiz')      <i class="bi bi-question-circle"></i> Quiz       @break
                @case('test')      <i class="bi bi-clipboard-check"></i> Test      @break
                @case('link')      <i class="bi bi-link-45deg"></i> Link         @break
                @default           <i class="bi bi-book"></i> Module            @break
              @endswitch
            </span>
                                        @endif

                              <span class="learning-mode-badge {{ strtolower($module->learning_mode)==='asynchronous'?'asynchronous':'' }}">
            @if(strtolower($module->learning_mode)==='asynchronous')
              <i class="bi bi-clock"></i> Async
            @else
              <i class="bi bi-people"></i> Sync
            @endif
          </span>

          <h3 class="module-title mb-0">{{ $module->module_name }}</h3>

          @if($module->batch)
            <div class="batch-info text-muted">
              <i class="bi bi-collection"></i> Batch: {{ $module->batch->batch_name }}
            </div>
          @endif
        </div>
      </div>

      @if($module->module_description)
        <div class="module-description">{{ $module->module_description }}</div>
      @endif

                            @if($module->content_data)
                                <div class="content-details">
                                    @php $data = $module->content_data; @endphp
                                    @switch($module->content_type)
                                        @case('assignment')
                                            @if(!empty($data['assignment_title']))
                                                <div class="detail-item">
                                                    <strong>Title:</strong> {{ $data['assignment_title'] }}
                                                </div>
                                            @endif
                                            @if(!empty($data['due_date']))
                                                <div class="detail-item">
                                                    <strong>Due:</strong> {{ \Carbon\Carbon::parse($data['due_date'])->format('M d, Y g:i A') }}
                                                </div>
                                            @endif
                                            @if(!empty($data['max_points']))
                                                <div class="detail-item">
                                                    <strong>Points:</strong> {{ $data['max_points'] }}
                                                </div>
                                            @endif
                                            @break
                                        @case('quiz')
                                        @case('ai_quiz')
                                            @if(!empty($data['quiz_title']) || !empty($data['ai_quiz_title']))
                                                <div class="detail-item">
                                                    <strong>Title:</strong> {{ $data['quiz_title'] ?? $data['ai_quiz_title'] }}
                                                </div>
                                            @endif
                                            @if(!empty($data['time_limit']) || !empty($data['ai_time_limit']))
                                                <div class="detail-item">
                                                    <strong>Time:</strong> {{ $data['time_limit'] ?? $data['ai_time_limit'] }} minutes
                                                </div>
                                            @endif
                                            @if(!empty($data['question_count']) || !empty($data['ai_num_questions']))
                                                <div class="detail-item">
                                                    <strong>Questions:</strong> {{ $data['question_count'] ?? $data['ai_num_questions'] }}
                                                </div>
                                            @endif
                                            @break
                                        @case('test')
                                            @if(!empty($data['test_title']))
                                                <div class="detail-item">
                                                    <strong>Title:</strong> {{ $data['test_title'] }}
                                                </div>
                                            @endif
                                            @if(!empty($data['test_date']))
                                                <div class="detail-item">
                                                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($data['test_date'])->format('M d, Y g:i A') }}
                                                </div>
                                            @endif
                                            @if(!empty($data['total_marks']))
                                                <div class="detail-item">
                                                    <strong>Total Marks:</strong> {{ $data['total_marks'] }}
                                                </div>
                                            @endif
                                            @break
                                        @case('link')
                                            @if(!empty($data['link_title']))
                                                <div class="detail-item">
                                                    <strong>Link:</strong> {{ $data['link_title'] }}
                                                </div>
                                            @endif
                                            @if(!empty($data['external_url']))
                                                <div class="detail-item">
                                                    <strong>URL:</strong> 
                                                    <a href="{{ $data['external_url'] }}" target="_blank" class="text-decoration-none">
                                                        {{ Str::limit($data['external_url'], 40) }}
                                                        <i class="bi bi-box-arrow-up-right"></i>
                                                    </a>
                                                </div>
                                            @endif
                                            @break
                                    @endswitch
                                </div>
                            @endif

 <div class="module-meta d-flex justify-content-between align-items-center">
        <div class="module-date">
          <i class="bi bi-calendar-date"></i>
          {{ $module->created_at->format('M d, Y') }}
        </div>
        <div class="module-status">
          @if($module->attachment)
            <span class="text-primary"><i class="bi bi-paperclip"></i></span>
          @endif
          @if($module->admin_override)
            <span class="text-success"><i class="bi bi-unlock-fill"></i></span>
          @endif
        </div>
      </div>

      <div class="module-actions d-flex gap-2">
        <button class="action-btn btn-edit"
                onclick="event.stopPropagation(); editModule({{ $module->modules_id }}, '{{ addslashes($module->module_name) }}', '{{ addslashes($module->module_description) }}', {{ $module->program_id }}, '{{ $module->attachment }}')">
          <i class="bi bi-pencil"></i> Edit
        </button>
        <button class="action-btn btn-override"
               onclick="event.stopPropagation(); showOverrideModal({{ $module->modules_id }}, '{{ addslashes($module->module_name) }}')">
          <i class="bi bi-unlock-fill"></i> Override
        </button>
        <button class="action-btn btn-archive"
                onclick="event.stopPropagation();showArchiveConfirmation({{ $module->modules_id }}, '{{ addslashes($module->module_name) }}')">
          <i class="bi bi-archive"></i> Archive
        </button>
                            </div>
                        </div>
                    @endforeach
                </div>        @else
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

@endsection

@push('scripts')
<script>
// Global variables
let currentArchiveModuleId = null;
let currentOverrideModuleId = null;

document.addEventListener('DOMContentLoaded', function(){
  const programSelect = document.getElementById('courseProgramSelect');
  const moduleSelect  = document.getElementById('courseModuleSelect');
  if (!programSelect || !moduleSelect) return;

  programSelect.addEventListener('change', function(){
    const programId = this.value;
    
    console.log('Program selected for course:', programId);

    // reset & disable module dropdown
    moduleSelect.innerHTML = '<option value="">Loading modules…</option>';
    moduleSelect.disabled = true;

    if (!programId) {
      moduleSelect.innerHTML = '<option value="">-- Select Module --</option>';
      return;
    }

    fetch(`/api/programs/${programId}/modules`)
      .then(res => {
        console.log('Modules API response status:', res.status);
        if (!res.ok) {
          throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.json();
      })
      .then(json => {
        console.log('Modules API response data:', json);
        if (json.success) {
          let options = '<option value="">-- Select Module --</option>';
          json.modules.forEach(m => {
            console.log('Module found:', m);
            const moduleId = m.modules_id || m.id;
            const moduleName = m.module_name || m.name;
            
            console.log('Processing module - ID:', moduleId, 'Name:', moduleName);
            
            // Skip modules with null or undefined IDs
            if (moduleId && moduleId !== 'null' && moduleId !== null) {
              options += `<option value="${moduleId}">${moduleName}</option>`;
              console.log('Added module option:', moduleId, moduleName);
            } else {
              console.warn('Skipping module with null/undefined ID:', m);
            }
          });
          moduleSelect.innerHTML = options;
          moduleSelect.disabled = false;
          console.log('Module dropdown populated with', json.modules.length, 'modules');
          
          // If no valid modules were found, show a message
          if (options === '<option value="">-- Select Module --</option>') {
            moduleSelect.innerHTML = '<option value="">No modules available</option>';
          }
        } else {
          throw new Error(json.message || 'Failed to load modules');
        }
      })
      .catch(err => {
        console.error('Error loading modules:', err);
        moduleSelect.innerHTML = '<option value="">Error loading modules</option>';
        moduleSelect.disabled = false;
      });
  });
});



 document.addEventListener('DOMContentLoaded', () => {
    initializeModals();
    initializeProgramSelector();
    initializeBatchUpload();
    initializeContentTypeFields();
    initializeFiltering();
    initializeSorting();
    initializeCourseModals();
    initializeCourseContentModal();
  });

// Initialize modal functionality
function initializeModals() {
    // Show add modal
    const showAddModalBtn = document.getElementById('showAddModal');
    if (showAddModalBtn) {
        showAddModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Add modal button clicked');
            const modal = document.getElementById('addModalBg');
            if (modal) {
                modal.classList.add('show');
                console.log('Modal opened');
            } else {
                console.error('Modal not found');
            }
        });
    }

    // Show add modal from empty state - for dynamically created buttons
    document.addEventListener('click', function(e) {
        if (e.target && e.target.matches('button[onclick*="addModalBg"]')) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Empty state add modal button clicked');
            const modal = document.getElementById('addModalBg');
            if (modal) {
                modal.classList.add('show');
                console.log('Modal opened from empty state');
            }
        }
    });

    // Close add modal
    const closeAddModal = document.getElementById('closeAddModal');
    if (closeAddModal) {
        closeAddModal.addEventListener('click', function() {
            document.getElementById('addModalBg').classList.remove('show');
        });
    }

    const closeAddModalBtn = document.getElementById('closeAddModalBtn');
    if (closeAddModalBtn) {
        closeAddModalBtn.addEventListener('click', function() {
            document.getElementById('addModalBg').classList.remove('show');
        });
    }

    // Show batch modal
    const showBatchModal = document.getElementById('showBatchModal');
    if (showBatchModal) {
        showBatchModal.addEventListener('click', function() {
            document.getElementById('batchModalBg').classList.add('show');
        });
    }

    // Close batch modal
    const closeBatchModal = document.getElementById('closeBatchModal');
    if (closeBatchModal) {
        closeBatchModal.addEventListener('click', function() {
            document.getElementById('batchModalBg').classList.remove('show');
        });
    }

    const closeBatchModalBtn = document.getElementById('closeBatchModalBtn');
    if (closeBatchModalBtn) {
        closeBatchModalBtn.addEventListener('click', function() {
            document.getElementById('batchModalBg').classList.remove('show');
        });
    }

    // Close edit modal
    const closeEditModal = document.getElementById('closeEditModal');
    if (closeEditModal) {
        closeEditModal.addEventListener('click', function() {
            document.getElementById('editModalBg').classList.remove('show');
        });
    }

    const closeEditModalBtn = document.getElementById('closeEditModalBtn');
    if (closeEditModalBtn) {
        closeEditModalBtn.addEventListener('click', function() {
            document.getElementById('editModalBg').classList.remove('show');
        });
    }

    // Close modals when clicking outside
    document.querySelectorAll('.modal-bg').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('show');
            }
        });
    });
}

// Initialize program selector
function initializeProgramSelector() {
    const programSelect = document.getElementById('programSelect');
    if (programSelect) {
        // Add logging to debug
        console.log('Program selector initialized');
        
        programSelect.addEventListener('change', function() {
            const programId = this.value;
            console.log('Program selected:', programId);
            
            if (programId) {
                // Show loading state
                const modulesDisplayArea = document.getElementById('modulesDisplayArea');
                if (modulesDisplayArea) {
                    modulesDisplayArea.innerHTML = `
                        <div class="loading-state">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Loading modules...</p>
                        </div>
                    `;
                }
                
                // Redirect to load modules
                window.location.href = `{{ route('admin.modules.index') }}?program_id=${programId}`;
            } else {
                window.location.href = `{{ route('admin.modules.index') }}`;
            }
        });
    }

    // Initialize batch loading for modal program selects
    const modalProgramSelect = document.getElementById('modalProgramSelect');
    const batchModalProgramSelect = document.getElementById('batchModalProgramSelect');
    
    if (modalProgramSelect) {
        modalProgramSelect.addEventListener('change', function() {
            console.log('Modal program selected:', this.value);
            loadBatchesForProgram(this.value, 'batch_id');
        });
    }
    
    if (batchModalProgramSelect) {
        batchModalProgramSelect.addEventListener('change', function() {
            console.log('Batch modal program selected:', this.value);
            loadBatchesForProgram(this.value, 'batch_batch_id');
        });
    }
}

// Function to load batches based on program selection
function loadBatchesForProgram(programId, batchSelectId) {
    console.log('Loading batches for program:', programId, 'Target select:', batchSelectId);
    
    const batchSelect = document.getElementById(batchSelectId);
    if (!batchSelect) {
        console.error('Batch select element not found:', batchSelectId);
        return;
    }

    // Clear existing options
    batchSelect.innerHTML = '<option value="">-- Select Batch --</option>';

    if (!programId) {
        batchSelect.disabled = true;
        return;
    }

    // Show loading state
    batchSelect.disabled = true;
    batchSelect.innerHTML = '<option value="">Loading batches...</option>';

    fetch(`/admin/programs/${programId}/batches`)
        .then(response => {
            console.log('Batch response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Batches loaded:', data);
            if (data.success) {
                batchSelect.innerHTML = '<option value="">-- Select Batch --</option>';
                data.batches.forEach(batch => {
                    const option = document.createElement('option');
                    option.value = batch.id;
                    option.textContent = batch.batch_name;
                    batchSelect.appendChild(option);
                });
                batchSelect.disabled = false;
            } else {
                batchSelect.innerHTML = '<option value="">Error loading batches</option>';
                console.error('Error loading batches:', data.message);
                showNotification('Error loading batches: ' + data.message, 'error');
            }
        })
        .catch(error => {
            batchSelect.innerHTML = '<option value="">Error loading batches</option>';
            console.error('Error loading batches:', error);
            showNotification('Error loading batches', 'error');
        });
}

// Initialize batch upload functionality
function initializeBatchUpload() {
    const batchPdfFiles = document.getElementById('batchPdfFiles');
    const batchDropzone = document.getElementById('batchDropzone');
    const selectedFiles = document.getElementById('selectedFiles');
    const fileList = document.getElementById('fileList');
    const uploadPdfBtn = document.getElementById('uploadPdfBtn');

    if (batchPdfFiles && batchDropzone) {
        // Handle file selection
        batchPdfFiles.addEventListener('change', function(e) {
            handleFileSelection(e.target.files);
        });

        // Handle drag and drop
        batchDropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        batchDropzone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        batchDropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            const files = e.dataTransfer.files;
            handleFileSelection(files);
            batchPdfFiles.files = files;
        });
    }

    function handleFileSelection(files) {
        if (!files || files.length === 0) {
            selectedFiles.style.display = 'none';
            uploadPdfBtn.disabled = true;
            return;
        }

        // Filter only PDF files
        const pdfFiles = Array.from(files).filter(file => 
            file.name.toLowerCase().endsWith('.pdf')
        );

        if (pdfFiles.length === 0) {
            showNotification('Please select PDF files only', 'error');
            selectedFiles.style.display = 'none';
            uploadPdfBtn.disabled = true;
            return;
        }

        // Display selected files
        fileList.innerHTML = '';
        pdfFiles.forEach(file => {
            const li = document.createElement('li');
            li.textContent = file.name;
            fileList.appendChild(li);
        });

        selectedFiles.style.display = 'block';
        uploadPdfBtn.disabled = false;
    }
}

// Initialize content type specific fields
function initializeContentTypeFields() {
    const contentTypeSelect = document.getElementById('content_type');
    if (contentTypeSelect) {
        contentTypeSelect.addEventListener('change', function() {
            updateContentFields(this.value);
        });
    }
}

function updateContentFields(contentType) {
    const fieldsContainer = document.getElementById('contentSpecificFields');
    fieldsContainer.innerHTML = '';

    switch(contentType) {
        case 'assignment':
            fieldsContainer.innerHTML = `
                <div class="content-specific-fields">
                    <h5>Assignment Details</h5>
                    <div class="form-group">
                        <label for="assignment_title">Assignment Title</label>
                        <input type="text" id="assignment_title" name="assignment_title" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="assignment_instructions">Instructions</label>
                        <textarea id="assignment_instructions" name="assignment_instructions" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="datetime-local" id="due_date" name="due_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="max_points">Maximum Points</label>
                        <input type="number" id="max_points" name="max_points" class="form-control" min="0">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="allow_late_submission" name="allow_late_submission" value="1">
                            Allow late submissions
                        </label>
                    </div>
                </div>
            `;
            break;
        case 'quiz':
            fieldsContainer.innerHTML = `
                <div class="content-specific-fields">
                    <h5>Quiz Details</h5>
                    <div class="form-group">
                        <label for="quiz_title">Quiz Title</label>
                        <input type="text" id="quiz_title" name="quiz_title" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="quiz_description">Quiz Description</label>
                        <textarea id="quiz_description" name="quiz_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="time_limit">Time Limit (minutes)</label>
                                <input type="number" id="time_limit" name="time_limit" class="form-control" min="1" value="30">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="question_count">Number of Questions</label>
                                <input type="number" id="question_count" name="question_count" class="form-control" min="1" value="10">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="quiz_instructions">Quiz Instructions</label>
                        <textarea id="quiz_instructions" name="quiz_instructions" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            `;
            break;
        case 'test':
            fieldsContainer.innerHTML = `
                <div class="content-specific-fields">
                    <h5>Test Details</h5>
                    <div class="form-group">
                        <label for="test_title">Test Title</label>
                        <input type="text" id="test_title" name="test_title" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="test_description">Test Description</label>
                        <textarea id="test_description" name="test_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="test_date">Test Date</label>
                        <input type="datetime-local" id="test_date" name="test_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="duration">Duration (minutes)</label>
                        <input type="number" id="duration" name="duration" class="form-control" min="1">
                    </div>
                    <div class="form-group">
                        <label for="total_marks">Total Marks</label>
                        <input type="number" id="total_marks" name="total_marks" class="form-control" min="0">
                    </div>
                </div>
            `;
            break;
        case 'link':
            fieldsContainer.innerHTML = `
                <div class="content-specific-fields">
                    <h5>External Link Details</h5>
                    <div class="form-group">
                        <label for="link_title">Link Title</label>
                        <input type="text" id="link_title" name="link_title" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="link_description">Link Description</label>
                        <textarea id="link_description" name="link_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="external_url">External URL</label>
                        <input type="url" id="external_url" name="external_url" class="form-control" placeholder="https://example.com">
                    </div>
                </div>
            `;
            break;
    }
}

// Initialize filtering
function initializeFiltering() {
    const programSelect = document.getElementById('programSelect');
    const filterSection = document.getElementById('filterSection');
    
    if (programSelect && filterSection) {
        // Show filter section when program is selected
        if (programSelect.value) {
            filterSection.style.display = 'block';
            loadBatchesForFilter(programSelect.value);
            loadCoursesForFilter(programSelect.value);
        }
        
        programSelect.addEventListener('change', function() {
            if (this.value) {
                filterSection.style.display = 'block';
                loadBatchesForFilter(this.value);
                loadCoursesForFilter(this.value);
            } else {
                filterSection.style.display = 'none';
            }
        });
    }

    // Filter functionality
    const batchFilter = document.getElementById('batchFilter');
    const courseFilter = document.getElementById('courseFilter');
    const learningModeFilter = document.getElementById('learningModeFilter');
    const contentTypeFilter = document.getElementById('contentTypeFilter');

    if (batchFilter) {
        batchFilter.addEventListener('change', applyFilters);
    }
    if (courseFilter) {
        courseFilter.addEventListener('change', applyFilters);
    }
    if (learningModeFilter) {
        learningModeFilter.addEventListener('change', applyFilters);
    }
    if (contentTypeFilter) {
        contentTypeFilter.addEventListener('change', applyFilters);
    }
}

function loadBatchesForFilter(programId) {
    const batchFilter = document.getElementById('batchFilter');
    if (!batchFilter) return;

    fetch(`/admin/programs/${programId}/batches`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                batchFilter.innerHTML = '<option value="">All Batches</option>';
                data.batches.forEach(batch => {
                    const option = document.createElement('option');
                    option.value = batch.id;
                    option.textContent = batch.batch_name;
                    batchFilter.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading batches for filter:', error);
        });
}

function loadCoursesForFilter(programId) {
    const courseFilter = document.getElementById('courseFilter');
    if (!courseFilter) return;

    fetch(`/admin/programs/${programId}/courses`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                courseFilter.innerHTML = '<option value="">All Courses</option>';
                data.courses.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.subject_id;
                    option.textContent = course.subject_name;
                    courseFilter.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading courses for filter:', error);
        });
}

function applyFilters() {
    const batchFilter = document.getElementById('batchFilter').value;
    const courseFilter = document.getElementById('courseFilter').value;
    const learningModeFilter = document.getElementById('learningModeFilter').value;
    const contentTypeFilter = document.getElementById('contentTypeFilter').value;

    const moduleCards = document.querySelectorAll('.module-card');
    let visibleCount = 0;
    
    moduleCards.forEach(card => {
        const cardBatchId = card.getAttribute('data-batch-id');
        const cardCourseId = card.getAttribute('data-course-id');
        const cardLearningMode = card.getAttribute('data-learning-mode');
        const cardContentType = card.getAttribute('data-content-type');

        let showCard = true;

        // Apply batch filter
        if (batchFilter && cardBatchId !== batchFilter) {
            showCard = false;
        }

        // Apply course filter
        if (courseFilter && cardCourseId !== courseFilter) {
            showCard = false;
        }

        // Apply learning mode filter
        if (learningModeFilter && cardLearningMode !== learningModeFilter) {
            showCard = false;
        }

        // Apply content type filter
        if (contentTypeFilter && cardContentType !== contentTypeFilter) {
            showCard = false;
        }

        // Show or hide card
        if (showCard) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // Show message if no modules match filters
    const moduleGrid = document.getElementById('sortableModules');
    if (moduleGrid) {
        const noResultsMsg = document.getElementById('noFilterResults');
        if (visibleCount === 0) {
            if (!noResultsMsg) {
                const msgDiv = document.createElement('div');
                msgDiv.id = 'noFilterResults';
                msgDiv.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-search" style="font-size: 3rem; color: #6c757d; margin-bottom: 1rem;"></i>
                        <h4 style="color: #6c757d; margin-bottom: 1rem;">No Modules Found</h4>
                        <p style="color: #6c757d;">No modules match your current filter criteria.</p>
                    </div>
                `;
                moduleGrid.appendChild(msgDiv);
            }
        } else {
            if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }
    }
}

// Initialize sorting
function initializeSorting() {
    const sortableModules = document.getElementById('sortableModules');
    if (sortableModules) {
        // Make modules sortable
        sortableModules.addEventListener('dragstart', function(e) {
            if (e.target.classList.contains('module-card')) {
                e.target.classList.add('dragging');
                e.dataTransfer.setData('text/plain', e.target.dataset.moduleId);
            }
        });

        sortableModules.addEventListener('dragend', function(e) {
            if (e.target.classList.contains('module-card')) {
                e.target.classList.remove('dragging');
            }
        });

        sortableModules.addEventListener('dragover', function(e) {
            e.preventDefault();
            const draggingElement = document.querySelector('.dragging');
            const afterElement = getDragAfterElement(sortableModules, e.clientY);
            
            if (afterElement == null) {
                sortableModules.appendChild(draggingElement);
            } else {
                sortableModules.insertBefore(draggingElement, afterElement);
            }
        });

        sortableModules.addEventListener('drop', function(e) {
            e.preventDefault();
            updateModuleOrder();
        });

        // Make module cards draggable
        document.querySelectorAll('.module-card').forEach(card => {
            card.setAttribute('draggable', true);
        });
    }
}

function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.module-card:not(.dragging)')];
    
    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}

function updateModuleOrder() {
    const moduleCards = document.querySelectorAll('.module-card');
    const moduleIds = [];
    
    moduleCards.forEach((card, index) => {
        moduleIds.push(card.getAttribute('data-module-id'));
    });

    // Send updated order to server
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
            showNotification('Module order updated successfully!', 'success');
        } else {
            showNotification('Error updating module order: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error updating module order:', error);
        showNotification('Error updating module order', 'error');
    });
}

// Archive confirmation functions
function showArchiveConfirmation(moduleId, moduleName) {
    currentArchiveModuleId = moduleId;
    document.getElementById('archiveModuleName').textContent = moduleName;
    document.getElementById('archiveConfirmationModal').classList.add('show');
}

function closeArchiveModal() {
    document.getElementById('archiveConfirmationModal').classList.remove('show');
    currentArchiveModuleId = null;
}

function confirmArchive() {
    if (!currentArchiveModuleId) return;

    fetch(`/admin/modules/${currentArchiveModuleId}/archive`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Module archived successfully!', 'success');
            // Remove the module card from the display
            const moduleCard = document.querySelector(`[data-module-id="${currentArchiveModuleId}"]`);
            if (moduleCard) {
                moduleCard.remove();
            }
            closeArchiveModal();
        } else {
            showNotification('Error archiving module: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error archiving module', 'error');
    });
}

// Override modal functions
function showOverrideModal(moduleId, moduleName) {
  currentOverrideModuleId = moduleId;
  document.getElementById('overrideModuleName').textContent = moduleName;

  fetch(`/admin/modules/${moduleId}/override`, {
    method: 'GET',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(res => {
    if (!res.ok) throw new Error(res.statusText);
    return res.json();
  })
  .then(data => {
    // reset all checkboxes
    document.querySelectorAll('#overrideModal input[type="checkbox"]')
            .forEach(cb => cb.checked = false);

    // check those that are enabled
    (data.overrides || []).forEach(name => {
      const cb = document.getElementById(`override_${name}_modal`);
      if (cb) cb.checked = true;
    });

    document.getElementById('overrideModal').classList.add('show');
  })
  .catch(err => {
    console.error('Error loading override settings:', err);
    showNotification('Error loading override settings', 'error');
  });
}

function closeOverrideModal() {
    document.getElementById('overrideModal').classList.remove('show');
    currentOverrideModuleId = null;
}

function saveOverrideSettings() {
  if (!currentOverrideModuleId) return;

  const overrides = Array.from(
    document.querySelectorAll('#overrideModal input[type="checkbox"]:checked')
  ).map(cb => cb.value);

  fetch(`/admin/modules/${currentOverrideModuleId}/override`, {
    method: 'PATCH',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({ admin_override: overrides })
  })
  .then(res => {
    if (!res.ok) throw new Error(res.statusText);
    return res.json();
  })
  .then(data => {
    showNotification('Override settings saved successfully!', 'success');
    closeOverrideModal();

    // update the little unlock icon on the card
    const card = document.querySelector(`[data-module-id="${currentOverrideModuleId}"]`);
    if (card) {
      const status = card.querySelector('.module-status');
      // remove any existing unlock icons
      status.querySelectorAll('.bi-unlock-fill').forEach(el => el.parentNode.remove());
      // if any overrides left, add it back
      if (overrides.length) {
        status.innerHTML += '<span class="text-success"><i class="bi bi-unlock-fill"></i></span>';
      }
    }
  })
  .catch(err => {
    console.error('Error saving override settings:', err);
    showNotification('Error saving override settings', 'error');
  });
}

// Edit module function
function editModule(moduleId, moduleName, moduleDescription, programId, attachment) {
    console.log('Editing module:', moduleId, moduleName, moduleDescription, programId);
    
    const editModalBg = document.getElementById('editModalBg');
    const editForm = document.getElementById('editModuleForm');
    const editProgramSelect = document.getElementById('editModalProgramSelect');
    const editModuleName = document.getElementById('editModuleName');
    const editModuleDescription = document.getElementById('editModuleDescription');
    
    // Set form action
    editForm.action = `/admin/modules/${moduleId}`;
    
    // Set form values
    editProgramSelect.value = programId;
    editModuleName.value = moduleName;
    editModuleDescription.value = moduleDescription;
    
    // Show modal
    editModalBg.classList.add('show');
}

// Initialize course modal functionality
function initializeCourseModals() {
    // Show add course modal
    const showAddCourseModalBtn = document.getElementById('showAddCourseModal');
    if (showAddCourseModalBtn) {
        showAddCourseModalBtn.addEventListener('click', function() {
            console.log('Add course button clicked');
            const modal = document.getElementById('addCourseModalBg');
            if (modal) {
                modal.classList.add('show');
                console.log('Course modal opened');
            } else {
                console.error('Course modal not found');
            }
        });
    } else {
        console.error('Add course button not found');
    }

    // Close add course modal
    const closeAddCourseModal = document.getElementById('closeAddCourseModal');
    if (closeAddCourseModal) {
        closeAddCourseModal.addEventListener('click', function() {
            document.getElementById('addCourseModalBg').classList.remove('show');
        });
    }

    const closeAddCourseModalBtn = document.getElementById('closeAddCourseModalBtn');
    if (closeAddCourseModalBtn) {
        closeAddCourseModalBtn.addEventListener('click', function() {
            document.getElementById('addCourseModalBg').classList.remove('show');
        });
    }

    // Close module courses modal
    const closeModuleCoursesModal = document.getElementById('closeModuleCoursesModal');
    if (closeModuleCoursesModal) {
        closeModuleCoursesModal.addEventListener('click', function() {
            document.getElementById('moduleCoursesModalBg').classList.remove('show');
        });
    }

    const closeModuleCoursesModalBtn = document.getElementById('closeModuleCoursesModalBtn');
    if (closeModuleCoursesModalBtn) {
        closeModuleCoursesModalBtn.addEventListener('click', function() {
            document.getElementById('moduleCoursesModalBg').classList.remove('show');
        });
    }

    // Close course content modal
    const closeCourseContentModal = document.getElementById('closeCourseContentModal');
    if (closeCourseContentModal) {
        closeCourseContentModal.addEventListener('click', function() {
            document.getElementById('courseContentModalBg').classList.remove('show');
        });
    }

    const closeCourseContentModalBtn = document.getElementById('closeCourseContentModalBtn');
    if (closeCourseContentModalBtn) {
        closeCourseContentModalBtn.addEventListener('click', function() {
            document.getElementById('courseContentModalBg').classList.remove('show');
        });
    }

    // Handle add course form submission
    const addCourseForm = document.getElementById('addCourseForm');
    if (addCourseForm) {
        addCourseForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitCourseForm();
        });
    }
}

// Initialize course content modal functionality
function initializeCourseContentModal() {
    const programSelect = document.getElementById('contentProgramSelect');
    const moduleSelect = document.getElementById('contentModuleSelect');
    const courseSelect = document.getElementById('contentCourseSelect');
    const contentTypeSelect = document.getElementById('contentTypeSelect');

    if (programSelect) {
        programSelect.addEventListener('change', function() {
            const programId = this.value;
            loadModulesForContent(programId);
            resetCourseSelect();
        });
    }

    if (moduleSelect) {
        moduleSelect.addEventListener('change', function() {
            const moduleId = this.value;
            loadCoursesForContent(moduleId);
        });
    }

    if (contentTypeSelect) {
        contentTypeSelect.addEventListener('change', function() {
            updateDynamicContentFields(this.value);
        });
    }

    // Handle form submission
    const courseContentForm = document.getElementById('courseContentForm');
    if (courseContentForm) {
        courseContentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitCourseContentForm();
        });
    }
}

function loadModulesForContent(programId) {
    const moduleSelect = document.getElementById('contentModuleSelect');
    
    moduleSelect.innerHTML = '<option value="">Loading modules...</option>';
    moduleSelect.disabled = true;
    resetCourseSelect();

    if (!programId) {
        moduleSelect.innerHTML = '<option value="">-- Select Module --</option>';
        return;
    }

    fetch(`/api/programs/${programId}/modules`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                moduleSelect.innerHTML = '<option value="">-- Select Module --</option>';
                data.modules.forEach(module => {
                    const option = document.createElement('option');
                    option.value = module.modules_id || module.id;
                    option.textContent = module.module_name || module.name;
                    moduleSelect.appendChild(option);
                });
                moduleSelect.disabled = false;
            } else {
                moduleSelect.innerHTML = '<option value="">Error loading modules</option>';
            }
        })
        .catch(error => {
            console.error('Error loading modules:', error);
            moduleSelect.innerHTML = '<option value="">Error loading modules</option>';
        });
}

function loadCoursesForContent(moduleId) {
    const courseSelect = document.getElementById('contentCourseSelect');
    
    courseSelect.innerHTML = '<option value="">Loading courses...</option>';
    courseSelect.disabled = true;

    if (!moduleId) {
        courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
        return;
    }

    fetch(`/admin/modules/${moduleId}/courses`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
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

function resetCourseSelect() {
    const courseSelect = document.getElementById('contentCourseSelect');
    courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
    courseSelect.disabled = true;
}

function updateDynamicContentFields(contentType) {
    const fieldsContainer = document.getElementById('dynamicContentFields');
    fieldsContainer.innerHTML = '';

    switch(contentType) {
        case 'lesson':
            fieldsContainer.innerHTML = `
                <div class="content-specific-fields">
                    <h5>Lesson Details</h5>
                    <div class="form-group">
                        <label for="lesson_video_url">Video URL (YouTube/Vimeo)</label>
                        <input type="url" id="lesson_video_url" name="lesson_video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                </div>
            `;
            break;
        case 'assignment':
            fieldsContainer.innerHTML = `
                <div class="content-specific-fields">
                    <h5>Assignment Details</h5>
                    <div class="form-group">
                        <label for="assignment_instructions">Instructions</label>
                        <textarea id="assignment_instructions" name="assignment_instructions" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="datetime-local" id="due_date" name="due_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="max_points">Maximum Points</label>
                        <input type="number" id="max_points" name="max_points" class="form-control" min="0">
                    </div>
                </div>
            `;
            break;
        case 'quiz':
            fieldsContainer.innerHTML = `
                <div class="content-specific-fields">
                    <h5>Quiz Details</h5>
                    <div class="form-group">
                        <label for="quiz_instructions">Quiz Instructions</label>
                        <textarea id="quiz_instructions" name="quiz_instructions" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="time_limit">Time Limit (minutes)</label>
                                <input type="number" id="time_limit" name="time_limit" class="form-control" min="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="max_points">Maximum Points</label>
                                <input type="number" id="max_points" name="max_points" class="form-control" min="0">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            break;
        case 'test':
            fieldsContainer.innerHTML = `
                <div class="content-specific-fields">
                    <h5>Test Details</h5>
                    <div class="form-group">
                        <label for="test_instructions">Test Instructions</label>
                        <textarea id="test_instructions" name="test_instructions" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="test_date">Test Date</label>
                        <input type="datetime-local" id="test_date" name="test_date" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="test_duration">Duration (minutes)</label>
                                <input type="number" id="test_duration" name="test_duration" class="form-control" min="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="total_marks">Total Marks</label>
                                <input type="number" id="total_marks" name="total_marks" class="form-control" min="0">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            break;
    }
}

function submitCourseContentForm() {
    const form = document.getElementById('courseContentForm');
    const formData = new FormData(form);
    
    // Validate required fields
    const requiredFields = ['program_id', 'module_id', 'course_id', 'content_type', 'content_title'];
    const missingFields = [];
    
    for (const field of requiredFields) {
        const value = formData.get(field);
        if (!value || value === 'null' || value === '') {
            missingFields.push(field);
        }
    }
    
    if (missingFields.length > 0) {
        showNotification(`Please fill in all required fields: ${missingFields.join(', ')}`, 'error');
        return;
    }
    
    fetch('/admin/modules/course-content-store', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        return response.json().then(data => {
            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }
            return data;
        });
    })
    .then(data => {
        if (data.success) {
            showNotification('Course content created successfully!', 'success');
            document.getElementById('batchModalBg').classList.remove('show');
            form.reset();
            
            // Reset dropdowns to initial state
            document.getElementById('contentModuleSelect').innerHTML = '<option value="">-- Select Module --</option>';
            document.getElementById('contentModuleSelect').disabled = true;
            document.getElementById('contentCourseSelect').innerHTML = '<option value="">-- Select Course --</option>';
            document.getElementById('contentCourseSelect').disabled = true;
            document.getElementById('dynamicContentFields').innerHTML = '';
        } else {
            console.error('Course content creation failed:', data);
            let errorMessage = data.message || 'Unknown error';
            
            if (data.errors) {
                const errorList = Object.keys(data.errors).map(key => `${key}: ${data.errors[key].join(', ')}`).join('\n');
                errorMessage += '\n\nValidation errors:\n' + errorList;
            }
            
            showNotification('Error creating course content: ' + errorMessage, 'error');
        }
    })
    .catch(error => {
        console.error('Error creating course content:', error);
        showNotification('Error creating course content: ' + error.message, 'error');
    });
}

// Notification function
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remove notification after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Course functionality functions
function submitCourseForm() {
    const form = document.getElementById('addCourseForm');
    const formData = new FormData(form);
    
    // Debug: Log form data
    console.log('Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }
    
    // Additional validation for module selection
    const moduleSelect = document.getElementById('courseModuleSelect');
    const moduleId = moduleSelect.value;
    
    console.log('Module select element:', moduleSelect);
    console.log('Module select value:', moduleId);
    console.log('Module select disabled:', moduleSelect.disabled);
    
    if (!moduleId || moduleId === 'null' || moduleId === '') {
        showNotification('Please select a module for this course.', 'error');
        return;
    }
    
    // Validate required fields
    const requiredFields = ['program_id', 'module_id', 'subject_name', 'subject_price'];
    const missingFields = [];
    
    for (const field of requiredFields) {
        const value = formData.get(field);
        if (!value || value === 'null' || value === '') {
            missingFields.push(field);
        }
    }
    
    if (missingFields.length > 0) {
        showNotification(`Please fill in all required fields: ${missingFields.join(', ')}`, 'error');
        return;
    }
    
    fetch('/admin/courses', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        return response.json().then(data => {
            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }
            return data;
        });
    })
    .then(data => {
        if (data.success) {
            showNotification('Course created successfully!', 'success');
            document.getElementById('addCourseModalBg').classList.remove('show');
            document.getElementById('addCourseForm').reset();
            
            // Reset the module dropdown to disabled state
            const moduleSelect = document.getElementById('courseModuleSelect');
            moduleSelect.innerHTML = '<option value="">-- Select Module --</option>';
            moduleSelect.disabled = true;
            
            // Refresh the modules view if needed
            if (typeof loadModules === 'function') {
                loadModules();
            }
        } else {
            console.error('Course creation failed:', data);
            let errorMessage = data.message || 'Unknown error';
            
            if (data.errors) {
                const errorList = Object.keys(data.errors).map(key => `${key}: ${data.errors[key].join(', ')}`).join('\n');
                errorMessage += '\n\nValidation errors:\n' + errorList;
            }
            
            if (data.debug) {
                console.log('Debug info:', data.debug);
            }
            
            showNotification('Error creating course: ' + errorMessage, 'error');
        }
    })
    .catch(error => {
        console.error('Error creating course:', error);
        showNotification('Error creating course: ' + error.message, 'error');
    });
}

function showModuleCourses(moduleId, moduleName) {
    document.getElementById('moduleCoursesTitle').textContent = `Courses in ${moduleName}`;
    document.getElementById('moduleCoursesModalBg').classList.add('show');
    
    fetch(`/admin/modules/${moduleId}/courses`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayModuleCourses(data.courses);
            } else {
                document.getElementById('moduleCoursesContent').innerHTML = '<p>Error loading courses</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('moduleCoursesContent').innerHTML = '<p>Error loading courses</p>';
        });
}

function displayModuleCourses(courses) {
    const content = document.getElementById('moduleCoursesContent');
    
    if (courses.length === 0) {
        content.innerHTML = '<p>No courses found for this module.</p>';
        return;
    }
    
    let html = '<div class="courses-grid">';
    courses.forEach(course => {
        html += `
            <div class="course-card">
                <div class="course-header">
                    <h4>${course.subject_name}</h4>
                    <span class="course-price">$${course.subject_price}</span>
                </div>
                <div class="course-description">
                    ${course.subject_description || 'No description'}
                </div>
                <div class="course-meta">
                    <span class="course-required ${course.is_required ? 'required' : 'optional'}">
                        ${course.is_required ? 'Required' : 'Optional'}
                    </span>
                    <span class="course-lessons">${course.lessons ? course.lessons.length : 0} lessons</span>
                </div>
                <div class="course-actions">
                    <button onclick="showCourseContent(${course.subject_id}, '${course.subject_name}')" class="view-btn">
                        <i class="bi bi-eye"></i> View Content
                    </button>
                    <button onclick="editCourse(${course.subject_id})" class="edit-btn">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    content.innerHTML = html;
}

function showCourseContent(courseId, courseName) {
    document.getElementById('courseContentTitle').textContent = `Content in ${courseName}`;
    document.getElementById('courseContentModalBg').classList.add('show');
    
    fetch(`/admin/courses/${courseId}/content`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayCourseContent(data.course);
            } else {
                document.getElementById('courseContentContent').innerHTML = '<p>Error loading content</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('courseContentContent').innerHTML = '<p>Error loading content</p>';
        });
}

function displayCourseContent(course) {
    const content = document.getElementById('courseContentContent');
    
    if (!course.lessons || course.lessons.length === 0) {
        content.innerHTML = '<p>No lessons found for this course.</p>';
        return;
    }
    
    let html = '<div class="lessons-list">';
    course.lessons.forEach(lesson => {
        html += `
            <div class="lesson-card">
                <div class="lesson-header">
                    <h5>${lesson.lesson_name}</h5>
                    <span class="lesson-price">$${lesson.lesson_price || '0.00'}</span>
                </div>
                <div class="lesson-description">
                    ${lesson.lesson_description || 'No description'}
                </div>
                <div class="lesson-content">
                    <h6>Content Items:</h6>
                    <div class="content-items">
        `;
        
        if (lesson.content_items && lesson.content_items.length > 0) {
            lesson.content_items.forEach(item => {
                html += `
                    <div class="content-item">
                        <span class="content-type-badge ${item.content_type}">
                            ${getContentTypeIcon(item.content_type)} ${item.content_type}
                        </span>
                        <span class="content-title">${item.content_title}</span>
                        ${item.max_points ? `<span class="content-points">${item.max_points} pts</span>` : ''}
                    </div>
                `;
            });
        } else {
            html += '<p class="no-content">No content items</p>';
        }
        
        html += `
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    content.innerHTML = html;
}

function getContentTypeIcon(contentType) {
    switch(contentType) {
        case 'lesson': return '<i class="bi bi-book"></i>';
        case 'assignment': return '<i class="bi bi-file-earmark-text"></i>';
        case 'quiz': return '<i class="bi bi-question-circle"></i>';
        case 'test': return '<i class="bi bi-clipboard-check"></i>';
        default: return '<i class="bi bi-file"></i>';
    }
}

function editCourse(courseId) {
    // Placeholder for edit course functionality
    console.log('Edit course:', courseId);
    showNotification('Edit course functionality coming soon!', 'info');
}
</script>
@endpush
