

<?php $__env->startSection('title', 'Dashboard'); ?>

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
  border: none;
  border-radius: var(--border-radius);
  padding: 2rem;
  box-shadow: var(--shadow-lg);
  transition: var(--transition);
  position: relative;
  overflow: hidden;
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

.stat-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-xl);
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
  font-size: 1.5rem;
  color: white;
  /* Ensure inline styles take precedence */
  background: var(--primary-color) !important;
}

.stat-number {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--dark-color);
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 0.9rem;
  color: #64748b;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Action Cards Grid */
.action-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.action-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border: none;
  border-radius: var(--border-radius);
  padding: 2rem;
  box-shadow: var(--shadow-lg);
  transition: var(--transition);
  text-decoration: none;
  color: inherit;
  display: block;
  position: relative;
  overflow: hidden;
}

.action-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
  transform: scaleX(0);
  transition: var(--transition);
}

.action-card:hover::before {
  transform: scaleX(1);
}

.action-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-xl);
  text-decoration: none;
  color: inherit;
}

.action-icon {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
  font-size: 1.25rem;
  color: white;
}

.action-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--dark-color);
  margin-bottom: 0.5rem;
}

.action-description {
  font-size: 0.9rem;
  color: #64748b;
  line-height: 1.5;
}

/* Content Sections */
.content-section {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-lg);
  margin-bottom: 2rem;
  overflow: hidden;
}

.section-header {
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: between;
  align-items: center;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--dark-color);
  margin: 0;
}

.section-body {
  padding: 2rem;
}

/* Program Cards */
.program-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.program-card {
  background: white;
  border-radius: 12px;
  box-shadow: var(--shadow-md);
  transition: var(--transition);
  overflow: hidden;
}

.program-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-lg);
}

.program-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e2e8f0;
}

.program-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--dark-color);
  margin-bottom: 0.5rem;
}

.program-description {
  font-size: 0.9rem;
  color: #64748b;
  line-height: 1.5;
}

.program-footer {
  padding: 1rem 1.5rem;
  background: #f8fafc;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* Announcement Cards */
.announcement-card {
  background: white;
  border-radius: 12px;
  box-shadow: var(--shadow-md);
  transition: var(--transition);
  margin-bottom: 1rem;
  overflow: hidden;
  position: relative;
}

.announcement-card::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
  background: var(--primary-color);
}

.announcement-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

/* Badges */
.badge-modern {
  padding: 0.375rem 0.75rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Buttons */
.btn-modern {
  padding: 0.625rem 1.25rem;
  border-radius: 8px;
  font-weight: 500;
  font-size: 0.875rem;
  transition: var(--transition);
  border: none;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn-primary-modern {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
}

.btn-primary-modern:hover {
  background: linear-gradient(135deg, #1d4ed8, #6d28d9);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 3rem;
  color: #64748b;
}

.empty-state-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
  color: #cbd5e1;
}

/* Responsive Design */
@media (max-width: 768px) {
  .content-wrapper {
    padding: 1rem;
  }
  
  .dashboard-header {
    padding: 1.5rem;
  }
  
  .welcome-title {
    font-size: 2rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .action-grid {
    grid-template-columns: 1fr;
  }
}

/* Animation keyframes */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fade-in-up {
  animation: fadeInUp 0.6s ease-out;
}

/* Ensure proper spacing */
.content-wrapper .row {
  margin-bottom: 1.5rem;
}

/* Force scrolling on the main content */
.main-content-area .content-wrapper {
  overflow-y: scroll !important;
  max-height: none !important;
}

</style>
<div class="container-fluid">
    <!-- Announcements Header Section -->
   
        <!-- Announcements Section -->
    <?php if(!empty($announcementManagementEnabled) && $announcementManagementEnabled && isset($announcements) && $announcements->count() > 0): ?>
    <div class="content-section fade-in-up">
        <div class="section-header">
            <h5 class="section-title">
                <i class="bi bi-megaphone me-2"></i>Recent Announcements
            </h5>
            <span class="badge badge-modern" style="background-color: var(--primary-color); color: white;">
                <?php echo e($announcements->count()); ?> Total
            </span>
        </div>
        <div class="section-body">
            <div class="row">
                <?php $__currentLoopData = $announcements->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4 mb-3">
                        <div class="announcement-card">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0 fw-semibold"><?php echo e($announcement->title); ?></h6>
                                    <?php if($announcement->priority === 'high'): ?>
                                        <span class="badge badge-modern" style="background-color: var(--danger-color); color: white;">
                                            <i class="bi bi-exclamation-triangle me-1"></i>High
                                        </span>
                                    <?php elseif($announcement->priority === 'medium'): ?>
                                        <span class="badge badge-modern" style="background-color: var(--warning-color); color: white;">
                                            <i class="bi bi-dash-circle me-1"></i>Medium
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-modern" style="background-color: var(--info-color); color: white;">
                                            <i class="bi bi-info-circle me-1"></i>Low
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <p class="card-text small text-muted mb-3 lh-base">
                                    <?php echo e(Str::limit($announcement->content, 120)); ?>

                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted d-flex align-items-center">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <?php echo e($announcement->created_at->format('M d, Y')); ?>

                                    </small>
                                    <?php if($announcement->target_scope === 'program_specific'): ?>
                                        <small class="text-primary d-flex align-items-center">
                                            <i class="bi bi-bookmark me-1"></i>Program Specific
                                        </small>
                                    <?php else: ?>
                                        <small class="text-success d-flex align-items-center">
                                            <i class="bi bi-globe me-1"></i>General
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <?php if($announcement->expire_date && $announcement->expire_date->isToday()): ?>
                                    <div class="mt-2">
                                        <small class="text-warning d-flex align-items-center">
                                            <i class="bi bi-exclamation-triangle me-1"></i>Expires today
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            
            <?php if($announcements->count() > 3): ?>
                <div class="text-center mt-3">
                    <button class="btn btn-modern btn-primary-modern" onclick="toggleAllAnnouncements()">
                        <span id="toggleText">Show All Announcements</span>
                        <i class="bi bi-chevron-down ms-1" id="toggleIcon"></i>
                    </button>
                </div>
                <div id="additionalAnnouncements" style="display: none;" class="mt-4">
                    <div class="row">
                        <?php $__currentLoopData = $announcements->skip(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-4 mb-3">
                                <div class="announcement-card">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0 fw-semibold"><?php echo e($announcement->title); ?></h6>
                                            <?php if($announcement->priority === 'high'): ?>
                                                <span class="badge badge-modern" style="background-color: var(--danger-color); color: white;">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>High
                                                </span>
                                            <?php elseif($announcement->priority === 'medium'): ?>
                                                <span class="badge badge-modern" style="background-color: var(--warning-color); color: white;">
                                                    <i class="bi bi-dash-circle me-1"></i>Medium
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-modern" style="background-color: var(--info-color); color: white;">
                                                    <i class="bi bi-info-circle me-1"></i>Low
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="card-text small text-muted mb-3 lh-base">
                                            <?php echo e(Str::limit($announcement->content, 120)); ?>

                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted d-flex align-items-center">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                <?php echo e($announcement->created_at->format('M d, Y')); ?>

                                            </small>
                                            <?php if($announcement->target_scope === 'program_specific'): ?>
                                                <small class="text-primary d-flex align-items-center">
                                                    <i class="bi bi-bookmark me-1"></i>Program Specific
                                                </small>
                                            <?php else: ?>
                                                <small class="text-success d-flex align-items-center">
                                                    <i class="bi bi-globe me-1"></i>General
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                        <?php if($announcement->expire_date && $announcement->expire_date->isToday()): ?>
                                            <div class="mt-2">
                                                <small class="text-warning d-flex align-items-center">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>Expires today
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <!-- Default Welcome Header when no announcements -->
    <div class="dashboard-header fade-in-up">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="welcome-title">Welcome back, <?php echo e($professor->full_name); ?>!</h1>
                <p class="welcome-subtitle">Manage your assigned programs, upload video content, and track student progress from your modern dashboard.</p>
            </div>
            <div class="d-none d-md-block">
                <div class="text-muted small">
                    <i class="bi bi-calendar3"></i>
                    <?php echo e(now()->format('l, F j, Y')); ?>

                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Modern Stats Grid -->
    <div class="stats-grid fade-in-up">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;">
                <i class="bi bi-collection"></i>
            </div>
            <div class="stat-number"><?php echo e($totalPrograms); ?></div>
            <div class="stat-label">Assigned Programs</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669) !important;">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-number"><?php echo e($totalStudents); ?></div>
            <div class="stat-label">Total Students</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706) !important;">
                <i class="bi bi-book"></i>
            </div>
            <div class="stat-number"><?php echo e($totalModules); ?></div>
            <div class="stat-label">Total Modules</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;">
                <i class="bi bi-play-circle"></i>
            </div>
            <div class="stat-number"><?php echo e($assignedPrograms->where('pivot.video_link', '!=', null)->count()); ?></div>
            <div class="stat-label">Videos Added</div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <?php
        $gradingEnabled = \App\Models\AdminSetting::where('setting_key', 'grading_enabled')->value('setting_value') !== 'false';
        $moduleManagementEnabled = \App\Models\AdminSetting::where('setting_key', 'professor_module_management_enabled')->value('setting_value') === '1';
        $announcementManagementEnabled = \App\Models\AdminSetting::where('setting_key', 'professor_announcement_management_enabled')->value('setting_value') === '1';
    ?>
    
    <div class="action-grid fade-in-up">
        <a href="<?php echo e(route('professor.programs')); ?>" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
                <i class="bi bi-collection"></i>
            </div>
            <h5 class="action-title">View Programs</h5>
            <p class="action-description">Access your assigned programs and manage content with ease.</p>
        </a>
        
        <a href="<?php echo e(route('professor.meetings')); ?>" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, var(--success-color), #10b981);">
                <i class="bi bi-calendar-event"></i>
            </div>
            <h5 class="action-title">Meetings</h5>
            <p class="action-description">Track and manage class meetings efficiently.</p>
        </a>
        
        <?php if(!empty($announcementManagementEnabled) && $announcementManagementEnabled): ?>
        <a href="<?php echo e(route('professor.announcements.index')); ?>" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, var(--warning-color), #f59e0b);">
                <i class="bi bi-megaphone"></i>
            </div>
            <h5 class="action-title">Announcements</h5>
            <p class="action-description">View and manage your announcements to students.</p>
        </a>
        <?php endif; ?>
        
        <?php if($gradingEnabled): ?>
        <a href="<?php echo e(route('professor.grading')); ?>" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, var(--danger-color), #ef4444);">
                <i class="bi bi-award"></i>
            </div>
            <h5 class="action-title">Grading</h5>
            <p class="action-description">Evaluate and assign grades to students.</p>
        </a>
        <?php endif; ?>
        
        <a href="<?php echo e(route('professor.profile')); ?>" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, var(--info-color), #06b6d4);">
                <i class="bi bi-person-circle"></i>
            </div>
            <h5 class="action-title">Profile</h5>
            <p class="action-description">Update your profile information and settings.</p>
        </a>
        
         
        <a href="<?php echo e(route('professor.students.index')); ?>" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, #8b5cf6, #a855f7);">
                <i class="bi bi-people"></i>
            </div>
            <h5 class="action-title">Students</h5>
            <p class="action-description">View and manage your students effectively.</p>
        </a>
        
        <?php if($moduleManagementEnabled): ?>
        <a href="<?php echo e(route('professor.modules.index')); ?>" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, #059669, #10b981);">
                <i class="bi bi-journals"></i>
            </div>
            <h5 class="action-title">Module Management</h5>
            <p class="action-description">Create and manage modules for your assigned programs.</p>
        </a>
        <?php endif; ?>
        
        <?php if($aiQuizEnabled ?? false): ?>
        <a href="<?php echo e(route('professor.quiz-generator')); ?>" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, var(--danger-color), #f87171);">
                <i class="bi bi-robot"></i>
            </div>
            <h5 class="action-title">AI Quiz Generator</h5>
            <p class="action-description">Generate quizzes from uploaded documents using AI.</p>
        </a>
        <?php endif; ?>
    </div>

    <!-- Your Programs Section -->
    <div class="content-section fade-in-up">
        <div class="section-header">
            <h5 class="section-title">
                <i class="bi bi-collection me-2"></i>Your Programs
            </h5>
            <a href="<?php echo e(route('professor.programs')); ?>" class="btn btn-modern btn-primary-modern">View All Programs</a>
        </div>
        <div class="section-body">
            <?php if($assignedPrograms->count() > 0): ?>
                <div class="program-grid">
                    <?php $__currentLoopData = $assignedPrograms->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="program-card">
                            <div class="program-header">
                                <h6 class="program-title"><?php echo e($program->program_name); ?></h6>
                                <p class="program-description">
                                    <?php echo e(Str::limit($program->program_description, 100)); ?>

                                </p>
                            </div>
                            <div class="program-footer">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-people me-1 text-muted"></i>
                                    <small class="text-muted"><?php echo e($program->students->count()); ?> students</small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if($program->pivot->video_link): ?>
                                        <span class="badge badge-modern" style="background-color: var(--success-color); color: white;">
                                            <i class="bi bi-check-circle me-1"></i>Video Added
                                        </span>
                                    <?php else: ?>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('professor.program.details', $program->program_id)); ?>" 
                                       class="btn btn-modern btn-primary-modern btn-sm">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-collection"></i>
                    </div>
                    <h5 class="text-muted">No Programs Assigned</h5>
                    <p class="text-muted">You haven't been assigned to any programs yet. Contact your administrator for assistance.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

  
</div>

<script>
function toggleAllAnnouncements() {
    const additionalDiv = document.getElementById('additionalAnnouncements');
    const toggleText = document.getElementById('toggleText');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (additionalDiv.style.display === 'none') {
        additionalDiv.style.display = 'block';
        toggleText.textContent = 'Show Less';
        toggleIcon.className = 'bi bi-chevron-up ms-1';
    } else {
        additionalDiv.style.display = 'none';
        toggleText.textContent = 'Show All Announcements';
        toggleIcon.className = 'bi bi-chevron-down ms-1';
    }
}

// Add fade-in animation on scroll
document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.fade-in-up').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('professor.professor-layouts.professor-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\professor\dashboard.blade.php ENDPATH**/ ?>