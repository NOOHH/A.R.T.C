

<?php $__env->startSection('title', 'My Enrolled Courses - A.R.T.C'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        /* Scoped styles for enrolled-courses page to prevent conflicts */
        .enrolled-courses-container {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .content-wrapper {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 0;
            margin: 0;
            overflow: hidden;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 2rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
            pointer-events: none;
        }

        .page-header-content {
            position: relative;
            z-index: 1;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }

        .courses-content {
            padding: 2rem;
        }

        .enrollment-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .enrollment-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            border-color: #667eea;
        }

        .enrollment-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .enrollment-header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .program-info {
            flex: 1;
        }

        .enrollment-card .program-name {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .program-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .enrollment-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            font-size: 0.9rem;
            color: #4a5568;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .meta-item i {
            color: #667eea;
        }

        .status-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid transparent;
        }

        .status-badge.approved {
            background: #d1fae5;
            color: #065f46;
            border-color: #a7f3d0;
        }

        .status-badge.pending {
            background: #fef3cd;
            color: #92400e;
            border-color: #fde68a;
        }

        .status-badge.paid {
            background: #dbeafe;
            color: #1e40af;
            border-color: #93c5fd;
        }

        .status-badge.modular {
            background: #e0e7ff;
            color: #3730a3;
            border-color: #c7d2fe;
        }

        .status-badge.full {
            background: #fce7f3;
            color: #be185d;
            border-color: #f9a8d4;
        }

        .enrollment-body {
            padding: 2rem;
        }

        .courses-section h3 {
            color: #1a202c;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .courses-section h3 i {
            color: #667eea;
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .course-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .course-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .course-card:hover {
            background: white;
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.1);
        }

        .course-card:hover::before {
            transform: scaleY(1);
        }

        .course-header {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .course-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .course-info {
            flex: 1;
        }

        .enrollment-card .course-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 0.25rem;
            line-height: 1.3;
        }

        .course-description {
            color: #4a5568;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .course-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: #718096;
            margin-top: auto;
        }

        .enrollment-card .module-tag {
            background: #e0e7ff;
            color: #3730a3;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.75rem;
        }

        .course-actions {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
        }

        .btn-course-view {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-course-view:hover {
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .no-courses {
            text-align: center;
            padding: 3rem 2rem;
            color: #4a5568;
        }

        .no-courses i {
            font-size: 3rem;
            color: #cbd5e0;
            margin-bottom: 1rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: #f8fafc;
            border-radius: 12px;
            margin: 2rem 0;
        }

        .empty-state i {
            font-size: 4rem;
            color: #cbd5e0;
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            color: #2d3748;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #718096;
            font-size: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .enrolled-courses-container {
                padding: 1rem;
            }

            .page-header {
                padding: 2rem 1rem 1.5rem;
            }

            .page-header h1 {
                font-size: 2rem;
                flex-direction: column;
                gap: 0.5rem;
            }

            .courses-content {
                padding: 1.5rem;
            }

            .enrollment-header {
                padding: 1rem;
            }

            .enrollment-header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .enrollment-body {
                padding: 1.5rem;
            }

            .courses-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .course-card {
                padding: 1rem;
            }

            .enrollment-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="enrolled-courses-container">
    <div class="content-wrapper">
        <!-- Header Section -->
        <div class="page-header">
            <div class="page-header-content">
                <h1>
                    <i class="bi bi-journal-bookmark"></i>
                    My Enrolled Courses
                </h1>
                <p>View all your current course enrollments and track your learning progress</p>
            </div>
        </div>

        <!-- Courses Content -->
        <div class="courses-content">
            <?php if(count($enrolledCoursesData) > 0): ?>
                <?php $__currentLoopData = $enrolledCoursesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="enrollment-card">
                        <!-- Enrollment Header -->
                        <div class="enrollment-header">
                            <div class="enrollment-header-content">
                                <div class="program-info">
                                    <div class="program-name">
                                        <div class="program-icon">
                                            <i class="bi bi-mortarboard-fill"></i>
                                        </div>
                                        <?php echo e($enrollment['program_name']); ?>

                                    </div>
                                    <div class="enrollment-meta">
                                        <div class="meta-item">
                                            <i class="bi bi-calendar-check"></i>
                                            <span>Enrolled: <?php echo e($enrollment['enrolled_at']); ?></span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="bi bi-box-seam"></i>
                                            <span>Package: <?php echo e($enrollment['package_name']); ?></span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="bi bi-clock"></i>
                                            <span>Mode: <?php echo e(ucfirst($enrollment['learning_mode'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="status-badges">
                                    <span class="status-badge <?php echo e(strtolower($enrollment['enrollment_status'])); ?>">
                                        <?php echo e(ucfirst($enrollment['enrollment_status'])); ?>

                                    </span>
                                    <span class="status-badge <?php echo e(strtolower($enrollment['payment_status'])); ?>">
                                        <?php echo e(ucfirst($enrollment['payment_status'])); ?>

                                    </span>
                                    <span class="status-badge <?php echo e(strtolower($enrollment['enrollment_type'])); ?>">
                                        <?php echo e($enrollment['enrollment_type']); ?>

                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Enrollment Body -->
                        <div class="enrollment-body">
                            <div class="courses-section">
                                <h3>
                                    <i class="bi bi-book-half"></i>
                                    Enrolled Courses
                                    <span class="badge bg-primary"><?php echo e(count($enrollment['courses'])); ?></span>
                                </h3>

                                <?php if(count($enrollment['courses']) > 0): ?>
                                    <div class="courses-grid">
                                        <?php $__currentLoopData = $enrollment['courses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="course-card">
                                                <div class="course-header">
                                                    <div class="course-icon">
                                                        <i class="bi bi-book"></i>
                                                    </div>
                                                    <div class="course-info">
                                                        <div class="course-name"><?php echo e($course['course_name']); ?></div>
                                                        <div class="module-tag"><?php echo e($course['module_name']); ?></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="course-description">
                                                    <?php echo e($course['course_description'] ?: 'No description available.'); ?>

                                                </div>
                                                
                                                <div class="course-meta">
                                                    <small>
                                                        <i class="bi bi-calendar-plus"></i>
                                                        Added: <?php echo e($course['enrolled_at']); ?>

                                                    </small>
                                                </div>

                                                <div class="course-actions">
                                                    <a href="<?php echo e(route('student.course', ['courseId' => $course['program_id']])); ?>" 
                                                       class="btn-course-view">
                                                        <i class="bi bi-arrow-right"></i>
                                                        View Course
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="no-courses">
                                        <i class="bi bi-journal-x"></i>
                                        <p>No courses found in this enrollment.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <i class="bi bi-journal-bookmark"></i>
                    <h3>No Enrolled Courses</h3>
                    <p>You haven't enrolled in any courses yet. Start exploring our programs to begin your learning journey!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth animations to cards
        const cards = document.querySelectorAll('.enrollment-card, .course-card');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = `all 0.6s ease ${index * 0.1}s`;
            observer.observe(card);
        });
        
        // Enhanced hover effects
        const courseCards = document.querySelectorAll('.course-card');
        courseCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    });

        // Check if we were redirected from calendar
        const calendarAssignmentId = sessionStorage.getItem('calendarAssignmentId');
        const calendarLessonId = sessionStorage.getItem('calendarLessonId');
        const calendarProgramName = sessionStorage.getItem('calendarProgramName');
        const calendarProgramId = sessionStorage.getItem('calendarProgramId');
        const calendarModuleId = sessionStorage.getItem('calendarModuleId');
        const calendarCourseId = sessionStorage.getItem('calendarCourseId');
        
        if (calendarAssignmentId || calendarLessonId) {
            // Clear the session storage
            sessionStorage.removeItem('calendarAssignmentId');
            sessionStorage.removeItem('calendarProgramName');
            sessionStorage.removeItem('calendarLessonId');
            sessionStorage.removeItem('calendarProgramId');
            sessionStorage.removeItem('calendarModuleId');
            sessionStorage.removeItem('calendarCourseId');
            
            // Show notification about the redirect
            let message = 'Redirected from calendar';
            if (calendarAssignmentId) {
                message = `Looking for assignment in ${calendarProgramName || 'your courses'}`;
            } else if (calendarLessonId) {
                message = `Looking for lesson in ${calendarProgramName || 'your courses'}`;
            }
            
            showNotification(message, 'info');
            
            // If we have specific IDs, try to navigate to the course
            if (calendarCourseId && calendarCourseId !== '') {
                setTimeout(() => {
                    window.location.href = `/student/course/${calendarCourseId}`;
                }, 2000);
            }
        }
    }
    
    // Function to show notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('student.student-dashboard.student-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\student\student-courses\enrolled-courses.blade.php ENDPATH**/ ?>