@extends('professor.layout')

@section('title', 'Student Grade Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Grade Details: {{ $student->full_name }}</h2>
                <a href="{{ route('professor.grading', ['program_id' => $programId]) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Grading
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Student Information -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Student Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Name:</strong> {{ $student->full_name }}</p>
                                    <p><strong>Email:</strong> {{ $student->email }}</p>
                                    <p><strong>Student ID:</strong> {{ $student->student_id }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Phone:</strong> {{ $student->phone_number ?? 'N/A' }}</p>
                                    <p><strong>Total Grades:</strong> {{ $grades->count() }}</p>
                                    <p><strong>Average Grade:</strong> 
                                        <span class="
                                            @if($averageGrade >= 90) text-success
                                            @elseif($averageGrade >= 80) text-info
                                            @elseif($averageGrade >= 70) text-warning
                                            @else text-danger
                                            @endif
                                        ">{{ number_format($averageGrade, 1) }}%</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Performance</h5>
                            <div style="font-size: 3rem;">
                                @if($averageGrade >= 90)
                                    <span class="text-success">A</span>
                                @elseif($averageGrade >= 80)
                                    <span class="text-info">B</span>
                                @elseif($averageGrade >= 70)
                                    <span class="text-warning">C</span>
                                @elseif($averageGrade >= 60)
                                    <span class="text-danger">D</span>
                                @else
                                    <span class="text-danger">F</span>
                                @endif
                            </div>
                            <p class="text-muted">Overall Grade</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grades List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Grades</h5>
                    <button type="button" class="btn btn-primary" 
                            onclick="addGrade({{ $student->student_id }}, '{{ $student->full_name }}')">
                        <i class="bi bi-plus-circle"></i> Add Grade
                    </button>
                </div>
                <div class="card-body">
                    @if($grades->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Assignment</th>
                                        <th>Program</th>
                                        <th>Grade</th>
                                        <th>Percentage</th>
                                        <th>Feedback</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($grades as $grade)
                                        <tr>
                                            <td><strong>{{ $grade->assignment_name }}</strong></td>
                                            <td>{{ $grade->program->program_name }}</td>
                                            <td>{{ $grade->grade }}/{{ $grade->max_points }}</td>
                                            <td>
                                                @php
                                                    $percentage = ($grade->grade / $grade->max_points) * 100;
                                                @endphp
                                                <span class="
                                                    @if($percentage >= 90) text-success
                                                    @elseif($percentage >= 80) text-info
                                                    @elseif($percentage >= 70) text-warning
                                                    @else text-danger
                                                    @endif
                                                ">{{ number_format($percentage, 1) }}%</span>
                                            </td>
                                            <td>
                                                @if($grade->feedback)
                                                    <span title="{{ $grade->feedback }}">
                                                        {{ Str::limit($grade->feedback, 30) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">No feedback</span>
                                                @endif
                                            </td>
                                            <td>{{ $grade->graded_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" 
                                                            onclick="editGrade({{ $grade->grade_id }}, '{{ $grade->assignment_name }}', {{ $grade->grade }}, {{ $grade->max_points }}, '{{ addslashes($grade->feedback) }}')">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form method="POST" action="{{ route('professor.grading.destroy', $grade->grade_id) }}" 
                                                          style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this grade?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-award text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">No Grades Yet</h5>
                            <p class="text-muted">This student hasn't been graded on any assignments yet.</p>
                            <button type="button" class="btn btn-primary" 
                                    onclick="addGrade({{ $student->student_id }}, '{{ $student->full_name }}')">
                                <i class="bi bi-plus-circle"></i> Add First Grade
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Grade Modal -->
<div class="modal fade" id="addGradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Grade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('professor.grading.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="program_id" value="{{ $programId }}">
                    <input type="hidden" name="student_id" value="{{ $student->student_id }}">
                    
                    <div class="mb-3">
                        <label class="form-label">Student</label>
                        <input type="text" class="form-control" value="{{ $student->full_name }}" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assignment_name" class="form-label">Assignment Name</label>
                        <input type="text" class="form-control" id="assignment_name" name="assignment_name" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="grade" class="form-label">Grade</label>
                            <input type="number" class="form-control" id="grade" name="grade" 
                                   min="0" max="100" step="0.1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="max_points" class="form-label">Max Points</label>
                            <input type="number" class="form-control" id="max_points" name="max_points" 
                                   min="1" step="0.1" value="100" required>
                        </div>
                    </div>
                    
                    <div class="mb-3 mt-3">
                        <label for="feedback" class="form-label">Feedback (Optional)</label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Grade</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Grade Modal -->
<div class="modal fade" id="editGradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Grade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editGradeForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Assignment Name</label>
                        <input type="text" class="form-control" id="edit_assignment_name" readonly>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="edit_grade" class="form-label">Grade</label>
                            <input type="number" class="form-control" id="edit_grade" name="grade" 
                                   min="0" max="100" step="0.1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_max_points" class="form-label">Max Points</label>
                            <input type="number" class="form-control" id="edit_max_points" name="max_points" 
                                   min="1" step="0.1" required>
                        </div>
                    </div>
                    
                    <div class="mb-3 mt-3">
                        <label for="edit_feedback" class="form-label">Feedback (Optional)</label>
                        <textarea class="form-control" id="edit_feedback" name="feedback" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Grade</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addGrade(studentId, studentName) {
    document.getElementById('assignment_name').value = '';
    document.getElementById('grade').value = '';
    document.getElementById('max_points').value = '100';
    document.getElementById('feedback').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('addGradeModal'));
    modal.show();
}

function editGrade(gradeId, assignmentName, grade, maxPoints, feedback) {
    document.getElementById('edit_assignment_name').value = assignmentName;
    document.getElementById('edit_grade').value = grade;
    document.getElementById('edit_max_points').value = maxPoints;
    document.getElementById('edit_feedback').value = feedback;
    
    const form = document.getElementById('editGradeForm');
    form.action = `{{ route('professor.grading.update', ':id') }}`.replace(':id', gradeId);
    
    const modal = new bootstrap.Modal(document.getElementById('editGradeModal'));
    modal.show();
}
</script>
@endsection
