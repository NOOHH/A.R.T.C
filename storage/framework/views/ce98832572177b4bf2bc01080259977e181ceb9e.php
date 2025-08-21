<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Tenant Modules</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style> body { padding: 24px } </style>
</head>
<body>
<div class="container">
    <h1 class="mb-3">Modules</h1>
    <?php if(session('status')): ?>
        <div class="alert alert-success"><?php echo e(session('status')); ?></div>
    <?php endif; ?>
    <form class="row g-2 mb-4" method="post" action="">
        <?php echo csrf_field(); ?>
        <div class="col-md-3">
            <select name="course_id" class="form-select" required>
                <option value="">Select Course</option>
                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>"><?php echo e($c->title); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-3"><input class="form-control" name="title" placeholder="Module Title" required></div>
        <div class="col-md-4"><input class="form-control" name="content" placeholder="Content"></div>
        <div class="col-md-2 d-grid"><button class="btn btn-primary">Create</button></div>
    </form>

    <table class="table table-striped">
        <thead><tr><th>Course</th><th>Title</th><th>Created</th></tr></thead>
        <tbody>
            <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr><td><?php echo e($m->course_title); ?></td><td><?php echo e($m->title); ?></td><td><?php echo e($m->created_at); ?></td></tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
</body>
</html>



<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\tenant\admin\modules\index.blade.php ENDPATH**/ ?>