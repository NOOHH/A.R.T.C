<!-- Professional Student Sidebar -->
<style>
.professional-sidebar.collapsed .sidebar-toggle-btn {
  display: none;
}

 </style>
<aside class="professional-sidebar" id="studentSidebar">
  <!-- Sidebar Header -->


  <!-- User Profile Section -->
  <div class="sidebar-profile">
    @php
      // Check if this is preview mode
      $isPreview = request()->has('preview') || request()->query('preview') === 'true';
      
      if ($isPreview) {
        // Use mock data for preview mode
        $profilePhoto = null;
      } else {
        // Only query database if not in preview mode
        try {
          $student = \App\Models\Student::where('user_id', session('user_id'))->first();
          $profilePhoto = $student && $student->profile_photo ? $student->profile_photo : null;
        } catch (\Exception $e) {
          // If there's an error (e.g., table doesn't exist), use null
          $profilePhoto = null;
        }
      }
    @endphp
    
    <div class="profile-avatar">
      @if($profilePhoto)
        <img src="{{ asset('storage/profile-photos/' . $profilePhoto) }}" 
             alt="Profile" 
             class="avatar-image">
      @else
        <div class="avatar-placeholder">
          {{ substr(session('user_firstname', 'S'), 0, 1) }}{{ substr(session('user_lastname', 'T'), 0, 1) }}
        </div>
      @endif
    </div>
    <div class="profile-info">
      <div class="profile-name">{{ session('user_firstname') }} {{ session('user_lastname') }}</div>
      <div class="profile-role">Student</div>
    </div>
       <button class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Toggle Sidebar">
      <i class="bi bi-chevron-left"></i>
    </button>
  </div>

  @php
    // Detect tenant context for proper routing
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
    $dashboardRoute = $tenantSlug ? $routePrefix . 'student.dashboard' : 'student.dashboard';
    $calendarRoute = $tenantSlug ? $routePrefix . 'student.calendar' : 'student.calendar';
    $coursesRoute = $tenantSlug ? $routePrefix . 'student.enrolled-courses' : 'student.enrolled-courses';
    $meetingsRoute = $tenantSlug ? $routePrefix . 'student.meetings' : 'student.meetings';
    $settingsRoute = $tenantSlug ? $routePrefix . 'student.settings' : 'student.settings';
    
    // Route parameters for tenant routes
    $routeParams = $tenantSlug ? ['tenant' => $tenantSlug] : [];
  @endphp

  <!-- Navigation Menu -->
  <nav class="sidebar-navigation">
    <div class="nav-section">
      <div class="nav-section-title">Main</div>
      
      <!-- Dashboard -->
      <a href="{{ route($dashboardRoute, $routeParams) }}" 
         class="nav-item @if(str_contains(Route::currentRouteName(), 'student.dashboard')) active @endif">
        <div class="nav-icon">
          <i class="bi bi-speedometer2"></i>
        </div>
        <span class="nav-text">Dashboard</span>
      </a>

      <!-- Calendar -->
      <a href="{{ route($calendarRoute, $routeParams) }}" 
         class="nav-item @if(str_contains(Route::currentRouteName(), 'student.calendar')) active @endif">
        <div class="nav-icon">
          <i class="bi bi-calendar-week"></i>
        </div>
        <span class="nav-text">Calendar</span>
      </a>

      <!-- Enrolled Courses -->
      <a href="{{ route($coursesRoute, $routeParams) }}" 
         class="nav-item @if(str_contains(Route::currentRouteName(), 'student.enrolled-courses')) active @endif">
        <div class="nav-icon">
          <i class="bi bi-journal-bookmark"></i>
        </div>
        <span class="nav-text">My Courses</span>
      </a>

      <!-- Meetings -->
      <a href="{{ route($meetingsRoute, $routeParams) }}" 
         class="nav-item @if(str_contains(Route::currentRouteName(), 'student.meetings')) active @endif">
        <div class="nav-icon">
          <i class="bi bi-camera-video"></i>
        </div>
        <span class="nav-text">Meetings</span>
      </a>
    </div>

    <!-- Programs Section -->
    @if(isset($studentPrograms) && !empty($studentPrograms))
    <div class="nav-section">
      <div class="nav-section-title">My Programs</div>
      
      @foreach($studentPrograms as $program)
        @php
          $courseRoute = $tenantSlug ? $routePrefix . 'student.course' : 'student.course';
          $courseParams = $tenantSlug ? ['tenant' => $tenantSlug, 'courseId' => $program['program_id']] : ['courseId' => $program['program_id']];
        @endphp
        <a href="{{ route($courseRoute, $courseParams) }}" 
           class="nav-item program-item @if(request()->route('courseId')==$program['program_id']) active @endif">
          <div class="nav-icon">
            <i class="bi bi-book"></i>
          </div>
          <div class="nav-text">
            <div class="program-name">{{ $program['program_name'] }}</div>
            <small class="program-package">{{ $program['package_name'] }}</small>
          </div>
        </a>
      @endforeach
    </div>
    @endif

    <!-- Account Section -->
    <div class="nav-section">
      <div class="nav-section-title">Account</div>
      
      <!-- Settings -->
      <a href="{{ route($settingsRoute, $routeParams) }}" 
         class="nav-item @if(str_contains(Route::currentRouteName(), 'student.settings')) active @endif">
        <div class="nav-icon">
          <i class="bi bi-gear"></i>
        </div>
        <span class="nav-text">Settings</span>
      </a>

      <!-- Logout - Only show in non-preview mode -->
      @if(!session('preview_mode') && !$tenantSlug)
      <a href="#" class="nav-item logout-item" onclick="document.getElementById('logout-form').submit();">
        <div class="nav-icon">
          <i class="bi bi-box-arrow-right"></i>
        </div>
        <span class="nav-text">Logout</span>
      </a>
      @endif
    </div>
  </nav>

  <!-- Sidebar Footer -->
  <div class="sidebar-footer">
    <div class="footer-text">
      <small>ARTC © {{ date('Y') }}</small>
    </div>
  </div>
</aside>

<!-- Sidebar Backdrop for Mobile -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- Mobile Toggle Button -->
<button class="mobile-sidebar-toggle" id="mobileSidebarToggle" title="Toggle Sidebar">
  <i class="bi bi-list"></i>
</button>

<script>
// Load and apply custom sidebar colors
document.addEventListener('DOMContentLoaded', function() {
    loadSidebarCustomization();
});

function loadSidebarCustomization() {
    // Get website parameter from URL if present
    const urlParams = new URLSearchParams(window.location.search);
    const websiteId = urlParams.get('website');
    
    let apiUrl = '/smartprep/api/sidebar-settings?role=student';
    if (websiteId) {
        apiUrl += '&website=' + websiteId;
    }
    
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.colors) {
                applySidebarColors(data.colors);
            }
        })
        .catch(error => {
            console.log('No custom sidebar settings found, using defaults');
        });
}

function applySidebarColors(settings) {
    const sidebar = document.getElementById('studentSidebar');
    if (sidebar && settings) {
        // Apply ONLY student-specific CSS variables to avoid conflicts with professor panel
        if (settings.primary_color) {
            sidebar.style.setProperty('--student-sidebar-bg', settings.primary_color);
            document.documentElement.style.setProperty('--student-sidebar-bg', settings.primary_color);
        }
        if (settings.secondary_color) {
            sidebar.style.setProperty('--student-sidebar-hover', settings.secondary_color);
            sidebar.style.setProperty('--student-sidebar-border', settings.secondary_color);
            document.documentElement.style.setProperty('--student-sidebar-hover', settings.secondary_color);
            document.documentElement.style.setProperty('--student-sidebar-border', settings.secondary_color);
        }
        if (settings.accent_color) {
            sidebar.style.setProperty('--student-sidebar-active', settings.accent_color);
            document.documentElement.style.setProperty('--student-sidebar-active', settings.accent_color);
        }
        if (settings.text_color) {
            sidebar.style.setProperty('--student-sidebar-text', settings.text_color);
            document.documentElement.style.setProperty('--student-sidebar-text', settings.text_color);
        }
        if (settings.hover_color) {
            sidebar.style.setProperty('--student-sidebar-hover-bg', settings.hover_color);
            document.documentElement.style.setProperty('--student-sidebar-hover-bg', settings.hover_color);
        }
        
        console.log('Student sidebar colors applied:', settings);
    }
}
</script>
