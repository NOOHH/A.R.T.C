
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title><?php echo $__env->yieldContent('title', 'Student Dashboard'); ?></title>

  <?php
    // Check if this is a preview request
    $isPreview = request()->has('preview') || request()->is('*/preview') || request()->query('preview') === 'true';
    
    if ($isPreview) {
      // For preview mode, create a mock user
      $user = (object)[
        'id'   => 'preview-user',
        'name' => isset($user) && isset($user->user_firstname) ? $user->user_firstname . ' Student' : 'John Student',
        'role' => 'student',
        'email'=> 'preview@example.com',
      ];
    } else {
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
    }
  ?>

  <!-- Global JS vars -->
  <script>
    window.myId            = <?php echo e($user->id); ?>;
    window.myName          = <?php echo json_encode($user->name, 15, 512) ?>;
    window.isAuthenticated = true;
    window.userRole        = <?php echo json_encode($user->role, 15, 512) ?>;
    window.csrfToken       = <?php echo json_encode(csrf_token(), 15, 512) ?>;
    console.log('Globals:', { myId, myName, isAuthenticated, userRole });
  </script>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="<?php echo e(asset('css/student/student-dashboard-layout.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('css/student/student-sidebar-professional.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('css/student/student-navbar.css')); ?>" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

  <?php echo $__env->yieldContent('head'); ?>
  <?php echo $__env->yieldPushContent('styles'); ?>

  <style>
    <?php echo \App\Helpers\SettingsHelper::getSidebarCSS('student'); ?>


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

    /* 2) Restore your white "card" appearance on the content wrapper */
    .content-wrapper {
      background-color: #fff;
      border-radius: .5rem;
      box-shadow: 0 .125rem .25rem rgba(0,0,0,0.075);
      overflow: visible !important;
      width: 100%;
      height: 100vh !important;
      min-height: 100vh !important;
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

    /* Mobile sidebar toggle */
    .mobile-sidebar-toggle {
      position: fixed;
      top: 1rem;
      left: 1rem;
      width: 48px;
      height: 48px;
      background: #1a1a1a;
      border: 2px solid #2d2d2d;
      border-radius: 12px;
      color: white;
      display: none;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
      z-index: 10000;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .mobile-sidebar-toggle:hover {
      background: #2d2d2d;
      transform: scale(1.05);
    }

    /* Main content area adjustments for sidebar collapse */
    .main-content-area {
      margin-left: 280px;
      transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .main-content-area.sidebar-collapsed {
      margin-left: 60px;
    }

    @media (max-width: 768px) {
      .mobile-sidebar-toggle {
        display: flex !important;
      }
      
      .main-content-area {
        margin-left: 0 !important;
      }
    }
  </style>
</head>
<body>
  <div class="student-container">
    <!-- Mobile Sidebar Toggle -->
    <button class="mobile-sidebar-toggle d-md-none" id="mobileSidebarToggle">
      <i class="bi bi-list"></i>
    </button>

    
    <?php if(!isset($hideSidebar) || !$hideSidebar): ?>
      <?php echo $__env->make('components.student-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    
    <div class="main-content-area">
      
      <?php echo $__env->make('components.student-navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

      
      <div class="content-wrapper">
        <?php echo $__env->yieldContent('content'); ?>
      </div>
    </div>

    
    <?php if(isset($hideSidebar) && $hideSidebar): ?>
      <div class="floating-course-toggle" id="floatingCourseToggle">
        <button class="course-toggle-btn" id="courseToggleBtn" title="Show Navigation">
          <i class="bi bi-list"></i>
        </button>
      </div>
      
      
      <?php
          $brandName = $navbar['brand_name'] ?? $settings['navbar']['brand_name'] ?? $navbarBrandName ?? 'ARTC';
          // Use abbreviation for compact display
          $compactBrandName = strlen($brandName) > 10 ? strtoupper(substr($brandName, 0, 4)) : $brandName;
      ?>
      
      <div class="compact-course-sidebar" id="compactCourseSidebar">
        <div class="compact-sidebar-header">
          <div class="compact-brand">
            <i class="bi bi-mortarboard-fill"></i>
            <span><?php echo e($compactBrandName); ?></span>
          </div>
          <button class="compact-close-btn" id="compactCloseBtn">
            <i class="bi bi-x"></i>
          </button>
        </div>
        <div class="compact-sidebar-content">
          <div class="compact-nav-section">
            <a href="<?php echo e(route('student.dashboard')); ?>" class="compact-nav-item">
              <i class="bi bi-speedometer2"></i>
              <span>Dashboard</span>
            </a>
            <a href="<?php echo e(route('student.calendar')); ?>" class="compact-nav-item">
              <i class="bi bi-calendar-week"></i>
              <span>Calendar</span>
            </a>
            <a href="<?php echo e(route('student.enrolled-courses')); ?>" class="compact-nav-item">
              <i class="bi bi-journal-bookmark"></i>
              <span>My Courses</span>
            </a>
            <a href="<?php echo e(route('student.meetings')); ?>" class="compact-nav-item">
              <i class="bi bi-camera-video"></i>
              <span>Meetings</span>
            </a>
          </div>
          
          <?php if(isset($studentPrograms) && !empty($studentPrograms)): ?>
          <div class="compact-nav-section">
            <div class="compact-section-title">My Programs</div>
            <?php $__currentLoopData = $studentPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <a href="<?php echo e(route('student.course', $program['program_id'])); ?>" 
                 class="compact-nav-item program-item">
                <i class="bi bi-book"></i>
                <div class="compact-program-info">
                  <div class="compact-program-name"><?php echo e($program['program_name']); ?></div>
                  <small class="compact-program-package"><?php echo e($program['package_name']); ?></small>
                </div>
              </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
      
      
      <div class="compact-sidebar-backdrop" id="compactSidebarBackdrop"></div>
    <?php endif; ?>
  </div>

  
  <form id="logout-form" action="<?php echo e(route('student.logout')); ?>" method="POST" style="display:none;">
    <?php echo csrf_field(); ?>
  </form>

  <?php echo $__env->yieldPushContent('scripts'); ?>
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
        
        // Update content margin (like professor sidebar)
        if (sidebar.classList.contains('collapsed')) {
          mainContentArea.style.marginLeft = '70px';
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

  // Make entire sidebar clickable to toggle (like professor sidebar)
  if (sidebar) {
    sidebar.addEventListener('click', function(e) {
      // Only toggle if we're not clicking on navigation links or forms
      if (!e.target.closest('.nav-link') && 
          !e.target.closest('.submenu-link') && 
          !e.target.closest('form') && 
          !e.target.closest('button') &&
          !e.target.closest('a')) {
        e.preventDefault();
        e.stopPropagation();
        toggleSidebar();
      }
    });
  }

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
        mainContentArea.style.marginLeft = '70px';
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
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\student\student-dashboard\student-dashboard-layout.blade.php ENDPATH**/ ?>