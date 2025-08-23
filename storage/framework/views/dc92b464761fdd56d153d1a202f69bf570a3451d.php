

<?php $__env->startSection('title', 'Archived Content'); ?>

<?php $__env->startSection('head'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/admin/admin-dashboard.css')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="bi bi-archive"></i> Archived Content Management
                    </h1>
                    <p class="page-description">Manage and review archived programs, courses, and materials</p>
                </div>

                <?php if(isset($isPreview) && $isPreview): ?>
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-eye"></i> Preview Mode Active - Showing sample archived content
                    </div>
                <?php endif; ?>

                <!-- Archived Programs Section -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-folder-x me-2"></i>Archived Programs
                        </h6>
                        <span class="badge bg-secondary"><?php echo e(isset($archivedPrograms) ? $archivedPrograms->count() : 3); ?></span>
                    </div>
                    <div class="card-body">
                        <?php if(isset($archivedPrograms) && $archivedPrograms->count() > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Program Name</th>
                                            <th>Archived Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $archivedPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($program->program_name ?? $program->name ?? 'Unknown Program'); ?></td>
                                            <td><?php echo e($program->archived_at ? $program->archived_at->format('M d, Y') : 'Unknown'); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-success" onclick="restoreProgram(<?php echo e($program->id); ?>)">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <!-- Mock data for preview or empty state -->
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Program Name</th>
                                            <th>Archived Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Nursing Program 2024-A</td>
                                            <td><?php echo e(now()->subDays(30)->format('M d, Y')); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-success">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>MedTech Batch 2024-B</td>
                                            <td><?php echo e(now()->subDays(15)->format('M d, Y')); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-success">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Pharmacy Review 2024-C</td>
                                            <td><?php echo e(now()->subDays(7)->format('M d, Y')); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-success">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Archived Courses Section -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-warning">
                            <i class="bi bi-journal-x me-2"></i>Archived Courses
                        </h6>
                        <span class="badge bg-secondary"><?php echo e(isset($archivedCourses) ? $archivedCourses->count() : 2); ?></span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Course Name</th>
                                        <th>Program</th>
                                        <th>Archived Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Advanced Nursing Procedures</td>
                                        <td>Nursing Program</td>
                                        <td><?php echo e(now()->subDays(20)->format('M d, Y')); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-success">
                                                <i class="bi bi-arrow-counterclockwise"></i> Restore
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Clinical Chemistry Lab</td>
                                        <td>Medical Technology</td>
                                        <td><?php echo e(now()->subDays(10)->format('M d, Y')); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-success">
                                                <i class="bi bi-arrow-counterclockwise"></i> Restore
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-body text-center">
                                <i class="bi bi-archive-fill display-4 text-muted"></i>
                                <h5 class="card-title mt-3">Archive Management</h5>
                                <p class="card-text">Review and manage all archived content from this central location.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-body text-center">
                                <i class="bi bi-arrow-counterclockwise display-4 text-success"></i>
                                <h5 class="card-title mt-3">Restore Content</h5>
                                <p class="card-text">Easily restore archived programs and courses when needed.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function restoreProgram(programId) {
    if (confirm('Are you sure you want to restore this program?')) {
        // Implementation for restoring program
        alert('Program restoration feature will be implemented here');
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/admin/archived/index.blade.php ENDPATH**/ ?>