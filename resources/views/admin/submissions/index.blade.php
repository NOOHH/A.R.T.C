@extends('admin.admin-dashboard-layout')

@section('title', 'Assignment Submissions')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
    .submissions-container {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin: 20px 0;
    }

    .submissions-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .submissions-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .filters-section {
        background: #f8f9fa;
        padding: 1.5rem;
        border-bottom: 1px solid #dee2e6;
    }

    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-submitted { background: #fff3cd; color: #856404; }
    .status-graded { background: #d4edda; color: #155724; }
    .status-returned { background: #f8d7da; color: #721c24; }

    .submission-row:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }

    .grade-modal .modal-dialog {
        max-width: 600px;
    }

    .file-download-btn {
        transition: all 0.3s ease;
    }

    .file-download-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }
</style>
@endpush

@section('content')
<div class="submissions-container">
    <!-- Header -->
    <div class="submissions-header">
        <h1><i class="bi bi-file-earmark-check"></i> Assignment Submissions</h1>
        <p>Review and grade student assignment submissions</p>
    </div>

    <!-- Filters Section -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.submissions.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="graded" {{ request('status') == 'graded' ? 'selected' : '' }}>Graded</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="program_id" class="form-label">Program</label>
                <select name="program_id" id="program_id" class="form-select">
                    <option value="">All Programs</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->program_id }}" {{ request('program_id') == $program->program_id ? 'selected' : '' }}>
                            {{ $program->program_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Submissions Table -->
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Student</th>
                    <th>Assignment</th>
                    <th>Submitted</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Grade</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $submission)
                <tr class="submission-row">
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-2">
                                {{ strtoupper(substr($submission->student->first_name ?? 'S', 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-medium">{{ $submission->student->first_name ?? 'Unknown' }} {{ $submission->student->last_name ?? 'Student' }}</div>
                                <small class="text-muted">{{ $submission->student->email ?? 'No email' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <div class="fw-medium">{{ $submission->contentItem->content_title ?? 'Unknown Assignment' }}</div>
                            <small class="text-muted">{{ $submission->contentItem->content_type ?? 'Assignment' }}</small>
                        </div>
                    </td>
                    <td>
                        <div>{{ $submission->submitted_at->format('M d, Y') }}</div>
                        <small class="text-muted">{{ $submission->submitted_at->format('g:i A') }}</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-file-earmark me-2"></i>
                            <div>
                                <div class="fw-medium">{{ $submission->original_filename }}</div>
                                <small class="text-muted">{{ $submission->file_size_human }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $submission->status }}">
                            {{ ucfirst($submission->status) }}
                        </span>
                    </td>
                    <td>
                        @if($submission->grade)
                            <div class="fw-bold text-success">{{ $submission->grade }}%</div>
                            @if($submission->graded_at)
                                <small class="text-muted">{{ $submission->graded_at->format('M d, Y') }}</small>
                            @endif
                        @else
                            <span class="text-muted">Not graded</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.submissions.download', $submission->id) }}" 
                               class="btn btn-sm btn-outline-primary file-download-btn" 
                               title="Download File">
                                <i class="bi bi-download"></i>
                            </a>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-success" 
                                    onclick="openGradeModal({{ $submission->id }}, '{{ $submission->contentItem->content_title ?? 'Assignment' }}', {{ $submission->grade ?? 'null' }}, '{{ addslashes($submission->feedback ?? '') }}')"
                                    title="Grade Assignment">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-info" 
                                    onclick="viewSubmissionDetails({{ $submission->id }})"
                                    title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                        <h5 class="mt-3 text-muted">No Submissions Found</h5>
                        <p class="text-muted">No assignment submissions match your current filters.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($submissions->hasPages())
    <div class="d-flex justify-content-center py-3">
        {{ $submissions->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Grade Assignment Modal -->
<div class="modal fade grade-modal" id="gradeModal" tabindex="-1" aria-labelledby="gradeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gradeModalLabel">Grade Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="gradeForm">
                @csrf
                <input type="hidden" id="submission_id" name="submission_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assignment_title" class="form-label">Assignment</label>
                        <input type="text" class="form-control" id="assignment_title" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="grade" class="form-label">Grade (0-100)</label>
                        <input type="number" class="form-control" id="grade" name="grade" min="0" max="100" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="feedback" class="form-label">Feedback</label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="4" placeholder="Provide feedback to the student..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Save Grade
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Submission Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Submission Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailsModalBody">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Grade assignment modal
    window.openGradeModal = function(submissionId, assignmentTitle, currentGrade, currentFeedback) {
        document.getElementById('submission_id').value = submissionId;
        document.getElementById('assignment_title').value = assignmentTitle;
        document.getElementById('grade').value = currentGrade || '';
        document.getElementById('feedback').value = currentFeedback || '';
        
        const modal = new bootstrap.Modal(document.getElementById('gradeModal'));
        modal.show();
    };

    // Grade form submission
    document.getElementById('gradeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submissionId = document.getElementById('submission_id').value;
        const formData = new FormData(this);
        
        fetch(`/admin/submissions/${submissionId}/grade`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Assignment graded successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while grading the assignment.');
        });
    });

    // View submission details
    window.viewSubmissionDetails = function(submissionId) {
        const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
        const modalBody = document.getElementById('detailsModalBody');
        
        modalBody.innerHTML = `
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        
        modal.show();
        
        fetch(`/admin/submissions/${submissionId}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displaySubmissionDetails(data.submission);
                } else {
                    modalBody.innerHTML = '<div class="alert alert-danger">Error loading details: ' + data.message + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                modalBody.innerHTML = '<div class="alert alert-danger">Error loading submission details.</div>';
            });
    };

    function displaySubmissionDetails(submission) {
        const modalBody = document.getElementById('detailsModalBody');
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Student Information</h6>
                    <p><strong>Name:</strong> ${submission.student_name}</p>
                    <p><strong>Email:</strong> ${submission.student_email}</p>
                </div>
                <div class="col-md-6">
                    <h6>Submission Information</h6>
                    <p><strong>Assignment:</strong> ${submission.assignment_title}</p>
                    <p><strong>Submitted:</strong> ${submission.submitted_at}</p>
                    <p><strong>Status:</strong> <span class="status-badge status-${submission.status}">${submission.status}</span></p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <h6>File Information</h6>
                    <p><strong>File:</strong> ${submission.original_filename}</p>
                    <p><strong>Size:</strong> ${submission.file_size_human}</p>
                    ${submission.submission_notes ? `<p><strong>Notes:</strong> ${submission.submission_notes}</p>` : ''}
                </div>
            </div>
            ${submission.grade ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Grade Information</h6>
                    <p><strong>Grade:</strong> ${submission.grade}%</p>
                    <p><strong>Graded By:</strong> ${submission.graded_by_name}</p>
                    <p><strong>Graded At:</strong> ${submission.graded_at}</p>
                    ${submission.feedback ? `<p><strong>Feedback:</strong> ${submission.feedback}</p>` : ''}
                </div>
            </div>
            ` : ''}
        `;
    }
});
</script>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1rem;
}
</style>
@endpush