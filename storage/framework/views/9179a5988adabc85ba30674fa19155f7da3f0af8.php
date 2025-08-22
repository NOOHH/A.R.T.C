
<?php $__env->startSection('content'); ?>
<div class="container py-5">
  <h2 class="mb-4">Student Dashboard Preview</h2>
  <p class="text-muted">This is a tenant preview of the student dashboard (read-only sample data).</p>
  <div class="row">
    <?php $__empty_1 = true; $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="col-md-3 mb-3">
        <div class="card h-100"><div class="card-body">
          <h6 class="card-title"><?php echo e($p->program_name ?? 'Program'); ?></h6>
          <p class="small text-muted mb-0">Preview item</p>
        </div></div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <p>No programs yet.</p>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/tenant/preview/student-dashboard.blade.php ENDPATH**/ ?>