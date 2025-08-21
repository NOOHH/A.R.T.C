

<?php $__env->startSection('content'); ?>
<div class="col-lg-6">
    <div class="auth-left">
        <div class="auth-left-content">
            <a href="<?php echo e(url('/')); ?>" class="auth-brand">
                <i class="fas fa-graduation-cap"></i>
                SmartPrep
            </a>
            <h3 class="auth-subtitle">Set your new password</h3>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 30px;">
                You're almost done! Create a strong new password to secure your account and regain access to your learning platform.
            </p>
            <ul class="feature-list">
                <li><i class="fas fa-key"></i> Strong password protection</li>
                <li><i class="fas fa-user-shield"></i> Account security</li>
                <li><i class="fas fa-check-circle"></i> Instant access restoration</li>
                <li><i class="fas fa-graduation-cap"></i> Continue learning</li>
            </ul>
        </div>
    </div>
</div>
<div class="col-lg-6">
    <div class="auth-right">
        <div class="auth-form-header">
            <h2>Reset Password</h2>
            <p>Enter your new password below</p>
        </div>

        <form method="POST" action="<?php echo e(route('password.update')); ?>">
            <?php echo csrf_field(); ?>

            <input type="hidden" name="token" value="<?php echo e($token); ?>">

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input id="email" type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e($email ?? old('email')); ?>" required autocomplete="email" autofocus>
                <?php $__errorArgs = ['email'];
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

            <div class="form-group">
                <label class="form-label">New Password</label>
                <input id="password" type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password" required autocomplete="new-password">
                <?php $__errorArgs = ['password'];
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

            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-key me-2"></i>Reset Password
            </button>
        </form>

        <div class="auth-links">
            <p class="text-muted mb-0">Remember your password? <a href="<?php echo e(route('login')); ?>">Sign in here</a></p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\auth\passwords\reset.blade.php ENDPATH**/ ?>