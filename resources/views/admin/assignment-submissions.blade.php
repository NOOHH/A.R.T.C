@extends('admin.admin-dashboard.admin-dashboard-layout')

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

  /* Grading Modal */
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
        <a href="{{ route('admin.modules.index') }}" class="btn btn-lg text-white fw-semibold px-4 py-2 rounded-pill shadow back-to-modules-btn">
            <i class="fas fa-arrow-left me-2"></i>Back to Modules
        </a>
    </div>

    @if($assignment)
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-file-text me-2"></i>{{ $assignment->module_name }}
                </h2>
                <p class="card-text">{{ $assignment->module_description }}</p>
                
                <div class="row">
                    @if(!empty($assignment->content_data['due_date']))
                    <div class="col-md-3 mb-3">
                        <div class="bg-light p-3 rounded">
                            <small class="text-muted d-block">Due Date</small>
                            <strong>{{ \Carbon\Carbon::parse($assignment->content_data['due_date'])->format('M d, Y g:i A') }}</strong>
                        </div>
                    </div>
                    @endif
                    
                    @if(!empty($assignment->content_data['max_points']))
                    <div class="col-md-3 mb-3">
                        <div class="bg-light p-3 rounded">
                            <small class="text-muted d-block">Max Points</small>
                            <strong>{{ $assignment->content_data['max_points'] }}</strong>
                        </div>
                    </div>
                    @endif

                    <div class="col-md-3 mb-3">
                        <div class="bg-light p-3 rounded">
                            <small class="text-muted d-block">Program</small>
                            <strong>{{ $assignment->program->program_name }}</strong>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="bg-light p-3 rounded">
                            <small class="text-muted d-block">Total Submissions</small>
                            <strong>{{ $submissions->count() }}</strong>
                        </div>
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
                        if (!empty($assignment->content_data['due_date'])) {
                            $dueDate = \Carbon\Carbon::parse($assignment->content_data['due_date']);
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
                                        {{ strtoupper(substr($submission->student->first_name ?? 'S', 0, 1)) }}{{ strtoupper(substr($submission->student->last_name ?? 'T', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1">{{ $submission->student->first_name ?? 'Unknown' }} {{ $submission->student->last_name ?? 'Student' }}</h5>
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

                                @if($submission->notes)
                                    <div class="bg-light p-3 rounded mb-3">
                                        <small class="text-muted d-block">Student Notes</small>
                                        <div class="mt-1">{{ $submission->notes }}</div>
                                    </div>
                                @endif

                                @if($submission->grade !== null)
                                    <div class="alert alert-success">
                                        <strong>Grade:</strong> {{ $submission->grade }}{{ !empty($assignment->content_data['max_points']) ? '/' . $assignment->content_data['max_points'] : '' }}
                                        
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
                                    @if($submission->attachment)
                                        <a href="{{ route('admin.assignments.download-submission', $submission->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    @endif
                                    
                                    <button class="btn btn-primary btn-sm" onclick="openGradingModal({{ $submission->id }}, '{{ $submission->student->first_name ?? 'Student' }}', {{ $submission->grade ?? 'null' }}, '{{ addslashes($submission->feedback ?? '') }}')">
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
                    <div class="text-muted h5 mb-2">No submissions yet for this assignment.</div>
                    <small class="text-muted">Students will see this assignment in their assignments page.</small>
                </div>
            </div>
        @endif
    @else
        <div class="no-submissions">
            Assignment not found.
        </div>
    @endif
</div>

<!-- Grading Modal -->
<div class="modal-bg" id="gradingModal">
    <div class="modal">
        <h3 id="gradingModalTitle">Grade Assignment</h3>
        <form id="gradingForm">
            @csrf
            <input type="hidden" id="submissionId" name="submission_id">
            
            <label for="grade">Grade {{ !empty($assignment->content_data['max_points']) ? '(out of ' . $assignment->content_data['max_points'] . ')' : '' }}:</label>
            <input type="number" id="grade" name="grade" step="0.1" min="0" {{ !empty($assignment->content_data['max_points']) ? 'max="' . $assignment->content_data['max_points'] . '"' : '' }} required>
            
            <label for="feedback">Feedback (optional):</label>
            <textarea id="feedback" name="feedback" placeholder="Provide feedback to the student..."></textarea>
            
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
function openGradingModal(submissionId, studentName, currentGrade, currentFeedback) {
    document.getElementById('submissionId').value = submissionId;
    document.getElementById('gradingModalTitle').textContent = `Grade Assignment - ${studentName}`;
    
    if (currentGrade !== null) {
        document.getElementById('grade').value = currentGrade;
    }
    document.getElementById('feedback').value = currentFeedback || '';
    
    document.getElementById('gradingModal').classList.add('show');
}

function closeGradingModal() {
    document.getElementById('gradingModal').classList.remove('show');
    document.getElementById('gradingForm').reset();
}

// Handle grading form submission
document.getElementById('gradingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submissionId = document.getElementById('submissionId').value;
    
    fetch(`/admin/assignments/submissions/${submissionId}/grade`, {
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
        alert('An error occurred while saving the grade.');
    });
});

// Click outside modal to close
document.getElementById('gradingModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeGradingModal();
    }
});
</script>
@endpush
