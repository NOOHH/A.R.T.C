

<?php $__env->startSection('title', 'Archived Directors'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-archive"></i> Archived Directors</h2>
                <a href="<?php echo e(route('admin.directors.index')); ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Active Directors
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

            <div class="card shadow">
                <div class="card-body">
                    <?php if($directors->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Department</th>
                                        <th>Assigned Programs</th>
                                        <th>Archived Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $directors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $director): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="table-light">
                                            <td>
                                                <strong><?php echo e($director->full_name); ?></strong>
                                                <span class="badge bg-secondary ms-2">Archived</span>
                                            </td>
                                            <td><?php echo e($director->directors_email); ?></td>
                                            <td>N/A</td>
                                            <td>N/A</td>
                                            <td>
                                                <?php if($director->programs->count() > 0): ?>
                                                    <span class="badge bg-warning"><?php echo e($director->programs->count()); ?> program(s)</span>
                                                <?php else: ?>
                                                    <span class="text-muted">No programs assigned</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($director->updated_at->format('M d, Y')); ?></td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <form method="POST" action="<?php echo e(route('admin.directors.restore', $director)); ?>" 
                                                          class="d-inline" onsubmit="return confirm('Are you sure you want to restore this director?')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PATCH'); ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Restore">
                                                            <i class="bi bi-arrow-counterclockwise"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="<?php echo e(route('admin.directors.destroy', $director)); ?>" 
                                                          class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete this director? This action cannot be undone!')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Permanently">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-archive fs-1 text-muted"></i>
                            <h4 class="text-muted mt-3">No Archived Directors</h4>
                            <p class="text-muted">There are no archived directors at the moment.</p>
                            <a href="<?php echo e(route('admin.directors.index')); ?>" class="btn btn-primary">
                                <i class="bi bi-arrow-left"></i> Back to Active Directors
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\directors\archived.blade.php ENDPATH**/ ?>