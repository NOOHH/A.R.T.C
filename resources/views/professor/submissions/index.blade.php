@extends('professor.professor-layouts.professor-layout')

@section('title', 'Assignment Submissions')

@push('styles')
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #0dcaf0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
        }

        .professor-header {
            background: linear-gradient(135deg, var(--primary-color), #4c84ff);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 1rem;
        }

        .submission-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }

        .submission-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(13, 110, 253, 0.15);
        }

        .submission-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: between;
            align-items: flex-start;
        }

        .student-info {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .student-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), #4c84ff);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.2rem;
            margin-right: 1rem;
        }

        .student-details h5 {
            margin: 0;
            color: var(--dark-color);
            font-weight: 600;
        }

        .student-details .text-muted {
            font-size: 0.9rem;
        }

        .submission-status {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 50rem;
            font-size: 0.875rem;
            font-weight: 500;
            margin-left: auto;
        }

        .status-pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning-color);
        }

        .status-graded {
            background-color: rgba(25, 135, 84, 0.1);
            color: var(--success-color);
        }

        .status-reviewed {
            background-color: rgba(13, 202, 240, 0.1);
            color: var(--info-color);
        }

        .submission-body {
            padding: 1.5rem;
        }

        .assignment-details {
            background: var(--light-color);
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
            border-color: var(--primary-color);
            background: rgba(13, 110, 253, 0.05);
        }

        .file-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .file-info {
            color: var(--dark-color);
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
                            {{ strtoupper(substr($submission->student->first_name ?? 'S', 0, 1)) }}{{ strtoupper(substr($submission->student->last_name ?? 'T', 0, 1)) }}
                        </div>
                        <div class="student-details">
                            <h5>{{ $submission->student->first_name ?? 'Unknown' }} {{ $submission->student->last_name ?? 'Student' }}</h5>
                            <p class="text-muted mb-0">
                                <i class="bi bi-envelope me-1"></i>{{ $submission->student->email ?? 'No email' }}
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
                                <button class="btn btn-outline-primary btn-action" onclick="editGrade({{ $submission->id }}, {{ $submission->grade }}, '{{ addslashes($submission->feedback ?? '') }}', '{{ $submission->status }}')">
                                    <i class="bi bi-pencil me-1"></i>Edit Grade
                                </button>
                            </div>
                        @else
                            <!-- Not Graded Yet -->
                            <div class="text-center">
                                <h6 class="text-muted mb-3">
                                    <i class="bi bi-exclamation-circle me-1"></i>This submission has not been graded yet
                                </h6>
                                <button class="btn btn-grade btn-action" onclick="gradeSubmission({{ $submission->id }})">
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

@push('scripts')
<script>
// Grade submission function
function gradeSubmission(submissionId) {
    document.getElementById('submissionId').value = submissionId;
    document.getElementById('gradeInput').value = '';
    document.getElementById('feedbackInput').value = '';
    document.getElementById('statusInput').value = 'graded';
    
    const modal = new bootstrap.Modal(document.getElementById('gradeModal'));
    modal.show();
}

// Edit existing grade
function editGrade(submissionId, currentGrade, currentFeedback, currentStatus) {
    document.getElementById('submissionId').value = submissionId;
    document.getElementById('gradeInput').value = currentGrade;
    document.getElementById('feedbackInput').value = currentFeedback;
    document.getElementById('statusInput').value = currentStatus;
    
    const modal = new bootstrap.Modal(document.getElementById('gradeModal'));
    modal.show();
}

// Submit grade
function submitGrade() {
    const form = document.getElementById('gradeForm');
    const formData = new FormData(form);
    const submissionId = document.getElementById('submissionId').value;

    // Show loading state
    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';
    submitBtn.disabled = true;

    fetch(`/professor/submissions/${submissionId}/grade`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('gradeModal')).hide();
            
            // Show success message
            showAlert('Grade saved successfully!', 'success');
            
            // Reload page to reflect changes
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while saving the grade.', 'danger');
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Refresh submissions
function refreshSubmissions() {
    window.location.reload();
}

// Show alert function
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
