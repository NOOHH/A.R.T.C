
<?php $__env->startSection('content'); ?>
<div class="container py-5">
  <h2 class="mb-4">Admin Dashboard Preview</h2>
  <p class="text-muted">Tenant preview of admin dashboard (summary only).</p>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card shadow-sm h-100"><div class="card-body text-center">
        <h5 class="card-title mb-1">Programs</h5>
        <div class="display-6"><?php echo e($stats['programs']); ?></div>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm h-100"><div class="card-body text-center">
        <h5 class="card-title mb-1">Courses</h5>
        <div class="display-6"><?php echo e($stats['courses']); ?></div>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm h-100"><div class="card-body text-center">
        <h5 class="card-title mb-1">Students</h5>
        <div class="display-6"><?php echo e($stats['students']); ?></div>
      </div></div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/tenant/preview/admin-dashboard.blade.php ENDPATH**/ ?>