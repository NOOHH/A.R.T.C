

<?php $__env->startSection('title', 'Director Profile'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Director Profile</h1>
                <a href="<?php echo e(route('director.dashboard')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
                        </div>
                        <div class="card-body">
                            <?php if(session('success')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo e(session('success')); ?>

                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="<?php echo e(route('director.profile.update')); ?>">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="directors_name">Full Name</label>
                                            <input type="text" 
                                                   class="form-control <?php $__errorArgs = ['directors_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="directors_name" 
                                                   name="directors_name" 
                                                   value="<?php echo e(old('directors_name', $director->directors_name ?? '')); ?>" 
                                                   required>
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
                                        <div class="form-group">
                                            <label for="directors_email">Email Address</label>
                                            <input type="email" 
                                                   class="form-control <?php $__errorArgs = ['directors_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="directors_email" 
                                                   name="directors_email" 
                                                   value="<?php echo e(old('directors_email', $director->directors_email ?? '')); ?>" 
                                                   required>
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
                                        <div class="form-group">
                                            <label for="directors_phone">Phone Number</label>
                                            <input type="tel" 
                                                   class="form-control <?php $__errorArgs = ['directors_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="directors_phone" 
                                                   name="directors_phone" 
                                                   value="<?php echo e(old('directors_phone', $director->directors_phone ?? '')); ?>">
                                            <?php $__errorArgs = ['directors_phone'];
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
                                        <div class="form-group">
                                            <label>Director ID</label>
                                            <input type="text" 
                                                   class="form-control bg-light" 
                                                   value="<?php echo e($director->directors_id ?? 'N/A'); ?>" 
                                                   readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Member Since</label>
                                            <input type="text" 
                                                   class="form-control bg-light" 
                                                   value="<?php echo e($director->created_at ? date('M d, Y', strtotime($director->created_at)) : 'N/A'); ?>" 
                                                   readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Last Updated</label>
                                            <input type="text" 
                                                   class="form-control bg-light" 
                                                   value="<?php echo e($director->updated_at ? date('M d, Y', strtotime($director->updated_at)) : 'N/A'); ?>" 
                                                   readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Profile Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <div class="avatar-lg mb-3">
                                    <div class="avatar-title rounded-circle bg-primary text-white" style="width: 80px; height: 80px; line-height: 80px; font-size: 2rem; margin: 0 auto;">
                                        <?php echo e(strtoupper(substr($director->directors_name ?? 'D', 0, 1))); ?>

                                    </div>
                                </div>
                                <h5 class="font-weight-bold"><?php echo e($director->directors_name ?? 'Director'); ?></h5>
                                <p class="text-muted"><?php echo e($director->directors_email ?? 'No email'); ?></p>
                                <p class="text-muted"><?php echo e($director->directors_phone ?? 'No phone'); ?></p>
                            </div>
                            
                            <hr>
                            
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <span class="font-weight-bold text-primary">Role</span>
                                        <span class="text-muted">Director</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <span class="font-weight-bold text-primary">Status</span>
                                        <span class="badge badge-success">Active</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\director\profile.blade.php ENDPATH**/ ?>