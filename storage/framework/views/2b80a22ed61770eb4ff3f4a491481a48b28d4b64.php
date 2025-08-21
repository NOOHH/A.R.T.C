

<?php $__env->startSection('title', $profileData['name'] . ' - Program Profile'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%); min-height: 100vh;">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow-lg border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
                <div class="card-body p-5 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <div class="program-icon-hero d-inline-flex align-items-center justify-content-center mb-3">
                                <i class="bi bi-mortarboard-fill" style="font-size: 4rem;"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                <h1 class="mb-0 me-3 fw-bold"><?php echo e($profileData['name']); ?></h1>
                                <span class="badge bg-<?php echo e($profileData['is_active'] ? 'success' : 'warning'); ?> fs-6 px-3 py-2">
                                    <i class="bi bi-<?php echo e($profileData['is_active'] ? 'check-circle-fill' : 'pause-circle-fill'); ?> me-2"></i>
                                    <?php echo e($profileData['is_active'] ? 'Active Program' : 'Inactive'); ?>

                                </span>
                            </div>
                            <p class="lead mb-3 opacity-90"><?php echo e($profileData['description'] ?: 'A comprehensive educational program designed to provide students with the knowledge and skills needed for professional success.'); ?></p>
                            <div class="d-flex align-items-center text-white-50">
                                <i class="bi bi-calendar-plus me-2"></i>
                                <span>Established <?php echo e($profileData['created_at']->format('F Y')); ?></span>
                                <?php if($profileData['is_archived']): ?>
                                    <span class="badge bg-dark ms-3">
                                        <i class="bi bi-archive me-1"></i>Archived
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <button onclick="history.back()" class="btn btn-light btn-lg shadow-sm">
                                <i class="bi bi-arrow-left me-2"></i>Back
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Statistics -->
    <div class="row mb-5 g-4">
        <div class="col-md-3">
            <div class="card stats-card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white text-center p-4">
                    <div class="stats-icon mb-3">
                        <i class="bi bi-collection" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="fw-bold mb-2"><?php echo e(count($profileData['modules'])); ?></h2>
                    <p class="mb-0 opacity-90">Learning Modules</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white text-center p-4">
                    <div class="stats-icon mb-3">
                        <i class="bi bi-book" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="fw-bold mb-2"><?php echo e($profileData['modules']->sum('courses_count')); ?></h2>
                    <p class="mb-0 opacity-90">Total Courses</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white text-center p-4">
                    <div class="stats-icon mb-3">
                        <i class="bi bi-person-badge" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="fw-bold mb-2"><?php echo e(count($profileData['professors'])); ?></h2>
                    <p class="mb-0 opacity-90">Expert Instructors</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="card-body text-white text-center p-4">
                    <div class="stats-icon mb-3">
                        <i class="bi bi-people" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="fw-bold mb-2"><?php echo e(count($profileData['students'])); ?></h2>
                    <p class="mb-0 opacity-90">Active Students</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Program Curriculum -->
        <div class="col-lg-8">
            <?php if(count($profileData['modules']) > 0): ?>
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 py-4" style="border-radius: 15px 15px 0 0;">
                        <div class="d-flex align-items-center">
                            <div class="feature-icon me-3">
                                <i class="bi bi-book-half text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 fw-bold text-dark">Program Curriculum</h4>
                                <p class="text-muted mb-0">Explore the comprehensive learning modules and courses</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="accordion curriculum-accordion" id="modulesAccordion">
                            <?php $__currentLoopData = $profileData['modules']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="accordion-item border-0 shadow-sm mb-3" style="border-radius: 12px;">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button <?php echo e($index > 0 ? 'collapsed' : ''); ?> fw-bold" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#module<?php echo e($index); ?>" 
                                                aria-expanded="<?php echo e($index === 0 ? 'true' : 'false'); ?>"
                                                style="border-radius: 12px; background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
                                            <div class="d-flex align-items-center w-100">
                                                <div class="module-icon me-3">
                                                    <i class="bi bi-collection text-primary"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="module-title"><?php echo e($module['module_name']); ?></div>
                                                    <small class="text-muted">
                                                        <?php echo e($module['courses_count']); ?> course<?php echo e($module['courses_count'] !== 1 ? 's' : ''); ?>

                                                        <?php if($module['module_description']): ?>
                                                            • <?php echo e(Str::limit($module['module_description'], 80)); ?>

                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                                <span class="badge bg-primary ms-3 px-3 py-2"><?php echo e($module['courses_count']); ?></span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="module<?php echo e($index); ?>" 
                                         class="accordion-collapse collapse <?php echo e($index === 0 ? 'show' : ''); ?>" 
                                         data-bs-parent="#modulesAccordion">
                                        <div class="accordion-body bg-light" style="border-radius: 0 0 12px 12px;">
                                            <?php if($module['module_description']): ?>
                                                <div class="module-description mb-4 p-3 bg-white rounded shadow-sm">
                                                    <h6 class="text-primary mb-2">
                                                        <i class="bi bi-info-circle me-2"></i>Module Overview
                                                    </h6>
                                                    <p class="mb-0 text-muted"><?php echo e($module['module_description']); ?></p>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if(count($module['courses']) > 0): ?>
                                                <h6 class="fw-bold mb-3 text-dark">
                                                    <i class="bi bi-list-ul me-2"></i>Course Content
                                                </h6>
                                                <div class="row g-3">
                                                    <?php $__currentLoopData = $module['courses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="col-md-6">
                                                            <div class="course-card card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #007bff !important;">
                                                                <div class="card-body p-3">
                                                                    <div class="d-flex align-items-start mb-2">
                                                                        <div class="course-icon me-3">
                                                                            <i class="bi bi-book text-primary"></i>
                                                                        </div>
                                                                        <div class="flex-grow-1">
                                                                            <h6 class="course-title mb-1 fw-bold text-dark"><?php echo e($course['course_title']); ?></h6>
                                                                            <div class="course-meta mb-2">
                                                                                <span class="badge bg-light text-dark border">Course <?php echo e($course['course_id']); ?></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php if($course['course_description']): ?>
                                                                        <p class="course-description text-muted small mb-0"><?php echo e($course['course_description']); ?></p>
                                                                    <?php else: ?>
                                                                        <p class="course-description text-muted small mb-0 fst-italic">Course details will be available soon</p>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="alert alert-info border-0 shadow-sm">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-info-circle text-info me-3" style="font-size: 1.5rem;"></i>
                                                        <div>
                                                            <h6 class="mb-1">No courses available</h6>
                                                            <p class="mb-0 small">This module is being prepared and courses will be added soon.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-book text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-muted">Curriculum Under Development</h4>
                        <p class="text-muted">The program curriculum is being developed and will be available soon.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Teaching Faculty -->
            <?php if(count($profileData['professors']) > 0): ?>
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 py-4" style="border-radius: 15px 15px 0 0;">
                        <div class="d-flex align-items-center">
                            <div class="feature-icon me-3">
                                <i class="bi bi-person-badge text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h5 class="mb-1 fw-bold text-dark">Expert Faculty</h5>
                                <small class="text-muted"><?php echo e(count($profileData['professors'])); ?> instructor<?php echo e(count($profileData['professors']) !== 1 ? 's' : ''); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <?php $__currentLoopData = $profileData['professors']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $professor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="faculty-item d-flex align-items-center mb-3 p-3 bg-light rounded" style="border-radius: 12px;">
                                <div class="faculty-avatar me-3 position-relative">
                                    <img src="<?php echo e($professor['avatar'] ?: '/images/default-avatar.png'); ?>" 
                                         alt="<?php echo e($professor['name']); ?>" 
                                         class="rounded-circle border border-3 border-white shadow-sm" 
                                         width="50" height="50">
                                    <span class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-2 border-white" 
                                          style="width: 15px; height: 15px;"></span>
                                </div>
                                <div class="faculty-info flex-grow-1">
                                    <h6 class="mb-1 fw-bold text-dark"><?php echo e($professor['name'] ?: 'Professor'); ?></h6>
                                    <p class="text-muted small mb-2"><?php echo e($professor['email'] ?: 'No contact available'); ?></p>
                                    <?php if(!empty($professor['professor_id'])): ?>
                                        <a href="<?php echo e(route('profile.professor', $professor['professor_id'])); ?>" 
                                           class="btn btn-sm btn-outline-success rounded-pill">
                                            <i class="bi bi-person me-1"></i>View Profile
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Program Information -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-4" style="border-radius: 15px 15px 0 0;">
                    <div class="d-flex align-items-center">
                        <div class="feature-icon me-3">
                            <i class="bi bi-info-circle text-info" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold text-dark">Program Details</h5>
                            <small class="text-muted">Key information</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="info-list">
                        <div class="info-item d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">
                                <i class="bi bi-calendar-plus me-2"></i>Established
                            </span>
                            <span class="fw-bold"><?php echo e($profileData['created_at']->format('Y')); ?></span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">
                                <i class="bi bi-collection me-2"></i>Learning Modules
                            </span>
                            <span class="fw-bold"><?php echo e(count($profileData['modules'])); ?></span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">
                                <i class="bi bi-book me-2"></i>Total Courses
                            </span>
                            <span class="fw-bold"><?php echo e($profileData['modules']->sum('courses_count')); ?></span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">
                                <i class="bi bi-people me-2"></i>Enrolled Students
                            </span>
                            <span class="fw-bold"><?php echo e(count($profileData['students'])); ?></span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted">
                                <i class="bi bi-shield-check me-2"></i>Status
                            </span>
                            <span class="badge bg-<?php echo e($profileData['is_active'] ? 'success' : 'warning'); ?> px-3 py-2">
                                <?php echo e($profileData['is_active'] ? 'Active' : 'Inactive'); ?>

                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Enrollment -->
            <?php if(count($profileData['students']) > 0): ?>
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 py-4" style="border-radius: 15px 15px 0 0;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon me-3">
                                    <i class="bi bi-people text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold text-dark">Student Community</h5>
                                    <small class="text-muted">Recent enrollments</small>
                                </div>
                            </div>
                            <span class="badge bg-warning text-dark px-3 py-2"><?php echo e(count($profileData['students'])); ?></span>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <?php if(auth()->guard()->check()): ?>
                            <?php if(in_array(auth()->user()->role, ['admin', 'director', 'professor'])): ?>
                                <?php $__currentLoopData = $profileData['students']->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="student-item d-flex align-items-center mb-3 p-3 bg-light rounded" style="border-radius: 12px;">
                                        <div class="student-avatar me-3 position-relative">
                                            <img src="<?php echo e($student['avatar'] ?: '/images/default-avatar.png'); ?>" 
                                                 alt="<?php echo e($student['name']); ?>" 
                                                 class="rounded-circle border border-3 border-white shadow-sm" 
                                                 width="40" height="40">
                                            <span class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-2 border-white" 
                                                  style="width: 12px; height: 12px;"></span>
                                        </div>
                                        <div class="student-info flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-dark small"><?php echo e($student['name'] ?: 'Student'); ?></h6>
                                            <p class="text-muted small mb-2"><?php echo e($student['email'] ?: 'No email'); ?></p>
                                            <?php if($student['user_id']): ?>
                                                <a href="<?php echo e(route('profile.user', $student['user_id'])); ?>" 
                                                   class="btn btn-xs btn-outline-primary rounded-pill">
                                                    <i class="bi bi-person me-1"></i>Profile
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(count($profileData['students']) > 5): ?>
                                    <div class="text-center mt-3">
                                        <small class="text-muted bg-light p-2 rounded">
                                            <i class="bi bi-three-dots me-1"></i>and <?php echo e(count($profileData['students']) - 5); ?> more student<?php echo e(count($profileData['students']) - 5 !== 1 ? 's' : ''); ?>

                                        </small>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="text-center py-3">
                                    <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                    <h6 class="mt-2 text-muted"><?php echo e(count($profileData['students'])); ?> Enrolled Students</h6>
                                    <p class="text-muted small mb-0">Student details are protected</p>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-3">
                                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                <h6 class="mt-2 text-muted"><?php echo e(count($profileData['students'])); ?> Active Students</h6>
                                <p class="text-muted small mb-0">Join this thriving learning community</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Admin Actions -->
            <?php if(auth()->guard()->check()): ?>
                <?php if(auth()->user()->role === 'admin' || auth()->user()->role === 'director'): ?>
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                        <div class="card-header bg-white border-0 py-4" style="border-radius: 15px 15px 0 0;">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon me-3">
                                    <i class="bi bi-gear text-secondary" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold text-dark">Quick Actions</h5>
                                    <small class="text-muted">Program management</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary rounded-pill">
                                    <i class="bi bi-pencil-square me-2"></i>Edit Program Details
                                </button>
                                <button class="btn btn-outline-success rounded-pill">
                                    <i class="bi bi-people me-2"></i>Manage Enrollments
                                </button>
                                <button class="btn btn-outline-info rounded-pill">
                                    <i class="bi bi-bar-chart me-2"></i>View Analytics
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.program-icon-hero {
    width: 120px;
    height: 120px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    backdrop-filter: blur(10px);
}

.stats-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 15px !important;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
}

.stats-icon {
    background: rgba(255, 255, 255, 0.2);
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.feature-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.curriculum-accordion .accordion-item {
    border: none !important;
    margin-bottom: 1rem;
}

.curriculum-accordion .accordion-button {
    border: none;
    padding: 1.5rem;
    font-weight: 600;
    color: #333;
}

.curriculum-accordion .accordion-button:not(.collapsed) {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.course-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-radius: 12px !important;
}

.course-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
}

.course-icon {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.faculty-item,
.student-item {
    transition: transform 0.2s ease;
}

.faculty-item:hover,
.student-item:hover {
    transform: translateX(5px);
}

.info-item {
    transition: background-color 0.2s ease;
}

.info-item:hover {
    background-color: #f8f9fa !important;
    border-radius: 8px;
    padding-left: 10px !important;
    padding-right: 10px !important;
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.badge {
    font-weight: 500;
}

@media (max-width: 768px) {
    .stats-card {
        margin-bottom: 1rem;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
    
    .program-icon-hero {
        width: 80px;
        height: 80px;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\profiles\program.blade.php ENDPATH**/ ?>