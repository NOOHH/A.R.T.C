
<?php $__env->startSection('content'); ?>
<div class="container py-5">
  <h2 class="mb-4">Professor Dashboard Preview</h2>
  <p class="text-muted">Tenant preview of professor dashboard (sample data).</p>
  <ul class="list-group">
    <?php $__empty_1 = true; $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <?php echo e($c->course_name ?? 'Course'); ?>

        <span class="badge bg-secondary">Preview</span>
      </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <li class="list-group-item">No courses yet.</li>
    <?php endif; ?>
  </ul>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/tenant/preview/professor-dashboard.blade.php ENDPATH**/ ?>