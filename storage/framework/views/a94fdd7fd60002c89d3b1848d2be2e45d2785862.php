
<?php $__env->startSection('hide_footer', true); ?>
<?php $__env->startSection('title', $program->program_name); ?>
<?php $__env->startSection('body_class', 'program-page'); ?>
<?php $__env->startPush('styles'); ?>
<?php echo App\Helpers\UIHelper::getNavbarStyles(); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.program-hero-section {
    background: #f8f9fa;
    border-radius: 20px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.07);
    padding: 32px 24px;
    margin-bottom: 32px;
}
.program-breadcrumb {
    font-size: 0.97rem;
    margin-bottom: 18px;
    color: #667eea;
}
.program-title {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 8px;
}
.program-subtitle {
    font-size: 1.1rem;
    color: #555;
    margin-bottom: 18px;
}
.program-stats {
    display: flex;
    gap: 32px;
    margin-bottom: 18px;
}
.stat-item {
    background: #eef2fa;
    border-radius: 10px;
    padding: 12px 24px;
    text-align: center;
    min-width: 110px;
}
.stat-number {
    font-size: 1.4rem;
    font-weight: 700;
    color: #667eea;
}
.stat-label {
    font-size: 0.95rem;
    color: #555;
}
.program-image {
    width: 100%;
    max-width: 320px;
    border-radius: 16px;
    object-fit: cover;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}
@media (max-width: 992px) {
    .program-hero-section { flex-direction: column; align-items: stretch; }
    .program-image { margin: 0 auto 24px auto; }
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
<div class="container" style="max-width: 1100px; margin: 0 auto;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="program-breadcrumb mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('programs.index')); ?>">Programs</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo e($program->program_name); ?></li>
        </ol>
    </nav>

    <!-- Hero Section -->
    <div class="row program-hero-section align-items-center g-4">
        <div class="col-lg-8">
            <div class="program-title"><?php echo e($program->program_name); ?></div>
            <?php if($program->program_description): ?>
                <div class="program-subtitle"><?php echo e($program->program_description); ?></div>
            <?php endif; ?>
            <div class="program-stats">
                <div class="stat-item">
                    <div class="stat-number"><?php echo e($program->modules->count()); ?></div>
                    <div class="stat-label">Modules</div>
                </div>
                <!-- Add more stats here if available -->
            </div>
        </div>
        <div class="col-lg-4 text-center">
            <img src="<?php echo e(asset('images/Home page image.png')); ?>" alt="Program Image" class="program-image">
        </div>
    </div>

    <!-- Tabs Section -->
    <ul class="nav nav-tabs mb-4" id="programTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">Overview</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="curriculum-tab" data-bs-toggle="tab" data-bs-target="#curriculum" type="button" role="tab" aria-controls="curriculum" aria-selected="false">Curriculum</button>
        </li>
    </ul>
    <div class="tab-content" id="programTabContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
            <div class="mb-4">
                <?php if($program->program_description): ?>
                    <div class="description-box">
                        <p class="description-text"><?php echo e($program->program_description); ?></p>
                    </div>
                <?php else: ?>
                    <div class="text-muted">No overview available for this program.</div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Curriculum Tab -->
        <div class="tab-pane fade" id="curriculum" role="tabpanel" aria-labelledby="curriculum-tab">
            <?php if($program->modules->count() > 0): ?>
                <div class="accordion" id="modulesAccordion">
                    <?php $__currentLoopData = $program->modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo e($index); ?>">
                                <button class="accordion-button <?php if($index !== 0): ?> collapsed <?php endif; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo e($index); ?>" aria-expanded="<?php echo e($index === 0 ? 'true' : 'false'); ?>" aria-controls="collapse<?php echo e($index); ?>">
                                    <?php echo e($index + 1); ?>. <?php echo e($module->module_name); ?>

                                </button>
                            </h2>
                            <div id="collapse<?php echo e($index); ?>" class="accordion-collapse collapse <?php if($index === 0): ?> show <?php endif; ?>" aria-labelledby="heading<?php echo e($index); ?>" data-bs-parent="#modulesAccordion">
                                <div class="accordion-body">
                                    <?php if($module->module_description): ?>
                                        <div class="mb-2"><?php echo e($module->module_description); ?></div>
                                    <?php endif; ?>
                                    <div class="text-muted small">
                                        Type: <?php echo e(ucfirst($module->content_type_display ?? $module->content_type)); ?>

                                        <?php if($module->module_order): ?>
                                            | Order: <?php echo e($module->module_order); ?>

                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-muted">No modules available for this program.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\programs\show.blade.php ENDPATH**/ ?>