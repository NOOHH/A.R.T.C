

<?php $__env->startSection('title', 'Student Management'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people display-4 me-3"></i>
                        <div>
                            <h4 class="mb-0"><?php echo e($students->count()); ?></h4>
                            <p class="mb-0">Total Students</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-collection display-4 me-3"></i>
                        <div>
                            <h4 class="mb-0"><?php echo e($assignedPrograms->count()); ?></h4>
                            <p class="mb-0">My Programs</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle display-4 me-3"></i>
                        <div>
                            <h4 class="mb-0"><?php echo e($students->where('enrollments.0.enrollment_status', 'approved')->count()); ?></h4>
                            <p class="mb-0">Active Students</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock display-4 me-3"></i>
                        <div>
                            <h4 class="mb-0"><?php echo e($students->where('enrollments.0.enrollment_status', 'pending')->count()); ?></h4>
                            <p class="mb-0">Pending</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Students in My Programs</h5>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" id="programFilter" style="width: auto;">
                    <option value="">All Programs</option>
                    <?php $__currentLoopData = $assignedPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($program->program_id); ?>"><?php echo e($program->program_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
                    <option value="">All Status</option>
                    <option value="approved">Active</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <?php if($students->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="studentsTable">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Program</th>
                                <th>Package</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $__currentLoopData = $student->enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr data-program="<?php echo e($enrollment->program_id); ?>" data-status="<?php echo e($enrollment->enrollment_status); ?>">
                                        <td>
                                            <span class="badge bg-secondary"><?php echo e($student->student_id); ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center me-2" 
                                                     style="width: 32px; height: 32px;">
                                                    <i class="bi bi-person-fill text-white" style="font-size: 0.8rem;"></i>
                                                </div>
                                                <div>
                                                    <strong><?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?></strong>
                                                    <?php if($student->middlename): ?>
                                                        <small class="text-muted"><?php echo e($student->middlename); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo e($student->email); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo e($enrollment->program->program_name ?? 'N/A'); ?></span>
                                        </td>
                                        <td><?php echo e($enrollment->package->package_name ?? 'N/A'); ?></td>
                                        <td>
                                            <?php if($enrollment->enrollment_status === 'approved'): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php elseif($enrollment->enrollment_status === 'pending'): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo e(ucfirst($enrollment->enrollment_status)); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <!-- Progress bar - you can implement actual progress calculation -->
                                            <div class="progress" style="width: 100px;">
                                                <div class="progress-bar" role="progressbar" style="width: <?php echo e(rand(0, 100)); ?>%"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#viewStudentModal"
                                                        data-student-id="<?php echo e($student->student_id); ?>"
                                                        data-student-name="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-success" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#gradeModal"
                                                        data-student-id="<?php echo e($student->student_id); ?>"
                                                        data-student-name="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-info" 
                                                        title="Monitor Attendance">
                                                    <i class="bi bi-calendar-check"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-people display-1 text-muted"></i>
                    <h5 class="mt-3 text-muted">No Students Found</h5>
                    <p class="text-muted">Students will appear here once they enroll in your programs.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Grade Student Modal -->
<div class="modal fade" id="gradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="gradeForm">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Assign Grade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="grade" class="form-label">Grade (0-100)</label>
                        <input type="number" class="form-control" id="grade" name="grade" min="0" max="100" required>
                    </div>
                    <div class="mb-3">
                        <label for="feedback" class="form-label">Feedback (Optional)</label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="3" placeholder="Add feedback for the student..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Assign Grade</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Student Modal -->
<div class="modal fade" id="viewStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Student Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="studentModalBody">
                <!-- Student details will be loaded here via AJAX -->
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Filter functionality
document.getElementById('programFilter').addEventListener('change', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);

function filterTable() {
    const programFilter = document.getElementById('programFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('#studentsTable tbody tr');
    
    rows.forEach(row => {
        const program = row.getAttribute('data-program');
        const status = row.getAttribute('data-status');
        
        const programMatch = !programFilter || program === programFilter;
        const statusMatch = !statusFilter || status === statusFilter;
        
        if (programMatch && statusMatch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Grade modal functionality
document.getElementById('gradeModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const studentId = button.getAttribute('data-student-id');
    const studentName = button.getAttribute('data-student-name');
    
    const form = document.getElementById('gradeForm');
    form.action = `/professor/students/${studentId}/grade`;
    
    const modalTitle = document.querySelector('#gradeModal .modal-title');
    modalTitle.textContent = `Assign Grade - ${studentName}`;
});

// View student modal functionality
document.getElementById('viewStudentModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const studentId = button.getAttribute('data-student-id');
    const studentName = button.getAttribute('data-student-name');
    
    const modalTitle = document.querySelector('#viewStudentModal .modal-title');
    modalTitle.textContent = `Student Details - ${studentName}`;
    
    // Here you could load student details via AJAX
    const modalBody = document.getElementById('studentModalBody');
    modalBody.innerHTML = `
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Student details for ${studentName} (ID: ${studentId}) would be loaded here.
            This feature will be implemented based on your specific requirements.
        </div>
    `;
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('professor.professor-layouts.professor-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\professor\students.blade.php ENDPATH**/ ?>