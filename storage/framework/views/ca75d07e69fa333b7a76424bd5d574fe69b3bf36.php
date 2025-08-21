<!-- Admin Sidebar Component -->
<aside class="modern-sidebar" id="modernSidebar">
    <!-- Sidebar Header with Brand and Toggle -->
    <div class="sidebar-header">
        <?php
            $adminUser = null;
            $adminName = 'Admin';
            $adminRole = 'Admin';
            
            if (session('user_type') === 'director') {
                $adminUser = \App\Models\Director::where('directors_id', session('user_id'))->first();
                $adminName = $adminUser ? $adminUser->directors_name : session('user_name', 'Director');
                $adminRole = 'Director';
            } elseif (session('user_type') === 'admin') {
                $adminName = session('user_name', 'Admin');
                $adminRole = 'Admin';
            }

            $profilePhoto = $adminUser && $adminUser->profile_photo ? $adminUser->profile_photo : null;
        ?>

        <div class="header-profile">
            <?php if($profilePhoto): ?>
                <img src="<?php echo e(asset('storage/' . $profilePhoto)); ?>" alt="Profile" class="header-profile-avatar">
            <?php else: ?>
                <div class="header-profile-avatar" style="background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px;">
                    <?php echo e(substr($adminName, 0, 1)); ?><?php echo e(substr(explode(' ', $adminName)[1] ?? '', 0, 1)); ?>

                </div>
            <?php endif; ?>
            <div class="header-profile-info">
                <p class="header-profile-name"><?php echo e($adminName); ?></p>
                <p class="header-profile-role"><?php echo e($adminRole); ?></p>
            </div>
        </div>
    </div>

    <div class="sidebar-content d-flex flex-column overflow-hidden">
        <nav class="sidebar-nav">

            <!-- Dashboard -->
            <div class="nav-item">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="nav-link <?php if(Route::currentRouteName() === 'admin.dashboard'): ?> active <?php endif; ?>">
                    <i class="bi bi-speedometer2"></i><span>Dashboard</span>
                </a>
            </div>

            <!-- Registration Menu -->
            <?php
                $registrationMenuVisible = $isAdmin || ($isDirector && ($directorFeatures['manage_enrollments'] || $directorFeatures['manage_batches']));
            ?>
            <?php if($registrationMenuVisible): ?>
            <div class="nav-item">
                <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#collapseRegistration" role="button" aria-expanded="false" aria-controls="collapseRegistration">
                    <i class="bi bi-person-plus"></i><span>Registration</span>
                </a>
                <div class="collapse <?php if(str_starts_with(Route::currentRouteName(), 'admin.student.registration')): ?> show <?php endif; ?>" id="collapseRegistration">
                    <div class="submenu">
                        <?php if($isAdmin || ($isDirector && $directorFeatures['manage_enrollments'])): ?>
                            <a href="<?php echo e(route('admin.student.registration.pending')); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'admin.student.registration.pending'): ?> active <?php endif; ?>">
                                <i class="bi bi-clock"></i><span>Pending</span>
                            </a>
                            <a href="<?php echo e(route('admin.student.registration.history')); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'admin.student.registration.history'): ?> active <?php endif; ?>">
                                <i class="bi bi-archive"></i><span>History</span>
                            </a>
                            <a href="<?php echo e(route('admin.student.registration.payment.pending')); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'admin.student.registration.payment.pending'): ?> active <?php endif; ?>">
                                <i class="bi bi-credit-card"></i><span>Payment Pending</span>
                            </a>
                            <a href="<?php echo e(route('admin.student.registration.payment.history')); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'admin.student.registration.payment.history'): ?> active <?php endif; ?>">
                                <i class="bi bi-receipt"></i><span>Payment History</span>
                            </a>
                        <?php endif; ?>
                        <?php if($isAdmin || ($isDirector && $directorFeatures['manage_batches'])): ?>
                            <a href="<?php echo e(route('admin.batches.index')); ?>" class="submenu-link <?php if(str_starts_with(Route::currentRouteName(), 'admin.batches')): ?> active <?php endif; ?>">
                                <i class="bi bi-people"></i><span>Batch Enroll</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Account Management -->
            <?php
                $accountsMenuVisible = $isAdmin || ($isDirector && ($directorFeatures['view_students'] || $directorFeatures['manage_professors']));
            ?>
            <?php if($accountsMenuVisible): ?>
            <div class="nav-item">
                <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#collapseAccounts" role="button" aria-expanded="false" aria-controls="collapseAccounts">
                    <i class="bi bi-people"></i><span>Accounts</span>
                </a>
                <div class="collapse <?php if(str_starts_with(Route::currentRouteName(), 'admin.students') || str_starts_with(Route::currentRouteName(), 'admin.professors') || str_starts_with(Route::currentRouteName(), 'admin.directors')): ?> show <?php endif; ?>" id="collapseAccounts">
                    <div class="submenu">
                        <?php if($isAdmin || ($isDirector && $directorFeatures['view_students'])): ?>
                            <a href="<?php echo e(route('admin.students.index')); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'admin.students.index'): ?> active <?php endif; ?>">
                                <i class="bi bi-person"></i><span>Students</span>
                            </a>
                        <?php endif; ?>
                        <?php if($isAdmin): ?>
                            <a href="<?php echo e(route('admin.directors.index')); ?>" class="submenu-link <?php if(str_starts_with(Route::currentRouteName(), 'admin.directors')): ?> active <?php endif; ?>">
                                <i class="bi bi-person-badge"></i><span>Directors</span>
                            </a>
                        <?php endif; ?>
                        <?php if($isAdmin || ($isDirector && $directorFeatures['manage_professors'])): ?>
                            <a href="<?php echo e(route('admin.professors.index')); ?>" class="submenu-link <?php if(str_starts_with(Route::currentRouteName(), 'admin.professors')): ?> active <?php endif; ?>">
                                <i class="bi bi-person-workspace"></i><span>Professors</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Programs Menu -->
            <?php
                $programsMenuVisible = $isAdmin || ($isDirector && ($directorFeatures['manage_programs'] || $directorFeatures['manage_modules'] || $directorFeatures['manage_batches']));
            ?>
            <?php if($programsMenuVisible): ?>
            <div class="nav-item">
                <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#collapsePrograms" role="button" aria-expanded="false" aria-controls="collapsePrograms">
                    <i class="bi bi-mortarboard"></i><span>Programs</span>
                </a>
                <div class="collapse <?php if(str_starts_with(Route::currentRouteName(), 'admin.programs') || str_starts_with(Route::currentRouteName(), 'admin.modules') || Route::currentRouteName() === 'admin.packages.index' || Route::currentRouteName() === 'admin.certificates' || str_starts_with(Route::currentRouteName(), 'admin.submissions')): ?> show <?php endif; ?>" id="collapsePrograms">
                    <div class="submenu">
                        <?php if($isAdmin || ($isDirector && $directorFeatures['manage_programs'])): ?>
                            <a href="<?php echo e(route('admin.programs.index')); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'admin.programs.index'): ?> active <?php endif; ?>">
                                <i class="bi bi-collection"></i><span>Manage Programs</span>
                            </a>
                        <?php endif; ?>
                        <?php if($isAdmin || ($isDirector && $directorFeatures['manage_modules'])): ?>
                            <a href="<?php echo e(route('admin.modules.index')); ?>" class="submenu-link <?php if(str_starts_with(Route::currentRouteName(), 'admin.modules')): ?> active <?php endif; ?>">
                                <i class="bi bi-puzzle"></i><span>Manage Modules</span>
                            </a>
                        <?php endif; ?>
                        <?php if($isAdmin || ($isDirector && $directorFeatures['manage_batches'])): ?>
                            <a href="<?php echo e(route('admin.batches.index')); ?>" class="submenu-link <?php if(str_starts_with(Route::currentRouteName(), 'admin.batches')): ?> active <?php endif; ?>">
                                <i class="bi bi-people"></i><span>Manage Batches</span>
                            </a>
                        <?php endif; ?>
                        <?php if($isAdmin): ?>
                            <a href="<?php echo e(route('admin.packages.index')); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'admin.packages.index'): ?> active <?php endif; ?>">
                                <i class="bi bi-box-seam"></i><span>Packages</span>
                            </a>
                        <?php endif; ?>
                        <?php if($isAdmin): ?>
                            <a href="<?php echo e(route('admin.certificates')); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'admin.certificates'): ?> active <?php endif; ?>">
                                <i class="bi bi-award"></i><span>Certificates</span>
                            </a>
                        <?php endif; ?>
                        <?php if($isAdmin): ?>
                            <a href="<?php echo e(route('admin.submissions')); ?>" class="submenu-link <?php if(str_starts_with(Route::currentRouteName(), 'admin.submissions')): ?> active <?php endif; ?>">
                                <i class="bi bi-file-earmark-text"></i><span>Assignment Submissions</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Analytics -->
            <?php if($isAdmin || ($isDirector && $directorFeatures['view_analytics'])): ?>
            <div class="nav-item">
                <a href="<?php echo e(route('admin.analytics.index')); ?>" class="nav-link <?php if(Route::currentRouteName() === 'admin.analytics.index'): ?> active <?php endif; ?>">
                    <i class="bi bi-graph-up"></i><span>Analytics</span>
                </a>
            </div>
            <?php endif; ?>

            <!-- FAQ Management -->
            <div class="nav-item">
                <a href="<?php echo e(route('admin.faq.index')); ?>" class="nav-link <?php if(Route::currentRouteName() === 'admin.faq.index'): ?> active <?php endif; ?>">
                    <i class="bi bi-question-circle"></i><span>FAQ Management</span>
                </a>
            </div>

            <!-- Announcements -->
            <div class="nav-item">
                <a href="<?php echo e(route('admin.announcements.index')); ?>" class="nav-link <?php if(str_starts_with(Route::currentRouteName(), 'admin.announcements')): ?> active <?php endif; ?>">
                    <i class="bi bi-broadcast"></i><span>Announcements</span>
                </a>
            </div>

            <!-- Settings -->
            <?php if($isAdmin): ?>
            <div class="nav-item">
                <a href="<?php echo e(route('admin.settings.index')); ?>" class="nav-link <?php if(Route::currentRouteName() === 'admin.settings.index'): ?> active <?php endif; ?>">
                    <i class="bi bi-gear"></i><span>Settings</span>
                </a>
            </div>
            <?php endif; ?>

            <!-- Logout -->
            <div class="nav-item">
                <form action="<?php echo e(route('student.logout')); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="nav-link logout-btn" onclick="return confirm('Are you sure you want to logout?')">
                        <i class="bi bi-box-arrow-right"></i><span>Logout</span>
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
</button>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\admin-layouts\admin-sidebar.blade.php ENDPATH**/ ?>