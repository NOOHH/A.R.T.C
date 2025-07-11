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
        <button type="button" class="batch-upload-btn" id="showBatchModal">
            <i class="bi bi-upload"></i> Batch Upload Multiple PDFs
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
                        <div class="module-card" data-module-id="{{ $module->modules_id }}" 
                             data-batch-id="{{ $module->batch_id }}"
                             data-learning-mode="{{ strtolower($module->learning_mode) }}"
                             data-content-type="{{ $module->content_type }}">
                            <div class="module-drag-handle">
                                <i class="bi bi-grip-vertical"></i>
                            </div>
                            
                            <div class="module-header">
                                <div>
                                    <div class="d-flex gap-2 mb-2">
                                        @if($module->content_type === 'ai_quiz')
                                            <span class="module-type-badge" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                                                <i class="bi bi-robot"></i> AI Quiz
                                            </span>
                                        @else
                                            <span class="module-type-badge">
                                                @switch($module->content_type)
                                                    @case('assignment')<i class="bi bi-file-earmark-text"></i> Assignment @break
                                                    @case('quiz')<i class="bi bi-question-circle"></i> Quiz @break
                                                    @case('test')<i class="bi bi-clipboard-check"></i> Test @break
                                                    @case('link')<i class="bi bi-link-45deg"></i> Link @break
                                                    @default<i class="bi bi-book"></i> Module @break
                                                @endswitch
                                            </span>
                                        @endif
                                        <span class="learning-mode-badge {{ strtolower($module->learning_mode) === 'asynchronous' ? 'asynchronous' : '' }}">
                                            @if(strtolower($module->learning_mode) === 'asynchronous')
                                                <i class="bi bi-clock"></i> Async
                                            @else
                                                <i class="bi bi-people"></i> Sync
                                            @endif
                                        </span>
                                    </div>
                                    <h3 class="module-title">{{ $module->module_name }}</h3>
                                    @if($module->batch)
                                        <div class="batch-info mb-2">
                                            <small class="text-muted">
                                                <i class="bi bi-collection"></i> 
                                                Batch: {{ $module->batch->batch_name }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            @if($module->module_description)
                                <div class="module-description">{{ $module->module_description }}</div>
                            @endif

                            @if($module->content_data)
                                <div class="content-details">
                                    @php $data = $module->content_data @endphp
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

                            <div class="module-meta">
                                <div class="module-date">
                                    <i class="bi bi-calendar-date"></i>
                                    {{ $module->created_at->format('M d, Y') }}
                                </div>
                                <div class="module-status">
                                    @if($module->attachment)
                                        <span class="text-primary">
                                            <i class="bi bi-paperclip"></i>
                                        </span>
                                    @endif
                                    @if($module->admin_override)
                                        <span class="text-success">
                                            <i class="bi bi-unlock-fill"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="module-actions">
                                <button class="action-btn btn-edit" 
                                        onclick="editModule({{ $module->modules_id }}, '{{ addslashes($module->module_name) }}', '{{ addslashes($module->module_description) }}', {{ $module->program_id }}, '{{ $module->attachment }}')">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button class="action-btn btn-override" 
                                        onclick="showOverrideModal({{ $module->modules_id }}, '{{ addslashes($module->module_name) }}')">
                                    <i class="bi bi-unlock-fill"></i> Override
                                </button>
                                <button class="action-btn btn-archive" 
                                        onclick="showArchiveConfirmation({{ $module->modules_id }}, '{{ addslashes($module->module_name) }}')">
                                    <i class="bi bi-archive"></i> Archive
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-modules">
                    <p>No modules found for this program.</p>
                    <button type="button" class="add-module-btn" id="showAddModalEmpty">
                        <i class="bi bi-plus-circle"></i> Create First Module
                    </button>
                </div>
            @endif
        @else
            <div class="select-program-msg">
                <p>Select a program from the dropdown above to view and manage its modules</p>
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
                        <option value="quiz">Quiz</option>
                        <option value="test">Test</option>
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

                <div class="form-group">
                    <label class="form-label">Admin Override Settings</label>
                    <div class="admin-override-checkboxes">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="override_completion" name="admin_override[]" value="completion">
                            <label class="form-check-label" for="override_completion">
                                <i class="bi bi-check-circle"></i> Override Completion Requirements
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="override_prerequisites" name="admin_override[]" value="prerequisites">
                            <label class="form-check-label" for="override_prerequisites">
                                <i class="bi bi-arrow-right-circle"></i> Override Prerequisites
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="override_time_limits" name="admin_override[]" value="time_limits">
                            <label class="form-check-label" for="override_time_limits">
                                <i class="bi bi-clock"></i> Override Time Limits
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="override_access_control" name="admin_override[]" value="access_control">
                            <label class="form-check-label" for="override_access_control">
                                <i class="bi bi-unlock"></i> Override Access Control
                            </label>
                        </div>
                    </div>
                    <small class="form-text text-muted">Select which admin overrides should be enabled for this module</small>
                </div>

            </div>
            
            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="closeAddModalBtn">Cancel</button>
                <button type="submit" class="add-btn">Create Content</button>
            </div>
        </form>
    </div>
</div>

<!-- Batch Upload Modal -->
<div class="modal-bg" id="batchModalBg">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="bi bi-upload"></i> Batch Upload Modules</h3>
            <button type="button" class="modal-close" id="closeBatchModal">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <form action="{{ route('admin.modules.batch-store') }}" method="POST" enctype="multipart/form-data" id="batchModuleForm">
            <div class="modal-body">
                @csrf
                
                <div class="form-group">
                    <label for="batchModalProgramSelect">Program <span class="text-danger">*</span></label>
                    <select id="batchModalProgramSelect" name="program_id" class="form-select" required>
                        <option value="">-- Select Program --</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="batch_batch_id">Batch <span class="text-danger">*</span></label>
                    <select id="batch_batch_id" name="batch_id" class="form-select" required disabled>
                        <option value="">-- Select Batch --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="batchPdfFiles">PDF Files</label>
                    <div class="dropzone" id="batchDropzone">
                        <input type="file" id="batchPdfFiles" name="pdf_files[]" multiple accept=".pdf" required>
                        <p>Drop PDF files here or click to select</p>
                        <small class="text-muted">Each PDF will be converted to a module automatically</small>
                    </div>
                    <div id="selectedFiles" style="display: none;">
                        <strong>Selected Files:</strong>
                        <ul id="fileList"></ul>
                    </div>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="closeBatchModalBtn">Cancel</button>
                <button type="submit" class="add-btn" id="uploadPdfBtn" disabled>Upload PDF Files</button>
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
@endsection

@push('scripts')
<script>
// Global variables
let currentArchiveModuleId = null;
let currentOverrideModuleId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initializeModals();
    initializeProgramSelector();
    initializeBatchUpload();
    initializeContentTypeFields();
    initializeFiltering();
    initializeSorting();
});

// Initialize modal functionality
function initializeModals() {
    // Show add modal
    document.getElementById('showAddModal').addEventListener('click', function() {
        document.getElementById('addModalBg').classList.add('show');
    });

    // Show add modal from empty state
    const showAddModalEmpty = document.getElementById('showAddModalEmpty');
    if (showAddModalEmpty) {
        showAddModalEmpty.addEventListener('click', function() {
            document.getElementById('addModalBg').classList.add('show');
        });
    }

    // Close add modal
    document.getElementById('closeAddModal').addEventListener('click', function() {
        document.getElementById('addModalBg').classList.remove('show');
    });

    document.getElementById('closeAddModalBtn').addEventListener('click', function() {
        document.getElementById('addModalBg').classList.remove('show');
    });

    // Show batch modal
    document.getElementById('showBatchModal').addEventListener('click', function() {
        document.getElementById('batchModalBg').classList.add('show');
    });

    // Close batch modal
    document.getElementById('closeBatchModal').addEventListener('click', function() {
        document.getElementById('batchModalBg').classList.remove('show');
    });

    document.getElementById('closeBatchModalBtn').addEventListener('click', function() {
        document.getElementById('batchModalBg').classList.remove('show');
    });

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
        programSelect.addEventListener('change', function() {
            const programId = this.value;
            if (programId) {
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
            loadBatchesForProgram(this.value, 'batch_id');
        });
    }
    
    if (batchModalProgramSelect) {
        batchModalProgramSelect.addEventListener('change', function() {
            loadBatchesForProgram(this.value, 'batch_batch_id');
        });
    }
}

// Function to load batches based on program selection
function loadBatchesForProgram(programId, batchSelectId) {
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
        .then(response => response.json())
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
        }
        
        programSelect.addEventListener('change', function() {
            if (this.value) {
                filterSection.style.display = 'block';
                loadBatchesForFilter(this.value);
            } else {
                filterSection.style.display = 'none';
            }
        });
    }

    // Filter functionality
    const batchFilter = document.getElementById('batchFilter');
    const learningModeFilter = document.getElementById('learningModeFilter');
    const contentTypeFilter = document.getElementById('contentTypeFilter');

    if (batchFilter) {
        batchFilter.addEventListener('change', applyFilters);
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

function applyFilters() {
    const batchFilter = document.getElementById('batchFilter').value;
    const learningModeFilter = document.getElementById('learningModeFilter').value;
    const contentTypeFilter = document.getElementById('contentTypeFilter').value;

    const moduleCards = document.querySelectorAll('.module-card');
    
    moduleCards.forEach(card => {
        const cardBatchId = card.getAttribute('data-batch-id');
        const cardLearningMode = card.getAttribute('data-learning-mode');
        const cardContentType = card.getAttribute('data-content-type');

        let showCard = true;

        // Apply batch filter
        if (batchFilter && cardBatchId !== batchFilter) {
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
        } else {
            card.style.display = 'none';
        }
    });
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
    
    // Load current override settings
    fetch(`/admin/modules/${moduleId}/override-settings`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const overrides = data.overrides || [];
                
                // Clear all checkboxes first
                document.querySelectorAll('#overrideModal input[type="checkbox"]').forEach(checkbox => {
                    checkbox.checked = false;
                });
                
                // Set checked state based on current overrides
                overrides.forEach(override => {
                    const checkbox = document.getElementById(`override_${override}_modal`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
                
                document.getElementById('overrideModal').classList.add('show');
            } else {
                showNotification('Error loading override settings', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading override settings:', error);
            showNotification('Error loading override settings', 'error');
        });
}

function closeOverrideModal() {
    document.getElementById('overrideModal').classList.remove('show');
    currentOverrideModuleId = null;
}

function saveOverrideSettings() {
    if (!currentOverrideModuleId) return;
    
    const checkedBoxes = document.querySelectorAll('#overrideModal input[type="checkbox"]:checked');
    const overrides = Array.from(checkedBoxes).map(checkbox => checkbox.value);
    
    fetch(`/admin/modules/${currentOverrideModuleId}/override`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ admin_override: overrides })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Override settings saved successfully!', 'success');
            closeOverrideModal();
            // Update the module status indicator
            const moduleCard = document.querySelector(`[data-module-id="${currentOverrideModuleId}"]`);
            if (moduleCard) {
                const statusIcon = moduleCard.querySelector('.module-status .bi-unlock-fill');
                if (overrides.length > 0) {
                    if (!statusIcon) {
                        const statusContainer = moduleCard.querySelector('.module-status');
                        statusContainer.innerHTML += '<span class="text-success"><i class="bi bi-unlock-fill"></i></span>';
                    }
                } else {
                    if (statusIcon) {
                        statusIcon.parentElement.remove();
                    }
                }
            }
        } else {
            showNotification('Error saving override settings: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error saving override settings:', error);
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
</script>
@endpush
