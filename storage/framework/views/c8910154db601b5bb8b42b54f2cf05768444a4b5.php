

<?php $__env->startSection('title', 'Enrollment Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="bi bi-book me-2"></i>Enrollment Management
                    </h3>
                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('admin.student.enrollment.batch')); ?>" class="btn btn-primary">
                            <i class="bi bi-people-fill me-1"></i>Batch Enrollment
                        </a>
                        <a href="<?php echo e(route('admin.student.registration.pending')); ?>" class="btn btn-info">
                            <i class="bi bi-clock me-1"></i>Pending Registrations
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <?php if(isset($dbError)): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo e($dbError); ?>

                        </div>
                    <?php endif; ?>

                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Individual Student Enrollment Assignment -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5><i class="bi bi-person-plus me-2"></i>Individual Student Enrollment</h5>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportApprovedStudents()">
                                        <i class="bi bi-download me-1"></i>Export Students CSV
                                    </button>
                                </div>
                                <div class="card-body">
                                    <form action="<?php echo e(route('admin.enrollment.assign')); ?>" method="POST" id="enrollmentForm">
                                        <?php echo csrf_field(); ?>
                                        
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
                                                <?php $__currentLoopData = $students ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($student->student_id); ?>">
                                                        <?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?> (<?php echo e($student->student_id); ?>) - <?php echo e($student->email); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>

                                        <!-- Multiple Student Selection -->
                                        <div class="mb-3" id="multiple_student_selection" style="display: none;">
                                            <label for="student_ids" class="form-label">Select Students (Hold Ctrl/Cmd for multiple)</label>
                                            <select name="student_ids[]" id="student_ids" class="form-select" multiple style="min-height: 120px;">
                                                <?php $__currentLoopData = $students ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($student->student_id); ?>">
                                                        <?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?> (<?php echo e($student->student_id); ?>) - <?php echo e($student->email); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <div class="form-text">Hold Ctrl (Windows) or Cmd (Mac) while clicking to select multiple students</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="program_id" class="form-label">Program</label>
                                            <select name="program_id" id="program_id" class="form-select" required>
                                                <option value="">Select program...</option>
                                                <?php $__currentLoopData = $programs ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($program->program_id); ?>">
                                                        <?php echo e($program->program_name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                                <?php $__currentLoopData = $batches ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($batch->batch_id); ?>">
                                                        <?php echo e($batch->batch_name); ?> (<?php echo e($batch->start_date ?? 'TBD'); ?>)
                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

    // Export approved students function
    function exportApprovedStudents() {
        const exportUrl = '<?php echo e(route("admin.students.export")); ?>?status=approved&download=direct';
        
        // Create a temporary link and trigger download
        const link = document.createElement('a');
        link.href = exportUrl;
        link.download = 'approved_students_export.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\admin-student-enrollment\admin-enrollments.blade.php ENDPATH**/ ?>