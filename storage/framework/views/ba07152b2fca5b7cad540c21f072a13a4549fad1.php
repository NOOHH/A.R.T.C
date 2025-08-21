<div class="student-detail-content">
    <div class="row">
        <div class="col-md-4">
            <div class="text-center">
                <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                    <i class="fas fa-user fa-2x text-white"></i>
                </div>
                <h5 class="fw-bold"><?php echo e($student->user_firstname); ?> <?php echo e($student->user_lastname); ?></h5>
                <p class="text-muted"><?php echo e($student->email); ?></p>
                <span class="badge bg-info"><?php echo e($student->registration->enrollment_type ?? 'N/A'); ?></span>
            </div>
        </div>
        <div class="col-md-8">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Student ID</label>
                    <p class="mb-2"><?php echo e($student->user_id); ?></p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Registration Date</label>
                    <p class="mb-2"><?php echo e($student->created_at->format('M d, Y')); ?></p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Phone</label>
                    <p class="mb-2"><?php echo e($student->registration->contact_number ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Address</label>
                    <p class="mb-2"><?php echo e($student->registration->street_address ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <hr class="my-4">
    
    <div class="row">
        <div class="col-md-6">
            <h6 class="fw-bold mb-3">Performance Metrics</h6>
            <div class="metrics-list">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Overall Progress:</span>
                    <div class="d-flex align-items-center">
                        <div class="progress me-2" style="width: 100px; height: 6px;">
                            <div class="progress-bar bg-success" style="width: <?php echo e(rand(60, 100)); ?>%"></div>
                        </div>
                        <span class="text-success fw-bold"><?php echo e(rand(60, 100)); ?>%</span>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Quiz Average:</span>
                    <span class="fw-bold"><?php echo e(rand(70, 95)); ?>%</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Attendance:</span>
                    <span class="fw-bold"><?php echo e(rand(85, 100)); ?>%</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Assignments Completed:</span>
                    <span class="fw-bold"><?php echo e(rand(15, 25)); ?>/25</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h6 class="fw-bold mb-3">Recent Activity</h6>
            <div class="activity-list">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 20px; height: 20px;">
                        <i class="fas fa-check text-white" style="font-size: 10px;"></i>
                    </div>
                    <small>Completed Quiz: Mathematics Fundamentals</small>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-info rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 20px; height: 20px;">
                        <i class="fas fa-book text-white" style="font-size: 10px;"></i>
                    </div>
                    <small>Started Module: Advanced Topics</small>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 20px; height: 20px;">
                        <i class="fas fa-clock text-white" style="font-size: 10px;"></i>
                    </div>
                    <small>Pending: Assignment Submission</small>
                </div>
            </div>
        </div>
    </div>
    
    <hr class="my-4">
    
    <div class="row">
        <div class="col-12">
            <h6 class="fw-bold mb-3">Subject Performance</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Progress</th>
                            <th>Quiz Avg</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Mathematics</td>
                            <td>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 85%"></div>
                                </div>
                                <small>85%</small>
                            </td>
                            <td>92%</td>
                            <td><span class="badge bg-success">Excellent</span></td>
                        </tr>
                        <tr>
                            <td>Science</td>
                            <td>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: 65%"></div>
                                </div>
                                <small>65%</small>
                            </td>
                            <td>78%</td>
                            <td><span class="badge bg-warning">Fair</span></td>
                        </tr>
                        <tr>
                            <td>English</td>
                            <td>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: 75%"></div>
                                </div>
                                <small>75%</small>
                            </td>
                            <td>88%</td>
                            <td><span class="badge bg-info">Good</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-lg {
    width: 80px;
    height: 80px;
}

.metrics-list .progress {
    background-color: #e9ecef;
}

.activity-list {
    max-height: 200px;
    overflow-y: auto;
}
</style>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\admin-analytics\partials\student-detail.blade.php ENDPATH**/ ?>