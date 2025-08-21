

<?php $__env->startSection('title', 'Archived Students'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-archive"></i> Archived Students</h2>
                <a href="<?php echo e(route('admin.students.index')); ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Active Students
                </a>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('admin.students.archived')); ?>">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="program_id" class="form-label">Filter by Program</label>
                                <select name="program_id" id="program_id" class="form-select">
                                    <option value="">All Programs</option>
                                    <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($program->program_id); ?>" 
                                                <?php echo e(request('program_id') == $program->program_id ? 'selected' : ''); ?>>
                                            <?php echo e($program->program_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Search by name, ID, or email..." value="<?php echo e(request('search')); ?>">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Search
                                    </button>
                                    <a href="<?php echo e(route('admin.students.archived')); ?>" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-body">
                    <?php if($students->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Program</th>
                                        <th>Status</th>
                                        <th>Archived Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="table-light">
                                            <td>
                                                <strong><?php echo e($student->student_id); ?></strong>
                                                <span class="badge bg-secondary ms-2">Archived</span>
                                            </td>
                                            <td><?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?></td>
                                            <td><?php echo e($student->email); ?></td>
                                            <td>
                                                <?php if($student->program): ?>
                                                    <span class="badge bg-info"><?php echo e($student->program->program_name); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">No Program</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($student->date_approved): ?>
                                                    <span class="badge bg-success">Approved</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($student->updated_at->format('M d, Y')); ?></td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="<?php echo e(route('admin.students.show', $student)); ?>" 
                                                       class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <form method="POST" action="<?php echo e(route('admin.students.restore', $student)); ?>" 
                                                          class="d-inline" onsubmit="return confirm('Are you sure you want to restore this student?')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PATCH'); ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Restore">
                                                            <i class="bi bi-arrow-counterclockwise"></i>
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteStudentModal"
                                                            data-student-id="<?php echo e($student->student_id); ?>"
                                                            data-student-name="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>"
                                                            title="Delete Permanently">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            <?php echo e($students->appends(request()->query())->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-archive fs-1 text-muted"></i>
                            <h4 class="text-muted mt-3">No Archived Students</h4>
                            <p class="text-muted">
                                <?php if(request()->hasAny(['program_id', 'search'])): ?>
                                    No archived students match your current filters.
                                <?php else: ?>
                                    There are no archived students at the moment.
                                <?php endif; ?>
                            </p>
                            <a href="<?php echo e(route('admin.students.index')); ?>" class="btn btn-primary">
                                <i class="bi bi-arrow-left"></i> Back to Active Students
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Student Modal -->
<div class="modal fade" id="deleteStudentModal" tabindex="-1" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteStudentModalLabel">
                    <i class="bi bi-trash text-danger"></i> Delete Student Permanently
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-1 mb-3"></i>
                    <h5>Are you sure you want to permanently delete this student?</h5>
                    <p class="text-muted mb-3">Student: <strong id="studentNameToDelete"></strong></p>
                    <p class="text-danger"><strong>Warning:</strong> This action cannot be undone! All student data will be permanently removed.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteStudentForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Delete Permanently
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Handle delete student modal
document.getElementById('deleteStudentModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const studentId = button.getAttribute('data-student-id');
    const studentName = button.getAttribute('data-student-name');
    
    // Update modal content
    document.getElementById('studentNameToDelete').textContent = studentName;
    
    // Update form action
    const form = document.getElementById('deleteStudentForm');
    form.action = `/admin/students/${studentId}`;
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\students\archived.blade.php ENDPATH**/ ?>