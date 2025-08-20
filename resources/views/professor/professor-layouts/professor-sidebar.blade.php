<!-- Professor Sidebar Component -->
<aside class="modern-sidebar" id="modernSidebar">
    <!-- Sidebar Header with Brand and Toggle -->
    <div class="sidebar-header">

        
        <!-- Profile Information in Header -->
        @php
            $professor = \App\Models\Professor::where('professor_id', session('professor_id'))->first();
            $profilePhoto = $professor && $professor->profile_photo ? $professor->profile_photo : null;
            $professorName = $professor ? $professor->professor_name : session('professor_name', 'Professor');
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
            
            <!-- Submissions/Grading -->
            <div class="nav-item">
                <a href="{{ route('professor.submissions.index') }}" class="nav-link @if(str_starts_with(Route::currentRouteName(), 'professor.submissions')) active @endif">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Submissions</span>
                </a>
            </div>
            
            <!-- Profile -->
            <div class="nav-item">
                <a href="{{ route('professor.profile') }}" class="nav-link @if(Route::currentRouteName() === 'professor.profile') active @endif">
                    <i class="bi bi-person-circle"></i>
                    <span>Profile</span>
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

<script>
// Load and apply custom sidebar colors for professor
document.addEventListener('DOMContentLoaded', function() {
    loadProfessorSidebarCustomization();
});

function loadProfessorSidebarCustomization() {
    fetch('/smartprep/api/sidebar-settings?role=professor')
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