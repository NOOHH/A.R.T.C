@extends('admin.admin-dashboard.admin-dashboard-layout')

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
    border-color: #5a67d8;
    background: #f0f2ff;
}

.file-upload-area-hover {
    border-color: #5a67d8;
    background: #f0f2ff;
}

.file-upload-icon {
    font-size: 3rem;
    color: #667eea;
    margin-bottom: 1rem;
}

.file-input {
    display: none;
}

.file-label {
    cursor: pointer;
    color: #667eea;
    font-weight: 600;
    text-decoration: underline;
}

.file-info {
    color: #6c757d;
    font-size: 0.9rem;
    margin-top: 0.5rem;
}

.selected-file {
    margin-top: 1rem;
    padding: 0.75rem;
    background: #d4edda;
    border: 1px solid #c3e6cb;
    border-radius: 6px;
    color: #155724;
    display: none;
}

.submit-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 1rem 2.5rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0 auto;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.progress-bar {
    width: 100%;
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    margin-top: 1rem;
    overflow: hidden;
    display: none;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    width: 0%;
    transition: width 0.3s ease;
}

.required {
    color: #e74c3c;
}

.select-loading {
    position: relative;
}

.select-loading::after {
    content: '';
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    border: 2px solid #667eea;
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: translateY(-50%) rotate(0deg); }
    100% { transform: translateY(-50%) rotate(360deg); }
}

.back-btn {
    color: #667eea;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.back-btn:hover {
    color: #5a67d8;
    text-decoration: none;
    transform: translateX(-3px);
}
</style>
@endpush

@section('content')
<div class="upload-container">
    <a href="{{ url()->previous() }}" class="back-btn">
        <i class="bi bi-arrow-left"></i>
        Back to Admin Modules
    </a>

    <div class="upload-header">
        <h1><i class="bi bi-cloud-upload"></i> Course Content Upload</h1>
        <p>Upload educational content for your courses with ease</p>
    </div>

    <form id="nonModalContentForm" action="{{ route('admin.modules.course-content-store') }}" method="POST" enctype="multipart/form-data" class="upload-form">
        @csrf
        
        <div class="form-section">
            <h3 class="section-title">
                <i class="bi bi-mortarboard"></i>
                Course Selection
            </h3>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nonModalProgramSelect" class="form-label">
                            Program <span class="required">*</span>
                        </label>
                        <select id="nonModalProgramSelect" name="program_id" class="form-select" required>
                            <option value="">-- Select Program --</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nonModalModuleSelect" class="form-label">
                            Module <span class="required">*</span>
                        </label>
                        <select id="nonModalModuleSelect" name="module_id" class="form-select" required disabled>
                            <option value="">-- Select Module --</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nonModalCourseSelect" class="form-label">
                            Course <span class="required">*</span>
                        </label>
                        <select id="nonModalCourseSelect" name="course_id" class="form-select" required disabled>
                            <option value="">-- Select Course --</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">
                <i class="bi bi-file-text"></i>
                Content Details
            </h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="contentType" class="form-label">
                            Content Type <span class="required">*</span>
                        </label>
                        <select id="contentType" name="content_type" class="form-select" required>
                            <option value="">-- Select Type --</option>
                            <option value="lesson">📚 Lesson</option>
                            <option value="video">🎥 Video</option>
                            <option value="assignment">📝 Assignment</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="contentTitle" class="form-label">
                            Content Title <span class="required">*</span>
                        </label>
                        <input type="text" id="contentTitle" name="content_title" class="form-control" 
                               placeholder="Enter a descriptive title..." required>
                    </div>
                </div>
            </div>
            <!-- Assignment Due Date (only for assignments) -->
            <div class="form-group" id="assignmentDueDateGroup" style="display:none;">
                <label for="assignmentDueDate" class="form-label">Assignment Due Date <span class="required">*</span></label>
                <input type="datetime-local" id="assignmentDueDate" name="due_date" class="form-control">
                <small class="form-text text-muted">Set the deadline for this assignment. Students cannot submit after this date/time.</small>
            </div>
            <div class="form-group">
                <label for="contentDescription" class="form-label">Content Description</label>
                <textarea id="contentDescription" name="content_description" class="form-control" 
                          rows="4" placeholder="Provide a detailed description of this content..."></textarea>
            </div>
        </div>

        <!-- Allow student submission toggle, hidden by default -->
        <div class="form-group" id="studentSubmissionToggle" style="display:none;">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="enableSubmission" name="enable_submission">
                <label class="form-check-label" for="enableSubmission">
                    Allow student submission (students can upload their work for this assignment)
                </label>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">
                <i class="bi bi-paperclip"></i>
                File Upload or Content Link
            </h3>
            <div class="form-group">
                <label for="contentLink" class="form-label">Content Link (URL)</label>
                <input type="url" id="contentLink" name="content_url" class="form-control" placeholder="Paste a link to content (optional)">
            </div>
            <div class="file-upload-area" id="fileUploadArea">
                <div class="file-upload-icon">
                    <i class="bi bi-cloud-arrow-up"></i>
                </div>
                <p><strong>Click to upload</strong> or drag and drop</p>
                <div class="file-info">
                    Supported formats: PDF, DOC, DOCX, ZIP, Images, Videos<br>
                    Maximum file size: 100MB each
                </div>
                <input type="file" id="attachment" name="attachment[]" class="file-input" multiple accept=".pdf,.doc,.docx,.zip,.png,.jpg,.jpeg,.mp4,.webm,.ogg,.avi,.mov">
                <div class="selected-file" id="selectedFile" style="display:none">
                    <i class="bi bi-file-check"></i>
                    <span id="fileName"></span>
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="clearFile()">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="submit-btn" id="submitBtn">
                <i class="bi bi-upload"></i>
                Upload Content
            </button>
            <div class="progress-bar" id="progressBar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
        </div>
    </form>
</div>

<script>
// Enhanced file upload functionality
const fileInput = document.getElementById('attachment');
const selectedFileDiv = document.getElementById('selectedFile');
const fileNameSpan = document.getElementById('fileName');
const submitBtn = document.getElementById('submitBtn');
const progressBar = document.getElementById('progressBar');
const progressFill = document.getElementById('progressFill');
const fileUploadArea = document.getElementById('fileUploadArea');
const contentLinkInput = document.getElementById('contentLink');
const contentTypeSelect = document.getElementById('contentType');
const studentSubmissionToggle = document.getElementById('studentSubmissionToggle');
const assignmentDueDateGroup = document.getElementById('assignmentDueDateGroup');
const assignmentDueDateInput = document.getElementById('assignmentDueDate');

// File selection handler (multiple files)
fileInput.addEventListener('change', function(e) {
    displaySelectedFiles();
});

function displaySelectedFiles() {
    const files = fileInput.files;
    if (files && files.length > 0) {
        let names = [];
        let valid = true;
        for (let i = 0; i < files.length; i++) {
            if (!validateFile(files[i])) {
                valid = false;
                break;
            }
            names.push(`${files[i].name} (${formatFileSize(files[i].size)})`);
        }
        if (valid) {
            fileNameSpan.textContent = names.join(', ');
            selectedFileDiv.style.display = 'block';
        } else {
            clearFile();
        }
    } else {
        selectedFileDiv.style.display = 'none';
    }
}

// File validation (multiple files)
function validateFile(file) {
    const maxSize = 100 * 1024 * 1024; // 100MB
    const allowedTypes = ['pdf', 'doc', 'docx', 'zip', 'png', 'jpg', 'jpeg', 'mp4', 'webm', 'ogg', 'avi', 'mov'];
    const extension = file.name.split('.').pop().toLowerCase();
    if (file.size > maxSize) {
        alert('File is too large. Maximum size allowed is 100MB.');
        return false;
    }
    if (!allowedTypes.includes(extension)) {
        alert('Invalid file type. Please select a supported file format.');
        return false;
    }
    return true;
}

// Clear file selection
function clearFile() {
    fileInput.value = '';
    selectedFileDiv.style.display = 'none';
}

// Drag and drop support
fileUploadArea.addEventListener('click', function() {
    fileInput.click();
});
fileUploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    fileUploadArea.classList.add('file-upload-area-hover');
});
fileUploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    fileUploadArea.classList.remove('file-upload-area-hover');
});
fileUploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    fileUploadArea.classList.remove('file-upload-area-hover');
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
        fileInput.files = e.dataTransfer.files;
        displaySelectedFiles();
    }
});

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Dynamic dropdown functionality
const nonModalProgramSelect = document.getElementById('nonModalProgramSelect');
const nonModalModuleSelect = document.getElementById('nonModalModuleSelect');
const nonModalCourseSelect = document.getElementById('nonModalCourseSelect');

// Helper to get query params
function getQueryParam(name) {
    const url = new URL(window.location.href);
    return url.searchParams.get(name);
}

// Auto-select from query params if present
const preselectProgramId = getQueryParam('program_id');
const preselectModuleId = getQueryParam('module_id');
const preselectCourseId = getQueryParam('course_id');

// Initialize dropdowns when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Course content upload page loaded');
    
    if (preselectProgramId) {
        console.log('Pre-selecting program ID:', preselectProgramId);
        nonModalProgramSelect.value = preselectProgramId;
        populateModules(preselectProgramId, preselectModuleId, preselectCourseId);
    } else {
        // If only one program, select it
        if (nonModalProgramSelect.options.length === 2) { // 1 placeholder + 1 real
            nonModalProgramSelect.selectedIndex = 1;
            populateModules(nonModalProgramSelect.value);
        }
    }

    // Show/hide student submission toggle based on content type
    contentTypeSelect.addEventListener('change', function() {
        if (this.value === 'assignment') {
            studentSubmissionToggle.style.display = 'block';
            assignmentDueDateGroup.style.display = 'block';
            assignmentDueDateInput.setAttribute('required', 'required');
        } else {
            studentSubmissionToggle.style.display = 'none';
            document.getElementById('enableSubmission').checked = false;
            assignmentDueDateGroup.style.display = 'none';
            assignmentDueDateInput.removeAttribute('required');
        }
    });
});

nonModalProgramSelect.addEventListener('change', function() {
    console.log('Program selected:', this.value);
    populateModules(this.value);
});

nonModalModuleSelect.addEventListener('change', function() {
    console.log('Module selected:', this.value);
    populateCourses(this.value);
});

function addLoadingState(element) {
    element.parentElement.classList.add('select-loading');
}

function removeLoadingState(element) {
    element.parentElement.classList.remove('select-loading');
}

// Enhance populateModules to auto-select if only one module
function populateModules(programId, moduleToSelect, courseToSelect) {
    nonModalModuleSelect.innerHTML = '<option value="">Loading...</option>';
    nonModalModuleSelect.disabled = true;
    nonModalCourseSelect.innerHTML = '<option value="">-- Select Course --</option>';
    nonModalCourseSelect.disabled = true;
    addLoadingState(nonModalModuleSelect);
    if (programId) {
        fetch('/admin/modules/by-program?program_id=' + programId)
            .then(response => response.json())
            .then(data => {
                nonModalModuleSelect.innerHTML = '<option value="">-- Select Module --</option>';
                if (data.success && data.modules && data.modules.length > 0) {
                    data.modules.forEach(module => {
                        const option = document.createElement('option');
                        option.value = module.modules_id;
                        option.textContent = module.module_name;
                        nonModalModuleSelect.appendChild(option);
                    });
                    nonModalModuleSelect.disabled = false;
                    if (moduleToSelect) {
                        nonModalModuleSelect.value = moduleToSelect;
                        populateCourses(moduleToSelect, courseToSelect);
                    } else if (data.modules.length === 1) {
                        nonModalModuleSelect.selectedIndex = 1;
                        populateCourses(nonModalModuleSelect.value);
                    }
                } else {
                    nonModalModuleSelect.innerHTML = '<option value="">No modules available</option>';
                }
            })
            .catch(() => {
                nonModalModuleSelect.innerHTML = '<option value="">Error loading modules</option>';
            })
            .finally(() => {
                removeLoadingState(nonModalModuleSelect);
            });
    } else {
        nonModalModuleSelect.innerHTML = '<option value="">-- Select Module --</option>';
        nonModalModuleSelect.disabled = true;
        removeLoadingState(nonModalModuleSelect);
    }
}

// Enhance populateCourses to auto-select if only one course
function populateCourses(moduleId, courseToSelect) {
    nonModalCourseSelect.innerHTML = '<option value="">Loading...</option>';
    nonModalCourseSelect.disabled = true;
    addLoadingState(nonModalCourseSelect);
    if (moduleId) {
        fetch('/admin/modules/' + moduleId + '/courses')
            .then(response => response.json())
            .then(data => {
                nonModalCourseSelect.innerHTML = '<option value="">-- Select Course --</option>';
                if (data.success && data.courses && data.courses.length > 0) {
                    data.courses.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course.subject_id;
                        option.textContent = course.subject_name;
                        nonModalCourseSelect.appendChild(option);
                    });
                    nonModalCourseSelect.disabled = false;
                    if (courseToSelect) {
                        nonModalCourseSelect.value = courseToSelect;
                    } else if (data.courses.length === 1) {
                        nonModalCourseSelect.selectedIndex = 1;
                    }
                } else {
                    nonModalCourseSelect.innerHTML = '<option value="">No courses available</option>';
                }
            })
            .catch(() => {
                nonModalCourseSelect.innerHTML = '<option value="">Error loading courses</option>';
            })
            .finally(() => {
                removeLoadingState(nonModalCourseSelect);
            });
    } else {
        nonModalCourseSelect.innerHTML = '<option value="">-- Select Course --</option>';
        nonModalCourseSelect.disabled = true;
        removeLoadingState(nonModalCourseSelect);
    }
}

// Enhanced form submission
document.getElementById('nonModalContentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const files = fileInput.files;
    const link = contentLinkInput.value.trim();
    if ((!files || files.length === 0) && !link) {
        alert('Please upload at least one file or provide a content link.');
        return;
    }
    if (files && files.length > 0) {
        for (let i = 0; i < files.length; i++) {
            if (!validateFile(files[i])) {
                return;
            }
        }
    }
    
    // Update UI for upload
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Uploading...';
    progressBar.style.display = 'block';
    
    // Simulate upload progress (in a real scenario, you'd get this from the server)
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += Math.random() * 30;
        if (progress > 90) {
            clearInterval(progressInterval);
        }
        progressFill.style.width = Math.min(progress, 90) + '%';
    }, 200);
    
    // Submit form
    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async response => {
        const responseText = await response.text();
        try {
            const data = JSON.parse(responseText);
            if (response.ok && data.success) {
                // Success animation
                submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Upload Complete!';
                submitBtn.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
                
                // Show success message
                setTimeout(() => {
                    alert('Content uploaded successfully!');
                    // Redirect back to admin modules
                    window.location.href = '{{ route("admin.modules.index") }}';
                }, 1000);
            } else {
                throw new Error(data.message || 'Upload failed');
            }
        } catch (error) {
            console.error('Invalid JSON response:', responseText);
            throw new Error('Unexpected server response');
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        alert('Upload failed: ' + error.message);
        
        // Reset form state
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-upload"></i> Upload Content';
        progressBar.style.display = 'none';
        progressFill.style.width = '0%';
    });
});
</script>
@endsection
