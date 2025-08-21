<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Settings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/css/admin/artc-theme.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style> body { padding: 24px } </style>
</head>
<body>
<div class="container">
    <h1 class="mb-4">Settings</h1>
    <?php if(session('status')): ?>
        <div class="alert alert-success"><?php echo e(session('status')); ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo e(route('admin.settings.save')); ?>">
        <?php echo csrf_field(); ?>
        <div class="mb-3">
            <label class="form-label">Entity Labels (Customizable Naming)</label>
            <div class="row g-2">
                <div class="col-md-3">
                    <input class="form-control" name="settings[labels][super_admin]" placeholder="Super Admin label" value="<?php echo e(optional($settings->firstWhere('key','super_admin'))->value); ?>">
                </div>
                <div class="col-md-3">
                    <input class="form-control" name="settings[labels][admin]" placeholder="Admin label" value="<?php echo e(optional($settings->firstWhere('key','admin'))->value); ?>">
                </div>
                <div class="col-md-3">
                    <input class="form-control" name="settings[labels][professor]" placeholder="Professor label" value="<?php echo e(optional($settings->firstWhere('key','professor'))->value); ?>">
                </div>
                <div class="col-md-3">
                    <input class="form-control" name="settings[labels][student]" placeholder="Student label" value="<?php echo e(optional($settings->firstWhere('key','student'))->value); ?>">
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Login Form Fields (Editable)</label>
            <div class="row g-2">
                <div class="col-md-3">
                    <input class="form-control" name="settings[login][email_label]" placeholder="Email label" value="<?php echo e(optional($settings->firstWhere('key','email_label'))->value); ?>">
                </div>
                <div class="col-md-3">
                    <input class="form-control" name="settings[login][password_label]" placeholder="Password label" value="<?php echo e(optional($settings->firstWhere('key','password_label'))->value); ?>">
                </div>
                <div class="col-md-3">
                    <input class="form-control" name="settings[login][submit_label]" placeholder="Submit label" value="<?php echo e(optional($settings->firstWhere('key','submit_label'))->value); ?>">
                </div>
            </div>
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
</div>
</body>
</html>


<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\smartprep\admin\settings\index.blade.php ENDPATH**/ ?>