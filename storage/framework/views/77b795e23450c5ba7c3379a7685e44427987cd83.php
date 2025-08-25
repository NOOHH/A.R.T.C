

<?php $__env->startSection('title', 'Announcement Management'); ?>

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
.announcement-card {
    transition: all 0.3s ease;
    border: 1px solid #e3e6f0;
}

.announcement-card:hover {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    transform: translateY(-2px);
}

.announcement-type-badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.announcement-type-general { background-color: #3498db; }
.announcement-type-urgent { background-color: #e74c3c; }
.announcement-type-event { background-color: #f39c12; }
.announcement-type-system { background-color: #9b59b6; }

.target-info {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 0.85rem;
}

.announcement-meta {
    font-size: 0.875rem;
    color: #6c757d;
}

.table-responsive {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.creator-avatar {
    width: 32px;
    height: 32px;
    object-fit: cover;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-megaphone me-2"></i>Announcement Management
            </h1>
            <p class="text-muted">Create and manage system announcements</p>
        </div>
        <?php
            $createUrl = $tenantSlug 
                ? route('tenant.admin.announcements.create', ['tenant' => $tenantSlug]) . $urlParams
                : route('admin.announcements.create');
        ?>
        <a href="<?php echo e($createUrl); ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create Announcement
        </a>
    </div>

    <!-- Success Message -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Announcements
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($totalAnnouncements); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-megaphone fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Urgent
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo e($urgentAnnouncements); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo e($activeAnnouncements); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-eye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-list-ul me-2"></i>All Announcements
            </h6>
        </div>
        <div class="card-body">
            <?php if($announcements->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">Creator</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Target Audience</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php
                                                $currentUserId = auth('admin')->id();
                                                $isCurrentUser = false;
                                                $creatorName = 'Unknown';
                                                $creatorAvatar = null;
                                                
                                                if ($announcement->admin_id && $announcement->admin_id == $currentUserId) {
                                                    $isCurrentUser = true;
                                                    $creatorName = 'You';
                                                } elseif ($announcement->admin_id) {
                                                    $creatorName = 'Admin';
                                                } elseif ($announcement->professor_id) {
                                                    $creatorName = 'Professor';
                                                } else {
                                                    $creatorName = 'System';
                                                }
                                            ?>
                                            
                                            <div class="me-2">
                                                <?php if($creatorAvatar && file_exists(public_path($creatorAvatar))): ?>
                                                    <img src="<?php echo e(asset($creatorAvatar)); ?>" alt="Profile" class="creator-avatar rounded-circle">
                                                <?php else: ?>
                                                    <img src="<?php echo e(asset('images/default-avatar.svg')); ?>" alt="Default Profile" class="creator-avatar rounded-circle">
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <small class="fw-bold"><?php echo e($isCurrentUser ? 'YOU' : $creatorName); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong><?php echo e($announcement->title); ?></strong>
                                            <?php if($announcement->description ?? null): ?>
                                                <small class="text-muted"><?php echo e(Str::limit($announcement->description, 60)); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge announcement-type-badge announcement-type-<?php echo e($announcement->type); ?>">
                                            <?php echo e(ucfirst($announcement->type)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <div class="target-info">
                                            <?php if($announcement->target_scope === 'all'): ?>
                                                <span class="badge bg-secondary">All Users</span>
                                            <?php else: ?>
                                                <?php
                                                    // Handle both array (new format) and JSON string (old format)
                                                    $targetUsers = [];
                                                    if (isset($announcement->target_users)) {
                                                        if (is_array($announcement->target_users)) {
                                                            $targetUsers = $announcement->target_users;
                                                        } elseif (is_string($announcement->target_users)) {
                                                            // Handle HTML-encoded JSON strings
                                                            $decodedString = html_entity_decode($announcement->target_users);
                                                            $targetUsers = json_decode($decodedString, true) ?: [];
                                                        }
                                                    }
                                                    
                                                    $targetPrograms = [];
                                                    if (isset($announcement->target_programs)) {
                                                        if (is_array($announcement->target_programs)) {
                                                            $targetPrograms = $announcement->target_programs;
                                                        } elseif (is_string($announcement->target_programs)) {
                                                            // Handle HTML-encoded JSON strings
                                                            $decodedString = html_entity_decode($announcement->target_programs);
                                                            $targetPrograms = json_decode($decodedString, true) ?: [];
                                                        }
                                                    }
                                                    
                                                    // Ensure both are arrays
                                                    $targetUsers = is_array($targetUsers) ? $targetUsers : [];
                                                    $targetPrograms = is_array($targetPrograms) ? $targetPrograms : [];
                                                ?>
                                                <?php if($targetUsers): ?>
                                                    <?php $__currentLoopData = $targetUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <span class="badge bg-info me-1"><?php echo e(ucfirst($user)); ?></span>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                                <?php if($targetPrograms): ?>
                                                    <br><small class="text-muted"><?php echo e(count($targetPrograms)); ?> program(s)</small>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="announcement-meta">
                                            <?php echo e(\Carbon\Carbon::parse($announcement->created_at)->format('M d, Y')); ?><br>
                                            <small><?php echo e(\Carbon\Carbon::parse($announcement->created_at)->format('g:i A')); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <?php
                                                $viewUrl = $tenantSlug 
                                                    ? route('tenant.admin.announcements.show', ['tenant' => $tenantSlug, 'id' => $announcement->announcement_id]) . $urlParams
                                                    : route('admin.announcements.show', $announcement->announcement_id);
                                                    
                                                $editUrl = $tenantSlug 
                                                    ? route('tenant.admin.announcements.edit', ['tenant' => $tenantSlug, 'id' => $announcement->announcement_id]) . $urlParams
                                                    : route('admin.announcements.edit', $announcement->announcement_id);
                                            ?>
                                            <a href="<?php echo e($viewUrl); ?>" 
                                               class="btn btn-outline-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?php echo e($editUrl); ?>" 
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button class="btn btn-outline-danger delete-btn" 
                                                    data-id="<?php echo e($announcement->announcement_id); ?>"
                                                    data-title="<?php echo e($announcement->title); ?>"
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-megaphone text-muted" style="font-size: 3rem;"></i>
                    <h4 class="text-muted mt-3">No announcements found</h4>
                    <p class="text-muted">Create your first announcement to get started.</p>
                    <?php
                        $emptyCreateUrl = $tenantSlug 
                            ? route('tenant.admin.announcements.create', ['tenant' => $tenantSlug]) . $urlParams
                            : route('admin.announcements.create');
                    ?>
                    <a href="<?php echo e($emptyCreateUrl); ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Announcement
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the announcement "<strong id="deleteAnnouncementTitle"></strong>"?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete buttons
    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const title = this.dataset.title;
            
            document.getElementById('deleteAnnouncementTitle').textContent = title;
            document.getElementById('deleteForm').action = `/admin/announcements/${id}`;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/admin/announcements/index.blade.php ENDPATH**/ ?>