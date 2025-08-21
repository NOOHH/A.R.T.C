

<?php $__env->startSection('title', 'View Announcement'); ?>

<?php $__env->startPush('styles'); ?>
<style>
.announcement-card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-radius: 12px;
    overflow: hidden;
}

.announcement-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    position: relative;
}

.announcement-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.1);
    pointer-events: none;
}

.announcement-header * {
    position: relative;
    z-index: 1;
}

.type-badge {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.announcement-type-general { background: linear-gradient(45deg, #3498db, #2980b9); }
.announcement-type-urgent { background: linear-gradient(45deg, #e74c3c, #c0392b); }
.announcement-type-event { background: linear-gradient(45deg, #f39c12, #e67e22); }
.announcement-type-system { background: linear-gradient(45deg, #9b59b6, #8e44ad); }

.announcement-content {
    padding: 2rem;
    line-height: 1.8;
    font-size: 1.1rem;
}

.meta-info {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
}

.meta-item:last-child {
    margin-bottom: 0;
}

.meta-item i {
    width: 20px;
    color: #6c757d;
    margin-right: 0.75rem;
}

.status-badge {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-weight: 600;
}

.status-published {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-draft {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-expired {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.video-container {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 aspect ratio */
    height: 0;
    overflow: hidden;
    border-radius: 8px;
    margin: 1.5rem 0;
}

.video-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
}

.target-info {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 8px;
    padding: 1.5rem;
    margin-top: 1.5rem;
}

.target-section {
    margin-bottom: 1rem;
}

.target-section:last-child {
    margin-bottom: 0;
}

.target-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.target-tag {
    background: #fff;
    border: 1px solid #2196F3;
    color: #1976D2;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.875rem;
    font-weight: 500;
}

.action-buttons {
    padding: 1.5rem;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.btn-group-custom {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn-custom {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-edit {
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
}

.btn-edit:hover {
    background: linear-gradient(45deg, #218838, #1ea080);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    color: white;
}

.btn-delete {
    background: linear-gradient(45deg, #dc3545, #c82333);
    color: white;
}

.btn-delete:hover {
    background: linear-gradient(45deg, #c82333, #bd2130);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    color: white;
}

.btn-back {
    background: linear-gradient(45deg, #6c757d, #545b62);
    color: white;
}

.btn-back:hover {
    background: linear-gradient(45deg, #545b62, #495057);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
    color: white;
}

.btn-toggle {
    background: linear-gradient(45deg, #ffc107, #ffb300);
    color: #212529;
}

.btn-toggle:hover {
    background: linear-gradient(45deg, #ffb300, #ffa000);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
    color: #212529;
}

@media (max-width: 768px) {
    .announcement-header {
        padding: 1.5rem;
    }
    
    .announcement-content {
        padding: 1.5rem;
    }
    
    .btn-group-custom {
        flex-direction: column;
    }
    
    .btn-custom {
        justify-content: center;
    }
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
                        <a href="<?php echo e(route('professor.dashboard')); ?>">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?php echo e(route('professor.announcements.index')); ?>">Announcements</a>
                    </li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Main Announcement Card -->
            <div class="card announcement-card">
                <!-- Header -->
                <div class="announcement-header">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h1 class="h2 mb-0"><?php echo e($announcement->title); ?></h1>
                        <span class="badge type-badge announcement-type-<?php echo e($announcement->type); ?>">
                            <?php echo e(ucfirst($announcement->type)); ?>

                        </span>
                    </div>
                    
                    <?php if($announcement->description): ?>
                        <p class="lead mb-0 opacity-90"><?php echo e($announcement->description); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Content -->
                <div class="announcement-content">
                    <?php echo nl2br(e($announcement->content)); ?>

                    
                    <?php if($announcement->video_link): ?>
                        <div class="video-container">
                            <?php
                                $videoId = null;
                                if (strpos($announcement->video_link, 'youtube.com') !== false) {
                                    parse_str(parse_url($announcement->video_link, PHP_URL_QUERY), $query);
                                    $videoId = $query['v'] ?? null;
                                } elseif (strpos($announcement->video_link, 'youtu.be') !== false) {
                                    $videoId = basename(parse_url($announcement->video_link, PHP_URL_PATH));
                                }
                            ?>
                            
                            <?php if($videoId): ?>
                                <iframe src="https://www.youtube.com/embed/<?php echo e($videoId); ?>" 
                                        allowfullscreen></iframe>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-play-circle me-2"></i>
                                    <a href="<?php echo e($announcement->video_link); ?>" target="_blank" class="alert-link">
                                        View Video
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <div class="btn-group-custom">
                        <a href="<?php echo e(route('professor.announcements.edit', $announcement)); ?>" class="btn-custom btn-edit">
                            <i class="bi bi-pencil-square"></i>Edit
                        </a>
                        
                        <form action="<?php echo e(route('professor.announcements.toggle-status', $announcement)); ?>" 
                              method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <button type="submit" class="btn-custom btn-toggle">
                                <i class="bi bi-<?php echo e($announcement->is_published ? 'eye-slash' : 'eye'); ?>"></i>
                                <?php echo e($announcement->is_published ? 'Unpublish' : 'Publish'); ?>

                            </button>
                        </form>
                        
                        <form action="<?php echo e(route('professor.announcements.destroy', $announcement)); ?>" 
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this announcement?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn-custom btn-delete">
                                <i class="bi bi-trash"></i>Delete
                            </button>
                        </form>
                        
                        <a href="<?php echo e(route('professor.announcements.index')); ?>" class="btn-custom btn-back">
                            <i class="bi bi-arrow-left"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Meta Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="meta-info">
                        <div class="meta-item">
                            <i class="bi bi-calendar-plus"></i>
                            <div>
                                <strong>Created:</strong><br>
                                <span class="text-muted"><?php echo e($announcement->created_at->format('M d, Y g:i A')); ?></span>
                            </div>
                        </div>
                        
                        <?php if($announcement->updated_at != $announcement->created_at): ?>
                            <div class="meta-item">
                                <i class="bi bi-calendar-check"></i>
                                <div>
                                    <strong>Last Updated:</strong><br>
                                    <span class="text-muted"><?php echo e($announcement->updated_at->format('M d, Y g:i A')); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="meta-item">
                            <i class="bi bi-<?php echo e($announcement->is_published ? 'check-circle' : 'clock'); ?>"></i>
                            <div>
                                <strong>Status:</strong><br>
                                <?php if($announcement->is_published): ?>
                                    <?php if($announcement->expire_date && $announcement->expire_date->isPast()): ?>
                                        <span class="status-badge status-expired">Expired</span>
                                    <?php else: ?>
                                        <span class="status-badge status-published">Published</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="status-badge status-draft">Draft</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if($announcement->publish_date): ?>
                            <div class="meta-item">
                                <i class="bi bi-calendar-event"></i>
                                <div>
                                    <strong>Publish Date:</strong><br>
                                    <span class="text-muted"><?php echo e($announcement->publish_date->format('M d, Y g:i A')); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($announcement->expire_date): ?>
                            <div class="meta-item">
                                <i class="bi bi-calendar-x"></i>
                                <div>
                                    <strong>Expire Date:</strong><br>
                                    <span class="text-muted"><?php echo e($announcement->expire_date->format('M d, Y g:i A')); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="meta-item">
                            <i class="bi bi-person-badge"></i>
                            <div>
                                <strong>Creator:</strong><br>
                                <span class="text-muted"><?php echo e($announcement->professor->first_name ?? 'Professor'); ?> <?php echo e($announcement->professor->last_name ?? ''); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Target Audience -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-people me-2"></i>Target Audience
                    </h5>
                </div>
                <div class="card-body">
                    <div class="target-info">
                        <?php if($announcement->target_users && count($announcement->target_users) > 0): ?>
                            <div class="target-section">
                                <strong class="text-primary">
                                    <i class="bi bi-people me-2"></i>User Types:
                                </strong>
                                <div class="target-list">
                                    <?php $__currentLoopData = $announcement->target_users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="target-tag"><?php echo e(ucfirst($userType)); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($announcement->target_programs && count($announcement->target_programs) > 0): ?>
                            <div class="target-section">
                                <strong class="text-primary">
                                    <i class="bi bi-book me-2"></i>Programs:
                                </strong>
                                <div class="target-list">
                                    <?php $__currentLoopData = $targetProgramNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $programName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="target-tag"><?php echo e($programName); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($announcement->target_batches && count($announcement->target_batches) > 0): ?>
                            <div class="target-section">
                                <strong class="text-primary">
                                    <i class="bi bi-collection me-2"></i>Batches:
                                </strong>
                                <div class="target-list">
                                    <?php $__currentLoopData = $targetBatchNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batchName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="target-tag"><?php echo e($batchName); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($announcement->target_plans && count($announcement->target_plans) > 0): ?>
                            <div class="target-section">
                                <strong class="text-primary">
                                    <i class="bi bi-puzzle me-2"></i>Enrollment Plans:
                                </strong>
                                <div class="target-list">
                                    <?php $__currentLoopData = $announcement->target_plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="target-tag"><?php echo e(ucfirst($plan)); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if((!$announcement->target_users || count($announcement->target_users) === 0) && 
                           (!$announcement->target_programs || count($announcement->target_programs) === 0) && 
                           (!$announcement->target_batches || count($announcement->target_batches) === 0) && 
                           (!$announcement->target_plans || count($announcement->target_plans) === 0)): ?>
                            <div class="text-center text-muted">
                                <i class="bi bi-globe me-2"></i>
                                <strong>All Students</strong>
                                <br>
                                <small>This announcement targets all students in your assigned programs</small>
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

<?php echo $__env->make('professor.professor-layouts.professor-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\professor\announcements\show.blade.php ENDPATH**/ ?>