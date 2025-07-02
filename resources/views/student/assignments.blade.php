@extends('student.student-dashboard-layout')

@section('title', 'My Assignments')

@push('styles')
<style>
  /* Assignment page styles */
  .assignments-container {
    background: #fff;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin: 20px 0;
  }

  .assignments-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f2f5;
  }

  .assignments-header h1 {
    font-size: 2.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
  }

  .assignment-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 25px;
  }

  .assignment-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 25px;
    transition: all 0.3s ease;
    border-left: 5px solid #667eea;
    position: relative;
  }

  .assignment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
  }

  .assignment-status {
    position: absolute;
    top: 15px;
    right: 15px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
  }

  .assignment-status.pending {
    background: #fff3cd;
    color: #856404;
  }

  .assignment-status.submitted {
    background: #d4edda;
    color: #155724;
  }

  .assignment-status.overdue {
    background: #f8d7da;
    color: #721c24;
  }

  .assignment-status.graded {
    background: #cce5ff;
    color: #004085;
  }

  .assignment-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
  }

  .assignment-description {
    color: #6c757d;
    margin-bottom: 15px;
    line-height: 1.5;
  }

  .assignment-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 20px;
    font-size: 0.9rem;
  }

  .meta-item {
    display: flex;
    flex-direction: column;
    gap: 3px;
  }

  .meta-label {
    font-weight: 600;
    color: #495057;
  }

  .meta-value {
    color: #6c757d;
  }

  .due-date.overdue {
    color: #dc3545;
    font-weight: 600;
  }

  .assignment-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
  }

  .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }

  .btn-secondary {
    background: #6c757d;
    color: white;
  }

  .btn-secondary:hover {
    background: #545b62;
  }

  .btn-success {
    background: #28a745;
    color: white;
  }

  .btn-success:hover {
    background: #218838;
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

  .no-assignments {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    font-size: 1.1rem;
  }

  .no-assignments::before {
    content: '📝';
    display: block;
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
  }

  /* Modal styles for submission */
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

  .modal input[type="file"] {
    width: 100%;
    padding: 12px;
    border: 2px dashed #667eea;
    border-radius: 8px;
    margin-bottom: 15px;
    background: #f8f9fa;
  }

  .modal textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    margin-bottom: 15px;
    min-height: 100px;
    resize: vertical;
  }

  .modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
  }

  .filter-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
  }

  .filter-tab {
    padding: 10px 20px;
    border: 2px solid #e1e5e9;
    border-radius: 25px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    background: white;
    color: #6c757d;
  }

  .filter-tab.active {
    background: #667eea;
    color: white;
    border-color: #667eea;
  }
</style>
@endpush

@section('content')
<div class="assignments-container">
    <div class="assignments-header">
        <h1>📝 My Assignments</h1>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <div class="filter-tab active" data-filter="all">All Assignments</div>
        <div class="filter-tab" data-filter="pending">Pending</div>
        <div class="filter-tab" data-filter="submitted">Submitted</div>
        <div class="filter-tab" data-filter="overdue">Overdue</div>
        <div class="filter-tab" data-filter="graded">Graded</div>
    </div>

    @if($assignments && $assignments->count() > 0)
        <div class="assignment-grid">
            @foreach($assignments as $assignment)
                @php
                    $dueDate = $assignment->content_data['due_date'] ?? null;
                    $isOverdue = $dueDate && \Carbon\Carbon::parse($dueDate)->isPast();
                    $submission = $assignment->submissions->where('student_id', auth()->id())->first();
                    
                    if ($submission && $submission->grade !== null) {
                        $status = 'graded';
                    } elseif ($submission) {
                        $status = 'submitted';
                    } elseif ($isOverdue) {
                        $status = 'overdue';
                    } else {
                        $status = 'pending';
                    }
                @endphp
                
                <div class="assignment-card" data-status="{{ $status }}">
                    <div class="assignment-status {{ $status }}">{{ ucfirst($status) }}</div>
                    
                    <div class="assignment-title">
                        📝 {{ $assignment->module_name }}
                    </div>
                    
                    <div class="assignment-description">
                        {{ $assignment->module_description }}
                    </div>

                    <div class="assignment-meta">
                        @if($dueDate)
                        <div class="meta-item">
                            <span class="meta-label">Due Date</span>
                            <span class="meta-value {{ $isOverdue ? 'due-date overdue' : '' }}">
                                {{ \Carbon\Carbon::parse($dueDate)->format('M d, Y g:i A') }}
                                @if($isOverdue)
                                    (Overdue)
                                @endif
                            </span>
                        </div>
                        @endif
                        
                        @if(!empty($assignment->content_data['max_points']))
                        <div class="meta-item">
                            <span class="meta-label">Points</span>
                            <span class="meta-value">{{ $assignment->content_data['max_points'] }}</span>
                        </div>
                        @endif

                        <div class="meta-item">
                            <span class="meta-label">Program</span>
                            <span class="meta-value">{{ $assignment->program->program_name }}</span>
                        </div>

                        @if($submission)
                        <div class="meta-item">
                            <span class="meta-label">Submitted</span>
                            <span class="meta-value">{{ $submission->created_at->format('M d, Y g:i A') }}</span>
                        </div>
                        @endif
                    </div>

                    @if($submission && $submission->grade !== null)
                        <div class="grade-display">
                            Grade: {{ $submission->grade }}{{ !empty($assignment->content_data['max_points']) ? '/' . $assignment->content_data['max_points'] : '' }}
                        </div>
                        @if($submission->feedback)
                            <div style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 8px; font-size: 0.9rem;">
                                <strong>Feedback:</strong> {{ $submission->feedback }}
                            </div>
                        @endif
                    @endif

                    <div class="assignment-actions">
                        @if($assignment->attachment)
                            <a href="{{ route('student.assignments.download', $assignment->modules_id) }}" class="btn btn-secondary">
                                📎 Download Assignment
                            </a>
                        @endif

                        @if($submission)
                            @if($submission->attachment)
                                <a href="{{ asset('storage/submissions/' . $submission->attachment) }}" class="btn btn-success" target="_blank">
                                    📄 View My Submission
                                </a>
                            @endif
                            @if(!$isOverdue && $submission->grade === null)
                                <button class="btn btn-primary" onclick="openSubmissionModal({{ $assignment->modules_id }}, '{{ $assignment->module_name }}', true)">
                                    🔄 Resubmit Assignment
                                </button>
                            @endif
                        @else
                            @if(!$isOverdue)
                                <button class="btn btn-primary" onclick="openSubmissionModal({{ $assignment->modules_id }}, '{{ $assignment->module_name }}', false)">
                                    📤 Submit Assignment
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="no-assignments">
            No assignments found.<br>
            <small>Your instructor hasn't posted any assignments yet.</small>
        </div>
    @endif
</div>

<!-- Submission Modal -->
<div class="modal-bg" id="submissionModal">
    <div class="modal">
        <h3 id="modalTitle">Submit Assignment</h3>
        <form id="submissionForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="assignmentId" name="assignment_id">
            
            <label for="submissionFile">Upload your assignment file:</label>
            <input type="file" id="submissionFile" name="submission_file" accept=".pdf,.doc,.docx,.txt,.zip" required>
            
            <label for="submissionNotes">Additional notes (optional):</label>
            <textarea id="submissionNotes" name="notes" placeholder="Any comments about your submission..."></textarea>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeSubmissionModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Assignment</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterTabs = document.querySelectorAll('.filter-tab');
    const assignmentCards = document.querySelectorAll('.assignment-card');

    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Update active tab
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;

            // Filter cards
            assignmentCards.forEach(card => {
                if (filter === 'all' || card.dataset.status === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});

function openSubmissionModal(assignmentId, assignmentName, isResubmission = false) {
    document.getElementById('assignmentId').value = assignmentId;
    document.getElementById('modalTitle').textContent = isResubmission ? 
        `Resubmit: ${assignmentName}` : `Submit: ${assignmentName}`;
    document.getElementById('submissionModal').classList.add('show');
}

function closeSubmissionModal() {
    document.getElementById('submissionModal').classList.remove('show');
    document.getElementById('submissionForm').reset();
}

// Handle form submission
document.getElementById('submissionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const assignmentId = document.getElementById('assignmentId').value;
    
    fetch(`/student/assignments/${assignmentId}/submit`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Assignment submitted successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting the assignment.');
    });
});

// Click outside modal to close
document.getElementById('submissionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSubmissionModal();
    }
});
</script>
@endpush
