<!-- Professor Sidebar Component -->
<aside class="modern-sidebar" id="modernSidebar">
    <!-- Sidebar Header with Brand and Toggle -->
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img src="{{ asset('images/ARTC_logo.png') }}" alt="A.R.T.C" class="brand-logo" style="height: 32px; width: auto; margin-right: 8px;">
            <div class="brand-content">
                <span class="brand-title">A.R.T.C</span>
                <span class="brand-subtitle">Professor Portal</span>
            </div>
        </div>
        <button class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Toggle Sidebar">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>
    
    <div class="sidebar-content" style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">
        <!-- User Profile Section -->
        <div class="user-profile">
            @php
                $professor = \App\Models\Professor::where('professor_id', session('user_id'))->first();
                $profilePhoto = $professor && $professor->profile_photo ? $professor->profile_photo : null;
            @endphp
            
            <div class="user-info">
                <div class="user-avatar">
                    @if($profilePhoto)
                        <img src="{{ asset('storage/profile-photos/' . $profilePhoto) }}" 
                             alt="Profile" 
                             style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                    @else
                        {{ substr(session('user_firstname', 'P'), 0, 1) }}{{ substr(session('user_lastname', 'R'), 0, 1) }}
                    @endif
                </div>
                <div class="profile-info">
                    <div class="profile-name">{{ $user->name ?? session('user_name', 'Professor') }}</div>
                    <div class="profile-role">Professor</div>
                </div>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <!-- Dashboard -->
            <div class="nav-item">
                <a href="{{ route('professor.dashboard') }}" class="nav-link @if(Route::currentRouteName() === 'professor.dashboard') active @endif">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <!-- Module Management -->
            @if(!empty($moduleManagementEnabled) && $moduleManagementEnabled)
            <div class="nav-item">
                <a href="{{ route('professor.modules.index') }}" class="nav-link @if(Route::currentRouteName() === 'professor.modules.index') active @endif">
                    <i class="bi bi-journals"></i>
                    <span>Module Management</span>
                </a>
            </div>
            @endif
            <!-- Meetings -->
            <div class="nav-item">
                <a href="{{ route('professor.meetings') }}" class="nav-link @if(Route::currentRouteName() === 'professor.meetings') active @endif">
                    <i class="bi bi-calendar-event"></i>
                    <span>Meetings</span>
                </a>
            </div>
            <!-- Announcements -->
            <div class="nav-item">
                <a href="{{ route('professor.announcements.index') }}" class="nav-link @if(str_starts_with(Route::currentRouteName(), 'professor.announcements')) active @endif">
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
                <div class="collapse @if(str_starts_with(Route::currentRouteName(), 'professor.students')) show @endif" id="studentsMenu">
                    <div class="submenu">
                        <a href="{{ route('professor.students.index') }}" class="submenu-link @if(Route::currentRouteName() === 'professor.students.index') active @endif">
                            <i class="bi bi-person-lines-fill"></i>
                            <span>All Students</span>
                        </a>
                        <a href="{{ route('professor.students.batches') }}" class="submenu-link @if(Route::currentRouteName() === 'professor.students.batches') active @endif">
                            <i class="bi bi-collection"></i>
                            <span>My Batches</span>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Programs -->
            <div class="nav-item">
                <a href="{{ route('professor.programs') }}" class="nav-link @if(Route::currentRouteName() === 'professor.programs') active @endif">
                    <i class="bi bi-book"></i>
                    <span>My Programs</span>
                </a>
            </div>
            <!-- Assignments Dropdown -->
            <div class="nav-item">
                <a class="nav-link dropdown-toggle" href="#assignmentsMenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="assignmentsMenu">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Assignments</span>
                    <i class="bi bi-chevron-down dropdown-arrow"></i>
                </a>
                <div class="collapse @if(str_starts_with(Route::currentRouteName(), 'professor.assignments') || str_starts_with(Route::currentRouteName(), 'professor.submissions')) show @endif" id="assignmentsMenu">
                    <div class="submenu">
                        <a href="{{ route('professor.grading') }}" class="submenu-link @if(Route::currentRouteName() === 'professor.assignments.index') active @endif">
                            <i class="bi bi-list-task"></i>
                            <span>View All</span>
                        </a>
                        <a href="{{ route('professor.assignments.create') }}" class="submenu-link @if(Route::currentRouteName() === 'professor.assignments.create') active @endif">
                            <i class="bi bi-plus-circle"></i>
                            <span>Create New</span>
                        </a>
                        <a href="{{ route('professor.submissions.index') }}" class="submenu-link @if(str_starts_with(Route::currentRouteName(), 'professor.submissions')) active @endif">
                            <i class="bi bi-file-earmark-text"></i>
                            <span>Student Submissions</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Settings -->
            <div class="nav-item">
                <a href="{{ route('professor.settings') }}" class="nav-link @if(Route::currentRouteName() === 'professor.settings') active @endif">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </div>
            
            <!-- Logout -->
            <div class="nav-item">
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
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