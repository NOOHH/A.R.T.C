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

    {{-- Main Content --}}
    <div class="main-content">
      <div class="main-wrapper">
        <div class="content-below-search">
          {{-- Conditional Sidebar --}}
          @if(!isset($hideSidebar) || !$hideSidebar)
            @include('components.student-sidebar')
          @endif
          
          {{-- Main Content --}}
          <div class="content-wrapper">
            @yield('content')
          </div>
        </div>
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
        // Sidebar Toggle Functionality
        const sidebarToggle = document.getElementById('sidebarToggle');
        const modernSidebar = document.getElementById('modernSidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const contentWrapper = document.querySelector('.content-wrapper');

        // Toggle sidebar function
        function toggleSidebar() {
            if (window.innerWidth >= 768) {
                // Desktop: Toggle collapsed state
                modernSidebar.classList.toggle('collapsed');    
                
                if (modernSidebar.classList.contains('collapsed')) {
                    contentWrapper.style.marginLeft = '70px';
                } else {
                    contentWrapper.style.marginLeft = '280px';
                }
            } else {
                // Mobile: Toggle sidebar visibility
                if (modernSidebar) {
                    modernSidebar.classList.toggle('active');
                }
                if (sidebarOverlay) {
                    sidebarOverlay.classList.toggle('active');
                }
                document.body.style.overflow = modernSidebar && modernSidebar.classList.contains('active') ? 'hidden' : '';
            }
        }

        // Close sidebar function (mobile only)
        function closeSidebar() {
            if (window.innerWidth < 768) {
                if (modernSidebar) {
                    modernSidebar.classList.remove('active');
                }
                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('active');
                }
                document.body.style.overflow = '';
            }
        }

        // Event listeners
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768 && contentWrapper) {
                contentWrapper.style.marginLeft = modernSidebar.classList.contains('collapsed')
                    ? '70px'
                    : '280px';
                
                // Close mobile overlay if open
                if (modernSidebar) {
                    modernSidebar.classList.remove('active');
                }
                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('active');
                }
                document.body.style.overflow = '';
            } else if (window.innerWidth < 768 && contentWrapper) {
                contentWrapper.style.marginLeft = '0';
            }
        });

        // Initialize sidebar state for desktop
        if (window.innerWidth >= 768 && contentWrapper) {
            contentWrapper.style.marginLeft = '280px';
        }
    });
  </script>
  
  @include('components.global-chat')
</body>
</html>
