@extends('professor.layout')

@section('title', 'Grading Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-clipboard-check"></i> Grading Management</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGradeModal">
                    <i class="bi bi-plus-circle"></i> Add Grade
                </button>
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

            <!-- Filter Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('professor.grading') }}" class="row g-3">
                        <div class="col-md-8">
                            <label for="program_id" class="form-label">Select Program</label>
                            <select name="program_id" id="program_id" class="form-select" required>
                                <option value="">Choose a program...</option>
                                @foreach($assignedPrograms as $program)
                                    <option value="{{ $program->program_id }}" 
                                            {{ $selectedProgramId == $program->program_id ? 'selected' : '' }}>
                                        {{ $program->program_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Load Students</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($selectedProgramId && $students->count() > 0)
                <!-- Students and Grades -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Students and Grades</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Student ID</th>
                                        <th>Grades</th>
                                        <th>Average</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                        @php
                                            $studentGrades = $grades->get($student->student_id, collect());
                                            $average = $studentGrades->count() > 0 ? $studentGrades->avg('grade') : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $student->full_name }}</strong><br>
                                                <small class="text-muted">{{ $student->email }}</small>
                                            </td>
                                            <td>{{ $student->student_id }}</td>
                                            <td>
                                                @if($studentGrades->count() > 0)
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($studentGrades->take(3) as $grade)
                                                            <span class="badge bg-secondary" title="{{ $grade->assignment_name }}">
                                                                {{ $grade->grade }}/{{ $grade->max_points }}
                                                            </span>
                                                        @endforeach
                                                        @if($studentGrades->count() > 3)
                                                            <span class="badge bg-light text-dark">
                                                                +{{ $studentGrades->count() - 3 }} more
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">No grades yet</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($average > 0)
                                                    <strong class="
                                                        @if($average >= 90) text-success
                                                        @elseif($average >= 80) text-info
                                                        @elseif($average >= 70) text-warning
                                                        @else text-danger
                                                        @endif
                                                    ">{{ number_format($average, 1) }}%</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-primary btn-sm" 
                                                            onclick="addGrade({{ $student->student_id }}, '{{ $student->full_name }}')">
                                                        <i class="bi bi-plus"></i> Add Grade
                                                    </button>
                                                    <a href="{{ route('professor.grading.student', ['student' => $student->student_id, 'program_id' => $selectedProgramId]) }}" 
                                                       class="btn btn-outline-secondary btn-sm">
                                                        <i class="bi bi-eye"></i> View Details
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @elseif($selectedProgramId)
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">No Students Found</h5>
                        <p class="text-muted">No students are enrolled in the selected program.</p>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-award text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">Select a Program</h5>
                        <p class="text-muted">Please select a program to manage student grades.</p>
                    </div>
                </div>
            @endif
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
                    <input type="hidden" name="program_id" value="{{ $selectedProgramId }}">
                    <input type="hidden" name="student_id" id="modal_student_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Student</label>
                        <input type="text" class="form-control" id="modal_student_name" readonly>
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

<script>
function addGrade(studentId, studentName) {
    document.getElementById('modal_student_id').value = studentId;
    document.getElementById('modal_student_name').value = studentName;
    document.getElementById('assignment_name').value = '';
    document.getElementById('grade').value = '';
    document.getElementById('max_points').value = '100';
    document.getElementById('feedback').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('addGradeModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when program changes
    const programSelect = document.getElementById('program_id');
    
    programSelect.addEventListener('change', function() {
        if (this.value) {
            this.closest('form').submit();
        }
    });
});
</script>
@endsection
