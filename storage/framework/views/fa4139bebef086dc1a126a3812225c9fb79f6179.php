<!-- Professor Sidebar Component -->
<aside class="modern-sidebar" id="modernSidebar">
    <!-- Sidebar Header with Brand and Toggle -->
    <div class="sidebar-header">

        
        <!-- Profile Information in Header -->
        <?php
            $professor = \App\Models\Professor::where('professor_id', session('professor_id'))->first();
            $profilePhoto = $professor && $professor->profile_photo ? $professor->profile_photo : null;
            $professorName = $professor ? $professor->professor_name : session('professor_name', 'Professor');
        ?>
        
        <div class="header-profile">
            <?php if($profilePhoto): ?>
                <img src="<?php echo e(asset('storage/' . $profilePhoto)); ?>" 
                     alt="Profile" 
                     class="header-profile-avatar">
            <?php else: ?>
                <div class="header-profile-avatar" style="background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px;">
                    <?php echo e(substr($professorName, 0, 1)); ?><?php echo e(substr(explode(' ', $professorName)[1] ?? '', 0, 1)); ?>

                </div>
            <?php endif; ?>
            <div class="header-profile-info">
                <p class="header-profile-name"><?php echo e($professorName); ?></p>
                <p class="header-profile-role">Professor</p>
            </div>
        </div>
    </div>
    
    <div class="sidebar-content" style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">
        
        <nav class="sidebar-nav">
            <!-- Dashboard -->
            <div class="nav-item">
                <a href="<?php echo e(route('professor.dashboard')); ?>" class="nav-link <?php if(Route::currentRouteName() === 'professor.dashboard'): ?> active <?php endif; ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <!-- Module Management -->
            <?php if(!empty($moduleManagementEnabled) && $moduleManagementEnabled): ?>
            <div class="nav-item">
                <a href="<?php echo e(route('professor.modules.index')); ?>" class="nav-link <?php if(Route::currentRouteName() === 'professor.modules.index'): ?> active <?php endif; ?>">
                    <i class="bi bi-journals"></i>
                    <span>Module Management</span>
                </a>
            </div>
            <?php endif; ?>
            <!-- Meetings -->
            <div class="nav-item">
                <a href="<?php echo e(route('professor.meetings')); ?>" class="nav-link <?php if(Route::currentRouteName() === 'professor.meetings'): ?> active <?php endif; ?>">
                    <i class="bi bi-calendar-event"></i>
                    <span>Meetings</span>
                </a>
            </div>
            <!-- Announcements -->
            <div class="nav-item">
                <a href="<?php echo e(route('professor.announcements.index')); ?>" class="nav-link <?php if(str_starts_with(Route::currentRouteName(), 'professor.announcements')): ?> active <?php endif; ?>">
                    <i class="bi bi-megaphone"></i>
                    <span>Announcements</span>
                </a>
            </div>
            <!-- Students Dropdown -->
            <div class="nav-item">
                <a class="nav-link dropdown-toggle" href="#studentsMenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="studentsMenu">
                    <i class="bi bi-people"></i>
                    <span>Students</span>
                    <i class="bi bi-chevron-down dropdown-arrow"></i>
                </a>
                <div class="collapse <?php if(str_starts_with(Route::currentRouteName(), 'professor.students')): ?> show <?php endif; ?>" id="studentsMenu">
                    <div class="submenu">
                        <a href="<?php echo e(route('professor.students.index')); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'professor.students.index'): ?> active <?php endif; ?>">
                            <i class="bi bi-person-lines-fill"></i>
                            <span>All Students</span>
                        </a>
                        <a href="<?php echo e(route('professor.students.batches')); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'professor.students.batches'): ?> active <?php endif; ?>">
                            <i class="bi bi-collection"></i>
                            <span>My Batches</span>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Programs -->
            <div class="nav-item">
                <a href="<?php echo e(route('professor.programs')); ?>" class="nav-link <?php if(Route::currentRouteName() === 'professor.programs'): ?> active <?php endif; ?>">
                    <i class="bi bi-book"></i>
                    <span>My Programs</span>
                </a>
            </div>
            
            <!-- Submissions/Grading -->
            <div class="nav-item">
                <a href="<?php echo e(route('professor.submissions.index')); ?>" class="nav-link <?php if(str_starts_with(Route::currentRouteName(), 'professor.submissions')): ?> active <?php endif; ?>">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Submissions</span>
                </a>
            </div>
            
            <!-- Profile -->
            <div class="nav-item">
                <a href="<?php echo e(route('professor.profile')); ?>" class="nav-link <?php if(Route::currentRouteName() === 'professor.profile'): ?> active <?php endif; ?>">
                    <i class="bi bi-person-circle"></i>
                    <span>Profile</span>
                </a>
            </div>
            
            <!-- Logout -->
            <div class="nav-item">
                <form action="<?php echo e(route('logout')); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="nav-link logout-btn" onclick="return confirm('Are you sure you want to logout?')">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </nav>
    </div>
</aside>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Mobile Toggle Button -->
<button class="mobile-sidebar-toggle" id="mobileSidebarToggle" title="Toggle Sidebar">
    <i class="bi bi-list"></i>
</button><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/professor/professor-layouts/professor-sidebar.blade.php ENDPATH**/ ?>