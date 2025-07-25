@extends('admin.admin-dashboard-layout')

@section('title', 'Student Assignment Submissions - A.R.T.C Admin')

@push('styles')
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Your custom CSS -->
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

        .admin-header {
            background: linear-gradient(135deg, var(--primary-color), #4c84ff);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
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

        .grade-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 50rem;
            font-weight: 600;
            font-size: 1rem;
        }

        .grade-excellent {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .grade-good {
            background: linear-gradient(135deg, #17a2b8, #20c997);
            color: white;
        }

        .grade-average {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
        }

        .grade-needs-improvement {
            background: linear-gradient(135deg, #dc3545, #e83e8c);
            color: white;
        }

        @media (max-width: 768px) {
            .submission-metadata {
                grid-template-columns: 1fr;
            }
            
            .admin-header {
                padding: 1.5rem 0;
            }
            
            .submission-card {
                margin-bottom: 1rem;
            }
        }
    </style>
@endpush
@section('content')
    <div class="admin-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h2 mb-1">
                                <i class="bi bi-file-earmark-text me-2"></i>
                                Assignment Submissions
                            </h1>
                            <p class="mb-0 opacity-75">Review and grade student assignment submissions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="{{ route('admin.submissions') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="program_filter" class="form-label">Program</label>
                        <select name="program_id" id="program_filter" class="form-select">
                            <option value="">All Programs</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->program_id }}" {{ request('program_id') == $program->program_id ? 'selected' : '' }}>
                                    {{ $program->program_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="module_filter" class="form-label">Module</label>
                        <select name="module_id" id="module_filter" class="form-select">
                            <option value="">All Modules</option>
                            @foreach($modules as $module)
                                <option value="{{ $module->module_id }}" {{ request('module_id') == $module->module_id ? 'selected' : '' }}>
                                    {{ $module->module_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status_filter" class="form-label">Status</label>
                        <select name="status" id="status_filter" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Review</option>
                            <option value="graded" {{ request('status') == 'graded' ? 'selected' : '' }}>Graded</option>
                            <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-funnel me-2"></i>Filter
                            </button>
                            <a href="{{ route('admin.submissions') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Submissions List -->
        @if($submissions->count() > 0)
            <div class="row">
                @foreach($submissions as $submission)
                    <div class="col-12">
                        <div class="submission-card">
                            <div class="submission-header">
                                <div class="flex-grow-1">
                                    <div class="student-info">
                                        <div class="student-avatar">
                                            @if($submission->student && $submission->student->user)
                                                {{ substr($submission->student->user->user_firstname ?? 'N', 0, 1) }}{{ substr($submission->student->user->user_lastname ?? 'A', 0, 1) }}
                                            @else
                                                NA
                                            @endif
                                        </div>
                                        <div class="student-details">
                                            <h5>
                                                @if($submission->student && $submission->student->user)
                                                    {{ $submission->student->user->user_firstname ?? 'Unknown' }} {{ $submission->student->user->user_lastname ?? 'Student' }}
                                                @else
                                                    Unknown Student
                                                @endif
                                            </h5>
                                            <div class="text-muted">
                                                Student ID: {{ $submission->student->student_id ?? 'N/A' }} | 
                                                Program: {{ $submission->program->program_name ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="submission-status status-{{ $submission->status }}">
                                    <i class="bi bi-{{ $submission->status == 'graded' ? 'check-circle' : ($submission->status == 'reviewed' ? 'eye' : 'clock') }} me-2"></i>
                                    {{ ucfirst($submission->status) }}
                                </div>
                            </div>

                            <div class="submission-body">
                                <!-- Assignment Details -->
                                <div class="assignment-details">
                                    <h6 class="mb-2">
                                        <i class="bi bi-file-earmark-text me-2"></i>
                                        Module: {{ $submission->module->module_name }}
                                    </h6>
                                    @if($submission->comments)
                                        <p class="mb-0 text-muted">{{ $submission->comments }}</p>
                                    @endif
                                </div>

                                <!-- Submission Metadata -->
                                <div class="submission-metadata">
                                    <div class="metadata-item">
                                        <div class="metadata-label">Submitted</div>
                                        <div class="metadata-value">{{ $submission->submitted_at->format('M d, Y - h:i A') }}</div>
                                    </div>
                                    @if($submission->status == 'graded' && $submission->grade !== null)
                                        <div class="metadata-item">
                                            <div class="metadata-label">Grade</div>
                                            <div class="metadata-value">
                                                <span class="grade-badge grade-{{ $submission->grade >= 90 ? 'excellent' : ($submission->grade >= 80 ? 'good' : ($submission->grade >= 70 ? 'average' : 'needs-improvement')) }}">
                                                    {{ $submission->grade }}/100
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                    @if($submission->graded_at)
                                        <div class="metadata-item">
                                            <div class="metadata-label">Graded On</div>
                                            <div class="metadata-value">{{ $submission->graded_at->format('M d, Y - h:i A') }}</div>
                                        </div>
                                    @endif
                                    <div class="metadata-item">
                                        <div class="metadata-label">Files</div>
                                        <div class="metadata-value">{{ count($submission->files ?? []) }} file(s)</div>
                                    </div>
                                </div>

                                <!-- Submitted Files -->
                                @if($submission->files && count($submission->files) > 0)
                                    <div class="row g-3 mb-3">
                                        @foreach($submission->files as $file)
                                            <div class="col-md-6">
                                                <div class="file-preview">
                                                    <div class="file-icon">
                                                        <i class="bi bi-{{ str_contains($file['type'], 'pdf') ? 'file-earmark-pdf' : (str_contains($file['type'], 'image') ? 'file-earmark-image' : (str_contains($file['type'], 'word') ? 'file-earmark-word' : 'file-earmark')) }}"></i>
                                                    </div>
                                                    <div class="file-info">
                                                        <h6 class="mb-1">{{ $file['original_filename'] ?? ($file['original_name'] ?? ($file['name'] ?? 'File')) }}</h6>
                                                        <p class="mb-2 text-muted">{{ round($file['size'] / 1024, 2) }} KB</p>
                                                        <div class="d-flex gap-2 justify-content-center">
                                                            <a href="{{ asset('storage/' . $file['path']) }}" target="_blank" class="btn btn-view btn-sm">
                                                                <i class="bi bi-eye me-1"></i>View
                                                            </a>
                                                            <a href="{{ asset('storage/' . $file['path']) }}" download class="btn btn-download btn-sm">
                                                                <i class="bi bi-download me-1"></i>Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Current Feedback (if exists) -->
                                @if($submission->feedback)
                                    <div class="feedback-section">
                                        <h6 class="mb-2">
                                            <i class="bi bi-chat-text me-2"></i>Previous Feedback
                                        </h6>
                                        <p class="mb-0">{{ $submission->feedback }}</p>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="d-flex gap-2 justify-content-end mt-3">
                                    @if($submission->status != 'graded')
                                        <button type="button" class="btn btn-grade btn-action" onclick="openGradingModal({{ $submission->id }}, '{{ ($submission->student && $submission->student->user) ? $submission->student->user->user_firstname . ' ' . $submission->student->user->user_lastname : 'Unknown Student' }}', {{ $submission->grade ?? 'null' }}, '{{ addslashes($submission->feedback ?? '') }}')">
                                            <i class="bi bi-award me-2"></i>Grade Assignment
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-warning btn-action" onclick="openGradingModal({{ $submission->id }}, '{{ ($submission->student && $submission->student->user) ? $submission->student->user->user_firstname . ' ' . $submission->student->user->user_lastname : 'Unknown Student' }}', {{ $submission->grade ?? 'null' }}, '{{ addslashes($submission->feedback ?? '') }}')">
                                            <i class="bi bi-pencil me-2"></i>Update Grade
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $submissions->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-file-earmark-x"></i>
                <h4>No Submissions Found</h4>
                <p>There are no assignment submissions matching your criteria.</p>
            </div>
        @endif
    </div>

    <!-- Grading Modal -->
    <div class="modal fade" id="gradingModal" tabindex="-1" aria-labelledby="gradingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="gradingModalLabel">Grade Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="gradingForm" onsubmit="submitGrade(event)">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" id="submissionId" name="submission_id">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="grade" class="form-label">Grade (0-100)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="grade" name="grade" min="0" max="100" step="0.1" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gradeStatus" class="form-label">Status</label>
                                    <select class="form-select" id="gradeStatus" name="status">
                                        <option value="graded">Graded</option>
                                        <option value="reviewed">Reviewed (needs revision)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="feedback" class="form-label">Feedback</label>
                            <textarea class="form-control" id="feedback" name="feedback" rows="5" placeholder="Provide detailed feedback to help the student improve..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Save Grade & Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function openGradingModal(submissionId, studentName, currentGrade, currentFeedback) {
            document.getElementById('submissionId').value = submissionId;
            document.getElementById('gradingModalLabel').textContent = `Grade Assignment - ${studentName}`;
            
            if (currentGrade !== null && currentGrade !== 'null') {
                document.getElementById('grade').value = currentGrade;
            } else {
                document.getElementById('grade').value = '';
            }
            
            document.getElementById('feedback').value = currentFeedback || '';
            
            const modal = new bootstrap.Modal(document.getElementById('gradingModal'));
            modal.show();
        }

        function submitGrade(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            const submissionId = document.getElementById('submissionId').value;
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';
            submitBtn.disabled = true;
            
            fetch(`/admin/submissions/${submissionId}/grade`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
                    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                    alertDiv.innerHTML = `
                        <i class="bi bi-check-circle me-2"></i>
                        Grade saved successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(alertDiv);
                    
                    // Close modal and reload page
                    bootstrap.Modal.getInstance(document.getElementById('gradingModal')).hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    throw new Error(data.message || 'An error occurred');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Show error message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
                alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                alertDiv.innerHTML = `
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error: ${error.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(alertDiv);
            })
            .finally(() => {
                // Restore button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                const alertInstance = bootstrap.Alert.getOrCreateInstance(alert);
                alertInstance.close();
            });
        }, 5000);
    </script>
@endpush