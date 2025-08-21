<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/homepage/login.css')); ?>">
    <style>
        <?php echo \App\Helpers\SettingsHelper::getLoginStyles(); ?>

    </style>
    <style>
        <?php echo \App\Helpers\SettingsHelper::getButtonStyles(); ?>

    </style>
</head>
<body class="login-page">
    <?php
        $settings = \App\Helpers\SettingsHelper::getSettings();
        $login = $settings['login'] ?? [];
        $footer = $settings['footer'] ?? [];
    ?>
    <div class="left">
        <div class="review-text">
            Reset Your Password.<br>Secure Your Account.<br>Continue Learning.
        </div>
        <div class="copyright">
            <?php echo $footer['text'] ?? '© Copyright Ascendo Review and Training Center.<br>All Rights Reserved.'; ?>

        </div>
    </div>
    <div class="right">
        <div class="logo-row">
            <img src="<?php echo e(\App\Helpers\SettingsHelper::getLogoUrl()); ?>" alt="Logo">
            <?php
                $navbarSettings = $settings['navbar'] ?? [];
            ?>
            <a href="<?php echo e(url('/')); ?>" class="brand-text"><?php echo e($navbarSettings['brand_name'] ?? 'Ascendo Review and Training Center'); ?></a>
        </div>
        <h2>Reset your password.</h2>

        
        <?php if(session('success')): ?>
            <div class="alert alert-success">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        
        <?php if(session('status')): ?>
            <div class="alert alert-success">
                <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>

        
        <?php if($errors->any()): ?>
            <div class="alert alert-error">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div><?php echo e($error); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form class="login-form" method="POST" action="<?php echo e(route('password.email')); ?>">
            <?php echo csrf_field(); ?>
            
            <label for="email">Enter your email address</label>
            <input type="email" id="email" name="email" placeholder="name@example.com" value="<?php echo e(old('email')); ?>" required>
            <small style="color: #666; font-size: 0.9em; margin-bottom: 16px; display: block;">
                We'll send you a password reset link if this email is registered in our system.
            </small>
            
            <button type="submit">SEND RESET LINK</button>
            
            <div style="margin-top: 20px; text-align: center;">
                <a href="<?php echo e(route('login')); ?>" class="forgot">← Back to Login</a>
            </div>
            
            <div style="margin-top: 8px; font-size: 1em; text-align: center;">
                Don't have an account? <a href="<?php echo e(route('signup')); ?>" class="register-link">Register here.</a>
            </div>
        </form>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\Login\password-reset.blade.php ENDPATH**/ ?>