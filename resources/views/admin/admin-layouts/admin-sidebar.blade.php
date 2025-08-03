<!-- Admin Sidebar Component -->
<aside class="modern-sidebar" id="modernSidebar">
    <!-- Sidebar Header with Brand and Toggle -->
    <div class="sidebar-header">
        <!-- Profile Information in Header -->
        @php
            // Get admin/director info
            $adminUser = null;
            $adminName = 'Admin';
            $adminRole = 'Admin';
            
            if (session('user_type') === 'director') {
                $adminUser = \App\Models\Director::where('directors_id', session('user_id'))->first();
                $adminName = $adminUser ? $adminUser->directors_name : session('user_name', 'Director');
                $adminRole = 'Director';
            } elseif (session('user_type') === 'admin') {
                // For admin users, use session data
                $adminName = session('user_name', 'Admin');
                $adminRole = 'Admin';
            }
            
            $profilePhoto = $adminUser && $adminUser->profile_photo ? $adminUser->profile_photo : null;
        @endphp
        
        <div class="header-profile">
            @if($profilePhoto)
                <img src="{{ asset('storage/' . $profilePhoto) }}" 
                     alt="Profile" 
                     class="header-profile-avatar">
            @else
                <div class="header-profile-avatar" style="background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px;">
                    {{ substr($adminName, 0, 1) }}{{ substr(explode(' ', $adminName)[1] ?? '', 0, 1) }}
                </div>
            @endif
            <div class="header-profile-info">
                <p class="header-profile-name">{{ $adminName }}</p>
                <p class="header-profile-role">{{ $adminRole }}</p>
            </div>
        </div>
    </div>
    
    <div class="sidebar-content" style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">
        
        <nav class="sidebar-nav">
            <!-- Dashboard -->
            <div class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link @if(Route::currentRouteName() === 'admin.dashboard') active @endif">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            
            <!-- Certificate Management -->
            <div class="nav-item">
                <a href="{{ route('admin.certificates') }}" class="nav-link @if(Route::currentRouteName() === 'admin.certificates') active @endif">
                    <i class="bi bi-award"></i>
                    <span>Certificate Management</span>
                </a>
            </div>

            <!-- Registration Management -->
            @php
                $registrationMenuVisible = $isAdmin || 
                    ($isDirector && ($directorFeatures['manage_enrollments'] || $directorFeatures['manage_batches']));
            @endphp
            @if($registrationMenuVisible)
            <div class="nav-item">
                <a class="nav-link dropdown-toggle" href="#registrationMenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="registrationMenu">
                    <i class="bi bi-person-plus"></i>
                    <span>Registration</span>
                    <i class="bi bi-chevron-down dropdown-arrow"></i>
                </a>
                <div class="collapse @if(str_starts_with(Route::currentRouteName(), 'admin.student.registration')) show @endif" id="registrationMenu">
                    <div class="submenu">
                        @if($isAdmin || ($isDirector && $directorFeatures['manage_enrollments']))
                        <a href="{{ route('admin.student.registration.pending') }}" class="submenu-link @if(Route::currentRouteName() === 'admin.student.registration.pending') active @endif">
                            <i class="bi bi-clock"></i>
                            <span>Pending</span>
                        </a>
                        <a href="{{ route('admin.student.registration.history') }}" class="submenu-link @if(Route::currentRouteName() === 'admin.student.registration.history') active @endif">
                            <i class="bi bi-archive"></i>
                            <span>History</span>
                        </a>
                        <a href="{{ route('admin.student.registration.payment.pending') }}" class="submenu-link @if(Route::currentRouteName() === 'admin.student.registration.payment.pending') active @endif">
                            <i class="bi bi-credit-card"></i>
                            <span>Payment Pending</span>
                        </a>
                        <a href="{{ route('admin.student.registration.payment.history') }}" class="submenu-link @if(Route::currentRouteName() === 'admin.student.registration.payment.history') active @endif">
                            <i class="bi bi-receipt"></i>
                            <span>Payment History</span>
                        </a>
                        @endif
                        @if($isAdmin || ($isDirector && $directorFeatures['manage_batches']))
                        <a href="{{ route('admin.batches.index') }}" class="submenu-link @if(str_starts_with(Route::currentRouteName(), 'admin.batches')) active @endif">
                            <i class="bi bi-people"></i>
                            <span>Batch Enroll</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Account Management -->
            @php
                $accountsMenuVisible = $isAdmin || 
                    ($isDirector && ($directorFeatures['view_students'] || $directorFeatures['manage_professors']));
            @endphp
            @if($accountsMenuVisible)
            <div class="nav-item">
                <a class="nav-link dropdown-toggle" href="#accountsMenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="accountsMenu">
                    <i class="bi bi-people"></i>
                    <span>Accounts</span>
                    <i class="bi bi-chevron-down dropdown-arrow"></i>
                </a>
                <div class="collapse" id="accountsMenu">
                    <div class="submenu">
                        @if($isAdmin || ($isDirector && $directorFeatures['view_students']))
                        <a href="{{ route('admin.students.index') }}" class="submenu-link @if(Route::currentRouteName() === 'admin.students.index') active @endif">
                            <i class="bi bi-person"></i>
                            <span>Students</span>
                        </a>
                        @endif
                        @if($isAdmin)
                        <a href="{{ route('admin.directors.index') }}" class="submenu-link @if(str_starts_with(Route::currentRouteName(), 'admin.directors')) active @endif">
                            <i class="bi bi-person-badge"></i>
                            <span>Directors</span>
                        </a>
                        @endif
                        @if($isAdmin || ($isDirector && $directorFeatures['manage_professors']))
                        <a href="{{ route('admin.professors.index') }}" class="submenu-link @if(str_starts_with(Route::currentRouteName(), 'admin.professors')) active @endif">
                            <i class="bi bi-person-workspace"></i>
                            <span>Professors</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Programs & Packages -->
            @php
                $programsMenuVisible = $isAdmin || 
                    ($isDirector && ($directorFeatures['manage_programs'] || $directorFeatures['manage_modules'] || $directorFeatures['manage_batches']));
            @endphp
            @if($programsMenuVisible)
            <div class="nav-item">
                <a class="nav-link dropdown-toggle" href="#programsMenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="programsMenu">
                    <i class="bi bi-mortarboard"></i>
                    <span>Programs</span>
                    <i class="bi bi-chevron-down dropdown-arrow"></i>
                </a>
                <div class="collapse @if(str_starts_with(Route::currentRouteName(), 'admin.programs') || str_starts_with(Route::currentRouteName(), 'admin.modules') || Route::currentRouteName() === 'admin.packages.index') show @endif" id="programsMenu">
                    <div class="submenu">
                        @if($isAdmin || ($isDirector && $directorFeatures['manage_programs']))
                        <a href="{{ route('admin.programs.index') }}" class="submenu-link @if(Route::currentRouteName() === 'admin.programs.index') active @endif">
                            <i class="bi bi-collection"></i>
                            <span>Manage Programs</span>
                        </a>
                        @endif
                        @if($isAdmin || ($isDirector && $directorFeatures['manage_modules']))
                        <a href="{{ route('admin.modules.index') }}" class="submenu-link @if(str_starts_with(Route::currentRouteName(), 'admin.modules')) active @endif">
                            <i class="bi bi-puzzle"></i>
                            <span>Manage Modules</span>
                        </a>
                        @endif
                        @if($isAdmin || ($isDirector && $directorFeatures['manage_batches']))
                        <a href="{{ route('admin.batches.index') }}" class="submenu-link @if(str_starts_with(Route::currentRouteName(), 'admin.batches')) active @endif">
                            <i class="bi bi-people"></i>
                            <span>Manage Batches</span>
                        </a>
                        @endif
                        @if($isAdmin)
                        <a href="{{ route('admin.packages.index') }}" class="submenu-link @if(Route::currentRouteName() === 'admin.packages.index') active @endif">
                            <i class="bi bi-box-seam"></i>
                            <span>Packages</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Analytics -->
            @if($isAdmin || ($isDirector && $directorFeatures['view_analytics']))
            <div class="nav-item">
                <a href="{{ route('admin.analytics.index') }}" class="nav-link @if(Route::currentRouteName() === 'admin.analytics.index') active @endif">
                    <i class="bi bi-graph-up"></i>
                    <span>Analytics</span>
                </a>
            </div>
            @endif
            
            <!-- FAQ Management -->
            <div class="nav-item">
                <a href="{{ route('admin.faq.index') }}" class="nav-link @if(Route::currentRouteName() === 'admin.faq.index') active @endif">
                    <i class="bi bi-question-circle"></i>
                    <span>FAQ Management</span>
                </a>
            </div>

            <!-- Announcements -->
            <div class="nav-item">
                <a href="{{ route('admin.announcements.index') }}" class="nav-link @if(str_starts_with(Route::currentRouteName(), 'admin.announcements')) active @endif">
                    <i class="bi bi-broadcast"></i>
                    <span>Announcements</span>
                </a>
            </div>

            <!-- Settings -->
            @if($isAdmin)
            <div class="nav-item">
                <a href="{{ route('admin.settings.index') }}" class="nav-link @if(Route::currentRouteName() === 'admin.settings.index') active @endif">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </div>
            @endif
            
            <!-- Logout -->
            <div class="nav-item">
                <form action="{{ route('student.logout') }}" method="POST" class="d-inline">
                    @csrf
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
</button>
