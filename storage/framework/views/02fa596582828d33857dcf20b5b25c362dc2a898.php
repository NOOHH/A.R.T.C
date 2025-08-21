

<?php $__env->startSection('title', 'Enrollment Management'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<style>
/* Enrollment Management Styles */
.enrollment-container {
    background: #fff;
    padding: 40px 20px 60px;
    margin: 40px 0 0 0;
    max-width: 1400px;
    width: 100%;
    box-sizing: border-box;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

/* Course Assignment Section */
.course-assignment {
    background: #fff;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.assignment-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-label {
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.9rem;
}

.form-select, .form-input {
    padding: 10px 12px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: border-color 0.3s ease;
}

.form-select:focus, .form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.assign-btn {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.assign-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(67, 233, 123, 0.3);
}

/* Enrollment Statistics */
.enrollment-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Recent Enrollments */
.recent-enrollments {
    background: #fff;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.enrollment-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.enrollment-item:last-child {
    border-bottom: none;
}

.student-info {
    flex: 1;
}

.student-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 4px;
}

.program-name {
    font-size: 0.85rem;
    color: #6c757d;
}

.enrollment-date {
    font-size: 0.8rem;
    color: #6c757d;
}

.enrollment-actions {
    display: flex;
    gap: 8px;
}

/* Program Enrollment Overview */
.program-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.program-card {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    border-left: 4px solid #667eea;
}

.program-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
}

.program-stats {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.program-enrollments {
    font-size: 1.5rem;
    font-weight: 700;
    color: #667eea;
}

.view-details-btn {
    background: #667eea;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.view-details-btn:hover {
    background: #5a67d8;
    color: white;
    text-decoration: none;
}

@media (max-width: 768px) {
    .enrollment-container {
        padding: 20px 15px 40px;
        margin: 20px 0 0 0;
    }
    
    .enrollment-stats {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
    }
    
    .program-overview {
        grid-template-columns: 1fr;
    }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="enrollment-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-4 fw-bold text-dark mb-0">
            <i class="fas fa-graduation-cap me-3"></i>Enrollment Management
        </h1>
    </div>

    <!-- Enrollment Statistics -->
    <div class="enrollment-stats">
        <div class="stat-card">
            <div class="stat-number"><?php echo e($totalEnrollments ?? 11); ?></div>
            <div class="stat-label">Total Enrollments</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo e($activeEnrollments ?? 8); ?></div>
            <div class="stat-label">Active Students</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo e($pendingEnrollments ?? 2); ?></div>
            <div class="stat-label">Pending Registrations</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo e($completedCourses ?? 15); ?></div>
            <div class="stat-label">Completed Courses</div>
        </div>
    </div>

    <!-- Course Assignment Section -->
    <div class="course-assignment">
        <div class="d-flex align-items-center mb-3">
            <i class="fas fa-user-plus me-2 text-primary"></i>
            <h3 class="mb-0">Assign Course to Student</h3>
        </div>
        
        <form class="assignment-form" id="courseAssignmentForm">
            <?php echo csrf_field(); ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Select Student</label>
                        <select class="form-select" name="student_id" required>
                            <option value="">Choose a student...</option>
                            <?php if(!isset($dbError) || !$dbError): ?>
                                <?php $__currentLoopData = \App\Models\Student::take(20)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($student->student_id); ?>"><?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Select Program</label>
                        <select class="form-select" name="program_id" required>
                            <option value="">Choose a program...</option>
                            <?php if(!isset($dbError) || !$dbError): ?>
                                <?php $__currentLoopData = \App\Models\Program::where('is_archived', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($program->program_id); ?>"><?php echo e($program->program_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Notes (optional)</label>
                        <input type="text" class="form-control" name="notes" placeholder="Additional notes...">
                    </div>
                </div>
            </div>
            
            <div class="text-end">
                <button type="submit" class="assign-btn">
                    <i class="fas fa-user-plus me-2"></i>Assign Course
                </button>
            </div>
        </form>
    </div>

    <!-- Program Overview -->
    <div class="program-overview">
        <?php if(!isset($dbError) || !$dbError): ?>
            <?php $__currentLoopData = \App\Models\Program::where('is_archived', false)->withCount('enrollments')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="program-card">
                <div class="program-title"><?php echo e($program->program_name); ?></div>
                <div class="program-stats">
                    <div class="program-enrollments"><?php echo e($program->enrollments_count); ?></div>
                    <a href="<?php echo e(route('admin.programs.enrollments', $program->program_id)); ?>" class="view-details-btn">
                        <i class="fas fa-eye me-1"></i>View Details
                    </a>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>

    <!-- Recent Enrollments -->
    <div class="recent-enrollments">
        <div class="d-flex align-items-center mb-3">
            <i class="fas fa-clock me-2 text-primary"></i>
            <h3 class="mb-0">Recent Enrollments</h3>
        </div>
        
        <?php if(!isset($dbError) || !$dbError): ?>
            <?php $__empty_1 = true; $__currentLoopData = \App\Models\Enrollment::with(['student', 'program'])->latest()->take(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="enrollment-item">
                <div class="student-info">
                    <div class="student-name"><?php echo e($enrollment->student->firstname ?? 'Unknown'); ?> <?php echo e($enrollment->student->lastname ?? 'Student'); ?></div>
                    <div class="program-name"><?php echo e($enrollment->program->program_name ?? 'Unknown Program'); ?></div>
                </div>
                <div class="enrollment-date">
                    <?php echo e($enrollment->created_at ? $enrollment->created_at->diffForHumans() : 'Recently'); ?>

                </div>
                <div class="enrollment-actions">
                    <a href="<?php echo e(route('admin.programs.enrollments', $enrollment->program_id)); ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-4 text-muted">
                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                No recent enrollments found.
            </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-4 text-muted">
                <i class="fas fa-database fa-2x mb-2 d-block"></i>
                Cannot load enrollments. Database unavailable.
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Course Assignment Form
    const assignmentForm = document.getElementById('courseAssignmentForm');
    if (assignmentForm) {
        assignmentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('.assign-btn');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Assigning...';
            submitBtn.disabled = true;
            
            fetch('/admin/programs/assign', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Course assigned successfully!', 'success');
                    assignmentForm.reset();
                    // Refresh the page after a short delay to show updated data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showNotification(data.message || 'Error assigning course', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while assigning the course', 'error');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Notification function
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            background: ${type === 'success' ? '#28a745' : '#dc3545'};
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\admin-student-enrollments\admin-enrollments.blade.php ENDPATH**/ ?>