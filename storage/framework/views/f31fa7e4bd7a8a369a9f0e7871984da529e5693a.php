

<?php $__env->startSection('title', $profile['name'] . ' - Student Profile'); ?>

<?php $__env->startSection('content'); ?>
<style>
/* Modern Dashboard Styles */
:root {
  --primary-color: #2563eb;
  --secondary-color: #7c3aed;
  --success-color: #059669;
  --warning-color: #d97706;
  --danger-color: #dc2626;
  --info-color: #0891b2;
  --dark-color: #1f2937;
  --light-color: #f8fafc;
  --border-radius: 16px;
  --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
  --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
  --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Reset overflow for professor dashboard */
html, body {
  overflow-x: hidden;
  overflow-y: auto !important;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
}

.professor-container {
  overflow: visible !important;
  min-height: 100vh;
}

.main-content-area {
  overflow: visible !important;
  min-height: 100vh;
}

.content-wrapper {
  overflow-y: auto !important;
  height: auto !important;
  min-height: 100vh;
  padding: 2rem;
  background: transparent;
  position: relative;
  width: 100%;
}

/* Modern container styling */
.content-wrapper .container-fluid {
  overflow: visible !important;
  height: auto !important;
  padding: 0;
  max-width: 1400px;
  margin: 0 auto;
  box-sizing: border-box;
  width: 100%;
}

/* Header Section */
.dashboard-header {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: var(--border-radius);
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: var(--shadow-lg);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.welcome-title {
  font-size: 2.5rem;
  font-weight: 700;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 0.5rem;
}

.welcome-subtitle {
  font-size: 1.1rem;
  color: #64748b;
  font-weight: 400;
}

/* Modern Stats Cards */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: var(--border-radius);
  padding: 2rem;
  box-shadow: var(--shadow-lg);
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: var(--transition);
  position: relative;
  overflow: hidden;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-xl);
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
}

.stat-card h3 {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.stat-card p {
  color: #64748b;
  font-size: 1rem;
  margin-bottom: 0;
  font-weight: 500;
}

.stat-card .stat-icon {
  position: absolute;
  top: 1.5rem;
  right: 1.5rem;
  font-size: 2rem;
  opacity: 0.1;
  color: var(--primary-color);
}

/* Profile Cards */
.profile-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-lg);
  border: 1px solid rgba(255, 255, 255, 0.2);
  overflow: hidden;
  transition: var(--transition);
}

.profile-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-xl);
}

.profile-card .card-header {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  border: none;
  padding: 1.5rem;
}

.profile-card .card-body {
  padding: 2rem;
}

.profile-avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  border: 4px solid rgba(255, 255, 255, 0.3);
  object-fit: cover;
  box-shadow: var(--shadow-md);
}

.profile-name {
  font-size: 2rem;
  font-weight: 700;
  color: var(--dark-color);
  margin-bottom: 0.5rem;
}

.profile-email {
  color: #64748b;
  font-size: 1.1rem;
  margin-bottom: 1rem;
}

.profile-badges {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.profile-badge {
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.9rem;
}

.profile-badge.primary {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
}

.profile-badge.success {
  background: var(--success-color);
  color: white;
}

.profile-badge.secondary {
  background: #64748b;
  color: white;
}

/* Enrollment Cards */
.enrollment-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: var(--border-radius);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: var(--shadow-md);
  transition: var(--transition);
  overflow: hidden;
}

.enrollment-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-lg);
}

.enrollment-card .card-header {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  border: none;
  padding: 1.5rem;
}

.enrollment-card .card-body {
  padding: 1.5rem;
}

.enrollment-item {
  background: rgba(255, 255, 255, 0.8);
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  border: 1px solid rgba(255, 255, 255, 0.3);
  transition: var(--transition);
}

.enrollment-item:hover {
  background: rgba(255, 255, 255, 0.95);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.enrollment-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: var(--primary-color);
  margin-bottom: 0.5rem;
}

.enrollment-date {
  color: #64748b;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.enrollment-status {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
  background: var(--success-color);
  color: white;
  margin-bottom: 1rem;
}

.enrollment-btn {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  border: none;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-weight: 600;
  transition: var(--transition);
  text-decoration: none;
  display: inline-block;
}

.enrollment-btn:hover {
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
  color: white;
  text-decoration: none;
}

/* Info Cards */
.info-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-lg);
  border: 1px solid rgba(255, 255, 255, 0.2);
  overflow: hidden;
  transition: var(--transition);
}

.info-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-xl);
}

.info-card .card-header {
  border: none;
  padding: 1.5rem;
  font-weight: 600;
}

.info-card .card-body {
  padding: 1.5rem;
}

.info-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.info-item:last-child {
  border-bottom: none;
}

.info-label {
  font-weight: 600;
  color: #64748b;
}

.info-value {
  color: var(--dark-color);
  font-weight: 500;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
}

.action-btn {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  transition: var(--transition);
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.action-btn.primary {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  border: none;
}

.action-btn.info {
  background: var(--info-color);
  color: white;
  border: none;
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
  color: white;
  text-decoration: none;
}

/* Responsive Design */
@media (max-width: 768px) {
  .content-wrapper {
    padding: 1rem;
  }
  
  .profile-name {
    font-size: 1.5rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .action-buttons {
    flex-direction: column;
  }
}
</style>

<!-- Profile Header -->
<div class="dashboard-header">
    <div class="row align-items-center">
        <div class="col-md-2 text-center">
            <img src="<?php echo e($profile['avatar']); ?>" 
                 alt="<?php echo e($profile['name']); ?>" 
                 class="profile-avatar">
        </div>
        <div class="col-md-8">
            <h1 class="profile-name"><?php echo e($profile['name']); ?></h1>
            <p class="profile-email">
                <i class="fas fa-envelope me-2"></i><?php echo e($profile['email']); ?>

            </p>
            <div class="profile-badges">
                <span class="profile-badge primary">
                    <i class="fas fa-user-graduate me-2"></i>Student
                </span>
                <span class="profile-badge <?php echo e($profile['status'] === 'Online' ? 'success' : 'secondary'); ?>">
                    <i class="fas fa-circle me-1"></i><?php echo e($profile['status']); ?>

                </span>
            </div>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-alt me-2"></i>Joined <?php echo e($profile['created_at']->format('M d, Y')); ?>

            </p>
            <?php if($profile['last_seen']): ?>
                <p class="text-muted mb-0">
                    <i class="fas fa-clock me-2"></i>Last seen <?php echo e($profile['last_seen']->diffForHumans()); ?>

                </p>
            <?php endif; ?>
        </div>
        <div class="col-md-2 text-end">
            <a href="javascript:history.back()" class="action-btn info">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>
</div>

<!-- Student Information -->
<?php if(isset($profile['enrollments'])): ?>
    <div class="enrollment-card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-graduation-cap me-2"></i>Program Enrollments
            </h5>
        </div>
        <div class="card-body">
            <?php if(count($profile['enrollments']) > 0): ?>
                <div class="row">
                    <?php $__currentLoopData = $profile['enrollments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="enrollment-item">
                                <h6 class="enrollment-title">
                                    <i class="fas fa-book me-2"></i><?php echo e($enrollment['program']); ?>

                                </h6>
                                <p class="enrollment-date">
                                    <i class="fas fa-calendar me-1"></i>
                                    Enrolled: <?php echo e($enrollment['enrolled_at']->format('M d, Y')); ?>

                                </p>
                                <span class="enrollment-status"><?php echo e($enrollment['status']); ?></span>
                                <div class="mt-2">
                                    <a href="<?php echo e(route('professor.professor.view.program', $enrollment['program_id'])); ?>" 
                                       class="enrollment-btn">
                                        <i class="fas fa-eye me-1"></i>View Program
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-info-circle text-muted" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <h6 class="text-muted">No Program Enrollments</h6>
                    <p class="text-muted">This student is not currently enrolled in any programs.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Contact Information -->
<div class="row">
    <div class="col-md-6">
        <div class="info-card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-address-card me-2"></i>Contact Information
                </h6>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo e($profile['email']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Role:</span>
                    <span class="info-value">Student</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="profile-badge <?php echo e($profile['status'] === 'Online' ? 'success' : 'secondary'); ?>">
                            <?php echo e($profile['status']); ?>

                        </span>
                    </span>
                </div>
                <?php if(isset($profile['student_id'])): ?>
                    <div class="info-item">
                        <span class="info-label">Student ID:</span>
                        <span class="info-value"><?php echo e($profile['student_id']); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="info-card">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Account Information
                </h6>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <span class="info-label">Member Since:</span>
                    <span class="info-value"><?php echo e($profile['created_at']->format('M d, Y')); ?></span>
                </div>
                <?php if($profile['last_seen']): ?>
                    <div class="info-item">
                        <span class="info-label">Last Activity:</span>
                        <span class="info-value"><?php echo e($profile['last_seen']->diffForHumans()); ?></span>
                    </div>
                <?php endif; ?>
                <div class="info-item">
                    <span class="info-label">Account Type:</span>
                    <span class="info-value">Student</span>
                </div>
                
                <div class="action-buttons">
                    <a href="#" class="action-btn primary">
                        <i class="fas fa-envelope me-1"></i>Send Message
                    </a>
                    <a href="<?php echo e(route('professor.students.index')); ?>" class="action-btn info">
                        <i class="fas fa-list me-1"></i>View All Students
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('professor.professor-layouts.professor-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\professor\student-profile.blade.php ENDPATH**/ ?>