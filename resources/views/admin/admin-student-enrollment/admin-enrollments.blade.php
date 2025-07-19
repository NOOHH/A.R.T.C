@extends('layouts.admin')

@section('title', 'Enrollment Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="bi bi-book me-2"></i>Enrollment Management
                    </h3>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.student.enrollment.batch') }}" class="btn btn-primary">
                            <i class="bi bi-people-fill me-1"></i>Batch Enrollment
                        </a>
                        <a href="{{ route('admin.student.registration.pending') }}" class="btn btn-info">
                            <i class="bi bi-clock me-1"></i>Pending Registrations
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(isset($dbError))
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ $dbError }}
                        </div>
                    @endif
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3>{{ $totalEnrollments }}</h3>
                                            <p class="mb-0">Total Enrollments</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-people fs-1"></i>
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
                                            <h3>{{ $activeEnrollments }}</h3>
                                            <p class="mb-0">Active Enrollments</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-check-circle fs-1"></i>
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
                                            <h3>{{ $pendingEnrollments }}</h3>
                                            <p class="mb-0">Pending Enrollments</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-clock fs-1"></i>
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
                                            <h3>{{ $completedCourses }}</h3>
                                            <p class="mb-0">Completed Courses</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-trophy fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.student.registration.pending') }}" class="btn btn-outline-primary">
                                            <i class="bi bi-eye me-2"></i>View Pending Registrations
                                        </a>
                                        <a href="{{ route('admin.student.registration.history') }}" class="btn btn-outline-info">
                                            <i class="bi bi-clock-history me-2"></i>View Registration History
                                        </a>
                                        <a href="{{ route('admin.student.enrollment.batch') }}" class="btn btn-outline-success">
                                            <i class="bi bi-people-fill me-2"></i>Manage Batch Enrollments
                                        </a>
                                        <a href="{{ route('admin.batches.create') }}" class="btn btn-outline-warning">
                                            <i class="bi bi-plus-circle me-2"></i>Create New Batch
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="bi bi-person-plus me-2"></i>Student Enrollment Assignment</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.enrollment.assign') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="student_id" class="form-label">Select Student</label>
                                            <select name="student_id" id="student_id" class="form-select" required>
                                                <option value="">Choose a student...</option>
                                                @foreach($approvedStudents ?? [] as $student)
                                                    <option value="{{ $student->student_id }}">
                                                        {{ $student->firstname }} {{ $student->lastname }} - {{ $student->email }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="program_id" class="form-label">Program</label>
                                            <select name="program_id" id="program_id" class="form-select" required>
                                                <option value="">Select program...</option>
                                                @foreach($programs ?? [] as $program)
                                                    <option value="{{ $program->program_id }}">
                                                        {{ $program->program_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="batch_id" class="form-label">Batch</label>
                                            <select name="batch_id" id="batch_id" class="form-select" required>
                                                <option value="">Select batch...</option>
                                                @foreach($batches ?? [] as $batch)
                                                    <option value="{{ $batch->batch_id }}">
                                                        {{ $batch->batch_name }} ({{ $batch->start_date ?? 'TBD' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="enrollment_type" class="form-label">Plan Type</label>
                                            <select name="enrollment_type" id="enrollment_type" class="form-select" required>
                                                <option value="">Select plan...</option>
                                                <option value="modular">Modular</option>
                                                <option value="full">Full Program</option>
                                                <option value="accelerated">Accelerated</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3" id="course_selection" style="display: none;">
                                            <label for="course_id" class="form-label">Select Course (for Modular)</label>
                                            <select name="course_id" id="course_id" class="form-select">
                                                <option value="">Select course...</option>
                                                @foreach($courses ?? [] as $course)
                                                    <option value="{{ $course->course_id }}">
                                                        {{ $course->course_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="learning_mode" class="form-label">Learning Mode</label>
                                            <select name="learning_mode" id="learning_mode" class="form-select" required>
                                                <option value="">Select mode...</option>
                                                <option value="online">Online</option>
                                                <option value="onsite">On-site</option>
                                                <option value="hybrid">Hybrid</option>
                                            </select>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="bi bi-check-circle me-2"></i>Assign Enrollment
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const enrollmentTypeSelect = document.getElementById('enrollment_type');
    const courseSelection = document.getElementById('course_selection');
    const courseSelect = document.getElementById('course_id');
    
    if (enrollmentTypeSelect) {
        enrollmentTypeSelect.addEventListener('change', function() {
            if (this.value === 'modular') {
                courseSelection.style.display = 'block';
                courseSelect.required = true;
            } else {
                courseSelection.style.display = 'none';
                courseSelect.required = false;
                courseSelect.value = '';
            }
        });
    }
});
</script>
@endsection
