

<?php $__env->startSection('title', 'Professor Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Professor Management</h2>
                    <p class="text-muted">Manage professors and their program assignments</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('admin.professors.archived')); ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-archive"></i> Archived Professors
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProfessorModal">
                        <i class="bi bi-plus-circle"></i> Add Professor
                    </button>
                </div>
            </div>

            <!-- Professors Table -->
            <div class="card shadow border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Active Professors</h5>
                </div>
                <div class="card-body p-0">
                    <?php if($professors->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Professor</th>
                                        <th>Email</th>
                                        <th>Assigned Programs</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $professors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $professor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="bi bi-person text-white"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold"><?php echo e($professor->full_name); ?></div>
                                                    <small class="text-muted"><?php echo e($professor->email); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo e($professor->email); ?></td>
                                        <td>
                                            <?php if($professor->programs->count() > 0): ?>
                                                <div class="d-flex flex-wrap gap-1">
                                                    <?php $__currentLoopData = $professor->programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <span class="badge bg-success"><?php echo e($program->program_name); ?></span>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">No assignments</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('admin.professors.edit', $professor->professor_id)); ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-success"
                                                        onclick="showSimpleModal('<?php echo e($professor->professor_id); ?>', '<?php echo e($professor->full_name); ?>')"
                                                        data-professor-id="<?php echo e($professor->professor_id); ?>"
                                                        data-professor-name="<?php echo e($professor->full_name); ?>">
                                                    <i class="bi bi-calendar-plus"></i>
                                                </button>
                                                <a href="<?php echo e(route('admin.professors.meetings', $professor->professor_id)); ?>" 
                                                   class="btn btn-sm btn-outline-secondary"
                                                   title="View Meetings">
                                                    <i class="bi bi-calendar2-week"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                        data-bs-toggle="modal" data-bs-target="#archiveModal"
                                                        data-professor-id="<?php echo e($professor->professor_id); ?>"
                                                        data-professor-name="<?php echo e($professor->full_name); ?>">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-professor-id="<?php echo e($professor->professor_id); ?>"
                                                        data-professor-name="<?php echo e($professor->full_name); ?>">
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
                        <?php if($professors->hasPages()): ?>
                            <div class="p-3">
                                <?php echo e($professors->links()); ?>

                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-person-workspace display-1 text-muted"></i>
                            <h5 class="mt-3">No Professors Found</h5>
                            <p class="text-muted">Start by adding your first professor.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Professor Modal -->
<div class="modal fade" id="addProfessorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.professors.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Add New Professor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prof_referral_code" class="form-label">Referral Code</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="prof_referral_code" name="referral_code" 
                                           placeholder="Auto-generated if empty">
                                    <button type="button" class="btn btn-outline-secondary" onclick="generateProfessorReferralCode()" title="Generate New Code">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>
                                <div class="form-text">Leave empty to auto-generate (e.g., PROF01NAME)</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Assign to Programs</label>
                        <div class="row">
                            <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="programs[]" value="<?php echo e($program->program_id); ?>" 
                                               id="program_<?php echo e($program->program_id); ?>">
                                        <label class="form-check-label" for="program_<?php echo e($program->program_id); ?>">
                                            <?php echo e($program->program_name); ?>

                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Professor</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Archive Confirmation Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Archive Professor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to archive <strong id="archiveProfessorName"></strong>?</p>
                <p class="text-muted">Archived professors can be restored later.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="archiveForm" action="<?php echo e(route('admin.professors.index')); ?>" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <button type="submit" class="btn btn-warning">Archive</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Professor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to permanently delete <strong id="deleteProfessorName"></strong>?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" action="<?php echo e(route('admin.professors.index')); ?>" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.professors.partials.create-meeting-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Archive modal
    $('#archiveModal').on('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const professorId = button.getAttribute('data-professor-id');
        const professorName = button.getAttribute('data-professor-name');
        
        document.getElementById('archiveProfessorName').textContent = professorName;
        document.getElementById('archiveForm').action = `/admin/professors/${professorId}/archive`;
    });

    // Delete modal
    $('#deleteModal').on('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const professorId = button.getAttribute('data-professor-id');
        const professorName = button.getAttribute('data-professor-name');
        
        document.getElementById('deleteProfessorName').textContent = professorName;
        document.getElementById('deleteForm').action = `/admin/professors/${professorId}`;
    });
});

function generateProfessorReferralCode() {
    const firstName = document.getElementById('first_name').value || '';
    const lastName = document.getElementById('last_name').value || '';
    
    if (!firstName.trim() || !lastName.trim()) {
        return;
    }
    
    // Generate code based on name
    const cleanFirstName = firstName.replace(/[^A-Za-z]/g, '').toUpperCase();
    const cleanLastName = lastName.replace(/[^A-Za-z]/g, '').toUpperCase();
    
    // Get next professor ID (simplified - in production should be from server)
    const nextId = String(Math.floor(Math.random() * 99) + 1).padStart(2, '0');
    
    // Generate code: PROF + ID + NAME_INITIALS
    const nameCode = cleanFirstName.substring(0, 2) + cleanLastName.substring(0, 2);
    const referralCode = 'PROF' + nextId + nameCode;
    
    document.getElementById('prof_referral_code').value = referralCode;
}

// Auto-generate when name changes
document.addEventListener('DOMContentLoaded', function() {
    const firstNameField = document.getElementById('first_name');
    const lastNameField = document.getElementById('last_name');
    
    function autoGenerateCode() {
        const referralCodeField = document.getElementById('prof_referral_code');
        if (!referralCodeField.value.trim()) {
            setTimeout(generateProfessorReferralCode, 500);
        }
    }
    
    if (firstNameField) firstNameField.addEventListener('input', autoGenerateCode);
    if (lastNameField) lastNameField.addEventListener('input', autoGenerateCode);
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/admin/professors/index.blade.php ENDPATH**/ ?>