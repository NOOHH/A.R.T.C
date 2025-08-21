<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Reviews</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style> body { padding: 24px } </style>
</head>
<body>
<div class="container">
    <h1 class="mb-3">Reviews</h1>
    <div class="list-group">
        <?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="list-group-item">
                <div class="d-flex justify-content-between">
                    <strong><?php echo e($r->author_name); ?></strong>
                    <span class="badge bg-primary"><?php echo e($r->rating); ?>/5</span>
                </div>
                <div class="text-muted"><?php echo e($r->comment); ?></div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-muted">No reviews yet.</div>
        <?php endif; ?>
    </div>
    </div>
</body>
</html>



<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\tenant\reviews\index.blade.php ENDPATH**/ ?>