

<?php $__env->startSection('title', 'Director Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-person-badge"></i> Director Details</h2>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('admin.directors.edit', $director)); ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="<?php echo e(route('admin.directors.index')); ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Directors
                    </a>
                </div>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Director Information -->
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-person-circle"></i> Director Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td><?php echo e($director->directors_name); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><?php echo e($director->directors_email); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Created By:</strong></td>
                                    <td><?php echo e($director->admin ? $director->admin->admin_name : 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Created Date:</strong></td>
                                    <td><?php echo e($director->created_at->format('M d, Y')); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Assigned Programs -->
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-book"></i> Assigned Programs</h5>
                        </div>
                        <div class="card-body">
                            <?php if($director->programs->count() > 0): ?>
                                <div class="list-group">
                                    <?php $__currentLoopData = $director->programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo e($program->program_name); ?></h6>
                                                <p class="mb-1 text-muted"><?php echo e(Str::limit($program->program_description, 60)); ?></p>
                                            </div>
                                            <form method="POST" action="<?php echo e(route('admin.directors.unassign-program', $director)); ?>" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="program_id" value="<?php echo e($program->program_id); ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Unassign this program?')" title="Unassign">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No programs assigned to this director.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assign New Program -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Assign Program</h5>
                        </div>
                        <div class="card-body">
                                    <?php
                                        $query = \App\Models\Program::where('is_archived', false);
                                        if ($director->programs->count() > 0) {
                                            $assignedProgramIds = $director->programs->pluck('program_id')->toArray();
                                            $query->whereNotIn('program_id', $assignedProgramIds);
                                        }
                                        $availablePrograms = $query->get();
                                    ?>

                            <?php if($availablePrograms->count() > 0): ?>
                                <form method="POST" action="<?php echo e(route('admin.directors.assign-program', $director)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <div class="row align-items-end">
                                        <div class="col-md-8">
                                            <label for="program_id" class="form-label">Select Program</label>
                                            <select name="program_id" id="program_id" class="form-select" required>
                                                <option value="">Choose a program...</option>
                                                <?php $__currentLoopData = $availablePrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($program->program_id); ?>">
                                                        <?php echo e($program->program_name); ?>

                                                        <?php if($program->director_id): ?>
                                                            (Currently assigned to <?php echo e($program->director->full_name); ?>)
                                                        <?php endif; ?>
                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-plus"></i> Assign Program
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            <?php else: ?>
                                <p class="text-muted">All available programs are already assigned.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\directors\show.blade.php ENDPATH**/ ?>