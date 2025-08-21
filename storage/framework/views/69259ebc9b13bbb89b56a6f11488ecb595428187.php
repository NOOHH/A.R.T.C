<form id="editRegistrationForm" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Editing Registration:</strong> Please correct the highlighted fields and resubmit your registration.
    </div>
    
    <?php if(!empty($rejectedFields)): ?>
    <div class="alert alert-warning">
        <h6><i class="bi bi-exclamation-triangle me-2"></i>Fields that need correction:</h6>
        <ul class="mb-0">
            <?php $__currentLoopData = $rejectedFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><strong><?php echo e(str_replace('_', ' ', strtoupper($field))); ?></strong></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Basic Information -->
        <div class="col-md-12">
            <h6 class="border-bottom pb-2 mb-3">Personal Information</h6>
        </div>
        
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="firstname" class="form-label">
                    First Name <span class="text-danger">*</span>
                    <?php if(in_array('firstname', $rejectedFields)): ?>
                        <span class="badge bg-danger ms-1">Needs Correction</span>
                    <?php endif; ?>
                </label>
                <input type="text" 
                       id="firstname" 
                       name="firstname" 
                       class="form-control <?php echo e(in_array('firstname', $rejectedFields) ? 'border-danger' : ''); ?>" 
                       value="<?php echo e($enrollment->firstname ?? $user->user_firstname ?? ''); ?>" 
                       required>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="lastname" class="form-label">
                    Last Name <span class="text-danger">*</span>
                    <?php if(in_array('lastname', $rejectedFields)): ?>
                        <span class="badge bg-danger ms-1">Needs Correction</span>
                    <?php endif; ?>
                </label>
                <input type="text" 
                       id="lastname" 
                       name="lastname" 
                       class="form-control <?php echo e(in_array('lastname', $rejectedFields) ? 'border-danger' : ''); ?>" 
                       value="<?php echo e($enrollment->lastname ?? $user->user_lastname ?? ''); ?>" 
                       required>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="middlename" class="form-label">
                    Middle Name
                    <?php if(in_array('middlename', $rejectedFields)): ?>
                        <span class="badge bg-danger ms-1">Needs Correction</span>
                    <?php endif; ?>
                </label>
                <input type="text" 
                       id="middlename" 
                       name="middlename" 
                       class="form-control <?php echo e(in_array('middlename', $rejectedFields) ? 'border-danger' : ''); ?>" 
                       value="<?php echo e($enrollment->middlename ?? ''); ?>">
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="email" class="form-label">
                    Email Address <span class="text-danger">*</span>
                    <?php if(in_array('email', $rejectedFields)): ?>
                        <span class="badge bg-danger ms-1">Needs Correction</span>
                    <?php endif; ?>
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control <?php echo e(in_array('email', $rejectedFields) ? 'border-danger' : ''); ?>" 
                       value="<?php echo e($enrollment->email ?? $user->user_email ?? ''); ?>" 
                       required>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="contact_number" class="form-label">
                    Contact Number <span class="text-danger">*</span>
                    <?php if(in_array('contact_number', $rejectedFields)): ?>
                        <span class="badge bg-danger ms-1">Needs Correction</span>
                    <?php endif; ?>
                </label>
                <input type="tel" 
                       id="contact_number" 
                       name="contact_number" 
                       class="form-control <?php echo e(in_array('contact_number', $rejectedFields) ? 'border-danger' : ''); ?>" 
                       value="<?php echo e($enrollment->contact_number ?? ''); ?>" 
                       required>
            </div>
        </div>
        
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label for="address" class="form-label">
                    Address <span class="text-danger">*</span>
                    <?php if(in_array('address', $rejectedFields)): ?>
                        <span class="badge bg-danger ms-1">Needs Correction</span>
                    <?php endif; ?>
                </label>
                <textarea id="address" 
                          name="address" 
                          class="form-control <?php echo e(in_array('address', $rejectedFields) ? 'border-danger' : ''); ?>" 
                          rows="3" 
                          required><?php echo e($enrollment->address ?? ''); ?></textarea>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="education_level" class="form-label">
                    Education Level <span class="text-danger">*</span>
                    <?php if(in_array('education_level', $rejectedFields)): ?>
                        <span class="badge bg-danger ms-1">Needs Correction</span>
                    <?php endif; ?>
                </label>
                <select id="education_level" 
                        name="education_level" 
                        class="form-select <?php echo e(in_array('education_level', $rejectedFields) ? 'border-danger' : ''); ?>" 
                        required>
                    <option value="">Select Education Level</option>
                    <option value="elementary" <?php echo e(($enrollment->education_level ?? '') == 'elementary' ? 'selected' : ''); ?>>Elementary Graduate</option>
                    <option value="high_school" <?php echo e(($enrollment->education_level ?? '') == 'high_school' ? 'selected' : ''); ?>>High School Graduate</option>
                    <option value="college_undergraduate" <?php echo e(($enrollment->education_level ?? '') == 'college_undergraduate' ? 'selected' : ''); ?>>College Undergraduate</option>
                    <option value="college_graduate" <?php echo e(($enrollment->education_level ?? '') == 'college_graduate' ? 'selected' : ''); ?>>College Graduate</option>
                    <option value="vocational" <?php echo e(($enrollment->education_level ?? '') == 'vocational' ? 'selected' : ''); ?>>Vocational Graduate</option>
                    <option value="masters" <?php echo e(($enrollment->education_level ?? '') == 'masters' ? 'selected' : ''); ?>>Master's Degree</option>
                    <option value="doctorate" <?php echo e(($enrollment->education_level ?? '') == 'doctorate' ? 'selected' : ''); ?>>Doctorate Degree</option>
                </select>
            </div>
        </div>
        
        <!-- Document Uploads -->
        <div class="col-md-12 mt-4">
            <h6 class="border-bottom pb-2 mb-3">Required Documents</h6>
            <p class="text-muted small">Please upload clear, readable copies of the required documents. Accepted formats: JPG, PNG, PDF (Max 5MB each)</p>
        </div>
        
        <?php
            $fileFields = [
                'tor' => 'Transcript of Records (TOR)',
                'psa_birth_certificate' => 'PSA Birth Certificate', 
                'good_moral_certificate' => 'Good Moral Certificate',
                'certificate' => 'Certificate/Diploma',
                'photo' => 'ID Photo (2x2)',
            ];
        ?>
        
        <?php $__currentLoopData = $fileFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fieldName => $fieldLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="<?php echo e($fieldName); ?>" class="form-label">
                    <?php echo e($fieldLabel); ?>

                    <?php if(in_array($fieldName, $rejectedFields)): ?>
                        <span class="text-danger">*</span>
                        <span class="badge bg-danger ms-1">Needs Correction</span>
                    <?php endif; ?>
                </label>
                <input type="file" 
                       id="<?php echo e($fieldName); ?>" 
                       name="<?php echo e($fieldName); ?>" 
                       class="form-control <?php echo e(in_array($fieldName, $rejectedFields) ? 'border-danger' : ''); ?>" 
                       accept=".jpg,.jpeg,.png,.pdf">
                
                <?php if($enrollment->{$fieldName}): ?>
                <div class="mt-2">
                    <small class="text-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Current file: <?php echo e(basename($enrollment->{$fieldName})); ?>

                    </small>
                    <br>
                    <small class="text-muted">Upload a new file to replace the current one</small>
                </div>
                <?php endif; ?>
                
                <?php if(in_array($fieldName, $rejectedFields)): ?>
                <div class="mt-1">
                    <small class="text-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        This document needs to be corrected or replaced
                    </small>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        
        <!-- Additional Fields based on Form Requirements -->
        <?php $__currentLoopData = $formRequirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $requirement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($requirement->field_type === 'file' && !in_array($requirement->field_name, array_keys($fileFields))): ?>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="<?php echo e($requirement->field_name); ?>" class="form-label">
                        <?php echo e($requirement->field_label); ?>

                        <?php if($requirement->is_required || in_array($requirement->field_name, $rejectedFields)): ?>
                            <span class="text-danger">*</span>
                        <?php endif; ?>
                        <?php if(in_array($requirement->field_name, $rejectedFields)): ?>
                            <span class="badge bg-danger ms-1">Needs Correction</span>
                        <?php endif; ?>
                    </label>
                    <input type="file" 
                           id="<?php echo e($requirement->field_name); ?>" 
                           name="<?php echo e($requirement->field_name); ?>" 
                           class="form-control <?php echo e(in_array($requirement->field_name, $rejectedFields) ? 'border-danger' : ''); ?>" 
                           accept=".jpg,.jpeg,.png,.pdf"
                           <?php echo e($requirement->is_required ? 'required' : ''); ?>>
                    
                    <?php if($enrollment->{$requirement->field_name}): ?>
                    <div class="mt-2">
                        <small class="text-success">
                            <i class="bi bi-check-circle me-1"></i>
                            Current file: <?php echo e(basename($enrollment->{$requirement->field_name})); ?>

                        </small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php elseif($requirement->field_type === 'text' && !in_array($requirement->field_name, ['firstname', 'lastname', 'middlename', 'email', 'contact_number', 'address'])): ?>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="<?php echo e($requirement->field_name); ?>" class="form-label">
                        <?php echo e($requirement->field_label); ?>

                        <?php if($requirement->is_required || in_array($requirement->field_name, $rejectedFields)): ?>
                            <span class="text-danger">*</span>
                        <?php endif; ?>
                        <?php if(in_array($requirement->field_name, $rejectedFields)): ?>
                            <span class="badge bg-danger ms-1">Needs Correction</span>
                        <?php endif; ?>
                    </label>
                    <input type="text" 
                           id="<?php echo e($requirement->field_name); ?>" 
                           name="<?php echo e($requirement->field_name); ?>" 
                           class="form-control <?php echo e(in_array($requirement->field_name, $rejectedFields) ? 'border-danger' : ''); ?>" 
                           value="<?php echo e($enrollment->{$requirement->field_name} ?? ''); ?>"
                           <?php echo e($requirement->is_required ? 'required' : ''); ?>>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    
    <div class="alert alert-warning mt-4">
        <h6><i class="bi bi-exclamation-triangle me-2"></i>Important Notes:</h6>
        <ul class="mb-0">
            <li>Please ensure all information is accurate and complete</li>
            <li>Upload clear, readable copies of all required documents</li>
            <li>Your registration will be reviewed again by administrators</li>
            <li>You will be notified of the review result via email</li>
        </ul>
    </div>
</form>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\student\components\edit-registration-form.blade.php ENDPATH**/ ?>