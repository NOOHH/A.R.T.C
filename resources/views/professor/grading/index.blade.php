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
                
                {{-- Program Analytics Dashboard --}}
                @if($programAnalytics)
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $programAnalytics['total_students'] }}</h4>
                                        <p class="mb-0">Total Students</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-people" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ number_format($programAnalytics['average_grade'], 1) }}%</h4>
                                        <p class="mb-0">Avg Grade</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-graph-up" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ number_format($programAnalytics['average_quiz_score'], 1) }}%</h4>
                                        <p class="mb-0">Avg Quiz Score</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-clipboard-check" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ number_format($programAnalytics['completion_rate'], 1) }}%</h4>
                                        <p class="mb-0">Completion Rate</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Grade Distribution Chart --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Grade Distribution</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="gradeDistributionChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Quick Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-primary" onclick="autoGradeQuizzes()">
                                        <i class="bi bi-robot"></i> Auto-Grade Quiz Submissions
                                    </button>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success" onclick="exportGrades('csv')">
                                            <i class="bi bi-file-earmark-excel"></i> Export CSV
                                        </button>
                                        <button type="button" class="btn btn-info" onclick="exportGrades('pdf')">
                                            <i class="bi bi-file-earmark-pdf"></i> Export PDF
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#performanceModal">
                                        <i class="bi bi-bar-chart"></i> View Performance Analytics
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

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
            <form method="POST" action="{{ route('professor.grading') }}">
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

{{-- Performance Analytics Modal --}}
@if($selectedProgramId && $programAnalytics)
<div class="modal fade" id="performanceModal" tabindex="-1" aria-labelledby="performanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="performanceModalLabel">
                    <i class="bi bi-bar-chart"></i> Performance Analytics
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Quiz Performance Overview</h6>
                        @if(!empty($programAnalytics['quiz_performance']))
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Quiz</th>
                                            <th>Submissions</th>
                                            <th>Avg Score</th>
                                            <th>Completion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($programAnalytics['quiz_performance'] as $quiz)
                                            <tr>
                                                <td>{{ substr($quiz['quiz_title'], 0, 30) }}...</td>
                                                <td>{{ $quiz['total_submissions'] }}</td>
                                                <td>{{ number_format($quiz['average_score'], 1) }}%</td>
                                                <td>{{ number_format($quiz['completion_rate'], 1) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No quiz data available yet.</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6>Students Needing Attention</h6>
                        @if($programAnalytics['low_performers']->isNotEmpty())
                            <div class="list-group">
                                @foreach($programAnalytics['low_performers'] as $performer)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $performer['student']->user->user_firstname ?? 'N/A' }} {{ $performer['student']->user->user_lastname ?? '' }}</strong><br>
                                            <small class="text-muted">ID: {{ $performer['student']->student_id }}</small>
                                        </div>
                                        <span class="badge bg-danger">{{ number_format($performer['average'], 1) }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">All students are performing well!</p>
                        @endif
                        
                        <h6 class="mt-4">Top Performers</h6>
                        @if($programAnalytics['top_performers']->isNotEmpty())
                            <div class="list-group">
                                @foreach($programAnalytics['top_performers'] as $performer)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $performer['student']->user->user_firstname ?? 'N/A' }} {{ $performer['student']->user->user_lastname ?? '' }}</strong><br>
                                            <small class="text-muted">ID: {{ $performer['student']->student_id }}</small>
                                        </div>
                                        <span class="badge bg-success">{{ number_format($performer['average'], 1) }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No top performers identified yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

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
    
    @if($selectedProgramId && $programAnalytics)
    // Grade Distribution Chart
    const ctx = document.getElementById('gradeDistributionChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['A (90-100)', 'B (80-89)', 'C (70-79)', 'D (60-69)', 'F (<60)'],
                datasets: [{
                    label: 'Number of Students',
                    data: [
                        {{ $programAnalytics['grade_distribution']['A'] }},
                        {{ $programAnalytics['grade_distribution']['B'] }},
                        {{ $programAnalytics['grade_distribution']['C'] }},
                        {{ $programAnalytics['grade_distribution']['D'] }},
                        {{ $programAnalytics['grade_distribution']['F'] }}
                    ],
                    backgroundColor: [
                        '#28a745',
                        '#17a2b8',
                        '#ffc107',
                        '#fd7e14',
                        '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    @endif
});

function autoGradeQuizzes() {
    if (confirm('This will automatically create grade entries for all ungraded quiz submissions. Continue?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("professor.grading.auto-grade-quizzes") }}';
        
        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = '{{ csrf_token() }}';
        form.appendChild(token);
        
        const programId = document.createElement('input');
        programId.type = 'hidden';
        programId.name = 'program_id';
        programId.value = '{{ $selectedProgramId }}';
        form.appendChild(programId);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function exportGrades(format) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("professor.grading.export") }}';
    
    const token = document.createElement('input');
    token.type = 'hidden';
    token.name = '_token';
    token.value = '{{ csrf_token() }}';
    form.appendChild(token);
    
    const programId = document.createElement('input');
    programId.type = 'hidden';
    programId.name = 'program_id';
    programId.value = '{{ $selectedProgramId }}';
    form.appendChild(programId);
    
    const formatInput = document.createElement('input');
    formatInput.type = 'hidden';
    formatInput.name = 'format';
    formatInput.value = format;
    form.appendChild(formatInput);
    
    document.body.appendChild(form);
    form.submit();
}

// Existing function - keeping for compatibility
function addGrade(studentId, studentName) {
    // Your existing addGrade implementation
    console.log('Add grade for student:', studentId, studentName);
}
</script>
@endsection
