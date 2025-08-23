

<?php $__env->startSection('title', 'Certificate Management'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<style>
.progress-bar-custom {
    height: 20px;
    border-radius: 10px;
}
.status-badge {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}
.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 25px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}
.avatar-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.1rem;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bi bi-award me-2 text-primary"></i>
                        Certificate Management
                    </h1>
                    <p class="text-muted mb-0">Generate and manage student certificates based on program completion</p>
                </div>
                <div>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0"><?php echo e($studentsForCertificates->where('is_completed', true)->count()); ?></h3>
                                    <small>Completed Programs</small>
                                </div>
                                <i class="bi bi-mortarboard fs-1 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0"><?php echo e($studentsForCertificates->where('eligible_for_certificate', true)->count()); ?></h3>
                                    <small>Certificate Eligible</small>
                                </div>
                                <i class="bi bi-award fs-1 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0"><?php echo e($studentsForCertificates->whereBetween('overall_progress', [80, 99])->count()); ?></h3>
                                    <small>Nearly Complete</small>
                                </div>
                                <i class="bi bi-hourglass-split fs-1 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0"><?php echo e($studentsForCertificates->count()); ?></h3>
                                    <small>Total Eligible</small>
                                </div>
                                <i class="bi bi-people fs-1 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-check me-2"></i>Students Eligible for Certificates
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($studentsForCertificates->count() > 0): ?>
                        <div class="row g-3">
                            <?php $__currentLoopData = $studentsForCertificates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $studentData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $student = $studentData['student'];
                                    $user = $student->user;
                                ?>
                                <div class="col-lg-6 col-xl-4">
                                    <div class="card card-hover h-100">
                                        <div class="card-body">
                                            <!-- Student Info -->
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar-circle bg-primary text-white me-3">
                                                    <?php echo e(substr($user->user_firstname ?? 'S', 0, 1)); ?><?php echo e(substr($user->user_lastname ?? 'T', 0, 1)); ?>

                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">
                                                        <?php echo e($user->user_firstname ?? 'Unknown'); ?> <?php echo e($user->user_lastname ?? 'Student'); ?>

                                                    </h6>
                                                    <small class="text-muted"><?php echo e($student->student_id); ?></small>
                                                </div>
                                            </div>

                                            <!-- Progress Info -->
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="small text-muted">Overall Progress</span>
                                                    <span class="fw-bold text-primary"><?php echo e($studentData['overall_progress']); ?>%</span>
                                                </div>
                                                <div class="progress progress-bar-custom">
                                                    <div class="progress-bar bg-primary" 
                                                         style="width: <?php echo e($studentData['overall_progress']); ?>%"></div>
                                                </div>
                                            </div>

                                            <!-- Program Details -->
                                            <div class="mb-3">
                                                <?php $__currentLoopData = $studentData['enrollments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="small"><?php echo e($enrollment['program']->program_name ?? 'Unknown Program'); ?></span>
                                                        <?php if($enrollment['completion_status'] === 'completed'): ?>
                                                            <span class="badge bg-success status-badge">Completed</span>
                                                        <?php elseif($enrollment['completion_status'] === 'approved'): ?>
                                                            <span class="badge bg-primary status-badge">In Progress</span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>

                                            <!-- Completion Stats -->
                                            <div class="row text-center mb-3">
                                                <div class="col-6">
                                                    <div class="border-end">
                                                        <div class="fw-bold text-success"><?php echo e($studentData['completed_modules']); ?>/<?php echo e($studentData['total_modules']); ?></div>
                                                        <small class="text-muted">Modules</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="fw-bold text-info"><?php echo e($studentData['completed_courses']); ?>/<?php echo e($studentData['total_courses']); ?></div>
                                                    <small class="text-muted">Courses</small>
                                                </div>
                                            </div>

                                            <!-- Actions -->
                                            <div class="d-grid gap-2">
                                                <?php if($studentData['is_completed'] || $studentData['overall_progress'] >= 100): ?>
                                                    <a href="<?php echo e(route('certificate.download', ['user_id' => $user->user_id, 'enrollment_id' => $studentData['enrollments'][0]['enrollment']->enrollment_id ?? ''])); ?>" 
                                                       class="btn btn-success btn-sm">
                                                        <i class="bi bi-download me-2"></i>Generate Certificate
                                                    </a>
                                                <?php elseif($studentData['eligible_for_certificate']): ?>
                                                    <a href="<?php echo e(route('certificate.download', ['user_id' => $user->user_id, 'enrollment_id' => $studentData['enrollments'][0]['enrollment']->enrollment_id ?? ''])); ?>" 
                                                       class="btn btn-warning btn-sm">
                                                        <i class="bi bi-award me-2"></i>Generate Partial Certificate
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-outline-secondary btn-sm" disabled>
                                                        <i class="bi bi-clock me-2"></i>Not Eligible Yet
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <a href="<?php echo e(route('certificate.show', ['user_id' => $user->user_id])); ?>" 
                                                   class="btn btn-outline-primary btn-sm" target="_blank">
                                                    <i class="bi bi-eye me-2"></i>Preview Certificate
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-award text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No Students Eligible for Certificates</h4>
                            <p class="text-muted">Students will appear here when they complete at least 80% of their program or complete their studies.</p>
                            <a href="<?php echo e(route('admin.students.index')); ?>" class="btn btn-primary">
                                <i class="bi bi-people me-2"></i>View All Students
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/admin/certificates/index.blade.php ENDPATH**/ ?>