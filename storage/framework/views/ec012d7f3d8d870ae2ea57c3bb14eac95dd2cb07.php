

<?php $__env->startSection('title', 'Batch Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="bi bi-people-fill me-2"></i>Batch Management
                    </h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBatchModal">
                        <i class="bi bi-plus-circle me-1"></i>Create New Batch
                    </button>
                </div>
                
                <div class="card-body">
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

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Batch Name</th>
                                    <th>Program</th>
                                    <th>Status</th>
                                    <th>Capacity</th>
                                    <th>Registration Deadline</th>
                                    <th>Start Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($batch->batch_name); ?></strong>
                                        <?php if($batch->batch_description): ?>
                                            <br><small class="text-muted"><?php echo e(Str::limit($batch->batch_description, 50)); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($batch->program->program_name); ?></td>
                                    <td>
                                        <?php if($batch->isAvailable()): ?>
                                            <span class="badge bg-success">Available</span>
                                        <?php elseif($batch->isOngoing()): ?>
                                            <span class="badge bg-warning">Ongoing</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Closed</span>
                                        <?php endif; ?>
                                        <br><small><?php echo e($batch->enrolled_count); ?>/<?php echo e($batch->capacity); ?> students</small>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar <?php echo e($batch->enrolled_count >= $batch->capacity ? 'bg-danger' : 'bg-primary'); ?>" 
                                                 role="progressbar" 
                                                 style="width: <?php echo e(($batch->enrolled_count / $batch->capacity) * 100); ?>%">
                                                <?php echo e($batch->enrolled_count); ?>/<?php echo e($batch->capacity); ?>

                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo e($batch->registration_deadline->format('M d, Y')); ?></td>
                                    <td><?php echo e($batch->start_date->format('M d, Y')); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('admin.batches.show', $batch->batch_id)); ?>" 
                                               class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-secondary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editBatchModal"
                                                    data-batch-id="<?php echo e($batch->batch_id); ?>"
                                                    data-batch-name="<?php echo e($batch->batch_name); ?>"
                                                    data-batch-description="<?php echo e($batch->batch_description); ?>"
                                                    data-capacity="<?php echo e($batch->capacity); ?>"
                                                    data-start-date="<?php echo e($batch->start_date->format('Y-m-d')); ?>"
                                                    data-end-date="<?php echo e($batch->end_date ? $batch->end_date->format('Y-m-d') : ''); ?>"
                                                    data-registration-deadline="<?php echo e($batch->registration_deadline->format('Y-m-d')); ?>"
                                                    title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-info dropdown-toggle" 
                                                        type="button" 
                                                        data-bs-toggle="dropdown">
                                                    Status
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="updateBatchStatus(<?php echo e($batch->batch_id); ?>, 'available')">Available</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="updateBatchStatus(<?php echo e($batch->batch_id); ?>, 'ongoing')">Ongoing</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="updateBatchStatus(<?php echo e($batch->batch_id); ?>, 'closed')">Closed</a></li>
                                                </ul>
                                            </div>
                                            <?php if($batch->enrolled_count == 0): ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteBatch(<?php echo e($batch->batch_id); ?>)"
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            No batches created yet. Create your first batch to get started.
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php echo e($batches->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Batch Modal -->
<div class="modal fade" id="createBatchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.batches.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Create New Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="batch_name" class="form-label">Batch Name *</label>
                            <input type="text" class="form-control" id="batch_name" name="batch_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="program_id" class="form-label">Program *</label>
                            <select class="form-control" id="program_id" name="program_id" required>
                                <option value="">Select Program</option>
                                <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($program->program_id); ?>"><?php echo e($program->program_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="batch_description" class="form-label">Description</label>
                            <textarea class="form-control" id="batch_description" name="batch_description" rows="3"></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="capacity" class="form-label">Capacity *</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" max="100" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="start_date" class="form-label">Start Date *</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="registration_deadline" class="form-label">Registration Deadline *</label>
                            <input type="date" class="form-control" id="registration_deadline" name="registration_deadline" required>
                        </div>
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

<!-- Edit Batch Modal -->
<div class="modal fade" id="editBatchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editBatchForm" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Edit Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_batch_name" class="form-label">Batch Name *</label>
                            <input type="text" class="form-control" id="edit_batch_name" name="batch_name" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="edit_batch_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_batch_description" name="batch_description" rows="3"></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_capacity" class="form-label">Capacity *</label>
                            <input type="number" class="form-control" id="edit_capacity" name="capacity" min="1" max="100" required>
                            <small class="text-muted">Cannot be less than current enrollment count</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_start_date" class="form-label">Start Date *</label>
                            <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="edit_end_date" name="end_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_registration_deadline" class="form-label">Registration Deadline *</label>
                            <input type="date" class="form-control" id="edit_registration_deadline" name="registration_deadline" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Batch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Update batch status
function updateBatchStatus(batchId, status) {
    if (confirm('Are you sure you want to change the batch status?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/batches/${batchId}/status`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        
        const statusField = document.createElement('input');
        statusField.type = 'hidden';
        statusField.name = 'status';
        statusField.value = status;
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        form.appendChild(statusField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Delete batch
function deleteBatch(batchId) {
    if (confirm('Are you sure you want to delete this batch? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/batches/${batchId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Handle edit batch modal
document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editBatchModal');
    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const batchId = button.getAttribute('data-batch-id');
        
        // Update form action
        document.getElementById('editBatchForm').action = `/admin/batches/${batchId}`;
        
        // Populate form fields
        document.getElementById('edit_batch_name').value = button.getAttribute('data-batch-name');
        document.getElementById('edit_batch_description').value = button.getAttribute('data-batch-description') || '';
        document.getElementById('edit_capacity').value = button.getAttribute('data-capacity');
        document.getElementById('edit_start_date').value = button.getAttribute('data-start-date');
        document.getElementById('edit_end_date').value = button.getAttribute('data-end-date') || '';
        document.getElementById('edit_registration_deadline').value = button.getAttribute('data-registration-deadline');
    });
});

// Set minimum dates for date inputs
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('start_date').min = today;
    document.getElementById('registration_deadline').min = today;
    
    // Update end date minimum when start date changes
    document.getElementById('start_date').addEventListener('change', function() {
        document.getElementById('end_date').min = this.value;
    });
    
    // Update start date minimum when registration deadline changes
    document.getElementById('registration_deadline').addEventListener('change', function() {
        document.getElementById('start_date').min = this.value;
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\admin-student-enrollment\batch-management.blade.php ENDPATH**/ ?>