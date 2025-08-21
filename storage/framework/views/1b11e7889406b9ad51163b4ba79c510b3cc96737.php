

<?php $__env->startSection('content'); ?>
<div class="col-lg-6">
    <div class="auth-left">
        <div class="auth-left-content">
            <a href="<?php echo e(url('/')); ?>" class="auth-brand">
                <i class="fas fa-graduation-cap"></i>
                SmartPrep
            </a>
            <h3 class="auth-subtitle">Start your educational journey today</h3>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 30px;">
                Join thousands of educators and institutions who trust SmartPrep to deliver exceptional learning experiences.
            </p>
            <ul class="feature-list">
                <li><i class="fas fa-rocket"></i> Quick setup & deployment</li>
                <li><i class="fas fa-palette"></i> Full customization control</li>
                <li><i class="fas fa-users-cog"></i> Advanced user management</li>
                <li><i class="fas fa-shield-alt"></i> Enterprise-grade security</li>
            </ul>
        </div>
    </div>
</div>
<div class="col-lg-6">
    <div class="auth-right">
        <div class="auth-form-header">
            <h2>Create Account</h2>
            <p>Get started with your professional learning platform</p>
        </div>

        <?php
            $fields = \App\Models\AuthFormField::forForm('register')->orderBy('sort_order')->get();
            if ($fields->isEmpty()) {
                $fields = collect([
                    (object)['field_key' => 'name', 'label' => 'Full Name', 'type' => 'text', 'is_required'=>true, 'is_enabled'=>true],
                    (object)['field_key' => 'email', 'label' => 'Email Address', 'type' => 'email', 'is_required'=>true, 'is_enabled'=>true],
                    (object)['field_key' => 'password', 'label' => 'Password', 'type' => 'password', 'is_required'=>true, 'is_enabled'=>true],
                    (object)['field_key' => 'password_confirmation', 'label' => 'Confirm Password', 'type' => 'password', 'is_required'=>true, 'is_enabled'=>true],
                ]);
            }
        ?>

    <form method="POST" action="<?php echo e(route('smartprep.register.submit')); ?>">
            <?php echo csrf_field(); ?>

            <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(!$f->is_enabled): ?> <?php continue; ?> <?php endif; ?>
                <div class="form-group">
                    <label class="form-label"><?php echo e($f->label); ?></label>
                    <input id="<?php echo e($f->field_key); ?>" type="<?php echo e($f->type); ?>" class="form-control <?php $__errorArgs = [$f->field_key];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="<?php echo e($f->field_key); ?>" value="<?php echo e(old($f->field_key)); ?>" <?php echo e($f->is_required ? 'required' : ''); ?> autocomplete="<?php echo e($f->field_key); ?>">
                    <?php $__errorArgs = [$f->field_key];
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
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                    <label class="form-check-label" for="terms">
                        I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" style="color: var(--primary-color); text-decoration: none;">Terms of Service</a> and <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal" style="color: var(--primary-color); text-decoration: none;">Privacy Policy</a>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>Create Account
            </button>
        </form>

        <div class="auth-links">
            <p class="text-muted mb-0">Already have an account? <a href="<?php echo e(route('smartprep.login')); ?>">Sign in here</a></p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\smartprep\auth\register.blade.php ENDPATH**/ ?>