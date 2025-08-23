

<?php $__env->startSection('title', 'View Announcement'); ?>

<?php
    // Detect tenant mode for preview
    $tenantSlug = request()->route('tenant') ?? null;
    $urlParams = '';
    
    if ($tenantSlug) {
        // Build query parameters for tenant preview
        $queryParams = request()->query();
        if (!empty($queryParams)) {
            $urlParams = '?' . http_build_query($queryParams);
        }
    }
?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
.announcement-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.announcement-type-badge {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
}

.announcement-type-general { background-color: #3498db; }
.announcement-type-urgent { background-color: #e74c3c; }
.announcement-type-event { background-color: #f39c12; }
.announcement-type-system { background-color: #9b59b6; }

.content-card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-radius: 12px;
}

.stats-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.target-info {
    background: #e9ecef;
    border-radius: 8px;
    padding: 12px;
    font-size: 0.9rem;
}

.meta-info {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
}

.status-active { background-color: #28a745; }
.status-inactive { background-color: #dc3545; }
.status-published { background-color: #17a2b8; }
.status-draft { background-color: #6c757d; }

.video-container {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
    overflow: hidden;
    border-radius: 8px;
    margin-top: 1rem;
}

.video-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.badge-custom {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    margin: 0.125rem;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-eye me-2"></i>View Announcement
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <?php
                            $indexUrl = $tenantSlug 
                                ? route('tenant.draft.admin.announcements', ['tenant' => $tenantSlug]) . $urlParams
                                : route('admin.announcements.index');
                        ?>
                        <a href="<?php echo e($indexUrl); ?>">Announcements</a>
                    </li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <?php
                $editUrl = $tenantSlug 
                    ? route('tenant.draft.admin.announcements.edit', ['tenant' => $tenantSlug, 'id' => $announcement->announcement_id]) . $urlParams
                    : route('admin.announcements.edit', $announcement->announcement_id);
                    
                $backUrl = $tenantSlug 
                    ? route('tenant.draft.admin.announcements', ['tenant' => $tenantSlug]) . $urlParams
                    : route('admin.announcements.index');
            ?>
            <a href="<?php echo e($editUrl); ?>" 
               class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            <a href="<?php echo e($backUrl); ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <!-- Announcement Header -->
    <div class="announcement-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2"><?php echo e($announcement->title); ?></h2>
                <?php if($announcement->description): ?>
                    <p class="mb-3 opacity-75"><?php echo e($announcement->description); ?></p>
                <?php endif; ?>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge announcement-type-badge announcement-type-<?php echo e($announcement->type); ?>">
                        <?php echo e(ucfirst($announcement->type)); ?>

                    </span>
                    <span class="status-indicator <?php echo e($announcement->is_active ? 'status-active' : 'status-inactive'); ?>"></span>
                    <span><?php echo e($announcement->is_active ? 'Active' : 'Inactive'); ?></span>
                    <span class="status-indicator <?php echo e($announcement->is_published ? 'status-published' : 'status-draft'); ?>"></span>
                    <span><?php echo e($announcement->is_published ? 'Published' : 'Draft'); ?></span>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="text-white-50">
                    <small>Created: <?php echo e($announcement->created_at->format('M d, Y g:i A')); ?></small><br>
                    <?php if($announcement->publish_date): ?>
                        <small>Published: <?php echo e($announcement->publish_date->format('M d, Y g:i A')); ?></small><br>
                    <?php endif; ?>
                    <?php if($announcement->expire_date): ?>
                        <small>Expires: <?php echo e($announcement->expire_date->format('M d, Y g:i A')); ?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Content Card -->
            <div class="card content-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-file-text me-2"></i>Content
                    </h5>
                </div>
                <div class="card-body">
                    <div class="announcement-content">
                        <?php echo nl2br(e($announcement->content)); ?>

                    </div>
                    
                    <?php if($announcement->video_link): ?>
                        <div class="video-container">
                            <iframe src="<?php echo e($announcement->video_link); ?>" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Target Audience -->
            <div class="card content-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-people me-2"></i>Target Audience
                    </h5>
                </div>
                <div class="card-body">
                    <div class="target-info">
                        <?php if($announcement->target_scope === 'all'): ?>
                            <h6><i class="bi bi-globe me-2"></i>All Users</h6>
                            <p class="mb-0">This announcement is visible to all users in the system.</p>
                        <?php else: ?>
                            <h6><i class="bi bi-funnel me-2"></i>Specific Targeting</h6>
                            
                            <?php
                                // Handle both array (new format) and JSON string (old format)
                                $targetUsers = [];
                                if (is_array($announcement->target_users)) {
                                    $targetUsers = $announcement->target_users;
                                } elseif (is_string($announcement->target_users)) {
                                    $targetUsers = json_decode($announcement->target_users, true) ?: [];
                                }
                                
                                $targetPrograms = [];
                                if (is_array($announcement->target_programs)) {
                                    $targetPrograms = $announcement->target_programs;
                                } elseif (is_string($announcement->target_programs)) {
                                    $targetPrograms = json_decode($announcement->target_programs, true) ?: [];
                                }
                                
                                $targetBatches = [];
                                if (is_array($announcement->target_batches)) {
                                    $targetBatches = $announcement->target_batches;
                                } elseif (is_string($announcement->target_batches)) {
                                    $targetBatches = json_decode($announcement->target_batches, true) ?: [];
                                }
                                
                                $targetPlans = [];
                                if (is_array($announcement->target_plans)) {
                                    $targetPlans = $announcement->target_plans;
                                } elseif (is_string($announcement->target_plans)) {
                                    $targetPlans = json_decode($announcement->target_plans, true) ?: [];
                                }
                            ?>

                            <?php if($targetUsers): ?>
                                <div class="mb-3">
                                    <strong>User Types:</strong><br>
                                    <?php $__currentLoopData = $targetUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge badge-custom bg-info"><?php echo e(ucfirst($user)); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>

                            <?php if($targetPrograms): ?>
                                <div class="mb-3">
                                    <strong>Programs:</strong><br>
                                    <?php $__currentLoopData = $targetPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $programId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $program = \App\Models\Program::find($programId);
                                        ?>
                                        <?php if($program): ?>
                                            <span class="badge badge-custom bg-primary"><?php echo e($program->program_name); ?></span>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>

                            <?php if($targetBatches): ?>
                                <div class="mb-3">
                                    <strong>Batches:</strong><br>
                                    <?php $__currentLoopData = $targetBatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batchId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $batch = \App\Models\StudentBatch::find($batchId);
                                        ?>
                                        <?php if($batch): ?>
                                            <span class="badge badge-custom bg-secondary"><?php echo e($batch->batch_name); ?></span>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>

                            <?php if($targetPlans): ?>
                                <div class="mb-3">
                                    <strong>Enrollment Plans:</strong><br>
                                    <?php $__currentLoopData = $targetPlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge badge-custom bg-warning"><?php echo e(ucfirst($plan)); ?> Program</span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Statistics -->
            <div class="card content-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>Target Audience Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-person-badge me-2"></i>Students</span>
                            <strong><?php echo e(number_format($stats['target_students'])); ?></strong>
                        </div>
                    </div>
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-person-workspace me-2"></i>Professors</span>
                            <strong><?php echo e(number_format($stats['target_professors'])); ?></strong>
                        </div>
                    </div>
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-person-fill me-2"></i>Directors</span>
                            <strong><?php echo e(number_format($stats['target_directors'])); ?></strong>
                        </div>
                    </div>
                    
                    <?php if(!empty($stats['target_programs'])): ?>
                        <div class="stats-card">
                            <div class="mb-2">
                                <strong><i class="bi bi-mortarboard me-2"></i>Target Programs:</strong>
                            </div>
                            <?php $__currentLoopData = $stats['target_programs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge badge-custom bg-primary"><?php echo e($program); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($stats['target_batches'])): ?>
                        <div class="stats-card">
                            <div class="mb-2">
                                <strong><i class="bi bi-collection me-2"></i>Target Batches:</strong>
                            </div>
                            <?php $__currentLoopData = $stats['target_batches']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge badge-custom bg-secondary"><?php echo e($batch); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Meta Information -->
            <div class="card content-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Meta Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="meta-info">
                        <div class="row mb-2">
                            <div class="col-6"><strong>ID:</strong></div>
                            <div class="col-6"><?php echo e($announcement->announcement_id); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Type:</strong></div>
                            <div class="col-6"><?php echo e(ucfirst($announcement->type)); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Status:</strong></div>
                            <div class="col-6">
                                <span class="status-indicator <?php echo e($announcement->is_active ? 'status-active' : 'status-inactive'); ?>"></span>
                                <?php echo e($announcement->is_active ? 'Active' : 'Inactive'); ?>

                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Published:</strong></div>
                            <div class="col-6">
                                <span class="status-indicator <?php echo e($announcement->is_published ? 'status-published' : 'status-draft'); ?>"></span>
                                <?php echo e($announcement->is_published ? 'Yes' : 'No'); ?>

                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Created:</strong></div>
                            <div class="col-6"><?php echo e($announcement->created_at->format('M d, Y')); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Updated:</strong></div>
                            <div class="col-6"><?php echo e($announcement->updated_at->format('M d, Y')); ?></div>
                        </div>
                        <?php if($announcement->publish_date): ?>
                            <div class="row mb-2">
                                <div class="col-6"><strong>Publish Date:</strong></div>
                                <div class="col-6"><?php echo e($announcement->publish_date->format('M d, Y g:i A')); ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if($announcement->expire_date): ?>
                            <div class="row mb-2">
                                <div class="col-6"><strong>Expire Date:</strong></div>
                                <div class="col-6"><?php echo e($announcement->expire_date->format('M d, Y g:i A')); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php $__env->stopPush(); ?> 

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/admin/announcements/show.blade.php ENDPATH**/ ?>