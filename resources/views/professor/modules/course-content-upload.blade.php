@extends('professor.professor-layouts.professor-layout')

@section('title', 'Course Content Upload')

@push('styles')
<style>
.upload-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.upload-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px 15px 0 0;
    text-align: center;
    margin-bottom: 0;
}

.upload-header h1 {
    margin: 0;
    font-size: 2.2rem;
    font-weight: 300;
}

.upload-header p {
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
    font-size: 1.1rem;
}

.upload-form {
    background: white;
    padding: 3rem;
    border-radius: 0 0 15px 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.form-section {
    margin-bottom: 2.5rem;
}

.section-title {
    color: #2c3e50;
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.form-control, .form-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    outline: none;
}

.form-control:disabled, .form-select:disabled {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    opacity: 0.7;
}

.file-upload-area {
    border: 2px dashed #667eea;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    background: #f8f9ff;
}

.file-upload-area:hover {
    border-color: #5a6fd8;
    background: #f0f2ff;
}

.file-upload-area.dragover {
    border-color: #28a745;
    background: #f8fff9;
    transform: scale(1.02);
}

.upload-icon {
    font-size: 3rem;
    color: #667eea;
    margin-bottom: 1rem;
}

.upload-text {
    font-size: 1.1rem;
    color: #495057;
    margin-bottom: 0.5rem;
}

.upload-hint {
    font-size: 0.9rem;
    color: #6c757d;
}

.file-list {
    margin-top: 1rem;
}

.file-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 0.5rem;
}

.file-item-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.file-icon {
    font-size: 1.2rem;
    color: #667eea;
}

.file-details h6 {
    margin: 0;
    font-size: 0.9rem;
    color: #495057;
}

.file-details small {
    color: #6c757d;
    font-size: 0.8rem;
}

.file-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-remove-file {
    background: #dc3545;
    color: white;
    border: none;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-remove-file:hover {
    background: #c82333;
    transform: scale(1.05);
}

.progress-bar {
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
    margin-top: 0.5rem;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    width: 0%;
    transition: width 0.3s ease;
}

.content-type-selector {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.content-type-option {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.content-type-option:hover {
    border-color: #667eea;
    background: #f8f9ff;
    transform: translateY(-2px);
}

.content-type-option.selected {
    border-color: #667eea;
    background: #f0f2ff;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

.content-type-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.content-type-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.25rem;
}

.content-type-description {
    font-size: 0.8rem;
    color: #6c757d;
}

.btn-submit {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
}

.btn-submit:hover {
    background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.btn-submit:disabled {
    background: #6c757d;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    border: 1px solid transparent;
}

.alert-success {
    background: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.alert-danger {
    background: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.alert-info {
    background: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

@media (max-width: 768px) {
    .upload-container {
        padding: 1rem;
    }
    
    .upload-form {
        padding: 2rem 1.5rem;
    }
    
    .content-type-selector {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')
<div class="upload-container">
    <div class="upload-header">
        <h1><i class="bi bi-cloud-upload"></i> Course Content Upload</h1>
        <p>Upload and organize educational content for your courses</p>
    </div>
    
    <div class="upload-form">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Please correct the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('professor.content.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf
            
            <!-- Program and Module Selection -->
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-diagram-3"></i>
                    Program & Module Selection
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="program_id" class="form-label">Program *</label>
                            <select class="form-select" id="program_id" name="program_id" required>
                                <option value="">Select Program</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->program_id }}" 
                                            {{ old('program_id') == $program->program_id ? 'selected' : '' }}>
                                        {{ $program->program_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="module_id" class="form-label">Module *</label>
                            <select class="form-select" id="module_id" name="module_id" required disabled>
                                <option value="">Select Module</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="course_id" class="form-label">Course *</label>
                            <select class="form-select" id="course_id" name="course_id" required disabled>
                                <option value="">Select Course</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="batch_id" class="form-label">Batch (Optional)</label>
                            <select class="form-select" id="batch_id" name="batch_id">
                                <option value="">All Batches</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Content Type Selection -->
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-file-earmark-text"></i>
                    Content Type
                </div>
                
                <div class="content-type-selector">
                    <div class="content-type-option" data-type="lesson">
                        <div class="content-type-icon">📚</div>
                        <div class="content-type-label">Lesson</div>
                        <div class="content-type-description">Text-based learning material</div>
                    </div>
                    
                    <div class="content-type-option" data-type="video">
                        <div class="content-type-icon">🎥</div>
                        <div class="content-type-label">Video</div>
                        <div class="content-type-description">Video content or recordings</div>
                    </div>
                    
                    <div class="content-type-option" data-type="assignment">
                        <div class="content-type-icon">📝</div>
                        <div class="content-type-label">Assignment</div>
                        <div class="content-type-description">Student assignments and tasks</div>
                    </div>
                    
                    <div class="content-type-option" data-type="quiz">
                        <div class="content-type-icon">❓</div>
                        <div class="content-type-label">Quiz</div>
                        <div class="content-type-description">Interactive quizzes and tests</div>
                    </div>
                    
                    <div class="content-type-option" data-type="pdf">
                        <div class="content-type-icon">📄</div>
                        <div class="content-type-label">PDF Document</div>
                        <div class="content-type-description">PDF files and documents</div>
                    </div>
                    
                    <div class="content-type-option" data-type="link">
                        <div class="content-type-icon">🔗</div>
                        <div class="content-type-label">External Link</div>
                        <div class="content-type-description">Links to external resources</div>
                    </div>
                </div>
                
                <input type="hidden" id="content_type" name="content_type" required>
            </div>
            
            <!-- Content Details -->
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-info-circle"></i>
                    Content Details
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="title" class="form-label">Content Title *</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="{{ old('title') }}" required>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="order" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="order" name="order" 
                                   value="{{ old('order', 1) }}" min="1">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" 
                              rows="4">{{ old('description') }}</textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="learning_mode" class="form-label">Learning Mode</label>
                            <select class="form-select" id="learning_mode" name="learning_mode">
                                <option value="">Select Learning Mode</option>
                                <option value="synchronous" {{ old('learning_mode') == 'synchronous' ? 'selected' : '' }}>
                                    Synchronous
                                </option>
                                <option value="asynchronous" {{ old('learning_mode') == 'asynchronous' ? 'selected' : '' }}>
                                    Asynchronous
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="duration" class="form-label">Estimated Duration (minutes)</label>
                            <input type="number" class="form-control" id="duration" name="duration" 
                                   value="{{ old('duration') }}" min="1">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- File Upload -->
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-upload"></i>
                    File Upload
                </div>
                
                <div class="file-upload-area" id="fileUploadArea">
                    <div class="upload-icon">
                        <i class="bi bi-cloud-upload"></i>
                    </div>
                    <div class="upload-text">Drag and drop files here or click to browse</div>
                    <div class="upload-hint">Supported formats: PDF, DOC, DOCX, PPT, PPTX, MP4, MP3, JPG, PNG</div>
                    <input type="file" id="files" name="files[]" multiple accept=".pdf,.doc,.docx,.ppt,.pptx,.mp4,.mp3,.jpg,.jpeg,.png" style="display: none;">
                </div>
                
                <div class="file-list" id="fileList"></div>
            </div>
            
            <!-- Additional Settings -->
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-gear"></i>
                    Additional Settings
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="is_required" class="form-label">Required Content</label>
                            <select class="form-select" id="is_required" name="is_required">
                                <option value="1" {{ old('is_required', 1) == 1 ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ old('is_required') == 0 ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="is_visible" class="form-label">Visible to Students</label>
                            <select class="form-select" id="is_visible" name="is_visible">
                                <option value="1" {{ old('is_visible', 1) == 1 ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ old('is_visible') == 0 ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="notes" class="form-label">Internal Notes (Optional)</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    <small class="text-muted">These notes are only visible to professors</small>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="form-section">
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="bi bi-cloud-upload"></i> Upload Content
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const programSelect = document.getElementById('program_id');
    const moduleSelect = document.getElementById('module_id');
    const courseSelect = document.getElementById('course_id');
    const batchSelect = document.getElementById('batch_id');
    const contentTypeOptions = document.querySelectorAll('.content-type-option');
    const contentTypeInput = document.getElementById('content_type');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('files');
    const fileList = document.getElementById('fileList');
    const submitBtn = document.getElementById('submitBtn');
    
    // Program selection handler
    programSelect.addEventListener('change', function() {
        const programId = this.value;
        moduleSelect.innerHTML = '<option value="">Select Module</option>';
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        batchSelect.innerHTML = '<option value="">All Batches</option>';
        
        if (programId) {
            moduleSelect.disabled = false;
            loadModules(programId);
            loadBatches(programId);
        } else {
            moduleSelect.disabled = true;
            courseSelect.disabled = true;
        }
    });
    
    // Module selection handler
    moduleSelect.addEventListener('change', function() {
        const moduleId = this.value;
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        
        if (moduleId) {
            courseSelect.disabled = false;
            loadCourses(moduleId);
        } else {
            courseSelect.disabled = true;
        }
    });
    
    // Content type selection
    contentTypeOptions.forEach(option => {
        option.addEventListener('click', function() {
            contentTypeOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            contentTypeInput.value = this.dataset.type;
        });
    });
    
    // File upload handling
    fileUploadArea.addEventListener('click', () => fileInput.click());
    
    fileUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileUploadArea.classList.add('dragover');
    });
    
    fileUploadArea.addEventListener('dragleave', () => {
        fileUploadArea.classList.remove('dragover');
    });
    
    fileUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        fileUploadArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        handleFiles(files);
    });
    
    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });
    
    // Form submission
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        if (!contentTypeInput.value) {
            e.preventDefault();
            alert('Please select a content type');
            return;
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Uploading...';
    });
    
    // Load modules for selected program
    function loadModules(programId) {
        fetch(`/api/programs/${programId}/modules`)
            .then(response => response.json())
            .then(data => {
                data.forEach(module => {
                    const option = document.createElement('option');
                    option.value = module.modules_id;
                    option.textContent = module.module_name;
                    moduleSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading modules:', error);
            });
    }
    
    // Load courses for selected module
    function loadCourses(moduleId) {
        fetch(`/api/modules/${moduleId}/courses`)
            .then(response => response.json())
            .then(data => {
                data.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.course_id;
                    option.textContent = course.course_name;
                    courseSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading courses:', error);
            });
    }
    
    // Load batches for selected program
    function loadBatches(programId) {
        fetch(`/api/programs/${programId}/batches`)
            .then(response => response.json())
            .then(data => {
                data.forEach(batch => {
                    const option = document.createElement('option');
                    option.value = batch.batch_id;
                    option.textContent = batch.batch_name;
                    batchSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading batches:', error);
            });
    }
    
    // Handle file selection
    function handleFiles(files) {
        Array.from(files).forEach(file => {
            if (isValidFile(file)) {
                addFileToList(file);
            } else {
                alert(`Invalid file type: ${file.name}. Please select a valid file.`);
            }
        });
    }
    
    // Validate file type
    function isValidFile(file) {
        const validTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'video/mp4',
            'audio/mpeg',
            'image/jpeg',
            'image/png'
        ];
        return validTypes.includes(file.type);
    }
    
    // Add file to list
    function addFileToList(file) {
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.innerHTML = `
            <div class="file-item-info">
                <div class="file-icon">
                    <i class="bi bi-file-earmark"></i>
                </div>
                <div class="file-details">
                    <h6>${file.name}</h6>
                    <small>${formatFileSize(file.size)}</small>
                </div>
            </div>
            <div class="file-actions">
                <button type="button" class="btn-remove-file" onclick="this.parentElement.parentElement.remove()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        `;
        fileList.appendChild(fileItem);
    }
    
    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
@endpush
@endsection 