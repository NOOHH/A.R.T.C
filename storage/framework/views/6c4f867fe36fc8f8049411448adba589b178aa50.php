

<?php $__env->startSection('title', 'Attendance Reports'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Attendance Reports</h5>
                </div>
                <div class="card-body">
                    <?php if($batches->count() > 0): ?>
                        <div class="row">
                            <?php $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><?php echo e($batch->batch_name); ?></h6>
                                            <small class="text-muted"><?php echo e($batch->program->program_title ?? 'Unknown Program'); ?></small>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Total Students:</strong> <?php echo e($batch->students->count()); ?></p>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Student</th>
                                                            <th>Last Attendance</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__empty_1 = true; $__currentLoopData = $batch->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                            <tr>
                                                                <td><?php echo e($student->student_firstname); ?> <?php echo e($student->student_lastname); ?></td>
                                                                <td>
                                                                    <small class="text-muted">No records yet</small>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-secondary">Unknown</span>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                            <tr>
                                                                <td colspan="3" class="text-center text-muted">No students enrolled</td>
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
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <h5 class="mt-3">No Batches Assigned</h5>
                            <p class="text-muted">You don't have any batches assigned to you yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('professor.professor-layouts.professor-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\professor\reports\attendance.blade.php ENDPATH**/ ?>