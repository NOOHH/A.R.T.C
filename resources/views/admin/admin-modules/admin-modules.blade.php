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

  /* Module cards with Bootstrap integration */
  .module-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .module-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
  }

  .module-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15) !important;
  }

  .module-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2c3e50;
  }

  .module-title::before {
    content: '📚';
    margin-right: 0.5rem;
  }

  .btn-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    transition: all 0.3s ease;
  }

  .btn-gradient-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    color: white;
  }

  .btn-gradient-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    border: none;
    color: white;
    transition: all 0.3s ease;
  }

  .btn-gradient-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    color: white;
  }

  .btn-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    color: white;
  }

  .btn-gradient-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    color: white;
  }

  .floating-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 100;
    border-radius: 50px !important;
    padding: 15px 30px;
    font-size: 1.1rem;
    font-weight: 600;
  }

  /* Modal enhancements */
  .modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
  }

  .modal-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #e1e5e9;
    border-radius: 15px 15px 0 0;
  }

  .modal-title {
    color: #2c3e50;
    font-weight: 700;
  }

  .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
  }

  /* Batch upload specific styles */
  .batch-module-item {
    background: #f8f9fa;
    border: 2px solid #e1e5e9;
    transition: border-color 0.3s ease;
  }

  .batch-module-item:hover {
    border-color: #667eea;
  }

  .remove-module-btn {
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    font-size: 18px;
    line-height: 1;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .remove-module-btn:hover {
    background: #c82333;
    transform: scale(1.1);
  }

  /* Empty state */
  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
  }

  .empty-state::before {
    content: '📚';
    display: block;
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
  }

  /* Analytics integration */
  .analytics-cards {
    margin-bottom: 2rem;
  }

  .analytics-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    color: white;
    position: relative;
    overflow: hidden;
  }

  .analytics-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    transform: translate(30px, -30px);
  }

  .analytics-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.2);
  }

  .analytics-card.modules {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  }

  .analytics-card.active {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  }

  .analytics-card.archived {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  }

  .analytics-card.recent {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
  }

  .card-number {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 2;
  }

  .card-label {
    font-size: 1rem;
    opacity: 0.9;
    margin-bottom: 0.75rem;
    font-weight: 600;
    position: relative;
    z-index: 2;
  }

  .card-trend {
    font-size: 0.85rem;
    opacity: 0.8;
    position: relative;
    z-index: 2;
  }

    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #667eea;
  }
  .program-selector label {
    display: block;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
    font-size: 1.1rem;
  }
  .program-selector select {
    width: 100%;
    max-width: 400px;
    padding: 12px 15px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 1rem;
    background: white;
    transition: border-color 0.3s ease;
  }
  .program-selector select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  /* Modules grid */
  .modules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
  }

  /* Module card */
  .module-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 15px;
    padding: 25px;
    transition: all 0.3s ease;
    border: 1px solid #e1e5e9;
    position: relative;
    overflow: hidden;
  }
  .module-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
  }
  .module-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
  }

  .module-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .module-title::before {
    content: '📚';
    font-size: 1.2rem;
  }

  .module-description {
    color: #6c757d;
    margin-bottom: 15px;
    line-height: 1.5;
    font-size: 0.95rem;
  }

  .download-link {
    display: inline-block;
    color: #667eea;
    text-decoration: none;
    font-size: 0.9rem;
    margin: 8px 0;
    padding: 4px 8px;
    border: 1px solid #667eea;
    border-radius: 4px;
    transition: all 0.3s ease;
  }

  .download-link:hover {
    background-color: #667eea;
    color: white;
    text-decoration: none;
  }

  .module-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
  }

  .module-program {
    background: #667eea;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
  }

  .module-actions {
    display: flex;
    gap: 8px;
  }

  .edit-module-btn, .delete-module-btn {
    padding: 8px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .edit-module-btn {
    background: #28a745;
    color: white;
  }
  .edit-module-btn:hover {
    background: #218838;
    transform: scale(1.05);
  }

  .delete-module-btn {
    background: #dc3545;
    color: white;
  }
  .delete-module-btn:hover {
    background: #c82333;
    transform: scale(1.05);
  }

  /* Add button */
  .add-module-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .add-module-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
  }

  .batch-upload-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
  }
  .batch-upload-btn:hover {
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
  }

  /* Batch actions and selection styles */


  .archive-btn {
    background: #6c757d;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .archive-btn:hover {
    background: #5a6268;
    transform: scale(1.05);
  }

  .archive-btn.unarchive {
    background: #28a745;
  }

  .archive-btn.unarchive:hover {
    background: #218838;
  }

  .content-type-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-left: 8px;
  }

  .content-type-badge.module { background: #e7f3ff; color: #0066cc; }
  .content-type-badge.assignment { background: #fff3e0; color: #ff9800; }
  .content-type-badge.quiz { background: #f3e5f5; color: #9c27b0; }
  .content-type-badge.test { background: #ffebee; color: #f44336; }
  .content-type-badge.link { background: #e8f5e8; color: #4caf50; }

  .content-specific-fields {
    margin: 15px 0;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 3px solid #667eea;
  }

  .view-archived-btn {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 25px;
    cursor: pointer;
  }
  
  #previewModal .preview-tab.active {
    background: white;
    font-weight: 600;
  }
  
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
    max-width: 600px;
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
        <h1>Modules</h1>
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
        <label for="programSelect">Select Program to View/Manage Modules:</label>
        <select id="programSelect" name="program_id">
            <option value="">-- Select a Program --</option>
            @foreach($programs as $program)
                <option value="{{ $program->program_id }}"
                    {{ request('program_id') == $program->program_id ? 'selected' : '' }}>
                    {{ $program->program_name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Modules Display Area -->
    <div id="modulesDisplayArea">
        @if(request('program_id') && isset($modules))
            @if($modules->count() > 0)
                <div class="modules-grid">
                    @foreach($modules as $module)
                        <div class="module-card">
                            <div class="module-title">
                                {{ $module->content_type_icon ?? '📚' }} {{ $module->module_name }}
                                <span class="content-type-badge {{ $module->content_type ?? 'module' }}">
                                    {{ $module->content_type_display ?? 'Module/Lesson' }}
                                </span>
                            </div>
                            
                            <div class="module-description">
                                {{ $module->module_description }}
                            </div>

                            @if($module->content_data)
                                <div class="content-details" style="margin: 10px 0; font-size: 0.9rem; color: #666;">
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
                                <p><a href="{{ asset('storage/'.$module->attachment) }}" target="_blank" class="download-link">
                                    📎 Download file
                                </a></p>
                            @endif
                            
                            <div class="module-meta">
                                <span class="module-program">
                                    {{ $module->program->program_name }}
                                </span>
                                <div class="module-actions">
                                    <button class="archive-btn" 
                                            onclick="toggleArchiveModule({{ $module->modules_id }}, false)">
                                        Archive
                                    </button>
                                    <button class="edit-module-btn" onclick="editModule({{ $module->modules_id }}, '{{ $module->module_name }}', '{{ addslashes($module->module_description ?? '') }}', {{ $module->program_id }}, '{{ $module->attachment }}')">
                                        Edit
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-modules">
                    No modules found for this program.<br>
                    <small>Click "Add Module" to create the first module.</small>
                </div>
            @endif
        @else
            <div class="select-program-msg">
                Please select a program above to view and manage its modules.
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
        showAddModal.addEventListener('click', function() {
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
    });

    // Content type change handlers
    const addContentType = document.getElementById('addContentType');
    if (addContentType) {
        addContentType.addEventListener('change', function() {
            updateContentFields(0, this.value, true);
        });
    }

    // XML Batch Upload Functionality
    const batchXmlFiles = document.getElementById('batchXmlFiles');
    const batchDropzone = document.getElementById('batchDropzone');
    const selectedFiles = document.getElementById('selectedFiles');
    const fileList = document.getElementById('fileList');
    const uploadXmlBtn = document.getElementById('uploadXmlBtn');

    if (batchXmlFiles && batchDropzone) {
        // Handle file selection
        batchXmlFiles.addEventListener('change', function(e) {
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
            batchXmlFiles.files = files; // Update the input's files
        });
    }

    // Function to show Add Content modal
    window.showAddContentModal = function() {
        console.log('showAddContentModal called');
        
        if (!programSelect || !addModalBg) {
            console.error('Required elements not found');
            return;
        }

        // Filter only XML files
        const xmlFiles = Array.from(files).filter(file => 
            file.name.toLowerCase().endsWith('.xml')
        );

        if (xmlFiles.length === 0) {
            alert('Please select only XML files.');
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
        
        // Ensure the modal is visible with explicit styles
        const modalElement = addModalBg.querySelector('.modal');
        if (modalElement) {
            modalElement.style.display = 'block';
            modalElement.style.opacity = '1';
            modalElement.style.zIndex = '10000';
        }
        
        // Show the modal background
        addModalBg.classList.add('show');
        addModalBg.style.display = 'flex';
        
        console.log('Modal should now be visible');
    };
});
</script>
@endpush
