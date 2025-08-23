<!-- Professor Sidebar Component -->
<aside class="modern-sidebar" id="modernSidebar">
    <!-- Sidebar Header with Brand and Toggle -->
    <div class="sidebar-header">

        
        <!-- Profile Information in Header -->
        @php
            // Check if this is preview mode
            $isPreview = request()->has('preview') || request()->query('preview') === 'true';
            
            if ($isPreview) {
                // Use mock data for preview mode
                $profilePhoto = null;
                $professorName = session('professor_name', 'John Professor');
            } else {
                // Only query database if not in preview mode
                try {
                    $professor = \App\Models\Professor::where('professor_id', session('professor_id'))->first();
                    $profilePhoto = $professor && $professor->profile_photo ? $professor->profile_photo : null;
                    $professorName = $professor ? $professor->professor_name : session('professor_name', 'Professor');
                } catch (\Exception $e) {
                    // If there's an error (e.g., table doesn't exist), use defaults
                    $profilePhoto = null;
                    $professorName = session('professor_name', 'Professor');
                }
            }
        @endphp
        
        <div class="header-profile">
            @if($profilePhoto)
                <img src="{{ asset('storage/' . $profilePhoto) }}" 
                     alt="Profile" 
                     class="header-profile-avatar">
            @else
                <div class="header-profile-avatar" style="background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px;">
                    {{ substr($professorName, 0, 1) }}{{ substr(explode(' ', $professorName)[1] ?? '', 0, 1) }}
                </div>
            @endif
            <div class="header-profile-info">
                <p class="header-profile-name">{{ $professorName }}</p>
                <p class="header-profile-role">Professor</p>
            </div>
        </div>
    </div>
    
    <div class="sidebar-content" style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">
        
        @php
            // Detect tenant context for proper routing (similar to student sidebar)
            $tenantSlug = null;
            $routePrefix = '';
            $isDraft = false;
            
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
            }
            
            // Determine route names based on context
            $dashboardRoute = $tenantSlug ? $routePrefix . 'professor.dashboard' : 'professor.dashboard';
            $modulesRoute = $tenantSlug ? $routePrefix . 'professor.modules' : 'professor.modules.index';
            $meetingsRoute = $tenantSlug ? $routePrefix . 'professor.meetings' : 'professor.meetings';
            $announcementsRoute = $tenantSlug ? $routePrefix . 'professor.announcements' : 'professor.announcements.index';
            $studentsRoute = $tenantSlug ? $routePrefix . 'professor.students' : 'professor.students.index';
            $studentsBatchesRoute = $tenantSlug ? $routePrefix . 'professor.students' : 'professor.students.batches';
            $programsRoute = $tenantSlug ? $routePrefix . 'professor.programs' : 'professor.programs';
            $submissionsRoute = $tenantSlug ? $routePrefix . 'professor.grading' : 'professor.submissions.index'; // Grading for preview, submissions for real
            $profileRoute = $tenantSlug ? $routePrefix . 'professor.profile' : 'professor.profile';
            $gradingRoute = $tenantSlug ? $routePrefix . 'professor.grading' : 'professor.grading';
            $settingsRoute = $tenantSlug ? $routePrefix . 'professor.settings' : 'professor.settings';
            
            // Route parameters for tenant routes
            $routeParams = $tenantSlug ? ['tenant' => $tenantSlug] : [];
        @endphp
        
        <nav class="sidebar-nav">
            <!-- Dashboard -->
            <div class="nav-item">
                <a href="{{ route($dashboardRoute, $routeParams) }}" class="nav-link @if(str_contains(Route::currentRouteName() ?? '', 'professor.dashboard')) active @endif">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <!-- Module Management -->
            @if(!empty($moduleManagementEnabled) && $moduleManagementEnabled)
            <div class="nav-item">
                <a href="{{ route($modulesRoute, $routeParams) }}" class="nav-link @if(str_contains(Route::currentRouteName() ?? '', 'professor.modules')) active @endif">
                    <i class="bi bi-journals"></i>
                    <span>Module Management</span>
                </a>
            </div>
            @endif
            <!-- Meetings -->
            <div class="nav-item">
                <a href="{{ route($meetingsRoute, $routeParams) }}" class="nav-link @if(str_contains(Route::currentRouteName() ?? '', 'professor.meetings')) active @endif">
                    <i class="bi bi-calendar-event"></i>
                    <span>Meetings</span>
                </a>
            </div>
            <!-- Announcements -->
            <div class="nav-item">
                <a href="{{ route($announcementsRoute, $routeParams) }}" class="nav-link @if(str_contains(Route::currentRouteName() ?? '', 'professor.announcements')) active @endif">
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
                <div class="collapse @if(str_contains(Route::currentRouteName() ?? '', 'professor.students')) show @endif" id="studentsMenu">
                    <div class="submenu">
                        <a href="{{ route($studentsRoute, $routeParams) }}" class="submenu-link @if(str_contains(Route::currentRouteName() ?? '', 'professor.students')) active @endif">
                            <i class="bi bi-person-lines-fill"></i>
                            <span>All Students</span>
                        </a>
                        @if(!$tenantSlug)
                        <a href="{{ route($studentsBatchesRoute, $routeParams) }}" class="submenu-link @if(str_contains(Route::currentRouteName() ?? '', 'professor.students.batches')) active @endif">
                            <i class="bi bi-collection"></i>
                            <span>My Batches</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Programs -->
            <div class="nav-item">
                <a href="{{ route($programsRoute, $routeParams) }}" class="nav-link @if(str_contains(Route::currentRouteName() ?? '', 'professor.programs')) active @endif">
                    <i class="bi bi-book"></i>
                    <span>My Programs</span>
                </a>
            </div>
            
            <!-- Submissions/Grading -->
            <div class="nav-item">
                <a href="{{ route($submissionsRoute, $routeParams) }}" class="nav-link @if(str_contains(Route::currentRouteName() ?? '', 'professor.submissions') || str_contains(Route::currentRouteName() ?? '', 'professor.grading')) active @endif">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>{{ $tenantSlug ? 'Grading' : 'Submissions' }}</span>
                </a>
            </div>
            
            <!-- Profile -->
            <div class="nav-item">
                <a href="{{ route($profileRoute, $routeParams) }}" class="nav-link @if(str_contains(Route::currentRouteName() ?? '', 'professor.profile')) active @endif">
                    <i class="bi bi-person-circle"></i>
                    <span>Profile</span>
                </a>
            </div>
            
            <!-- Settings (only show in preview mode) -->
            @if($tenantSlug)
            <div class="nav-item">
                <a href="{{ route($settingsRoute, $routeParams) }}" class="nav-link @if(str_contains(Route::currentRouteName() ?? '', 'professor.settings')) active @endif">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </div>
            @endif
            
            <!-- Logout -->
            <div class="nav-item">
                @if($tenantSlug)
                    <!-- In preview mode, show a back/exit button instead of logout -->
                    <a href="javascript:history.back()" class="nav-link logout-btn">
                        <i class="bi bi-arrow-left"></i>
                        <span>Back to Customization</span>
                    </a>
                @else
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link logout-btn" onclick="return confirm('Are you sure you want to logout?')">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                @endif
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

<script>
// Load and apply custom sidebar colors for professor
document.addEventListener('DOMContentLoaded', function() {
    loadProfessorSidebarCustomization();
});

function loadProfessorSidebarCustomization() {
    fetch('/smartprep/api/sidebar-settings')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.colors) {
                applyProfessorSidebarColors(data.colors);
            }
        })
        .catch(error => {
            console.log('No custom professor sidebar settings found, using defaults');
        });
}

function applyProfessorSidebarColors(settings) {
    const sidebar = document.getElementById('modernSidebar');
    if (sidebar && settings) {
        // Apply custom CSS properties for professor sidebar
        if (settings.primary_color) {
            sidebar.style.setProperty('--sidebar-bg', settings.primary_color);
            document.documentElement.style.setProperty('--sidebar-bg', settings.primary_color);
        }
        if (settings.secondary_color) {
            sidebar.style.setProperty('--sidebar-hover', settings.secondary_color);
            sidebar.style.setProperty('--sidebar-border', settings.secondary_color);
            document.documentElement.style.setProperty('--sidebar-hover', settings.secondary_color);
            document.documentElement.style.setProperty('--sidebar-border', settings.secondary_color);
        }
        if (settings.accent_color) {
            sidebar.style.setProperty('--sidebar-active', settings.accent_color);
            document.documentElement.style.setProperty('--sidebar-active', settings.accent_color);
        }
        if (settings.text_color) {
            sidebar.style.setProperty('--sidebar-text', settings.text_color);
            document.documentElement.style.setProperty('--sidebar-text', settings.text_color);
        }
        if (settings.hover_color) {
            sidebar.style.setProperty('--sidebar-hover-bg', settings.hover_color);
            document.documentElement.style.setProperty('--sidebar-hover-bg', settings.hover_color);
        }

        console.log('Professor sidebar colors applied:', settings);
    }
}
</script>