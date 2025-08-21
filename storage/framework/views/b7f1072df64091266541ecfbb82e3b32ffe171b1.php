

<?php $__env->startSection('title', 'Batch Enrollment Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Batch Enrollment Management</h3>
                    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#createBatchModal">
                        Create New Batch
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Batch Name</th>
                                    <th>Program</th>
                                    <th>Start Date</th>
                                    <th>Schedule</th>
                                    <th>Capacity</th>
                                    <th>Assigned Professor</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($batch->batch_name); ?></td>
                                    <td><?php echo e($batch->program->program_name); ?></td>
                                    <td><?php echo e($batch->start_date->format('M d, Y')); ?></td>
                                    <td><?php echo e($batch->schedule); ?></td>
                                    <td><?php echo e($batch->current_capacity); ?>/<?php echo e($batch->max_capacity); ?></td>
                                    <td>
                                        <?php if($batch->professors && $batch->professors->count() > 0): ?>
                                            <?php $__currentLoopData = $batch->professors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $professor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge bg-info me-1"><?php echo e($professor->professor_name); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <span class="text-muted">No professor assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo e($batch->status === 'active' ? 'success' : 'warning'); ?>">
                                            <?php echo e(ucfirst($batch->status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editBatchModal-<?php echo e($batch->id); ?>">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteBatch(<?php echo e($batch->id); ?>)">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php echo e($batches->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Batch Modal -->
<div class="modal fade" id="createBatchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Batch</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?php echo e(route('admin.batches.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Batch Name</label>
                        <input type="text" class="form-control" name="batch_name" required>
                    </div>
                    <div class="form-group">
                        <label>Program</label>
                        <select class="form-control" name="program_id" required>
                            <option value="">Select Program</option>
                            <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($program->program_id); ?>"><?php echo e($program->program_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label>Maximum Capacity</label>
                        <input type="number" class="form-control" name="max_capacity" required min="1">
                    </div>
                    <div class="form-group">
                        <label>Schedule</label>
                        <input type="text" class="form-control" name="schedule" required placeholder="e.g., MWF 9:00 AM - 12:00 PM">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Batch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<!-- Edit Batch Modal -->
<div class="modal fade" id="editBatchModal-<?php echo e($batch->id); ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Batch</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form class="edit-batch-form" data-batch-id="<?php echo e($batch->id); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Batch Name</label>
                        <input type="text" class="form-control" name="batch_name" value="<?php echo e($batch->batch_name); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" class="form-control" name="start_date" value="<?php echo e($batch->start_date->format('Y-m-d')); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Maximum Capacity</label>
                        <input type="number" class="form-control" name="max_capacity" value="<?php echo e($batch->max_capacity); ?>" required min="1">
                    </div>
                    <div class="form-group">
                        <label>Schedule</label>
                        <input type="text" class="form-control" name="schedule" value="<?php echo e($batch->schedule); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" required>
                            <option value="active" <?php echo e($batch->status === 'active' ? 'selected' : ''); ?>>Active</option>
                            <option value="inactive" <?php echo e($batch->status === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Batch</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function deleteBatch(batchId) {
    if (confirm('Are you sure you want to delete this batch?')) {
        fetch(`/admin/batches/${batchId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            }
        });
    }
}

// AJAX for Edit Batch forms

document.querySelectorAll('.edit-batch-form').forEach(form => {
    form.onsubmit = function(e) {
        e.preventDefault();
        const batchId = form.getAttribute('data-batch-id');
        const formData = new FormData(form);
        fetch(`/admin/batches/${batchId}`, {
            method: 'POST', // Laravel expects POST with _method=PUT
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                // DO NOT set 'Content-Type' here!
            },
            body: formData
        })
        .then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                return response.text().then(text => { throw new Error(text); });
            }
        })
        .catch(error => {
            alert('Update failed: ' + error.message);
        });
    };
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\batch-enrollment\index.blade.php ENDPATH**/ ?>