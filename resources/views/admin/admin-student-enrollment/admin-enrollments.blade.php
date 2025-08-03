@extends('admin.admin-dashboard-layout')

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

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <!-- Individual Student Enrollment Assignment -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5><i class="bi bi-person-plus me-2"></i>Individual Student Enrollment</h5>
                                    <form method="GET" action="{{ route('admin.students.export') }}" style="display: inline;">
                                        <input type="hidden" name="status" value="approved">
                                        <input type="hidden" name="download" value="direct">
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download me-1"></i>Export Students CSV
                                        </button>
                                    </form>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.enrollment.assign') }}" method="POST" id="enrollmentForm">
                                        @csrf
                                        
                                        <!-- Student Selection Mode -->
                                        <div class="mb-3">
                                            <label class="form-label">Selection Mode</label>
                                            <div class="d-flex gap-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="selection_mode" id="single_mode" value="single" checked>
                                                    <label class="form-check-label" for="single_mode">
                                                        Single Student
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="selection_mode" id="multiple_mode" value="multiple">
                                                    <label class="form-check-label" for="multiple_mode">
                                                        Multiple Students
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Single Student Selection -->
                                        <div class="mb-3" id="single_student_selection">
                                            <label for="student_id" class="form-label">Select Student</label>
                                            <select name="student_id" id="student_id" class="form-select" required>
                                                <option value="">Choose a student...</option>
                                                @foreach($students ?? [] as $student)
                                                    <option value="{{ $student->student_id }}">
                                                        {{ $student->firstname }} {{ $student->lastname }} ({{ $student->student_id }}) - {{ $student->email }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Multiple Student Selection -->
                                        <div class="mb-3" id="multiple_student_selection" style="display: none;">
                                            <label for="student_ids" class="form-label">Select Students (Hold Ctrl/Cmd for multiple)</label>
                                            <select name="student_ids[]" id="student_ids" class="form-select" multiple style="min-height: 120px;">
                                                @foreach($students ?? [] as $student)
                                                    <option value="{{ $student->student_id }}">
                                                        {{ $student->firstname }} {{ $student->lastname }} ({{ $student->student_id }}) - {{ $student->email }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Hold Ctrl (Windows) or Cmd (Mac) while clicking to select multiple students</div>
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
                                            <label for="enrollment_type" class="form-label">Plan Type</label>
                                            <select name="enrollment_type" id="enrollment_type" class="form-select" required>
                                                <option value="">Select plan...</option>
                                                <option value="modular">Modular</option>
                                                <option value="full">Full Program</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3" id="module_selection" style="display: none;">
                                            <label for="module_id" class="form-label">Select Module</label>
                                            <select name="module_id" id="module_id" class="form-select">
                                                <option value="">Select module...</option>
                                                <!-- Modules will be loaded via JavaScript based on program selection -->
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3" id="course_selection" style="display: none;">
                                            <label for="course_id" class="form-label">Select Course</label>
                                            <select name="course_id" id="course_id" class="form-select">
                                                <option value="">Select course...</option>
                                                <!-- Courses will be loaded via JavaScript based on module selection -->
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
                                            <label for="learning_mode" class="form-label">Learning Mode</label>
                                            <select name="learning_mode" id="learning_mode" class="form-select" required>
                                                <option value="">Select mode...</option>
                                                <option value="online">Synchronous</option>
                                                <option value="onsite">Self Paced</option>
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
    const programSelect = document.getElementById('program_id');
    const enrollmentTypeSelect = document.getElementById('enrollment_type');
    const moduleSelection = document.getElementById('module_selection');
    const moduleSelect = document.getElementById('module_id');
    const courseSelection = document.getElementById('course_selection');
    const courseSelect = document.getElementById('course_id');
    const selectionModeRadios = document.querySelectorAll('input[name="selection_mode"]');
    const singleStudentSelection = document.getElementById('single_student_selection');
    const multipleStudentSelection = document.getElementById('multiple_student_selection');
    const singleStudentSelect = document.getElementById('student_id');
    const multipleStudentSelect = document.getElementById('student_ids');
    
    // Handle program selection change
    if (programSelect) {
        programSelect.addEventListener('change', function() {
            const programId = this.value;
            
            // Reset dependent dropdowns
            moduleSelect.innerHTML = '<option value="">Select module...</option>';
            courseSelect.innerHTML = '<option value="">Select course...</option>';
            moduleSelection.style.display = 'none';
            courseSelection.style.display = 'none';
            
            if (programId) {
                // Load modules for the selected program
                fetch(`/api/programs/${programId}/modules`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.modules) {
                            data.modules.forEach(module => {
                                const option = document.createElement('option');
                                option.value = module.module_id; // This now comes from our mapped response
                                option.textContent = module.module_name;
                                moduleSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => console.error('Error loading modules:', error));
            }
        });
    }
    
    // Handle enrollment type change
    if (enrollmentTypeSelect) {
        enrollmentTypeSelect.addEventListener('change', function() {
            if (this.value === 'modular') {
                moduleSelection.style.display = 'block';
                // Don't set required here - will be set when module is actually needed
                
                // If a program is already selected, show modules
                if (programSelect.value) {
                    const event = new Event('change');
                    programSelect.dispatchEvent(event);
                }
            } else {
                moduleSelection.style.display = 'none';
                courseSelection.style.display = 'none';
                moduleSelect.required = false;
                courseSelect.required = false;
                moduleSelect.value = '';
                courseSelect.value = '';
            }
        });
    }
    
    // Handle module selection change
    if (moduleSelect) {
        moduleSelect.addEventListener('change', function() {
            const moduleId = this.value;
            
            // Reset course dropdown
            courseSelect.innerHTML = '<option value="">Select course...</option>';
            courseSelection.style.display = 'none';
            
            if (moduleId) {
                courseSelection.style.display = 'block';
                courseSelect.required = true;
                
                // Load courses for the selected module
                fetch(`/api/modules/${moduleId}/courses`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.courses) {
                            data.courses.forEach(course => {
                                const option = document.createElement('option');
                                option.value = course.subject_id;
                                option.textContent = course.subject_name;
                                courseSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => console.error('Error loading courses:', error));
            } else {
                courseSelect.required = false;
            }
        });
    }

    // Handle selection mode change
    selectionModeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'single') {
                singleStudentSelection.style.display = 'block';
                multipleStudentSelection.style.display = 'none';
                singleStudentSelect.required = true;
                multipleStudentSelect.required = false;
            } else {
                singleStudentSelection.style.display = 'none';
                multipleStudentSelection.style.display = 'block';
                singleStudentSelect.required = false;
                multipleStudentSelect.required = true;
            }
        });
    });

    // Handle form submission
    document.getElementById('enrollmentForm').addEventListener('submit', function(e) {
        const selectionMode = document.querySelector('input[name="selection_mode"]:checked').value;
        
        if (selectionMode === 'single') {
            // Remove multiple student selection from form data
            multipleStudentSelect.disabled = true;
        } else {
            // Remove single student selection from form data
            singleStudentSelect.disabled = true;
            
            // Validate multiple selection
            if (multipleStudentSelect.selectedOptions.length === 0) {
                e.preventDefault();
                alert('Please select at least one student.');
                return;
            }
        }
    });
});
</script>
@endsection
