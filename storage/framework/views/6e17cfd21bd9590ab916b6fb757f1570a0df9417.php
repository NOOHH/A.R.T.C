<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Courses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style> body { padding: 24px } </style>
</head>
<body>
<div class="container">
    <h1 class="mb-3">Courses</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($course->title); ?></td>
                    <td><?php echo e($course->created_at); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="2" class="text-muted">No courses yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>



<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\tenant\courses\index.blade.php ENDPATH**/ ?>