

<?php $__env->startSection('title', $profile['name'] . ' - Professor Profile'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Profile Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <img src="<?php echo e($profile['avatar']); ?>" 
                                 alt="<?php echo e($profile['name']); ?>" 
                                 class="rounded-circle mb-3" 
                                 width="120" height="120">
                        </div>
                        <div class="col-md-8">
                            <h2 class="mb-2"><?php echo e($profile['name']); ?></h2>
                            <div class="mb-2">
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-chalkboard-teacher me-2"></i>
                                    <?php echo e($profile['role']); ?>

                                </span>
                                <span class="badge bg-success fs-6 ms-2">
                                    <i class="fas fa-circle me-1"></i><?php echo e($profile['status']); ?>

                                </span>
                            </div>
                            <p class="text-muted mb-1">
                                <i class="fas fa-envelope me-2"></i><?php echo e($profile['email']); ?>

                            </p>
                            <p class="text-muted mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>Joined <?php echo e($profile['created_at']->format('M d, Y')); ?>

                            </p>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Professor Information -->
    <?php if(isset($profile['programs']) && count($profile['programs']) > 0): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-graduation-cap me-2"></i>Teaching Programs
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php $__currentLoopData = $profile['programs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <h6 class="card-title text-success">
                                                <i class="fas fa-book me-2"></i><?php echo e($program['program_name']); ?>

                                            </h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <?php echo e($program['program_description'] ?: 'No description available'); ?>

                                                </small>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-info"><?php echo e($program['modules_count']); ?> Modules</span>
                                                <span class="badge bg-warning"><?php echo e($program['students_count']); ?> Students</span>
                                            </div>
                                            <div class="mt-2">
                                                <a href="<?php echo e(route('profile.program', $program['program_id'])); ?>" 
                                                   class="btn btn-sm btn-outline-success">
                                                    View Program
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Batches Information -->
    <?php if(isset($profile['batches']) && count($profile['batches']) > 0): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>Teaching Batches
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php $__currentLoopData = $profile['batches']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-primary">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary">
                                                <i class="fas fa-layer-group me-2"></i><?php echo e($batch['batch_name']); ?>

                                            </h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <i class="fas fa-graduation-cap me-1"></i><?php echo e($batch['program_name']); ?>

                                                </small>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-primary"><?php echo e($batch['students_count']); ?> Students</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Contact Information -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-address-card me-2"></i>Contact Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><i class="fas fa-envelope me-2 text-muted"></i><?php echo e($profile['email']); ?></p>
                            <p><i class="fas fa-user me-2 text-muted"></i>Professor ID: <?php echo e($profile['professor_id']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><i class="fas fa-calendar me-2 text-muted"></i>Member since <?php echo e($profile['created_at']->format('F Y')); ?></p>
                            <p><i class="fas fa-chalkboard-teacher me-2 text-muted"></i>Status: <?php echo e($profile['status']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\profiles\professor.blade.php ENDPATH**/ ?>