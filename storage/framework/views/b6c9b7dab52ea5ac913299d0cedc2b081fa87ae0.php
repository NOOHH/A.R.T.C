

<?php $__env->startSection('title', 'Edit Director'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-pencil"></i> Edit Director: <?php echo e($director->full_name); ?></h2>
                <a href="<?php echo e(route('admin.directors.index')); ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Directors
                </a>
            </div>

            <div class="card shadow">
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('admin.directors.update', $director)); ?>" autocomplete="off">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
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
                                           id="directors_first_name" name="directors_first_name" value="<?php echo e(old('directors_first_name', $director->directors_first_name)); ?>" required>
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
                                           id="directors_last_name" name="directors_last_name" value="<?php echo e(old('directors_last_name', $director->directors_last_name)); ?>" required>
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
                                           id="directors_email" name="directors_email" value="<?php echo e(old('directors_email', $director->directors_email)); ?>" required autocomplete="off">
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
                                    <label for="directors_password" class="form-label">Password (leave blank to keep current)</label>
                                    <input type="password" class="form-control <?php $__errorArgs = ['directors_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="directors_password" name="directors_password" autocomplete="new-password">
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="referral_code" class="form-label">Referral Code</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control <?php $__errorArgs = ['referral_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               id="referral_code" name="referral_code" 
                                               value="<?php echo e(old('referral_code', $director->referral_code)); ?>" 
                                               placeholder="Auto-generated if empty">
                                        <button type="button" class="btn btn-outline-secondary" onclick="generateReferralCode()" title="Generate New Code">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Current: <?php echo e($director->referral_code ?? 'Not set'); ?></div>
                                    <?php $__errorArgs = ['referral_code'];
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
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Assign Programs <span class="text-danger">*</span></label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="selectAllPrograms" onclick="toggleAllProgramsCheckboxes(this)">
                                        <label class="form-check-label" for="selectAllPrograms">Select All Programs</label>
                                    </div>
                                    <div id="programCheckboxList" style="border: 1px solid #ced4da; border-radius: 0.375rem; max-height: 220px; overflow-y: auto; padding: 0.75rem; background: #fafbfc;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="program_all" name="program_access[]" value="all" <?php echo e($director->has_all_program_access ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="program_all">All Programs</label>
                                        </div>
                                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="program_<?php echo e($program->program_id); ?>" name="program_access[]" value="<?php echo e($program->program_id); ?>" <?php echo e($director->assignedPrograms->contains('program_id', $program->program_id) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="program_<?php echo e($program->program_id); ?>"><?php echo e($program->program_name); ?></label>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    <div class="form-text">Check one or more programs, or select 'All Programs'.</div>
                                    <?php $__errorArgs = ['program_access'];
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

                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?php echo e(route('admin.directors.index')); ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Director
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function generateReferralCode() {
    const firstName = document.getElementById('directors_first_name').value.split(' ')[0] || '';
    const lastName = document.getElementById('directors_last_name').value.split(' ')[0] || '';
    
    if (!firstName.trim() && !lastName.trim()) {
        alert('Please enter the director name first');
        return;
    }
    
    // Generate code based on name
    const cleanFirstName = firstName.replace(/[^A-Za-z]/g, '').toUpperCase();
    const cleanLastName = lastName.replace(/[^A-Za-z]/g, '').toUpperCase();
    
    // Get current director ID from URL
    const currentId = window.location.pathname.split('/').pop();
    const directorId = String(currentId).padStart(2, '0');
    
    // Generate code: DIR + ID + NAME_INITIALS
    const nameCode = cleanFirstName.substring(0, 2) + cleanLastName.substring(0, 2);
    const referralCode = 'DIR' + directorId + nameCode;
    
    document.getElementById('referral_code').value = referralCode;
}

function toggleAllProgramsCheckboxes(checkbox) {
    const checkboxes = document.querySelectorAll('#programCheckboxList input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\directors\edit.blade.php ENDPATH**/ ?>