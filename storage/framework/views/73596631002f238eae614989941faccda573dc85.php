

<?php $__env->startSection('title', 'Debug Navbar Test'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-5 pt-5">
    <h1>Debug Navbar Test</h1>
    
    <div class="card">
        <div class="card-body">
            <h3>Navbar Data Debug:</h3>
            <pre><?php echo e(print_r($navbar ?? 'NO NAVBAR VARIABLE', true)); ?></pre>
            
            <h3>Settings Data Debug:</h3>
            <pre><?php echo e(print_r($settings ?? 'NO SETTINGS VARIABLE', true)); ?></pre>
            
            <h3>All View Data:</h3>
            <pre><?php echo e(print_r(get_defined_vars(), true)); ?></pre>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\debug-navbar.blade.php ENDPATH**/ ?>