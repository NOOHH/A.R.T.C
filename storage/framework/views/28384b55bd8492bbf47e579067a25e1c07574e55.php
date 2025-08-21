

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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDirectorModal">
                        <i class="bi bi-plus-circle"></i> Add Director
                    </button>
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
                                        <th>Created Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $directors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $director): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo e($director->full_name); ?></strong>
                                            </td>
                                            <td><?php echo e($director->directors_email); ?></td>
                                            <td>
                                                <?php if($director->has_all_program_access): ?>
                                                    <span class="badge bg-primary">All Programs</span>
                                                <?php else: ?>
                                                    <?php
                                                        // Get programs from both relationships
                                                        $allAssignedPrograms = collect();
                                                        if($director->programs->count() > 0) {
                                                            $allAssignedPrograms = $allAssignedPrograms->merge($director->programs);
                                                        }
                                                        if($director->assignedPrograms->count() > 0) {
                                                            $allAssignedPrograms = $allAssignedPrograms->merge($director->assignedPrograms);
                                                        }
                                                        $allAssignedPrograms = $allAssignedPrograms->unique('program_id');
                                                    ?>
                                                    
                                                    <?php if($allAssignedPrograms->count() > 0): ?>
                                                        <?php $__currentLoopData = $allAssignedPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <span class="badge bg-info me-1 mb-1"><?php echo e($program->program_name); ?></span>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">No programs assigned</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($director->created_at->format('M d, Y')); ?></td>
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
                                                          style="display: inline;" 
                                                          onsubmit="return confirm('Are you sure you want to archive <?php echo e($director->full_name); ?>?');">
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
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDirectorModal">
                                <i class="bi bi-plus-circle"></i> Add Director
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Director Modal -->
<div class="modal fade" id="addDirectorModal" tabindex="-1" aria-labelledby="addDirectorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDirectorModalLabel">
                    <i class="bi bi-person-plus"></i> Add New Director
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?php echo e(route('admin.directors.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="directors_first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php $__errorArgs = ['directors_first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="directors_first_name" name="directors_first_name" value="<?php echo e(old('directors_first_name')); ?>" required>
                                <?php $__errorArgs = ['directors_first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="directors_last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php $__errorArgs = ['directors_last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="directors_last_name" name="directors_last_name" value="<?php echo e(old('directors_last_name')); ?>" required>
                                <?php $__errorArgs = ['directors_last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="directors_email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control <?php $__errorArgs = ['directors_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="directors_email" name="directors_email" value="<?php echo e(old('directors_email')); ?>" required>
                                <?php $__errorArgs = ['directors_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="directors_password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control <?php $__errorArgs = ['directors_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="directors_password" name="directors_password" required>
                                <?php $__errorArgs = ['directors_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Program Access</label>
                                <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="all" id="program_all" name="program_access[]" checked>
                                        <label class="form-check-label fw-bold text-primary" for="program_all">
                                            All Programs
                                        </label>
                                    </div>
                                    <hr class="my-2">
                                    <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="form-check">
                                            <input class="form-check-input program-checkbox" type="checkbox" value="<?php echo e($program->program_id); ?>" 
                                                   id="program_<?php echo e($program->program_id); ?>" name="program_access[]">
                                            <label class="form-check-label" for="program_<?php echo e($program->program_id); ?>">
                                                <?php echo e($program->program_name); ?>

                                            </label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <?php $__errorArgs = ['program_access'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text">
                                    Select "All Programs" for full access, or choose specific programs. You can select multiple programs.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Add Director
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Reopen modal with validation errors
<?php if($errors->any()): ?>
    document.addEventListener('DOMContentLoaded', function() {
        var addDirectorModal = new bootstrap.Modal(document.getElementById('addDirectorModal'));
        addDirectorModal.show();
    });
<?php endif; ?>

// Clear form when modal is closed
const addDirectorModal = document.getElementById('addDirectorModal');
if (addDirectorModal) {
    addDirectorModal.addEventListener('hidden.bs.modal', function () {
        // Clear form fields
        const form = this.querySelector('form');
        form.reset();
        
        // Remove validation error classes
        form.querySelectorAll('.is-invalid').forEach(function(element) {
            element.classList.remove('is-invalid');
        });
        
        // Hide error messages
        form.querySelectorAll('.invalid-feedback').forEach(function(element) {
            element.style.display = 'none';
        });
        
        // Reset program access to "All Programs"
        const allProgramsCheckbox = document.getElementById('program_all');
        if (allProgramsCheckbox) {
            allProgramsCheckbox.checked = true;
        }
        
        // Uncheck all individual program checkboxes
        const programCheckboxes = document.querySelectorAll('.program-checkbox');
        programCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    });
}

// Handle program access selection logic
document.addEventListener('DOMContentLoaded', function() {
    const allProgramsCheckbox = document.getElementById('program_all');
    const programCheckboxes = document.querySelectorAll('.program-checkbox');
    
    // Check if elements exist before adding event listeners
    if (!allProgramsCheckbox) {
        console.warn('Element with ID "program_all" not found');
        return;
    }
    
    // Handle "All Programs" checkbox
    allProgramsCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // If "All Programs" is checked, uncheck all individual programs
            programCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    });
    
    // Handle individual program checkboxes
    programCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                // If any individual program is checked, uncheck "All Programs"
                allProgramsCheckbox.checked = false;
            }
            
            // If no individual programs are checked, check "All Programs"
            const anyChecked = Array.from(programCheckboxes).some(cb => cb.checked);
            if (!anyChecked) {
                allProgramsCheckbox.checked = true;
            }
        });
    });
});
</script>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\directors\director.blade.php ENDPATH**/ ?>