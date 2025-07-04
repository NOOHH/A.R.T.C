@extends('admin.admin-dashboard-layout')

@section('title', 'Modules')

@push('styles')
<style>
  /* Custom styles that complement Bootstrap */
  .modules-container {
    background: #fff;
    padding: 2.5rem 1.25rem 3.75rem;
    margin: 2.5rem 0 0 0;
    max-width: 1400px;
    width: 100%;
    box-sizing: border-box;
  }

  .modules-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    text-transform: uppercase;
    letter-spacing: 2px;
  }

  .header-buttons .btn {
    margin-left: 0.75rem;
  }
  
  .add-module-btn, .view-archived-btn, .batch-upload-btn {
    background: #3498db;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    margin-left: 10px;
  }
  
  .add-module-btn:hover, .view-archived-btn:hover, .batch-upload-btn:hover {
    background: #2980b9;
    transform: translateY(-2px);
  }
  
  .view-archived-btn {
    background: #f39c12;
  }
  
  .view-archived-btn:hover {
    background: #e67e22;
  }
  
  .batch-upload-btn {
    background: #9b59b6;
  }
  
  .batch-upload-btn:hover {
    background: #8e44ad;
  }

  .program-selector select {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #e1e5e9;
    transition: all 0.3s ease;
  }

  .program-selector select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
  }
  
  /* Program Overview Section */
  .program-overview {
    background: linear-gradient(135deg, #8e44ad 0%, #3498db 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  }
  
  .program-overview h2 {
    font-size: 2rem;
    margin: 0 0 10px 0;
    font-weight: 700;
  }
  
  .program-description {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 25px;
  }
  
  .program-stats {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
  }
  
  .stat-card {
    background: rgba(255,255,255,0.2);
    padding: 15px 25px;
    border-radius: 10px;
    min-width: 120px;
    text-align: center;
  }
  
  .stat-number {
    font-size: 2rem;
    font-weight: 700;
    display: block;
  }
  
  .stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
  }
  
  /* Module Controls */
  .module-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
  }
  
  .module-filters {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }
  
  .filter-btn {
    background: #f1f2f6;
    border: none;
    padding: 8px 15px;
    border-radius: 30px;
    color: #555;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: 600;
    font-size: 0.9rem;
  }
  
  .filter-btn:hover {
    background: #e2e5ec;
  }
  
  .filter-btn.active {
    background: #3498db;
    color: white;
  }
  
  .save-order-btn {
    background: #2ecc71;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 30px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
  }
  
  .save-order-btn:hover {
    background: #27ae60;
    transform: translateY(-2px);
  }
  
  /* Modules List - New Card Style */
  .modules-list {
    margin-top: 30px;
  }
  
  .module-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    margin-bottom: 15px;
    display: flex;
    overflow: hidden;
    transition: all 0.3s ease;
    border-left: 5px solid #3498db;
  }
  
  .module-card[data-type="module"] { border-left-color: #3498db; }
  .module-card[data-type="assignment"] { border-left-color: #f39c12; }
  .module-card[data-type="quiz"] { border-left-color: #9b59b6; }
  .module-card[data-type="test"] { border-left-color: #e74c3c; }
  .module-card[data-type="link"] { border-left-color: #2ecc71; }
  
  .module-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transform: translateY(-3px);
  }
  
  .drag-handle {
    background: #f8f9fa;
    color: #aaa;
    width: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    cursor: move;
    transition: all 0.2s;
  }
  
  .drag-handle:hover {
    background: #eaecef;
    color: #555;
  }
  
  .module-content {
    flex: 1;
    padding: 20px;
  }
  
  .module-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  
  .module-description {
    color: #555;
    margin-bottom: 15px;
    line-height: 1.5;
  }
  
  .content-details {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 6px;
    font-size: 0.9rem;
    margin-bottom: 15px;
  }
  
  .content-type-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
  }
  
  .download-link {
    display: inline-flex;
    align-items: center;
    background: #f1f2f6;
    color: #555;
    padding: 8px 15px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
    margin-top: 10px;
    font-size: 0.9rem;
  }
  
  .download-link:hover {
    background: #e2e5ec;
    color: #3498db;
  }
  
  .module-actions {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    border-left: 1px solid #eee;
  }
  
  .edit-module-btn, .archive-btn, .preview-btn {
    padding: 8px 15px;
    border-radius: 6px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
    font-size: 0.9rem;
  }
  
  .edit-module-btn {
    background: #3498db;
    color: white;
  }
  
  .edit-module-btn:hover {
    background: #2980b9;
  }
  
  .archive-btn {
    background: #f39c12;
    color: white;
  }
  
  .archive-btn:hover {
    background: #e67e22;
  }
  
  .preview-btn {
    background: #2ecc71;
    color: white;
  }
  
  .preview-btn:hover {
    background: #27ae60;
  }
  
  /* Welcome Message */
  .select-program-msg {
    text-align: center;
    padding: 60px 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 15px;
    color: #2c3e50;
  }
  
  .select-program-msg h3 {
    font-size: 1.8rem;
    margin: 0 0 15px 0;
    font-weight: 700;
  }
  
  .select-program-msg p {
    font-size: 1.1rem;
    margin-bottom: 25px;
  }
  
  .lms-features {
    list-style: none;
    padding: 0;
    margin: 0;
    display: inline-block;
    text-align: left;
  }
  
  .lms-features li {
    padding: 8px 0;
    font-size: 1.1rem;
  }
  
  /* No modules state */
  .no-modules {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 10px;
    color: #6c757d;
    font-size: 1.1rem;
    border: 2px dashed #dee2e6;
  }

  /* Modal styles for preview */
  #previewModal .modal-body {
    padding: 0;
  }
  
  #previewModal .preview-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #e1e5e9;
  }
  
  #previewModal .preview-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
  }
  
  #previewModal .preview-content {
    padding: 20px;
  }
  
  #previewModal .preview-tabs {
    display: flex;
    background: #f1f2f6;
    padding: 10px 20px 0;
  }
  
  #previewModal .preview-tab {
    padding: 10px 20px;
    background: none;
    border: none;
    border-radius: 6px 6px 0 0;
    cursor: pointer;
  }
  
  #previewModal .preview-tab.active {
    background: white;
    font-weight: 600;
  }
  
  /* Modal Styles */
  .modal-bg {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    overflow-y: auto;
  }
  
  .modal-bg.show {
    display: flex !important;
  }
  
  .modal {
    background-color: white;
    padding: 30px;
    border-radius: 15px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    position: relative;
  }
  
  .modal h3 {
    margin: 0 0 20px 0;
    color: #2c3e50;
    font-size: 1.5rem;
  }
  
  .modal input, .modal textarea, .modal select {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 1rem;
    box-sizing: border-box;
  }
  
  .modal textarea {
    resize: vertical;
    min-height: 80px;
  }
  
  .content-type-fields {
    border: 1px solid #e1e5e9;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    background: #f8f9fa;
  }
  
  .dropzone {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    margin-bottom: 20px;
    background: #fafafa;
    position: relative;
    cursor: pointer;
  }
  
  .dropzone:hover {
    border-color: #3498db;
    background: #f0f8ff;
  }
  
  .dropzone input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
  }
  
  .modal-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
  }
  
  .cancel-btn, .add-btn {
    padding: 12px 25px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    font-size: 1rem;
  }
  
  .cancel-btn {
    background: #95a5a6;
    color: white;
  }
  
  .cancel-btn:hover {
    background: #7f8c8d;
  }
  
  .add-btn {
    background: #3498db;
    color: white;
  }
  
  .add-btn:hover {
    background: #2980b9;
  }
</style>
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Display validation errors -->
@if($errors->any())
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Display success message -->
@if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

<div class="modules-container">
    <div class="modules-header">
        <h1>Learning Management</h1>
        <div class="header-buttons">
            <a href="{{ route('admin.modules.archived') }}" class="view-archived-btn">
                <span>🗃️</span> View Archived
            </a>
            <button type="button" class="add-module-btn batch-upload-btn" id="showBatchModal">
                <span style="font-size:1.3em;">📚</span> Batch Upload
            </button>
            <button type="button" class="add-module-btn" id="showAddModal">
                <span style="font-size:1.3em;">&#43;</span> Add Content
            </button>
        </div>
    </div>

    <!-- Program Selector -->
    <div class="program-selector">
        <label for="programSelect">Select Program to Manage Learning Content:</label>
        <select id="programSelect" name="program_id">
            <option value="">-- Select a Program --</option>
            @foreach($programs as $program)
                <option value="{{ $program->program_id }}"
                    {{ request('program_id') == $program->program_id ? 'selected' : '' }}>
                    {{ $program->program_name }}{{ $program->is_archived ? ' (Archived)' : '' }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Modules Display Area -->
    <div id="modulesDisplayArea">
        @if(request('program_id') && isset($selectedProgram))
            <div class="program-overview">
                <h2>{{ $selectedProgram->program_name }}</h2>
                <p class="program-description">{{ $selectedProgram->program_description ?: 'No description available.' }}</p>
                
                <div class="program-stats">
                    <div class="stat-card">
                        <span class="stat-number">{{ $modules->count() }}</span>
                        <span class="stat-label">Content Items</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">{{ $modules->where('content_type', 'module')->count() }}</span>
                        <span class="stat-label">Modules</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">{{ $modules->where('content_type', 'assignment')->count() + $modules->where('content_type', 'quiz')->count() + $modules->where('content_type', 'test')->count() }}</span>
                        <span class="stat-label">Assessments</span>
                    </div>
                </div>
            </div>
            
            @if($modules->count() > 0)
                <!-- Module sorting controls -->
                <div class="module-controls">
                    <div class="module-filters">
                        <button class="filter-btn active" data-filter="all">All Content</button>
                        <button class="filter-btn" data-filter="module">Modules</button>
                        <button class="filter-btn" data-filter="assignment">Assignments</button>
                        <button class="filter-btn" data-filter="quiz">Quizzes</button>
                        <button class="filter-btn" data-filter="test">Tests</button>
                    </div>
                    <div class="sort-controls">
                        <button id="saveModuleOrder" class="save-order-btn" style="display:none;">Save Order</button>
                    </div>
                </div>
                
                <div class="modules-list sortable-modules" id="sortableModules">
                    @foreach($modules as $module)
                        <div class="module-card" data-id="{{ $module->modules_id }}" data-type="{{ $module->content_type }}">
                            <div class="drag-handle">≡</div>
                            <div class="module-content">
                                <div class="module-title">
                                    {{ $module->content_type_icon ?? '📚' }} {{ $module->module_name }}
                                    <span class="content-type-badge {{ $module->content_type ?? 'module' }}">
                                        {{ ucfirst($module->content_type) }}
                                    </span>
                                </div>
                                
                                <div class="module-description">
                                    {{ $module->module_description }}
                                </div>

                                @if($module->content_data)
                                    <div class="content-details">
                                        @php $data = $module->content_data @endphp
                                        @switch($module->content_type)
                                            @case('assignment')
                                                @if(!empty($data['assignment_title']))
                                                    <strong>Title:</strong> {{ $data['assignment_title'] }}<br>
                                                @endif
                                                @if(!empty($data['due_date']))
                                                    <strong>Due:</strong> {{ \Carbon\Carbon::parse($data['due_date'])->format('M d, Y g:i A') }}<br>
                                                @endif
                                                @if(!empty($data['max_points']))
                                                    <strong>Points:</strong> {{ $data['max_points'] }}
                                                @endif
                                                @break
                                            @case('quiz')
                                                @if(!empty($data['quiz_title']))
                                                    <strong>Title:</strong> {{ $data['quiz_title'] }}<br>
                                                @endif
                                                @if(!empty($data['time_limit']))
                                                    <strong>Time:</strong> {{ $data['time_limit'] }} minutes<br>
                                                @endif
                                                @if(!empty($data['question_count']))
                                                    <strong>Questions:</strong> {{ $data['question_count'] }}
                                                @endif
                                                @break
                                            @case('test')
                                                @if(!empty($data['test_title']))
                                                    <strong>Title:</strong> {{ $data['test_title'] }}<br>
                                                @endif
                                                @if(!empty($data['test_date']))
                                                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($data['test_date'])->format('M d, Y g:i A') }}<br>
                                                @endif
                                                @if(!empty($data['total_marks']))
                                                    <strong>Total Marks:</strong> {{ $data['total_marks'] }}
                                                @endif
                                                @break
                                            @case('link')
                                                @if(!empty($data['link_title']))
                                                    <strong>Link:</strong> {{ $data['link_title'] }}<br>
                                                @endif
                                                @if(!empty($data['external_url']))
                                                    <a href="{{ $data['external_url'] }}" target="_blank" class="download-link">
                                                        🔗 Open Link
                                                    </a>
                                                @endif
                                                @break
                                        @endswitch
                                    </div>
                                @endif

                                @if($module->attachment)
                                    <a href="{{ asset('storage/'.$module->attachment) }}" target="_blank" class="download-link">
                                        📎 Download file
                                    </a>
                                @endif
                            </div>
                            
                            <div class="module-actions">
                                <button class="archive-btn" onclick="toggleArchiveModule({{ $module->modules_id }}, false)">
                                    Archive
                                </button>
                                <button class="edit-module-btn" onclick="editModule({{ $module->modules_id }}, '{{ $module->module_name }}', '{{ addslashes($module->module_description ?? '') }}', {{ $module->program_id }}, '{{ $module->attachment }}')">
                                    Edit
                                </button>
                                <button class="preview-btn" onclick="previewModule({{ $module->modules_id }})">
                                    Preview
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-modules">
                    No learning content found for this program.<br>
                    <small>Click "Add Content" to create your first module.</small>
                </div>
            @endif
        @else
            <div class="select-program-msg">
                <img src="{{ asset('images/lms-illustration.png') }}" alt="LMS Illustration" style="max-width: 200px; margin-bottom: 20px;">
                <h3>Welcome to the Learning Management System</h3>
                <p>Please select a program above to manage its learning content.</p>
                <ul class="lms-features">
                    <li>📚 Create and organize educational modules</li>
                    <li>📝 Add quizzes, assignments, and tests</li>
                    <li>🔄 Drag and drop to reorder content</li>
                    <li>🔍 Preview content as students will see it</li>
                </ul>
            </div>
        @endif
    </div>
</div>

<!-- Add Module Modal -->
<div class="modal-bg" id="addModalBg">
    <div class="modal">
        <h3>Add New Content</h3>
        <form action="{{ route('admin.modules.store') }}"
              method="POST"
              enctype="multipart/form-data"
              id="addModuleForm">
            @csrf
            <select name="program_id" id="modalProgramSelect" required>
                <option value="">-- Select Program --</option>
                @foreach($programs as $program)
                    <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                @endforeach
            </select>

            <input type="text" name="module_name" placeholder="Content Name" required>
            <textarea name="module_description" placeholder="Content Description (optional)"></textarea>

            <select name="content_type" id="addContentType" required>
                <option value="">-- Select Content Type --</option>
                <option value="module">📚 Module/Lesson</option>
                <option value="assignment">📝 Assignment</option>
                <option value="quiz">❓ Quiz</option>
                <option value="test">📋 Test</option>
                <option value="link">🔗 External Link</option>
                <option value="file">📎 File Upload</option>
            </select>

            <div class="content-specific-fields" id="addContentFields">
                <!-- Dynamic fields will be added here based on content type -->
            </div>

            <!-- Assignment specific fields -->
            <div class="content-type-fields" id="assignmentFields" style="display: none;">
                <input type="text" name="assignment_title" placeholder="Assignment Title">
                <textarea name="assignment_instructions" placeholder="Assignment Instructions"></textarea>
                <input type="datetime-local" name="due_date" placeholder="Due Date">
                <input type="number" name="max_points" placeholder="Maximum Points" min="1">
            </div>

            <!-- Quiz specific fields -->
            <div class="content-type-fields" id="quizFields" style="display: none;">
                <input type="text" name="quiz_title" placeholder="Quiz Title">
                <textarea name="quiz_description" placeholder="Quiz Description"></textarea>
                <input type="number" name="time_limit" placeholder="Time Limit (minutes)" min="1">
                <input type="number" name="question_count" placeholder="Number of Questions" min="1">
            </div>

            <!-- Test specific fields -->
            <div class="content-type-fields" id="testFields" style="display: none;">
                <input type="text" name="test_title" placeholder="Test Title">
                <textarea name="test_description" placeholder="Test Description"></textarea>
                <input type="datetime-local" name="test_date" placeholder="Test Date">
                <input type="number" name="duration" placeholder="Duration (minutes)" min="1">
                <input type="number" name="total_marks" placeholder="Total Marks" min="1">
            </div>

            <!-- External Link specific fields -->
            <div class="content-type-fields" id="linkFields" style="display: none;">
                <input type="text" name="link_title" placeholder="Link Title">
                <input type="url" name="external_url" placeholder="External URL (https://...)">
                <textarea name="link_description" placeholder="Link Description"></textarea>
                <select name="link_type">
                    <option value="video">📹 Video</option>
                    <option value="article">📄 Article</option>
                    <option value="website">🌐 Website</option>
                    <option value="tool">🔧 Tool</option>
                    <option value="other">📂 Other</option>
                </select>
            </div>

            <div class="dropzone" id="addDropzone">
                <p>📁 Drop files here or click to browse</p>
                <small>Supported formats: PDF, DOC, DOCX, ZIP, PNG, JPG, JPEG (Max 10MB)</small>
                <input type="file"
                       name="attachment"
                       id="addAttachment"
                       accept=".pdf,.doc,.docx,.zip,.png,.jpg,.jpeg">
            </div>

            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="cancelAddModal">Cancel</button>
                <button type="submit" class="add-btn" id="submitAddContent">Add Content</button>
            </div>
        </form>
    </div>
</div>

<!-- Batch Upload Modal -->
<div class="modal-bg" id="batchModalBg">
    <div class="modal" style="max-width: 800px; width: 90vw; max-height: 90vh; overflow-y: auto;">
        <h3>Batch Upload Modules (XML Files)</h3>
        <form action="{{ route('admin.modules.batch-store') }}"
              method="POST"
              enctype="multipart/form-data"
              id="batchModuleForm">
            @csrf
            
            <select name="program_id" id="batchModalProgramSelect" required style="margin-bottom: 20px;">
                <option value="">-- Select Program --</option>
                @foreach($programs as $program)
                    <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                @endforeach
            </select>

            <div class="dropzone" id="batchDropzone" style="margin: 20px 0; min-height: 120px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <p style="text-align: center; margin: 0;">
                    📁 Drop XML files here or click to browse<br>
                    <small style="color: #666;">Select multiple XML files. Each file will become a separate module with its content parsed automatically.</small>
                </p>
                <input type="file"
                       name="xml_files[]"
                       id="batchXmlFiles"
                       multiple
                       accept=".xml"
                       style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
            </div>

            <div id="selectedFiles" style="display: none; margin: 15px 0;">
                <h4 style="color: #667eea; margin-bottom: 10px;">Selected Files:</h4>
                <ul id="fileList" style="list-style: none; padding: 0;"></ul>
            </div>

            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="cancelBatchModal">Cancel</button>
                <button type="submit" class="add-btn" id="uploadXmlBtn" disabled>Upload XML Files</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Module Modal -->
<div class="modal-bg" id="editModalBg">
    <div class="modal">
        <h3>Edit Module</h3>
        <form action="" 
              method="POST"
              enctype="multipart/form-data"
              id="editModuleForm">
            @csrf
            @method('PUT')

            <select name="program_id" id="editModalProgramSelect" required>
                <option value="">-- Select Program --</option>
                @foreach($programs as $program)
                    <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                @endforeach
            </select>

            <input type="text" name="module_name" id="editModuleName" placeholder="Module Name" required>
            <textarea name="module_description" id="editModuleDescription" placeholder="Module Description (optional)"></textarea>

            <div class="dropzone" id="editDropzone">
                <p>Drop PDF/DOC here or click to browse</p>
                <input type="file"
                       name="attachment"
                       id="editAttachment"
                       accept=".pdf,.doc,.docx">
            </div>

            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="cancelEditModal">Cancel</button>
                <button type="submit" class="update-btn">Update Module</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin modules page loaded');
    
    // Program selector
    const programSelect = document.getElementById('programSelect');
    if (programSelect) {
        programSelect.addEventListener('change', function() {
            const pid = this.value;
            window.location.href = pid 
                ? `{{ route('admin.modules.index') }}?program_id=${pid}`
                : `{{ route('admin.modules.index') }}`;
        });
    }

    // Add Content modal functionality
    const showAddModal = document.getElementById('showAddModal');
    const addModalBg = document.getElementById('addModalBg');
    const cancelAdd = document.getElementById('cancelAddModal');
    
    // Initialize the Add Content modal
    if (showAddModal) {
        showAddModal.addEventListener('click', function(e) {
            e.preventDefault();
            showAddContentModal();
        });
    }
    
    // Close modal when cancel button is clicked
    if (cancelAdd && addModalBg) {
        cancelAdd.addEventListener('click', function() {
            addModalBg.classList.remove('show');
            addModalBg.style.display = 'none';
        });
    }
    
    // Close modal when clicking outside the modal content
    if (addModalBg) {
        addModalBg.addEventListener('click', function(e) {
            if (e.target === addModalBg) {
                addModalBg.classList.remove('show');
                addModalBg.style.display = 'none';
            }
        });
    }
    
    // Handle content type selection to show/hide specific fields
    const contentTypeSelect = document.getElementById('addContentType');
    if (contentTypeSelect) {
        contentTypeSelect.addEventListener('change', function() {
            // Hide all content type specific fields
            const allFields = document.querySelectorAll('.content-type-fields');
            allFields.forEach(field => {
                field.style.display = 'none';
            });
            
            // Remove required attribute from all conditional fields
            const conditionalInputs = document.querySelectorAll('#linkFields input[name="external_url"]');
            conditionalInputs.forEach(input => {
                input.removeAttribute('required');
            });
            
            // Show relevant fields based on selection
            const selectedType = this.value;
            switch (selectedType) {
                case 'assignment':
                    document.getElementById('assignmentFields').style.display = 'block';
                    break;
                case 'quiz':
                    document.getElementById('quizFields').style.display = 'block';
                    break;
                case 'test':
                    document.getElementById('testFields').style.display = 'block';
                    break;
                case 'link':
                    document.getElementById('linkFields').style.display = 'block';
                    // Make URL required only for link type
                    const urlInput = document.querySelector('#linkFields input[name="external_url"]');
                    if (urlInput) {
                        urlInput.setAttribute('required', 'required');
                    }
                    break;
                default:
                    // For 'module' and 'file' types, no additional fields needed
                    break;
            }
        });
    }
    
    // Make modules sortable
    const sortableEl = document.getElementById('sortableModules');
    if (sortableEl) {
        const sortable = new Sortable(sortableEl, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'module-card-ghost',
            onStart: function() {
                document.getElementById('saveModuleOrder').style.display = 'block';
            }
        });
        
        // Save module order button
        const saveOrderBtn = document.getElementById('saveModuleOrder');
        if (saveOrderBtn) {
            saveOrderBtn.addEventListener('click', function() {
                const moduleIds = Array.from(sortableEl.querySelectorAll('.module-card')).map(card => {
                    return parseInt(card.getAttribute('data-id'));
                });
                
                fetch('{{ route("admin.modules.updateOrder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ moduleIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        saveOrderBtn.textContent = 'Order Saved!';
                        setTimeout(() => {
                            saveOrderBtn.textContent = 'Save Order';
                            saveOrderBtn.style.display = 'none';
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Error saving order:', error);
                    alert('Error saving module order.');
                });
            });
        }
    }
    
    // Module filtering functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    if (filterButtons.length) {
        filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                const filter = this.getAttribute('data-filter');
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
    
    // Module preview functionality
    window.previewModule = function(moduleId) {
        fetch(`/admin/modules/${moduleId}/preview`)
            .then(response => response.json())
            .then(data => {
                showPreviewModal(data);
            })
            .catch(error => {
                console.error('Error fetching module preview:', error);
                alert('Error loading module preview.');
            });
    };
    
    function showPreviewModal(moduleData) {
        // Create modal if it doesn't exist
        let previewModal = document.getElementById('previewModal');
        if (!previewModal) {
            previewModal = document.createElement('div');
            previewModal.id = 'previewModal';
            previewModal.className = 'modal-bg';
            previewModal.innerHTML = `
                <div class="modal" style="max-width: 800px;">
                    <div class="preview-header">
                        <h2 class="preview-title">Module Preview</h2>
                        <p class="preview-subtitle">This is how students will see this content</p>
                    </div>
                    <div class="preview-tabs">
                        <button class="preview-tab active" data-tab="student">Student View</button>
                        <button class="preview-tab" data-tab="details">Module Details</button>
                    </div>
                    <div class="preview-content">
                        <div class="preview-tab-content" id="student-view"></div>
                        <div class="preview-tab-content" id="details-view" style="display:none;"></div>
                    </div>
                    <div class="modal-actions">
                        <button class="cancel-btn" id="closePreview">Close</button>
                    </div>
                </div>
            `;
            document.body.appendChild(previewModal);
            
            // Set up tab switching
            const tabs = previewModal.querySelectorAll('.preview-tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    const tabId = this.getAttribute('data-tab');
                    const tabContents = previewModal.querySelectorAll('.preview-tab-content');
                    
                    tabContents.forEach(content => {
                        content.style.display = 'none';
                    });
                    
                    if (tabId === 'student') {
                        document.getElementById('student-view').style.display = 'block';
                    } else {
                        document.getElementById('details-view').style.display = 'block';
                    }
                });
            });
            
            // Set up close button
            const closeBtn = document.getElementById('closePreview');
            closeBtn.addEventListener('click', function() {
                previewModal.classList.remove('show');
            });
            
            // Close when clicking outside
            previewModal.addEventListener('click', function(e) {
                if (e.target === previewModal) {
                    previewModal.classList.remove('show');
                }
            });
        }
        
        // Update modal content with module data
        const titleEl = previewModal.querySelector('.preview-title');
        titleEl.textContent = moduleData.module_name;
        
        // Student view tab
        const studentView = document.getElementById('student-view');
        studentView.innerHTML = `
            <div class="student-module-view">
                <h3>${moduleData.module_name}</h3>
                <div class="module-description">${moduleData.module_description || 'No description available.'}</div>
                
                ${moduleData.content_type === 'module' ? `
                    <div class="module-content-preview">
                        ${moduleData.attachment ? `
                            <div class="attachment-preview">
                                <a href="/storage/${moduleData.attachment}" target="_blank" class="download-link">
                                    📎 Download Learning Material
                                </a>
                            </div>
                        ` : ''}
                    </div>
                ` : ''}
                
                ${moduleData.content_type === 'assignment' ? `
                    <div class="assignment-preview">
                        <h4>${moduleData.content_data?.assignment_title || 'Assignment'}</h4>
                        <div class="assignment-instructions">
                            ${moduleData.content_data?.assignment_instructions || 'No instructions provided.'}
                        </div>
                        <div class="assignment-meta">
                            ${moduleData.content_data?.due_date ? `<p><strong>Due:</strong> ${moduleData.content_data.due_date}</p>` : ''}
                            ${moduleData.content_data?.max_points ? `<p><strong>Points:</strong> ${moduleData.content_data.max_points}</p>` : ''}
                        </div>
                        <div class="assignment-submit">
                            <button disabled>Submit Assignment</button>
                        </div>
                    </div>
                ` : ''}
                
                ${moduleData.content_type === 'quiz' || moduleData.content_type === 'test' ? `
                    <div class="quiz-preview">
                        <h4>${moduleData.content_data?.quiz_title || moduleData.content_data?.test_title || 'Assessment'}</h4>
                        <div class="quiz-instructions">
                            ${moduleData.content_data?.quiz_description || moduleData.content_data?.test_description || 'No description provided.'}
                        </div>
                        <div class="quiz-meta">
                            ${moduleData.content_data?.time_limit ? `<p><strong>Time Limit:</strong> ${moduleData.content_data.time_limit} minutes</p>` : ''}
                            ${moduleData.content_data?.question_count ? `<p><strong>Questions:</strong> ${moduleData.content_data.question_count}</p>` : ''}
                            ${moduleData.content_data?.total_marks ? `<p><strong>Total Marks:</strong> ${moduleData.content_data.total_marks}</p>` : ''}
                        </div>
                        <div class="quiz-start">
                            <button disabled>Start ${moduleData.content_type === 'quiz' ? 'Quiz' : 'Test'}</button>
                        </div>
                    </div>
                ` : ''}
                
                ${moduleData.content_type === 'link' ? `
                    <div class="link-preview">
                        <h4>${moduleData.content_data?.link_title || 'External Resource'}</h4>
                        <p>${moduleData.content_data?.link_description || 'No description provided.'}</p>
                        <a href="${moduleData.content_data?.external_url || '#'}" target="_blank" class="external-link">
                            🔗 Visit External Resource
                        </a>
                    </div>
                ` : ''}
            </div>
        `;
        
        // Details view tab
        const detailsView = document.getElementById('details-view');
        detailsView.innerHTML = `
            <div class="module-details">
                <table class="details-table">
                    <tr>
                        <th>ID:</th>
                        <td>${moduleData.modules_id}</td>
                    </tr>
                    <tr>
                        <th>Name:</th>
                        <td>${moduleData.module_name}</td>
                    </tr>
                    <tr>
                        <th>Description:</th>
                        <td>${moduleData.module_description || 'No description'}</td>
                    </tr>
                    <tr>
                        <th>Content Type:</th>
                        <td><span class="content-type-badge ${moduleData.content_type}">${moduleData.content_type}</span></td>
                    </tr>
                    <tr>
                        <th>Program:</th>
                        <td>${moduleData.program?.program_name || 'Unknown program'}</td>
                    </tr>
                    <tr>
                        <th>Created:</th>
                        <td>${new Date(moduleData.created_at).toLocaleString()}</td>
                    </tr>
                    <tr>
                        <th>Last Updated:</th>
                        <td>${new Date(moduleData.updated_at).toLocaleString()}</td>
                    </tr>
                    <tr>
                        <th>Order:</th>
                        <td>${moduleData.module_order || 'Not set'}</td>
                    </tr>
                    <tr>
                        <th>Has Attachment:</th>
                        <td>${moduleData.attachment ? 'Yes' : 'No'}</td>
                    </tr>
                </table>
            </div>
        `;
        
        // Show the modal
        previewModal.classList.add('show');
    }

    // Function to show Add Content modal
    window.showAddContentModal = function() {
        console.log('showAddContentModal called');
        
        const programSelect = document.getElementById('programSelect');
        const addModalBg = document.getElementById('addModalBg');
        
        if (!programSelect || !addModalBg) {
            console.error('Required elements not found');
            return;
        }
        
        const currentProgramId = programSelect.value;
        
        // Check if a program is selected
        if (!currentProgramId) {
            alert('Please select a program first before adding content.');
            return;
        }
        
        // Check if program is archived
        const selectedOption = programSelect.options[programSelect.selectedIndex];
        if (selectedOption.text.includes('(Archived)')) {
            alert('Cannot add content to archived programs.');
            return;
        }
        
        // Set the program in the modal form
        const modalProgramSelect = document.getElementById('modalProgramSelect');
        if (modalProgramSelect) {
            modalProgramSelect.value = currentProgramId;
        }
        
        // Reset form fields
        document.getElementById('addModuleForm').reset();
        modalProgramSelect.value = currentProgramId;
        
        // Hide all content type specific fields
        const allFields = document.querySelectorAll('.content-type-fields');
        allFields.forEach(field => {
            field.style.display = 'none';
        });
        
        // Show the modal
        addModalBg.classList.add('show');
        addModalBg.style.display = 'flex';
        
        console.log('Modal should now be visible');
    };
    
    // Handle file drop functionality
    const dropzone = document.getElementById('addDropzone');
    const fileInput = document.getElementById('addAttachment');
    
    if (dropzone && fileInput) {
        dropzone.addEventListener('click', function() {
            fileInput.click();
        });
        
        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropzone.style.borderColor = '#3498db';
            dropzone.style.background = '#f0f8ff';
        });
        
        dropzone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            dropzone.style.borderColor = '#ddd';
            dropzone.style.background = '#fafafa';
        });
        
        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropzone.style.borderColor = '#ddd';
            dropzone.style.background = '#fafafa';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                updateDropzoneText(files[0].name);
            }
        });
        
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                updateDropzoneText(this.files[0].name);
            }
        });
    }
    
    function updateDropzoneText(fileName) {
        const dropzoneText = dropzone.querySelector('p');
        if (dropzoneText) {
            dropzoneText.textContent = `Selected: ${fileName}`;
        }
    }
    
    // Form validation for Add Content
    const addForm = document.getElementById('addModuleForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            console.log('Form submit event triggered');
            
            const moduleName = document.querySelector('input[name="module_name"]').value.trim();
            const contentType = document.querySelector('select[name="content_type"]').value;
            const programId = document.querySelector('select[name="program_id"]').value;
            
            console.log('Form data:', { moduleName, contentType, programId });
            
            if (!moduleName) {
                console.log('Validation failed: No module name');
                e.preventDefault();
                alert('Please enter a content name.');
                return false;
            }
            
            if (!contentType) {
                console.log('Validation failed: No content type');
                e.preventDefault();
                alert('Please select a content type.');
                return false;
            }
            
            if (!programId) {
                console.log('Validation failed: No program ID');
                e.preventDefault();
                alert('Please select a program.');
                return false;
            }
            
            // Additional validation for external links
            if (contentType === 'link') {
                const externalUrl = document.querySelector('input[name="external_url"]').value.trim();
                if (!externalUrl) {
                    console.log('Validation failed: No external URL for link');
                    e.preventDefault();
                    alert('Please enter an external URL for link content.');
                    return false;
                }
                
                // Basic URL validation
                try {
                    new URL(externalUrl);
                } catch {
                    console.log('Validation failed: Invalid URL');
                    e.preventDefault();
                    alert('Please enter a valid URL (including http:// or https://).');
                    return false;
                }
            }
            
            console.log('Form validation passed, submitting...');
            
            // Show loading state
            const submitBtn = document.getElementById('submitAddContent');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Adding...';
            }
            
            return true;
        });
    } else {
        console.error('Form element not found!');
    }
    
    // Debug: Add click handler to submit button
    const submitBtn = document.getElementById('submitAddContent');
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            console.log('Submit button clicked');
            console.log('Form element:', document.getElementById('addModuleForm'));
            const form = document.getElementById('addModuleForm');
            if (form) {
                console.log('Form action:', form.action);
                console.log('Form method:', form.method);
                console.log('Form CSRF token:', form.querySelector('input[name="_token"]')?.value);
            }
        });
    }
    
    // Alternative form submission method in case normal submission fails
    window.submitAddContentForm = function() {
        console.log('Alternative form submission triggered');
        const form = document.getElementById('addModuleForm');
        if (form) {
            // Manually trigger form validation
            if (form.checkValidity()) {
                console.log('Form is valid, submitting...');
                form.submit();
            } else {
                console.log('Form validation failed');
                form.reportValidity();
            }
        }
    };
    
    // Debug function - commented out since feature is working
    /*
    window.testFormSubmission = function() {
        console.log('Test form submission triggered');
        const form = document.getElementById('addModuleForm');
        if (form) {
            // Clone the form data
            const formData = new FormData(form);
            
            // Log form data
            console.log('Form data being sent:');
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }
            
            fetch('/admin/modules', { // Changed to a safe route that exists
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Test submission response:', data);
                alert('Test submission successful! Check console for details.');
            })
            .catch(error => {
                console.error('Test submission error:', error);
                console.error('Error details:', error.message);
                alert(`Test submission failed! Error: ${error.message}. Check console for details.`);
            });
        } else {
            console.error('Form not found!');
            alert('Form not found!');
        }
    };
    */
    
    // Complete debugging function
    window.debugFormState = function() {
        console.log('=== FORM DEBUG START ===');
        
        const form = document.getElementById('addModuleForm');
        console.log('Form element:', form);
        
        if (form) {
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);
            console.log('Form enctype:', form.enctype);
            
            const inputs = form.querySelectorAll('input, select, textarea');
            console.log('Form inputs:', inputs.length);
            
            inputs.forEach((input, index) => {
                console.log(`Input ${index}:`, {
                    name: input.name,
                    type: input.type,
                    value: input.value,
                    required: input.required,
                    valid: input.checkValidity()
                });
            });
            
            const csrfToken = form.querySelector('input[name="_token"]');
            console.log('CSRF Token:', csrfToken ? csrfToken.value : 'NOT FOUND');
            
            console.log('Form validity:', form.checkValidity());
            
            // Test simple fetch to check server connection
            fetch('/admin/modules', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Server connection test - Status:', response.status);
                console.log('Server connection test - OK:', response.ok);
            })
            .catch(error => {
                console.error('Server connection test failed:', error);
            });
        }
        
        console.log('Current URL:', window.location.href);
        console.log('Base URL:', window.location.origin);
        console.log('=== FORM DEBUG END ===');
    };
    
    // Test actual form route
    window.testActualSubmission = function() {
        console.log('Testing actual form submission route');
        const form = document.getElementById('addModuleForm');
        if (form) {
            const formData = new FormData(form);
            
            fetch('{{ route("admin.modules.store") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Actual route response status:', response.status);
                console.log('Actual route response ok:', response.ok);
                
                if (response.redirected) {
                    console.log('Response was redirected to:', response.url);
                    alert('Form submission worked! Response was redirected (likely success).');
                } else if (response.ok) {
                    return response.text();
                } else {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
            })
            .then(data => {
                if (data) {
                    console.log('Response data:', data);
                    alert('Form submission completed. Check console for details.');
                }
            })
            .catch(error => {
                console.error('Actual route error:', error);
                alert(`Actual route test failed: ${error.message}`);
            });
        }
    };
});
</script>
@endpush

@section('head')
<style>
    /* Specific fixes for the Add Content modal to ensure it displays correctly */
    #addModalBg {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999 !important;
        align-items: center;
        justify-content: center;
        overflow-y: auto;
    }
    
    #addModalBg.show {
        display: flex !important;
    }
    
    #addModalBg .modal {
        position: relative !important;
        display: block !important;
        background-color: white;
        padding: 30px;
        border-radius: 15px;
        max-width: 500px;
        width: 90%;
        margin: 1.75rem auto !important;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        z-index: 10000 !important;
        transform: none !important;
        opacity: 1 !important;
        transition: transform 0.3s ease-out !important;
        pointer-events: auto !important;
    }
</style>
@endsection
