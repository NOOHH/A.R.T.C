

<?php $__env->startSection('content'); ?>
<div class="col-lg-6">
    <div class="auth-left">
        <div class="auth-left-content">
            <a href="<?php echo e(url('/')); ?>" class="auth-brand">
                <i class="fas fa-graduation-cap"></i>
                SmartPrep
            </a>
            <h3 class="auth-subtitle">Verify your email address</h3>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 30px;">
                We've sent a verification link to your email address. Click the link to activate your account and start your learning journey.
            </p>
            <ul class="feature-list">
                <li><i class="fas fa-envelope-open"></i> Check your inbox</li>
                <li><i class="fas fa-mouse-pointer"></i> Click verification link</li>
                <li><i class="fas fa-check-circle"></i> Activate your account</li>
                <li><i class="fas fa-rocket"></i> Start learning</li>
            </ul>
        </div>
    </div>
</div>
<div class="col-lg-6">
    <div class="auth-right">
        <div class="auth-form-header">
            <h2>Email Verification</h2>
            <p>We've sent you a verification email</p>
        </div>

        <?php if(session('resent')): ?>
            <div class="alert alert-success" role="alert" style="border-radius: 12px; border: none; background: rgba(5, 150, 105, 0.1); color: #059669; margin-bottom: 30px;">
                <i class="fas fa-check-circle me-2"></i>A fresh verification link has been sent to your email address.
            </div>
        <?php endif; ?>

        <div style="background: rgba(37, 99, 235, 0.05); border-radius: 12px; padding: 30px; margin-bottom: 30px; text-align: center;">
            <i class="fas fa-envelope" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 20px;"></i>
            <h4 style="color: var(--text-dark); margin-bottom: 15px;">Check Your Email</h4>
            <p style="color: var(--text-light); margin-bottom: 0;">
                Before proceeding, please check your email for a verification link. If you didn't receive the email, you can request a new one below.
            </p>
        </div>

    <form method="POST" action="<?php echo e(route('smartprep.verification.resend')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Resend Verification Email
            </button>
        </form>

        <div class="auth-links">
            <p class="text-muted mb-0">Need help? <a href="<?php echo e(route('smartprep.login')); ?>">Back to login</a></p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\smartprep\auth\verify.blade.php ENDPATH**/ ?>