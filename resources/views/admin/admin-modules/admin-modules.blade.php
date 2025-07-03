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
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
  }

  .view-archived-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    color: white;
    text-decoration: none;
  }

  /* Modal styles */
  .modal-bg {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
  }
  .modal-bg.show {
    display: flex;
  }

  .modal {
    background: white;
    padding: 30px;
    border-radius: 15px;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: modalSlideIn 0.3s ease;
  }

  @keyframes modalSlideIn {
    from {
      opacity: 0;
      transform: translateY(-50px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .modal h3 {
    color: #2c3e50;
    margin: 0 0 20px 0;
    font-size: 1.5rem;
    text-align: center;
  }

  .modal input, .modal select, .modal textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
    box-sizing: border-box;
  }

  .modal input:focus, .modal select:focus, .modal textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  .modal textarea {
    resize: vertical;
    min-height: 80px;
  }

  .modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 20px;
  }

  .cancel-btn, .add-btn, .update-btn {
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 1rem;
  }

  .cancel-btn {
    background: #6c757d;
    color: white;
  }
  .cancel-btn:hover {
    background: #5a6268;
  }

  .add-btn, .update-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
  }
  .add-btn:hover, .update-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }

  /* Drag-and-drop zone */
  .dropzone {
    border: 2px dashed #667eea;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    color: #6c757d;
    cursor: pointer;
    transition: background 0.2s ease;
    position: relative;
    margin-bottom: 15px;
  }
  .dropzone.dragover {
    background: rgba(102, 126, 234, 0.1);
  }
  .dropzone p {
    margin: 0;
    font-size: 1rem;
  }
  .dropzone input[type="file"] {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    width: 100%; height: 100%;
    opacity: 0;
    cursor: pointer;
  }

  /* Empty state */
  .no-modules {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    font-size: 1.1rem;
  }
  .no-modules::before {
    content: '📚';
    display: block;
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
  }

  /* Program not selected state */
  .select-program-msg {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    font-size: 1.1rem;
    background: #f8f9fa;
    border-radius: 10px;
    border: 2px dashed #dee2e6;
  }
  .select-program-msg::before {
    content: '👆';
    display: block;
    font-size: 3rem;
    margin-bottom: 15px;
  }

  /* Batch upload styles */
  .batch-module-item {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    position: relative;
  }

  .batch-module-item h4 {
    margin: 0 0 15px 0;
    color: #2c3e50;
    font-size: 1.1rem;
  }

  .remove-module-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.3s ease;
  }
  .remove-module-btn:hover {
    background: #c82333;
    transform: scale(1.1);
  }

  .add-another-btn {
    background: #28a745;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    margin-bottom: 20px;
    transition: all 0.3s ease;
  }
  .add-another-btn:hover {
    background: #218838;
    transform: translateY(-1px);
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
                <option value="module">Module/Lesson</option>
                <option value="assignment">Assignment</option>
                <option value="quiz">Quiz</option>
                <option value="test">Test</option>
                <option value="link">External Link</option>
            </select>

            <div class="content-specific-fields" id="addContentFields">
                <!-- Dynamic fields will be added here based on content type -->
            </div>

            <div class="dropzone" id="addDropzone">
                <p>Drop files here or click to browse</p>
                <input type="file"
                       name="attachment"
                       id="addAttachment"
                       accept=".pdf,.doc,.docx,.zip,.png,.jpg,.jpeg">
            </div>

            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="cancelAddModal">Cancel</button>
                <button type="submit" class="add-btn">Add Content</button>
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
// Global edit function called from edit buttons
function editModule(moduleId, moduleName, moduleDescription, programId, attachment) {
    console.log('Editing module:', moduleId, moduleName, moduleDescription, programId);
    
    const editModalBg = document.getElementById('editModalBg');
    const editForm = document.getElementById('editModuleForm');
    const editProgramSelect = document.getElementById('editModalProgramSelect');
    const editModuleName = document.getElementById('editModuleName');
    const editModuleDescription = document.getElementById('editModuleDescription');
    
    editForm.action = `/admin/modules/${moduleId}`;
    editProgramSelect.value = programId;
    editModuleName.value = moduleName;
    editModuleDescription.value = moduleDescription || '';
    
    editModalBg.classList.add('show');
}

// Archive/Unarchive module function
function toggleArchiveModule(moduleId, isArchived) {
    if (confirm(isArchived ? 'Are you sure you want to unarchive this module?' : 'Are you sure you want to archive this module?')) {
        fetch(`/admin/modules/${moduleId}/archive`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ is_archived: !isArchived })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the module.');
        });
    }
}

// Batch delete functionality
let selectedModules = new Set();

function toggleModuleSelection(moduleId, checkbox) {
    if (checkbox.checked) {
        selectedModules.add(moduleId);
    } else {
        selectedModules.delete(moduleId);
    }
    updateBatchDeleteButton();
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllModules');
    const moduleCheckboxes = document.querySelectorAll('.module-checkbox');
    
    moduleCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
        const moduleId = parseInt(checkbox.dataset.moduleId);
        if (selectAllCheckbox.checked) {
            selectedModules.add(moduleId);
        } else {
            selectedModules.delete(moduleId);
        }
    });
    
    updateBatchDeleteButton();
}



// Function to update content-specific fields
function updateContentFields(index, contentType, isAdd = false) {
    const prefix = isAdd ? 'add' : 'batch';
    const fieldsContainer = document.getElementById(`${prefix}ContentFields${isAdd ? '' : index}`);
    if (!fieldsContainer) return;
    
    let fieldsHtml = '';
    const namePrefix = isAdd ? '' : `modules[${index}]`;
    
    switch (contentType) {
        case 'assignment':
            fieldsHtml = `
                <input type="text" name="${namePrefix}[assignment_title]" placeholder="Assignment Title">
                <textarea name="${namePrefix}[assignment_instructions]" placeholder="Assignment Instructions"></textarea>
                <input type="datetime-local" name="${namePrefix}[due_date]" placeholder="Due Date">
                <input type="number" name="${namePrefix}[max_points]" placeholder="Maximum Points" min="1">
            `;
            break;
        case 'quiz':
            fieldsHtml = `
                <input type="text" name="${namePrefix}[quiz_title]" placeholder="Quiz Title">
                <textarea name="${namePrefix}[quiz_description]" placeholder="Quiz Description"></textarea>
                <input type="number" name="${namePrefix}[time_limit]" placeholder="Time Limit (minutes)" min="1">
                <input type="number" name="${namePrefix}[question_count]" placeholder="Number of Questions" min="1">
            `;
            break;
        case 'test':
            fieldsHtml = `
                <input type="text" name="${namePrefix}[test_title]" placeholder="Test Title">
                <textarea name="${namePrefix}[test_description]" placeholder="Test Description"></textarea>
                <input type="datetime-local" name="${namePrefix}[test_date]" placeholder="Test Date">
                <input type="number" name="${namePrefix}[duration]" placeholder="Duration (minutes)" min="1">
                <input type="number" name="${namePrefix}[total_marks]" placeholder="Total Marks" min="1">
            `;
            break;
        case 'link':
            fieldsHtml = `
                <input type="url" name="${namePrefix}[external_url]" placeholder="External URL" required>
                <input type="text" name="${namePrefix}[link_title]" placeholder="Link Title">
                <textarea name="${namePrefix}[link_description]" placeholder="Link Description"></textarea>
                <select name="${namePrefix}[link_type]">
                    <option value="video">Video</option>
                    <option value="article">Article</option>
                    <option value="resource">Resource</option>
                    <option value="tool">Tool</option>
                    <option value="other">Other</option>
                </select>
            `;
            break;
    }
    
    fieldsContainer.innerHTML = fieldsHtml;
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, initializing module functions...');
    
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

    // Modal elements
    const showAddModal = document.getElementById('showAddModal');
    const addModalBg = document.getElementById('addModalBg');
    const cancelAdd = document.getElementById('cancelAddModal');
    const editModalBg = document.getElementById('editModalBg');
    const cancelEdit = document.getElementById('cancelEditModal');
    const showBatchModal = document.getElementById('showBatchModal');
    const batchModalBg = document.getElementById('batchModalBg');
    const cancelBatch = document.getElementById('cancelBatchModal');

    // Add Modal functionality
    if (showAddModal && addModalBg) {
        showAddModal.addEventListener('click', function(e) {
            e.preventDefault();
            const currentProgramId = programSelect?.value;
            if (currentProgramId) {
                const modalProgramSelect = document.getElementById('modalProgramSelect');
                if (modalProgramSelect) {
                    modalProgramSelect.value = currentProgramId;
                }
            }
            addModalBg.classList.add('show');
        });
    }

    // Batch Modal functionality - FIXED
    if (showBatchModal && batchModalBg) {
        showBatchModal.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Batch modal button clicked - showing modal');
            const currentProgramId = programSelect?.value;
            if (currentProgramId) {
                const batchProgramSelect = document.getElementById('batchModalProgramSelect');
                if (batchProgramSelect) {
                    batchProgramSelect.value = currentProgramId;
                }
            }
            batchModalBg.classList.add('show');
        });
    } else {
        console.log('Batch modal elements not found:', {
            showBatchModal: !!showBatchModal,
            batchModalBg: !!batchModalBg
        });
    }

    // Cancel buttons
    if (cancelAdd && addModalBg) {
        cancelAdd.addEventListener('click', () => {
            addModalBg.classList.remove('show');
        });
    }
    if (cancelEdit && editModalBg) {
        cancelEdit.addEventListener('click', () => {
            editModalBg.classList.remove('show');
        });
    }
    if (cancelBatch && batchModalBg) {
        cancelBatch.addEventListener('click', function() {
            batchModalBg.classList.remove('show');
        });
    }

    // Click outside to close modals
    [addModalBg, editModalBg, batchModalBg].forEach(modal => {
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('show');
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

    function handleFileSelection(files) {
        if (!files || files.length === 0) {
            selectedFiles.style.display = 'none';
            uploadXmlBtn.disabled = true;
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

        // Show selected files
        selectedFiles.style.display = 'block';
        fileList.innerHTML = '';
        
        xmlFiles.forEach((file, index) => {
            const li = document.createElement('li');
            li.style.cssText = 'padding: 8px 12px; background: #f8f9fa; margin-bottom: 5px; border-radius: 5px; border-left: 3px solid #667eea; display: flex; justify-content: space-between; align-items: center;';
            li.innerHTML = `
                <span>📄 ${file.name} (${(file.size / 1024).toFixed(1)} KB)</span>
                <button type="button" onclick="removeFile(${index})" style="background: #dc3545; color: white; border: none; border-radius: 3px; padding: 2px 6px; cursor: pointer; font-size: 12px;">×</button>
            `;
            fileList.appendChild(li);
        });

        uploadXmlBtn.disabled = false;
        uploadXmlBtn.textContent = `Upload ${xmlFiles.length} XML File${xmlFiles.length > 1 ? 's' : ''}`;
    }

    // Make removeFile function global for the onclick handler
    window.removeFile = function(index) {
        // This is a simplified version - in a real implementation,
        // you'd need to maintain a separate array of files
        const currentFiles = Array.from(batchXmlFiles.files);
        const newFileList = new DataTransfer();
        
        currentFiles.forEach((file, i) => {
            if (i !== index && file.name.toLowerCase().endsWith('.xml')) {
                newFileList.items.add(file);
            }
        });
        
        batchXmlFiles.files = newFileList.files;
        handleFileSelection(batchXmlFiles.files);
    };

    console.log('Module initialization complete');
});
</script>
@endpush
