

<?php $__env->startSection('content'); ?>
<div class="col-lg-6">
    <div class="auth-left">
        <div class="auth-left-content">
            <a href="<?php echo e(url('/')); ?>" class="auth-brand">
                <i class="fas fa-graduation-cap"></i>
                SmartPrep
            </a>
            <h3 class="auth-subtitle">Welcome back to your learning platform</h3>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 30px;">
                Access your dashboard and continue your educational journey with our comprehensive learning management system.
            </p>
            <ul class="feature-list">
                <li><i class="fas fa-check"></i> Multi-tenant architecture</li>
                <li><i class="fas fa-check"></i> Advanced analytics & reporting</li>
                <li><i class="fas fa-check"></i> Seamless payment integration</li>
                <li><i class="fas fa-check"></i> Professional customization</li>
            </ul>
        </div>
    </div>
</div>
<div class="col-lg-6">
    <div class="auth-right">
        <div class="auth-form-header">
            <h2>Sign In</h2>
            <p>Enter your credentials to access your account</p>
        </div>

        <?php
            $fields = \App\Models\AuthFormField::forForm('login')->orderBy('sort_order')->get();
            $loginIdentifier = \App\Models\AdminSetting::where('setting_key','login_identifier')->value('setting_value') ?? 'email';
            if ($fields->isEmpty()) {
                // sensible defaults if none configured
                $fields = collect([
                    (object)['field_key' => $loginIdentifier, 'label' => ucfirst($loginIdentifier), 'type' => $loginIdentifier==='email'?'email':'text', 'is_required'=>true, 'is_enabled'=>true],
                    (object)['field_key' => 'password', 'label' => 'Password', 'type' => 'password', 'is_required'=>true, 'is_enabled'=>true],
                ]);
            }
        ?>

        <form method="POST" action="<?php echo e(route('login')); ?>">
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
unset($__errorArgs, $__bag); ?>" name="<?php echo e($f->field_key); ?>" 
                        value="<?php echo e(old($f->field_key) ?? (($f->field_key == 'email' || $f->field_key == 'username') && isset($autoEmail) ? $autoEmail : '')); ?>" 
                        <?php echo e($f->is_required ? 'required' : ''); ?> autocomplete="<?php echo e($f->field_key); ?>" autofocus>
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
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="remember"><?php echo e(__('Keep me signed in')); ?></label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt me-2"></i>Sign In
            </button>
        </form>

        <div class="auth-links">
            <p class="text-muted mb-3">Don't have an account? <a href="<?php echo e(route('register')); ?>">Create one here</a></p>
            <?php if(Route::has('password.request')): ?>
                <a href="<?php echo e(route('password.request')); ?>">Forgot your password?</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if(isset($autoEmail) && isset($autoPassword)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Find the email/username field
            var emailField = document.querySelector('input[name="email"]') || 
                            document.querySelector('input[name="username"]');
            var passwordField = document.querySelector('input[name="password"]');
            
            if (emailField && passwordField) {
                emailField.value = '<?php echo e($autoEmail); ?>';
                passwordField.value = '<?php echo e($autoPassword); ?>';
                
                // Auto submit after a short delay
                setTimeout(function() {
                    document.querySelector('form').submit();
                }, 500);
            }
        });
    </script>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\auth\login.blade.php ENDPATH**/ ?>