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
  <link href="{{ asset('css/student/student-sidebar.css') }}" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

  @yield('head')
  @stack('styles')

  <style>
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
    {{-- Top Header --}}
    @include('components.student-navbar')

    {{-- Main Container --}}
    <div class="container-fluid p-0">
      <div class="row g-0">
        {{-- Conditional Sidebar --}}
        @if(!isset($hideSidebar) || !$hideSidebar)
          @include('components.student-sidebar')
        @endif
        
        {{-- Main Content with dynamic width based on sidebar visibility --}}
        <main class="@if(!isset($hideSidebar) || !$hideSidebar) col-lg-9 col-xl-10 @else col-12 @endif main-content">
          <div class="content-wrapper p-3">
            @yield('content')
          </div>
        </main>
      </div>
    </div>
  </div>

  {{-- Hidden Logout Form --}}
  <form id="logout-form" action="{{ route('student.logout') }}" method="POST" style="display:none;">
    @csrf
  </form>

  @stack('scripts')
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Sidebar Toggle JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.getElementById('modernSidebar');
      const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
      const sidebarClose = document.getElementById('sidebarClose');
      const sidebarOverlay = document.getElementById('sidebarOverlay');
      const sidebarReopenBtn = document.getElementById('sidebarReopenBtn');
      const mainContent = document.querySelector('.main-content');
      
      let sidebarCollapsed = false;
      
      function toggleSidebar() {
        if (!sidebar) return;
        
        if (window.innerWidth >= 992) {
          // Desktop behavior - collapse/expand sidebar
          sidebarCollapsed = !sidebarCollapsed;
          
          if (sidebarCollapsed) {
            sidebar.classList.add('collapsed');
            if (mainContent) {
              mainContent.classList.add('sidebar-collapsed');
              mainContent.classList.remove('sidebar-expanded');
            }
            if (sidebarReopenBtn) {
              sidebarReopenBtn.style.display = 'block';
            }
          } else {
            sidebar.classList.remove('collapsed');
            if (mainContent) {
              mainContent.classList.remove('sidebar-collapsed');
              mainContent.classList.add('sidebar-expanded');
            }
            if (sidebarReopenBtn) {
              sidebarReopenBtn.style.display = 'none';
            }
          }
        } else {
          // Mobile behavior - show/hide with overlay
          sidebar.classList.toggle('active');
          if (sidebarOverlay) {
            sidebarOverlay.classList.toggle('active');
          }
        }
      }
      
      function closeSidebar() {
        if (!sidebar) return;
        
        sidebar.classList.remove('active');
        if (sidebarOverlay) {
          sidebarOverlay.classList.remove('active');
        }
      }
      
      function reopenSidebar() {
        if (!sidebar) return;
        
        sidebarCollapsed = false;
        sidebar.classList.remove('collapsed');
        if (mainContent) {
          mainContent.classList.remove('sidebar-collapsed');
          mainContent.classList.add('sidebar-expanded');
        }
        if (sidebarReopenBtn) {
          sidebarReopenBtn.style.display = 'none';
        }
      }
      
      // Sidebar toggle button
      if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', toggleSidebar);
      }
      
      // Close sidebar
      if (sidebarClose) {
        sidebarClose.addEventListener('click', closeSidebar);
      }
      
      // Reopen sidebar
      if (sidebarReopenBtn) {
        sidebarReopenBtn.addEventListener('click', reopenSidebar);
      }
      
      // Close sidebar when clicking overlay
      if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
      }
      
      // Handle responsive behavior
      window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
          closeSidebar(); // Close mobile overlay if open
          // Reset to expanded state on desktop if not manually collapsed
          if (!sidebarCollapsed) {
            if (mainContent) {
              mainContent.classList.add('sidebar-expanded');
              mainContent.classList.remove('sidebar-collapsed');
            }
            if (sidebarReopenBtn) {
              sidebarReopenBtn.style.display = 'none';
            }
          }
        } else {
          // Mobile: hide reopen button and reset classes
          if (sidebarReopenBtn) {
            sidebarReopenBtn.style.display = 'none';
          }
          if (mainContent) {
            mainContent.classList.remove('sidebar-collapsed', 'sidebar-expanded');
          }
        }
      });
      
      // Initialize sidebar state
      if (window.innerWidth >= 992 && mainContent) {
        mainContent.classList.add('sidebar-expanded');
      }
    });
  </script>
  
  @include('components.global-chat')
</body>
</html>
