

<?php $__env->startSection('title', 'Batch Enrollment Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Batch Enrollment Management</h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBatchModal">
                        <i class="fas fa-plus"></i> Create New Batch
                    </button>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo e($error); ?><br>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Batch Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Batches</h6>
                                            <h3 class="mb-0"><?php echo e($batches->count()); ?></h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-graduation-cap fa-2x"></i>
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
                                            <h6 class="card-title">Available</h6>
                                            <h3 class="mb-0"><?php echo e($batches->where('batch_status', 'available')->count()); ?></h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x"></i>
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
                                            <h6 class="card-title">Ongoing</h6>
                                            <h3 class="mb-0"><?php echo e($batches->where('batch_status', 'ongoing')->count()); ?></h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-play-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Closed</h6>
                                            <h3 class="mb-0"><?php echo e($batches->where('batch_status', 'closed')->count()); ?></h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-times-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Batches Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Batch Name</th>
                                    <th>Program</th>
                                    <th>Capacity</th>
                                    <th>Status</th>
                                    <th>Registration Deadline</th>
                                    <th>Start Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($batch->batch_name); ?></td>
                                    <td><?php echo e($batch->program->program_name ?? 'N/A'); ?></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?php echo e($batch->current_capacity > 0 ? ($batch->current_capacity / $batch->max_capacity) * 100 : 0); ?>%"
                                                 aria-valuenow="<?php echo e($batch->current_capacity); ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="<?php echo e($batch->max_capacity); ?>">
                                                <?php echo e($batch->current_capacity); ?>/<?php echo e($batch->max_capacity); ?>

                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            <?php if($batch->batch_status === 'available'): ?> badge-success
                                            <?php elseif($batch->batch_status === 'ongoing'): ?> badge-warning
                                            <?php else: ?> badge-danger
                                            <?php endif; ?>">
                                            <?php if($batch->batch_status === 'available'): ?>
                                                Available
                                            <?php elseif($batch->batch_status === 'ongoing'): ?>
                                                Ongoing
                                            <?php else: ?>
                                                Closed
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td><?php echo e(\Carbon\Carbon::parse($batch->registration_deadline)->format('M d, Y')); ?></td>
                                    <td><?php echo e(\Carbon\Carbon::parse($batch->start_date)->format('M d, Y')); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editBatchModal<?php echo e($batch->batch_id); ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="deleteBatch(<?php echo e($batch->batch_id); ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                onclick="toggleStatus(<?php echo e($batch->batch_id); ?>)">
                                            <i class="fas fa-toggle-on"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No batches found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Batch Modal -->
<div class="modal fade" id="createBatchModal" tabindex="-1" aria-labelledby="createBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createBatchModalLabel">Create New Batch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?php echo e(route('admin.batches.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="batch_name" class="form-label">Batch Name</label>
                        <input type="text" class="form-control" id="batch_name" name="batch_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="program_id" class="form-label">Program</label>
                        <select class="form-control" id="program_id" name="program_id" required>
                            <option value="">Select a program</option>
                            <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($program->program_id); ?>"><?php echo e($program->program_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="max_capacity" class="form-label">Maximum Capacity</label>
                        <input type="number" class="form-control" id="max_capacity" name="max_capacity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="registration_deadline" class="form-label">Registration Deadline</label>
                        <input type="date" class="form-control" id="registration_deadline" name="registration_deadline" required>
                    </div>
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Batch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Set minimum dates for date inputs
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const registrationDeadlineInput = document.getElementById('registration_deadline');
    const startDateInput = document.getElementById('start_date');
    
    if(registrationDeadlineInput) {
        registrationDeadlineInput.min = today;
        
        // Update start date minimum when registration deadline changes
        registrationDeadlineInput.addEventListener('change', function() {
            if(startDateInput) {
                startDateInput.min = this.value;
            }
        });
    }
});

function deleteBatch(batchId) {
    if(confirm('Are you sure you want to delete this batch?')) {
        // Implementation for delete batch
        console.log('Delete batch:', batchId);
    }
}

function toggleStatus(batchId) {
    if(confirm('Are you sure you want to toggle the status of this batch?')) {
        // Implementation for toggle status
        console.log('Toggle status for batch:', batchId);
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\admin-student-enrollment\batch-enroll-fixed.blade.php ENDPATH**/ ?>