
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/homepage/login.css')); ?>">
    <style>
        <?php echo \App\Helpers\SettingsHelper::getLoginStyles(); ?>

    </style>
    <style>
        <?php echo \App\Helpers\SettingsHelper::getButtonStyles(); ?>

    </style>
    <style>
        /* Enhanced Password Toggle Design for Login Page */
        .login-form .input-row {
            position: relative;
            margin-bottom: 20px;
        }
        
        .login-form .input-row input {
            margin-bottom: 0;
            padding-right: 50px;
        }
        
        .login-form .input-row .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            background: rgba(139, 69, 19, 0.1);
            border: 1px solid rgba(139, 69, 19, 0.2);
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.3s ease;
            color: #8B4513;
            backdrop-filter: blur(5px);
        }
        
        .login-form .input-row .toggle-password:hover {
            background: rgba(139, 69, 19, 0.2);
            border-color: rgba(139, 69, 19, 0.4);
            transform: translateY(-50%) scale(1.1);
            box-shadow: 0 2px 8px rgba(139, 69, 19, 0.3);
        }
        
        .login-form .input-row .toggle-password:active {
            transform: translateY(-50%) scale(0.95);
        }
        
        .login-form .input-row .toggle-password.showing {
            background: rgba(34, 139, 34, 0.15);
            border-color: rgba(34, 139, 34, 0.3);
            color: #228B22;
        }
        
        .login-form .input-row .toggle-password.showing:hover {
            background: rgba(34, 139, 34, 0.25);
            border-color: rgba(34, 139, 34, 0.5);
        }
        

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
            Review Smarter.<br>Learn Better.<br>Succeed Faster.
        </div>

        <div class="login-illustration-container">
            <img src="<?php echo e(asset('images/Login-image.png')); ?>" alt="Login Illustration" class="login-illustration">
            <div class="floating-icon-1">📚</div>
            <div class="floating-icon-2">▶️</div>
        </div>

        <div class="copyright">
            © Copyright Ascendo Review and Training Center.<br>All Rights Reserved.
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
        <h2>Log in to your account.</h2>

        
        <?php if(session('success')): ?>
            <div class="alert alert-success">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        
        <?php if($errors->any()): ?>
            <div class="alert alert-error">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div><?php echo e($error); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form class="login-form" method="POST" action="<?php echo e(route('login.submit')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="from_enrollment" value="<?php echo e(request()->query('from_enrollment', 'false')); ?>">
            
            <label for="email">Enter your email address</label>
            <input type="email" id="email" name="email" placeholder="name@example.com" value="<?php echo e(old('email')); ?>" required>

            <label for="password">Enter your password</label>
            <div class="input-row">
                <input type="password" id="password" name="password" placeholder="at least 8 characters" required>
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
            </div>
            
            <a href="<?php echo e(route('password.request')); ?>" class="forgot">Forgot your password? Click here.</a>
            <button type="submit">LOG IN</button>
            <div style="margin-top: 16px; font-size: 0.95em; text-align: center;">Don't have an account? <a href="<?php echo e(route('signup')); ?>" class="register-link">Register here.</a></div>
        </form>
    </div>
    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const toggleBtn = pwd.nextElementSibling;
            
            if (pwd.type === 'password') {
                pwd.type = 'text';
                toggleBtn.classList.add('showing');
                toggleBtn.innerHTML = '👁️‍🗨️';
            } else {
                pwd.type = 'password';
                toggleBtn.classList.remove('showing');
                toggleBtn.innerHTML = '👁️';
            }
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\Login\login.blade.php ENDPATH**/ ?>