{{-- resources/views/student/student-dashboard/student-dashboard-layout.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Student Dashboard')</title>

  @php
    // Force student authentication context
    $user = null;
    if (session('user_id') && session('user_role') === 'student') {
      $user = (object)[
        'id'   => session('user_id'),
        'name' => session('user_name') ?? session('user_firstname').' '.session('user_lastname'),
        'role' => 'student',
        'email'=> session('user_email'),
      ];
    }
    if (! $user) {
      session()->flush();
      redirect()->route('login')->send();
    }
  @endphp

  <!-- Global JS vars -->
  <script>
    window.myId            = {{ $user->id }};
    window.myName          = @json($user->name);
    window.isAuthenticated = true;
    window.userRole        = @json($user->role);
    window.csrfToken       = @json(csrf_token());
    console.log('Globals:', { myId, myName, isAuthenticated, userRole });
  </script>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="{{ asset('css/student/student-dashboard-layout.css') }}" rel="stylesheet">
  <link href="{{ asset('css/student/student-sidebar-professional.css') }}" rel="stylesheet">
  <link href="{{ asset('css/student/student-navbar.css') }}" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

  @yield('head')
  @stack('styles')

  <style>


    /* Global text rendering optimization */
    * {
      text-rendering: optimizeLegibility;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    /* 1) Remove any nested scrollbars up to the main container */
    html, body,
    .student-container,
    .container-fluid,
    .row.g-0,
    .main-content {
      height: auto        !important;
      max-height: none    !important;
      overflow-x: visible !important;
      overflow-y: auto    !important;
    }

    /* 2) Restore your white “card” appearance on the content wrapper */
    .content-wrapper {
      background-color: #fff;
      border-radius: .5rem;
      box-shadow: 0 .125rem .25rem rgba(0,0,0,0.075);
      overflow: visible !important;
      width: 100%;
      height: 100%;
    }

    /* 3) Sidebar toggle button styling */
    .sidebar-toggle-btn {
      background: none;
      border: none;
      color: #333;
      font-size: 1.2rem;
      padding: 0.5rem;
      border-radius: 0.375rem;
      transition: background-color 0.3s ease;
    }

    .sidebar-toggle-btn:hover {
      background-color: rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>
  <div class="student-container">
    {{-- Professional Sidebar --}}
    @if(!isset($hideSidebar) || !$hideSidebar)
      @include('components.student-sidebar')
    @endif

    {{-- Main Content Area --}}
    <div class="main-content-area">
      {{-- Top Header --}}
      @include('components.student-navbar')

      {{-- Page Content --}}
      <div class="content-wrapper">
        @yield('content')
      </div>
    </div>

    {{-- Floating Course Toggle (Only shown when sidebar is hidden) --}}
    @if(isset($hideSidebar) && $hideSidebar)
      <div class="floating-course-toggle" id="floatingCourseToggle">
        <button class="course-toggle-btn" id="courseToggleBtn" title="Show Navigation">
          <i class="bi bi-list"></i>
        </button>
      </div>
      
      {{-- Compact Course Sidebar (Hidden by default) --}}
      <div class="compact-course-sidebar" id="compactCourseSidebar">
        <div class="compact-sidebar-header">
          <div class="compact-brand">
            <i class="bi bi-mortarboard-fill"></i>
            <span>ARTC</span>
          </div>
          <button class="compact-close-btn" id="compactCloseBtn">
            <i class="bi bi-x"></i>
          </button>
        </div>
        <div class="compact-sidebar-content">
          <div class="compact-nav-section">
            <a href="{{ route('student.dashboard') }}" class="compact-nav-item">
              <i class="bi bi-speedometer2"></i>
              <span>Dashboard</span>
            </a>
            <a href="{{ route('student.calendar') }}" class="compact-nav-item">
              <i class="bi bi-calendar-week"></i>
              <span>Calendar</span>
            </a>
            <a href="{{ route('student.enrolled-courses') }}" class="compact-nav-item">
              <i class="bi bi-journal-bookmark"></i>
              <span>My Courses</span>
            </a>
            <a href="{{ route('student.meetings') }}" class="compact-nav-item">
              <i class="bi bi-camera-video"></i>
              <span>Meetings</span>
            </a>
          </div>
          
          @if(isset($studentPrograms) && !empty($studentPrograms))
          <div class="compact-nav-section">
            <div class="compact-section-title">My Programs</div>
            @foreach($studentPrograms as $program)
              <a href="{{ route('student.course', $program['program_id']) }}" 
                 class="compact-nav-item program-item">
                <i class="bi bi-book"></i>
                <div class="compact-program-info">
                  <div class="compact-program-name">{{ $program['program_name'] }}</div>
                  <small class="compact-program-package">{{ $program['package_name'] }}</small>
                </div>
              </a>
            @endforeach
          </div>
          @endif
        </div>
      </div>
      
      {{-- Compact Sidebar Backdrop --}}
      <div class="compact-sidebar-backdrop" id="compactSidebarBackdrop"></div>
    @endif
  </div>

  {{-- Hidden Logout Form --}}
  <form id="logout-form" action="{{ route('student.logout') }}" method="POST" style="display:none;">
    @csrf
  </form>

  @stack('scripts')
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Professional Sidebar JavaScript -->
  <script>
document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.getElementById('studentSidebar');
  const backdrop = document.getElementById('sidebarBackdrop');
  const toggleBtns = document.querySelectorAll('#sidebarToggleBtn, .sidebar-toggle-btn');
  const mainContentArea = document.querySelector('.main-content-area');

  // Course page compact sidebar elements
  const floatingToggle = document.getElementById('floatingCourseToggle');
  const courseToggleBtn = document.getElementById('courseToggleBtn');
  const compactSidebar = document.getElementById('compactCourseSidebar');
  const compactBackdrop = document.getElementById('compactSidebarBackdrop');
  const compactCloseBtn = document.getElementById('compactCloseBtn');

  // Toggle sidebar function
  function toggleSidebar() {
    if (window.innerWidth >= 769) {
      // Desktop: Toggle collapsed state
      if (sidebar) {
        sidebar.classList.toggle('collapsed');
        
        // Update content margin
        if (sidebar.classList.contains('collapsed')) {
          mainContentArea.style.marginLeft = '60px';
        } else {
          mainContentArea.style.marginLeft = '280px';
        }
      }
    } else {
      // Mobile: Toggle visibility
      if (sidebar) {
        sidebar.classList.toggle('mobile-open');
      }
      if (backdrop) {
        backdrop.classList.toggle('active');
      }
      
      // Prevent body scroll when sidebar is open
      if (sidebar && sidebar.classList.contains('mobile-open')) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    }
  }

  // Close sidebar (mobile)
  function closeSidebar() {
    if (sidebar) {
      sidebar.classList.remove('mobile-open');
    }
    if (backdrop) {
      backdrop.classList.remove('active');
    }
    document.body.style.overflow = '';
  }

  // Compact course sidebar functions
  function toggleCompactSidebar() {
    if (compactSidebar) {
      compactSidebar.classList.toggle('active');
    }
    if (compactBackdrop) {
      compactBackdrop.classList.toggle('active');
    }
    
    // Prevent body scroll when compact sidebar is open
    if (compactSidebar && compactSidebar.classList.contains('active')) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
  }

  function closeCompactSidebar() {
    if (compactSidebar) {
      compactSidebar.classList.remove('active');
    }
    if (compactBackdrop) {
      compactBackdrop.classList.remove('active');
    }
    document.body.style.overflow = '';
  }

  // Event listeners for main sidebar
  toggleBtns.forEach(btn => {
    if (btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleSidebar();
      });
    }
  });

  // Backdrop click to close (mobile)
  if (backdrop) {
    backdrop.addEventListener('click', closeSidebar);
  }

  // Event listeners for compact course sidebar
  if (courseToggleBtn) {
    courseToggleBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      toggleCompactSidebar();
    });
  }

  if (compactCloseBtn) {
    compactCloseBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      closeCompactSidebar();
    });
  }

  if (compactBackdrop) {
    compactBackdrop.addEventListener('click', closeCompactSidebar);
  }

  // Handle window resize
  window.addEventListener('resize', function() {
    if (window.innerWidth >= 769) {
      // Desktop mode
      if (sidebar) {
        sidebar.classList.remove('mobile-open');
      }
      if (backdrop) {
        backdrop.classList.remove('active');
      }
      document.body.style.overflow = '';
      
      // Set proper margin
      if (sidebar && sidebar.classList.contains('collapsed')) {
        mainContentArea.style.marginLeft = '60px';
      } else if (sidebar) {
        mainContentArea.style.marginLeft = '280px';
      } else {
        mainContentArea.style.marginLeft = '0'; // Course pages without sidebar
      }
    } else {
      // Mobile mode
      if (sidebar) {
        sidebar.classList.remove('collapsed');
      }
      mainContentArea.style.marginLeft = '0';
    }
    
    // Close compact sidebar on resize
    closeCompactSidebar();
  });

  // Initialize proper layout on load
  if (window.innerWidth >= 769) {
    if (sidebar) {
      mainContentArea.style.marginLeft = '280px';
    } else {
      mainContentArea.style.marginLeft = '0'; // Course pages without sidebar
    }
  } else {
    mainContentArea.style.marginLeft = '0';
  }

  // Add tooltips for collapsed state
  const navItems = document.querySelectorAll('.nav-item');
  navItems.forEach(item => {
    const textSpan = item.querySelector('.nav-text');
    if (textSpan) {
      const text = textSpan.textContent.trim();
      item.setAttribute('data-tooltip', text);
    }
  });

  // Close compact sidebar when clicking on navigation items
  const compactNavItems = document.querySelectorAll('.compact-nav-item');
  compactNavItems.forEach(item => {
    item.addEventListener('click', function() {
      closeCompactSidebar();
    });
  });
});
  </script>
  
  <!-- Global chat is already included in student-navbar component, no need to duplicate -->
</body>
</html>
