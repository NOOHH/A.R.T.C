

<?php $__env->startSection('title', 'Grades Reports'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Grades Reports</h5>
                </div>
                <div class="card-body">
                    <?php if($programs->count() > 0): ?>
                        <div class="row">
                            <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-12 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><?php echo e($program->program_title); ?></h6>
                                            <small class="text-muted"><?php echo e($program->program_description ?? 'No description'); ?></small>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Total Enrolled Students:</strong> <?php echo e($program->enrollments->count()); ?></p>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Student</th>
                                                            <th>Email</th>
                                                            <th>Overall Grade</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__empty_1 = true; $__currentLoopData = $program->enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                            <tr>
                                                                <td><?php echo e($enrollment->student->student_firstname ?? 'N/A'); ?> <?php echo e($enrollment->student->student_lastname ?? ''); ?></td>
                                                                <td><?php echo e($enrollment->student->student_email ?? 'N/A'); ?></td>
                                                                <td>
                                                                    <span class="badge bg-secondary">No grades yet</span>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-info"><?php echo e(ucfirst($enrollment->status ?? 'enrolled')); ?></span>
                                                                </td>
                                                                <td>
                                                                    <a href="<?php echo e(route('professor.grading.student-details', $enrollment->student->student_id ?? '#')); ?>?program_id=<?php echo e($program->program_id); ?>" 
                                                                       class="btn btn-sm btn-outline-primary">
                                                                        View Details
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                            <tr>
                                                                <td colspan="5" class="text-center text-muted">No students enrolled</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-award fs-1 text-muted"></i>
                            <h5 class="mt-3">No Programs Assigned</h5>
                            <p class="text-muted">You don't have any programs assigned to you yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('professor.professor-layouts.professor-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\professor\reports\grades.blade.php ENDPATH**/ ?>