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
        <?php
            // Detect tenant context and preserve URL parameters for proper routing
            $tenantSlug = null;
            $routePrefix = '';
            $isDraft = false;
            $urlParams = '';
            
            // Check if we're in tenant preview mode
            if (request()->is('t/*')) {
                $segments = request()->segments();
                if (count($segments) >= 2 && $segments[0] === 't') {
                    if ($segments[1] === 'draft' && count($segments) >= 3) {
                        $tenantSlug = $segments[2];
                        $routePrefix = 'tenant.draft.';
                        $isDraft = true;
                    } else {
                        $tenantSlug = $segments[1];
                        $routePrefix = 'tenant.';
                    }
                }
                
                // Preserve URL parameters (website, preview, t, etc.)
                $queryParams = request()->query();
                if (!empty($queryParams)) {
                    $urlParams = '?' . http_build_query($queryParams);
                }
            }
            
            // Build base URL for tenant preview links
            $basePreviewUrl = $tenantSlug ? "/t/draft/{$tenantSlug}" : '';
        ?>
        
        <nav class="sidebar-nav">

            <!-- Dashboard -->
            <div class="nav-item">
                <?php
                    $dashboardUrl = $tenantSlug 
                        ? $basePreviewUrl . "/admin-dashboard" . $urlParams
                        : route('admin.dashboard');
                ?>
                <a href="<?php echo e($dashboardUrl); ?>" class="nav-link <?php if(Route::currentRouteName() === 'admin.dashboard'): ?> active <?php endif; ?>">
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
                            <?php
                                $pendingUrl = $tenantSlug 
                                    ? $basePreviewUrl . "/admin-student-registration/pending" . $urlParams
                                    : route('admin.student.registration.pending');
                                $historyUrl = $tenantSlug 
                                    ? $basePreviewUrl . "/admin-student-registration/history" . $urlParams
                                    : route('admin.student.registration.history');
                                $paymentPendingUrl = $tenantSlug 
                                    ? $basePreviewUrl . "/admin-student-registration/payment/pending" . $urlParams
                                    : route('admin.student.registration.payment.pending');
                                $paymentHistoryUrl = $tenantSlug 
                                    ? $basePreviewUrl . "/admin-student-registration/payment/history" . $urlParams
                                    : route('admin.student.registration.payment.history');
                            ?>
                            <a href="<?php echo e($pendingUrl); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'admin.student.registration.pending'): ?> active <?php endif; ?>">
                                <i class="bi bi-clock"></i><span>Pending</span>
                            </a>
                            <a href="<?php echo e($historyUrl); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'admin.student.registration.history'): ?> active <?php endif; ?>">
                                <i class="bi bi-archive"></i><span>History</span>
                            </a>
                            <a href="<?php echo e($paymentPendingUrl); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'admin.student.registration.payment.pending'): ?> active <?php endif; ?>">
                                <i class="bi bi-credit-card"></i><span>Payment Pending</span>
                            </a>
                            <a href="<?php echo e($paymentHistoryUrl); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'admin.student.registration.payment.history'): ?> active <?php endif; ?>">
                                <i class="bi bi-receipt"></i><span>Payment History</span>
                            </a>
                        <?php endif; ?>
                        <?php if($isAdmin || ($isDirector && $directorFeatures['manage_batches'])): ?>
                            <?php
                                $batchesUrl = $tenantSlug 
                                    ? $basePreviewUrl . "/admin/batches" . $urlParams
                                    : route('admin.batches.index');
                            ?>
                            <a href="<?php echo e($batchesUrl); ?>" class="submenu-link <?php if(str_starts_with(Route::currentRouteName(), 'admin.batches')): ?> active <?php endif; ?>">
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
                            <?php
                                $studentsUrl = $tenantSlug 
                                    ? $basePreviewUrl . "/admin/students" . $urlParams
                                    : route('admin.students.index');
                            ?>
                            <a href="<?php echo e($studentsUrl); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'admin.students.index'): ?> active <?php endif; ?>">
                                <i class="bi bi-person"></i><span>Students</span>
                            </a>
                        <?php endif; ?>
                        <?php if($isAdmin): ?>
                            <a href="<?php echo e(route('admin.directors.index')); ?>" class="submenu-link <?php if(str_starts_with(Route::currentRouteName(), 'admin.directors')): ?> active <?php endif; ?>">
                                <i class="bi bi-person-badge"></i><span>Directors</span>
                            </a>
                        <?php endif; ?>
                        <?php if($isAdmin || ($isDirector && $directorFeatures['manage_professors'])): ?>
                            <?php
                                $professorsUrl = $tenantSlug 
                                    ? $basePreviewUrl . "/admin/professors" . $urlParams
                                    : route('admin.professors.index');
                            ?>
                            <a href="<?php echo e($professorsUrl); ?>" class="submenu-link <?php if(str_starts_with(Route::currentRouteName(), 'admin.professors')): ?> active <?php endif; ?>">
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
                            <?php
                                $programsUrl = $tenantSlug 
                                    ? $basePreviewUrl . "/admin/programs" . $urlParams
                                    : route('admin.programs.index');
                            ?>
                            <a href="<?php echo e($programsUrl); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'admin.programs.index'): ?> active <?php endif; ?>">
                                <i class="bi bi-collection"></i><span>Manage Programs</span>
                            </a>
                        <?php endif; ?>
                        <?php if($isAdmin || ($isDirector && $directorFeatures['manage_modules'])): ?>
                            <?php
                                $modulesUrl = $tenantSlug 
                                    ? $basePreviewUrl . "/admin/modules" . $urlParams
                                    : route('admin.modules.index');
                            ?>
                            <a href="<?php echo e($modulesUrl); ?>" class="submenu-link <?php if(str_starts_with(Route::currentRouteName(), 'admin.modules')): ?> active <?php endif; ?>">
                                <i class="bi bi-puzzle"></i><span>Manage Modules</span>
                            </a>
                        <?php endif; ?>
                        <?php if($isAdmin || ($isDirector && $directorFeatures['manage_batches'])): ?>
                            <?php
                                $batchesUrl = $tenantSlug 
                                    ? $basePreviewUrl . "/admin/batches" . $urlParams
                                    : route('admin.batches.index');
                            ?>
                            <a href="<?php echo e($batchesUrl); ?>" class="submenu-link <?php if(str_starts_with(Route::currentRouteName(), 'admin.batches')): ?> active <?php endif; ?>">
                                <i class="bi bi-people"></i><span>Manage Batches</span>
                            </a>
                        <?php endif; ?>
                        <?php if($isAdmin): ?>
                            <?php
                                $packagesUrl = $tenantSlug 
                                    ? $basePreviewUrl . "/admin/packages" . $urlParams
                                    : route('admin.packages.index');
                            ?>
                            <a href="<?php echo e($packagesUrl); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'admin.packages.index'): ?> active <?php endif; ?>">
                                <i class="bi bi-box-seam"></i><span>Packages</span>
                            </a>
                        <?php endif; ?>
                        <?php if($isAdmin): ?>
                            <?php
                                $certificatesUrl = $tenantSlug 
                                    ? $basePreviewUrl . "/admin/certificates" . $urlParams
                                    : route('admin.certificates');
                            ?>
                            <a href="<?php echo e($certificatesUrl); ?>" class="submenu-link <?php if(Route::currentRouteName() === 'admin.certificates'): ?> active <?php endif; ?>">
                                <i class="bi bi-award"></i><span>Certificates</span>
                            </a>
                        <?php endif; ?>
                        <?php if($isAdmin): ?>
                            <?php
                                $archivedUrl = $tenantSlug 
                                    ? $basePreviewUrl . "/admin/archived" . $urlParams
                                    : route('admin.archived', [], false);
                            ?>
                            <a href="<?php echo e($archivedUrl); ?>" class="submenu-link">
                                <i class="bi bi-archive"></i><span>Archived Content</span>
                            </a>
                        <?php endif; ?>
                        <?php if($isAdmin): ?>
                            <?php
                                $submissionsUrl = $tenantSlug 
                                    ? $basePreviewUrl . "/admin/submissions" . $urlParams
                                    : route('admin.submissions');
                            ?>
                            <a href="<?php echo e($submissionsUrl); ?>" class="submenu-link <?php if(str_starts_with(Route::currentRouteName(), 'admin.submissions')): ?> active <?php endif; ?>">
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
                <?php
                    $analyticsUrl = $tenantSlug 
                        ? $basePreviewUrl . "/admin/analytics" . $urlParams
                        : route('admin.analytics.index');
                ?>
                <a href="<?php echo e($analyticsUrl); ?>" class="nav-link <?php if(Route::currentRouteName() === 'admin.analytics.index'): ?> active <?php endif; ?>">
                    <i class="bi bi-graph-up"></i><span>Analytics</span>
                </a>
            </div>
            <?php endif; ?>

            <!-- FAQ Management -->
            <div class="nav-item">
                <?php
                    $faqUrl = $tenantSlug 
                        ? $basePreviewUrl . "/admin/faq" . $urlParams
                        : route('admin.faq.index');
                ?>
                <a href="<?php echo e($faqUrl); ?>" class="nav-link <?php if(Route::currentRouteName() === 'admin.faq.index'): ?> active <?php endif; ?>">
                    <i class="bi bi-question-circle"></i><span>FAQ Management</span>
                </a>
            </div>

            <!-- Announcements -->
            <div class="nav-item">
                <?php
                    $announcementsUrl = $tenantSlug 
                        ? $basePreviewUrl . "/admin/announcements" . $urlParams
                        : route('admin.announcements.index');
                ?>
                <a href="<?php echo e($announcementsUrl); ?>" class="nav-link <?php if(str_starts_with(Route::currentRouteName(), 'admin.announcements')): ?> active <?php endif; ?>">
                    <i class="bi bi-broadcast"></i><span>Announcements</span>
                </a>
            </div>

            <!-- Settings -->
            <?php if($isAdmin): ?>
            <div class="nav-item">
                <?php
                    $settingsUrl = $tenantSlug 
                        ? $basePreviewUrl . "/admin/settings" . $urlParams
                        : route('admin.settings.index');
                ?>
                <a href="<?php echo e($settingsUrl); ?>" class="nav-link <?php if(Route::currentRouteName() === 'admin.settings.index'): ?> active <?php endif; ?>">
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
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/admin/admin-layouts/admin-sidebar.blade.php ENDPATH**/ ?>