@extends('professor.professor-layouts.professor-layout')

@section('title', 'Assignment Submissions')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
  /* Assignment submissions page styles with Bootstrap */
  .submissions-container {
    background: #fff;
    padding: 40px 20px 60px;
    margin: 40px 0 0 0;
    max-width: 1400px;
    width: 100%;
    box-sizing: border-box;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  }

  .back-to-modules-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

  .back-to-modules-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    color: white;
    text-decoration: none;
  }

  .assignment-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    border-left: 4px solid #667eea;
  }

  .assignment-info h2 {
    color: #2c3e50;
    margin: 0 0 10px 0;
    font-size: 1.4rem;
  }

  .assignment-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
  }

  .meta-item {
    display: flex;
    flex-direction: column;
    gap: 3px;
  }

  .meta-label {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
  }

  .meta-value {
    color: #6c757d;
    font-size: 0.95rem;
  }

  .submissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 25px;
  }

  .submission-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 15px;
    padding: 25px;
    transition: all 0.3s ease;
    border: 1px solid #e1e5e9;
    position: relative;
    overflow: hidden;
  }

  .submission-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
  }

  .submission-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
  }

  .submission-status {
    position: absolute;
    top: 15px;
    right: 15px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
  }

  .submission-status.submitted {
    background: #d4edda;
    color: #155724;
  }

  .submission-status.graded {
    background: #cce5ff;
    color: #004085;
  }

  .submission-status.late {
    background: #f8d7da;
    color: #721c24;
  }

  .student-info {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
  }

  .student-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.2rem;
  }

  .student-details h3 {
    margin: 0;
    color: #2c3e50;
    font-size: 1.1rem;
  }

  .student-details p {
    margin: 0;
    color: #6c757d;
    font-size: 0.9rem;
  }

  .submission-details {
    margin: 15px 0;
  }

  .submission-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 15px;
    font-size: 0.9rem;
  }

  .submission-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 15px;
  }

  .btn {
    padding: 8px 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.85rem;
  }

  .btn-primary {
    background: #667eea;
    color: white;
  }

  .btn-primary:hover {
    background: #5a6fd8;
    transform: scale(1.05);
  }

  .btn-success {
    background: #28a745;
    color: white;
  }

  .btn-success:hover {
    background: #218838;
    transform: scale(1.05);
  }

  .btn-info {
    background: #17a2b8;
    color: white;
  }

  .btn-info:hover {
    background: #138496;
    transform: scale(1.05);
  }

  .grade-display {
    background: #e7f3ff;
    color: #0066cc;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: 600;
    display: inline-block;
    margin-top: 10px;
  }

  .feedback-display {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 8px;
    margin-top: 10px;
    font-size: 0.9rem;
    border-left: 3px solid #667eea;
  }

  /* Grading Modal - Complete Custom Implementation */
  #grading-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    z-index: 9999;
    align-items: center;
    justify-content: center;
  }

  #grading-modal-overlay.show {
    display: flex !important;
  }

  #grading-modal-container {
    background: white;
    border-radius: 12px;
    max-width: 900px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    position: relative;
  }

  #grading-modal-inner {
    padding: 30px;
  }

  .modal h3 {
    color: #2c3e50;
    margin: 0 0 20px 0;
    font-size: 1.5rem;
  }

  .modal input, .modal textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 1rem;
    box-sizing: border-box;
  }

  .modal textarea {
    min-height: 100px;
    resize: vertical;
  }

  .modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
  }

  .no-submissions {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    font-size: 1.1rem;
  }

  .no-submissions::before {
    content: '📝';
    display: block;
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
  }
</style>
@endpush

@section('content')
<div class="submissions-container">
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <h1 class="display-4 fw-bold text-uppercase text-dark mb-0" style="letter-spacing: 2px;">Assignment Submissions</h1>
        @php
            // Check if we're in tenant preview mode
            $tenantSlug = request()->route('tenant') ?? session('preview_tenant');
            $routePrefix = $tenantSlug ? 'tenant.draft.' : '';
            $routeParams = $tenantSlug ? ['tenant' => $tenantSlug] : [];
            $modulesRoute = $tenantSlug ? $routePrefix . 'professor.modules' : 'professor.modules.index';
        @endphp
        <a href="{{ route($modulesRoute, $routeParams) }}" class="btn btn-lg text-white fw-semibold px-4 py-2 rounded-pill shadow back-to-modules-btn">
            <i class="fas fa-arrow-left me-2"></i>Back to Modules
        </a>
    </div>

    <!-- Summary Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="bg-light p-3 rounded">
                        <small class="text-muted d-block">Total Submissions</small>
                        <strong>{{ $submissions->total() }}</strong>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="bg-light p-3 rounded">
                        <small class="text-muted d-block">Assigned Programs</small>
                        <strong>{{ $assignedPrograms->count() }}</strong>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="bg-light p-3 rounded">
                        <small class="text-muted d-block">Available Modules</small>
                        <strong>{{ $modules->count() }}</strong>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="bg-light p-3 rounded">
                        <small class="text-muted d-block">Graded</small>
                        <strong>{{ $submissions->where('grade', '!=', null)->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($submissions->count() > 0)
            <div class="row">
                @foreach($submissions as $submission)
                    @php
                        $isLate = false;
                        if ($submission->module && !empty($submission->module->content_data['due_date'])) {
                            $dueDate = \Carbon\Carbon::parse($submission->module->content_data['due_date']);
                            $isLate = $submission->created_at->isAfter($dueDate);
                        }
                        
                        $status = $submission->grade !== null ? 'graded' : ($isLate ? 'late' : 'submitted');
                    @endphp
                    
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="badge bg-{{ $status === 'graded' ? 'success' : ($isLate ? 'warning' : 'primary') }}">
                                    {{ $status === 'graded' ? 'Graded' : ($isLate ? 'Late' : 'Submitted') }}
                                </span>
                            </div>
                            
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                        {{ strtoupper(substr($submission->student->firstname ?? 'S', 0, 1)) }}{{ strtoupper(substr($submission->student->lastname ?? 'T', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1">{{ $submission->student->firstname ?? 'Unknown' }} {{ $submission->student->lastname ?? 'Student' }}</h5>
                                        <small class="text-muted">{{ $submission->student->email ?? 'No email' }}</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted">Submitted</small>
                                    <div class="fw-semibold">{{ $submission->created_at->format('M d, Y g:i A') }}</div>
                                </div>
                                
                                @if($isLate)
                                <div class="mb-3">
                                    <small class="text-muted">Status</small>
                                    <div class="text-danger fw-semibold">Late Submission</div>
                                </div>
                                @endif

                                @if($submission->comments)
                                    <div class="bg-light p-3 rounded mb-3">
                                        <small class="text-muted d-block">Student Notes</small>
                                        <div class="mt-1">{{ $submission->comments }}</div>
                                    </div>
                                @endif

                                @if($submission->grade !== null)
                                    <div class="alert alert-success">
                                        <strong>Grade:</strong> {{ $submission->grade }}{{ ($submission->module && !empty($submission->module->content_data['max_points'])) ? '/' . $submission->module->content_data['max_points'] : '' }}
                                        
                                        @if($submission->feedback)
                                            <hr class="my-2">
                                            <strong>Feedback:</strong><br>
                                            {{ $submission->feedback }}
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="card-footer bg-transparent">
                                <div class="d-flex gap-2">
                                    @if($submission->processed_files && count($submission->processed_files) > 0)
                                        <a href="{{ route('professor.submissions.download', $submission->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    @endif
                                    
                                    <button class="btn btn-primary btn-sm" onclick="openGradingModal({{ $submission->id }})">
                                        <i class="fas fa-{{ $submission->grade !== null ? 'edit' : 'star' }} me-1"></i>{{ $submission->grade !== null ? 'Edit Grade' : 'Grade' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <div class="bg-light border border-2 border-dashed rounded-4 p-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <div class="text-muted h5 mb-2">No submissions found.</div>
                    <small class="text-muted">No assignment submissions are available at this time.</small>
                </div>
            </div>
        @endif
</div>

<!-- Grading Modal -->
<div id="grading-modal-overlay" class="custom-modal-bg">
    <div id="grading-modal-container" class="custom-modal-content">
        <div id="grading-modal-inner">
            <h3 id="gradingModalTitle">Grade Assignment</h3>
            
            <!-- Assignment Details Section -->
            <div class="assignment-details mb-3" id="assignmentDetails" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <h5>Assignment Information</h5>
            <p><strong>Title:</strong> <span id="assignmentTitle"></span></p>
            <p><strong>Description:</strong> <span id="assignmentDescription"></span></p>
            <p><strong>Max Points:</strong> <span id="assignmentMaxPoints"></span></p>
            <p><strong>Due Date:</strong> <span id="assignmentDueDate"></span></p>
        </div>

        <!-- Student Submission Details -->
        <div class="submission-details mb-3" id="submissionDetails" style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <h5>Student Submission</h5>
            <p><strong>Student:</strong> <span id="studentName"></span></p>
            <p><strong>Submitted:</strong> <span id="submissionDate"></span></p>
            <p><strong>Files:</strong> <span id="submissionFiles"></span></p>
            <p><strong>Comments:</strong> <span id="submissionComments"></span></p>
        </div>

        <!-- File Viewer Section -->
        <div class="submission-file-viewer mb-3" id="fileViewerSection">
            <h5>File Preview</h5>
            <div class="file-preview-controls">
                <select id="fileSelector" class="form-control mb-2" style="max-width: 300px;">
                    <option value="">Select a file to view...</option>
                </select>
            </div>
            
            <div class="file-preview-container" id="filePreviewContainer" style="min-height: 400px; border: 1px solid #ddd; border-radius: 8px; padding: 10px; background: #f8f9fa; position: relative;">
                <!-- PDF Viewer -->
                <iframe id="pdfViewer" style="width: 100%; height: 500px; border: none; display: none;"></iframe>
                
                <!-- Image Viewer -->
                <div id="imageViewer" style="width: 100%; height: 500px; display: none; text-align: center; overflow: auto;">
                    <img id="imagePreview" style="max-width: 100%; max-height: 100%;" />
                </div>
                
                <!-- Text Viewer -->
                <pre id="textViewer" style="width: 100%; height: 500px; overflow: auto; display: none; padding: 15px; white-space: pre-wrap;"></pre>
                
                <!-- Unsupported File Type -->
                <div id="unsupportedFileType" style="display: none; text-align: center; padding: 50px 0;">
                    <i class="fas fa-file-alt fa-3x mb-3" style="color: #6c757d;"></i>
                    <h5>Cannot Preview This File Type</h5>
                    <p>Click the button below to download this file for viewing.</p>
                    <a id="downloadFileLink" href="#" class="btn btn-info" target="_blank">
                        <i class="fas fa-download me-1"></i> Download File
                    </a>
                </div>
                
                <!-- No Files Message -->
                <div id="noFilesMessage" style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6c757d; text-align: center;">
                    <div>
                        <i class="fas fa-file-upload fa-3x mb-3"></i>
                        <p>No files available for preview</p>
                    </div>
                </div>
            </div>
        </div>

        <form id="gradingForm">
            @csrf
            <input type="hidden" id="submissionId" name="submission_id">
            
            <div class="form-group mb-3">
                <label for="grade">Grade (out of <span id="maxPointsLabel"></span>):</label>
                <input type="number" id="grade" name="grade" step="0.1" min="0" class="form-control" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="status">Status:</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="graded">Graded</option>
                    <option value="reviewed">Reviewed (Needs Revision)</option>
                </select>
            </div>
            
            <div class="form-group mb-3">
                <label for="feedback">Feedback (optional):</label>
                <textarea id="feedback" name="feedback" class="form-control" rows="4" placeholder="Provide feedback to the student..."></textarea>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeGradingModal()">Cancel</button>
                <button type="submit" class="btn btn-success">Save Grade</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openGradingModal(submissionId) {
    console.log('openGradingModal called with ID:', submissionId);
    
    // Check if modal element exists
    const modal = document.getElementById('grading-modal-overlay');
    if (!modal) {
        console.error('Modal element not found!');
        alert('Modal element not found!');
        return;
    }
    
    console.log('Modal element found:', modal);
    
    // Show loading in modal
    const titleElement = document.getElementById('gradingModalTitle');
    if (titleElement) {
        titleElement.textContent = 'Loading...';
    }
    
    // Show modal
    modal.classList.add('show');
    console.log('Modal should now be visible');
    
    // Fetch submission details
    console.log('Fetching submission details from:', `/professor/submissions/${submissionId}/details`);
    fetch(`/professor/submissions/${submissionId}/details`)
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                populateGradingModal(data.submission);
            } else {
                alert('Error loading submission details: ' + data.message);
                closeGradingModal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading submission details: ' + error.message);
            closeGradingModal();
        });
}

function populateGradingModal(submission) {
    // Set form values
    document.getElementById('submissionId').value = submission.id;
    document.getElementById('gradingModalTitle').textContent = `Grade Assignment - ${submission.student.firstname} ${submission.student.lastname}`;
    
    // Assignment details
    if (submission.content_item) {
        document.getElementById('assignmentTitle').textContent = submission.content_item.content_title || 'Assignment';
        document.getElementById('assignmentDescription').textContent = submission.content_item.content_description || 'No description';
        
        // Handle content_data - could be string or already parsed object
        let contentData = {};
        if (submission.content_item.content_data) {
            if (typeof submission.content_item.content_data === 'string') {
                try {
                    contentData = JSON.parse(submission.content_item.content_data);
                } catch (e) {
                    console.warn('Failed to parse content_data:', e);
                    contentData = {};
                }
            } else if (typeof submission.content_item.content_data === 'object') {
                contentData = submission.content_item.content_data;
            }
        }
        
        const maxPoints = contentData.max_points || 100;
        document.getElementById('assignmentMaxPoints').textContent = maxPoints;
        document.getElementById('maxPointsLabel').textContent = maxPoints;
        document.getElementById('assignmentDueDate').textContent = contentData.due_date || 'No due date';
        
        // Set max value for grade input
        document.getElementById('grade').setAttribute('max', maxPoints);
    } else {
        document.getElementById('assignmentTitle').textContent = submission.module.module_name + ' Assignment';
        document.getElementById('assignmentDescription').textContent = 'Module assignment';
        document.getElementById('assignmentMaxPoints').textContent = '100';
        document.getElementById('maxPointsLabel').textContent = '100';
        document.getElementById('assignmentDueDate').textContent = 'No due date';
        document.getElementById('grade').setAttribute('max', 100);
    }
    
    // Student submission details
    document.getElementById('studentName').textContent = submission.student.firstname + ' ' + submission.student.lastname;
    document.getElementById('submissionDate').textContent = new Date(submission.submitted_at).toLocaleString();
    
    // Files
    let filesText = 'No files';
    const fileSelector = document.getElementById('fileSelector');
    // Clear existing options except the first one
    while (fileSelector.options.length > 1) {
        fileSelector.remove(1);
    }
    
    // Hide all viewers initially
    document.getElementById('pdfViewer').style.display = 'none';
    document.getElementById('imageViewer').style.display = 'none';
    document.getElementById('textViewer').style.display = 'none';
    document.getElementById('unsupportedFileType').style.display = 'none';
    document.getElementById('noFilesMessage').style.display = 'flex';
    
    if (submission.processed_files && submission.processed_files.length > 0) {
        // Update files text
        filesText = submission.processed_files.map(file => file.original_name || file.name).join(', ');
        document.getElementById('noFilesMessage').style.display = 'none';
        
        // Populate file selector
        submission.processed_files.forEach((file, index) => {
            const option = document.createElement('option');
            option.value = index;
            option.textContent = file.original_name || file.name || `File ${index + 1}`;
            fileSelector.appendChild(option);
        });
        
        // Add event listener for file selection
        fileSelector.onchange = function() {
            const selectedIndex = this.value;
            if (selectedIndex === '') {
                // No file selected
                document.getElementById('pdfViewer').style.display = 'none';
                document.getElementById('imageViewer').style.display = 'none';
                document.getElementById('textViewer').style.display = 'none';
                document.getElementById('unsupportedFileType').style.display = 'none';
                document.getElementById('noFilesMessage').style.display = 'flex';
                return;
            }
            
            const selectedFile = submission.processed_files[selectedIndex];
            loadFilePreview(selectedFile, submission.id);
        };
        
        // Automatically load the first file if available
        if (submission.processed_files.length > 0) {
            fileSelector.value = 0;
            loadFilePreview(submission.processed_files[0], submission.id);
        }
    } else {
        // No files available
        document.getElementById('fileViewerSection').style.display = 'none';
    }
    
    document.getElementById('submissionFiles').textContent = filesText;
    
    // Comments
    document.getElementById('submissionComments').textContent = submission.comments || 'No comments';
    
    // Current grade and feedback if exists
    if (submission.grade !== null) {
        document.getElementById('grade').value = submission.grade;
    }
    document.getElementById('feedback').value = submission.feedback || '';
    document.getElementById('status').value = submission.status || 'graded';
}

function closeGradingModal() {
    const modal = document.getElementById('grading-modal-overlay');
    if (modal) {
        modal.classList.remove('show');
    }
    document.getElementById('gradingForm').reset();
    
    // Reset file viewers
    document.getElementById('pdfViewer').src = '';
    document.getElementById('imagePreview').src = '';
    document.getElementById('textViewer').textContent = '';
}

// Function to load and display file preview
function loadFilePreview(file, submissionId) {
    // Hide all viewers first
    document.getElementById('pdfViewer').style.display = 'none';
    document.getElementById('imageViewer').style.display = 'none';
    document.getElementById('textViewer').style.display = 'none';
    document.getElementById('unsupportedFileType').style.display = 'none';
    document.getElementById('noFilesMessage').style.display = 'none';
    
    const fileUrl = `/professor/submissions/${submissionId}/download?file=${encodeURIComponent(file.path || file.name)}`;
    const downloadLink = document.getElementById('downloadFileLink');
    downloadLink.href = fileUrl;
    
    // Detect file type based on extension or mime type
    const fileName = file.original_name || file.name || '';
    const fileExtension = fileName.split('.').pop().toLowerCase();
    const mimeType = file.mime || '';
    
    // PDF files
    if (fileExtension === 'pdf' || mimeType.includes('pdf')) {
        document.getElementById('pdfViewer').src = fileUrl;
        document.getElementById('pdfViewer').style.display = 'block';
    }
    // Image files
    else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'].includes(fileExtension) || 
             mimeType.includes('image/')) {
        document.getElementById('imagePreview').src = fileUrl;
        document.getElementById('imageViewer').style.display = 'block';
    }
    // Text files
    else if (['txt', 'md', 'html', 'css', 'js', 'json', 'xml', 'csv'].includes(fileExtension) ||
             mimeType.includes('text/')) {
        // Fetch text content
        fetch(fileUrl)
            .then(response => response.text())
            .then(text => {
                document.getElementById('textViewer').textContent = text;
                document.getElementById('textViewer').style.display = 'block';
            })
            .catch(error => {
                console.error('Error loading text file:', error);
                document.getElementById('unsupportedFileType').style.display = 'block';
            });
    }
    // Unsupported file types
    else {
        document.getElementById('unsupportedFileType').style.display = 'block';
    }
}

// Handle grading form submission
document.getElementById('gradingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submissionId = document.getElementById('submissionId').value;
    
    fetch(`/professor/submissions/${submissionId}/grade`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Grade saved successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving grade');
    });
});

// Click outside modal to close
document.getElementById('grading-modal-overlay').addEventListener('click', function(e) {
    if (e.target === this) {
        closeGradingModal();
    }
});

// Enhanced search functionality for professor submissions
document.addEventListener('DOMContentLoaded', function() {
    // Get the universal search input from the header
    const searchInput = document.getElementById('universalSearchInput');
    
    if (searchInput) {
        // Clear any existing event listeners and add our custom one
        searchInput.removeEventListener('input', handleSearchInput);
        searchInput.addEventListener('input', function() {
            filterSubmissionCards(this.value);
        });
        
        // Override the global search to work with submissions page
        window.handleSearchInput = function() {
            const searchTerm = searchInput.value;
            filterSubmissionCards(searchTerm);
        };
    }
    
    // Function to filter submission cards
    function filterSubmissionCards(searchTerm) {
        const cards = document.querySelectorAll('.card.h-100');
        const searchLower = searchTerm.toLowerCase().trim();
        
        if (searchLower === '') {
            // Show all cards if search is empty
            cards.forEach(card => {
                card.style.display = 'block';
                card.closest('.col-md-6').style.display = 'block';
            });
            return;
        }
        
        let visibleCount = 0;
        
        cards.forEach(card => {
            const cardContent = card.textContent.toLowerCase();
            const studentName = card.querySelector('.card-title')?.textContent?.toLowerCase() || '';
            const studentEmail = card.querySelector('.text-muted')?.textContent?.toLowerCase() || '';
            
            // Check if search term matches student name, email, or any content
            const matches = studentName.includes(searchLower) || 
                          studentEmail.includes(searchLower) || 
                          cardContent.includes(searchLower);
            
            if (matches) {
                card.style.display = 'block';
                card.closest('.col-md-6').style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
                card.closest('.col-md-6').style.display = 'none';
            }
        });
        
        // Show/hide empty state message
        updateEmptyState(visibleCount, searchTerm);
    }
    
    // Function to update empty state
    function updateEmptyState(visibleCount, searchTerm) {
        let emptyState = document.querySelector('.search-empty-state');
        
        if (visibleCount === 0 && searchTerm.trim() !== '') {
            // Create empty state if it doesn't exist
            if (!emptyState) {
                emptyState = document.createElement('div');
                emptyState.className = 'search-empty-state text-center py-5';
                emptyState.innerHTML = `
                    <div class="bg-light border border-2 border-dashed rounded-4 p-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <div class="text-muted h5 mb-2">No submissions found</div>
                        <small class="text-muted">Try adjusting your search terms</small>
                    </div>
                `;
                
                // Insert after the page header
                const pageHeader = document.querySelector('.page-header, .d-flex.justify-content-between');
                if (pageHeader) {
                    pageHeader.insertAdjacentElement('afterend', emptyState);
                }
            }
            emptyState.style.display = 'block';
        } else if (emptyState) {
            emptyState.style.display = 'none';
        }
    }
});
</script>
@endpush

@section('title', 'Assignment Submissions')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
  /* Assignment submissions page styles with Bootstrap */
  .submissions-container {
    background: #fff;
    padding: 40px 20px 60px;
    margin: 40px 0 0 0;
    max-width: 1400px;
    width: 100%;
    box-sizing: border-box;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  }

  .back-to-modules-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

  .back-to-modules-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    color: white;
    text-decoration: none;
  }

  .assignment-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    border-left: 4px solid #667eea;
  }

  .assignment-info h2 {
    color: #2c3e50;
    margin: 0 0 10px 0;
    font-size: 1.4rem;
  }

  .assignment-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
  }

  .meta-item {
    display: flex;
    flex-direction: column;
    gap: 3px;
  }

  .meta-label {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
  }

  .meta-value {
    color: #6c757d;
    font-size: 0.95rem;
  }

  .submissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 25px;
  }

  .submission-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 15px;
    padding: 25px;
    transition: all 0.3s ease;
    border: 1px solid #e1e5e9;
    position: relative;
    overflow: hidden;
  }

  .submission-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
  }
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .file-preview {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 0.75rem;
            padding: 2rem;
            text-align: center;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .file-preview:hover {
            border-color: #0d6efd;
            background: rgba(13, 110, 253, 0.05);
        }

        .file-icon {
            font-size: 3rem;
            color: #0d6efd;
            margin-bottom: 1rem;
        }

        .file-info {
            color: #212529;
        }

        .grading-section {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .grade-input-group {
            position: relative;
            margin-bottom: 1rem;
        }

        .grade-display {
            background: linear-gradient(135deg, var(--success-color), #20c997);
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        .feedback-section {
            background: var(--light-color);
            border-radius: 0.5rem;
            padding: 1rem;
        }

        .btn-action {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-grade {
            background: linear-gradient(135deg, var(--success-color), #20c997);
            border: none;
            color: white;
        }

        .btn-grade:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
            color: white;
        }

        .btn-download {
            background: linear-gradient(135deg, var(--info-color), #17a2b8);
            border: none;
            color: white;
        }

        .btn-download:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 202, 240, 0.3);
            color: white;
        }

        .btn-view {
            background: linear-gradient(135deg, var(--primary-color), #4c84ff);
            border: none;
            color: white;
        }

        .btn-view:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
            color: white;
        }

        .submission-metadata {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .metadata-item {
            background: white;
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #e9ecef;
        }

        .metadata-label {
            font-size: 0.8rem;
            color: var(--secondary-color);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .metadata-value {
            font-size: 1rem;
            color: var(--dark-color);
            font-weight: 600;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .filter-section {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .document-viewer {
            width: 100%;
            height: 600px;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 1rem;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .stats-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid #e9ecef;
            margin-bottom: 1rem;
        }

        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
        }

        .stats-label {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="professor-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h2 mb-2">
                        <i class="bi bi-file-earmark-text me-2"></i>Assignment Submissions
                    </h1>
                    <p class="mb-0 opacity-75">Review and grade student assignment submissions</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex justify-content-md-end gap-2">
                        <button class="btn btn-light" onclick="refreshSubmissions()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filter-section">
        <form method="GET" action="{{ route('professor.submissions.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="program_id" class="form-label">
                    <i class="bi bi-mortarboard me-1"></i>Program
                </label>
                <select name="program_id" id="program_id" class="form-select">
                    <option value="">All Programs</option>
                    @foreach($assignedPrograms as $program)
                        <option value="{{ $program->program_id }}" 
                                {{ $programId == $program->program_id ? 'selected' : '' }}>
                            {{ $program->program_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="module_id" class="form-label">
                    <i class="bi bi-journals me-1"></i>Module
                </label>
                <select name="module_id" id="module_id" class="form-select">
                    <option value="">All Modules</option>
                    @foreach($modules as $module)
                        <option value="{{ $module->modules_id }}" 
                                {{ $moduleId == $module->modules_id ? 'selected' : '' }}>
                            {{ $module->module_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">
                    <i class="bi bi-flag me-1"></i>Status
                </label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="graded" {{ $status === 'graded' ? 'selected' : '' }}>Graded</option>
                    <option value="reviewed" {{ $status === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <i class="bi bi-file-earmark-text stats-icon text-primary"></i>
                <div class="stats-number">{{ $submissions->total() }}</div>
                <div class="stats-label">Total Submissions</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="bi bi-clock-history stats-icon text-warning"></i>
                <div class="stats-number">{{ $submissions->where('status', 'pending')->count() }}</div>
                <div class="stats-label">Pending Review</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="bi bi-check-circle stats-icon text-success"></i>
                <div class="stats-number">{{ $submissions->where('status', 'graded')->count() }}</div>
                <div class="stats-label">Graded</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="bi bi-eye-check stats-icon text-info"></i>
                <div class="stats-number">{{ $submissions->where('status', 'reviewed')->count() }}</div>
                <div class="stats-label">Reviewed</div>
            </div>
        </div>
    </div>

    <!-- Submissions List -->
    @if($submissions->count() > 0)
        @foreach($submissions as $submission)
            <div class="submission-card">
                <div class="submission-header">
                    <div class="student-info">
                        <div class="student-avatar">
                            {{ strtoupper(substr($submission->student->firstname ?? 'S', 0, 1)) }}{{ strtoupper(substr($submission->student->lastname ?? 'T', 0, 1)) }}
                        </div>
                        <div class="student-details">
                            <h5>{{ $submission->student->firstname ?? 'Unknown' }} {{ $submission->student->lastname ?? 'Student' }}</h5>
                            <p class="text-muted mb-0">
                                <i class="bi bi-envelope me-1"></i>{{ $submission->student->email ?? ($submission->student->user->email ?? 'No email') }}
                            </p>
                        </div>
                    </div>
                    <div class="submission-status status-{{ $submission->status }}">
                        <i class="bi bi-{{ $submission->status === 'pending' ? 'clock' : ($submission->status === 'graded' ? 'check-circle' : 'eye-check') }} me-1"></i>
                        {{ ucfirst($submission->status) }}
                    </div>
                </div>

                <div class="submission-body">
                    <!-- Assignment Metadata -->
                    <div class="submission-metadata">
                        <div class="metadata-item">
                            <div class="metadata-label">Program</div>
                            <div class="metadata-value">{{ $submission->program->program_name ?? 'N/A' }}</div>
                        </div>
                        <div class="metadata-item">
                            <div class="metadata-label">Module</div>
                            <div class="metadata-value">{{ $submission->module->module_name ?? 'N/A' }}</div>
                        </div>
                        <div class="metadata-item">
                            <div class="metadata-label">Submitted</div>
                            <div class="metadata-value">{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y H:i') : 'N/A' }}</div>
                        </div>
                        <div class="metadata-item">
                            <div class="metadata-label">Assignment Title</div>
                            <div class="metadata-value">{{ $submission->assignment_title ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <!-- Assignment Description -->
                    @if($submission->assignment_description)
                        <div class="assignment-details">
                            <h6><i class="bi bi-card-text me-1"></i>Assignment Description</h6>
                            <p class="mb-0">{{ $submission->assignment_description }}</p>
                        </div>
                    @endif

                    <!-- Files Section -->
                    @if($submission->processed_files && count($submission->processed_files) > 0)
                        <div class="mb-3">
                            <h6><i class="bi bi-file-earmark me-1"></i>Submitted Files</h6>
                            <div class="row">
                                @foreach($submission->processed_files as $file)
                                    <div class="col-md-6 col-lg-4 mb-2">
                                        <div class="file-preview">
                                            <div class="file-icon">
                                                @php
                                                    $fileName = $file['name'] ?? $file['original_filename'] ?? 'Unknown File';
                                                @endphp
                                                <i class="bi bi-file-earmark-{{ str_contains($fileName, '.pdf') ? 'pdf' : (str_contains($fileName, '.doc') ? 'word' : 'text') }}"></i>
                                            </div>
                                            <div class="file-info">
                                                <strong>{{ $fileName }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $file['size'] ?? 'Unknown size' }}</small>
                                                <br>
                                                <a href="{{ $file['path'] ?? $file['file_path'] ?? '#' }}" class="btn btn-sm btn-download btn-action mt-2" target="_blank">
                                                    <i class="bi bi-download me-1"></i>Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Grading Section -->
                    <div class="grading-section">
                        @if($submission->grade !== null)
                            <!-- Already Graded -->
                            <div class="grade-display">
                                <h4 class="mb-1">
                                    <i class="bi bi-trophy me-2"></i>Grade: {{ $submission->grade }}/100
                                </h4>
                                <small>Graded on {{ $submission->graded_at ? $submission->graded_at->format('M d, Y H:i') : 'N/A' }}</small>
                            </div>
                            
                            @if($submission->feedback)
                                <div class="feedback-section">
                                    <h6><i class="bi bi-chat-text me-1"></i>Feedback</h6>
                                    <p class="mb-0">{{ $submission->feedback }}</p>
                                </div>
                            @endif

                            <div class="text-center mt-3">
                                <button class="btn btn-outline-primary btn-action" onclick="openGradingModal({{ $submission->id }})">
                                    <i class="bi bi-pencil me-1"></i>Edit Grade
                                </button>
                            </div>
                        @else
                            <!-- Not Graded Yet -->
                            <div class="text-center">
                                <h6 class="text-muted mb-3">
                                    <i class="bi bi-exclamation-circle me-1"></i>This submission has not been graded yet
                                </h6>
                                <button class="btn btn-grade btn-action" onclick="openGradingModal({{ $submission->id }})">
                                    <i class="bi bi-plus-circle me-1"></i>Grade Submission
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $submissions->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <i class="bi bi-file-earmark-text"></i>
            <h4>No submissions found</h4>
            <p>There are no assignment submissions matching your current filters.</p>
            <a href="{{ route('professor.submissions.index') }}" class="btn btn-primary">
                <i class="bi bi-arrow-clockwise me-1"></i>Clear Filters
            </a>
        </div>
    @endif
</div>

<!-- Grade Submission Modal -->
<div class="modal fade" id="gradeModal" tabindex="-1" aria-labelledby="gradeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gradeModalLabel">
                    <i class="bi bi-clipboard-check me-2"></i>Grade Submission
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="gradeForm">
                    @csrf
                    <input type="hidden" id="submissionId" name="submission_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="grade-input-group">
                                <label for="gradeInput" class="form-label">
                                    <i class="bi bi-trophy me-1"></i>Grade (0-100)
                                </label>
                                <input type="number" class="form-control" id="gradeInput" name="grade" 
                                       min="0" max="100" step="0.01" required>
                                <div class="invalid-feedback">
                                    Please provide a valid grade between 0 and 100.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="statusInput" class="form-label">
                                <i class="bi bi-flag me-1"></i>Status
                            </label>
                            <select class="form-select" id="statusInput" name="status" required>
                                <option value="graded">Graded</option>
                                <option value="reviewed">Reviewed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label for="feedbackInput" class="form-label">
                            <i class="bi bi-chat-text me-1"></i>Feedback (Optional)
                        </label>
                        <textarea class="form-control" id="feedbackInput" name="feedback" 
                                  rows="4" placeholder="Provide feedback to the student..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-success" onclick="submitGrade()">
                    <i class="bi bi-check-circle me-1"></i>Save Grade
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
