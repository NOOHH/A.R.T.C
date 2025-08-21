

<?php $__env->startSection('content'); ?>
<div class="col-lg-6">
    <div class="auth-left">
        <div class="auth-left-content">
            <a href="<?php echo e(url('/')); ?>" class="auth-brand">
                <i class="fas fa-graduation-cap"></i>
                SmartPrep
            </a>
            <h3 class="auth-subtitle">Reset your password</h3>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 30px;">
                Don't worry! It happens to the best of us. Enter your email address and we'll send you a link to reset your password.
            </p>
            <ul class="feature-list">
                <li><i class="fas fa-shield-alt"></i> Secure password recovery</li>
                <li><i class="fas fa-clock"></i> Quick and easy process</li>
                <li><i class="fas fa-envelope"></i> Email verification</li>
                <li><i class="fas fa-lock"></i> Enhanced security</li>
            </ul>
        </div>
    </div>
</div>
<div class="col-lg-6">
    <div class="auth-right">
        <div class="auth-form-header">
            <h2>Reset Password</h2>
            <p>Enter your email address to receive a password reset link</p>
        </div>

        <?php if(session('status')): ?>
            <div class="alert alert-success" role="alert" style="border-radius: 12px; border: none; background: rgba(5, 150, 105, 0.1); color: #059669; margin-bottom: 30px;">
                <i class="fas fa-check-circle me-2"></i><?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>

    <form method="POST" action="<?php echo e(route('smartprep.password.email')); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input id="email" type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e(old('email')); ?>" required autocomplete="email" autofocus>
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

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Send Password Reset Link
            </button>
        </form>

        <div class="auth-links">
            <p class="text-muted mb-0">Remember your password? <a href="<?php echo e(route('smartprep.login')); ?>">Sign in here</a></p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\smartprep\auth\passwords\email.blade.php ENDPATH**/ ?>