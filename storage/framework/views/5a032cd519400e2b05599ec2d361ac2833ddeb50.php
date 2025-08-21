

<?php $__env->startSection('title', 'Directors Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-person-badge"></i> Directors Management</h2>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('admin.directors.archived')); ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-archive"></i> View Archived
                    </a>
                    <a href="<?php echo e(route('admin.directors.create')); ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Director
                    </a>
                </div>
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
                                        <th>Assigned Programs</th>
                                        <th>Hire Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $directors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $director): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo e($director->full_name); ?></strong>
                                            </td>
                                            <td><?php echo e($director->email); ?></td>
                                            <td>
                                                <?php if($director->programs->count() > 0): ?>
                                                    <span class="badge bg-info"><?php echo e($director->programs->count()); ?> program(s)</span>
                                                <?php else: ?>
                                                    <span class="text-muted">No programs assigned</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($director->hire_date ? $director->hire_date->format('M d, Y') : 'N/A'); ?></td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="<?php echo e(route('admin.directors.show', $director)); ?>" 
                                                       class="btn btn-sm btn-outline-info" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('admin.directors.edit', $director)); ?>" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" action="<?php echo e(route('admin.directors.archive', $director)); ?>" 
                                                          class="d-inline" onsubmit="return confirm('Are you sure you want to archive this director?')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PATCH'); ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Archive">
                                                            <i class="bi bi-archive"></i>
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
                            <i class="bi bi-person-badge fs-1 text-muted"></i>
                            <h4 class="text-muted mt-3">No Directors Found</h4>
                            <p class="text-muted">Start by adding your first director to the system.</p>
                            <a href="<?php echo e(route('admin.directors.create')); ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add Director
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

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\directors\index.blade.php ENDPATH**/ ?>