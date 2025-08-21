

<?php $__env->startSection('title', 'Program Details'); ?>

<?php $__env->startSection('content'); ?>
<style>
/* Modern Program Details Styles */
.program-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
}

.detail-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    transition: all 0.3s ease;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.detail-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
}

.stat-badge {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 8px;
    padding: 0.5rem 1rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0.25rem;
}

.module-card {
    border-left: 4px solid #2563eb;
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.module-card:hover {
    border-left-color: #7c3aed;
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Enhanced Student Profile Styles */
.student-avatar-enhanced {
    position: relative;
}

.avatar-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
    border: 3px solid #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.status-indicator {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.student-item {
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb !important;
}

.student-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
    border-color: #d1d5db !important;
}

.student-name {
    color: #1f2937;
    font-size: 0.95rem;
}

.student-email {
    font-size: 0.8rem;
    line-height: 1.4;
}

.student-meta {
    gap: 0.5rem;
}

.student-actions .dropdown-toggle {
    border: none;
    background: transparent;
    color: #6b7280;
    padding: 0.25rem 0.5rem;
}

.student-actions .dropdown-toggle:hover {
    background: #f3f4f6;
    color: #374151;
}

.student-list {
    max-height: 600px;
    overflow-y: auto;
}

.student-list::-webkit-scrollbar {
    width: 6px;
}

.student-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.student-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.student-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Module Enhancement */
.module-icon .bg-primary {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Quick Actions Styling */
.btn-outline-primary:hover,
.btn-outline-success:hover,
.btn-outline-info:hover,
.btn-outline-warning:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.btn-modern {
    padding: 0.625rem 1.25rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary-modern {
    background: linear-gradient(135deg, #2563eb, #7c3aed);
    color: white;
}

.btn-primary-modern:hover {
    background: linear-gradient(135deg, #1d4ed8, #6d28d9);
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
}
</style>

<div class="container-fluid">
    <!-- Program Header -->
    <div class="program-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="h2 mb-3"><?php echo e($program->program_name); ?></h1>
                <p class="mb-3 opacity-90"><?php echo e($program->program_description ?? 'No description available.'); ?></p>
                <div class="d-flex flex-wrap">
                    <div class="stat-badge">
                        <i class="bi bi-people"></i>
                        <span><?php echo e($program->students->count()); ?> Students</span>
                    </div>
                    <div class="stat-badge">
                        <i class="bi bi-book"></i>
                        <span><?php echo e($program->modules->count()); ?> Modules</span>
                    </div>
                    <div class="stat-badge">
                        <i class="bi bi-calendar"></i>
                        <span>Created <?php echo e($program->created_at->format('M Y')); ?></span>
                    </div>
                </div>
            </div>
            <a href="<?php echo e(route('professor.programs')); ?>" class="btn btn-light btn-modern">
                <i class="bi bi-arrow-left me-1"></i>Back to Programs
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content Area -->
        <div class="col-lg-8">
            <!-- Program Analytics -->
            <div class="detail-card">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title d-flex align-items-center">
                        <i class="bi bi-bar-chart text-primary me-2"></i>
                        Program Analytics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-primary bg-gradient rounded-3 text-white">
                                <i class="bi bi-people display-6 mb-2"></i>
                                <h4 class="mb-1"><?php echo e($program->students->count()); ?></h4>
                                <small class="text-white-50">Enrolled Students</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-success bg-gradient rounded-3 text-white">
                                <i class="bi bi-book display-6 mb-2"></i>
                                <h4 class="mb-1"><?php echo e($program->modules->count()); ?></h4>
                                <small class="text-white-50">Total Modules</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-info bg-gradient rounded-3 text-white">
                                <i class="bi bi-calendar-event display-6 mb-2"></i>
                                <h4 class="mb-1"><?php echo e($program->created_at->diffForHumans()); ?></h4>
                                <small class="text-white-50">Program Age</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Modules Section -->
            <div class="detail-card">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title d-flex align-items-center">
                        <i class="bi bi-book text-primary me-2"></i>
                        Course Modules (<?php echo e($program->modules->count()); ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($program->modules->count() > 0): ?>
                        <div class="row">
                            <?php $__currentLoopData = $program->modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card module-card h-100 shadow-sm" style="min-height: 280px;">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex align-items-start justify-content-between mb-3">
                                                <div class="module-icon">
                                                    <div class="bg-primary bg-gradient rounded-3 p-2 d-inline-flex">
                                                        <i class="bi bi-journal-text text-white"></i>
                                                    </div>
                                                </div>
                                                <span class="badge bg-primary-subtle text-primary border">Module <?php echo e($loop->iteration); ?></span>
                                            </div>
                                            <h6 class="card-title fw-semibold mb-3"><?php echo e($module->module_name); ?></h6>
                                            <div class="flex-grow-1 mb-4">
                                                <p class="card-text text-muted mb-3 lh-base">
                                                    <?php echo e($module->module_description ?? 'No description available for this module. This module contains important course content and learning materials for students.'); ?>

                                                </p>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <div class="bg-light rounded p-2 text-center">
                                                            <small class="text-muted d-block">Courses</small>
                                                            <strong class="text-primary"><?php echo e($module->courses->count() ?? 0); ?></strong>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="bg-light rounded p-2 text-center">
                                                            <small class="text-muted d-block">Status</small>
                                                            <span class="badge bg-success-subtle text-success">Active</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-auto">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted d-flex align-items-center">
                                                        <i class="bi bi-calendar3 me-1"></i>
                                                        <?php echo e($module->created_at->format('M d, Y')); ?>

                                                    </small>
                                                    <button class="btn btn-outline-primary btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#moduleModal<?php echo e($module->modules_id); ?>">
                                                        <i class="bi bi-eye me-1"></i>View Details
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-book text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                            </div>
                            <h6 class="text-muted mb-2">No Modules Available</h6>
                            <p class="text-muted small">This program doesn't have any modules yet. Modules will appear here once they are created.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Enrolled Students -->
            <div class="detail-card">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title d-flex align-items-center justify-content-between">
                        <span class="d-flex align-items-center">
                            <i class="bi bi-people text-primary me-2"></i>
                            Enrolled Students
                        </span>
                        <span class="badge bg-primary-subtle text-primary"><?php echo e($program->students->count()); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($program->students->count() > 0): ?>
                        <div class="student-list">
                            <?php $__currentLoopData = $program->students->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="student-item d-flex align-items-center p-3 mb-3 bg-light bg-gradient rounded-3 border border-light shadow-sm">
                                    <div class="student-avatar-enhanced me-3">
                                        <div class="position-relative">
                                            <?php if($student->profile_photo && file_exists(public_path('storage/profile-photos/' . $student->profile_photo))): ?>
                                                <img src="<?php echo e(asset('storage/profile-photos/' . $student->profile_photo)); ?>" 
                                                     alt="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>"
                                                     class="avatar-circle"
                                                     style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                            <?php elseif($student->profile_photo && file_exists(public_path('storage/' . $student->profile_photo))): ?>
                                                <img src="<?php echo e(asset('storage/' . $student->profile_photo)); ?>" 
                                                     alt="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>"
                                                     class="avatar-circle"
                                                     style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                            <?php elseif($student->photo_2x2 && file_exists(public_path('storage/profile-photos/' . $student->photo_2x2))): ?>
                                                <img src="<?php echo e(asset('storage/profile-photos/' . $student->photo_2x2)); ?>" 
                                                     alt="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>"
                                                     class="avatar-circle"
                                                     style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                            <?php elseif($student->photo_2x2 && file_exists(public_path('storage/' . $student->photo_2x2))): ?>
                                                <img src="<?php echo e(asset('storage/' . $student->photo_2x2)); ?>" 
                                                     alt="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>"
                                                     class="avatar-circle"
                                                     style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                            <?php elseif($student->passport_photo && file_exists(public_path('storage/profile-photos/' . $student->passport_photo))): ?>
                                                <img src="<?php echo e(asset('storage/profile-photos/' . $student->passport_photo)); ?>" 
                                                     alt="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>"
                                                     class="avatar-circle"
                                                     style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                            <?php elseif($student->passport_photo && file_exists(public_path('storage/' . $student->passport_photo))): ?>
                                                <img src="<?php echo e(asset('storage/' . $student->passport_photo)); ?>" 
                                                     alt="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>"
                                                     class="avatar-circle"
                                                     style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                            <?php elseif($student->id_photo && file_exists(public_path('storage/profile-photos/' . $student->id_photo))): ?>
                                                <img src="<?php echo e(asset('storage/profile-photos/' . $student->id_photo)); ?>" 
                                                     alt="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>"
                                                     class="avatar-circle"
                                                     style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                            <?php elseif($student->id_photo && file_exists(public_path('storage/' . $student->id_photo))): ?>
                                                <img src="<?php echo e(asset('storage/' . $student->id_photo)); ?>" 
                                                     alt="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>"
                                                     class="avatar-circle"
                                                     style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                            <?php else: ?>
                                                <div class="avatar-circle">
                                                    <?php echo e(substr($student->firstname, 0, 1)); ?><?php echo e(substr($student->lastname, 0, 1)); ?>

                                                </div>
                                            <?php endif; ?>
                                            <div class="status-indicator bg-success"></div>
                                        </div>
                                    </div>
                                    <div class="student-info flex-grow-1">
                                        <h6 class="student-name mb-1 fw-semibold"><?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?></h6>
                                        <div class="student-details">
                                            <p class="student-email text-muted small mb-1">
                                                <i class="bi bi-envelope me-1"></i><?php echo e($student->email); ?>

                                            </p>
                                            <div class="student-meta d-flex align-items-center">
                                                <span class="badge bg-info-subtle text-info me-2">
                                                    <i class="bi bi-person-badge me-1"></i>Student
                                                </span>
                                                <?php if($student->student_id): ?>
                                                    <small class="text-muted">
                                                        ID: <?php echo e($student->student_id); ?>

                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php if($program->students->count() > 8): ?>
                                <div class="text-center mt-3">
                                    <button class="btn btn-outline-primary btn-sm" onclick="toggleAllStudents()">
                                        <span id="toggleStudentsText">View <?php echo e($program->students->count() - 8); ?> More Students</span>
                                        <i class="bi bi-chevron-down ms-1" id="toggleStudentsIcon"></i>
                                    </button>
                                </div>
                                <div id="additionalStudents" style="display: none;" class="mt-3">
                                    <?php $__currentLoopData = $program->students->skip(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="student-item d-flex align-items-center p-3 mb-3 bg-light bg-gradient rounded-3 border border-light shadow-sm">
                                            <div class="student-avatar-enhanced me-3">
                                                <div class="position-relative">
                                                    <?php if($student->profile_photo && file_exists(public_path('storage/profile-photos/' . $student->profile_photo))): ?>
                                                        <img src="<?php echo e(asset('storage/profile-photos/' . $student->profile_photo)); ?>" 
                                                             alt="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>"
                                                             class="avatar-circle"
                                                             style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                                    <?php elseif($student->profile_photo && file_exists(public_path('storage/' . $student->profile_photo))): ?>
                                                        <img src="<?php echo e(asset('storage/' . $student->profile_photo)); ?>" 
                                                             alt="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>"
                                                             class="avatar-circle"
                                                             style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                                    <?php elseif($student->photo_2x2 && file_exists(public_path('storage/profile-photos/' . $student->photo_2x2))): ?>
                                                        <img src="<?php echo e(asset('storage/profile-photos/' . $student->photo_2x2)); ?>" 
                                                             alt="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>"
                                                             class="avatar-circle"
                                                             style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                                    <?php elseif($student->photo_2x2 && file_exists(public_path('storage/' . $student->photo_2x2))): ?>
                                                        <img src="<?php echo e(asset('storage/' . $student->photo_2x2)); ?>" 
                                                             alt="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>"
                                                             class="avatar-circle"
                                                             style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                                    <?php elseif($student->passport_photo && file_exists(public_path('storage/profile-photos/' . $student->passport_photo))): ?>
                                                        <img src="<?php echo e(asset('storage/profile-photos/' . $student->passport_photo)); ?>" 
                                                             alt="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>"
                                                             class="avatar-circle"
                                                             style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                                    <?php elseif($student->passport_photo && file_exists(public_path('storage/' . $student->passport_photo))): ?>
                                                        <img src="<?php echo e(asset('storage/' . $student->passport_photo)); ?>" 
                                                             alt="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>"
                                                             class="avatar-circle"
                                                             style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                                    <?php elseif($student->id_photo && file_exists(public_path('storage/profile-photos/' . $student->id_photo))): ?>
                                                        <img src="<?php echo e(asset('storage/profile-photos/' . $student->id_photo)); ?>" 
                                                             alt="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>"
                                                             class="avatar-circle"
                                                             style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                                    <?php elseif($student->id_photo && file_exists(public_path('storage/' . $student->id_photo))): ?>
                                                        <img src="<?php echo e(asset('storage/' . $student->id_photo)); ?>" 
                                                             alt="<?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?>"
                                                             class="avatar-circle"
                                                             style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                                    <?php else: ?>
                                                        <div class="avatar-circle">
                                                            <?php echo e(substr($student->firstname, 0, 1)); ?><?php echo e(substr($student->lastname, 0, 1)); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="status-indicator bg-success"></div>
                                                </div>
                                            </div>
                                            <div class="student-info flex-grow-1">
                                                <h6 class="student-name mb-1 fw-semibold"><?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?></h6>
                                                <div class="student-details">
                                                    <p class="student-email text-muted small mb-1">
                                                        <i class="bi bi-envelope me-1"></i><?php echo e($student->email); ?>

                                                    </p>
                                                    <div class="student-meta d-flex align-items-center">
                                                        <span class="badge bg-info-subtle text-info me-2">
                                                            <i class="bi bi-person-badge me-1"></i>Student
                                                        </span>
                                                        <?php if($student->student_id): ?>
                                                            <small class="text-muted">
                                                                ID: <?php echo e($student->student_id); ?>

                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-people text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                            </div>
                            <h6 class="text-muted mb-2">No Students Enrolled</h6>
                            <p class="text-muted small">No students have enrolled in this program yet. Students will appear here once they enroll.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle students visibility
function toggleAllStudents() {
    const additionalDiv = document.getElementById('additionalStudents');
    const toggleText = document.getElementById('toggleStudentsText');
    const toggleIcon = document.getElementById('toggleStudentsIcon');
    
    if (additionalDiv.style.display === 'none') {
        additionalDiv.style.display = 'block';
        toggleText.textContent = 'Show Less';
        toggleIcon.className = 'bi bi-chevron-up ms-1';
    } else {
        additionalDiv.style.display = 'none';
        const hiddenCount = <?php echo e($program->students->count() - 8); ?>;
        toggleText.textContent = `View ${hiddenCount} More Students`;
        toggleIcon.className = 'bi bi-chevron-down ms-1';
    }
}

// Add smooth scrolling and fade effects
document.addEventListener('DOMContentLoaded', function() {
    // Add fade-in animation for student items
    const studentItems = document.querySelectorAll('.student-item');
    studentItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        
        setTimeout(() => {
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Add smooth hover effects for cards
    const cards = document.querySelectorAll('.detail-card, .module-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>

<?php if(session('success')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show position-fixed';
            alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
            alert.innerHTML = `
                <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alert);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 5000);
        });
    </script>
<?php endif; ?>

<!-- Module Details Modals -->
<?php $__currentLoopData = $program->modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="moduleModal<?php echo e($module->modules_id); ?>" tabindex="-1" aria-labelledby="moduleModalLabel<?php echo e($module->modules_id); ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #2563eb, #7c3aed);">
                <h5 class="modal-title" id="moduleModalLabel<?php echo e($module->modules_id); ?>">
                    <i class="bi bi-book me-2"></i><?php echo e($module->module_name); ?>

                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <h6 class="text-primary mb-3">Module Information</h6>
                        <div class="mb-3">
                            <strong>Description:</strong>
                            <p class="text-muted mt-1"><?php echo e($module->module_description ?? 'No description available'); ?></p>
                        </div>
                        <div class="mb-3">
                            <strong>Created:</strong>
                            <span class="text-muted"><?php echo e($module->created_at->format('F d, Y')); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light rounded p-3">
                            <h6 class="text-center mb-3">Module Stats</h6>
                            <div class="text-center">
                                <div class="mb-2">
                                    <i class="bi bi-journal-text text-primary fs-4"></i>
                                    <div class="small text-muted">Courses</div>
                                    <div class="fw-bold"><?php echo e($module->courses->count()); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if($module->courses && $module->courses->count() > 0): ?>
                <hr>
                <h6 class="text-primary mb-3">
                    <i class="bi bi-list-ul me-2"></i>Courses in this Module
                </h6>
                <div class="row">
                    <?php $__currentLoopData = $module->courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-primary bg-gradient rounded-2 p-2 me-2">
                                        <i class="bi bi-play-circle text-white"></i>
                                    </div>
                                    <h6 class="card-title text-primary mb-0 fw-semibold">
                                        <?php echo e($course->subject_name ?? 'Course ' . $loop->iteration); ?>

                                    </h6>
                                </div>
                                <p class="card-text small text-muted mb-3">
                                    <?php echo e(Str::limit($course->subject_description ?? 'This course contains important learning materials and content for students to study and complete.', 120)); ?>

                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <?php echo e($course->created_at ? $course->created_at->format('M d, Y') : 'N/A'); ?>

                                    </small>
                                    <span class="badge bg-primary-subtle text-primary">
                                        <i class="bi bi-book me-1"></i>Course
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php else: ?>
                <hr>
                <div class="text-center py-4">
                    <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mt-2">No courses added to this module yet.</p>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('professor.professor-layouts.professor-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\professor\program-details.blade.php ENDPATH**/ ?>