

<?php $__env->startSection('title', 'Add Director'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-person-plus"></i> Add New Director</h2>
                <a href="<?php echo e(route('admin.directors.index')); ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Directors
                </a>
            </div>

            <div class="card shadow">
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('admin.directors.store')); ?>" autocomplete="off">
                        <?php echo csrf_field(); ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="directors_name" class="form-label">Director Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['directors_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="directors_name" name="directors_name" value="<?php echo e(old('directors_name')); ?>" required>
                                    <?php $__errorArgs = ['directors_name'];
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
                                    <label for="directors_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control <?php $__errorArgs = ['directors_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="directors_email" name="directors_email" value="<?php echo e(old('directors_email')); ?>" required autocomplete="off">
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
                        </div>

                        <div class="row">
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
                                           id="directors_password" name="directors_password" required autocomplete="new-password">
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
                                               id="referral_code" name="referral_code" value="<?php echo e(old('referral_code')); ?>" 
                                               placeholder="Auto-generated if empty">
                                        <button type="button" class="btn btn-outline-secondary" onclick="generateReferralCode()" title="Generate New Code">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Leave empty to auto-generate based on name (e.g., DIR01NAME)</div>
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
                                    <label for="program_access" class="form-label">Assign Programs <span class="text-danger">*</span></label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="selectAllPrograms" onclick="toggleAllPrograms(this)">
                                        <label class="form-check-label" for="selectAllPrograms">Select All Programs</label>
                                    </div>
                                    <select class="form-select" id="program_access" name="program_access[]" multiple required>
                                        <option value="all">All Programs</option>
                                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($program->program_id); ?>"><?php echo e($program->program_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div class="form-text">Hold Ctrl (Windows) or Cmd (Mac) to select multiple programs, or select 'All Programs'.</div>
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
                                <i class="bi bi-save"></i> Save Director
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
    const firstName = document.getElementById('directors_name').value.split(' ')[0] || '';
    const lastName = document.getElementById('directors_name').value.split(' ').slice(1).join(' ') || '';
    
    if (!firstName.trim()) {
        alert('Please enter the director name first');
        return;
    }
    
    // Generate code based on name
    const cleanFirstName = firstName.replace(/[^A-Za-z]/g, '').toUpperCase();
    const cleanLastName = lastName.replace(/[^A-Za-z]/g, '').toUpperCase();
    
    // Get next director ID (simplified - in production should be from server)
    const nextId = String(Math.floor(Math.random() * 99) + 1).padStart(2, '0');
    
    // Generate code: DIR + ID + NAME_INITIALS
    const nameCode = cleanFirstName.substring(0, 1) + cleanLastName.substring(0, 3);
    const referralCode = 'DIR' + nextId + nameCode;
    
    document.getElementById('referral_code').value = referralCode;
}

// Auto-generate when name changes
document.getElementById('directors_name').addEventListener('input', function() {
    const referralCodeField = document.getElementById('referral_code');
    if (!referralCodeField.value.trim()) {
        setTimeout(generateReferralCode, 500); // Small delay for better UX
    }
});

function toggleAllPrograms(checkbox) {
    const select = document.getElementById('program_access');
    for (let i = 0; i < select.options.length; i++) {
        select.options[i].selected = checkbox.checked;
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\directors\create.blade.php ENDPATH**/ ?>